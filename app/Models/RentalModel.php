<?php

namespace App\Models;

use CodeIgniter\Model;

class RentalModel extends Model
{
    protected $table            = 'rentals';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'tenant_id',
        'invoice_number',
        'gadget_id',
        'customer_id',
        'processed_by',
        'quantity',
        'rental_days',
        'daily_rate',
        'total_amount',
        'start_date',
        'end_date',
        'status',
        'notes',
        'returned_at',
    ];
    protected $useTimestamps = true;

    public function forTenant(int $tenantId): self
    {
        return $this->where('rentals.tenant_id', $tenantId);
    }

    public function detailedQuery(?int $tenantId = null)
    {
        $builder = $this->select('rentals.*, gadgets.name AS gadget_name, gadgets.brand AS gadget_brand, gadgets.code AS gadget_code, users.username AS customer_name')
            ->join('gadgets', 'gadgets.id = rentals.gadget_id')
            ->join('users', 'users.id = rentals.customer_id');

        if ($tenantId !== null) {
            $builder->where('rentals.tenant_id', $tenantId);
        }

        return $builder;
    }
}