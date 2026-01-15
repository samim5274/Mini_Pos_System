<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function products() {
        return $this->hasMany(Product::class, 'tenant_id');
    }

    public function orders() {
        return $this->hasMany(Order::class, 'tenant_id');
    }

    public function cart() {
        return $this->hasMany(Cart::class,'tenant_id');
    }
}
