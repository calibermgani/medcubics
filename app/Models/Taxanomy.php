<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taxanomy extends Model {
    use SoftDeletes;
    protected $dates = ['deleted_at'];
	protected $fillable		= ['specialization','code','speciality_id'];
	
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
		  'specialization'     => 'required|not_in:0',
          'code'               => 'required|alpha_num|not_in:0',
          'speciality_id'      => 'required|numeric|not_in:0',
	];
	
	public static $messages = [
			'specialization.required'   => 'Specialization field is mandatory!',
            'code.required'             => 'Code field is mandatory',
            'code.alpha_num'            => 'Code field must be alpha numeric',
            'speciality_id.required'    =>  'Speciality id field is mandatory',
            'speciality_id.numeric'     =>  'Only numeric values allowed for Speciality ID field'
	];
    
    public function speciality()
    {
        return $this->hasOne('App\Models\Speciality', 'id','speciality_id');
    }
    
    public function providers()
    {
        return $this->belongsToMany('App\Models\Provider');
    }
    
    public static function getTaxanomyId($code)
    {
        return Taxanomy::where('code',$code)->pluck('id')->first();
    }
}