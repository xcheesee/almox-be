<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaidaProfissional extends JsonResource
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
            'saida_id' => $this->ordem_servico_id,
            'profissional_id' => $this->profissional_id,
            'profissional' => $this->nome ? $this->nome : null,
            'data_inicio' => $this->data_inicio,
            'data_inicio_formatada' => $this->data_inicio_formatada,
            'horas_empregadas' => $this->horas_empregadas,
        ];
    }
}
