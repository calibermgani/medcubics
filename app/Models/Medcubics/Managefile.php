<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Managefile extends Model 
{
	protected $dates 		= ['deleted_at'];
	protected $table 		= 'manage_files';
	protected $connection 	= 'responsive';	
	protected $fillable 	= ['source','module','record_id','filename','filepath','mode','created_by','updated_by'];
	
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
