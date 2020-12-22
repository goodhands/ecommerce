<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $table = 'store_order';

    protected $fillable = [
        'payment_status', 'product_id', 'customer_id', 'total', 'fulfilled',
        'payment_method', 'delivery_method', 'store_id', 'delivery_date'
    ];

    protected $casts = [
        'fulfilled' => 'bool',
        'delivery_date' => 'datetime',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function delivery(){
        return $this->hasOne(DeliveryMethods::class, 'id', 'delivery_method');
    }

    public function products(){
        return $this->belongsToMany(Product::class, 'order_product')->withPivot(
            'quantity'
        );
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}
