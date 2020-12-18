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
        'payment_method', 'delivery_method', 'store_id'
    ];

    protected $casts = [
        'fulfilled' => 'bool',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class, 'order_product')->withPivot(
            'quantity'
        );
    }
}
