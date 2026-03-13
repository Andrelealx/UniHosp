<?php

namespace App\Services;

use App\Models\Chamada;
use App\Models\Painel;
use Illuminate\Database\Eloquent\Builder;

class PainelService
{
    /**
     * @return array<string, mixed>
     */
    public function dadosPorSlug(string $slug): array
    {
        $painel = Painel::query()
            ->with('setor')
            ->where('slug', $slug)
            ->where('ativo', true)
            ->firstOrFail();

        $chamadasQuery = Chamada::query()
            ->with(['senha.paciente', 'setor', 'sala', 'usuario'])
            ->where(function (Builder $query) use ($painel): void {
                $query->where('painel_id', $painel->id);

                if ($painel->setor_id) {
                    $query->orWhere('setor_id', $painel->setor_id);
                }
            })
            ->latest('chamado_em');

        $chamadaAtual = (clone $chamadasQuery)->first();
        $ultimasChamadas = (clone $chamadasQuery)->limit(8)->get();

        return [
            'painel' => $painel,
            'chamada_atual' => $chamadaAtual ? $this->mapChamada($chamadaAtual, $painel->forma_exibicao_paciente) : null,
            'ultimas_chamadas' => $ultimasChamadas->map(fn (Chamada $chamada) => $this->mapChamada($chamada, $painel->forma_exibicao_paciente)),
            'horario_atual' => now()->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapChamada(Chamada $chamada, string $formaExibicao): array
    {
        $paciente = $chamada->senha?->paciente?->nome;

        return [
            'id' => $chamada->id,
            'senha' => $chamada->senha?->codigo,
            'paciente' => $this->formatarPaciente($paciente, $formaExibicao),
            'setor' => $chamada->setor?->nome,
            'sala' => $chamada->sala?->nome,
            'profissional' => $chamada->usuario?->name,
            'status' => $chamada->status,
            'tipo' => $chamada->tipo,
            'horario' => optional($chamada->chamado_em)?->format('H:i:s'),
        ];
    }

    protected function formatarPaciente(?string $nome, string $formaExibicao): ?string
    {
        if (! $nome) {
            return null;
        }

        return match ($formaExibicao) {
            'senha' => null,
            'senha_iniciais' => collect(explode(' ', trim($nome)))
                ->filter()
                ->map(fn (string $parte) => strtoupper(substr($parte, 0, 1)))
                ->implode(''),
            'senha_primeiro_nome' => (function () use ($nome): string {
                $partes = collect(explode(' ', trim($nome)))->filter()->values();
                $primeiro = $partes->first() ?? '';
                $sobrenome = $partes->count() > 1 ? substr((string) $partes->last(), 0, 1).'.' : '';

                return trim("{$primeiro} {$sobrenome}");
            })(),
            default => $nome,
        };
    }
}
