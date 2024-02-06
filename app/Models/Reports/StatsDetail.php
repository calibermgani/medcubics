<?php namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
class StatsDetail extends Model {

use SoftDeletes;

protected $dates = ['deleted_at'];

protected $table = "stats_detail";

protected $fillable=[
           'user_id','module_name','position','stats_id','created_at','updated_at'];


public function statslist(){
		return $this->belongsTo('App\Models\Reports\StatsList','stats_id','id');
	}

	public static function boot() {
       parent::boot();
       // create a event to happen on saving
       static::saving(function($table)  {
            foreach ($table->toArray() as $name => $value) {
                if (empty($value) && $name <> 'deleted_at') {
                    $table->{$name} = '';
                }
            }
            return true;
       });
    }
		

}
