<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PlantDataSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Chris',
            'email' => 'chrisganzert@pflanzen.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@pflanzen.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('user');
    }
}
