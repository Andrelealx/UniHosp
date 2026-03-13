<?php

namespace Database\Seeders;

use App\Models\Convenio;
use Illuminate\Database\Seeder;

class ConveniosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $convenios = [
            ['nome' => 'Particular', 'codigo' => 'PART'],
            ['nome' => 'SUS', 'codigo' => 'SUS'],
            ['nome' => 'UniSaúde', 'codigo' => 'UNIS'],
            ['nome' => 'Vida Plus', 'codigo' => 'VIDA'],
        ];

        foreach ($convenios as $convenio) {
            Convenio::query()->updateOrCreate(
                ['codigo' => $convenio['codigo']],
                [
                    'nome' => $convenio['nome'],
                    'ativo' => true,
                ],
            );
        }
    }
}
