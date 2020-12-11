<?php

namespace App\Models\Store\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'product_categories';
    
    use HasFactory;

    public function subCategory()
    {
        return $this->hasMany(SubCategory::class);
    }
}
