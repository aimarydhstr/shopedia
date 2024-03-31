<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'subtotal',
        'tax',
        'total',
        'image',
        'status',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
