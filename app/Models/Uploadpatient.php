<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uploadpatient extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'patient_upload';
	protected $fillable=['file_name','org_filename','total_patients','status','error_msg','msg','comments','created_at','updated_at','created_by','updated_by'];
	
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
    
	public function user() {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

}