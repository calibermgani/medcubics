<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payments\ClaimCPTOthersAdjustmentInfoV1;
use DB;
use Lang;

class AdjustmentReason extends Model {

	use SoftDeletes;
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
        'adjustment_type', 'adjustment_reason', 'status','created_by','updated_by','adjustment_shortname','add_type'
    ];	
    
    public static $rules = [
        'adjustment_type' => 'required',
        'adjustment_reason' => 'required',
	];

	public static function messages(){
		return [
			'adjustment_reason.required'=> Lang::get("practice/practicemaster/adjustmentreason.validation.adjustmentreason"),
			'adjustment_type.required' 	=> Lang::get("practice/practicemaster/adjustmentreason.validation.adjustmenttype")
		];
	}
    
    public static function getAdjustmentReason($type = '',$cpt = '',$cptId = ''){
		if(empty($cpt) && empty($cptId)){
			$adjustment_reason = AdjustmentReason::where('status', 'Active')->where('add_type','User')->where('adjustment_type', $type)->pluck('adjustment_shortname', 'id')->all();
		}else{
			$CptDeniedCode = ClaimCPTOthersAdjustmentInfoV1::where('claim_cpt_id',$cptId)->whereNull('deleted_at')->where('adjustment_id','!=','0')->get();
			$adjustment_reason = AdjustmentReason::where('status', 'Active')->where('add_type','User')->where('adjustment_type', $type)->pluck('adjustment_shortname', 'id')->all();
			foreach($CptDeniedCode as $list){
				$AdjShortName = AdjustmentReason::where('id',$list->adjustment_id)->value('adjustment_shortname');
				$adjustment_reason[$list->adjustment_id] = $AdjShortName;
			}
		}
    	return $adjustment_reason;
    }
	
	public static function getAdjustmentReasonName($id) {	
		$adjustment_reason = AdjustmentReason::where('id',$id)->where('status', 'Active')->value('adjustment_reason');
		return $adjustment_reason;
	}
}