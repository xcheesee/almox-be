<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Saida extends JsonResource
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
            'ordem_servico_id' => $this->ordem_servico_id,
            'almoxarife_nome' => $this->almoxarife_nome,
            'almoxarife_email' => $this->almoxarife_email,
            'almoxarife_cargo' => $this->almoxarife_cargo,
            'baixa_user_id' => $this->baixa_user_id,
            'baixa_datahora' => $this->baixa_datahora,
        ];
    }
}
