<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class WorkspaceController extends BaseController
{
    public function edit(): string|RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin'])) {
            return $redirect;
        }

        $tenant = $this->tenant();

        if ($tenant === null) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Workspace not found.');
        }

        return view('workspace/settings', [
            'tenant' => $tenant,
            'stats'  => $this->workspaceStats((int) $tenant['id']),
        ]);
    }

    public function update(): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin'])) {
            return $redirect;
        }

        $tenant = $this->tenant();

        if ($tenant === null) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Workspace not found.');
        }

        $input = $this->request->getPost(['name', 'slug']);
        $errors = $this->validateWorkspaceInput($input, (int) $tenant['id']);

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        db_connect()->table('tenants')
            ->where('id', $tenant['id'])
            ->update([
                'name'       => trim((string) $input['name']),
                'slug'       => strtolower(trim((string) $input['slug'])),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to(site_url('workspace/settings'))->with('message', 'Workspace settings updated.');
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    private function validateWorkspaceInput(array $input, int $tenantId): array
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[120]',
            'slug' => 'required|min_length[3]|max_length[120]|regex_match[/^[a-z0-9]+(?:-[a-z0-9]+)*$/]',
        ];

        $errors = [];

        if (! $this->validateData($input, $rules)) {
            $errors = $this->validator->getErrors();
        }

        $slug = strtolower(trim((string) ($input['slug'] ?? '')));

        if ($slug !== '') {
            $existing = db_connect()->table('tenants')
                ->select('id')
                ->where('slug', $slug)
                ->where('id !=', $tenantId)
                ->get()
                ->getRowArray();

            if ($existing !== null) {
                $errors['slug'] = 'That workspace slug is already in use.';
            }
        }

        return $errors;
    }

    /**
     * @return array<string, int>
     */
    private function workspaceStats(int $tenantId): array
    {
        $db = db_connect();

        $staffCount = $db->table('tenant_users')
            ->join('auth_groups_users', 'auth_groups_users.user_id = tenant_users.user_id')
            ->where('tenant_users.tenant_id', $tenantId)
            ->where('auth_groups_users.group', 'staff')
            ->countAllResults();

        $customerCount = $db->table('customer_contacts')
            ->where('tenant_id', $tenantId)
            ->countAllResults();

        $gadgetCount = $db->table('gadgets')
            ->where('tenant_id', $tenantId)
            ->countAllResults();

        $rentalCount = $db->table('rentals')
            ->where('tenant_id', $tenantId)
            ->countAllResults();

        return [
            'staff'     => $staffCount,
            'customers' => $customerCount,
            'gadgets'   => $gadgetCount,
            'rentals'   => $rentalCount,
        ];
    }
}