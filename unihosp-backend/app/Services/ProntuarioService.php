<?php

namespace App\Services;

use App\Models\Atendimento;
use App\Models\Paciente;
use App\Models\Prontuario;
use App\Models\Senha;
use App\Models\Triagem;
use Illuminate\Support\Collection;

class ProntuarioService
{
    /**
     * @return array<string, mixed>
     */
    public function obterPorPaciente(Paciente $paciente): array
    {
        $prontuario = Prontuario::query()->firstOrCreate(
            ['paciente_id' => $paciente->id],
            [
                'alergias' => $paciente->alergias,
                'comorbidades' => $paciente->comorbidades,
                'observacoes' => $paciente->observacoes,
            ],
        );

        $prontuario->load([
            'paciente.convenio',
            'evolucoes.medico',
        ]);

        return [
            'prontuario' => $prontuario,
            'timeline' => $this->gerarTimeline($paciente),
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function gerarTimeline(Paciente $paciente): Collection
    {
        $senhas = Senha::query()
            ->where('paciente_id', $paciente->id)
            ->latest('created_at')
            ->limit(20)
            ->get()
            ->map(fn (Senha $senha) => [
                'tipo' => 'senha',
                'data' => optional($senha->created_at)?->toIso8601String(),
                'titulo' => "Senha {$senha->codigo} ({$senha->status})",
                'detalhes' => "Fila: {$senha->fila?->nome}",
            ]);

        $triagens = Triagem::query()
            ->where('paciente_id', $paciente->id)
            ->latest('created_at')
            ->limit(20)
            ->get()
            ->map(fn (Triagem $triagem) => [
                'tipo' => 'triagem',
                'data' => optional($triagem->created_at)?->toIso8601String(),
                'titulo' => 'Triagem registrada',
                'detalhes' => $triagem->classificacao_risco ? "Risco: {$triagem->classificacao_risco}" : null,
            ]);

        $atendimentos = Atendimento::query()
            ->where('paciente_id', $paciente->id)
            ->latest('created_at')
            ->limit(20)
            ->get()
            ->map(fn (Atendimento $atendimento) => [
                'tipo' => 'atendimento',
                'data' => optional($atendimento->created_at)?->toIso8601String(),
                'titulo' => 'Atendimento médico',
                'detalhes' => $atendimento->hipotese_diagnostica,
            ]);

        return $senhas
            ->concat($triagens)
            ->concat($atendimentos)
            ->sortByDesc('data')
            ->values();
    }
}
