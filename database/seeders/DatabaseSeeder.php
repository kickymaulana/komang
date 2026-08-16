<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@komang.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ],
        );

        $admin->assignRole('admin');
    }
}
