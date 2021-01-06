<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    use HasFactory;

    protected $casts = [
        'promotionals' => 'bool'
    ];

    protected $fillable = [
        'lastname', 'firstname', 'store_id', 'email', 'promotionals', 'address', 'apartment',
        'city', 'state', 'country', 'postal'
    ];

    public function scopeDateBetween(Builder $query, $start, $end){
        return $query->whereBetween('created_at', [Carbon::parse($start), Carbon::parse($end)]);
    }
}
