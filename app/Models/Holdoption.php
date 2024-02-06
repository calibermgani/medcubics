<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
use App\Models\Payments\ClaimInfoV1;

class Holdoption extends Model {

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

	protected $fillable=['option','status','hold_reason','created_by','updated_by'];
		
	public static $rules = [
			'status' 	=> 'required'
	];

	public static function messages(){
		return [
			'option.required' 	=> Lang::get("practice/practicemaster/holdoption.validation.option"),
			'option.unique' 	=> Lang::get("practice/practicemaster/holdoption.validation.option_unique"),
			'status.required' 	=> Lang::get("practice/practicemaster/holdoption.validation.status"),
		];
	}

	public static function getClaimHoldReasonAppliedCount($hold_reason_id = 0) {
		$cnt = ClaimInfoV1::where('hold_reason_id', $hold_reason_id)->count();
		return $cnt;
	}
}