<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        User::factory()->create([
            'name' => 'Seller001',
            'email' => 'seller001@example.com',
            'password' => 'password',
        ]);
        User::factory()->create([
            'name' => 'Selelr002',
            'email' => 'seller002@example.com',
            'password' => 'password',
        ]);
        User::factory()->create([
            'name' => 'Selelr003',
            'email' => 'seller003@example.com',
            'password' => 'password',
        ]);
    }
}
