<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProntuarioResource extends JsonResource
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
            'paciente_id' => $this->paciente_id,
            'paciente' => $this->whenLoaded('paciente', fn () => new PacienteResource($this->paciente)),
            'resumo_clinico' => $this->resumo_clinico,
            'alergias' => $this->alergias,
            'comorbidades' => $this->comorbidades,
            'observacoes' => $this->observacoes,
            'ultimo_atendimento_em' => optional($this->ultimo_atendimento_em)?->toIso8601String(),
            'evolucoes' => $this->whenLoaded('evolucoes', fn () => $this->evolucoes->map(fn ($evolucao) => [
                'id' => $evolucao->id,
                'data_registro' => optional($evolucao->data_registro)?->toIso8601String(),
                'cid_codigo' => $evolucao->cid_codigo,
                'descricao' => $evolucao->descricao,
            ])),
        ];
    }
}
