<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChamadaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'senha_id' => $this->senha_id,
            'senha' => $this->whenLoaded('senha', fn () => new SenhaResource($this->senha)),
            'usuario_id' => $this->usuario_id,
            'usuario' => $this->whenLoaded('usuario', fn () => new UserResource($this->usuario)),
            'setor_id' => $this->setor_id,
            'setor' => $this->whenLoaded('setor', fn () => new SetorResource($this->setor)),
            'sala_id' => $this->sala_id,
            'painel_id' => $this->painel_id,
            'tipo' => $this->tipo,
            'status' => $this->status,
            'repeticao' => $this->repeticao,
            'mensagem' => $this->mensagem,
            'chamado_em' => optional($this->chamado_em)?->toIso8601String(),
        ];
    }
}
