<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class ReasonForVisit extends Model {

	use SoftDeletes;
	public $timestamps = false;
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
    
	public function creator(){
		return $this->belongsTo('App\User', 'created_by', 'id');
	}
	public function modifier(){
		return $this->belongsTo('App\User', 'updated_by', 'id');
	}
	protected $fillable=['reason','status','created_by','updated_by'];
	
	public static $rules = [
			'status' 	=> 'required'
	];
		
	public static function messages(){
		return [
			'reason.required' 	=> Lang::get("practice/practicemaster/reason.validation.reason"),
			'reason.unique' 	=> Lang::get("practice/practicemaster/reason.validation.reason_unique"),
			'status.required' 	=> Lang::get("practice/practicemaster/reason.validation.status"),
		];
	}
	
	public static function reasonForVists(){
		return ReasonForVisit::where('status','Active')->pluck('reason','id')->all();
	}
}