<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Lang;

class ProcedureCategory extends Model 
{
	use SoftDeletes;
	protected $table = "procedure_categories";

	protected $dates 	= ['deleted_at'];
	protected $fillable	= ['procedure_category','status','created_by'];

  public function creator(){
    return $this->belongsTo('App\User', 'created_by', 'id');
  }

  public function modifier(){
    return $this->belongsTo('App\User', 'updated_by', 'id');
  }
  
	public static $rules = ['procedure_category' => 'required'];

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
  public static function messages(){
  return [
    'status.required'   => Lang::get("practice/practicemaster/holdoption.validation.status"),
  ];
}
}