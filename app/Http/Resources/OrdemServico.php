<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdemServico extends JsonResource
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
            'origem_id' => $this->origem_id,
            'origem' => $this->origem ? $this->origem->nome : null,
            'local_servico_id' => $this->local_servico_id,
            'local_servico' => $this->local_servico ? $this->local_servico->nome : null,
            'status' => $this->status,
            'data_inicio_servico' => $this->data_inicio_servico,
            'data_fim_servico' => $this->data_fim_servico,
            'almoxarife_nome' => $this->almoxarife_nome,
            'almoxarife_email' => $this->almoxarife_email,
            'especificacao' => $this->especificacao,
            'profissional' => $this->profissional,
            'horas_execucao' => $this->horas_execucao,
            'observacoes' => $this->observacoes,
            'flg_baixa' => $this->flg_baixa,
            'user_id' => $this->user_id,
            'user' => $this->user->name,
        ];
    }
}
