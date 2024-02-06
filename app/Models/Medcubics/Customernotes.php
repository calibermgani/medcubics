<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customernotes extends Model 
{

	use SoftDeletes;
    protected $dates 	= ['deleted_at'];
	protected $fillable = ['cust_id','title', 'content','created_by'];
	
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

	public static $rules = [
		'title' 	=> 'required',
		'content' 	=> 'required'
	];
	
	public static function messages(){
		return [
			'title.required' 	=> trans("common.validation.title"),
			'content.required' 	=> trans("common.validation.content"),
		];
	}
	
}
