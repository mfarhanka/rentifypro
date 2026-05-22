<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRentalTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'brand' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'daily_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'stock' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'default'    => 1,
            ],
            'available_stock' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'default'    => 1,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('gadgets');

        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'invoice_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
            ],
            'gadget_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'customer_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'processed_by' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'quantity' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'default'    => 1,
            ],
            'rental_days' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
            ],
            'daily_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'returned_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey('invoice_number');
        $this->forge->addKey('gadget_id');
        $this->forge->addKey('customer_id');
        $this->forge->addForeignKey('gadget_id', 'gadgets', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('processed_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('rentals');
    }

    public function down()
    {
        $this->forge->dropTable('rentals', true);
        $this->forge->dropTable('gadgets', true);
    }
}