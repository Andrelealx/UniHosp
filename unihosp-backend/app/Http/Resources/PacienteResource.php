<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PacienteResource extends JsonResource
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
            'prontuario_codigo' => $this->prontuario_codigo,
            'nome' => $this->nome,
            'nome_social' => $this->nome_social,
            'data_nascimento' => optional($this->data_nascimento)?->toDateString(),
            'sexo' => $this->sexo,
            'nome_mae' => $this->nome_mae,
            'cpf' => $this->cpf,
            'cns' => $this->cns,
            'rg' => $this->rg,
            'telefone' => $this->telefone,
            'telefone_secundario' => $this->telefone_secundario,
            'email' => $this->email,
            'endereco' => $this->endereco,
            'convenio_id' => $this->convenio_id,
            'convenio' => $this->whenLoaded('convenio', fn () => new ConvenioResource($this->convenio)),
            'responsavel_nome' => $this->responsavel_nome,
            'responsavel_telefone' => $this->responsavel_telefone,
            'alergias' => $this->alergias,
            'comorbidades' => $this->comorbidades,
            'observacoes' => $this->observacoes,
            'ativo' => $this->ativo,
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
