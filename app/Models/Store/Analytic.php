<?php

namespace App\Models\Store;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Analytic extends Model
{
    use HasFactory;

    protected $table = 'ga_store';

    protected $fillable = [
        'store_id',
        'property_id',
        'measurement_id',
        'type',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
