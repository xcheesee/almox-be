<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Profissional extends JsonResource
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
            'local_tipo' => $this->local ? $this->local->tipo : null,
            'nome' => $this->nome,
            'profissao' => $this->profissao,
            'completo' => $this->completo,
        ];
    }
}
