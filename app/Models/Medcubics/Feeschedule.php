<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;

class Feeschedule extends Model 
{
	protected $fillable=array(
							  'file_name', 'fees_type','template','choose_year','conversion_factor','percentage'
							);
	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
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

	public static $rules = [
							'file_name' 		=> 'required|unique:feeschedules',
							'fees_type' 		=> 'required',
							'template' 			=> 'required',
							'choose_year' 		=> 'required|digits:4',
							'conversion_factor' => 'required',
							'percentage' 		=> 'required',
							];
	public static $messages = [
							'file_name.required' 		=> 'Please, Enter your File Name!',
							'file_name.unique' 			=> 'File name must be unique!',
							'fees_type.required' 		=> 'Please, Select your Fees Type!',
							'template.required' 		=> 'Please, Select your Template!',
							'choose_year.required' 		=> 'Please, Choose year!',
							'choose_year.digits' 		=> 'Year should be 4 digits',
							'conversion_factor.required'=> 'Please, Conversion Factor!',
							'percentage.required' 		=> 'Please, Enter your Percentage!',
							];
}
