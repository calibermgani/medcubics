<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserSeeder extends Seeder
{

	public function run()
	{
	   DB::table('users')->insert(array(            
		   array(
	          'role_id' => 1,
	          'customer_id' => 1,
	          'name' => 'medcubics',
	          'email' => 'medcubics@gmail.com',
	          'password' =>  Hash::make('medcubics'),
	          'user_type' => 'medcubics'
          )
		));
	}

}
