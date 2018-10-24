<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        	'name' => 'admin',
        	'telefone' => '988962374',
        	'cpf' => '87090876565',
        	'rg' => '1623459879',
        	'email' => 'admin@gmail.com',
        	'password' => Hash::make('123123')
        ]);

        
    }
}
