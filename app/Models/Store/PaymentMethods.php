<?php

namespace App\Models\Store;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $guarded = [];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function secret()
    {
        return $this->hasOne(Secrets::class, 'provider_id', 'id');
    }
}
