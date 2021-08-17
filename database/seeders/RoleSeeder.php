<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::updateOrCreate([
            'name' => 'Admin',
            'slug' => 'admin'
        ]);
        $superAdmin = Role::updateOrCreate([
            'name' => 'Super Admin',
            'slug' => 'super-admin'
        ]);
        $userRole = Role::updateOrCreate([
            'name' => 'User',
            'slug' => 'user'
        ]);

        $permission = Permission::updateOrCreate([
            'name' => 'Access Dashboard',
            'slug' => 'access-dashboard',
        ]);

        $admin->givePermissionTo($permission);

        $getAdmin = User::find(1);

        $getAdmin->assignRole($admin);

        $getUser = User::find(2);

        $getAdmin->assignRole($userRole);

    }
}
