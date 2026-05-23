<?php

namespace App\Controllers;

use App\Models\GadgetModel;
use App\Models\RentalModel;
use CodeIgniter\HTTP\RedirectResponse;

class Dashboard extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if ($redirect = $this->requireTenant()) {
            return $redirect;
        }

        $gadgetModel = model(GadgetModel::class);
        $rentalModel = model(RentalModel::class);
        $role        = $this->primaryRole();
        $user        = $this->user();
        $tenantId    = (int) $this->tenantId();

        if ($role === 'customer') {
            $stats = [
                'labelOne'   => 'My rentals',
                'valueOne'   => $rentalModel->forTenant($tenantId)->where('customer_id', $user->id)->countAllResults(),
                'labelTwo'   => 'Pending',
                'valueTwo'   => $rentalModel->forTenant($tenantId)->where(['customer_id' => $user->id, 'status' => 'pending'])->countAllResults(),
                'labelThree' => 'Invoices total',
                'valueThree' => $rentalModel->selectSum('total_amount')->where('tenant_id', $tenantId)->where('customer_id', $user->id)->whereIn('status', ['approved', 'returned'])->get()->getRowArray()['total_amount'] ?? 0,
                'labelFour'  => 'Available gadgets',
                'valueFour'  => $gadgetModel->forTenant($tenantId)->where('available_stock >', 0)->countAllResults(),
            ];

            $recentRentals = $rentalModel->detailedQuery($tenantId)
                ->where('rentals.customer_id', $user->id)
                ->orderBy('rentals.created_at', 'DESC')
                ->findAll(5);
        } else {
            $stats = [
                'labelOne'   => 'Catalog items',
                'valueOne'   => $gadgetModel->forTenant($tenantId)->countAllResults(),
                'labelTwo'   => 'Units available',
                'valueTwo'   => $gadgetModel->selectSum('available_stock')->where('tenant_id', $tenantId)->get()->getRowArray()['available_stock'] ?? 0,
                'labelThree' => 'Pending rentals',
                'valueThree' => $rentalModel->forTenant($tenantId)->where('status', 'pending')->countAllResults(),
                'labelFour'  => 'Revenue',
                'valueFour'  => $rentalModel->selectSum('total_amount')->where('tenant_id', $tenantId)->whereIn('status', ['approved', 'returned'])->get()->getRowArray()['total_amount'] ?? 0,
            ];

            $recentRentals = $rentalModel->detailedQuery($tenantId)
                ->orderBy('rentals.created_at', 'DESC')
                ->findAll(8);
        }

        $gadgets = $gadgetModel->forTenant($tenantId)->orderBy('available_stock', 'DESC')->findAll(6);

        return view('dashboard/index', [
            'role'          => $role,
            'stats'         => $stats,
            'recentRentals' => $recentRentals,
            'gadgets'       => $gadgets,
        ]);
    }
}