<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Lang;
class Pos extends Model 
{
	 protected $table = 'pos';
	 public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	protected $fillable = array('code','pos');
	
	public static $rules	= [
		'pos'=> 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
	];

	public static function messages(){
		return [
			'code.required'	=> 	Lang::get("admin/pos.validation.code"),
			'code.numeric'	=>  Lang::get("common.validation.numeric"),
			'code.unique'	=> 	Lang::get("admin/pos.validation.code_unique"),
			'pos.required'	=> 	Lang::get("admin/pos.validation.pos"),
			'pos.regex'		=> 	Lang::get("admin/pos.validation.pos_regex")
		];
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