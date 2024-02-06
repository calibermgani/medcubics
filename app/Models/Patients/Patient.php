<?php

namespace App\Models\Patients;

use App\Models\Payments\PMTWalletV1;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\Helpers as Helpers;
use DB;
use DateTime;
use App\Models\Patients\PatientBudget;
use App\Models\Patients\PatientInsuranceArchive as PatientInsuranceArchive;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Models\Insurance as Insurance;
use App\Models\Code as Code;
use App\Models\Practice as Practice;
use Illuminate\Database\Eloquent\SoftDeletes;
// New payment tables
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Traits\ClaimUtil;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\STMTCategory;
use App\Models\STMTHoldReason;
use Carbon\Carbon;
//use App\Models\Payments\PMTWalletV1;
class Patient extends Model {

    use SoftDeletes;
    use ClaimUtil;

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

    public function insurance_details() {
        return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id');
    }

    public function ethnicity_details() {
        return $this->belongsTo('App\Models\Ethnicity', 'ethnicity_id', 'id');
    }

    public function correspondence_details() {
        return $this->hasMany('App\Models\Patients\PatientCorrespondence', 'patient_id', 'id');
    }

    public function language_details() {
        return $this->belongsTo('App\Models\Language', 'language_id', 'id');
    }

    public function facility_details() {
        return $this->belongsTo('App\Models\Facility', 'facility_id', 'id');
    }

    public function provider_details() {
        return $this->belongsTo('App\Models\Provider', 'provider_id', 'id');
    }

    public function patient_insurance() {
        return $this->hasMany('App\Models\Patients\PatientInsurance')->with('insurance_details');
    }

    public function patient_claim() {
        // return $this->hasMany('App\Models\Patients\Claims', 'patient_id', 'id');
        return $this->hasMany('App\Models\Payments\ClaimInfoV1', 'patient_id', 'id')->with('insurance_details');
    }

    public function patient_claim_fin() {
        return $this->hasMany('App\Models\Payments\PMTClaimFINV1', 'patient_id', 'id')
                        ->select(DB::raw('patient_id, sum(insurance_due) AS total_ins_due, sum(patient_due) AS total_pat_due, '
                                        . '(sum(total_charge)-(sum(patient_paid)+sum(insurance_paid) + sum(withheld) + sum(patient_adj) + sum(insurance_adj) )) AS total_ar'))
                        ->groupBy('patient_id');
    }

    public function patient_insurance_archive() {
        return $this->hasMany('App\Models\Patients\PatientInsuranceArchive');
    }

    public function country_details() {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    public function contact_details() {
        return $this->hasMany('App\Models\Patients\PatientContact', 'patient_id', 'id');
    }

    public function patient_insurance_detils() {
        return $this->hasMany('App\Models\Patients\PatientInsurance', 'patient_id', 'id');
    }

    public function authorization_details() {
        return $this->hasMany('App\Models\Patients\PatientAuthorization', 'patient_id', 'id');
    }

    public function notes() {
        return $this->hasMany('App\Models\Patients\PatientNote', 'notes_type_id', 'id');
    }

    public function get_notes_app() {
        return $this->hasMany('App\Models\Patients\PatientNote', 'notes_type_id', 'id')->where('patient_notes_type','<>','followup_notes')->orderBy('id','desc')->with('claims')->selectRaw("*,DATE_FORMAT(created_at,'%m/%d/%y %H:%i:%s') as date");
    }

    public function patient_statement_note() {
        return $this->hasOne('App\Models\Patients\PatientNote', 'notes_type_id', 'id')
            ->where('patient_notes_type','=','statement_notes')
            ->where('status', 'Active')
            ->select('id', 'content', 'notes_type_id')
            ->orderBy('id','desc')->latest();
    }

    public function patient_last_pmt() {
        $practice_timezone = Helpers::getPracticeTimeZone();
        return $this->hasOne('App\Models\Payments\PMTInfoV1', 'patient_id', 'id')
                ->where('pmt_method', 'Patient')
                ->whereIn('pmt_type', ['Payment', 'Credit Balance'])                
                ->select("pmt_amt as total_paid",DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), 'patient_id')
                ->orderBy('created_at','desc')->latest();           
    }

    public function patient_appointment() {
        return $this->belongsTo('App\Models\Scheduler\PatientAppointment', 'patient_id', 'id');
    }

    public function patient_sch_appointment() {
        return $this->hasMany('App\Models\Scheduler\PatientAppointment', 'patient_id', 'id');
    }

    public function pos_details() {
        return $this->belongsTo('App\Models\Pos', 'pos_id', 'id');
    }

    public function insured_detail() {
        return $this->hasMany('App\Models\Patients\PatientInsurance', 'patient_id', 'id')->with('insurance_details');
    }

    public function patient_budget() {
        return $this->belongsTo('App\Models\Patients\PatientBudget', 'id', 'patient_id')->where('status','Active')->whereNull('deleted_at');
    }

    public function creator() {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function patient_document() {
        return $this->hasMany('App\Models\Document', 'type_id', 'id')->where('document_type', 'patients');
    }

    public function pmt_info() {
        // return $this->hasMany('App\Models\Patients\Claims', 'patient_id', 'id');
        return $this->hasMany('App\Models\Payments\PMTInfoV1', 'patient_id', 'id')->where('pmt_type', 'Refund')->where('source', 'refundwallet')->select(DB::raw('sum(pmt_amt) AS refund_amt'), 'patient_id')->where('pmt_mode', 'Check')->groupby('patient_id');
    }
    
    public function stmt_category_info() {
        return $this->belongsTo('App\Models\STMTCategory', 'stmt_category', 'id');
    }

    public function stmt_holdreason_info() {
        return $this->belongsTo('App\Models\STMTHoldReason', 'hold_reason', 'id');
    }

    protected $fillable = [
        'account_no',
        'is_self_pay',
        'last_name',
        'first_name',
        'middle_name',
        'title',
        'address1',
        'address2',
        'city',
        'state',
        'zip5',
        'zip4',
        'country_id',
        'gender',
        'ssn',
        'dob',
        'phone',
        'work_phone',
        'work_phone_ext',
        'mobile',
        'email',
        'driver_license',
        'ethnicity_id',
        'race',
        'language_id',
        'employment_status',
        'employer_name',
        'marital_status',
        'student_status',
        'provider_id',
        'facility_id',
        'email_notification',
        'phone_reminder',
        'preferred_communication',
        'statements',
        'stmt_category',
        'hold_reason',
        'hold_release_date',
        'statements_sent',
        'bill_cycle',
        'deceased_date',
        'medical_chart_no',
        'demographic_status',
        'status',
        'percentage',
        'demo_percentage',
        'ins_percentage',
        'contact_percentage',
        'auth_percentage',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
        'avatar_name',
        'avatar_ext',
        'organization_name',
        'occupation',
        'referring_provider_id'
    ];
    public static $rules = [
        'last_name' => 'required|regex:/^[A-Za-z- \t]*$/i',
        'first_name' => 'required|regex:/^[A-Za-z- \t]*$/i',
        'middle_name' => 'nullable|alpha',
        'gender' => 'required',
        //'address1' 			=> 'required|regex:/^[A-Za-z0-9 \t]*$/i',
        //'address2' 			=> 'regex:/^[A-Za-z0-9 \t]*$/i',
        //'stmt_category' => 'required',        
        'state' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        'city' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        //'medical_chart_no' 	=> 'alpha_num',
        'bill_cycle' => 'required',
        'status' => 'required',
    ];
    public static $messages = [
        'last_name.required' => 'Last name is required!',
        'last_name.alpha_num' => 'Last name contains only alpha numeric!',
        'last_name.regex' => 'Alpha, space only allowed',
        'first_name.regex' => 'Alpha, space only allowed',
        'last_name.max' => 'Last name contains only 25 Characters!',
        'first_name.required' => 'First name is required!',
        //'address1.required' 	=> 'Address1 is required!',
        'address1.regex' => 'Alpha numeric, space only allowed',
        //'address2.regex'		=> 'Alpha numeric, space only allowed',
        'state.regex' => 'Alpha numeric, space only allowed',
        'city.regex' => 'Alpha numeric, space only allowed',
        'gender.required' => 'Select your gender!',
        'bill_cycle.required' => 'Select your bill cycle',
        'ssn.chk_ssn_dob_unique' => 'This SSN already exists'
    ];

    /*     * * Get all patient detail start  ** */

    public static function getAllpatients() {
        $patient = Patient::orderBy('id', 'ASC')->selectRaw('CONCAT(last_name,", ",first_name," ",middle_name) as patient_name, id')->pluck("patient_name", "id")->all();
        return $patient;
    }

    /*     * * Get all patient detail end  ** */

    // Get Patient insurance with category starts here
    public static function getPatientInsurance($patient_id, $is_decode = true) {

        if ($is_decode)
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        //$getpatientinsurances = PatientInsurance::with(array('insurance_details'=>function($query){ $query->select('id','insurance_name');}))->where('patient_id', $patient_id)->select('id', 'insurance_id', 'category')->orderBy('orderby_category', 'asc')->get();

        $getpatientinsurances = PatientInsurance::with('insurance_details')->Where('category', 'Primary')->where('patient_id', $patient_id)->orderBy('orderby_category', 'asc')->get();
        $insurance_lists = [];
        if (!empty($getpatientinsurances)) {
            foreach ($getpatientinsurances as $getins) {
                if (!empty($getins->insurance_details)) {
                    $insurance_lists[@$getins->category . '-' . @$getins->insurance_id] = @$getins->category . '-' . @$getins->insurance_details->insurance_name;
                }
            }
        }
        return $insurance_lists;
    }

    //only primary,secondary,teriary
    public static  function getPatientUniqueInsuranceDetails($patient_id){
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $getpatientinsurances = PatientInsurance::whereIn('category', ['Primary', 'Secondary', 'Tertiary'])->with('insurance_details')->where('patient_id', $patient_id)->orderBy('orderby_category', 'asc')->get();
        $insurance_lists = [];
        if (!empty($getpatientinsurances)) {
            foreach ($getpatientinsurances as $getins) {
                if (!empty($getins->insurance_details)) {
                    $insurance_lists[@$getins->category . '-' . @$getins->insurance_id] = @$getins->category . '-' . @$getins->insurance_details->insurance_name;
                }
            }
        }
        return $insurance_lists;
    }

    // Get Patient insurance with category starts here
    public static function getPatientInsuranceWithCategory($patient_id, $is_decode = true, $claim_id = null, $from=null) {
        if ($is_decode)
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $posted_insurance = ClaimInfoV1::where('patient_id', $patient_id)->groupby('insurance_id');
        $posted_insurance = is_null($claim_id) ? $posted_insurance->pluck('insurance_id')->all() : $posted_insurance->where('id', $claim_id)->pluck('insurance_id')->all();
        $getpatientinsurancess = PatientInsurance::with('insurance_details')->Where('category', 'Primary')->where('patient_id', $patient_id)->pluck('insurance_id')->all();

        if (!empty($posted_insurance)) {
            $insurance_difference = array_filter(array_diff($posted_insurance, $getpatientinsurancess));
            if (!empty($insurance_difference)) {
                //PatientInsuranceArchive   here it was changed to PatientInsurance because archive make records only the insurance gets deleted
                $archive_insurance_data = PatientInsurance::with('insurance_details')->whereIn('insurance_id', $insurance_difference)->groupby('insurance_id')->where('patient_id', $patient_id)->get();
            }
        }
        if (!empty($claim_id)) {
			if(empty($from))
				$getpatientinsurances = PatientInsurance::whereIn('category', ['Primary'])->with('insurance_details')->where('patient_id', $patient_id)->orderBy('orderby_category', 'asc')->get();
			else
				$getpatientinsurances = PatientInsurance::whereIn('category', ['Primary', 'Secondary', 'Tertiary'])->with('insurance_details')->where('patient_id', $patient_id)->orderBy('orderby_category', 'asc')->get();
        } else {
            $getpatientinsurances = PatientInsurance::with('insurance_details')->where('patient_id', $patient_id)->orderBy('orderby_category', 'asc')->get();
        }
        $insurance_archieve_lists = [];
        if (!empty($archive_insurance_data)) {
            foreach ($archive_insurance_data as $getins) {
                if (!empty($getins->insurance_details)) {
                    $insurance_archieve_lists[@$getins->category . '-' . @$getins->insurance_id] = @$getins->category . '-' . @$getins->insurance_details->insurance_name;
                }
            }
        }
        $insurance_lists = [];
        if (!empty($getpatientinsurances)) {
            foreach ($getpatientinsurances as $getins) {
                if (!empty($getins->insurance_details)) {
                    $insurance_lists[@$getins->category . '-' . @$getins->insurance_id] = @$getins->category . '-' . @$getins->insurance_details->insurance_name;
                }
            }
        }
        $insurance_lists = (!empty($from))?$insurance_lists:array_merge($insurance_lists, $insurance_archieve_lists);
        return $insurance_lists;
    }

    public static function getPatientInsuranceEditcharge($patient_id, $is_decode = true, $claim_id = null) {
        if ($is_decode)
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        //$claim_data    = ClaimInfoV1::where('patient_id', $patient_id)->where('id', $claim_id)->first();		
        $claim_data = ClaimInfoV1::where('patient_id', $patient_id)->where('id', $claim_id)->first();
        $claim_category = $claim_data->insurance_category;
        $insurance_id = $claim_data->insurance_id;
        $created_at = $claim_data->create_at;
        if (!empty($claim_category) && !empty($insurance_id)) {
            $getpatientinsurancess = PatientInsurance::with('insurance_details')->where('patient_id', $patient_id)->where('category', $claim_category)->pluck('insurance_id')->all();
        } else {
            $getpatientinsurancess = PatientInsurance::with('insurance_details')->where('patient_id', $patient_id)->orderBy('orderby_category', 'asc')->get();
        }
        if (empty($getpatientinsurancess)) {
            $archive_insurance_data = PatientInsuranceArchive::with('insurance_details')->where('category', $claim_category)->where('insurance_id', $insurance_id)->where('patient_id', $patient_id)->first();
            $insurance_lists[@$archive_insurance_data->category . '-' . @$archive_insurance_data->insurance_id] = @$archive_insurance_data->category . '-' . @$archive_insurance_data->insurance_details->insurance_name;
        } else {
            $insurance_lists[@$getpatientinsurancess->category . '-' . @$getpatientinsurancess->insurance_id] = @$getpatientinsurancess->category . '-' . @$getpatientinsurancess->insurance_details->insurance_name;
        }
        return $insurance_lists;
    }

    // Get Patient insurance with category ends here

    /*     * *  Get patient ledger finacial related data starts here ** */
    public static function getFinancialData($patient_id) {
        $ledgerfinancialdetail = [];
        //  $total_charges = Claims::select(DB::raw("SUM(insurance_paid) as insurance_paid"), DB::raw("SUM(patient_paid) as patient_paid"), DB::raw("SUM(total_charge) as total_charge"))->where('patient_id', $patient_id)->whereNotIn('status', ['Hold'])->first();
        // @todo join cliam_info with pmt_claim_fin_v1 to get the below details.
        //$total_charges 	= PMTClaimFINV1::select(DB::raw("SUM(insurance_paid) as insurance_paid"),DB::raw("SUM(patient_paid) as patient_paid"),DB::raw("SUM(total_charge) as total_charge"))->where('patient_id', $patient_id)->whereNotIn('status', ['Hold'])->first();
        $total_charges = Patient::getPatientFinDetails($patient_id);
        $ledgerfinancialdetail['ins_paid'] = $total_charges['total_insurance_paid'];
        $ledgerfinancialdetail['pat_paid'] = $total_charges['total_patient_paid'];
        $ledgerfinancialdetail['billed'] = Helpers::getPatientBilledAmount($patient_id);
        
        //$ledgerfinancialdetail['unbilled'] 	= 	Claims::where('patient_id', $patient_id)->whereIn('status', ['Patient', 'Ready'])->sum('total_charge');	
        //$ledgerfinancialdetail['unbilled'] = Claims::has('paymentclaimtransaction', '<', 1)->where('patient_id', $patient_id)->whereIn('status', ['Ready'])->sum('total_charge');
        $ledgerfinancialdetail['unbilled'] = Helpers::getPatientUnBilledAmount($patient_id);
        /* $last_payment =  ClaimUtil::getPatientLastPaymentDate();//PMTClaimTXV1::where('patient_id', $patient_id)->whereIn('pmt_method', ['Patient', 'Insurance'])->orderBy('created_at', 'DESC')->select("created_at")->first();
          if (!empty($last_payment))
          $last_payment = $last_payment->toArray();
          else
          $last_payment["created_at"] = ''; */
        $ledgerfinancialdetail['last_pay'] = Helpers::getPatientLastPaymentDate($patient_id); //$last_payment["created_at"];
        $date = Helpers::timezone(date("m/d/y H:i:s"),'Y-m-d');
        $last_app = PatientAppointment::where('patient_id', $patient_id)
                        //->where('status', "Complete")
                        ->where('deleted_at', NULL)
                        ->where('scheduled_on', "<=", $date)
                        ->orderBy('scheduled_on', 'desc')
                        ->pluck('scheduled_on')->first();
        $future_app = PatientAppointment::where('patient_id', $patient_id)->where('status', "Scheduled")->where('scheduled_on', ">=", $date)->orderBy('scheduled_on', 'asc')->pluck('scheduled_on')->first();

        $ledgerfinancialdetail['last_appt'] = ($last_app) ? $last_app : '-';
        $ledgerfinancialdetail['future_appt'] = ($future_app) ? $future_app : '-';
        //echo "<pre>";print_r($ledgerfinancialdetail);die;
        return $ledgerfinancialdetail;
    }

    /*     * *  Get patient ledger finacial related data end here ** */

    /*     * *  Get patient ledger red alert data starts here ** */

    public static function getRedalertData($patient_id) {
        $ledgerredalertdetail = [];
        $practice_timezone = Helpers::getPracticeTimeZone();
        $return_check = Helpers::getPatientClaimDateOfService($patient_id); //Claims::where('patient_id', $patient_id)->where('balance_amt', '<>', 0)->orderBy('date_of_service', 'ASC')->pluck('date_of_service');
        $current_date = Helpers::timezone(date("m/d/y H:i:s"),'Y-m-d');
        $date2 = new DateTime($return_check);
        $date1 = new DateTime($current_date);
        $diff = $date1->diff($date2);
        $ledgerredalertdetail['return_check'] = ($diff->days == 0) ? 0 : $diff->days + 1;
        $statement_det = Patient::where('id', $patient_id)->select('statements', 'statements_sent',DB::raw('CONVERT_TZ(last_statement_sent_date,"UTC","'.$practice_timezone.'") as last_statement_sent_date'))->first();
        $ledgerredalertdetail['statement'] = ($statement_det['statements']) ? $statement_det['statements'] : '';
        $ledgerredalertdetail['statement_sent'] = ($statement_det['statements_sent']) ? $statement_det['statements_sent'] : '';
        $ledgerredalertdetail['last_statement'] = ($statement_det['last_statement_sent_date']) ? $statement_det['last_statement_sent_date'] : '';
        return $ledgerredalertdetail;
    }

    /*     * *  Get patient ledger red alert data end here ** */

    /*     * *  Get patient ledger age data starts here ** */
  
    public static function getageData($patient_id, $start, $end, $key) {
        $start_date = '-' . $start . ' day'; 
       /* $start_date = date('Y-m-d', strtotime($start_date));
        if ($end != 'above') {

            $end_date = '-' . $end . ' day';
            $end_date = date('Y-m-d', strtotime($end_date));
        }*/
        ## Get Aging Date by using Practice timezone Author::Thilagavathy
        $getdate_Arr = Self::getstartAndEndDate($start,$end);
        $start_date = $getdate_Arr['start_date'];
        $end_date = $getdate_Arr['end_date'];      
        ## End Aging Dates Author::Thilagavathy
        $charge = ClaimInfoV1::LeftJoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->whereNull('claim_info_v1.deleted_at')
                ->where('claim_info_v1.patient_id', $patient_id);
        
        $get_ar = 0;
        $getOverPayment = 0;
        
        if ($key == "Patient") {
            /* Patient Ledger page Patient AR Outstanding Balance */
            $charge = $charge->where('claim_info_v1.insurance_id', 0); //->where('pmt_info_v1.pmt_method', "Patient");
            if ($end == "above") {
                $get_ar = $charge->where('claim_info_v1.date_of_service', "<=", $start_date)
                        ->sum('pmt_claim_fin_v1.patient_due');
            } else {
                $get_ar = $charge->where('claim_info_v1.date_of_service', ">=", $end_date)
                        ->where('claim_info_v1.date_of_service', "<=", $start_date)
                        ->sum('pmt_claim_fin_v1.patient_due');
            }
            
            $getOverPayment = ClaimInfoV1::LeftJoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->whereNull('claim_info_v1.deleted_at')
                ->where('claim_info_v1.patient_id', $patient_id)
                ->where('pmt_claim_fin_v1.patient_due', '<','0')
                ->where('claim_info_v1.insurance_id','!=',0);
            
            if ($end == "above") {
                $getOverPayment = $getOverPayment->where('claim_info_v1.date_of_service', "<=", $start_date);
            } else {
                $getOverPayment = $getOverPayment->where('claim_info_v1.date_of_service', ">=", $end_date)
                        ->where('claim_info_v1.date_of_service', "<=", $start_date);
            }
            
            $getOverPayment = $getOverPayment->sum('pmt_claim_fin_v1.patient_due');

        } elseif ($key == "Insurance") {
            /* Patient Ledger page Insurance AR Outstanding Balance */

            $charge = $charge->where('claim_info_v1.patient_id', $patient_id)
                    ->where('claim_info_v1.insurance_id', "!=", "0")
                    ->where('claim_info_v1.claim_submit_count','>' ,0);
            
            if ($end == "above") {
                $charge = $charge->where('claim_info_v1.date_of_service', "<=", $start_date);
            } else {
                $charge = $charge->where('claim_info_v1.date_of_service', "<=", $start_date)
                        ->where('claim_info_v1.date_of_service', ">=", $end_date);
            }
            //Get Insurance Due only when Responsibility is in Isnurance
            //Get Insurance Due, Patient Payment and Patient Adj since Insurance AR = Ins. Due - (Pat. Paid + Pat. Adj)
            $insurance_due = $charge->sum('pmt_claim_fin_v1.insurance_due');
            $patient_payment = $charge->sum('pmt_claim_fin_v1.patient_paid');
            $patient_adjustment = $charge->sum('pmt_claim_fin_v1.patient_adj');
            //Insurance AR = Ins. Due - (Pat. Paid + Pat. Adj)
            $get_ar = $insurance_due - ($patient_payment + $patient_adjustment);
            
             $getOverPayment = ClaimInfoV1::LeftJoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->whereNull('claim_info_v1.deleted_at')
                ->where('claim_info_v1.patient_id', $patient_id)
               // ->where('claim_info_v1.claim_submit_count','>' ,0)
                ->where('pmt_claim_fin_v1.insurance_due', '<','0')
                ->where('claim_info_v1.insurance_id',0);
            
            if ($end == "above") {
                $getOverPayment = $getOverPayment->where('claim_info_v1.date_of_service', "<=", $start_date);
            } else {
                $getOverPayment = $getOverPayment->where('claim_info_v1.date_of_service', ">=", $end_date)
                        ->where('claim_info_v1.date_of_service', "<=", $start_date);
            }
            
            $getOverPayment = $getOverPayment->sum('pmt_claim_fin_v1.insurance_due');
        }
        /* Return the Patient AR Outstanding & Insurance AR outstanding  */
        
        return $get_ar + $getOverPayment;
    }
	
	public static function getageDataForSTMT($patient_id, $start, $end, $key) {
        $start_date = '-' . $start . ' day'; 
       /* $start_date = date('Y-m-d', strtotime($start_date));
        if ($end != 'above') {

            $end_date = '-' . $end . ' day';
            $end_date = date('Y-m-d', strtotime($end_date));
        }*/
        ## Get Aging Date by using Practice timezone Author::Thilagavathy
        $getdate_Arr = Self::getstartAndEndDate($start,$end);
        $start_date = $getdate_Arr['start_date'];
        $end_date = $getdate_Arr['end_date'];      
        ## End Aging Dates Author::Thilagavathy
        $charge = ClaimInfoV1::LeftJoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->whereNull('claim_info_v1.deleted_at')
                ->where('claim_info_v1.status','!=','Hold')
                ->where('claim_info_v1.patient_id', $patient_id);
        
        $get_ar = 0;
        $getOverPayment = 0;
        
        if ($key == "Patient") {
            /* Patient Ledger page Patient AR Outstanding Balance */
            $charge = $charge->where('claim_info_v1.insurance_id', 0); //->where('pmt_info_v1.pmt_method', "Patient");
            if ($end == "above") {
                $get_ar = $charge->where('claim_info_v1.date_of_service', "<=", $start_date)
                        ->sum('pmt_claim_fin_v1.patient_due');
            } else {
                $get_ar = $charge->where('claim_info_v1.date_of_service', ">=", $end_date)
                        ->where('claim_info_v1.date_of_service', "<=", $start_date)
                        ->sum('pmt_claim_fin_v1.patient_due');
            }
            
            $getOverPayment = ClaimInfoV1::LeftJoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->whereNull('claim_info_v1.deleted_at')
                ->where('claim_info_v1.patient_id', $patient_id)
				->where('claim_info_v1.status','!=','Hold')
                ->where('pmt_claim_fin_v1.patient_due', '<','0')
                ->where('claim_info_v1.insurance_id','!=',0);
            
            if ($end == "above") {
                $getOverPayment = $getOverPayment->where('claim_info_v1.date_of_service', "<=", $start_date);
            } else {
                $getOverPayment = $getOverPayment->where('claim_info_v1.date_of_service', ">=", $end_date)
                        ->where('claim_info_v1.date_of_service', "<=", $start_date);
            }
            
            $getOverPayment = $getOverPayment->sum('pmt_claim_fin_v1.patient_due');

        } elseif ($key == "Insurance") {
            /* Patient Ledger page Insurance AR Outstanding Balance */

            $charge = $charge->where('claim_info_v1.patient_id', $patient_id)
                    ->where('claim_info_v1.insurance_id', "!=", "0")
                    ->where('claim_info_v1.claim_submit_count','>' ,0);
            
            if ($end == "above") {
                $charge = $charge->where('claim_info_v1.date_of_service', "<=", $start_date);
            } else {
                $charge = $charge->where('claim_info_v1.date_of_service', "<=", $start_date)
                        ->where('claim_info_v1.date_of_service', ">=", $end_date);
            }
            //Get Insurance Due only when Responsibility is in Isnurance
            //Get Insurance Due, Patient Payment and Patient Adj since Insurance AR = Ins. Due - (Pat. Paid + Pat. Adj)
            $insurance_due = $charge->sum('pmt_claim_fin_v1.insurance_due');
            $patient_payment = $charge->sum('pmt_claim_fin_v1.patient_paid');
            $patient_adjustment = $charge->sum('pmt_claim_fin_v1.patient_adj');
            //Insurance AR = Ins. Due - (Pat. Paid + Pat. Adj)
            $get_ar = $insurance_due - ($patient_payment + $patient_adjustment);
            
             $getOverPayment = ClaimInfoV1::LeftJoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->whereNull('claim_info_v1.deleted_at')
                ->where('claim_info_v1.patient_id', $patient_id)
               // ->where('claim_info_v1.claim_submit_count','>' ,0)
                ->where('pmt_claim_fin_v1.insurance_due', '<','0')
                ->where('claim_info_v1.insurance_id',0);
            
            if ($end == "above") {
                $getOverPayment = $getOverPayment->where('claim_info_v1.date_of_service', "<=", $start_date);
            } else {
                $getOverPayment = $getOverPayment->where('claim_info_v1.date_of_service', ">=", $end_date)
                        ->where('claim_info_v1.date_of_service', "<=", $start_date);
            }
            
            $getOverPayment = $getOverPayment->sum('pmt_claim_fin_v1.insurance_due');
        }
        /* Return the Patient AR Outstanding & Insurance AR outstanding  */
        
        return $get_ar + $getOverPayment;
    }

    public static function getstartAndEndDate($start,$end){
        $practice_timezone = Helpers::getPracticeTimeZone(); 
       if(@$start == 0){
            $return_data['start_date'] = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$start))));         
        }
        else{
            $return_data['start_date'] = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$start-1))));
        }
         if((empty($end)) || $end == 'above'){
             $return_data['end_date'] = '';
        }else{
            $return_data['end_date'] = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(@$end-1)));
        }
        return $return_data;
    }
    /*     * *  Get patient ledger age data end here ** */

    /*     * *  Get patient ledger outstanding data starts here ** */

    public static function getOutstandingData($patient_id) {
        $outstandingdtl = [];
        $age_date = ["0-30", "31-60", "61-90", "91-120", "121-above"];

        //total_charge
        //Total Patient Due 
        $patient_due = ClaimInfoV1::where('claim_info_v1.patient_id', $patient_id)
                ->leftJoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->where('claim_info_v1.insurance_id',0)
                ->whereNull('claim_info_v1.deleted_at')
                ->sum(DB::raw('pmt_claim_fin_v1.total_charge-(pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.withheld + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj )'));
        // $total_ins_charge
        //Total Insurance Due
        $insurance_billed = ClaimInfoV1::where('claim_info_v1.patient_id', $patient_id)
                ->leftJoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->where('claim_info_v1.insurance_id','!=',0)
                ->whereNull('claim_info_v1.deleted_at')
                ->where('claim_info_v1.claim_submit_count','>' ,0);
        $insurance_due = $insurance_billed->sum(DB::raw('pmt_claim_fin_v1.total_charge-(pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.withheld + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj )'));
        $patient_paid = $insurance_billed->sum(DB::raw('pmt_claim_fin_v1.patient_paid'));
        $patient_adj = $insurance_billed->sum(DB::raw('pmt_claim_fin_v1.patient_adj'));
        //Total Balance for the patient
        $total_due = 0;

        foreach ($age_date as $key => $value) {
            $date_key = explode("-", $value);
            $patient_val = self::getageData($patient_id, $date_key[0], $date_key[1], "Patient");
            $insurance_val = self::getageData($patient_id, $date_key[0], $date_key[1], "Insurance");
            $values["Patient"][$key] = $patient_val;            
            $values["Insurance"][$key] = $insurance_val;
            $values["Outstanding"][$key] = $values["Patient"][$key] + $values["Insurance"][$key];
            $total_due = $total_due + $values["Outstanding"][$key];
        }

        foreach ($age_date as $key => $value) {
            if ($total_due <> 0) {
                $values['Percentage'][$key] = round(($values["Outstanding"][$key] / $total_due) * 100, 2);
            }
            else
            {
                $values['Percentage'][$key] = 0;
            }
        }

        $values["Patient"][] = array_sum($values["Patient"]);
        $values["Insurance"][] = array_sum($values["Insurance"]);
        $values["Outstanding"][] = array_sum($values["Outstanding"]);
        $totalPercentage = array_sum($values["Patient"]) + array_sum($values["Insurance"]) + array_sum($values["Outstanding"]);
        //MR-1563
        //We are not taking the total of Patient and Insurance because we are rounding off the value to 2 decimal
        //places, and it may end up as 99% or 99.5% which will not be right. So we either show 0% or 100% 
        if($totalPercentage == 0)
            $values["Percentage"][] = 0;
        else
            $values["Percentage"][] = 100;
        return $values;
    }

    /*     * *  Get patient ledger outstanding data end here ** */

    /*     * *  Get patient Aging starts here ** */

    public static function getAging($patient_id) {
        $outstandingdtl = [];
        $age_date = ["0-30", "31-60", "61-90", "91-120", "121-above"];
        foreach ($age_date as $key => $value) {
            $date_key = explode("-", $value);
            $values["Insurance"][$key] = self::getageData($patient_id, $date_key[0], $date_key[1], "Insurance");
            //$values["Insurance"][$key] = '';
            $values["Patient"][$key] = self::getageData($patient_id, $date_key[0], $date_key[1], "Patient");
        }
        $values["Patient"][] = $patient_age = array_sum($values["Patient"]);
        $values["Insurance"][] = $ins_age = array_sum($values["Insurance"]);
        $values["total_aging"] = $patient_age + $ins_age;
        return $values;
    }
	
	public static function getAgingForSTMT($patient_id) {
        $outstandingdtl = [];
        $age_date = ["0-30", "31-60", "61-90", "91-120", "121-above"];
        foreach ($age_date as $key => $value) {
            $date_key = explode("-", $value);
            $values["Insurance"][$key] = self::getageDataForSTMT($patient_id, $date_key[0], $date_key[1], "Insurance");
            //$values["Insurance"][$key] = '';
            $values["Patient"][$key] = self::getageDataForSTMT($patient_id, $date_key[0], $date_key[1], "Patient");
        }
        $values["Patient"][] = $patient_age = array_sum($values["Patient"]);
        $values["Insurance"][] = $ins_age = array_sum($values["Insurance"]);
        $values["total_aging"] = $patient_age + $ins_age;
        return $values;
    }


    /*     * *  Get patient Aging end here ** */

    /*     * *  Get patient name with surname starts here ** */

    public static function PatientWithSurname($patient_id) {
        $patient_data = Patient::where('id', $patient_id)->first();
        $nameformat = Helpers::getNameformat($patient_data->last_name, $patient_data->first_name, $patient_data->middle_name);
        $set_title = ($patient_data->title != '') ? $patient_data->title . ". " : '';
        $get_patient_name = $set_title . $nameformat;
        return $get_patient_name;
    }

    /*     * *  Get patient Aging end here ** */

    /* Patient lisitng AR  due */

    // @@ todo - As now not used in listing page. Check if not going to use it anymore then remove it.
    public static function getAllPatientARData() {
        $arData = [];
        /*
        $total_dues = ClaimInfoV1::whereNotIn('status', ['Hold']);
        $total_dues = $total_dues->select(DB::raw('sum(insurance_due) AS total_insurance_due, sum(patient_due) AS total_patient_due, sum(balance_amt) AS total_balance, patient_id'))->whereIn('status', ['Patient', 'Submitted'])
            ->groupBy('patient_id')->get();
        */

        $total_dues = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                        ->whereIn('status', ['Patient', 'Submitted'])
                        ->whereNotIn('status', ['Hold'])
                        ->select(DB::raw('pmt_claim_fin_v1.patient_id, sum(patient_paid) AS tpat_paid, '
                                    . ' sum(insurance_paid) AS tins_paid,  '
                                    . ' sum(pmt_claim_fin_v1.total_charge) AS tcharge_amt,  '
                                    . ' sum(patient_due) AS tpat_due,  '
                                    . ' sum(insurance_due) AS tins_due,  '
                                    . ' sum(patient_adj) AS tpat_adj,  '
                                    . ' sum(insurance_adj) AS tins_adj, '
                                    . ' sum(withheld) AS twithheld '))
                        ->groupBy('claim_info_v1.patient_id')->get();
        
        if (!empty($total_dues)) {
            foreach ($total_dues as $dues) {
                $total_ar = $dues->tcharge_amt - ($dues->tpat_paid + $dues->tins_paid + $dues->twithheld + $dues->tpat_adj + $dues->tins_adj);
                $arData[$dues->patient_id]['total_ar'] = Helpers::priceFormat($total_ar);
                $arData[$dues->patient_id]['insurance_due'] = Helpers::priceFormat($dues->tins_due);
                $arData[$dues->patient_id]['patient_due'] = Helpers::priceFormat($dues->tpat_due);
            }
        }
        return $arData;
    }

    public static function getPatienttabARData($patient_id) {
        /* $total_dues = Claims::where('patient_id', $patient_id)->whereNotIn('status', ['Hold']);
          $total_dues = $total_dues->select(DB::raw('sum(balance_amt) AS total_balance'))->first();
          $total_ar = $total_dues->total_balance;
         */ $total_ar = Patient::getPatientFinDetails($patient_id);
        $total_ar = $total_ar['total_ar'];
        $total_ar = ($total_ar == 0) ? Helpers::priceFormat(0) : $total_ar;
        return $total_ar;
    }

    public static function getPatientAR($patient_id) {
        /* $total_dues = Claims::where('patient_id', $patient_id)->whereNotIn('status', ['Hold']);
          $total_dues = $total_dues->select(DB::raw('sum(patient_due) AS patient_due'))->first();
          $patient_due = $total_dues->patient_due;
         */
        $patient_due = Patient::getPatientFinDetails($patient_id);
        $patient_due = $patient_due['total_patient_due'];
        $patient_due = ($patient_due == 0) ? Helpers::priceFormat(0) : $patient_due;
        return $patient_due;
    }

    public static function getInsuranceAR($patient_id) {
        /*
        $total_dues = ClaimInfoV1::where('patient_id', $patient_id)->whereNotIn('status', ['Hold']);
        $total_dues = $total_dues->select(DB::raw('sum(insurance_due) AS insurance_due'))->first();
        */
        $insurance_due = Patient::getPatientFinDetails($patient_id);
        $insurance_due = $insurance_due['total_insurance_due'];
        $insurance_due = ($insurance_due == 0) ? Helpers::priceFormat(0) : $insurance_due;
        return $insurance_due;
    }

    //  Get patient related data starts here
    public static function getPatienttabData($patient_id) {
        if (!is_numeric($patient_id))
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $patient_tab_data = [];
        $practice_timezone = Helpers::getPracticeTimeZone();
        $patient_data = Patient::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(last_statement_sent_date,"UTC","'.$practice_timezone.'") as last_statement_sent_date'))->where('id', $patient_id)->first();

        // New payment table start
        // $total_dues = ClaimInfoV1::with('pmtClaimFinAggr')
        //	->where('patient_id', $patient_id)->whereNotIn('status', ['Hold'])->get();
        /*
          $deliveries = Delivery::with(array('order' => function($query)
          {
          $query->where('orders.user_id', $customerID);
          $query->orderBy('orders.created_at', 'DESC');
          }))
         */
        // New payment table end		
        // @todo - Have to fetch payment details from new tables.
        //$total_dues = Claims::where('patient_id', $patient_id)->whereNotIn('status', ['Hold']);
        $total_dues = Patient::getPatientFinDetails($patient_id);
        //$total_dues = $total_dues->select(DB::raw('sum(insurance_due) AS total_insurance_due, sum(patient_due) AS total_patient_due, sum(balance_amt) AS total_balance'))->first();
        //	->select(DB::raw('sum(order_lines.quantity*order_lines.per_qty) AS total_sales'))

        $pmt = Patient::paymentclaimsum($patient_id);
        $patient_tab_data['insurance_due'] = $pmt['tins_due'];
        $patient_tab_data['patient_due'] = $pmt['tpat_due'];   
        $patient_tab_data['total_ar'] = $pmt['tcharge_amt']-($pmt['tpat_paid'] + $pmt['tins_paid'] + $pmt['twithheld'] +$pmt['tins_adj'] + $pmt['tpat_adj']);
        $currentdate = Helpers::timezone(date("m/d/y H:i:s"),'Y-m-d');
        $total_unbilled = Helpers::getPatientUnBilledAmount($patient_id);
        $total_billed = Helpers::getPatientBilledAmount($patient_id);
        $patient_tab_data['billed'] = $total_billed; //Claims::where('patient_id', $patient_id)->sum('total_charge');
        $patient_tab_data['unbilled'] = $total_unbilled; //Claims::has('paymentclaimtransaction', '<', 1)->where('patient_id', $patient_id)->whereIn('status', ['Ready'])->sum('total_charge');
        //$patient_tab_data['wallet_balance'] = PMTWalletV1::where('patient_id', $patient_id)->sum('amount');
        //$patient_tab_data['wallet_balance'] = ($patient_tab_data['wallet_balance'] < 0) ? 0 : $patient_tab_data['wallet_balance'];
        $patient_tab_data['patient_budget'] =  (PatientBudget::where('patient_id', $patient_id)->where('status','Active')->count()) ? 'Yes' : 'No';
        $patient_tab_data['ar_days'] = '120+';
        $patient_tab_data['last_appoinment'] = PatientAppointment::where('patient_id', $patient_id)->where('deleted_at', NULL)
                                                //->where('status', "Complete")                                                
                                                ->where('scheduled_on', "<=", $currentdate)->orderBy('scheduled_on', 'desc')->pluck('scheduled_on')->first();
        $patient_tab_data['last_appoinment'] = ($patient_tab_data['last_appoinment']) ? Helpers::dateFormat($patient_tab_data['last_appoinment']) : '-';
        $patient_tab_data['last_statement'] = (!empty($patient_data->last_statement_sent_date) && $patient_data->last_statement_sent_date != '0000-00-00') ? Helpers::dateFormat($patient_data->last_statement_sent_date) : "-";
        if (@$patient_data->is_self_pay == "Yes") {
            $eligibile = "NA";
        } elseif (@$patient_data->eligibility_verification == "Active") {
            $eligibile = "Yes";
        } else {
            $eligibile = "No";
        }
        $patient_tab_data['eligibility'] = $eligibile;
        //Mk work start
        $patient_tab_data['wallet_balance'] = PMTWalletV1::getPatientWalletData($patient_id);
        return $patient_tab_data;
    }
	
	public static function getPatienttabDataSTMT($patient_id) {
        if (!is_numeric($patient_id))
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $patient_tab_data = [];
        $practice_timezone = Helpers::getPracticeTimeZone();
        $patient_data = Patient::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(last_statement_sent_date,"UTC","'.$practice_timezone.'") as last_statement_sent_date'))->where('id', $patient_id)->first();

        // New payment table start
        // $total_dues = ClaimInfoV1::with('pmtClaimFinAggr')
        //	->where('patient_id', $patient_id)->whereNotIn('status', ['Hold'])->get();
        /*
          $deliveries = Delivery::with(array('order' => function($query)
          {
          $query->where('orders.user_id', $customerID);
          $query->orderBy('orders.created_at', 'DESC');
          }))
         */
        // New payment table end		
        // @todo - Have to fetch payment details from new tables.
        //$total_dues = Claims::where('patient_id', $patient_id)->whereNotIn('status', ['Hold']);
        $total_dues = Patient::getPatientFinDetails($patient_id);
        //$total_dues = $total_dues->select(DB::raw('sum(insurance_due) AS total_insurance_due, sum(patient_due) AS total_patient_due, sum(balance_amt) AS total_balance'))->first();
        //	->select(DB::raw('sum(order_lines.quantity*order_lines.per_qty) AS total_sales'))

        $pmt = Patient::paymentclaimsumSTMT($patient_id);
        $patient_tab_data['insurance_due'] = $pmt['tins_due'];
        $patient_tab_data['patient_due'] = $pmt['tpat_due'];   
        $patient_tab_data['total_ar'] = $pmt['tcharge_amt']-($pmt['tpat_paid'] + $pmt['tins_paid'] + $pmt['twithheld'] +$pmt['tins_adj'] + $pmt['tpat_adj']);
        $currentdate = Helpers::timezone(date("m/d/y H:i:s"),'Y-m-d');
        $total_unbilled = Helpers::getPatientUnBilledAmount($patient_id);
        $total_billed = Helpers::getPatientBilledAmount($patient_id);
        $patient_tab_data['billed'] = $total_billed; //Claims::where('patient_id', $patient_id)->sum('total_charge');
        $patient_tab_data['unbilled'] = $total_unbilled; //Claims::has('paymentclaimtransaction', '<', 1)->where('patient_id', $patient_id)->whereIn('status', ['Ready'])->sum('total_charge');
        //$patient_tab_data['wallet_balance'] = PMTWalletV1::where('patient_id', $patient_id)->sum('amount');
        //$patient_tab_data['wallet_balance'] = ($patient_tab_data['wallet_balance'] < 0) ? 0 : $patient_tab_data['wallet_balance'];
        $patient_tab_data['patient_budget'] = 'No'; // (PatientBudget::where('patient_id', $patient_id)->count()) ? 'Yes' : 'No';
        $patient_tab_data['ar_days'] = '120+';
        $patient_tab_data['last_appoinment'] = PatientAppointment::where('patient_id', $patient_id)->where('deleted_at', NULL)
                                                //->where('status', "Complete")                                                
                                                ->where('scheduled_on', "<=", $currentdate)->orderBy('scheduled_on', 'desc')->pluck('scheduled_on')->first();
        $patient_tab_data['last_appoinment'] = ($patient_tab_data['last_appoinment']) ? Helpers::dateFormat($patient_tab_data['last_appoinment']) : '-';
        $patient_tab_data['last_statement'] = (!empty($patient_data->last_statement_sent_date) && $patient_data->last_statement_sent_date != '0000-00-00') ? Helpers::dateFormat($patient_data->last_statement_sent_date) : "-";
        if (@$patient_data->is_self_pay == "Yes") {
            $eligibile = "NA";
        } elseif (@$patient_data->eligibility_verification == "Active") {
            $eligibile = "Yes";
        } else {
            $eligibile = "No";
        }
        $patient_tab_data['eligibility'] = $eligibile;
        //Mk work start
        $patient_tab_data['wallet_balance'] = PMTWalletV1::getPatientWalletData($patient_id);
        return $patient_tab_data;
    }

    //  Get patient related data starts ends   
    public static function getARPatientInsurance($patient_id) {
        $getpatientinsurances = PatientInsurance::with(array('insurance_details' => function($query) {
                        $query->select('id', 'insurance_name');
                    }))->where('patient_id', $patient_id)->select('id', 'insurance_id', 'category')->get();
        $insurance_lists = [];
        if (!empty($getpatientinsurances)) {
            foreach ($getpatientinsurances as $getins) {
                $insurance_lists[$getins->insurance_id] = @$getins->category . '-' . @$getins->insurance_details->insurance_name;
            }
        }
        return $insurance_lists;
    }

    public static function getARDenialNotes($notes_detail_content) {
        $notes_detail_content_arr = explode('^^^', $notes_detail_content);
        $result = array();
        $result['denial_date'] = $notes_detail_content_arr[0];
        $result['check_no'] = $notes_detail_content_arr[1];
        if ($notes_detail_content_arr[2] == 'self') {
            $result['denial_insurance'] = 'Self';
        } else {
            $result['denial_insurance'] = Insurance::where('id', $notes_detail_content_arr[2])->pluck('short_name')->first();
        }
        $result['reference'] = $notes_detail_content_arr[3];
        $denial_code_result = array();
        $denial_code_array = explode(',', $notes_detail_content_arr[4]);
        foreach ($denial_code_array as $denial_code_value) {
            $res_val = Code::where('id', $denial_code_value)->select(DB::raw("CONCAT(transactioncode_id,' ',description) AS transactioncode_desc"))->pluck('transactioncode_desc')->first();
            $denial_code_result[$denial_code_value] = $res_val;
        }
        $result['denial_code_result'] = $denial_code_result;
        return $result;
    }

    public static function getPopupPatientInsurance($patient_id) {
        //if(!is_numeric($patient_id))	
        // $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
        $getpatientinsurances = PatientInsurance::with('insurance_details')
                        ->where('patient_id', $patient_id)
                        ->whereIn('category', ['Primary', 'Secondary', 'Tertiary'])->orderBy('orderby_category', 'asc')->get();
        return $getpatientinsurances;
    }

    public static function getPatientname($patient_id) {
        $patient_name_det = Patient::where('id', $patient_id)->select('last_name', 'middle_name', 'first_name')->first();
        $last_name = (@$patient_name_det['last_name']) ? $patient_name_det['last_name'] : '';
        $middle_name = (@$patient_name_det['middle_name']) ? $patient_name_det['middle_name'] : '';
        $first_name = (@$patient_name_det['first_name']) ? $patient_name_det['first_name'] : '';
        $patient = Helpers::getNameformat(@$last_name, @$first_name, @$middle_name);
        return $patient;
    }

    public static function getPatientSearchname($patient_id) {
        $patient_name_det = Patient::where('id', $patient_id)->select('last_name', 'middle_name', 'first_name')->first();
        $last_name = (@$patient_name_det['last_name']) ? $patient_name_det['last_name'] : '';
       // $middle_name = (@$patient_name_det['middle_name']) ? $patient_name_det['middle_name'] : '';
        $first_name = (@$patient_name_det['first_name']) ? $patient_name_det['first_name'] : '';
        $patient = @$last_name." ".@$first_name.",";
        return $patient;
    }

    public static function patientSettings() {
        $patient_acc = Patient::orderBy('id', 'DESC')->pluck('account_no')->first();
        return $patient_acc;
    }

    /* Scheduler Quick patient ssn validation check unique validation */

    public static function total_ssn() {
        $patient_ssn = Patient::orderBy('id', 'DESC')->where('ssn', "!=", '')->pluck('dob', 'ssn')->all();
        return $patient_ssn;
    }

    /* Patient New table changes */
    /*
     * Common function for get patient balance.
     * Params: patient_id - integer
     * response: Patient_paid, insurance_paid, patient_due, insurance_due, patient_adj, insurance_adj, withheld - array
     * 
     */

    public static function getPatientFinDetails($patient_id = 0) {
        // Patient_paid, insurance_paid, patient_due, insurance_due, patient_adj, insurance_adj, withheld
        // have to check the values for status of claims etc...
        $pmtData = [];
        if ($patient_id > 0) {
            $pmt = Patient::paymentclaimsum($patient_id);

            if (!empty($pmt)) {
                //Patient Payment is any money that comes into the system, even if it is not posted to any claim
                //Revision 1: MR-2536 - 8 Aug 2019 - Kannan
                //Added pmt_type 'Credit Balance' to include cheque which are not posted to any claim
                $tpat_paid = Helpers::priceFormat(PMTInfoV1::where('patient_id',$patient_id)
                    ->whereIn('pmt_type',['Payment','Credit Balance'])
                    ->where('pmt_method','patient')
                    ->whereNull('void_check')
                    ->sum('pmt_amt'));
                $tins_paid = Helpers::priceFormat($pmt->tins_paid);
                $tcharge_amt = Helpers::priceFormat($pmt->tcharge_amt);
                $tpat_due = Helpers::priceFormat($pmt->tpat_due);
                $tins_due = Helpers::priceFormat($pmt->tins_due);
                $tpat_adj = Helpers::priceFormat($pmt->tpat_adj);
                $tins_adj = Helpers::priceFormat($pmt->tins_adj);
                $twithheld = Helpers::priceFormat($pmt->twithheld);
                $tot_ar = Helpers::priceFormat($pmt->tcharge_amt - ($pmt->tpat_paid + $pmt->tins_paid + $pmt->twithheld + $pmt->tpat_adj + $pmt->tins_adj));
            }
        }
        $pmtData['total_patient_paid'] = isset($tpat_paid) ? $tpat_paid : 0.0;
        $pmtData['total_insurance_paid'] = isset($tins_paid) ? $tins_paid : 0.0;
        $pmtData['total_charge_amt'] = isset($tcharge_amt) ? $tcharge_amt : 0.0;
        $pmtData['total_patient_due'] = isset($tpat_due) ? $tpat_due : 0.0;
        $pmtData['total_insurance_due'] = isset($tins_due) ? $tins_due : 0.0;
        $pmtData['total_patient_adj'] = isset($tpat_adj) ? $tpat_adj : 0.0;
        $pmtData['total_insurance_adj'] = isset($tins_adj) ? $tins_adj : 0.0;
        $pmtData['total_withheld'] = isset($twithheld) ? $twithheld : 0.0;
        $pmtData['total_ar'] = isset($tot_ar) ? $tot_ar : 0.0;
        return $pmtData;
    }
	
	public static function getPatientFinDetailsSTMT($patient_id = 0) {
        // Patient_paid, insurance_paid, patient_due, insurance_due, patient_adj, insurance_adj, withheld
        // have to check the values for status of claims etc...
        $pmtData = [];
        if ($patient_id > 0) {
            $pmt = Patient::paymentclaimsumSTMT($patient_id);

            if (!empty($pmt)) {
                //Patient Payment is any money that comes into the system, even if it is not posted to any claim
                //Revision 1: MR-2536 - 8 Aug 2019 - Kannan
                //Added pmt_type 'Credit Balance' to include cheque which are not posted to any claim
                $tpat_paid = Helpers::priceFormat(PMTInfoV1::where('patient_id',$patient_id)
                    ->whereIn('pmt_type',['Payment','Credit Balance'])
                    ->where('pmt_method','patient')
                    ->whereNull('void_check')
                    ->sum('pmt_amt'));
                $tins_paid = Helpers::priceFormat($pmt->tins_paid);
                $tcharge_amt = Helpers::priceFormat($pmt->tcharge_amt);
                $tpat_due = Helpers::priceFormat($pmt->tpat_due);
                $tins_due = Helpers::priceFormat($pmt->tins_due);
                $tpat_adj = Helpers::priceFormat($pmt->tpat_adj);
                $tins_adj = Helpers::priceFormat($pmt->tins_adj);
                $twithheld = Helpers::priceFormat($pmt->twithheld);
                $tot_ar = Helpers::priceFormat($pmt->tcharge_amt - ($pmt->tpat_paid + $pmt->tins_paid + $pmt->twithheld + $pmt->tpat_adj + $pmt->tins_adj));
            }
        }
        $pmtData['total_patient_paid'] = isset($tpat_paid) ? $tpat_paid : 0.0;
        $pmtData['total_insurance_paid'] = isset($tins_paid) ? $tins_paid : 0.0;
        $pmtData['total_charge_amt'] = isset($tcharge_amt) ? $tcharge_amt : 0.0;
        $pmtData['total_patient_due'] = isset($tpat_due) ? $tpat_due : 0.0;
        $pmtData['total_insurance_due'] = isset($tins_due) ? $tins_due : 0.0;
        $pmtData['total_patient_adj'] = isset($tpat_adj) ? $tpat_adj : 0.0;
        $pmtData['total_insurance_adj'] = isset($tins_adj) ? $tins_adj : 0.0;
        $pmtData['total_withheld'] = isset($twithheld) ? $twithheld : 0.0;
        $pmtData['total_ar'] = isset($tot_ar) ? $tot_ar : 0.0;
        return $pmtData;
    }

    public static function paymentclaimsum($patient_id ='') {
        $pmt_cal = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
        //$pmt_cal = PMTClaimFINV1::where('patient_id', $patient_id)
                    ->where('claim_info_v1.deleted_at', NULL)
                    ->where('claim_info_v1.patient_id', $patient_id)
                    ->groupBy('pmt_claim_fin_v1.patient_id')
                    ->orderBy('claim_info_v1.id')
                    ->select(DB::raw('sum(pmt_claim_fin_v1.patient_paid) AS tpat_paid, '
                                    . ' sum(pmt_claim_fin_v1.insurance_paid) AS tins_paid,  '
                                    . ' sum(pmt_claim_fin_v1.total_charge) AS tcharge_amt,  '
                                    . ' sum(pmt_claim_fin_v1.patient_due) AS tpat_due,  '
                                    . ' sum(pmt_claim_fin_v1.insurance_due) AS tins_due,  '
                                    . ' sum(pmt_claim_fin_v1.patient_adj) AS tpat_adj,  '
                                    . ' sum(pmt_claim_fin_v1.insurance_adj) AS tins_adj, '
                                    . ' sum(pmt_claim_fin_v1.withheld) AS twithheld '))
                    ->first();
        return $pmt_cal;
    }  
	
	public static function paymentclaimsumSTMT($patient_id ='') {
        $pmt_cal = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
        //$pmt_cal = PMTClaimFINV1::where('patient_id', $patient_id)
                    ->where('claim_info_v1.deleted_at', NULL)
                    ->where('claim_info_v1.patient_id', $patient_id)
					->where('claim_info_v1.status','!=','Hold')
                    ->groupBy('pmt_claim_fin_v1.patient_id')
                    ->orderBy('claim_info_v1.id')
                    ->select(DB::raw('sum(pmt_claim_fin_v1.patient_paid) AS tpat_paid, '
                                    . ' sum(pmt_claim_fin_v1.insurance_paid) AS tins_paid,  '
                                    . ' sum(pmt_claim_fin_v1.total_charge) AS tcharge_amt,  '
                                    . ' sum(pmt_claim_fin_v1.patient_due) AS tpat_due,  '
                                    . ' sum(pmt_claim_fin_v1.insurance_due) AS tins_due,  '
                                    . ' sum(pmt_claim_fin_v1.patient_adj) AS tpat_adj,  '
                                    . ' sum(pmt_claim_fin_v1.insurance_adj) AS tins_adj, '
                                    . ' sum(pmt_claim_fin_v1.withheld) AS twithheld '))
                    ->first();
        return $pmt_cal;
    }  

    
    /* For statement start */
    public static function paymentclaimARForStmt($patient_id ='', $claim_id=0) {
        $pmt_cal = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
        //$pmt_cal = PMTClaimFINV1::where('patient_id', $patient_id)
                    ->where('claim_info_v1.deleted_at', NULL)
                    ->where('claim_info_v1.status','!=','Hold');
        if($claim_id > 0) {
            $pmt_cal = $pmt_cal->where('claim_info_v1.id', $claim_id);    
        }            
        $pmt_cal = $pmt_cal->where('claim_info_v1.patient_id', $patient_id)
                    ->groupBy('pmt_claim_fin_v1.claim_id')
                    ->select(DB::raw('sum(pmt_claim_fin_v1.patient_paid) AS tpat_paid, '
                                    . ' sum(pmt_claim_fin_v1.insurance_paid) AS tins_paid,  '
                                    . ' sum(pmt_claim_fin_v1.total_charge) AS tcharge_amt,  '
                                    . ' sum(pmt_claim_fin_v1.patient_due) AS tpat_due,  '
                                    . ' sum(pmt_claim_fin_v1.insurance_due) AS tins_due,  '
                                    . ' sum(pmt_claim_fin_v1.patient_adj) AS tpat_adj,  '
                                    . ' sum(pmt_claim_fin_v1.insurance_adj) AS tins_adj, '
                                    . ' sum(pmt_claim_fin_v1.withheld) AS twithheld, '
                                    . ' claim_info_v1.insurance_id, '
                                    . ' claim_info_v1.status, '
                                    . ' claim_info_v1.id '
                                ))
                    ->get();
		$pat_bal = $ins_bal = $tot_ar = 0;
        $claim_id_collection = $total_ar_collection = '';
        foreach ($pmt_cal as $pmtKey => $pmt) {
			
            $resp = ($pmt->insurance_id > 0) ? $pmt->insurance_id : 0;            
            $tot_ar = ($pmt->tcharge_amt - ($pmt->tpat_paid + $pmt->tins_paid + $pmt->twithheld + $pmt->tpat_adj + $pmt->tins_adj));          
            if($resp > 0) {
                $ins_bal += $tot_ar;
            } else {
                $pat_bal += $tot_ar;
				if($tot_ar != 0){
					$claim_id_collection .= $pmt->id.',';
					$total_ar_collection .= Helpers::priceFormat($tot_ar).',';
				}
            } 
        }                    
        $data['claim_id_collection'] = rtrim($claim_id_collection, ',');
        $data['total_ar_collection'] = rtrim($total_ar_collection, ',');
        $data['total_ar'] = Helpers::priceFormat($tot_ar);
        $data['patient_balance'] = Helpers::priceFormat($pat_bal);
        $data['insurance_balance'] = Helpers::priceFormat($ins_bal);
        
        return $data;
    }
    /* For statement end */
	/*
		### Patient Dob & Snn unique validation in Quick Patient add option in scheduler page	
	*/                  
    public static function ssnloop()
    {
        $patient_snn = Patient::where('ssn','<>','')->pluck('ssn','dob')->all();       
        return json_encode($patient_snn);
    }

    public static function generatePatientAccNo($pat_id) {        
        // Append prefix practice
        $practice_name = trim(Practice::getPracticeName());        
        $practice_Arr = array_map('trim', explode(" ", $practice_name));
        if(COUNT($practice_Arr) > 2 ) {
            $practice_prefix = $practice_Arr[0][0].$practice_Arr[1][0].$practice_Arr[2][0];    
        } elseif(COUNT($practice_Arr) > 1 ) {
            $practice_prefix = $practice_Arr[0][0].substr($practice_Arr[1],0,2);
        } elseif(COUNT($practice_Arr) == 1) {
            $practice_prefix = substr($practice_Arr[0],0,2);
        } else {
            $practice_prefix = ''; // Default
        }
        $acc_no = strtoupper($practice_prefix).str_pad($pat_id, 5, '0', STR_PAD_LEFT);
        return $acc_no;
    }
	
	// To get single patient data
    public static function singlePatientData($patient_id) {
        if (!is_numeric($patient_id))
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $patient_data = [];
        $patient_data = Patient::where('id', $patient_id)->first();
        return $patient_data;
    }

}