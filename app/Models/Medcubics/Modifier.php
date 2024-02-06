<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use App\Models\Medcubics\Modifierstype as Modifierstype; 
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Modifier extends Model 
{
	
	use SoftDeletes;
	protected $dates 	= ['deleted_at'];
	public $guarded = array();
	
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
	
	public function modifierstype()
	{
		return $this->belongsTo('App\Models\Medcubics\Modifierstype','modifiers_type_id','id');
	}
	
	protected $fillable=['code', 'modifiers_type_id','name', 'description','anesthesia_base_unit','status'];
	
	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	
	public static $rules = [
		'modifiers_type_id' => 'required|not_in:0',
		'name' 				=> 'required',
		//'code' 				=> 'regex:/^[A-Za-z0-9]+$/i|not_in:0',
		'description' 		=> 'required',
		'status' 			=> 'required',
	];
	public static function messages(){
		return [
			'modifiers_type_id.required'=> Lang::get("practice/practicemaster/modifier.validation.modifiers_category"),
			'name.required' 			=> Lang::get("practice/practicemaster/modifier.validation.name"),
			'code.regex' 				=> Lang::get("practice/practicemaster/modifier.validation.code_regex"),
			'code.not_in' 				=> Lang::get("practice/practicemaster/modifier.validation.code_regex"),
			'description.required' 		=> Lang::get("common.validation.description"),
			'status.required' 			=> Lang::get("practice/practicemaster/modifier.validation.status"),
			'code.required' 			=> Lang::get("practice/practicemaster/modifier.validation.code"),
			'code.unique' 				=> Lang::get("practice/practicemaster/modifier.validation.code_unique"),
		];
	}
}
