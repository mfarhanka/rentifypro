<?php

namespace App\Controllers;

use App\Models\GadgetModel;
use App\Models\RentalModel;
use CodeIgniter\HTTP\RedirectResponse;
use DateTimeImmutable;
use Exception;

class RentalController extends BaseController
{
    public function index(): string
    {
        $rentalModel = model(RentalModel::class);
        $role        = $this->primaryRole();
        $query       = $rentalModel->detailedQuery()->orderBy('rentals.created_at', 'DESC');

        if ($role === 'customer') {
            $query->where('rentals.customer_id', $this->user()->id);
        }

        return view('rentals/index', [
            'role'    => $role,
            'rentals' => $query->findAll(),
        ]);
    }

    public function create(): string|RedirectResponse
    {
        if ($redirect = $this->requireGroups(['customer'])) {
            return $redirect;
        }

        $gadgetModel = model(GadgetModel::class);
        $selectedId  = (int) ($this->request->getGet('gadget') ?? 0);

        return view('rentals/create', [
            'gadgets'    => $gadgetModel->where('available_stock >', 0)->orderBy('name', 'ASC')->findAll(),
            'selectedId' => $selectedId,
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['customer'])) {
            return $redirect;
        }

        $rules = [
            'gadget_id'   => 'required|integer',
            'quantity'    => 'required|integer|greater_than[0]',
            'start_date'  => 'required|valid_date[Y-m-d]',
            'end_date'    => 'required|valid_date[Y-m-d]',
            'notes'       => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $gadgetModel = model(GadgetModel::class);
        $rentalModel = model(RentalModel::class);
        $gadget      = $gadgetModel->find((int) $this->request->getPost('gadget_id'));

        if ($gadget === null) {
            return redirect()->back()->withInput()->with('error', 'Selected gadget was not found.');
        }

        try {
            $start = new DateTimeImmutable((string) $this->request->getPost('start_date'));
            $end   = new DateTimeImmutable((string) $this->request->getPost('end_date'));
        } catch (Exception) {
            return redirect()->back()->withInput()->with('error', 'Invalid rental dates.');
        }

        if ($end < $start) {
            return redirect()->back()->withInput()->with('error', 'End date must be after the start date.');
        }

        $quantity    = (int) $this->request->getPost('quantity');
        $rentalDays  = (int) $start->diff($end)->days + 1;
        $totalAmount = $rentalDays * $quantity * (float) $gadget['daily_rate'];

        if ((int) $gadget['available_stock'] < $quantity) {
            return redirect()->back()->withInput()->with('error', 'Not enough stock available for this gadget.');
        }

        $db = db_connect();
        $db->transStart();

        $rentalModel->insert([
            'invoice_number' => $this->generateInvoiceNumber(),
            'gadget_id'      => $gadget['id'],
            'customer_id'    => $this->user()->id,
            'quantity'       => $quantity,
            'rental_days'    => $rentalDays,
            'daily_rate'     => $gadget['daily_rate'],
            'total_amount'   => $totalAmount,
            'start_date'     => $start->format('Y-m-d'),
            'end_date'       => $end->format('Y-m-d'),
            'status'         => 'pending',
            'notes'          => (string) $this->request->getPost('notes'),
        ]);

        $gadgetModel->update($gadget['id'], [
            'available_stock' => ((int) $gadget['available_stock']) - $quantity,
        ]);

        $db->transComplete();

        return redirect()->to(site_url('rentals'))->with('message', 'Rental request submitted and invoice generated.');
    }

    public function updateStatus(int $id): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin', 'staff'])) {
            return $redirect;
        }

        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['pending', 'approved', 'returned', 'cancelled'], true)) {
            return redirect()->to(site_url('rentals'))->with('error', 'Invalid rental status.');
        }

        $rentalModel = model(RentalModel::class);
        $gadgetModel = model(GadgetModel::class);
        $rental      = $rentalModel->find($id);

        if ($rental === null) {
            return redirect()->to(site_url('rentals'))->with('error', 'Rental not found.');
        }

        $gadget = $gadgetModel->find($rental['gadget_id']);
        if ($gadget === null) {
            return redirect()->to(site_url('rentals'))->with('error', 'Related gadget not found.');
        }

        $oldClosed = in_array($rental['status'], ['returned', 'cancelled'], true);
        $newClosed = in_array($status, ['returned', 'cancelled'], true);
        $db        = db_connect();
        $db->transStart();

        if (! $oldClosed && $newClosed) {
            $gadgetModel->update($gadget['id'], [
                'available_stock' => ((int) $gadget['available_stock']) + ((int) $rental['quantity']),
            ]);
        }

        if ($oldClosed && ! $newClosed) {
            if ((int) $gadget['available_stock'] < (int) $rental['quantity']) {
                $db->transRollback();

                return redirect()->to(site_url('rentals'))->with('error', 'Not enough stock to reopen this rental.');
            }

            $gadgetModel->update($gadget['id'], [
                'available_stock' => ((int) $gadget['available_stock']) - ((int) $rental['quantity']),
            ]);
        }

        $payload = [
            'status'       => $status,
            'processed_by' => $this->user()->id,
            'returned_at'  => $status === 'returned' ? date('Y-m-d H:i:s') : null,
        ];
        $rentalModel->update($id, $payload);

        $db->transComplete();

        return redirect()->to(site_url('rentals'))->with('message', 'Rental status updated.');
    }

    public function invoice(int $id): string|RedirectResponse
    {
        $rentalModel = model(RentalModel::class);
        $rental      = $rentalModel->detailedQuery()
            ->select('rentals.*, gadgets.name AS gadget_name, gadgets.brand AS gadget_brand, gadgets.code AS gadget_code, users.username AS customer_name')
            ->where('rentals.id', $id)
            ->first();

        if ($rental === null) {
            return redirect()->to(site_url('rentals'))->with('error', 'Invoice not found.');
        }

        if ($this->primaryRole() === 'customer' && (int) $rental['customer_id'] !== (int) $this->user()->id) {
            return redirect()->to(site_url('rentals'))->with('error', 'You do not have access to that invoice.');
        }

        return view('rentals/invoice', [
            'rental' => $rental,
        ]);
    }

    protected function generateInvoiceNumber(): string
    {
        return 'INV-' . date('YmdHis') . '-' . random_int(100, 999);
    }
}