<?php

namespace App\Services;

use App\Events\PainelAtualizado;
use App\Events\SenhaCriada;
use App\Models\Fila;
use App\Models\Paciente;
use App\Models\Senha;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RecepcaoService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function emitirSenha(array $data, User $usuario): Senha
    {
        return DB::transaction(function () use ($data, $usuario): Senha {
            $fila = Fila::query()->with('setor')->findOrFail($data['fila_id']);
            $paciente = Paciente::query()->findOrFail($data['paciente_id']);

            $sequencial = (int) Senha::query()
                ->whereDate('data_referencia', now()->toDateString())
                ->max('numero_sequencial') + 1;

            $prefixo = strtoupper(substr((string) ($fila->codigo ?: $data['tipo_atendimento']), 0, 1));
            $codigo = sprintf('%s%03d', $prefixo, $sequencial);

            $senha = Senha::query()->create([
                'codigo' => $codigo,
                'numero_sequencial' => $sequencial,
                'data_referencia' => now()->toDateString(),
                'tipo_atendimento' => $data['tipo_atendimento'],
                'prioridade' => $data['prioridade'] ?? 'normal',
                'paciente_id' => $paciente->id,
                'fila_id' => $fila->id,
                'setor_id' => $data['setor_id'] ?? $fila->setor_id,
                'sala_id' => $data['sala_id'] ?? null,
                'status' => 'aguardando',
                'observacoes_iniciais' => $data['observacoes_iniciais'] ?? null,
                'emitida_por_user_id' => $usuario->id,
                'horario_emissao' => now(),
            ]);

            $payload = [
                'senha_id' => $senha->id,
                'codigo' => $senha->codigo,
                'fila_id' => $fila->id,
                'setor_id' => $senha->setor_id,
                'status' => $senha->status,
            ];

            event(new SenhaCriada($payload));
            event(new PainelAtualizado($payload));

            return $senha->load(['paciente', 'fila', 'setor']);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function abrirFichaPaciente(Paciente $paciente): array
    {
        return [
            'paciente' => $paciente->load(['convenio']),
            'senhas_recentes' => $paciente->senhas()
                ->with(['fila', 'setor'])
                ->latest('horario_emissao')
                ->limit(10)
                ->get(),
        ];
    }
}
