<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg',
        'date',
        'user_id',
        'tenant_id',
        'status',
        'total'
    ];

    public function tenant() {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
