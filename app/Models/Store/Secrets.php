<?php

namespace App\Models\Store;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Secrets extends Model
{
    use HasFactory;

    public function store() : BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
