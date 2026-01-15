<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'name', 'sku', 'price', 'stock_quantity', 'low_stock_threshold'];

    public function tenant() {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function cart() {
        return $this->hasMany(Cart::class,'product_id');
    }
}
