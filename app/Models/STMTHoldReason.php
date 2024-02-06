<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Patients\Patient as Patient;
use DB;
use Lang;

class STMTHoldReason extends Model {

	use SoftDeletes;
	
	protected $table = "stmt_hold_reason";

	protected $dates = ['deleted_at'];
	
	public $timestamps = false;

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
	
	protected $fillable = [
        'hold_reason', 'status','created_by','updated_by'
    ];	
    
    public static $rules = [
        'hold_reason' => 'required',
	];

	public static function messages(){
		return [
			'hold_reason.required'		=> "Enter the hold reason"
		];
	}

	public static function getStmtHoldReasonList() {
		$stmt_holdreason = STMTHoldReason::where('status', 'Active')->pluck('hold_reason','id')->all();
		return $stmt_holdreason;
	}

	public static function getHoldReasonAppliedCount($hold_reason = 0) {
		$cnt = Patient::where('hold_reason', $hold_reason)->count();
		return $cnt;
	}

}
