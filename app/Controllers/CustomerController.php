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
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin', 'staff'])) {
            return $redirect;
        }

        $input = $this->request->getPost(['email', 'phone']);

        if ($errors = $this->validateCustomerInput($input)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $email = $this->normalizeEmail((string) ($input['email'] ?? ''));
        $phone = $this->normalizePhone((string) ($input['phone'] ?? ''));
        $username = $this->generateCustomerUsername($phone !== '' ? $phone : $email);
        $setupToken = bin2hex(random_bytes(32));
        $userModel = model(UserModel::class);
        $user      = new User([
            'username' => $username,
            'active'   => 1,
        ]);

        $userModel->save($user);
        $createdUser = $userModel->find($userModel->getInsertID());
        $createdUser?->syncGroups('customer');

        db_connect()->table('customer_contacts')->insert([
            'user_id'         => $createdUser?->id,
            'email'           => $email !== '' ? $email : null,
            'phone'           => $phone !== '' ? $phone : null,
            'setup_token'     => $setupToken,
            'password_set_at' => null,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('customers'))->with('message', 'Customer account created. Share this setup link: ' . site_url('customers/setup/' . $setupToken));
    }

    public function setup(string $token): string|RedirectResponse
    {
        $contact = $this->pendingCustomerContact($token);

        if ($contact === null) {
            return redirect()->to(site_url('login'))->with('error', 'That setup link is invalid or has already been used.');
        }

        return view('customers/setup', [
            'title'      => 'Create Your Password',
            'action'     => site_url('customers/setup/' . $token),
            'email'      => $contact['email'],
            'phone'      => $contact['phone'],
            'identifier' => $contact['phone'] ?: $contact['email'],
        ]);
    }

    public function completeSetup(string $token): RedirectResponse
    {
        $contact = $this->pendingCustomerContact($token);

        if ($contact === null) {
            return redirect()->to(site_url('login'))->with('error', 'That setup link is invalid or has already been used.');
        }

        $rules = [
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $identityEmail = $contact['email'] ?: sprintf('customer-%d@setup.rentifypro.local', (int) $contact['user_id']);
        $userModel = model(UserModel::class);
        $user      = $userModel->find((int) $contact['user_id']);

        if (! $user instanceof User) {
            return redirect()->to(site_url('login'))->with('error', 'Customer account not found for setup.');
        }

        $user->email    = $identityEmail;
        $user->password = (string) $this->request->getPost('password');
        $user->active   = 1;

        $userModel->save($user);

        db_connect()->table('customer_contacts')
            ->where('id', $contact['id'])
            ->update([
                'setup_token'     => null,
                'password_set_at' => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to(site_url('login'))->with('message', 'Password created. You can now sign in with your email or phone number.');
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    private function validateCustomerInput(array $input): array
    {
        $errors = [];

        $rules = [
            'email' => 'permit_empty|valid_email|max_length[254]',
            'phone' => 'permit_empty|min_length[7]|max_length[30]|regex_match[/^[\d\s\-\+\(\)]+$/]',
        ];

        if (! $this->validateData($input, $rules)) {
            $errors = $this->validator->getErrors();
        }

        $normalizedEmail = $this->normalizeEmail((string) ($input['email'] ?? ''));
        $normalizedPhone = $this->normalizePhone((string) ($input['phone'] ?? ''));

        if ($normalizedEmail === '' && $normalizedPhone === '') {
            $errors['email'] = 'Provide either an email address or a phone number.';
        }

        $db = db_connect();

        if ($normalizedEmail !== '') {
            $existingByEmail = $db->table('customer_contacts')
            ->select('id')
            ->where('LOWER(email)', $normalizedEmail)
            ->get()
            ->getRowArray();

            if ($existingByEmail !== null) {
                $errors['email'] = 'That email is already in use.';
            }
        }

        if ($normalizedPhone !== '') {
            $existingByPhone = $db->table('customer_contacts')
                ->select('id')
                ->where('phone', $normalizedPhone)
                ->get()
                ->getRowArray();

            if ($existingByPhone !== null) {
                $errors['phone'] = 'That phone number is already in use.';
            }
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
            ->select('users.id, users.username, users.active, users.last_active, users.created_at, customer_contacts.email, customer_contacts.phone, customer_contacts.setup_token, customer_contacts.password_set_at')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->join('customer_contacts', 'customer_contacts.user_id = users.id', 'left')
            ->where('auth_groups_users.group', 'customer')
            ->orderBy('users.username', 'ASC')
            ->get()
            ->getResultArray();

        return array_values($rows);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function pendingCustomerContact(string $token): ?array
    {
        $contact = db_connect()->table('customer_contacts')
            ->select('id, user_id, email, phone, setup_token, password_set_at')
            ->where('setup_token', $token)
            ->get()
            ->getRowArray();

        if ($contact === null || $contact['password_set_at'] !== null) {
            return null;
        }

        return $contact;
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', trim($phone)) ?? '';
    }

    private function generateCustomerUsername(string $seed): string
    {
        $base = strtolower(trim($seed));
        $base = preg_replace('/[^a-z0-9]+/', '-', $base) ?? 'customer';
        $base = trim($base, '-');

        if ($base === '') {
            $base = 'customer';
        }

        $username = $base;
        $suffix = 1;
        $db = db_connect();

        while ($db->table('users')->select('id')->where('LOWER(username)', $username)->get()->getRowArray() !== null) {
            $suffix++;
            $username = $base . '-' . $suffix;
        }

        return $username;
    }
}