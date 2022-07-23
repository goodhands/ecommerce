<?php

namespace App\Models\Store\Payments\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PaymentStore extends Pivot
{
    protected $fillable = [
        'notes', 'active', 'channels'
    ];

    protected $casts = [
        'active' => 'bool',
        'channels' => 'array'
    ];

}
