<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
class Pos extends Model {

	use SoftDeletes;

    protected $dates = ['deleted_at'];
	protected $fillable = array('code','pos');
	
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
          'code'	=> 'required|numeric|not_in:0',
          'pos'		=> 'required|alpha_num|not_in:0',
	];
	public static function messages(){
		return [
			'code.required'	=> Lang::get("common.validation.code"),
			'code.numeric'	=> Lang::get("common.validation.numeric"),
			'pos.required' 	=> Lang::get("common.validation.pos"),
			'pos.alpha_num' => Lang::get("common.validation.alphanumeric"),
		];
	}
	
	public function facility()
	{
		return $this->belongsTo('App\Models\Facility','id','pos_id');
	}

}