<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PainelResource extends JsonResource
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
            'nome' => $this->nome,
            'slug' => $this->slug,
            'tipo' => $this->tipo,
            'setor_id' => $this->setor_id,
            'setor' => $this->whenLoaded('setor', fn () => new SetorResource($this->setor)),
            'mensagem_institucional' => $this->mensagem_institucional,
            'forma_exibicao_paciente' => $this->forma_exibicao_paciente,
            'logo_url' => $this->logo_url,
            'ativo' => $this->ativo,
        ];
    }
}
