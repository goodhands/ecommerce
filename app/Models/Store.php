<?php

namespace App\Models;

use App\Models\Store\Collections\Collections;
use App\Models\Store\Customer;
use App\Models\Store\DeliveryMethods;
use App\Models\Store\Order;
use App\Models\Store\PaymentMethods;
use App\Models\Store\Product;
use App\Models\Store\Secrets;
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
        return $this->hasMany(Product::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(
            'firstname', 'lastname', 'role'
        );
    }

    public function secrets()
    {
        return $this->hasMany(Secrets::class);
    }

    public function delivery(): HasMany
    {
        return $this->hasMany(DeliveryMethods::class);
    }

    public function payment(): HasMany
    {
        return $this->hasMany(PaymentMethods::class)->where('active', 1);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collections::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function getRouteKeyName()
    {
        return 'shortname';
    }
}
