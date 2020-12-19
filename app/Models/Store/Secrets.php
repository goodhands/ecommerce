<?php

namespace App\Models\Store;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Secrets extends Model
{
    use HasFactory;

    protected $table = 'store_secrets';

    protected $fillable = [
        'provider_id', 'provider_type', 'api_key', 'public_key', 'secret_key'
    ];

    public function store() : BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
