<?php

namespace App\Models\Store;

use App\Models\Store;
use App\Models\Store\Category\Category;
use App\Models\Store\Collections\Collections;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $table = 'store_product';

    protected $fillable = [
        'name', 'shortname', 'price', 'discount', 'product_type', 'media_library',
        'description', 'collection_id', 'category_id', 'status', 'stock', 'sku'
    ];

    protected $casts = [
        'media_library' => 'array'
    ];

    public function store() : BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collections::class);
    }
}
