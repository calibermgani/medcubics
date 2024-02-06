<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Maintenance_sql extends Model {

	protected $fillable = ['status','query','applied_date','success_practice','failure_practice','developer_name','developed_date','user'];

}
