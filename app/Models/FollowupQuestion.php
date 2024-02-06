<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowupQuestion extends Model {
	
	use SoftDeletes;
	
	protected $table = 'followup_question';
	
	protected $dates = ['deleted_at'];
	
	public $timestamps = true;

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

	protected $fillable=['question', 'category_id', 'field_type','field_validation', 'date_type', 'status', 'created_at', 'updated_at','question_label','hint','user_id'];
	
	public function category(){
		return $this->belongsTo('App\Models\FollowupCategory','category_id','id');
	}
	
	public function user(){
		return $this->belongsTo('App\Models\Medcubics\Users','user_id','id');
	}
}