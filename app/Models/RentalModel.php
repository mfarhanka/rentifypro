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

    public function detailedQuery()
    {
        return $this->select('rentals.*, gadgets.name AS gadget_name, gadgets.brand AS gadget_brand, gadgets.code AS gadget_code, users.username AS customer_name')
            ->join('gadgets', 'gadgets.id = rentals.gadget_id')
            ->join('users', 'users.id = rentals.customer_id');
    }
}