<?php

namespace App\Models;

use App\Models\Store\Collections\Collections;
use App\Models\Store\Customer;
use App\Models\Store\Delivery\Methods as DeliveryMethods;
use App\Models\Store\Delivery\Pivot\Store as DeliveryStore;
use App\Models\Store\Order;
use App\Models\Store\Payments\Pivot\PaymentStore;
use App\Models\Store\Payments\Methods as PaymentMethods;
use App\Models\Store\Product;
use App\Models\Store\Secrets;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function delivery(): BelongsToMany
    {
        return $this->belongsToMany(DeliveryMethods::class, 'delivery_store', 'store_id', 'delivery_id')
                ->using(DeliveryStore::class)->withPivot(['flat_rate', 'notes', 'conditional_pricing', 'active'])
                ->withTimestamps()
                ->where('active', '1');
    }

    public function payment(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethods::class, 'payment_store', 'store_id', 'payment_id')
                ->using(PaymentStore::class)->withPivot(['notes', 'active', 'channels'])
                ->withTimestamps()
                ->where('active', 1);
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
