<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user=User::updateOrCreate(
            ['email'=>'admin@example.com'],
            [
                'name'=>'Super Admin',
                'username'=>'admin',
                'phone'=>null,
                'password'=>'ChangeMe123!',
                'status'=>'active',
            ]
        );
        $user->syncRoles(['Super Admin']);
    }
}
