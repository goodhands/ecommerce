<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $casts = [
        'promotionals' => 'bool'
    ];

    protected $fillable = [
        'lastname', 'firstname', 'store_id', 'email', 'promotionals', 'address', 'apartment',
        'city', 'state', 'country', 'postal'
    ];
}
