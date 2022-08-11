<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Entrada extends JsonResource
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
            'local_id' => $this->local_id,
            'processo_sei' => $this->processo_sei,
            'numero_contrato' => $this->numero_contrato,
            'numero_nota_fiscal' => $this->numero_nota_fiscal,
            'arquivo_nota_fiscal' => $this->arquivo_nota_fiscal,
        ];
    }
}
