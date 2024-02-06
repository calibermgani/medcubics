<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnariesAnswer extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "questionnaries_answer";
	protected $fillable=['patient_id','template_id','questionnaries_template_id','answer','created_by','updated_by'];
	
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
    
	public function questionnaries_template()
    {
        return $this->belongsTo('App\Models\QuestionnariesTemplate','questionnaries_template_id','id');
    }

	public function usercreated()	
	{
		return $this->belongsTo('App\Models\Medcubics\Users','created_by','id');
	}	
}