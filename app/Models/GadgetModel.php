<?php

namespace App\Models;

use CodeIgniter\Model;

class GadgetModel extends Model
{
    protected $table            = 'gadgets';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'tenant_id',
        'code',
        'name',
        'brand',
        'daily_rate',
        'stock',
        'available_stock',
        'description',
    ];
    protected $useTimestamps = true;

    public function forTenant(int $tenantId): self
    {
        return $this->where('tenant_id', $tenantId);
    }
}