<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransfer extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from');
    }

    // Relationship to the 'to' warehouse
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to');
    }
}
