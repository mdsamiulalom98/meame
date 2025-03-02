<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerComplaint extends Model
{
    use HasFactory;
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function images()
    {
        return $this->hasMany(ComplaintImage::class, 'complaint_id')->select('id', 'image', 'complaint_id');
    }
}
