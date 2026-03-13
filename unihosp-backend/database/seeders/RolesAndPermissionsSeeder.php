<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'users.manage',
            'pacientes.view',
            'pacientes.manage',
            'recepcao.view',
            'recepcao.manage',
            'filas.view',
            'filas.manage',
            'senhas.view',
            'senhas.manage',
            'chamadas.view',
            'chamadas.manage',
            'paineis.view',
            'paineis.manage',
            'triagem.view',
            'triagem.manage',
            'prontuarios.view',
            'atendimentos.view',
            'atendimentos.manage',
            'relatorios.view',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'administrador' => $permissions,
            'recepcao' => [
                'dashboard.view',
                'pacientes.view',
                'pacientes.manage',
                'recepcao.view',
                'recepcao.manage',
                'filas.view',
                'filas.manage',
                'senhas.view',
                'senhas.manage',
                'chamadas.view',
                'chamadas.manage',
                'paineis.view',
                'relatorios.view',
            ],
            'enfermagem' => [
                'dashboard.view',
                'pacientes.view',
                'filas.view',
                'senhas.view',
                'senhas.manage',
                'triagem.view',
                'triagem.manage',
                'prontuarios.view',
                'paineis.view',
            ],
            'medico' => [
                'dashboard.view',
                'pacientes.view',
                'filas.view',
                'senhas.view',
                'atendimentos.view',
                'atendimentos.manage',
                'prontuarios.view',
                'chamadas.view',
                'chamadas.manage',
                'relatorios.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
