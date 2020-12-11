<?php

namespace App\Models\Store\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'product_sub_categories';
    
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function type()
    {
        return $this->hasMany(CategoryType::class);
    }
}
