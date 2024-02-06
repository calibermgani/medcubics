<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Code extends Model {

	use SoftDeletes;
	public $timestamps = false;
	protected $dates = ['deleted_at'];
	protected $fillable = ['codecategory_id','transactioncode_id','description','status','start_date','last_modified_date','created_at','updated_at'];

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

	public function codecategories()
	{
		return $this->belongsTo('App\Models\Codecategory','codecategory_id','id');
	}

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}

	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}

	public static $rules = [
		'codecategory_id' 		=> 'required',
		'description' 			=> 'required',
		'status' 				=> 'required'
	];
	
	public static function messages(){
		return [
			'codecategory_id.required' 		=> Lang::get("practice/practicemaster/codes.validation.category"),
			'transactioncode_id.required' 	=> Lang::get("practice/practicemaster/codes.validation.tr_code"), 
			'transactioncode_id.max' 		=> Lang::get("practice/practicemaster/codes.validation.tr_code_regex"), 
			'transactioncode_id.chk_code_exists' => Lang::get("practice/practicemaster/codes.validation.tr_code_unique"),
			'description.required' 			=> Lang::get("common.validation.description"),
			'status.required' 				=> Lang::get("practice/practicemaster/codes.validation.status"),
		];
	}

	public function favourite()
	{
		return $this->hasOne('App\Models\Favouritecodes', 'code_id','id');
	}
	
	public function rule_engine()
	{	
		return $this->hasMany('App\Models\CodesRuleEngine', 'transactioncode_id','transactioncode_id');
	}
	
}