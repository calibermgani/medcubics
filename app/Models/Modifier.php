<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Modifierstype as Modifierstype;
use Lang;
class Modifier extends Model {

	use SoftDeletes;
	protected $dates 	= ['deleted_at'];
	public $timestamps 	= false;

	public static function boot() {
		parent::boot();
		static::saving(function($table) {
			$requests = \Request::all();
			$table->anesthesia_base_unit = !empty($requests['anesthesia_base_unit'])?$requests['anesthesia_base_unit']:'';
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

	public function modifierstype()
	{
		return $this->belongsTo('App\Models\Modifierstype','modifiers_type_id','id');
	}

	protected $fillable	=[
		'code','modifiers_type_id','name','description','anesthesia_base_unit','status','created_by',
		'updated_by',
		'created_at',
		'updated_at',
		'deleted_at'
	];

	public static $rules = [
		'modifiers_type_id' => 'required|not_in:0',
		'name' 				=> 'required',
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