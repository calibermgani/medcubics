<?php

namespace App\Models;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Database\Eloquent\Model;
use Lang;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insurancetype extends Model
{
	use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = "insurancetypes";
  	protected $fillable = [ 'code', 'type_name','cms_type','created_by','updated_by'];

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

	public function insurance()
	{
		return $this->belongsTo('App\Models\Insurance','id','insurancetype_id');
	}
		
	public static $rules = [];
	
	public static function messages()
	{
		return [
			'type_name.required'	=> Lang::get("admin/insurancetype.validation.type_name"),
			'type_name.unique'		=> Lang::get("admin/insurancetype.validation.type_name_unique"),
			'code.required'    		=> Lang::get("admin/insurancetype.validation.code"),
			'code.unique'			=> Lang::get("admin/insurancetype.validation.code_unique"),
		];
	}
	
	public function getInsurancetype()
	{
		$insurance_type = Insurancetype::pluck('id',"type_name")->all();
		//$insurance_type = Insurancetype::has("insurance")->pluck('id',"type_name")->all();
		$insurance	 	= array_flip(array_map(array($this,'encodeId'),$insurance_type));
		return $insurance;
	}
	
	function encodeId($num)
	{
	  	return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}
}