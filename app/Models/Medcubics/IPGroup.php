<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IPGroup extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $connection = 'responsive';
	protected $fillable = ['group_name','ip_address'];
	protected $table = 'ip_group';
	
}
