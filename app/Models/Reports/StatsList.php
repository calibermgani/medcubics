<?php namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class StatsList extends Model {

	protected $table = 'stats_list';
	public function stats_detail(){
		return $this->belongsTo('App\Models\Reports\StatsDetail','stats_id','id');
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
