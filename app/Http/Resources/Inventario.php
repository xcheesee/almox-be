<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Inventario extends JsonResource
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
            'item_id' => $this->item_id,
            'item' => $this->item ? $this->item->nome : null,
            'tipo_item' => $this->item && $this->item->tipo_item_id ? $this->item->tipo_item->nome : null,
            'medida' => $this->item ? $this->item->medida->tipo : null,
            'local_id' => $this->local_id,
            'local' => $this->local ? $this->local->nome : null,
            'local_tipo' => $this->local ? $this->local->tipo : null,
            'quantidade' => $this->quantidade,
            'qtd_alerta' => $this->qtd_alerta,
        ];
    }
}
