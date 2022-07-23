<?php

namespace App\Models\Store\Delivery;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'delivery_region';

    protected $fillable = [
        'delivery_id', 'location', 'price'
    ];
}
