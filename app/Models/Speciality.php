<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Speciality extends Model {

	use SoftDeletes;

	protected $dates = ['deleted_at'];
	
	protected $fillable		= ['speciality'];
	
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
    	
	public static $rules	= [
		  'speciality' => 'required|not_in:0',
	];
	
	public static $messages = [
			'speciality.required' => 'Speciality field is mandatory!',
	];

	public function providers()
    {
        return $this->belongsToMany('App\Provider');
    }
    
    public static function getSpecialityId($speciality)
    {
        return Speciality::where('speciality',$speciality)->pluck('id')->first();
    }
	
	public function facility()
    {
        return $this->belongsTo('App\Models\Facility','id','speciality_id');
    }
}