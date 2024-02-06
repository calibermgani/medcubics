<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Lang;
class Code extends Model
{
	public $timestamps = false;
	
	
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
		return $this->belongsTo('App\Models\Medcubics\Codecategory','codecategory_id','id');
	}
	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	
	protected $fillable=[
						'codecategory_id','transactioncode_id','description','status','start_date','last_modified_date','created_at','updated_at'
						];
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
	public static $messages = [
							'codecategory_id.required' 		=> 'Select your code category!',
							'transactioncode_id.required' 	=> 'Enter your 3 digit aplha numeric transaction code!',
							'transactioncode_id.chk_code_exists' => 'Transaction code must be unique by code category',
							'description.required' 			=> 'Enter your description!',
							'status.required' 				=> 'Select status!'
							];
							
	public function rule_engine()
	{	
		return $this->hasMany('App\Models\Medcubics\CodesRuleEngine', 'transactioncode_id','transactioncode_id');
	}
}
	
