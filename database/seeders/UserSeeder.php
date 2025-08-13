<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder{
    public function run(): void{
        $adminRole = Role::where('name', 'administrador')->first();
        $personalRole = Role::where('name', operator: 'personal')->first();
        $vendedorRole = Role::where('name', operator: 'vendedor')->first();
        $permissions = Permission::all();
        if ($adminRole) {
            $adminRole->syncPermissions($permissions);
        }

        $admin_1 = User::create([
            'name' => 'Jefferson Grabiel',
            'dni' => '76393671',
            'apellidos' => 'Covenas Roman',
            'nacimiento' => '2003-03-11',
            'email' => 'jefersoncovenas7@gmail.com',
            'username' => 'JCOVENASRO11',
            'password' => Hash::make('12345678'),
            'status' => true,
            'restablecimiento' => 0,
        ]);

        $admin_2 = User::create([
            'name' => 'Pablo Isaac',
            'dni' => '07777777',
            'apellidos' => 'Lupu Garcia',
            'nacimiento' => '2003-03-11',
            'email' => 'pablolupu2020@gmail.com',
            'username' => 'pablolupu',
            'password' => Hash::make('Pl221103%'),
            'status' => true,
            'restablecimiento' => 0,
        ]);

        $admin_3 = User::create([
            'name' => 'Karin Hair',
            'dni' => '77777777',
            'apellidos' => 'Chozo',
            'nacimiento' => '2003-03-11',
            'email' => 'kayisanta5@gmail.com',
            'username' => 'kchozo27',
            'password' => Hash::make('12345678'),
            'status' => true,
            'restablecimiento' => 0,
        ]);

        $admin_1->assignRole($adminRole);
        $admin_2->assignRole($adminRole);
        $admin_3->assignRole($adminRole);
    }
}
