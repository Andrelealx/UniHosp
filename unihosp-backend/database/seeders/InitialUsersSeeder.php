<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Administrador UniHosp',
                'email' => 'admin@unihosp.local',
                'cpf' => '11111111111',
                'phone' => '(11) 99999-0001',
                'role' => 'administrador',
            ],
            [
                'name' => 'Recepção UniHosp',
                'email' => 'recepcao@unihosp.local',
                'cpf' => '11111111112',
                'phone' => '(11) 99999-0002',
                'role' => 'recepcao',
            ],
            [
                'name' => 'Enfermagem UniHosp',
                'email' => 'enfermagem@unihosp.local',
                'cpf' => '11111111113',
                'phone' => '(11) 99999-0003',
                'role' => 'enfermagem',
            ],
            [
                'name' => 'Médico UniHosp',
                'email' => 'medico@unihosp.local',
                'cpf' => '11111111114',
                'phone' => '(11) 99999-0004',
                'role' => 'medico',
            ],
        ];

        foreach ($usuarios as $dados) {
            $role = $dados['role'];
            unset($dados['role']);

            /** @var User $user */
            $user = User::query()->updateOrCreate(
                ['email' => $dados['email']],
                array_merge($dados, [
                    'password' => Hash::make('UniHosp@123'),
                    'is_active' => true,
                ]),
            );

            $user->syncRoles([$role]);
        }
    }
}
