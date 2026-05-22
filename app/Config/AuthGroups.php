<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'customer';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'admin' => [
            'title'       => 'Administrator',
            'description' => 'Manage staff, rentals, invoices, and gadget catalog.',
        ],
        'staff' => [
            'title'       => 'Staff',
            'description' => 'Handle day-to-day gadget rentals and invoice processing.',
        ],
        'customer' => [
            'title'       => 'Customer',
            'description' => 'Browse gadgets, place rentals, and view invoices.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'dashboard.access'      => 'Can access the dashboard',
        'gadgets.manage'        => 'Can create and update gadgets',
        'rentals.manage'        => 'Can manage all rentals',
        'rentals.create'        => 'Can create a rental request',
        'invoices.view-all'     => 'Can view any invoice',
        'invoices.view-own'     => 'Can view own invoices',
        'users.manage-staff'    => 'Can manage staff accounts',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'admin' => [
            'dashboard.access',
            'gadgets.manage',
            'rentals.manage',
            'rentals.create',
            'invoices.view-all',
            'invoices.view-own',
            'users.manage-staff',
        ],
        'staff' => [
            'dashboard.access',
            'gadgets.manage',
            'rentals.manage',
            'invoices.view-all',
        ],
        'customer' => [
            'dashboard.access',
            'rentals.create',
            'invoices.view-own',
        ],
    ];
}
