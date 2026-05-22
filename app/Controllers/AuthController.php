<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Traits\Viewable;

class AuthController extends BaseController
{
    use Viewable;

    public function loginView(): string|RedirectResponse
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        if ($authenticator->hasAction()) {
            return redirect()->route('auth-action-show');
        }

        return $this->view(setting('Auth.views')['login']);
    }

    public function loginAction(): RedirectResponse
    {
        $rules = [
            'identifier' => 'required|max_length[254]',
            'password'   => 'required',
        ];

        if (! $this->validateData($this->request->getPost(), $rules, [], config('Auth')->DBGroup)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $identifier = trim((string) $this->request->getPost('identifier'));
        $credentials = $this->credentialsFromIdentifier($identifier);
        $credentials['password'] = (string) $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        $result = $authenticator->remember($remember)->attempt($credentials);
        if (! $result->isOK()) {
            return redirect()->to(site_url('login'))->withInput()->with('error', $result->reason());
        }

        if ($authenticator->hasAction()) {
            return redirect()->route('auth-action-show')->withCookies();
        }

        return redirect()->to(config('Auth')->loginRedirect())->withCookies();
    }

    /**
     * @return array{email?: string, username?: string}
     */
    private function credentialsFromIdentifier(string $identifier): array
    {
        $normalized = trim($identifier);

        if (filter_var($normalized, FILTER_VALIDATE_EMAIL) !== false) {
            return ['email' => strtolower($normalized)];
        }

        if (preg_match('/^[\d\s\-\+\(\)]+$/', $normalized) === 1) {
            $digits = preg_replace('/\D+/', '', $normalized) ?? '';

            if ($digits !== '') {
                $identityEmail = $this->emailIdentityForPhone($digits);

                if ($identityEmail !== null) {
                    return ['email' => $identityEmail];
                }

                return ['username' => $digits];
            }
        }

        return ['username' => strtolower($normalized)];
    }

    private function emailIdentityForPhone(string $phone): ?string
    {
        $contact = db_connect()->table('customer_contacts')
            ->select('user_id')
            ->where('phone', $phone)
            ->get()
            ->getRowArray();

        if ($contact === null) {
            return null;
        }

        $identity = db_connect()->table('auth_identities')
            ->select('secret')
            ->where('user_id', $contact['user_id'])
            ->where('type', 'email_password')
            ->get()
            ->getRowArray();

        return $identity['secret'] ?? null;
    }
}