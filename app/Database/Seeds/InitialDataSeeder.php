<?php

namespace App\Database\Seeds;

use App\Models\GadgetModel;
use App\Models\RentalModel;
use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $db          = db_connect();
        $userModel   = model(UserModel::class);
        $gadgetModel = model(GadgetModel::class);
        $rentalModel = model(RentalModel::class);

        $tenant = $db->table('tenants')
            ->select('id')
            ->where('slug', 'rentifypro-default')
            ->get()
            ->getRowArray();

        if ($tenant === null) {
            $db->table('tenants')->insert([
                'name'       => 'Rentify Pro Demo Workspace',
                'slug'       => 'rentifypro-default',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $tenant = [
                'id' => $db->insertID(),
            ];
        }

        $tenantId = (int) $tenant['id'];

        $accounts = [
            ['username' => 'admin', 'email' => 'admin@rentifypro.test', 'password' => 'password123', 'group' => 'admin'],
            ['username' => 'staff', 'email' => 'staff@rentifypro.test', 'password' => 'password123', 'group' => 'staff'],
            ['username' => 'customer', 'email' => 'customer@rentifypro.test', 'password' => 'password123', 'group' => 'customer'],
        ];

        foreach ($accounts as $account) {
            $user = $userModel->findByCredentials(['email' => $account['email']]);

            if ($user === null) {
                $user = new User([
                    'username' => $account['username'],
                    'email'    => $account['email'],
                    'password' => $account['password'],
                    'active'   => 1,
                ]);

                $userModel->save($user);
                $user = $userModel->findByCredentials(['email' => $account['email']]);
            }

            if ($user !== null && ! $user->inGroup($account['group'])) {
                $user->addGroup($account['group']);
            }

            if ($user !== null) {
                $membership = $db->table('tenant_users')
                    ->select('id')
                    ->where('tenant_id', $tenantId)
                    ->where('user_id', $user->id)
                    ->get()
                    ->getRowArray();

                if ($membership === null) {
                    $db->table('tenant_users')->insert([
                        'tenant_id'   => $tenantId,
                        'user_id'     => $user->id,
                        'created_at'  => date('Y-m-d H:i:s'),
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        if ($gadgetModel->where('tenant_id', $tenantId)->countAllResults() === 0) {
            $gadgetModel->insertBatch([
                [
                    'tenant_id'        => $tenantId,
                    'code'            => 'CAM-001',
                    'name'            => 'Canon EOS M50',
                    'brand'           => 'Canon',
                    'daily_rate'      => 25.00,
                    'stock'           => 4,
                    'available_stock' => 4,
                    'description'     => 'Mirrorless camera kit for content creators.',
                ],
                [
                    'tenant_id'        => $tenantId,
                    'code'            => 'DRN-002',
                    'name'            => 'DJI Mini 4 Pro',
                    'brand'           => 'DJI',
                    'daily_rate'      => 40.00,
                    'stock'           => 3,
                    'available_stock' => 3,
                    'description'     => 'Compact drone with 4K recording and spare battery.',
                ],
                [
                    'tenant_id'        => $tenantId,
                    'code'            => 'CON-003',
                    'name'            => 'PlayStation 5',
                    'brand'           => 'Sony',
                    'daily_rate'      => 30.00,
                    'stock'           => 5,
                    'available_stock' => 5,
                    'description'     => 'Console rental bundle with two controllers.',
                ],
            ]);
        }

        $customer = $userModel->findByCredentials(['email' => 'customer@rentifypro.test']);
        $gadget   = $gadgetModel->where('tenant_id', $tenantId)->first();

        if ($customer !== null) {
            $contact = $db->table('customer_contacts')
                ->select('id')
                ->where('user_id', $customer->id)
                ->get()
                ->getRowArray();

            if ($contact === null) {
                $db->table('customer_contacts')->insert([
                    'tenant_id'    => $tenantId,
                    'user_id'      => $customer->id,
                    'email'        => 'customer@rentifypro.test',
                    'phone'        => null,
                    'setup_token'  => null,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($customer !== null && $gadget !== null && $rentalModel->where('tenant_id', $tenantId)->countAllResults() === 0) {
            $rentalModel->insert([
                'tenant_id'      => $tenantId,
                'invoice_number' => 'INV-DEMO-1001',
                'gadget_id'      => $gadget['id'],
                'customer_id'    => $customer->id,
                'quantity'       => 1,
                'rental_days'    => 3,
                'daily_rate'     => $gadget['daily_rate'],
                'total_amount'   => 3 * (float) $gadget['daily_rate'],
                'start_date'     => date('Y-m-d'),
                'end_date'       => date('Y-m-d', strtotime('+2 days')),
                'status'         => 'approved',
                'notes'          => 'Demo invoice generated during setup.',
            ]);

            $gadgetModel->update($gadget['id'], [
                'available_stock' => max(0, ((int) $gadget['available_stock']) - 1),
            ]);
        }
    }
}