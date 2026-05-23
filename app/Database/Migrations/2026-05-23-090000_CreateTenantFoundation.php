<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTenantFoundation extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('tenants')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                ],
                'slug' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->createTable('tenants');
        }

        if (! $this->db->tableExists('tenant_users')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'tenant_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['tenant_id', 'user_id']);
            $this->forge->addKey('tenant_id');
            $this->forge->addKey('user_id');
            $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('tenant_users');
        }

        if (! $this->db->fieldExists('tenant_id', 'gadgets')) {
            $this->forge->addColumn('gadgets', [
                'tenant_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
            $this->db->query('ALTER TABLE `gadgets` ADD INDEX `gadgets_tenant_id_index` (`tenant_id`)');
            $this->db->query('ALTER TABLE `gadgets` ADD CONSTRAINT `gadgets_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        }

        if (! $this->db->fieldExists('tenant_id', 'customer_contacts')) {
            $this->forge->addColumn('customer_contacts', [
                'tenant_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
            $this->db->query('ALTER TABLE `customer_contacts` ADD INDEX `customer_contacts_tenant_id_index` (`tenant_id`)');
            $this->db->query('ALTER TABLE `customer_contacts` ADD CONSTRAINT `customer_contacts_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        }

        if (! $this->db->fieldExists('tenant_id', 'rentals')) {
            $this->forge->addColumn('rentals', [
                'tenant_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
            $this->db->query('ALTER TABLE `rentals` ADD INDEX `rentals_tenant_id_index` (`tenant_id`)');
            $this->db->query('ALTER TABLE `rentals` ADD CONSTRAINT `rentals_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        }

        $tenant = $this->db->table('tenants')
            ->select('id')
            ->where('slug', 'rentifypro-default')
            ->get()
            ->getRowArray();

        if ($tenant === null) {
            $this->db->table('tenants')->insert([
                'name'       => 'Rentify Pro Default Workspace',
                'slug'       => 'rentifypro-default',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $tenant = [
                'id' => $this->db->insertID(),
            ];
        }

        $tenantId = (int) $tenant['id'];
        $timestamp = date('Y-m-d H:i:s');

        $users = $this->db->table('users')->select('id')->get()->getResultArray();
        foreach ($users as $user) {
            $membership = $this->db->table('tenant_users')
                ->select('id')
                ->where('tenant_id', $tenantId)
                ->where('user_id', $user['id'])
                ->get()
                ->getRowArray();

            if ($membership === null) {
                $this->db->table('tenant_users')->insert([
                    'tenant_id'  => $tenantId,
                    'user_id'    => $user['id'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        }

        $this->db->table('gadgets')->where('tenant_id', null)->set(['tenant_id' => $tenantId])->update();
        $this->db->table('customer_contacts')->where('tenant_id', null)->set(['tenant_id' => $tenantId])->update();
        $this->db->table('rentals')->where('tenant_id', null)->set(['tenant_id' => $tenantId])->update();
    }

    public function down(): void
    {
        if ($this->db->fieldExists('tenant_id', 'rentals')) {
            $this->db->query('ALTER TABLE `rentals` DROP FOREIGN KEY `rentals_tenant_id_foreign`');
            $this->db->query('ALTER TABLE `rentals` DROP INDEX `rentals_tenant_id_index`');
            $this->forge->dropColumn('rentals', 'tenant_id');
        }

        if ($this->db->fieldExists('tenant_id', 'customer_contacts')) {
            $this->db->query('ALTER TABLE `customer_contacts` DROP FOREIGN KEY `customer_contacts_tenant_id_foreign`');
            $this->db->query('ALTER TABLE `customer_contacts` DROP INDEX `customer_contacts_tenant_id_index`');
            $this->forge->dropColumn('customer_contacts', 'tenant_id');
        }

        if ($this->db->fieldExists('tenant_id', 'gadgets')) {
            $this->db->query('ALTER TABLE `gadgets` DROP FOREIGN KEY `gadgets_tenant_id_foreign`');
            $this->db->query('ALTER TABLE `gadgets` DROP INDEX `gadgets_tenant_id_index`');
            $this->forge->dropColumn('gadgets', 'tenant_id');
        }

        $this->forge->dropTable('tenant_users', true);
        $this->forge->dropTable('tenants', true);
    }
}