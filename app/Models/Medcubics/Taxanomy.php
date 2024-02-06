<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Lang;

class Taxanomy extends Model 
{
	protected $fillable		= ['description','code','speciality_id'];
	public static $rules	= [
		'description'     	=> 'required|not_in:0',
		'speciality_id'  	=> 'required|numeric|not_in:0',
	];
	public static function messages() { 
		return [
			'description.required'   	=> 	Lang::get("admin/taxanomy.validation.description"),
			'code.required'             => 	Lang::get("admin/taxanomy.validation.code"),
			'code.unique'	            => 	Lang::get("admin/taxanomy.validation.unique"),
			'code.alpha_num'            => 	Lang::get("common.validation.alphanumeric"),
			'speciality_id.required'    =>  Lang::get("admin/taxanomy.validation.speciality"),
		];
	}
	public function speciality()
	{
		return $this->belongsTo('App\Models\Medcubics\Speciality', 'speciality_id','id');
	}
	public function providers()
	{
		return $this->belongsToMany('App\Models\Medcubics\Provider');
	}
	public static function getTaxanomyId($code)
	{
		return Taxanomy::where('code',$code)->pluck('id')->first();
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

