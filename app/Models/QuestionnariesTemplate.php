<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
class QuestionnariesTemplate extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	public $timestamps = false;
	protected $table = "questionnaries_template";
	protected $fillable=['template_id','title','question','answer_type','question_order','created_by','updated_by','created_at','updated_at'];
	
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
        return $this->hasMany('App\Models\QuestionnariesOption','questionnaries_template_id','id');
    }	
    
    public function questionnaries(){
		return $this->hasMany('App\Models\Questionnaries', 'id', 'template_id');
	} 

	public function questionnaries_app(){
		return $this->hasMany('App\Models\Questionnaries', 'template_id', 'template_id');
	}

	public function creator(){
		return $this->belongsTo('App\User', 'created_by', 'id');
	}

	public function modifier(){
		return $this->belongsTo('App\User', 'updated_by', 'id');
	}
	
	public static function messages(){
		return [
			'title.required' => Lang::get("common.validation.title"),
			'title.unique' => Lang::get("practice/practicemaster/questionnaries.validation.title_unique"),
			'question.required' => Lang::get("practice/practicemaster/questionnaries.validation.question"),
		];
	}
	
	public static function getLastid()
    {
		$get_lastid = QuestionnariesTemplate::orderBy('template_id','DESC')->pluck('template_id')->first();
		$get_lastid = $get_lastid+1;
		return $get_lastid;
    }
}