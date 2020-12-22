<?php

namespace App\Models\Store;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMethods extends Model
{
    use HasFactory;

    protected $table = 'delivery_methods';

    protected $fillable = [
        'label', 'active', 'store_id'
    ];

    protected $casts = [
        'active' => 'bool'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function secret()
    {
        return $this->hasOne(Secrets::class, 'provider_id', 'id');
    }
}
