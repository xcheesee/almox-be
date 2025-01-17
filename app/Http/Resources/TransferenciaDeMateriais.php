<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransferenciaDeMateriais extends JsonResource
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
            'destino' => $this->base_destino ? $this->base_destino->nome : null,
            'origem' => $this->base_origem ? $this->base_origem->nome : null,
            'origem_json' => $this->base_origem ? $this->base_origem : null,
            'base_destino_id' => $this->base_destino_id,
            'base_origem_id' => $this->base_origem_id,
            'data_transferencia' => $this->data_transferencia,
            'status' => $this->status,
            'observacao' => $this->observacao,
            'observacao_motivo' => $this->observacao_motivo,
            'observacao_user_id' => $this->observacao_user_id,
        ];
    }
}
