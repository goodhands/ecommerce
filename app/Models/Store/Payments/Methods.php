<?php

namespace App\Models\Store\Payments;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Methods extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = [
        'label', 'active', 'methods', 'website', 'settings'
    ];

    protected $casts = [
        'methods' => 'array',
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
