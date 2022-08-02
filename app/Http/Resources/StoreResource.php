<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "shortname" => $this->shortname,
            "category" => $this->category,
            "size" => $this->size,
            "industry" => $this->industry,
            "url" => $this->url,
            $this->mergeWhen($request->has('administrators'), [
                "administrators" => new StoreAdminCollection($this->users)
            ]),
        ];
    }
}
