<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customer::class,'user_id')->select('id','name','phone');
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class,'user_id')->select('id','name','phone');
    }
}
