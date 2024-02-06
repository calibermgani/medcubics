<?php
namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Insurancetype extends Model
{
	use SoftDeletes;
	
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
	
	
	/*** User Module Jion ***/
	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	
	/*** Variable declaration ***/
	protected $fillable = ['type_name','cms_type','code','created_by','updated_by'];
	
	/*** Error message Contant ***/
	public static function messages()
	{
		return [
			'type_name.required'	=> Lang::get("admin/insurancetype.validation.type_name"),
			'type_name.unique'		=> Lang::get("admin/insurancetype.validation.type_name_unique"),
			'code.required'	=> Lang::get("admin/insurancetype.validation.code"),
			'code.unique'		=> Lang::get("admin/insurancetype.validation.code_unique"),
		];
	}						
}
