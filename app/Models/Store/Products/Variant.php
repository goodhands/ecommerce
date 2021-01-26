<?php

namespace App\Models\Store\Products;

use App\Models\Store\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;

    protected $table = 'variants';

    protected $casts = [
        'values' => 'array'
    ];

    protected $fillable = [
        'type', 'product_id', 'values'
    ];

    // Each variant has its own stock and other details
    public function inventory(){
        return $this->hasMany(VariantInventory::class);
    }

    //Variants belong to a product
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
