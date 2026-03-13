<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AtendimentoResource extends JsonResource
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
            'medico_id' => $this->medico_id,
            'medico' => $this->whenLoaded('medico', fn () => new UserResource($this->medico)),
            'triagem_id' => $this->triagem_id,
            'triagem' => $this->whenLoaded('triagem', fn () => new TriagemResource($this->triagem)),
            'queixa_principal' => $this->queixa_principal,
            'hipotese_diagnostica' => $this->hipotese_diagnostica,
            'cid_codigo' => $this->cid_codigo,
            'conduta' => $this->conduta,
            'prescricao_resumo' => $this->prescricao_resumo,
            'status' => $this->status,
            'iniciado_em' => optional($this->iniciado_em)?->toIso8601String(),
            'finalizado_em' => optional($this->finalizado_em)?->toIso8601String(),
            'created_at' => optional($this->created_at)?->toIso8601String(),
        ];
    }
}
