<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaries extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	public $timestamps = false;
	protected $table = "questionnaries";
	protected $fillable=['facility_id','provider_id','template_id','created_by','updated_by','created_at','updated_at'];
	
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

	public function questionnaries_option()
    {
        return $this->belongsTo('App\Models\QuestionnariesTemplate','template_id','template_id');
    }

	public function provider(){
		return $this->belongsTo('App\Models\Provider', 'provider_id', 'id');
	}

	public function facility(){
		return $this->belongsTo('App\Models\Facility', 'facility_id', 'id');
	}

	public function creator(){
		return $this->belongsTo('App\User', 'created_by', 'id');
	}

	public function modifier(){
		return $this->belongsTo('App\User', 'updated_by', 'id');
	}
	
	public static $rules = [
		'provider_id' 		=> 'required',
		'facility_id' 		=> 'required'
	];
		
	public static $messages = [
		'provider_id.required' 	=> 'Choose provider',
		'facility_id.required' 	=> 'Choose facility',
		'template_id.required' 	=> 'Choose questionnaires template',
		'template_id.uniquequestionaries' 	=> 'Already allocated the Questionnaires template to the same facility and provider'
	];
}