<?php

namespace Database\Seeders;

use App\Models\Sala;
use App\Models\Setor;
use Illuminate\Database\Seeder;

class SalasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = Setor::query()->get()->keyBy('codigo');

        $salas = [
            ['setor' => 'RECEP', 'nome' => 'Guichê 01', 'codigo' => 'G01', 'ordem' => 1],
            ['setor' => 'RECEP', 'nome' => 'Guichê 02', 'codigo' => 'G02', 'ordem' => 2],
            ['setor' => 'TRIAG', 'nome' => 'Sala de Triagem 01', 'codigo' => 'T01', 'ordem' => 1],
            ['setor' => 'TRIAG', 'nome' => 'Sala de Triagem 02', 'codigo' => 'T02', 'ordem' => 2],
            ['setor' => 'MEDIC', 'nome' => 'Consultório 01', 'codigo' => 'M01', 'ordem' => 1],
            ['setor' => 'MEDIC', 'nome' => 'Consultório 02', 'codigo' => 'M02', 'ordem' => 2],
        ];

        foreach ($salas as $salaData) {
            $setor = $setores[$salaData['setor']] ?? null;

            if (! $setor) {
                continue;
            }

            Sala::query()->updateOrCreate(
                [
                    'setor_id' => $setor->id,
                    'codigo' => $salaData['codigo'],
                ],
                [
                    'nome' => $salaData['nome'],
                    'ordem' => $salaData['ordem'],
                    'ativo' => true,
                ],
            );
        }
    }
}
