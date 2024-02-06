<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserLoginSeeder extends Seeder
{

public function run()
{
    DB::table('users_login')->delete();
    \App\UserLogin::create(array(
        'name'     => 'Sukumar',
        'username' => 'sukumar',
        'email'    => 'sukumar@gmail.com',
        'password' => Hash::make('awesome')
    ));
}

}
