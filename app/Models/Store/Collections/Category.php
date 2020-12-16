<?php

namespace App\Models\Store\Collections;

use App\Models\Store\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'category';
    
    use HasFactory;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collections::class);
    }
}
