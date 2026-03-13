<?php

namespace App\Services;

use App\Models\Atendimento;
use App\Models\Chamada;
use App\Models\Fila;
use App\Models\Senha;
use App\Models\Triagem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function resumo(?Carbon $inicio = null, ?Carbon $fim = null): array
    {
        $inicio = $inicio?->copy()->startOfDay() ?? now()->startOfDay();
        $fim = $fim?->copy()->endOfDay() ?? now()->endOfDay();

        $atendimentosHoje = Atendimento::query()
            ->whereBetween('created_at', [$inicio, $fim])
            ->count();

        $triagensHoje = Triagem::query()
            ->whereBetween('created_at', [$inicio, $fim])
            ->count();

        $senhasAguardando = Senha::query()
            ->where('status', 'aguardando')
            ->count();

        $senhasAtendimento = Senha::query()
            ->whereIn('status', ['chamado', 'em_atendimento', 'encaminhado'])
            ->count();

        $chamadasRecentes = Chamada::query()
            ->with(['senha.paciente', 'setor', 'sala'])
            ->latest('chamado_em')
            ->limit(12)
            ->get();

        $filasSnapshot = Fila::query()
            ->withCount([
                'senhas as aguardando_count' => fn (Builder $query) => $query->where('status', 'aguardando'),
                'senhas as chamado_count' => fn (Builder $query) => $query->where('status', 'chamado'),
                'senhas as em_atendimento_count' => fn (Builder $query) => $query->where('status', 'em_atendimento'),
            ])
            ->orderBy('ordem')
            ->get();

        return [
            'indicadores' => [
                'pacientes_atendidos_hoje' => $atendimentosHoje,
                'pacientes_aguardando' => $senhasAtendimento,
                'senhas_aguardando' => $senhasAguardando,
                'triagens_realizadas_hoje' => $triagensHoje,
                'atendimentos_medicos_hoje' => $atendimentosHoje,
            ],
            'chamadas_recentes' => $chamadasRecentes->map(fn (Chamada $chamada) => [
                'id' => $chamada->id,
                'senha' => $chamada->senha?->codigo,
                'paciente' => $chamada->senha?->paciente?->nome,
                'setor' => $chamada->setor?->nome,
                'sala' => $chamada->sala?->nome,
                'status' => $chamada->status,
                'chamado_em' => optional($chamada->chamado_em)?->toIso8601String(),
            ]),
            'filas' => $filasSnapshot->map(fn (Fila $fila) => [
                'id' => $fila->id,
                'nome' => $fila->nome,
                'tipo' => $fila->tipo,
                'aguardando' => $fila->aguardando_count,
                'chamado' => $fila->chamado_count,
                'em_atendimento' => $fila->em_atendimento_count,
            ]),
            'grafico_atendimentos_hora' => $this->graficoAtendimentosPorHora($inicio, $fim),
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{hora: string, total: int}>
     */
    protected function graficoAtendimentosPorHora(Carbon $inicio, Carbon $fim): Collection
    {
        $dados = Atendimento::query()
            ->selectRaw("to_char(created_at, 'HH24') as hora, count(*) as total")
            ->whereBetween('created_at', [$inicio, $fim])
            ->groupByRaw("to_char(created_at, 'HH24')")
            ->orderByRaw("to_char(created_at, 'HH24')")
            ->get()
            ->pluck('total', 'hora');

        return collect(range(0, 23))->map(function (int $hora) use ($dados): array {
            $key = str_pad((string) $hora, 2, '0', STR_PAD_LEFT);

            return [
                'hora' => "{$key}:00",
                'total' => (int) ($dados[$key] ?? 0),
            ];
        });
    }
}
