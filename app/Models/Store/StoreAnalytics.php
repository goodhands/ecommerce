<?php

namespace App\Models\Store;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreAnalytics extends Model
{
    use HasFactory;

    protected $table = 'ga_store';

    protected $fillable = [
        'store_id', 'ga_store_id', 'view_id','url', 'ga_tracking_id', 'date_created', 'date_updated'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
