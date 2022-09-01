<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaidaItem extends JsonResource
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
            'id' => $this->id,
            'saida_id' => $this->saida_id,
            'item_id' => $this->item_id,
            'item' => $this->item ? $this->item->nome : null,
            'medida' => $this->item ? $this->item->medida->tipo : null,
            'quantidade' => $this->quantidade,
        ];
    }
}
