<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class StaffController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin'])) {
            return $redirect;
        }

        return view('staff/index', [
            'staffMembers' => $this->staffRows(),
        ]);
    }

    public function create(): string|RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin'])) {
            return $redirect;
        }

        return view('staff/form', [
            'title'       => 'Add Staff Account',
            'action'      => '/staff',
            'staffMember' => null,
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin'])) {
            return $redirect;
        }

        $input = $this->request->getPost(['username', 'email', 'password', 'password_confirm']);

        if ($errors = $this->validateStaffInput($input)) {
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
        $createdUser?->syncGroups('staff');

        return redirect()->to('/staff')->with('message', 'Staff account created.');
    }

    public function edit(int $id): string|RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin'])) {
            return $redirect;
        }

        $staffMember = $this->findStaffMember($id);

        if ($staffMember === null) {
            return redirect()->to('/staff')->with('error', 'Staff member not found.');
        }

        return view('staff/form', [
            'title'       => 'Edit Staff Account',
            'action'      => '/staff/' . $id,
            'staffMember' => $staffMember,
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin'])) {
            return $redirect;
        }

        $staffMember = $this->findStaffMember($id);

        if ($staffMember === null) {
            return redirect()->to('/staff')->with('error', 'Staff member not found.');
        }

        $input = $this->request->getPost(['username', 'email', 'password', 'password_confirm']);

        if ($errors = $this->validateStaffInput($input, $staffMember)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $userModel = model(UserModel::class);

        $payload = [
            'username' => (string) $input['username'],
            'email'    => strtolower((string) $input['email']),
        ];

        if (($input['password'] ?? '') !== '') {
            $payload['password'] = (string) $input['password'];
        }

        $userModel->update($staffMember->id, $payload);

        $refreshedUser = $userModel->withGroups()->find($staffMember->id);
        $refreshedUser?->syncGroups('staff');

        return redirect()->to('/staff')->with('message', 'Staff account updated.');
    }

    public function toggleSuspend(int $id): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin'])) {
            return $redirect;
        }

        $staffMember = $this->findStaffMember($id);

        if ($staffMember === null) {
            return redirect()->to('/staff')->with('error', 'Staff member not found.');
        }

        $newActive = $staffMember->active ? 0 : 1;
        model(UserModel::class)->update($staffMember->id, ['active' => $newActive]);

        $message = $newActive === 1 ? 'Staff account reactivated.' : 'Staff account suspended.';

        return redirect()->to('/staff')->with('message', $message);
    }

    public function delete(int $id): RedirectResponse
    {
        if ($redirect = $this->requireGroups(['admin'])) {
            return $redirect;
        }

        $staffMember = $this->findStaffMember($id);

        if ($staffMember === null) {
            return redirect()->to('/staff')->with('error', 'Staff member not found.');
        }

        model(UserModel::class)->delete($staffMember->id, true);

        return redirect()->to('/staff')->with('message', 'Staff account removed.');
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    private function validateStaffInput(array $input, ?User $staffMember = null): array
    {
        $errors = [];

        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]',
            'email'            => 'required|valid_email|max_length[254]',
            'password'         => $staffMember === null ? 'required|min_length[8]' : 'permit_empty|min_length[8]',
            'password_confirm' => $staffMember === null || ($input['password'] ?? '') !== '' ? 'required|matches[password]' : 'permit_empty',
        ];

        if (! $this->validateData($input, $rules)) {
            $errors = $this->validator->getErrors();
        }

        $db            = db_connect();
        $normalizedEmail = strtolower((string) ($input['email'] ?? ''));
        $normalizedUsername = strtolower((string) ($input['username'] ?? ''));

        $existingByEmail = $db->table('auth_identities')
            ->select('user_id')
            ->where('type', 'email_password')
            ->where('LOWER(secret)', $normalizedEmail)
            ->get()
            ->getRowArray();

        if ($existingByEmail !== null && ($staffMember === null || (int) $existingByEmail['user_id'] !== (int) $staffMember->id)) {
            $errors['email'] = 'That email is already in use.';
        }

        $existingByUsername = $db->table('users')
            ->select('id')
            ->where('LOWER(username)', $normalizedUsername)
            ->get()
            ->getRowArray();

        if ($existingByUsername !== null && ($staffMember === null || (int) $existingByUsername['id'] !== (int) $staffMember->id)) {
            $errors['username'] = 'That username is already in use.';
        }

        return $errors;
    }

    private function findStaffMember(int $id): ?User
    {
        $user = model(UserModel::class)->withGroups()->find($id);

        if (! $user instanceof User || ! $user->inGroup('staff')) {
            return null;
        }

        return $user;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function staffRows(): array
    {
        $rows = db_connect()
            ->table('users')
            ->select('users.id, users.username, users.active, users.last_active, users.created_at, auth_identities.secret AS email')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->join('auth_identities', "auth_identities.user_id = users.id AND auth_identities.type = 'email_password'", 'left')
            ->where('auth_groups_users.group', 'staff')
            ->orderBy('users.username', 'ASC')
            ->get()
            ->getResultArray();

        return array_values($rows);
    }
}