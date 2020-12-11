<?php

namespace App\Models\Store;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * This model will be referred to externally 
 * as StoreUser
 */
class User extends Model
{
    protected $table = 'store_user';

    use HasFactory;

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
