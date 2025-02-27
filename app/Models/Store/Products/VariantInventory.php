<?php

namespace App\Models\Store\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantInventory extends Model
{
    use HasFactory;

    protected $table = 'variant_inventory';

    protected $fillable = [
        'variant_id', 'variant', 'stock', 'price', 'media', 'product_id'
    ];

    protected $casts = [
        'media' => 'array',
        'variant' => 'array'
    ];
}
