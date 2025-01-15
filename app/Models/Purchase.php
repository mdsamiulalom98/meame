<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    
    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id')->select('id','name','phone');
    }
    public function purchasedetails()
    {
        return $this->hasMany(PurchaseDetails::class, 'purchase_id');
    }
}
