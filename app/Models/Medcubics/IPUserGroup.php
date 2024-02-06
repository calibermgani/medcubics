<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IPUserGroup extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $connection = 'responsive';
	protected $fillable = ['group_name','user_id','status'];
	protected $table = 'ip_user_group';
	
}
