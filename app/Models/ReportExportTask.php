<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Lang;
use Config;
use Auth;

class ReportExportTask extends Model {
	
	use SoftDeletes;
	
	protected $fillable = ['report_name','report_url','report_file_name','report_controller_name','report_controller_func','status','created_by','created_at','updated_at','deleted_at'];
	
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
}