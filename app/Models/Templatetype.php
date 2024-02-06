<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Templatetype extends Model 
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

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
    
	public function template()
	{
		return $this->hasMany('App\Models\Template','template_type_id','id')->where('template_type_id', '!=', '0');
	}

	public function creator()
	{
		return $this->belongsTo('App\User', 'created_by', 'id');
	}

	protected $fillable=[ 'templatetypes', 'created_by','updated_by' ];
	
	//Rules are defined in Api controller file //
	public static function messages(){
		return [
			'templatetypes.category_alert' => Lang::get("practice/practicemaster/template.validation.category_alert"),
			'templatetypes.unique' 	 => Lang::get("practice/practicemaster/template.validation.unique_category"),
			'templatetypes.required' => Lang::get("practice/practicemaster/template.validation.category")
		];
	}	
}