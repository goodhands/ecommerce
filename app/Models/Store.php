<?php

namespace App\Models;

use App\Models\Store\DeliveryMethods;
use App\Models\Store\PaymentMethods;
use App\Models\Store\Product;
use App\Models\Store\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class)->withPivot([
            'firstname', 'lastname', 'role'
        ]);
    }

    public function delivery(): HasMany
    {
        return $this->hasMany(DeliveryMethods::class);
    }

    public function payment(): HasMany
    {
        return $this->hasMany(PaymentMethods::class);
    }
}
