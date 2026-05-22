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
        'code',
        'name',
        'brand',
        'daily_rate',
        'stock',
        'available_stock',
        'description',
    ];
    protected $useTimestamps = true;
}