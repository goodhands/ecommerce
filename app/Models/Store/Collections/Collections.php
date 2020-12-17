<?php

namespace App\Models\Store\Collections;

use App\Models\Store\Collections\Category;
use App\Models\Store\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collections extends Model
{
    protected $table = 'collections';

    protected $fillable = [
        'name', 'shortname', 'createdBy', 'hasAutomation', 'description'
    ];
    
    use HasFactory;

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'collection_id', 'id');
    }

    public function condition()
    {
        return $this->hasMany(Conditions::class);
    }

    public function getRouteKeyName()
    {
        return 'shortname';
    }
}
