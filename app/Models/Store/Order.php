<?php

namespace App\Models\Store;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store\Delivery\Methods as DeliveryMethods;
use App\Models\Store\Payments\Methods as PaymentMethods;

class Order extends Model
{
    use HasFactory;

    protected $table = 'store_order';

    protected $fillable = [
        'payment_status', 'product_id', 'customer_id', 'total', 'fulfilled',
        'payment_method', 'delivery_method', 'store_id', 'delivery_date',
        'delivery_address'
    ];

    protected $casts = [
        'fulfilled' => 'bool',
        'delivery_date' => 'datetime',
        'delivery_address' => 'array',
        'payment_data'  => 'array',
        'products.pivot.variant' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment()
    {
        return $this->hasOne(PaymentMethods::class, 'id', 'payment_method');
    }

    public function delivery()
    {
        return $this->hasOne(DeliveryMethods::class, 'id', 'delivery_method');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')->withPivot(
            array(
                'quantity',
                'variant'
            )
        );
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function scopePaid(Builder $query, $paymentStatus)
    {
        if ($paymentStatus) {
            $result = $query->where('payment_status', 'Paid');
        } else {
            $result = $query->where('payment_status', '!=', 'Paid');
        }

        return $result;
    }

    public function scopeDateBetween(Builder $query, $start, $end)
    {
        return $query->whereBetween('created_at', [Carbon::parse($start), Carbon::parse($end)]);
    }
}
