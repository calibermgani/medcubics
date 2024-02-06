<?php

namespace App\Models\patients;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
use App\Http\Helpers\Helpers as Helpers;

class ProblemList extends Model {

    protected $table = "problem_lists";

    public function patient() {
        return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id');
    }
	
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

    public function claim() {
        // return $this->belongsTo('App\Models\Patients\Claims', 'claim_id', 'id');
		// switch practice page added condition for provider login based showing values
		// Revision 1 - Ref: MR-2719 22 Aug 2019: Selva
		if(Auth::check() && Auth::user()->isProvider())
			return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id')->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id)->with('pmtClaimFinData');
		else
			return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id')->with('pmtClaimFinData');
    }

    public function user() {
        return $this->belongsTo('App\Models\Medcubics\Users', 'assign_user_id', 'id');
    }

    public function created_by() {
        return $this->belongsTo('App\Models\Medcubics\Users', 'created_by', 'id');
    }

    protected $fillable = ['patient_id', 'claim_id', 'assign_user_id', 'fllowup_date', 'priority', 'status', 'description', 'created_by'];
    public static $rules = [
        'patient_id' => 'required',
        'claim_id' => 'required',
        'assign_user_id' => 'required',
        'priority' => 'required',
        'description' => 'required',
        'status' => 'required',
    ];
    public static $messages = [
        'patient_id.required' => 'Patient name!',
        'claim_id.required' => 'Claim number!',
        'assign_user_id.required' => 'Select assign user id!',
        'priority.required' => 'Select priority',
        'description.required' => 'Enter description',
        'status.required' => 'Select status',
    ];

    public static function getProblemListCount($p_id = '') {
		if($p_id != ''){
        $patient_id = Helpers::getEncodeAndDecodeOfId($p_id, 'decode');
        $count_problem_list = ProblemList::where('assign_user_id', Auth::user()->id)->where('patient_id', $patient_id)->where('status', '!=', 'Completed')->where('id', function ($sub) {
                    $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
                })->pluck("id")->all();
		}else{
			$count_problem_list = ProblemList::where('assign_user_id', Auth::user()->id)->where('status', '!=', 'Completed')->where('id', function ($sub) {
                    $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
                })->pluck("id")->all();
			
		}
        return count($count_problem_list);
    }

    public static function  getProblemListCount_User() {
		// where('assign_user_id', Auth::user()->id)-> Now showing all practice assigned user count
        /* $count_problem_list = ProblemList::where('status', '!=', 'Completed')->where('id', function ($sub) {
                    $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
                })->lists("id"); */
				
		//Practice Total Assigned Document Count - Ignores deleted document even if the document is in followup list
        //Revision 1 - Ref: MR-1264 06 Aug 2019: Selva
		// MEDV2-716 - completed status should not come : thilagavathy
		$count_problem_list = ProblemList::select('p1.*')->has('claim')->with(['claim' => function($query) {
                        $query->select('id', 'claim_number', 'facility_id', 'insurance_id', 'rendering_provider_id', 'date_of_service','total_charge');
                    }, 'claim.facility_detail' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.insurance_details' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.rendering_provider', 'patient' => function($query) {
                        $query->select('id', 'account_no','last_name','first_name','middle_name','title','account_no','dob','gender','address1','city','state','zip5','zip4','phone','mobile','is_self_pay');
                    }, 'user' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }, 'created_by' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }])->where('id', function ($sub) {
                        $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
                    })->where('status','!=','Completed')->count();
        return $count_problem_list;
    }

    ### problem list count for Provider Dashboard ## Author::Thilagavathy
    public static function getProviderProblemListCount_User() {       
        $provider_id = Auth::user()->provider_access_id;
        $count_problem_list = ProblemList::has('claim')->with(['claim' => function($query) {
                        $query->select('id', 'claim_number', 'facility_id', 'insurance_id', 'rendering_provider_id', 'date_of_service','total_charge');
                    }, 'claim.facility_detail' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.insurance_details' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.rendering_provider', 'patient' => function($query) {
                        $query->select('id', 'account_no','last_name','first_name','middle_name','title','account_no','dob','gender','address1','city','state','zip5','zip4','phone','mobile','is_self_pay');
                    }, 'user' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }, 'created_by' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }])->orderBy('id', 'desc')->where('id', function ($sub) {
                        $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
                    })->where('status','!=','Completed')
                    ->whereHas('claim', function($q)use($provider_id) {
                        $q->where('rendering_provider_id', $provider_id);
                    })->groupBy('claim_id')->get()->count();
        return $count_problem_list;
    }
    ### End Problem list count for Provider Dashboard

    ##3 Start Practice Total Assigned Document Count  For Provider  ## Author::Thilagavathy
    public static function getProviderProblemListCountByUser() {
        $provider_id = Auth::user()->provider_access_id;
        $count_problem_list = ProblemList::has('claim')->with(['claim' => function($query) {
                        $query->select('id', 'claim_number', 'facility_id', 'insurance_id', 'rendering_provider_id', 'date_of_service','total_charge');
                    }, 'claim.facility_detail' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.insurance_details' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.rendering_provider', 'patient' => function($query) {
                        $query->select('id', 'account_no','last_name','first_name','middle_name','title','account_no','dob','gender','address1','city','state','zip5','zip4','phone','mobile','is_self_pay');
                    }, 'user' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }, 'created_by' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }])->orderBy('id', 'desc')->where('id', function ($sub) {
            $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
        })->where('status','!=','Completed')->where('assign_user_id', Auth::user()->id)
        ->whereHas('claim', function($q)use($provider_id) {
            $q->where('rendering_provider_id', $provider_id);
        })->groupBy('claim_id')->get()->count();
        return $count_problem_list;
    }
    ### End 

    public static function getMainProblemListCount() {
        $problem_list = ProblemList::where('status', '!=', 'Completed')->where('id', function ($sub) {
            $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
        });
        $pblemList = clone $problem_list;
        $data['my_problem_list_count'] = $problem_list->where('assign_user_id', Auth::user()->id)->pluck("id")->all();
        $data['total_problem_list_count'] = $pblemList->pluck("id")->all();
        //dd($data);
        return $data;
    }
    
    // Start sidebar notification by baskar -04/02/19
    public static function getProblemListCountByUser() {
		//Practice Total Assigned Document Count - Ignores deleted document even if the document is in followup list
        //Revision 1 - Ref: MR-1264 06 Aug 2019: Selva
        // where('assign_user_id', Auth::user()->id)-> Now showing all practice assigned user count
        $count_problem_list = ProblemList::has('claim')->with(['claim' => function($query) {
                        $query->select('id', 'claim_number', 'facility_id', 'insurance_id', 'rendering_provider_id', 'date_of_service','total_charge');
                    }, 'claim.facility_detail' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.insurance_details' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.rendering_provider', 'patient' => function($query) {
                        $query->select('id', 'account_no','last_name','first_name','middle_name','title','account_no','dob','gender','address1','city','state','zip5','zip4','phone','mobile','is_self_pay');
                    }, 'user' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }, 'created_by' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }])->orderBy('id', 'desc')->where('id', function ($sub) {
            $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
        })->where('status','!=','Completed')->where('assign_user_id', Auth::user()->id)->groupBy('claim_id')->count();
        return $count_problem_list;
    }
    // End sidebar notification by baskar -04/02/19
	
 	// Start sidebar notification by baskar -04/02/19
    public static function getDueProblemListCountByUser() {
        $pblm_cnt = ProblemList::has('claim')->select('problem_lists.id')->where('assign_user_id', Auth::user()->id)->whereRaw('Date(fllowup_date) < DATE(UTC_TIMESTAMP())')->count();
        return $pblm_cnt;
    }
	// End sidebar notification by Ravikumar -23/09/19
}
