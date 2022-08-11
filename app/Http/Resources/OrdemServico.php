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
            'origem_id' => $this->origem_id,
            'destino_id' => $this->destino_id,
            'local_servico_id' => $this->local_servico_id,
            'almoxarife_nome' => $this->almoxarife_nome,
            'almoxarife_email' => $this->almoxarife_email,
            'almoxarife_cargo' => $this->almoxarife_cargo,
            'data_servico' => $this->data_servico,
            'especificacao' => $this->especificacao,
            'profissional' => $this->profissional,
            'horas_execucao' => $this->horas_execucao,
            'observacoes' => $this->observacoes,
            'user_id' => $this->user_id,
        ];
    }
}
