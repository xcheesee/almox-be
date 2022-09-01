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
            'departamento' => $this->departamento ? $this->departamento->nome : null,
            'ordem_servico_id' => $this->ordem_servico_id,
            'almoxarife_nome' => $this->almoxarife_nome,
            'almoxarife_email' => $this->almoxarife_email,
            'baixa_user_id' => $this->baixa_user_id,
            'baixa_user' => $this->baixa_user->name,
            'baixa_datahora' => $this->baixa_datahora,
        ];
    }
}
