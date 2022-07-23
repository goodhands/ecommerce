<?php

namespace App\Models\Store\Delivery\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Store extends Pivot
{
    protected $fillable = [
        'notes', 'active', 'flat_rate',
        'conditional_pricing',
    ];

    protected $casts = [
        'active' => 'bool',
    ];
}
