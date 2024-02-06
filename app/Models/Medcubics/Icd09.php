<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Auth;
class Icd09 extends Model {

	protected $table = 'icd_09';


protected $fillable=[
           'code','change_indicator','code_status','short_desc',
           'medium_desc','long_desc'
        ];
		
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
						'code' => 'required|max:6',
						'change_indicator' => 'nullable|max:7',
						'code_status' => 'required|max:7',
						'short_desc' => 'required|max:28',
						'medium_desc' => 'required|max:35',
						'long_desc' => 'required|max:163',
						];
public static $messages = [

						'code.required' => 'Enter code!',
						'code.max' => 'Enter valid code!',
						'change_indicator.max' => 'Enter valid change indicator!',
						'code_status.required' => 'Enter code status!',
						'code_status.max' => 'Enter valid code status!',
						'long_desc.required' => 'Enter long description!',
						'long_desc.max' => 'Short description should not exceed 48 characters!',
						'short_desc.required' => 'Enter short description!',
						'short_desc.max' => 'Short description should not exceed 48 characters!',
						'medium_desc.required' => 'Enter medium description!',
						'medium_desc.max' => 'Medium description should not exceed 60 characters!',
						
						];
}
