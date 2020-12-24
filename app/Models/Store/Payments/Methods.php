<?php

namespace App\Models\Store\Payments;

use App\Models\Store;
use App\Models\Store\Secrets;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PaymentStore;

class Methods extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'label', 'name', 'type', 'rates', 'website', 'description'
    ];

    public function store()
    {
        return $this->belongsToMany(Store::class, 'payment_store')->using(PaymentStore::class);
    }

    public function secret()
    {
        return $this->hasOne(Secrets::class, 'provider_id', 'id');
    }
}
