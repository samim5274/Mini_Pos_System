<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg',
        'date',
        'name',
        'phone',
        'address',
        'total',
        'discount',
        'payable',
        'pay',
        'duestatus',
        'due',
        'return',
        'status',
        'userId',
    ];

    // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
