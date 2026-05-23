<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Entities\User;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * @var list<string>
     */
    protected $helpers = ['auth', 'form', 'url'];

    protected ?User $currentUser = null;

    /**
     * @var array<string, mixed>|null
     */
    protected ?array $currentTenant = null;

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        $this->currentUser = auth()->user();

        if ($this->currentUser !== null) {
            $this->currentTenant = $this->resolveCurrentTenant();
        }

        service('renderer')->setVar('currentTenant', $this->currentTenant);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    protected function user(): ?User
    {
        return $this->currentUser;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function tenant(): ?array
    {
        return $this->currentTenant;
    }

    protected function tenantId(): ?int
    {
        return $this->currentTenant === null ? null : (int) $this->currentTenant['id'];
    }

    protected function primaryRole(): string
    {
        if ($this->currentUser === null) {
            return 'guest';
        }

        foreach (['admin', 'staff', 'customer'] as $group) {
            if ($this->currentUser->inGroup($group)) {
                return $group;
            }
        }

        return 'customer';
    }

    /**
     * @param list<string> $groups
     */
    protected function requireGroups(array $groups): ?RedirectResponse
    {
        if ($this->currentUser === null) {
            return redirect()->to(site_url('login'));
        }

        if ($this->tenantId() === null) {
            return redirect()->to(site_url('/'))->with('error', 'Your account is not assigned to a workspace.');
        }

        foreach ($groups as $group) {
            if ($this->currentUser->inGroup($group)) {
                return null;
            }
        }

        return redirect()->to(site_url('dashboard'))->with('error', 'You do not have access to that page.');
    }

    protected function requireTenant(): ?RedirectResponse
    {
        if ($this->currentUser === null) {
            return redirect()->to(site_url('login'));
        }

        if ($this->tenantId() === null) {
            return redirect()->to(site_url('/'))->with('error', 'Your account is not assigned to a workspace.');
        }

        return null;
    }

    protected function assignUserToCurrentTenant(int $userId): void
    {
        $tenantId = $this->tenantId();

        if ($tenantId === null) {
            return;
        }

        $existing = db_connect()->table('tenant_users')
            ->select('id')
            ->where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if ($existing !== null) {
            return;
        }

        db_connect()->table('tenant_users')->insert([
            'tenant_id'   => $tenantId,
            'user_id'     => $userId,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    protected function userBelongsToCurrentTenant(int $userId): bool
    {
        $tenantId = $this->tenantId();

        if ($tenantId === null) {
            return false;
        }

        return db_connect()->table('tenant_users')
            ->select('id')
            ->where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray() !== null;
    }

    protected function userTenantCount(int $userId): int
    {
        return db_connect()->table('tenant_users')
            ->where('user_id', $userId)
            ->countAllResults();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveCurrentTenant(): ?array
    {
        if ($this->currentUser === null) {
            return null;
        }

        return db_connect()->table('tenant_users')
            ->select('tenants.id, tenants.name, tenants.slug')
            ->join('tenants', 'tenants.id = tenant_users.tenant_id')
            ->where('tenant_users.user_id', $this->currentUser->id)
            ->orderBy('tenant_users.id', 'ASC')
            ->get()
            ->getRowArray();
    }
}
