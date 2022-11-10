<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResponsaveisEmail extends JsonResource
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
            'nome' => $this->nome,
            'email' => $this->email,
            'ativo' => $this->ativo,
        ];
    }
}
