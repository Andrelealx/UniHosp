<?php

namespace App\Services;

use App\Models\Fila;
use App\Models\Prontuario;
use App\Models\Senha;
use App\Models\Triagem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TriagemService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected SenhaService $senhaService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function registrar(array $data, User $profissional): Triagem
    {
        return DB::transaction(function () use ($data, $profissional): Triagem {
            $senha = Senha::query()
                ->with('paciente')
                ->findOrFail($data['senha_id']);

            $triagem = Triagem::query()->updateOrCreate(
                ['senha_id' => $senha->id],
                [
                    'paciente_id' => $data['paciente_id'] ?? $senha->paciente_id,
                    'profissional_id' => $profissional->id,
                    'pressao_arterial' => $data['pressao_arterial'] ?? null,
                    'temperatura' => $data['temperatura'] ?? null,
                    'saturacao' => $data['saturacao'] ?? null,
                    'frequencia_cardiaca' => $data['frequencia_cardiaca'] ?? null,
                    'peso' => $data['peso'] ?? null,
                    'altura' => $data['altura'] ?? null,
                    'glicemia' => $data['glicemia'] ?? null,
                    'classificacao_risco' => $data['classificacao_risco'] ?? null,
                    'observacoes' => $data['observacoes'] ?? null,
                    'encaminhar_fila_id' => $data['encaminhar_fila_id'] ?? null,
                    'iniciado_em' => $data['iniciado_em'] ?? now(),
                    'finalizado_em' => now(),
                ],
            );

            if (! empty($data['encaminhar_fila_id'])) {
                $filaDestino = Fila::query()->findOrFail($data['encaminhar_fila_id']);
                $this->senhaService->encaminhar($senha, $filaDestino);
            } else {
                $this->senhaService->iniciarAtendimento($senha, $profissional);
            }

            Prontuario::query()->firstOrCreate(
                ['paciente_id' => $senha->paciente_id],
                [
                    'alergias' => $senha->paciente?->alergias,
                    'comorbidades' => $senha->paciente?->comorbidades,
                    'observacoes' => $senha->paciente?->observacoes,
                ],
            );

            return $triagem->fresh(['paciente', 'profissional', 'filaEncaminhamento', 'senha']);
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function chamarProximo(Fila $fila, User $profissional, array $payload = []): ?Senha
    {
        return $this->senhaService->chamarProximo($fila, $profissional, $payload);
    }
}
