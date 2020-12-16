<?php

namespace App\Models\Store\Collections;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conditions extends Model
{
    use HasFactory;

    protected $table = 'collection_conditions';

    public function collection()
    {
        return $this->belongsTo(Collections::class);
    }
}
