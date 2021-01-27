<?php

namespace App\Models\Store;

use App\Models\Store;
use App\Models\Store\Category\Category;
use App\Models\Store\Collections\Collections;
use App\Models\Store\Products\Inventory;
use App\Models\Store\Products\Variant;
use App\Models\Store\Products\VariantInventory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Product extends Model
{
    use HasFactory;

    protected $table = 'store_product';

    protected $with = ['inventory', 'variant', 'variantInventory'];

    protected $fillable = [
        'name', 'shortname', 'price', 'discount', 'product_type', 'media_library',
        'description', 'collection_id', 'category_id', 'status', 'stock', 'sku',
        'weight', 'isbn'
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

    public function scopeDateBetween(Builder $query, $start, $end){
        return $query->whereBetween('created_at', [Carbon::parse($start), Carbon::parse($end)]);
    }

    public function inventory(){
        return $this->hasMany(Inventory::class);
    }

    public function variant(){
        return $this->hasMany(Variant::class);
    }

    // Each variant has its own stock and other details
    public function variantInventory(){
        return $this->hasMany(VariantInventory::class);
    }
}
