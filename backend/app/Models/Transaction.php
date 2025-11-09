<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'remitter_id', 'receiver_id', 'amount', 'status', 'reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

}
