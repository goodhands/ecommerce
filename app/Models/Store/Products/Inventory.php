<?php

namespace App\Models\Store\Products;

use App\Models\Store\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = "store_inventory";

    protected $guarded = [];

    public function product(){
        return $this->belongsTo(Product::class)
                    ->where('action', 'stock');
    }
}
