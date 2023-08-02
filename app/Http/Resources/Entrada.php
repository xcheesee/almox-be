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
            'departamento' => $this->departamento ? $this->departamento->nome : null,
            'local_id' => $this->local_id,
            'local' => $this->local ? $this->local->nome : null,
            'data_entrada' => $this->data_entrada,
            'data_entrada_formatada' => $this->data_entrada_formatada,
            'processo_sei' => $this->processo_sei,
            'processo_sei_formatado' => $this->processo_sei_formatado,
            'numero_contrato' => $this->numero_contrato,
            'numero_contrato_formatado' => $this->numero_contrato_formatado,
            'numero_nota_fiscal' => $this->numero_nota_fiscal,
            'arquivo_nota_fiscal' => $this->arquivo_nota_fiscal,
            'arquivo_nota_fiscal_url' => $this->arquivo_nota_fiscal_url,
        ];
    }
}
