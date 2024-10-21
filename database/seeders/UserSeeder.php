<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

       User::create([
        'name' => 'namaManager',
        'email' => 'manager@gmail.com',
        'password' => Hash::make('P@ssw0rd'),
        'role_id' => 4
       ]);

       User::create([
        'name' => 'namaWaitress',
        'email' => 'waitress@gmail.com',
        'password' => Hash::make('P@ssw0rd'),
        'role_id' => 1
       ]);
    }
}
