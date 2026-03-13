<?php

namespace Database\Seeders;

use App\Models\Fila;
use App\Models\Setor;
use Illuminate\Database\Seeder;

class FilasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = Setor::query()->get()->keyBy('codigo');

        $filas = [
            ['setor' => 'RECEP', 'nome' => 'Fila Geral', 'codigo' => 'FG', 'tipo' => 'geral', 'ordem' => 1],
            ['setor' => 'RECEP', 'nome' => 'Fila Prioritária', 'codigo' => 'FP', 'tipo' => 'prioritaria', 'ordem' => 2],
            ['setor' => 'TRIAG', 'nome' => 'Fila Triagem', 'codigo' => 'FT', 'tipo' => 'triagem', 'ordem' => 1],
            ['setor' => 'MEDIC', 'nome' => 'Fila Médica', 'codigo' => 'FM', 'tipo' => 'medica', 'ordem' => 1],
        ];

        foreach ($filas as $filaData) {
            $setor = $setores[$filaData['setor']] ?? null;

            if (! $setor) {
                continue;
            }

            Fila::query()->updateOrCreate(
                ['codigo' => $filaData['codigo']],
                [
                    'setor_id' => $setor->id,
                    'nome' => $filaData['nome'],
                    'tipo' => $filaData['tipo'],
                    'ordem' => $filaData['ordem'],
                    'ativo' => true,
                ],
            );
        }
    }
}
