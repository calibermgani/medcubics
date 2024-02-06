<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

use Session;
class ReportExport extends Model {
	//use SoftDeletes;
	
	protected $connection = 'responsive'; // connecting betacore table
	
	protected $fillable = ['report_name','report_url','report_file_name','report_controller_name','report_controller_func','status','created_by','report_count','practice_id','parameter','deleted_at'];
	
	protected $table = 'report_export_task';
	
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
    
	public function createdUser(){
		return $this->belongsTo('App\User','created_by','id');
	}

}
