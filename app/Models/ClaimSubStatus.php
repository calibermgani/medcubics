<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
use App\Models\Payments\ClaimInfoV1;

class ClaimSubStatus extends Model
{
    use SoftDeletes;
	public $timestamps = false;
	protected $dates = ['deleted_at'];
	protected $table = "claim_sub_status";

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

	protected $fillable=['sub_status_desc','status','hold_reason','created_by','updated_by'];
		
	public static $rules = [
			'sub_status_desc' 	=> 'required',
			'status' 			=> 'required'
	];

	public static function messages(){
		return [
			'sub_status_desc.required' 	=> Lang::get("practice/practicemaster/holdoption.validation.option"),
			'sub_status_desc.unique' 	=> "Sub Status Already Exists",
			'status.required' 			=> Lang::get("practice/practicemaster/holdoption.validation.status"),
		];
	}

	public static function getClaimSubStatusList($filter=0) {
		if(!$filter)
			$substatus = ClaimSubStatus::where('status', 'Active')->pluck('sub_status_desc','id')->all();
		else 
			$substatus = ClaimSubStatus::pluck('sub_status_desc','id')->all()+['0'=>'--Nil--'];
		return $substatus;
	}

	public static function getClaimSubStatusAppliedCount($sub_status_id = 0) {
		$cnt = ClaimInfoV1::where('sub_status_id', $sub_status_id)->count();
		return $cnt;
	}
}
