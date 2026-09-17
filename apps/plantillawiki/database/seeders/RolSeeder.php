<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            [
                'id' => 1,
                'name' => 'admin',
                'guard_name' => 'api',
            ],
            [
                'id' => 2,
                'name' => 'user',
                'guard_name' => 'api',
            ],
            [
                'id' => 3,
                'name' => 'viewer',
                'guard_name' => 'api',
            ],
        ];

        foreach ($roles as $rol) {
            $rol = Role::create($rol);
            if ($rol->name == 'admin') {
                $rol->syncPermissions([
                    'create_user',
                    'edit_user',
                    'delete_user',
                    'view_user',
                    'create_rol',
                    'edit_rol',
                    'delete_rol',
                    'view_rol',
                    'create_permission',
                    'edit_permission',
                    'delete_permission',
                    'view_permission',
                ]);
            }
            if ($rol->name == 'user') {
                $rol->syncPermissions([
                    'create_user',
                    'edit_user',
                    'view_user',
                ]);
            }
            if ($rol->name == 'viewer') {
                $rol->syncPermissions([
                    'view_user',
                ]);
            }
        }

    }
}
