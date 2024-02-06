<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model {
	
	protected $fillable	= ['speciality'];
		
	public static $rules = [
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
        return Speciality::where('speciality',$speciality)->pluck('id')->all();
    }

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
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
