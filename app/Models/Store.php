<?php

namespace App\Models;

use App\Models\Store\DeliveryMethods;
use App\Models\Store\PaymentMethods;
use App\Models\Store\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'store_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(
            'firstname', 'lastname', 'role'
        );
    }

    public function delivery(): HasMany
    {
        return $this->hasMany(DeliveryMethods::class);
    }

    public function payment(): HasMany
    {
        return $this->hasMany(PaymentMethods::class);
    }

    public function getRouteKeyName()
    {
        return 'shortname';
    }
}
