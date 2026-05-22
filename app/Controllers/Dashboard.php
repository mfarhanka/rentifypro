<?php

namespace App\Controllers;

use App\Models\GadgetModel;
use App\Models\RentalModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $gadgetModel = model(GadgetModel::class);
        $rentalModel = model(RentalModel::class);
        $role        = $this->primaryRole();
        $user        = $this->user();

        if ($role === 'customer') {
            $stats = [
                'labelOne'   => 'My rentals',
                'valueOne'   => $rentalModel->where('customer_id', $user->id)->countAllResults(),
                'labelTwo'   => 'Pending',
                'valueTwo'   => $rentalModel->where(['customer_id' => $user->id, 'status' => 'pending'])->countAllResults(),
                'labelThree' => 'Invoices total',
                'valueThree' => $rentalModel->selectSum('total_amount')->where('customer_id', $user->id)->whereIn('status', ['approved', 'returned'])->get()->getRowArray()['total_amount'] ?? 0,
                'labelFour'  => 'Available gadgets',
                'valueFour'  => $gadgetModel->where('available_stock >', 0)->countAllResults(),
            ];

            $recentRentals = $rentalModel->detailedQuery()
                ->where('rentals.customer_id', $user->id)
                ->orderBy('rentals.created_at', 'DESC')
                ->findAll(5);
        } else {
            $stats = [
                'labelOne'   => 'Catalog items',
                'valueOne'   => $gadgetModel->countAllResults(),
                'labelTwo'   => 'Units available',
                'valueTwo'   => $gadgetModel->selectSum('available_stock')->get()->getRowArray()['available_stock'] ?? 0,
                'labelThree' => 'Pending rentals',
                'valueThree' => $rentalModel->where('status', 'pending')->countAllResults(),
                'labelFour'  => 'Revenue',
                'valueFour'  => $rentalModel->selectSum('total_amount')->whereIn('status', ['approved', 'returned'])->get()->getRowArray()['total_amount'] ?? 0,
            ];

            $recentRentals = $rentalModel->detailedQuery()
                ->orderBy('rentals.created_at', 'DESC')
                ->findAll(8);
        }

        $gadgets = $gadgetModel->orderBy('available_stock', 'DESC')->findAll(6);

        return view('dashboard/index', [
            'role'          => $role,
            'stats'         => $stats,
            'recentRentals' => $recentRentals,
            'gadgets'       => $gadgets,
        ]);
    }
}