<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Template extends Model 
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
    
	public function templatetype()
	{
		return $this->belongsTo('App\Models\Templatetype', 'template_type_id','id');
	}
	
	public function creator()
	{
		return $this->belongsTo('App\User', 'created_by', 'id');
	}
	
	public function modifier()
	{
		return $this->belongsTo('App\User', 'updated_by', 'id');
	}

	protected $fillable = [ 'name', 'content', 'template_type_id','status', 'created_by','updated_by' ];	
	
	public static $rules = [ 
				'template_type_id' 	=> 'required|not_in:0',
				'status' 			=> 'required',
			];
			
	public static function messages(){
		return [
			'name.required' => Lang::get("practice/practicemaster/template.validation.name"),
			'name.unique' 	=> Lang::get("practice/practicemaster/template.validation.unique_name"),
			'content.required' 			=> Lang::get("common.validation.content"),
			'template_type_id.required' => Lang::get("practice/practicemaster/template.validation.category_id"),
			'status.required' 			=> Lang::get("practice/practicemaster/template.validation.status"),
		];
	}	
}