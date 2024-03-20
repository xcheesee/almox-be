<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Historico extends JsonResource
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
            // 'departamento_id' => $this->departamento_id,
            // 'departamento' => $this->departamento ? $this->departamento->nome : null,
            'data_acao' => $this->data_acao,
            'data_acao_formatada' => $this->data_acao_formatada,
            'nome_tabela' => $this->nome_tabela,
            'tipo_acao' => $this->tipo_acao,
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->name : null,
        ];
    }
}
