<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaResource extends JsonResource
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
            'setor_id' => $this->setor_id,
            'setor' => $this->whenLoaded('setor', fn () => new SetorResource($this->setor)),
            'nome' => $this->nome,
            'codigo' => $this->codigo,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
        ];
    }
}
