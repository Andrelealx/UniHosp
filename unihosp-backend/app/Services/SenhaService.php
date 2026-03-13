<?php

namespace App\Services;

use App\Events\PacienteAusente;
use App\Events\PainelAtualizado;
use App\Events\SenhaChamada;
use App\Events\SenhaEncaminhada;
use App\Events\SenhaFinalizada;
use App\Models\Chamada;
use App\Models\Fila;
use App\Models\Senha;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SenhaService
{
    /**
     * @return array<string, mixed>
     */
    public function snapshotFilas(): array
    {
        $filas = Fila::query()
            ->with('setor')
            ->withCount([
                'senhas as aguardando_count' => fn (Builder $q) => $q->where('status', 'aguardando'),
                'senhas as chamado_count' => fn (Builder $q) => $q->where('status', 'chamado'),
                'senhas as em_atendimento_count' => fn (Builder $q) => $q->where('status', 'em_atendimento'),
                'senhas as ausente_count' => fn (Builder $q) => $q->where('status', 'ausente'),
            ])
            ->orderBy('ordem')
            ->get();

        return [
            'filas' => $filas->map(fn (Fila $fila) => [
                'id' => $fila->id,
                'nome' => $fila->nome,
                'codigo' => $fila->codigo,
                'tipo' => $fila->tipo,
                'setor' => $fila->setor?->nome,
                'aguardando' => $fila->aguardando_count,
                'chamado' => $fila->chamado_count,
                'em_atendimento' => $fila->em_atendimento_count,
                'ausente' => $fila->ausente_count,
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function chamarProximo(Fila $fila, User $usuario, array $payload = []): ?Senha
    {
        return DB::transaction(function () use ($fila, $usuario, $payload): ?Senha {
            /** @var Senha|null $senha */
            $senha = Senha::query()
                ->where('fila_id', $fila->id)
                ->where('status', 'aguardando')
                ->orderByRaw("CASE prioridade WHEN 'urgente' THEN 1 WHEN 'prioritario' THEN 2 ELSE 3 END")
                ->orderBy('horario_emissao')
                ->lockForUpdate()
                ->first();

            if (! $senha) {
                return null;
            }

            $senha->update([
                'status' => 'chamado',
                'horario_chamada' => now(),
                'chamada_por_user_id' => $usuario->id,
                'sala_id' => $payload['sala_id'] ?? $senha->sala_id,
            ]);

            $this->registrarChamada($senha, $usuario, array_merge($payload, ['tipo' => $payload['tipo'] ?? 'chamada']));

            $eventPayload = $this->eventPayload($senha->fresh(['paciente', 'fila', 'setor', 'sala']));
            event(new SenhaChamada($eventPayload));
            event(new PainelAtualizado($eventPayload));

            return $senha->fresh(['paciente', 'fila', 'setor', 'sala']);
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function rechamar(Senha $senha, User $usuario, array $payload = []): Senha
    {
        DB::transaction(function () use ($senha, $usuario, $payload): void {
            $senha->update([
                'status' => 'chamado',
                'horario_chamada' => now(),
                'chamada_por_user_id' => $usuario->id,
                'sala_id' => $payload['sala_id'] ?? $senha->sala_id,
            ]);

            $this->registrarChamada($senha, $usuario, array_merge($payload, ['tipo' => 'rechamada']));

            $eventPayload = $this->eventPayload($senha);
            event(new SenhaChamada($eventPayload));
            event(new PainelAtualizado($eventPayload));
        });

        return $senha->fresh(['paciente', 'fila', 'setor', 'sala']);
    }

    public function iniciarAtendimento(Senha $senha, User $usuario): Senha
    {
        $senha->update([
            'status' => 'em_atendimento',
            'chamada_por_user_id' => $usuario->id,
            'horario_chamada' => $senha->horario_chamada ?? now(),
        ]);

        event(new PainelAtualizado($this->eventPayload($senha)));

        return $senha->fresh(['paciente', 'fila', 'setor', 'sala']);
    }

    public function marcarAusente(Senha $senha, User $usuario): Senha
    {
        $senha->update([
            'status' => 'ausente',
            'finalizada_por_user_id' => $usuario->id,
            'horario_finalizacao' => now(),
        ]);

        $payload = $this->eventPayload($senha);
        event(new PacienteAusente($payload));
        event(new PainelAtualizado($payload));

        return $senha->fresh(['paciente', 'fila', 'setor', 'sala']);
    }

    public function cancelar(Senha $senha, User $usuario): Senha
    {
        $senha->update([
            'status' => 'cancelado',
            'finalizada_por_user_id' => $usuario->id,
            'horario_finalizacao' => now(),
        ]);

        event(new PainelAtualizado($this->eventPayload($senha)));

        return $senha->fresh(['paciente', 'fila', 'setor', 'sala']);
    }

    public function encaminhar(Senha $senha, Fila $filaDestino): Senha
    {
        $senha->update([
            'status' => 'encaminhado',
            'encaminhada_para_fila_id' => $filaDestino->id,
            'fila_id' => $filaDestino->id,
            'setor_id' => $filaDestino->setor_id,
        ]);

        $payload = $this->eventPayload($senha->fresh(['paciente', 'fila', 'setor', 'sala']));
        event(new SenhaEncaminhada($payload));
        event(new PainelAtualizado($payload));

        return $senha->fresh(['paciente', 'fila', 'setor', 'sala']);
    }

    public function finalizar(Senha $senha, User $usuario): Senha
    {
        $senha->update([
            'status' => 'finalizado',
            'finalizada_por_user_id' => $usuario->id,
            'horario_finalizacao' => now(),
        ]);

        $payload = $this->eventPayload($senha);
        event(new SenhaFinalizada($payload));
        event(new PainelAtualizado($payload));

        return $senha->fresh(['paciente', 'fila', 'setor', 'sala']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function registrarChamada(Senha $senha, User $usuario, array $payload): Chamada
    {
        $repeticoes = Chamada::query()
            ->where('senha_id', $senha->id)
            ->where('tipo', $payload['tipo'] ?? 'chamada')
            ->count();

        return Chamada::query()->create([
            'senha_id' => $senha->id,
            'usuario_id' => $usuario->id,
            'setor_id' => $senha->setor_id,
            'sala_id' => $payload['sala_id'] ?? $senha->sala_id,
            'painel_id' => $payload['painel_id'] ?? null,
            'tipo' => $payload['tipo'] ?? 'chamada',
            'status' => 'emitida',
            'repeticao' => $repeticoes,
            'mensagem' => $payload['mensagem'] ?? null,
            'chamado_em' => now(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function eventPayload(Senha $senha): array
    {
        $senha->loadMissing(['paciente', 'fila', 'setor', 'sala']);

        return [
            'senha_id' => $senha->id,
            'codigo' => $senha->codigo,
            'status' => $senha->status,
            'setor_id' => $senha->setor_id,
            'setor' => $senha->setor?->nome,
            'fila_id' => $senha->fila_id,
            'fila' => $senha->fila?->nome,
            'sala' => $senha->sala?->nome,
            'paciente' => $senha->paciente?->nome,
            'horario_chamada' => optional($senha->horario_chamada)?->toIso8601String(),
            'updated_at' => optional($senha->updated_at)?->toIso8601String(),
        ];
    }
}
