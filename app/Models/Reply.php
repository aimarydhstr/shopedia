<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'reply',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
