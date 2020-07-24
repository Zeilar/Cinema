<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'Admin Admin',
            'password' => Hash::make('cinema-admin-123'),
            'color'    => 'rgb(255, 0, 0)',
            'role'     => 'admin',
        ]);
    }
}