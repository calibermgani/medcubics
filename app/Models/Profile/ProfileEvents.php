<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileEvents extends Model {

protected $table = 'profile_events';
use SoftDeletes;
public static $rules = [			
	'title' => 'required',
	'start_date' => 'required',
	'description' => 'required',		
];	
public static $messages = [		
	'email_id.required' => 'Email address is required!',
	'email_id.email' => 'Email address in not an vaid format!',
	'message.required' => 'Message field is required!',
	'subject.required' => 'Subject field is required!',			
	];
protected $fillable = [	
		'title', 'start_date', 'end_date','event_id', 'start_time', 'end_time', 'description', 'participants','repeated_day','reminder_type','reminder_type_repeat', 'repeated_by', 'reminder_days', 'reminder_date'
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

}
