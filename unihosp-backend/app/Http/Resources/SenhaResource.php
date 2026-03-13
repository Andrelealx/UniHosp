<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SenhaResource extends JsonResource
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
            'codigo' => $this->codigo,
            'numero_sequencial' => $this->numero_sequencial,
            'data_referencia' => optional($this->data_referencia)?->toDateString(),
            'tipo_atendimento' => $this->tipo_atendimento,
            'prioridade' => $this->prioridade,
            'status' => $this->status,
            'paciente_id' => $this->paciente_id,
            'paciente' => $this->whenLoaded('paciente', fn () => new PacienteResource($this->paciente)),
            'fila_id' => $this->fila_id,
            'fila' => $this->whenLoaded('fila', fn () => new FilaResource($this->fila)),
            'setor_id' => $this->setor_id,
            'setor' => $this->whenLoaded('setor', fn () => new SetorResource($this->setor)),
            'sala_id' => $this->sala_id,
            'sala' => $this->whenLoaded('sala', fn () => new SalaResource($this->sala)),
            'encaminhada_para_fila_id' => $this->encaminhada_para_fila_id,
            'observacoes_iniciais' => $this->observacoes_iniciais,
            'horario_emissao' => optional($this->horario_emissao)?->toIso8601String(),
            'horario_chamada' => optional($this->horario_chamada)?->toIso8601String(),
            'horario_finalizacao' => optional($this->horario_finalizacao)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
