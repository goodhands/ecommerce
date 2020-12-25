<?php

namespace App\Models\Store\Delivery;

use App\Models\Store;
use App\Models\Store\Delivery\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Methods extends Model
{
    use HasFactory;

    protected $table = 'deliverys';

    protected $fillable = [
        'name', 'description', 'label'
    ];

    protected $casts = [
        'active' => 'bool'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function region()
    {
        return $this->hasMany(Region::class, 'delivery_id', 'id');
    }

    public function secret()
    {
        return $this->hasOne(Secrets::class, 'provider_id', 'id');
    }
}
