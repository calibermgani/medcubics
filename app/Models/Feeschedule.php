<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Feeschedule extends Model {

	use SoftDeletes;

	protected $dates = ['deleted_at'];

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

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}

	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	
	public function multiCptInfo(){
		return $this->belongsTo('App\Models\MultiFeeschedule','cpt_id','cpt_id')->select('id','cpt_hcpcs');
	}

	protected $fillable=array(
		'file_name', 'fees_type','template','choose_year','conversion_factor','percentage','saved_file_name'
	);

	public static $rules = [
		'file_name' 	=> 'required',
		'conversion_factor' => 'required',
		'percentage' 	=> 'required'
		/*'fees_type' 	=> 'required',
		'template' 		=> 'required',
		'choose_year' 	=> 'required|digits:4',
		'fees_type.required' => 'Please, Select your Fees Type!',
		'template.required' => 'Please, Select your Template!',
		'choose_year.required' => 'Please, Choose year!',
		'choose_year.digits' => 'Year should be 4 digits',*/
		
	];

	public static function messages(){
		return [
			'file_name.required' 		=> Lang::get("practice/practicemaster/feeschedule.validation.file"),
			'conversion_factor.required'=> Lang::get("practice/practicemaster/feeschedule.validation.conversion_factor"),
			'percentage.required' 		=> Lang::get("practice/practicemaster/feeschedule.validation.percentage"),
		];
	}
}