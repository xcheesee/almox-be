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
            'origem_id' => $this->origem_id,
            'origem' => $this->origem ? $this->origem->nome : null,
            'tipo_servico_id' => $this->tipo_servico_id,
            'tipo_servico' => $this->tipo_servico ? $this->tipo_servico->servico : null,
            'local_servico_id' => $this->local_servico_id,
            'local_servico' => $this->local_servico ? $this->local_servico->nome : null,
            'justificativa_os' => $this->justificativa_os,
            'especificacao' => $this->especificacao,
            'observacoes' => $this->observacoes,
            'status' => $this->status,
            'baixa_user_id' => $this->baixa_user_id,
            'baixa_user' => $this->baixa_user ? $this->baixa_user->name : null,
            'baixa_datahora' => $this->baixa_datahora,
            'flg_baixa' => $this->flg_baixa
        ];
    }
}
