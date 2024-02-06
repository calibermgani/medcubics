<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $connection = 'responsive';    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public static function getusername($group_users){
        $users='';
        $user_list = explode(",",$group_users);
        
        $user_table = User::whereIn('id', $user_list)->select('name')->get();
        $users="";
        
        foreach($user_table as $user_table){
            if($users !=""){
                $users.=',';
            }
            $users.=$user_table->name;
        }
        return $users;
    }

	public function isProvider()  {        
        // Check for an provider id and user type column in your users table
        return ($this->practice_user_type == 'provider' && $this->provider_access_id != 0) ? true : false; 
    }
}