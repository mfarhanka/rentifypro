<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class CustomerController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin', 'staff'])) {
            return $redirect;
        }

        return view('customers/index', [
            'customers' => $this->customerRows(),
        ]);
    }

    public function create(): string|RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin', 'staff'])) {
            return $redirect;
        }

        return view('customers/form', [
            'title'    => 'Add Customer Account',
            'action'   => site_url('customers'),
            'customer' => null,
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin', 'staff'])) {
            return $redirect;
        }

        $input = $this->request->getPost(['username', 'email', 'password', 'password_confirm']);

        if ($errors = $this->validateCustomerInput($input)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $userModel = model(UserModel::class);
        $user      = new User([
            'username' => (string) $input['username'],
            'email'    => strtolower((string) $input['email']),
            'password' => (string) $input['password'],
            'active'   => 1,
        ]);

        $userModel->save($user);
        $createdUser = $userModel->findByCredentials(['email' => strtolower((string) $input['email'])]);
        $createdUser?->syncGroups('customer');

        return redirect()->to(site_url('customers'))->with('message', 'Customer account created.');
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    private function validateCustomerInput(array $input): array
    {
        $errors = [];

        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]',
            'email'            => 'required|valid_email|max_length[254]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validateData($input, $rules)) {
            $errors = $this->validator->getErrors();
        }

        $db                 = db_connect();
        $normalizedEmail    = strtolower((string) ($input['email'] ?? ''));
        $normalizedUsername = strtolower((string) ($input['username'] ?? ''));

        $existingByEmail = $db->table('auth_identities')
            ->select('user_id')
            ->where('type', 'email_password')
            ->where('LOWER(secret)', $normalizedEmail)
            ->get()
            ->getRowArray();

        if ($existingByEmail !== null) {
            $errors['email'] = 'That email is already in use.';
        }

        $existingByUsername = $db->table('users')
            ->select('id')
            ->where('LOWER(username)', $normalizedUsername)
            ->get()
            ->getRowArray();

        if ($existingByUsername !== null) {
            $errors['username'] = 'That username is already in use.';
        }

        return $errors;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function customerRows(): array
    {
        $rows = db_connect()
            ->table('users')
            ->select('users.id, users.username, users.active, users.last_active, users.created_at, auth_identities.secret AS email')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->join('auth_identities', "auth_identities.user_id = users.id AND auth_identities.type = 'email_password'", 'left')
            ->where('auth_groups_users.group', 'customer')
            ->orderBy('users.username', 'ASC')
            ->get()
            ->getResultArray();

        return array_values($rows);
    }
}