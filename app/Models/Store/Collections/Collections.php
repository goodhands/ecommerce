<?php

namespace App\Models\Store\Collections;

use App\Models\Store\Collections\Category;
use App\Models\Store\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collections extends Model
{
    protected $table = 'product_categories';
    
    use HasFactory;

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function condition()
    {
        return $this->hasMany(Conditions::class);
    }
}
