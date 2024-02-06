<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Staticpage extends Model 
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	public $timestamps 	= false;

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
	
	public function updateuser()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	
	protected $fillable = [
		'type','title','slug','content','status','created_by','updated_by','created_at','updated_at'
	];

	public static $rules = [	'title' => 'required',
		'content' => 'required',
		'status' => 'required',
	];

	public static function messages(){
		return [
			'title.required' 	=> Lang::get("admin/staticpage.validation.type_required"),
			'content.required' 	=> Lang::get("common.validation.content"),
			'type.required' 	=> Lang::get("admin/staticpage.validation.type_required"),
			//'type.unique' 		=> Lang::get("admin/staticpage.validation.type_unique"),
			'status.required' 	=> Lang::get("admin/staticpage.validation.status"),
		];
	}
}