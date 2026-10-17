<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Employee::factory(100)->create();
        User::create([
            'name' => 'Admin système',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
