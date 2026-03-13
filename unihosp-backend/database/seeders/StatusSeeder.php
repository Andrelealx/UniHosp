<?php

namespace Database\Seeders;

use App\Models\Fila;
use App\Models\Paciente;
use App\Models\Senha;
use App\Models\User;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'recepcao@unihosp.local')->first();
        $fila = Fila::query()->where('codigo', 'FG')->first();
        $setor = $fila?->setor;

        if (! $user || ! $fila || ! $setor) {
            return;
        }

        $paciente = Paciente::query()->firstOrCreate(
            ['cpf' => '22222222222'],
            [
                'prontuario_codigo' => sprintf('PRT-%s-%04d', now()->format('Ymd'), 9999),
                'nome' => 'Paciente Exemplo Status',
                'telefone' => '(11) 98888-7777',
                'sexo' => 'O',
            ],
        );

        $statuses = ['aguardando', 'chamado', 'em_atendimento', 'ausente', 'finalizado', 'cancelado', 'encaminhado'];

        foreach ($statuses as $index => $status) {
            Senha::query()->updateOrCreate(
                ['codigo' => sprintf('S%03d', $index + 1)],
                [
                    'numero_sequencial' => 900 + $index,
                    'data_referencia' => now()->toDateString(),
                    'tipo_atendimento' => 'consulta',
                    'prioridade' => 'normal',
                    'paciente_id' => $paciente->id,
                    'fila_id' => $fila->id,
                    'setor_id' => $setor->id,
                    'status' => $status,
                    'emitida_por_user_id' => $user->id,
                    'chamada_por_user_id' => $user->id,
                    'finalizada_por_user_id' => in_array($status, ['ausente', 'finalizado', 'cancelado'], true) ? $user->id : null,
                    'horario_emissao' => now()->subMinutes(30 - $index),
                    'horario_chamada' => in_array($status, ['chamado', 'em_atendimento', 'ausente', 'finalizado', 'cancelado'], true) ? now()->subMinutes(20 - $index) : null,
                    'horario_finalizacao' => in_array($status, ['ausente', 'finalizado', 'cancelado'], true) ? now()->subMinutes(10 - $index) : null,
                ],
            );
        }
    }
}
