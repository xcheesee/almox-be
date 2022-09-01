<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
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
            'departamento_id' => $this->departamento_id,
            'departamento' => $this->departamento ? $this->departamento->nome : null,
            'medida_id' => $this->medida_id,
            'medida' => $this->medida ? $this->medida->tipo : null,
            'tipo_item_id' => $this->tipo_item_id,
            'tipo_item' => $this->tipo_item ? $this->tipo_item->nome : null,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
        ];
    }
}
