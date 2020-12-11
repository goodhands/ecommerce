<?php

namespace App\Models\Store;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMethods extends Model
{
    use HasFactory;

    protected $table = 'delivery_methods';

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
