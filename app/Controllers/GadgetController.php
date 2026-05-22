<?php

namespace App\Controllers;

use App\Models\GadgetModel;
use CodeIgniter\HTTP\RedirectResponse;

class GadgetController extends BaseController
{
    public function index(): string
    {
        $gadgetModel = model(GadgetModel::class);

        return view('gadgets/index', [
            'role'    => $this->primaryRole(),
            'gadgets' => $gadgetModel->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function create(): string|RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin', 'staff'])) {
            return $redirect;
        }

        return view('gadgets/form', [
            'title'  => 'Add Gadget',
            'action' => site_url('gadgets'),
            'gadget' => null,
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin', 'staff'])) {
            return $redirect;
        }

        $rules = [
            'code'        => 'required|min_length[3]|max_length[30]|is_unique[gadgets.code]',
            'name'        => 'required|max_length[120]',
            'brand'       => 'required|max_length[80]',
            'daily_rate'  => 'required|decimal',
            'stock'       => 'required|integer|greater_than[0]',
            'description' => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $stock       = (int) $this->request->getPost('stock');
        $gadgetModel = model(GadgetModel::class);
        $gadgetModel->insert([
            'code'            => strtoupper((string) $this->request->getPost('code')),
            'name'            => (string) $this->request->getPost('name'),
            'brand'           => (string) $this->request->getPost('brand'),
            'daily_rate'      => $this->request->getPost('daily_rate'),
            'stock'           => $stock,
            'available_stock' => $stock,
            'description'     => (string) $this->request->getPost('description'),
        ]);

        return redirect()->to(site_url('gadgets'))->with('message', 'Gadget added successfully.');
    }

    public function edit(int $id): string|RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin', 'staff'])) {
            return $redirect;
        }

        $gadgetModel = model(GadgetModel::class);
        $gadget      = $gadgetModel->find($id);

        if ($gadget === null) {
            return redirect()->to(site_url('gadgets'))->with('error', 'Gadget not found.');
        }

        return view('gadgets/form', [
            'title'  => 'Edit Gadget',
            'action' => site_url('gadgets/' . $id),
            'gadget' => $gadget,
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin', 'staff'])) {
            return $redirect;
        }

        $gadgetModel = model(GadgetModel::class);
        $gadget      = $gadgetModel->find($id);

        if ($gadget === null) {
            return redirect()->to(site_url('gadgets'))->with('error', 'Gadget not found.');
        }

        $rules = [
            'code'        => 'required|min_length[3]|max_length[30]|is_unique[gadgets.code,id,' . $id . ']',
            'name'        => 'required|max_length[120]',
            'brand'       => 'required|max_length[80]',
            'daily_rate'  => 'required|decimal',
            'stock'       => 'required|integer|greater_than[0]',
            'description' => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newStock       = (int) $this->request->getPost('stock');
        $reservedUnits  = max(0, ((int) $gadget['stock']) - ((int) $gadget['available_stock']));
        $availableStock = max(0, $newStock - $reservedUnits);

        $gadgetModel->update($id, [
            'code'            => strtoupper((string) $this->request->getPost('code')),
            'name'            => (string) $this->request->getPost('name'),
            'brand'           => (string) $this->request->getPost('brand'),
            'daily_rate'      => $this->request->getPost('daily_rate'),
            'stock'           => $newStock,
            'available_stock' => $availableStock,
            'description'     => (string) $this->request->getPost('description'),
        ]);

        return redirect()->to(site_url('gadgets'))->with('message', 'Gadget updated successfully.');
    }
}