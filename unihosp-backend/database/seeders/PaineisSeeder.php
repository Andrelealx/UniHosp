<?php

namespace Database\Seeders;

use App\Models\Painel;
use App\Models\Setor;
use Illuminate\Database\Seeder;

class PaineisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = Setor::query()->get()->keyBy('codigo');

        $paineis = [
            [
                'nome' => 'Painel Recepção',
                'slug' => 'recepcao',
                'tipo' => 'recepcao',
                'setor' => 'RECEP',
                'mensagem_institucional' => 'Bem-vindo ao UniHosp.',
                'forma_exibicao_paciente' => 'senha',
            ],
            [
                'nome' => 'Painel Triagem',
                'slug' => 'triagem',
                'tipo' => 'triagem',
                'setor' => 'TRIAG',
                'mensagem_institucional' => 'Triagem em andamento.',
                'forma_exibicao_paciente' => 'senha_iniciais',
            ],
            [
                'nome' => 'Painel Médico',
                'slug' => 'medico',
                'tipo' => 'medico',
                'setor' => 'MEDIC',
                'mensagem_institucional' => 'Aguarde a chamada para consultório.',
                'forma_exibicao_paciente' => 'senha_primeiro_nome',
            ],
        ];

        foreach ($paineis as $painelData) {
            $setor = $setores[$painelData['setor']] ?? null;

            Painel::query()->updateOrCreate(
                ['slug' => $painelData['slug']],
                [
                    'nome' => $painelData['nome'],
                    'tipo' => $painelData['tipo'],
                    'setor_id' => $setor?->id,
                    'mensagem_institucional' => $painelData['mensagem_institucional'],
                    'forma_exibicao_paciente' => $painelData['forma_exibicao_paciente'],
                    'ativo' => true,
                ],
            );
        }
    }
}
