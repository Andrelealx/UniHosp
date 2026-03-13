<?php

namespace Database\Seeders;

use App\Models\Setor;
use Illuminate\Database\Seeder;

class SetoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = [
            [
                'nome' => 'Recepção',
                'codigo' => 'RECEP',
                'tipo' => 'recepcao',
                'descricao' => 'Atendimento inicial e emissão de senhas.',
            ],
            [
                'nome' => 'Triagem',
                'codigo' => 'TRIAG',
                'tipo' => 'triagem',
                'descricao' => 'Classificação de risco e sinais vitais.',
            ],
            [
                'nome' => 'Consultório Médico',
                'codigo' => 'MEDIC',
                'tipo' => 'medico',
                'descricao' => 'Atendimento médico e prontuário clínico.',
            ],
        ];

        foreach ($setores as $setor) {
            Setor::query()->updateOrCreate(
                ['codigo' => $setor['codigo']],
                array_merge($setor, ['ativo' => true]),
            );
        }
    }
}
