<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        Permission::create(['name' => 'shiva233333']);

        // Create roles
        $userRole = Role::create(['name' => 'user']);

        // Assign permissions to roles
        $userRole->givePermissionTo('shiva233333');
    }
}
