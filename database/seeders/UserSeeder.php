<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }
        foreach (['anna' => 'Анна Морозова', 'mikhail' => 'Михаил Волков', 'daria' => 'Дарья Соколова'] as $handle => $name) {
            User::firstOrCreate(['email' => $handle.'@example.com'], ['name' => $name, 'password' => 'Travel2026!']);
        }
    }
}
