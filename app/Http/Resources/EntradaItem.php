<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntradaItem extends JsonResource
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
            'entrada_id' => $this->entrada_id,
            'item_id' => $this->item_id,
            'item' => $this->item ? $this->item->nome : null,
            'medida' => $this->item ? $this->item->medida->tipo : null,
            'medida_id' => $this->item ? $this->item->medida->id : null,
            'quantidade' => $this->quantidade,
            'tipo_item_id'=> $this->item ? $this->item->tipo_item_id : null,
            'tipo_item' => $this->item ? $this->item->tipo_item->nome : null,
        ];
    }
}
