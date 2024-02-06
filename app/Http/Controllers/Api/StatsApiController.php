<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reports\StatsList as StatsList;
use App\Models\Reports\StatsDetail as StatsDetail;
use App\Models\Patients\Patient as Patient;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Models\Patients\ProblemList as ProblemList;
use App\Http\Helpers\Helpers as Helpers;
use Input;
use Auth;
use Response;
use Request;
use App;
use DB;
use Lang;
use App\Traits\ClaimUtil;

class StatsApiController extends Controller {

    use ClaimUtil;

    public function getSelectlistChangeApi($data) {
        $arr = explode("&", $data);
        $arr_new = '';
        $stats_detail = [];
        foreach ($arr as $arr_val) {
            $arr_new = explode("=", $arr_val);
            $stats_detail[$arr_new[0]] = $arr_new[1];
        }
        $stats_detail['user_id'] = Auth::user()->id;
        $stats_detail['stats_id'] = StatsList::where('name', $stats_detail['stats_name'])->value('id');
        unset($stats_detail['stats_name']);
        $stats_detail_count = StatsDetail::where('user_id', $stats_detail['user_id'])->where('position', $stats_detail['position'])->where('module_name', $stats_detail['module_name'])->count();
       
        if ($stats_detail['stats_id'] == "" || $stats_detail['stats_id'] == null) {
            if ($stats_detail_count) {
                $stats_detail_delete = StatsDetail::where('user_id', $stats_detail['user_id'])->where('position', $stats_detail['position'])->where('module_name', $stats_detail['module_name'])->delete();
                $stats_record_update = StatsDetail::where('user_id', $stats_detail['user_id'])->where('position', '>', $stats_detail['position'])->where('module_name', $stats_detail['module_name'])->get();
                $i = $stats_detail['position'];
                foreach ($stats_record_update as $stats_record_update_val) {
                    $stats_detail_update = StatsDetail::where('user_id', $stats_detail['user_id'])->where('position', $stats_record_update_val->position)->where('module_name', $stats_detail['module_name'])->update(['position' => $i]);
                    $i++;
                }
                $message = Lang::get("common.validation.remove_msg");
            }
        } else {
            if (!$stats_detail_count) {
                $stats_detail_create = StatsDetail::Create($stats_detail);
                $message = Lang::get("common.validation.create_msg");
            } else {
                $stats_detail_update = StatsDetail::where('user_id', $stats_detail['user_id'])->where('position', $stats_detail['position'])->where('module_name', $stats_detail['module_name'])->update($stats_detail);
                $message = Lang::get("common.validation.update_msg");
            }
        }

        $stats_list = StatsList::whereNotIn('id', StatsDetail::where('user_id', $stats_detail['user_id'])->pluck('stats_id')->all())->get();
        $module = $stats_detail['module_name'];
        return Response::json(array('status' => 'success', 'message' => @$message, 'data' => compact('module', 'stats_list')));
    }

    public static function getStatsDetail($module) {

        $user = Auth::user()->id;
        $stats_list = StatsList::whereNotIn('id', StatsDetail::where('module_name', '=', $module)->where('user_id', $user)->pluck('stats_id')->all())->get();
        $statsdetail = StatsDetail::with('statslist')->where('module_name', '=', $module)->where('user_id', '=', $user)->orderBy('position', 'asc')->get();
        $statsdetail_obj = json_decode(json_encode($statsdetail), true);
        $collect_count = array();

        foreach ($statsdetail_obj as $statsdetail) {

            if ($statsdetail['statslist'] != null && $statsdetail['statslist'] != '') {
                $get_name = str_replace("-", "", str_replace("stat-", "", $statsdetail['statslist']['image_name']));

                if ($get_name == "patient")
                    $call_function_name = "get_Patients";
                elseif ($get_name == "selfpay")
                    $call_function_name = "get_Self_pay";
                elseif ($get_name == "unbilled")
                    $call_function_name = "get_Unbilled";
                elseif ($get_name == "hold")
                    $call_function_name = "get_Hold";
                elseif ($get_name == "rejection")
                    $call_function_name = "get_Rejections";
                elseif ($get_name == "submitted")
                    $call_function_name = "get_Submitted";
                elseif ($get_name == "noshow")
                    $call_function_name = "get_No_show";                
                elseif ($get_name == "collections")
                    $call_function_name = "get_Collections";
                elseif ($get_name == "inspayment")
                    $call_function_name = "get_Ins_payments";
                elseif ($get_name == "patpayment")
                    $call_function_name = "get_Pat_payments";
                elseif ($get_name == "unapplied")
                    $call_function_name = "get_Unapplied";
                elseif ($get_name == "outstanding_ar")
                    $call_function_name = "get_Outstanding_ar";
                elseif ($get_name == "appointment")
                    $call_function_name = "get_Appointments";
                elseif ($get_name == "charges")
                    $call_function_name = "get_Charges";
                elseif ($get_name == "problemlist")
                    $call_function_name = "get_Problem_list";
                elseif ($get_name == "adjustments")
                    $call_function_name = "get_Adjustments";
                elseif ($get_name == "newvisit")
                    $call_function_name = "get_New_visits";
              
                $collect_count[$statsdetail['statslist']['id']] = self::$call_function_name();
            }
        }
        return compact('statsdetail_obj', 'collect_count', 'stats_list');
    }

    // Patients All patients (Both active and inactive) To Date
    public static function get_Patients() {
        return Patient::where('status','Active')->count();
    }

    public static function get_New_visits() {
        return PatientAppointment::where('status', '=', 'Scheduled')->count();
    }

    public static function get_Appointments() {        
        return PatientAppointment::whereIn('status', ['Complete', 'Scheduled', 'No Show', 'Canceled', 'Rescheduled','Encounter'])->count();
    }

    public static function TodayAndMonthAppointments() {
        $result = [];
        $result["today"] = PatientAppointment::whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete'])->whereRaw('Date(scheduled_on) = DATE(UTC_TIMESTAMP())')->count();
        $result["month"] = PatientAppointment::whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete'])->whereRaw('MONTH(scheduled_on) = MONTH(UTC_TIMESTAMP())')->count();
        return $result;
    }

    public static function get_No_show() {
        return PatientAppointment::where('status', 'No Show')->count();
    }

    // Self Pay All self pay patients (Both active and inactive)    To Date
    public static function get_Self_pay() {
        return Patient::where('status','Active')->where('is_self_pay', 'Yes')->count();
    }

    public static function CurrentYearOutstanding() {
        //return Claims::whereRaw("YEAR(created_at) = YEAR(CURDATE())")->sum(DB::raw('patient_due + insurance_due'));
        return Helpers::getCurrentMonthYearTotalCollections();
    }

    public static function CurrentMonthYearCharges() {
        /*
          $result["month"] = Claims::whereNotIn('status', ['Hold'])->whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->sum('total_charge');
          $result["year"] = Claims::whereNotIn('status', ['Hold'])->whereRaw('YEAR(created_at) = YEAR(CURDATE())')->sum('total_charge');
          return $result;
         */
        return Helpers::CurrentMonthYearCharges();
    }

    /* public static function TodayRejection($practice_db_name){

      return DB::connection($practice_db_name)->table("claims")->whereRaw('Date(created_at) = DATE(CURDATE())')->where('status', 'Denied')->where("deleted_at",null)->count();
      } */

    public static function TodayAndMonthRejection() {
        $result = [];
        // $result["today"] = Claims::whereRaw('Date(created_at) = DATE(CURDATE())')->where('status', 'Rejection')->count();
        // $result["month"] = Claims::whereRaw('MONTH(created_at) = MONTH(CURDATE())')->where('status', 'Rejection')->count();
        return Helpers::getTodayAndMonthRejection();
    }

    /* public static function TodayUnbilled($practice_db_name)
      {
      return DB::connection($practice_db_name)->table("claims")->where(function($query) {
      return $query->where('status','Patient')->where('self_pay','Yes')->where('patient_paid','0.00')->where('patient_due',"!=",'0.00')->whereRaw('Date(created_at) = DATE(CURDATE())');
      })->orWhere(function($query) {
      return $query->where('status','Ready')->where('insurance_due',"!=",'0.00')->where('self_pay','No')->where('claim_submit_count','0')->whereRaw('Date(created_at) = DATE(CURDATE())');
      })->where("deleted_at",null)->count();
      } */

    public static function TodayAndMonthUnbilled() {
        /*
          $result["today"] = Helpers::priceFormat(Claims::has('paymentclaimtransaction', '<', 1)->whereIn('status', ['Ready'])->whereRaw('Date(created_at) = DATE(CURDATE())')->sum('total_charge'), 'no');
          $result["month"] = Claims::has('paymentclaimtransaction', '<', 1)->whereIn('status', ['Ready'])->whereRaw('MONTH(created_at) = MONTH(CURDATE())')->sum('total_charge');
         */
        $result = Helpers::getTodayAndMonthUnbilled();
        return $result;
    }

    public static function get_Problem_list() {
		//Practice Total Assigned Document Count - Ignores deleted document even if the document is in followup list
        //Revision 1 - Ref: MR-1264 06 Aug 2019: Selva
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
        })->where('status','!=','Completed')->groupBy('claim_id')->get()->count();
        return $count_problem_list;
    }

    public static function CurrentMonthProblemList() {
        $data['total'] = ProblemList::has('claim')->with(['claim' => function($query) {
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
        })->where('status','!=','Completed')->groupBy('claim_id')->get()->count();
		$data['month'] =  ProblemList::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')->where("status", "!=", "Completed")->groupBy('claim_id')->count();
		$data['total_workbench'] =  ProblemList::distinct()->where("status", "!=", "Completed")->get(['claim_id'])->count();
        return $data;
    }

    public static function CurrentMonthYearCollections($practice_key) {
        $result = [];
        // $result["month"] = Claims::whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->sum(DB::raw('insurance_paid + patient_paid'));
        // $result["year"] = Claims::whereRaw('YEAR(created_at) = YEAR(CURDATE())')->sum(DB::raw('insurance_paid + patient_paid'));
        return Helpers::getCurrentMonthYearTotalCollections($practice_key);
    }

    // Ins Payments Insurance Payments  month to date
    public static function get_Ins_payments() {
        //return '$' . Helpers::priceFormat(Claims::sum('insurance_paid'), 'no');
       $insPaymentDetails = Helpers::getCurrentMonthYearTotalCollections();	
		return Helpers::priceFormat($insPaymentDetails['payment_tillDate'],'yes');
    }

    public static function get_Unapplied() {
        //return '$' . Helpers::priceFormat(Payment::sum('balance'), 'no');
         return Helpers::getTotalUnappliedPmt();
    }

    // Get Current month adjustment
    public static function get_Adjustments() {
        return Helpers::getTotalClaimAdjustmentAmount();
    }

    public static function get_Collections() {
		$totalCollection = Helpers::getCurrentMonthYearTotalCollections();
        return Helpers::priceFormat($totalCollection['till_date'],'yes');
    }

    // Get current month patient payment including wallet
    public static function get_Pat_payments() {
        $insPaymentDetails = Helpers::getCurrentMonthYearTotalCollections();		
		return Helpers::priceFormat($insPaymentDetails['pat_payment_till'],'yes');
    }

    // Unbilled Claim Submit Count = 0 and Status = Ready   To Date
    public static function get_Unbilled() {
        $unbilledDetails = Helpers::getClaimStats('unbilled');
        return Helpers::priceFormat($unbilledDetails['unbilled']['total_amount'],'yes');
    }

    public static function get_Hold() {
        $holdDetails = Helpers::getClaimStats('hold');
        return Helpers::priceFormat($holdDetails['hold']['total_amount'],'yes');
    }

    public static function get_Rejections() {
		$rejectionDetails = Helpers::getClaimStats('rejected');
        return $rejectionDetails['rejected']['total_charges'];
    }

    public static function get_Submitted() {
        //return Claims::whereIn('status', ['Submitted', 'Paid', 'Denied', 'Partial Paid'])->count();
        return Helpers::getSubmittedClaimCount();
    }

    // Get Current month charge amount
    public static function get_Charges() {
        return Helpers::getTotalChargesAmount();
    }

    public static function get_Outstanding_ar() {
        //return Helpers::priceFormat(Claims::where("patient_id", "!=", "0")->sum(DB::raw('patient_due + insurance_due')), 'yes');
        return Helpers::priceFormat( Helpers::getTotalOutstandingAr(), 'yes');
    }

    public static function getPracticeStatsDetail($details) {
        $resp = [];    
        if(!empty($details)) {
            foreach($details as $get_name) {
                $call_function_name = "get".str_replace(" ", "", ucwords(str_replace("_", " ", $get_name)));
                //echo "<br>##".$call_function_name."##";          
                if(is_callable('self::'.$call_function_name)) {
                    $resp[$get_name] = self::$call_function_name();    
                } else {
                    \Log::info("Function not exist ".$get_name."##".$call_function_name);                    
                    $resp[$get_name] = 0;
                }                
            }
        } 
        return $resp;    
    }

     // Get Current month charge amount
    public static function get_Patient_statements() {
        return 0; //Helpers::getCurrentMonthChargeAmount();
    }

    public static function getPatientStatementSent() {
        return Helpers::getTotalPatientStatements();
    }


    public static function getErrorLogCount() {
        return 0;
    }

    public static function getPatientStatementApiUsage() {
        return 0;
    }

    public static function getDocumentUploadSize() {
        return 0;
    }

    public static function getDocumentCount() {
        return Helpers::getTotalDocuments();
    }

    public static function getEligibilityApiUsage() {
        return 0;
    }

    
    public static function getTwilioSmsUsage() {
        return 0;
    }

    
    public static function getTwilioCallUsage() {
        return 0;
    }

    public static function getTwilioFaxUsage() {
        return 0;
    }

    public static function getTotalPatients() {
        return Patient::count();
    }

    public static function getTotalCharges() {
        return Helpers::getTotalCharges();
    }
    
    public static function getTotalPayments() {
        return Helpers::getTotalPayments();
    }

    public static function getPatientPayments() {
        return Helpers::getPatientPayments();
    }

    public static function getInsurancePayments() {
        return Helpers::getInsurancePayments();
    }

    public static function getTotalAdjustment() {
        return Helpers::getTotalAdjustment();
    }

    public static function getTotalDenial() {
        return Helpers::getTotalDenial();
    }

    public static function getTotalRejections() {
        return Helpers::getTotalRejections();
    }
    
    public static function getTotalSubmittedClaims() {
        return Helpers::getTotalSubmittedClaims();
    }

    public static function getFrequentlyGeneratedReports() {
        return 0;
    }

    public static function getTotalReportsGenerated() {
        return 0;
    }
    
    public static function getTemplatesSent() {
        return Helpers::getTemplatesSent();
    }

    public static function getPatientIntakeUsage() {
        return 0;
    }

    public static function getChargeCaptureUsage() {
        return 0;
    }
    
    public static function getUsers() {
        return Helpers::getUsers();
    }

    public static function getProviders() {
        return Helpers::getProviders();
    }
    
    public static function getAddressNpiApi() {
        return 0;
    }

    public static function getAppointments() {
        return PatientAppointment::whereIn('status', ['Complete', 'Scheduled', 'No Show', 'Canceled', 'Encounter'])->count();
    }
}
