<?php

namespace App\Services;

use App\Models\Atendimento;
use App\Models\EvolucaoMedica;
use App\Models\Prescricao;
use App\Models\Prontuario;
use App\Models\Senha;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AtendimentoService
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
    public function registrar(array $data, User $medico): Atendimento
    {
        return DB::transaction(function () use ($data, $medico): Atendimento {
            $senha = Senha::query()->findOrFail($data['senha_id']);
            $this->senhaService->iniciarAtendimento($senha, $medico);

            $atendimento = Atendimento::query()->updateOrCreate(
                [
                    'senha_id' => $senha->id,
                ],
                [
                    'paciente_id' => $data['paciente_id'] ?? $senha->paciente_id,
                    'medico_id' => $medico->id,
                    'triagem_id' => $data['triagem_id'] ?? null,
                    'queixa_principal' => $data['queixa_principal'] ?? null,
                    'hipotese_diagnostica' => $data['hipotese_diagnostica'] ?? null,
                    'cid_codigo' => $data['cid_codigo'] ?? null,
                    'conduta' => $data['conduta'] ?? null,
                    'prescricao_resumo' => $data['prescricao_resumo'] ?? $data['prescricao_texto'] ?? null,
                    'status' => 'finalizado',
                    'iniciado_em' => $data['iniciado_em'] ?? now(),
                    'finalizado_em' => now(),
                ],
            );

            if (! empty($data['prescricao_texto'])) {
                Prescricao::query()->create([
                    'atendimento_id' => $atendimento->id,
                    'paciente_id' => $atendimento->paciente_id,
                    'medico_id' => $medico->id,
                    'conteudo' => $data['prescricao_texto'],
                    'orientacoes' => $data['orientacoes'] ?? null,
                    'emitida_em' => now(),
                ]);
            }

            $prontuario = Prontuario::query()->firstOrCreate(
                ['paciente_id' => $atendimento->paciente_id],
                [
                    'alergias' => optional($atendimento->paciente)->alergias,
                    'comorbidades' => optional($atendimento->paciente)->comorbidades,
                ],
            );

            $prontuario->update([
                'ultimo_atendimento_em' => now(),
                'resumo_clinico' => $data['hipotese_diagnostica'] ?? $prontuario->resumo_clinico,
                'observacoes' => $data['conduta'] ?? $prontuario->observacoes,
            ]);

            EvolucaoMedica::query()->create([
                'prontuario_id' => $prontuario->id,
                'atendimento_id' => $atendimento->id,
                'medico_id' => $medico->id,
                'descricao' => $data['conduta'] ?? $data['hipotese_diagnostica'] ?? 'Atendimento registrado.',
                'cid_codigo' => $data['cid_codigo'] ?? null,
                'data_registro' => now(),
            ]);

            $this->senhaService->finalizar($senha->fresh(), $medico);

            return $atendimento->fresh(['paciente', 'medico', 'triagem', 'prescricoes']);
        });
    }
}
