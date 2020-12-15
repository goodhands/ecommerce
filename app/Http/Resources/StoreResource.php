<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            "store" => [
                "id" => $this->id,
                "name" => $this->name,
                "shortname" => $this->shortname,
                "category" => $this->category,
                "size" => $this->size,
                "industry" => $this->industry,
                "users" => StoreUsersCollection::collection($this->users)
            ]
        ];
    }
}
