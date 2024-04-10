<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class local_users extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "local_id" => $this->local_id,
            "user_id" => $this->user_id
        ];
    }
}
