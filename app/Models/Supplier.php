<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function purchase(){
        return $this->hasMany(Purchase::class,'supplier_id');
    }
    public function payments(){
        return $this->hasMany(Transaction::class,'user_id')->where('type','purchase');
    }
}
