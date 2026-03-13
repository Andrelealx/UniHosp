<?php

namespace App\Services;

use App\Models\Atendimento;
use App\Models\Chamada;
use App\Models\Paciente;
use App\Models\Senha;
use App\Models\Triagem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class RelatorioService
{
    /**
     * @return array<string, mixed>
     */
    public function gerar(Carbon $inicio, Carbon $fim, ?string $setorId = null, ?string $filaId = null): array
    {
        $inicio = $inicio->copy()->startOfDay();
        $fim = $fim->copy()->endOfDay();

        $senhasQuery = Senha::query()
            ->whereBetween('created_at', [$inicio, $fim])
            ->when($setorId, fn (Builder $q) => $q->where('setor_id', $setorId))
            ->when($filaId, fn (Builder $q) => $q->where('fila_id', $filaId));

        $triagensQuery = Triagem::query()
            ->whereBetween('created_at', [$inicio, $fim]);

        $atendimentosQuery = Atendimento::query()
            ->whereBetween('created_at', [$inicio, $fim]);

        $chamadasQuery = Chamada::query()
            ->whereBetween('created_at', [$inicio, $fim])
            ->when($setorId, fn (Builder $q) => $q->where('setor_id', $setorId));

        $tempoMedioEsperaSegundos = (float) (clone $senhasQuery)
            ->whereNotNull('horario_chamada')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (horario_chamada - horario_emissao))) as tempo_medio')
            ->value('tempo_medio');

        return [
            'periodo' => [
                'inicio' => $inicio->toDateString(),
                'fim' => $fim->toDateString(),
            ],
            'pacientes_cadastrados' => Paciente::query()->whereBetween('created_at', [$inicio, $fim])->count(),
            'pacientes_atendidos' => (clone $atendimentosQuery)->count(),
            'senhas_emitidas' => (clone $senhasQuery)->count(),
            'tempo_medio_espera_minutos' => round($tempoMedioEsperaSegundos / 60, 2),
            'triagens_realizadas' => (clone $triagensQuery)->count(),
            'atendimentos_medicos' => (clone $atendimentosQuery)->count(),
            'chamadas_realizadas' => (clone $chamadasQuery)->count(),
            'status_senhas' => [
                'aguardando' => (clone $senhasQuery)->where('status', 'aguardando')->count(),
                'chamado' => (clone $senhasQuery)->where('status', 'chamado')->count(),
                'em_atendimento' => (clone $senhasQuery)->where('status', 'em_atendimento')->count(),
                'ausente' => (clone $senhasQuery)->where('status', 'ausente')->count(),
                'finalizado' => (clone $senhasQuery)->where('status', 'finalizado')->count(),
                'cancelado' => (clone $senhasQuery)->where('status', 'cancelado')->count(),
                'encaminhado' => (clone $senhasQuery)->where('status', 'encaminhado')->count(),
            ],
        ];
    }
}
