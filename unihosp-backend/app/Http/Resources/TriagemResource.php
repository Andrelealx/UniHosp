<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TriagemResource extends JsonResource
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
            'paciente_id' => $this->paciente_id,
            'paciente' => $this->whenLoaded('paciente', fn () => new PacienteResource($this->paciente)),
            'profissional_id' => $this->profissional_id,
            'profissional' => $this->whenLoaded('profissional', fn () => new UserResource($this->profissional)),
            'pressao_arterial' => $this->pressao_arterial,
            'temperatura' => $this->temperatura,
            'saturacao' => $this->saturacao,
            'frequencia_cardiaca' => $this->frequencia_cardiaca,
            'peso' => $this->peso,
            'altura' => $this->altura,
            'glicemia' => $this->glicemia,
            'classificacao_risco' => $this->classificacao_risco,
            'observacoes' => $this->observacoes,
            'encaminhar_fila_id' => $this->encaminhar_fila_id,
            'iniciado_em' => optional($this->iniciado_em)?->toIso8601String(),
            'finalizado_em' => optional($this->finalizado_em)?->toIso8601String(),
            'created_at' => optional($this->created_at)?->toIso8601String(),
        ];
    }
}
