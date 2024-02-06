<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Provider;
use App\Models\Facility;
use App\Models\Patients\PatientNote;
use App\Models\Patients\ProblemList;
use App\Models\Insurance as Insurance;
use DB;
use Datetime;
use Response;
use Carbon\Carbon;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientContact;
use App\Http\Helpers\Helpers;
use Lang;

class ClaimInfoV1 extends Model {

    use SoftDeletes;

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

    protected $table = "claim_info_v1";
    protected $fillable = ['claim_number', 'patient_id', 'template_id',
        'date_of_service', 'charge_add_type', 'icd_codes', 'primary_cpt_code',
        'rendering_provider_id', 'refering_provider_id',
        'billing_provider_id', 'facility_id', 'insurance_id','patient_insurance_id','pos_id',
        'self_pay', 'insurance_category', 'auth_no', 'copay_id',
        'doi', 'admit_date', 'discharge_date', 'anesthesia_id', 'total_charge',
        'hold_reason_id', 'status', 'no_of_issues', 'error_message',
        'claim_type', 'submited_date', 'last_submited_date', 'claim_submit_count',
        'pmt_count', 'claim_armanagement_status', 'is_send_paid_amount',
        'payer_claim_number', 'payment_hold_reason', 'claim_reference', 'created_by', 'updated_by','created_at','updated_at'];

    // protected static function boot() {
    //     parent::boot();
    //     static::deleting(function($claims) {
         // called BEFORE delete()
            //  $claims->dosdetails()->delete();
    //     });
    // }

    public static $rules = [
            // 'billing_provider_id' => 'required',
            // 'rendering_provider_id' => 'required',
            // 'icd1' => 'required',
            // 'facility_id' => 'required',
    ];
    public static $messages = [
            // 'billing_provider_id.required' => 'Select billing provider!',
            // 'rendering_provider_id.required' => 'Select rendering provider!',
            // 'icd1.required' => 'Enter Icd!',
            // 'facility_id.required' => 'Select facility',
    ];

    public function patient() {
        return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id')->with('insured_detail');
    }
    
    public function rend_providers() {
      return $this->belongsTo('App\Models\Provider', 'rendering_provider_id', 'id')->select('id', 'provider_name', 'npi', 'etin_type', 'etin_type_number', 'provider_degrees_id', 'provider_types_id', 'gender', 'short_name');
    } 

    public function rendering_provider() {
        return $this->belongsTo('App\Models\Provider', 'rendering_provider_id', 'id')->select('id', 'provider_name', 'npi', 'etin_type', 'etin_type_number', 'provider_degrees_id', 'provider_types_id', 'gender', 'short_name')->with(array('degrees', 'provider_types'));
    }

    public function refering_provider() {
        return $this->belongsTo('App\Models\Provider', 'refering_provider_id', 'id')->select('first_name', 'middle_name','last_name','provider_name', 'npi', 'id', 'provider_types_id', 'upin', 'statelicense', 'statelicense_2', 'specialitylicense', 'short_name', 'provider_degrees_id')->with('provider_types', 'degrees');
    }

    public function billing_provider() {
        return $this->belongsTo('App\Models\Provider', 'billing_provider_id', 'id')->select('id', 'provider_name', 'npi', 'etin_type', 'etin_type_number', 'city', 'state', 'zipcode5', 'zipcode4', 'address_1', 'taxanomy_id', 'taxanomy_id2', 'statelicense', 'statelicense_2', 'specialitylicense', 'provider_degrees_id', 'provider_types_id', 'short_name', 'first_name', 'last_name', 'middle_name', 'organization_name', 'phone')->with(array('taxanomy' => function($query) {
                        $query->select('id', 'code');
                    }, 'degrees', 'provider_types'));
    }

    public function facility_detail() {
        return $this->belongsTo('App\Models\Facility', 'facility_id', 'id')->select('id', 'facility_name', 'facility_npi', 'pos_id', 'speciality_id', 'short_name', 'clia_number')->with(array('facility_address' => function($query) {
                        $query->select('id', 'facilityid', 'address1', 'address2', 'city', 'state', 'pay_zip5', 'pay_zip4');
                    }, 'pos_details' => function($query) {
                        $query->select('id', 'code');
                    }, 'speciality_details'));
    }

    public function facility() {
        return $this->belongsTo('App\Models\Facility', 'facility_id', 'id');
    }
    public function pmt_info() {
        return $this->hasOne('App\Models\Payments\PMTClaimFINV1', 'claim_id', 'id');
    }
    public function insurance_details() {
        return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id')->select('id', 'insurancetype_id', 'insurance_name', 'short_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4', 'payerid', 'insurance_name')->with('insurancetype');
    }

    public function pos() {
        return $this->belongsTo('App\Models\Pos', 'pos_id', 'id');
    }

    public function claim_details() {
        return $this->hasOne('App\Models\Payments\ClaimAddDetailsV1', 'claim_id');
    }

    public function hold_option() {
        return $this->belongsTo('App\Models\Holdoption','hold_reason_id','id');
    }
    public function cpt_option() {
        return $this->hasMany('App\Models\Payments\ClaimCPTInfoV1','claim_id')->with('claimtxdescv1');
    }
    public function claimcpttxdescv1() {
        return $this->hasMany('App\Models\Payments\ClaimCPTTXDESCV1','claim_id');
    }
    public function claimtxdescv1() {
        return $this->hasMany('App\Models\Payments\ClaimTXDESCV1','claim_id');
    }
    public function dosdetails() {
        return $this->hasMany('App\Models\Payments\ClaimCPTInfoV1', 'claim_id')->with('cptdetails','claimCptFinDetails', 'claimCptPatientTxDetails', 'claimCptShadedDetails');
    }

    public function anesthesia_details() {
        return $this->belongsTo('App\Models\Payments\ClaimAnesthesiaV1', 'anesthesia_id', 'id');
    }

    public function paymentInfo() {
        return $this->hasOne('App\Models\Payments\PMTInfoV1', 'source_id', 'id')->with('checkDetails');
    }

    public function insurancePaymentTx() { // This is used only to check whether insurance payment has been done or not
        // return $this->hasMany('App\Models\Patients\PaymentClaimDetail','claim_id')->select('id', 'claim_id','insurance_id')->where('payment_type', 'Insurance')->where('insurance_id', '!=', 0);
        return $this->hasMany('App\Models\Payments\PMTClaimTXV1', 'claim_id')->select('id', 'claim_id', 'payer_insurance_id')
                        ->where('pmt_method', 'Insurance')->where('payer_insurance_id', '!=', 0);
    }

    public function pmtClaimFinData() {
        return $this->hasOne("App\Models\Payments\PMTClaimFINV1", 'claim_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function claim_notes_details() {
        return $this->hasMany('App\Models\Patients\PatientNote', 'claim_id', 'id')->where('status','Active')->with('user');
    }

    public function eligibility_list() {
        return $this->hasMany('App\Models\Patients\PatientEligibility', 'patients_id', 'patient_id')->with('insurance_details', 'get_eligibilityinfo');
    }

    public function dueClaimFin() {
        return $this->belongsTo('App\Models\Payments\PMTClaimFINV1', 'claim_id', 'id');
    }

    public function problem_list() {
        return $this->hasMany('App\Models\Patients\ProblemList', 'claim_id', 'id')->with('user', 'created_by');
    }

    public function payment_claimdetail() {
        return $this->hasMany('App\Models\Payments\PMTClaimTXV1', 'claim_id', 'id')->with(array('payment_info', 'insurance_detail'));
    }

    public function claimediinfo() {
        return $this->belongsTo('App\Models\Payments\ClaimEDIInfoV1', 'id', 'claim_id');
    }

    public function claim_unit_details() {
        return $this->belongsTo('App\Models\Payments\ClaimCPTInfoV1', 'id', 'claim_id');
    }

    public function cpttransactiondetails() {
        return $this->hasMany('App\Models\Payments\ClaimCPTInfoV1', 'claim_id')->with('claimCptFinDetails');
        //return $this->hasMany('App\Models\Patients\Claimdoscptdetail','claim_id')->with('cpttransaction');
    }
	
    public function documents(){
      return $this->hasMany('App\Models\Document', 'claim_number_data','claim_number')->with('document_followup','document_categories');
    }

    public function claim_sub_status() {
        return $this->belongsTo('App\Models\ClaimSubStatus', 'sub_status_id', 'id');
    }
    
    public function provider_details(){
      return $this->belongsTo('App\Models\Provider', 'billing_provider_id', 'id')->groupBy("id");
    }
	
	 /* Getting claim tx submitted and rejected info */
	 public function claim_txt_info(){
	   return $this->belongsTo('App\Models\Payments\ClaimTXDESCV1', 'id', 'claim_id');
	 }
    
	 /* 
	 * Follow up relation for claim
	 */  
  	public function followup_details(){
      return $this->belongsTo('App\Models\Patients\PatientNote', 'id', 'claim_id')->where("patient_notes_type","followup_notes");
    }
    
    
    public function pmtClaimARFinData() {
        $totalPaid = 'patient_paid+insurance_paid';
        $totalAdjustment = 'patient_adj+insurance_adj';
        $balance = 'total_charge - (' . $totalPaid . '+' . $totalAdjustment . '+withheld)';
        return $this->hasOne("App\Models\Payments\PMTClaimFINV1", 'claim_id')->select('id', 'total_charge', 'withheld', 'patient_due', 'insurance_due', 'patient_paid', DB::raw("sum(" . $totalPaid . ") as totalPaid"), DB::raw("sum(" . $totalAdjustment . ") as totalAdjustment"), DB::raw("sum(" . $balance . ") as balance"));
    }

    /** Ref
      public function provider_details(){
      return $this->belongsTo('App\Models\Provider', 'provider_id', 'id');
      }

      public function cptdetails()
      {
      return $this->belongsTo('App\Models\Medcubics\Cpt', 'cpt_codes', 'cpt_hcpcs');
      }

      public function patient_insurance()
      {
      return $this->belongsto('App\Models\Patients\PatientInsurance', 'id', 'insurance_id');
      }

      public function rendering_provider_claim(){
      return $this->belongsTo('App\Models\Provider', 'rendering_provider_id', 'id');
      }

      public function rendering_provider(){
      return $this->belongsTo('App\Models\Provider', 'rendering_provider_id', 'id')->select('id','provider_name','npi','etin_type', 'etin_type_number', 'provider_degrees_id', 'provider_types_id', 'gender', 'short_name')->with(array('degrees', 'provider_types'));
      }

      public function refering_provider()
      {
      return $this->belongsTo('App\Models\Provider', 'refering_provider_id', 'id')->select('provider_name', 'npi', 'id', 'provider_types_id', 'upin', 'statelicense','statelicense_2', 'specialitylicense', 'short_name', 'provider_degrees_id')->with('provider_types', 'degrees');
      }

      public function report_refering_provider($id)
      {
      return $this->belongsTo('App\Models\Provider', 'refering_provider_id', 'id')->select('provider_name', 'npi', 'id', 'provider_types_id', 'upin', 'statelicense','statelicense_2', 'specialitylicense', 'short_name');
      }

      public function billing_provider()
      {
      return $this->belongsTo('App\Models\Provider', 'billing_provider_id', 'id')->select('id','provider_name','npi', 'etin_type', 'etin_type_number',  'city', 'state', 'zipcode5', 'zipcode4', 'address_1','taxanomy_id', 'taxanomy_id2','statelicense','statelicense_2', 'specialitylicense', 'provider_degrees_id', 'provider_types_id','short_name','first_name','last_name','middle_name','organization_name', 'phone')->with(array('taxanomy'=>function($query){ $query->select('id','code');},'degrees', 'provider_types'));
      }
      public function billing_provider_detail()
      {
      return $this->belongsTo('App\Models\Provider', 'billing_provider_id', 'id');
      }

      public function facility_detail()
      {
      return $this->belongsTo('App\Models\Facility', 'facility_id', 'id')->select('id','facility_name', 'facility_npi', 'pos_id', 'speciality_id', 'short_name')->with(array('facility_address'=>function($query){ $query->select('id','facilityid','address1','address2','city','state','pay_zip5','pay_zip4');}, 'pos_details' => function($query) {$query->select('id', 'code');}, 'speciality_details'));
      }

      public function facility_user_details()
      {
      return $this->belongsTo('App\Models\Medcubics\Users','created_by','id');
      }

      public function pos_details()
      {
      return $this->belongsTo('App\Models\Pos', 'pos_id', 'id');
      }

      public function facility(){
      return $this->belongsTo('App\Models\Facility', 'facility_id','id')->with('claimInfo');
      }

      public function payment_claimdetail()
      {
      return $this->hasMany('App\Models\Patients\PaymentClaimDetail', 'claim_id', 'id')->with(array('payment', 'insurance_detail'));
      }

      public function insurance_details()
      {
      return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id')->select('id','insurancetype_id','insurance_name','short_name','address_1','address_2','city','state','zipcode5','zipcode4','payerid','insurance_name')->with('insurancetype');
      }

      public function employer_details()
      {
      return $this->belongsTo('App\Models\Patients\PatientContact', 'employer_id', 'id')->select('id','employer_name');
      }

      public function dosdetails()
      {
      return $this->hasMany('App\Models\Patients\Claimdoscptdetail','claim_id')->with('cptdetails')->where('cpt_code', '!=', 'Patient');
      }

      public function claim_notes_details()
      {
      return $this->hasMany('App\Models\Patients\PatientNote','claim_id','id')->with('user');
      }

      public function eligibility_list()
      {
      return $this->hasMany('App\Models\Patients\PatientEligibility','patients_id','patient_id')->with('insurance_details','get_eligibilityinfo');
      }

      public function problem_list()
      {
      return $this->hasMany('App\Models\Patients\ProblemList','claim_id','id')->with('user','created_by');
      }

      public function cpttransactiondetails()
      {
      return $this->hasMany('App\Models\Patients\Claimdoscptdetail','claim_id')->with('cpttransaction');
      }

      public function claim_details()
      {
      return $this->hasOne('App\Models\Patients\ClaimDetail','claim_id');
      }

      public function hold_option()
      {
      return $this->belongsTo('App\Models\Holdoption','hold_reason_id','id');
      }

      public function payment_transaction()
      {
      return $this->hasMany('App\Models\Patients\PaymentTransaction','claim_id');
      }

      public function paymentctpdetail()
      {
      return $this->hasMany('App\Models\Patients\Paymentcptdetail','claim_id')->with(array('payment_detail'=>function($query){ $query->select('id','payment_amt');}));
      }

      public function paymentclaimtransaction() // This is used only to check whether insurance payment has been done or not
      {
      return $this->hasMany('App\Models\Patients\PaymentClaimDetail','claim_id')->select('id', 'claim_id','insurance_id')->where('payment_type', 'Insurance')->where('insurance_id', '!=', 0);
      }

      public function provider()
      {
      return $this->belongsTo('App\Models\Provider','providers_id','id');
      }
     */
    /*
      public function pmtAggrs(){
      return $this->hasOne('App\Models\Payments\PMTClaimFINV1','claim_id', 'claim_id')
      ->select(DB::raw('sum(insurance_due) AS total_insurance_due, sum(patient_due) AS total_patient_due '));
      }

      public function pmtClaimFinTotalDetails() {
      return $this->hasOne('App\Models\Payments\PMTClaimFINV1','claim_id')
      ->select(DB::raw('sum(insurance_due) AS total_insurance_due, sum(patient_due) AS total_patient_due, '
      . '(sum(total_charge)-(sum(patient_paid)+sum(insurance_paid) + sum(withheld) + sum(patient_adj) + sum(insurance_adj) )) AS total_ar'));
      }
     */
    // Claimwise total_insurance_due, total_patient_due calculation
    public function pmtClaimFinAggr() {
        return $this->hasOne('App\Models\Payments\PMTClaimFINV1', 'claim_id')
                        ->select(DB::raw('sum(insurance_due) AS total_insurance_due, sum(patient_due) AS total_patient_due'))
                        ->groupBy('claim_id');
    }

    public static function getFirstResponsibility($claim_id) {
        // @todo - check and use new pmt flow
        // $insurance_id = TransmissionClaimDetails::where('claim_id',$claim_id)->orderBy('id','ASC')->pluck('insurance_id');
        $insurance_id = PMTClaimTXV1::where('claim_id', $claim_id)->orderBy('id', 'ASC')->pluck('claim_insurance_id')->first();
        if ($insurance_id != '' && $insurance_id != 0) {
            $first_responsibility_name = Insurance::where('id', $insurance_id)->pluck('insurance_name')->first();
        } else {
            $first_responsibility_name = 'Self';
        }
        return $first_responsibility_name;
    }

    public static function checkChargeActivity($claim_id) {
        $notes_count = PatientNote::where('claim_id', $claim_id)->where('status','Active')->count();
        if (!$notes_count) {
            $notes_count = ProblemList::where('claim_id', $claim_id)->count();
        }
        return $notes_count;
    }

    /* AR Days calculation for Dashboard page */

    public static function arDays($week = '', $patient_id = '') {

        /* Claim start Date  */
        if (empty($patient_id))
            $start_date = ClaimInfoV1::orderBy('created_at', 'Asc')->pluck('created_at')->first();
        else
            $start_date = ClaimInfoV1::where('patient_id', $patient_id)->orderBy('created_at', 'Asc')->pluck('created_at')->first();
        if ($start_date != '') {
            $start_date = date_format($start_date, "Y-m-d");

            $end_date = date("Y-m-d");

            $start_date = new DateTime($start_date);

            $end_date = new DateTime($end_date);
            $one_weeks = date('Y-m-d', strtotime('-7 days'));
 
            $end_one_week = new DateTime($one_weeks);

            $interval = $start_date->diff($end_date);

            //Day in between start to current date.
            $less_week = $start_date->diff($end_one_week);

            //current days
            $curr_date = date('Y-m-d', strtotime(Carbon::now()->subDay(7)));

            $days = $interval->days;

            //Date in between start date to current date minus 7 days
            $one_week = $less_week->days;

            //$receivables = Claims::where('created_at','>=',$start_date)->where('created_at','<=',$end_date)->sum('total_paid');
            if(!empty($patient_id)) {               
               $pmt = Patient::paymentclaimsum($patient_id);

            }else{                          
               $pmt = PMTClaimFINV1::select(DB::raw('sum(patient_paid) AS tpat_paid, '
                                  . ' sum(insurance_paid) AS tins_paid,  '
                                  . ' sum(total_charge) AS tcharge_amt,  '
                                  . ' sum(patient_due) AS tpat_due,  '
                                  . ' sum(insurance_due) AS tins_due,  '
                                  . ' sum(patient_adj) AS tpat_adj,  '
                                  . ' sum(insurance_adj) AS tins_adj, '
                                  . ' sum(withheld) AS twithheld '))
                  ->first();              
            }

             $gross_amt = $pmt['tcharge_amt'];   
             $ar_balance_amt = $pmt['tcharge_amt'] - ($pmt['tpat_paid'] + $pmt['tins_paid'] + $pmt['twithheld'] + $pmt['tins_adj'] + $pmt['tpat_adj']);
            //$ar_balance_amt = $patient_paid+$insurance_adj+$patient_adj+$withheld+$insurance_paid;
            //$ar_balance_amt = ClaimInfoV1::sum('balance_amt');
            $total_charges = ($days != 0) ? $gross_amt / $days : 0;
            $ar_days = ($total_charges != 0) ? $ar_balance_amt / $total_charges : 0;
            //ONE week before
            if ($week == 'week') {
                //$one_week_bef_gross_amt = ClaimInfoV1::where('created_at','<=',$curr_date)->sum('total_charge');
                $one_week_bef_gross_amt = PMTClaimFINV1::where('created_at', '<=', $curr_date)->sum('total_charge');
                //$one_week_bef_ar_balance_amt = ClaimInfoV1::where('created_at','<=',$curr_date)->sum('balance_amt');
                $one_week_bef_ar_balance_amt = PMTClaimFINV1::where('created_at', '<=', $curr_date)->sum('patient_paid', 'insurance_paid', 'withheld', 'patient_adj', 'insurance_adj');
                $before_week_totoal_charges = ($one_week != 0) ? $one_week_bef_gross_amt / $one_week : 0;
                $before_week_ar_days = ($before_week_totoal_charges != 0) ? @$one_week_bef_ar_balance_amt / @$before_week_totoal_charges : 0;
                $week_pre_days = ($ar_days != 0) ? ((($ar_days - $before_week_ar_days) / $ar_days) * 100) : 0;
                return round($week_pre_days, 2) . "%";
            }
            return round($ar_days, 0);
        } else {
            return "0";
        }
    }

    public static function getPayerIdbilledToInsurance($insurance_id) {
        if (!empty($insurance_id)) {
            $payer_id = Insurance::where('id', $insurance_id)->pluck('payerid')->first();
            return !empty($payer_id) ? "Electronic" : 'Paper';
        } elseif (!is_null($insurance_id)) {
            return "Patient";
        }
        return "- Nil -";
    }

    public static function getSearchCriteria($patient_id = null) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $rendering_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
        $referring_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')]);
        $billing_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
        $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
        $insurance = Insurance::pluck('insurance_name', 'id')->all();
        $claimnumber = !(empty($patient_id)) ? ClaimInfoV1::orderBy('claim_number', 'asc')->where('patient_id', $patient_id)->pluck('claim_number', 'claim_number')->all() : ClaimInfoV1::orderBy('claim_number', 'asc')->pluck('claim_number', 'claim_number')->all();
        $patients = Patient::select(DB::raw("CONCAT(last_name,' ',first_name, middle_name) AS patient_name"), 'id')->where('status', '=', 'Active')->pluck('patient_name', 'id')->all();
        return Response::json(compact('rendering_providers', 'referring_providers', 'billing_providers', 'facilities', 'insurance', 'patients', 'claimnumber'));
    }

    public static function countPatientPayment($patient_id) {
        $patient_count = PMTClaimFINV1::where('patient_id', $patient_id)->where('patient_paid', '<>', 0)->get()->count();
        return $patient_count;
    }

    public static function GetInsuranceName($insurance_id = null) {
        if ($insurance_id != '' && $insurance_id != 0) {
            $insurance = Insurance::where('id', $insurance_id)->select('short_name')->orderBy('id')->first();
            $insurance_name = (!empty($insurance)) ? $insurance->short_name : "";
        } else {
            $insurance_name = "Self";
        }
        return $insurance_name;
    }

    public static function GetUnbilledCharge($claim_id = null) {
        $claim_data = self::where('id', $claim_id)->where('status', 'Ready')->where('claim_submit_count', '=', 0)->value('total_charge');
        return @$claim_data;
    }

    public static function GetPatientName($patient_id) {
        $patient_name = Patient::getPatientname($patient_id);
        return $patient_name;
    }

    /* Ref functions
      // below function used for tabs to check whether paer id available for the selected insurance
      public static function getPayerIdbilledToInsurabce($insurance_id)
      {
      if(!empty($insurance_id)){
      $payer_id = Insurance::where('id', $insurance_id)->pluck('payerid');
      return !empty($payer_id)?"Electronic":'Paper';
      } elseif(!is_null($insurance_id)){
      return "Patient";
      }
      return "- Nil -";
      }

     

      public static function getFirstResponsibility($claim_id)
      {
      $insurance_id = TransmissionClaimDetails::where('claim_id',$claim_id)->orderBy('id','ASC')->pluck('insurance_id');
      if($insurance_id!='' && $insurance_id!=0){
      $first_responsibility_name = Insurance::where('id', $insurance_id)->pluck('insurance_name');
      } else {
      $first_responsibility_name = 'Self';
      }
      return $first_responsibility_name;
      }

      public static function GetPatientName($patient_id)
      {
      $patient_detail = Patient::where('id', $patient_id)->first();
      $patient_name = Helpers::getNameformat($patient_detail->last_name,$patient_detail->first_name,$patient_detail->middle_name);
      return $patient_name;
      }

      public static function GetProviderName($provider_id)
      {
      if(!empty($provider_id)){
      $provider = Provider::where('id', $provider_id)->first();
      return @$provider->short_name;
      } else {
      return "-";
      }
      }

      public static function GetFacilityName($facility_id)
      {
      $facility = Facility::where('id', $facility_id)->first();
      return @$facility->facility_name;
      }
     */
	 
	  public static function getBalance($patient_id,$option='')
      {
      $patient_due      = PMTClaimFINV1::select(DB::raw('sum(patient_due) as patient_due'))->where('patient_id',$patient_id)->first();
		$get_balance = 0; 
      if($option == 'patient_balance')
      {
      $patient_balance = PMTClaimFINV1::select(DB::raw('sum(patient_due) as patient_due'))->where('patient_id',$patient_id)->first();
      $get_balance = $get_balance + $patient_due->patient_due;
      }

      if($option == 'insurance_balance')
      {
      $insurance_balance = PMTClaimFINV1::select(DB::raw('sum(patient_due) as patient_due'))->where('patient_id',$patient_id)->first();
      $get_balance = $get_balance - $patient_due->patient_due;
      }
      return $get_balance;
      }

    public static function getClaimTxnsByPatID($patient_id = 0) {
        $txnList = [];
        $claims_list = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                        ->where("claim_info_v1.patient_id", $patient_id)
                        ->select('claim_info_v1.id', 'date_of_service', 'claim_info_v1.created_at')->get();
        if (COUNT($claims_list) > 0) {
            foreach ($claims_list as $claim_det) {
                //dd($claim_det);
                $claimId = $claim_det->id;
                $txnList[$claimId]['claim_id'] = $claimId;
                $txnList[$claimId]['date_of_service'] = $claim_det['date_of_service'];
                $txnList[$claimId]['created_at'] = $claim_det['created_at'];
                $txnList[$claimId]['txn_list'] = SELF::getClaimTxnList($claimId);
            }
        }
        return $txnList;
    }

    public static function getClaimTxnList($claimId = 0, $sortBy='ASC') {
       $practice_timezone = Helpers::getPracticeTimeZone();  
        try {
            $claimFinDetails = PmtClaimFinV1::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->where('claim_id', $claimId)->first();
            if (!empty($claimFinDetails)) {
                $claim['total_charge'] = $claimFinDetails->total_charge;
                $claim['total_payments'] = $claimFinDetails->patient_paid + $claimFinDetails->insurance_paid;
                $claim['total_adjustment'] = $claimFinDetails->patient_adj + $claimFinDetails->insurance_adj;
                $claim['patient_balance'] = '';
                $claim['insurance_balance'] = '';
            }
            $descArr = [];            
            $sortBy = ($sortBy != '') ? $sortBy : 'ASC';  
            $txnDesc = ClaimTXDESCV1::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('claim_info', 'claim_txn', 'insurance_details')
                            ->where('claim_id', $claimId)->orderBy('id', $sortBy)->get();
            
            if (COUNT($txnDesc) > 0) {
                $pat_bal = $ins_bal = $tpat_paid = $tins_paid = $tpat_refund = $tins_refund = $tpat_adj = $tins_adj = 0;
                foreach ($txnDesc as $txnKey => $txnVal) {
                    $desc = $pmt_type = '';
                    $charges = $pmts = $adj = $responsibility = 0;

                    $txnArr['txn_date'] = Helpers::dateFormat($txnVal['created_at'], 'date');
                    $txnDet = isset($txnVal['claim_txn']) ? $txnVal['claim_txn'] : [];
                    $txnId = $txnVal['txn_id'];
                    $claimDetails = $txnVal['claim_info'];
                    $pat_bal = $txnVal['pat_bal'];
                    $ins_bal = $txnVal['ins_bal'];
                    $respCat = $cat_bg_class = "";
                    $respCat = ($txnVal['transaction_type'] != 'Responsibility' && isset($txnDet->ins_category) && !is_numeric($txnDet->ins_category) ) ? @$txnDet->ins_category : "";

                    if($txnVal['transaction_type'] == 'Insurance Payment' && $respCat == '') {
                      $respCat = 'Others';
                    }

                    if($respCat =='Primary')
                        $cat_bg_class = "pri-bg";
                    elseif($respCat =='Secondary')
                        $cat_bg_class = "sec-bg";
                    elseif($respCat =='Tertiary')
                        $cat_bg_class = "ter-bg";
                    else
                        $cat_bg_class = "pri-bg";

                    // Current responsibility
                    $responsibility = (!empty($txnVal['insurance_details'])) ? @$txnVal['insurance_details']->short_name : 'Patient'; // have to get the resp name
                    switch ($txnVal['transaction_type']) {
                        case 'New Charge':                              // Claim Created
                            $desc = Lang::get('payments/claim_transaction.claim_txn.charge_created_desc');
                            if(trim(@$txnVal['claim_info']->claim_reference) != '') {
                              $desc .= "\n Ref: ".trim($txnVal['claim_info']->claim_reference);
                            }  
                            $charges = @$claimDetails->total_charge;
              				// Need to take from claim desc value json string if available 
              				if($txnVal['value_1'] != '') {
                              	$descDet = json_decode($txnVal['value_1'], true);
                              	if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                	if(!empty($descDet) && isset($descDet['charge_amt']) && trim($descDet['charge_amt']) != '') {
                                  	$charges = trim($descDet['charge_amt']);
                                	}
                            	}
              				}
                            if ($responsibility == '')
                                $responsibility = 'Patient';
                            $total_charge = ($ins_bal + $pat_bal) - $adj;
                            break;

                        case 'Responsibility':
                            //$charges = @$claimDetails->total_charge;
                            $desc = Lang::get("payments/claim_transaction.claim_txn.transfer_to_ins_desc");
                            //echo "<br>Resp::".$responsibility;
                            $responsibility = ($responsibility == '') ? 'Patient' : $responsibility;
                            $desc = str_replace("VAR_INS_NAME", $responsibility, $desc);

                            // Set old responsiblity 
                            $old_resp = ($txnVal['value_1'] != 0) ? self::GetInsuranceName($txnVal['value_1']) : 'Patient';
                            $responsibility = ($old_resp == "Self" || $old_resp == '' ) ? 'Patient': $old_resp;
                            $pmts = $charges = '';
                            break;

                        case 'Patient Payment':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.pat_pmt_paid_desc");
                            $patient_id = @$txnVal['claim_txn']->patient_id;
                            $claimDetails = $txnVal['claim_info'];
                            $total_charge = $claimDetails->total_charge;
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);

                            $pat_name = 'Patient';
                            $desc = str_replace("VAR_PAT_NAME", $pat_name, $desc);
                            $pmt_type = @$pmtInfo->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt_type = "Chk No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                                $pmt_type = "MO No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt_type = "EFT No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt_type = (@$pmtInfo->card_last_4 != '') ? "Credit Card No. " . @$pmtInfo->card_last_4 : "Credit Card";
                            }
                            $pmts = @$txnDet->total_paid; //@$pmtInfo->pmt_amt;   
                            if(trim(@$pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            break;

                        case 'Insurance Payment':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_paid_desc");
                            $resp = ($txnVal['value_1'] != 0 && $txnVal['value_1'] != "") ? self::GetInsuranceName($txnVal['value_1']) : $responsibility;
                            $desc = str_replace("VAR_INS_NAME", $resp, $desc);
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            $pmt_type = @$pmtInfo->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt_type = "Chk No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt_type = "EFT No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt_type = (@$pmtInfo->card_last_4 != '') ? "Credit Card No. " . @$pmtInfo->card_last_4 : "Credit Card";
                            }
                            $pmts = @$txnDet->total_paid; // @$pmtInfo->pmt_amt; 
                            $total_charge = ($ins_bal + $pat_bal) - $adj;

                            if (@$txnDet->total_deduction != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_ded_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_deduction, $resp);
                            }
                            if (@$txnDet->total_coins != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_coins_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_coins, $resp);
                            }
                            if (@$txnDet->total_copay != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_coppay_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_copay, $resp);
                            }
							
							
							//if (@$txnDet->total_withheld != 0 || 1) { // adjustment not consider total withheld
								// Other adjustment details shown instead of withheld.
                                $adj_resp = '';
                                $adjs = SELF::getClaimOtherAdjDetails($claimId, @$txnDet->id);
                                if(!empty($adjs)) {
                                  foreach($adjs as $adjRec) {
                                    $adj_resp .="\n" .$adjRec['adj_code'].": ".Helpers::priceFormat($adjRec['adj_amt']);
                                  }
                                }
                                $desc .=$adj_resp;
                                /*
                                $resp = Lang::get("payments/claim_transaction.claim_txn.pmt_withheld_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_withheld, $resp);
                                */
                            //}
							
                            // if adjustment applied it needs to append
                            
							if (@$txnDet->total_writeoff != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_adj_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_writeoff, $resp);
                            } 
							
                            if($txnVal['value_2'] != '') {
                                $denial_desc = Lang::get("payments/claim_transaction.cpt_txn.denial_code_desc");
                                $denail_codes = implode(':', array_filter(array_unique(explode(',', $txnVal['value_2']))));
                                $desc .= "\n" . str_replace("VAR_CODES", rtrim($denail_codes, ':'), $denial_desc);
                            }
                            if(trim(@$pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim(@$pmtInfo->reference);
                            }
                            // If withheld or writeoff provided then so it in adjustment column
                            if(@$txnDet->total_withheld != 0 || @$txnDet->total_writeoff != 0) {
                                $adj = $txnDet->total_withheld + $txnDet->total_writeoff;  
                            }
                            break;

                        case 'Denials':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.denial_txn_desc");
                            if(isset($txnDet->total_paid))
                              $pmts = @$txnDet->total_paid; // @$pmtInfo->pmt_amt; 
                            break;

                        case 'Insurance Refund':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.refund_txn_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            $pmt_type = @$pmtInfo->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt_type = "Chk No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt_type = "EFT No. " . @$pmtInfo->check_no;
                            }
                            if(trim($pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            $pmts = $txnDet->total_paid; // @$pmtInfo->pmt_amt; 
                            break;

                        case 'Patient Refund':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.refund_txn_desc");
                            $desc = str_replace("VAR_SHORT_NAME", 'Patient', $desc);
                            //$pat_bal = 0;       // Existing balane + refund amount
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            $pmt_type = @$pmtInfo->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt_type = "Chk No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt_type = "EFT No. " . @$pmtInfo->check_no;
                            }
                            if(trim($pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            $pmts = $txnDet->total_paid; // @$pmtInfo->pmt_amt; 
                            break;

                        case 'Insurance Adjustment':
                            /*$pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);//PMTInfoV1::getPaymentInfoById($claimId, @$txnVal['payment_id']);
                            $reason = (@$pmtInfo->pmtadjustment_details['adjustment_shortname'] != '' ) ?@$pmtInfo->pmtadjustment_details['adjustment_shortname'] : @$pmtInfo->pmtadjustment_details['adjustment_reason'];
                            $desc = Lang::get("payments/claim_transaction.claim_txn.ins_adj_txn_desc");
                            $desc = str_replace("VAR_REASON", $reason, $desc);
                            $adj = @$txnDet->total_withheld+@$txnDet->total_writeoff;
                            if(isset($pmtInfo->reference) && trim($pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            break;*/
                            $desc = Lang::get("payments/claim_transaction.claim_txn.ins_adj_txn_desc");
                            $resp = ($txnVal['value_1'] > 0 && $txnVal['value_1'] != "") ? self::GetInsuranceName($txnVal['value_1']) : $responsibility;
                            $desc = str_replace("VAR_REASON", $resp, $desc);
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);// echo "<pre>"; print_r($pmtInfo);
                            $pmt_type = @$pmtInfo->pmt_mode; //echo "<br>".$pmt_type;
                            if ($pmt_type == 'Check') {
                                $pmt_type = "Chk No. " . (isset($pmtInfo->check_no) ? $pmtInfo->check_no : 0);
                            } elseif ($pmt_type == 'EFT') {
                                $pmt_type = "EFT No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt_type = (@$pmtInfo->card_last_4 != '') ? "Credit Card No. " . @$pmtInfo->card_last_4 : "Credit Card";
                            }
                            $pmts = @$txnDet->total_paid; // @$pmtInfo->pmt_amt; 
                            $total_charge = ($ins_bal + $pat_bal) - $adj;
                            if (@$txnDet->total_deduction != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_ded_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_deduction, $resp);
                            }
                            if (@$txnDet->total_coins != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_coins_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_coins, $resp);
                            }
                            if (@$txnDet->total_copay != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_coppay_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_copay, $resp);
                            }
                           	// if (@$txnDet->total_withheld != 0) {
                			// Other adjustment details shown instead of withheld.
                                $adj_resp = '';
                                $adjs = SELF::getClaimOtherAdjDetails($claimId, @$txnDet->id);
                                if(!empty($adjs)) {
                                  foreach($adjs as $adjRec) {
                                    $adj_resp .="\n" .$adjRec['adj_code'].": ".$adjRec['adj_amt'];
                                  }
                                }
                                $desc .=$adj_resp;
                                /*
                                $resp = Lang::get("payments/claim_transaction.claim_txn.pmt_withheld_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_withheld, $resp);
                                */
                           // }
                            // if adjustment applied it needs to append
                            if (@$txnDet->total_writeoff != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_adj_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_writeoff, $resp);
                            }

                            if($txnVal['value_2'] != '') {
                                $denial_desc = Lang::get("payments/claim_transaction.cpt_txn.denial_code_desc");
                                $denail_codes = implode(':', array_filter(array_unique(explode(',', $txnVal['value_2']))));
                                $desc .= "\n" . str_replace("VAR_CODES", rtrim($denail_codes, ':'), $denial_desc);
                            }
                            if(trim(@$pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim(@$pmtInfo->reference);
                            }
                            // If withheld or writeoff provided then so it in adjustment column
                            if(@$txnDet->total_withheld != 0 || @$txnDet->total_writeoff != 0) {
                                $adj = $txnDet->total_withheld + $txnDet->total_writeoff;  
                            }
                            break;

                        case 'Patient Adjustment':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.pat_adj_txn_desc"); // pat_adj_txn
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            $reason = (@$pmtInfo->pmtadjustment_details['adjustment_shortname'] != '' ) ?@$pmtInfo->pmtadjustment_details['adjustment_shortname'] : @$pmtInfo->pmtadjustment_details['adjustment_reason'];
                            $desc = str_replace("VAR_REASON", $reason, $desc);
                            $adj = @$txnDet->total_withheld+@$txnDet->total_writeoff;
                            if(isset($pmtInfo->reference) && trim($pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            break;

                        case 'Patient Credit Balance':
                        case 'Credit Balance':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.pat_cr_bal_txn_desc");
                            // Handle credit balance
                            if($txnVal['value_1'] != "" ){
                              $descDet = json_decode($txnVal['value_1'], true);
                              if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                foreach ($descDet as $key => $detVal) {
                                  $pmtInfo = PMTInfoV1::with('checkDetails','creditCardDetails','eftDetails')
                                             // ->where('source_id', $claimId)
                                              ->where('id', @$detVal['pmt_info_id'])->first();
                                  $desc = str_replace("VAR_AMOUNT", $detVal['amountApplied'], $desc);
                                  if(!empty($pmtInfo)) {          
                                    if($pmtInfo->pmt_mode == 'Check'){
                                      $desc .= "\n " .$detVal['amountApplied']." from CHK# ".@$pmtInfo->checkDetails['check_no'];  
                                    } elseif($pmtInfo->pmt_mode == 'EFT'){
                                      $desc .= "\n " .$detVal['amountApplied']." from EFT# ".@$pmtInfo->eftDetails['eft_no'];  
                                    } elseif($pmtInfo->pmt_mode == 'Credit'){
                                      $desc .= "\n " .$detVal['amountApplied']." from CREDIT# ".@$pmtInfo->creditCardDetails['card_last_4']; 
                                    } else {
                                      $desc .= "\n " .$detVal['amountApplied']." from CASH"; 
                                    }  
                                    if(trim($pmtInfo->reference) != '') {
                                      $desc .= "\n Ref: ".trim($pmtInfo->reference);
                                    }
                                  }
                                }
                              }
                            }
                            // $responsibility = $ins_name = 'Patient';
                            $pmts = @$txnDet->total_paid;
                            // handle value2 txn ids and make it sum
                            if(trim($txnVal['value_2']) != "" ){
                              $txnIds = array_filter(array_unique(explode(',', $txnVal['value_2'])));
                              $pmts = 0;
                              foreach ($txnIds as $key => $txId) {
                                $txnAmt = PMTInfoV1::getClaimTxAmtById($txId);
                                $pmts +=$txnAmt; 
                              }
                            }
                            break;

                        // No caluclation required
                        case 'Edit Charge':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.charge_updated_desc");
                            // Need to take charge amount from claim desc value json string if available 
                            if($txnVal['value_1'] != '') {
                				$descDet = json_decode($txnVal['value_1'], true);
                              	if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                	if(!empty($descDet) && isset($descDet['charge_amt']) && trim($descDet['charge_amt']) != '') {
                                  		$charges = trim($descDet['charge_amt']);
                                	}
                              	}
              				}
                            break;

                        case 'Submitted': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.submitted_edi_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Submitted Paper': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.submitted_paper_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Resubmitted': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.resubmitted_edi_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Resubmitted Paper': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.resubmitted_paper_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;      

                        case 'Payer Rejected':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_payer_rej_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Payer Accepted':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_payer_acc_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Clearing House Rejection':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_rej_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Clearing House Accepted': // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_acc_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Wallet':
                            $pmts = number_format($txnVal['value_1'], 2);
                            $desc = Lang::get("payments/claim_transaction.claim_txn.excess_wallet_transfer_desc");
                            $pmtsVal = ($pmts < 0 ) ? -1 * $pmts : $pmts;
                            $desc = str_replace("VAR_AMOUNT", $pmtsVal, $desc);
                            $pmts = -1*$pmts;
                            break;

                        case 'Void Check':
                            if($txnVal['value_2'] != '' && $txnVal['value_2'] == 1)
                              $desc = Lang::get("payments/claim_transaction.claim_txn.void_ins_check_desc");
                            else
                              $desc = Lang::get("payments/claim_transaction.claim_txn.void_check_desc");
                            $pmts = @$txnDet->total_paid;
                            $desc = str_replace("VAR_TXN_AMOUNT", $pmts, $desc);

                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            $pmt_type = @$pmtInfo->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt_type = "Chk No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt_type = "EFT No. " . @$pmtInfo->check_no;
                            }
                            if(trim($pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            break;
                            
                        default:
                            $desc = $txnVal['transaction_type']; // Handle non handled types
                            break;
                    }
               
                    $txnArr['txn_details'] = $txnVal;
                    $txnArr['desc_id'] = $txnVal['id'];
                    $txnArr['txn_id'] = $txnId;
                    $txnArr['description'] = $desc;
                    $txnArr['payment_type'] = $pmt_type;
                    $txnArr['charges'] = ($charges != 0) ? Helpers::priceFormat($charges) : "";
                    $txnArr['payments'] = Helpers::priceFormat($pmts);
                    $txnArr['adjustment'] = Helpers::priceFormat($adj);
                    $txnArr['pat_balance'] = Helpers::priceFormat($pat_bal);
                    $txnArr['ins_balance'] = Helpers::priceFormat($ins_bal);
                    $txnArr['resp_category'] = @$respCat;
                    $txnArr['resp_cat_class'] = @$cat_bg_class; // For apply bg color
                    $txnArr['responsibility'] = $responsibility;
                    $txnArr['cpt_transaction'] = SELF::getClaimCptTxnList($claimId, '', $txnVal['id']);
                    $descArr[] = $txnArr;
                }
            }

            return $descArr;
        } catch (Exception $e) {
            $respMsg = "getClaimTxnDesc | Error Msg: " . $e->getMessage() . ". | Occured on Line# " . $e->getLine();
            $respMsg .= "Trace |" . $e->getTraceAsString();
            \Log::info(' Exception Occurred: >>' . $respMsg);
        }
    }

    public static function getClaimCptTxnList($claimId, $cptId = '', $cptTxId = 0, $sortBy='ASC') {
        // populate the claim cpt txn list by claim id
        // List: CPT, Txn date, Resp, Desc, Charges, Pmts, Adj, Pat.Bal., Ins.Bal
        // Eg: Claim denied Denial/Remark Codes - PR21, | 
        $practice_timezone = Helpers::getPracticeTimeZone();
        $descArr = [];
        try {
            $cptTxnDesc = ClaimCPTTXDESCV1::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('claimcpt_info', 'claimcpt_txn', 'insurance_details')
                    ->where('claim_id', $claimId);
            if ($cptId != '')
                $cptTxnDesc->where('claim_cpt_info_id', $cptId);
            if ($cptTxId > 0)
                $cptTxnDesc->where('claim_tx_desc_id', $cptTxId);
            $sortBy = ($sortBy != '') ? $sortBy : 'ASC';  
            //$cptTxnDesc = $cptTxnDesc->orderBy('claim_tx_desc_id', $sortBy)->get();
            $cptTxnDesc = $cptTxnDesc->orderBy('claim_cpt_info_id', $sortBy)->orderBy('claim_tx_desc_id', $sortBy)->get();

            if (!empty($cptTxnDesc)) {
                $pat_bal = $ins_bal = 0;
                foreach ($cptTxnDesc as $txnKey => $txnVal) {
                    $txDescId = $txnVal['claim_tx_desc_id'];
                    $txnArr['txn_date'] = Helpers::dateFormat($txnVal['created_at'], 'date');
                    $txnId = $txnVal['txn_id'];
                    $cptId = $txnVal['claim_cpt_info_id'];
                    $claimCptInfo = $txnVal['claimcpt_info'];
                    $cptTxnDet = isset($txnVal['claimcpt_txn']) ? $txnVal['claimcpt_txn'] : [];
                    $pat_bal = $txnVal['pat_bal'];
                    $ins_bal = $txnVal['ins_bal'];
                    $respCat = ($txnVal['transaction_type'] != 'Responsibility' && isset($cptTxnDet->claimtxdetails->ins_category) && !is_numeric($cptTxnDet->claimtxdetails->ins_category)) ? @$cptTxnDet->claimtxdetails->ins_category : "";
                    
                    if($txnVal['transaction_type'] == 'Insurance Payment' && $respCat == '') {
                      $respCat = 'Others';
                    }

                    if($respCat =='Primary')
                        $resp_bg_class = "pri-bg";
                    elseif($respCat =='Secondary')
                        $resp_bg_class = "sec-bg";
                    elseif($respCat =='Tertiary')
                        $resp_bg_class = "ter-bg";
                    else
                        $resp_bg_class = "pri-bg";

                    $responsibility = $desc = $charges = '';
                    $pmts = $adj = 0;
                    $responsibility = (!empty(@$txnVal['insurance_details'])) ? @$txnVal['insurance_details']->short_name : "Patient";
                   
                    switch ($txnVal['transaction_type']) {
                        case 'New Charge':
                            // New line item added
                            //  - | - | 1/4/2018 | AKT | Charge created | 100 | 0 | 0 | 0 | 100
                            $desc = Lang::get('payments/claim_transaction.cpt_txn.charge_created_desc');
                            $charges = @$claimCptInfo->charge;
              				// Need to take charge amount from claim cpt desc value json string if available, otherwise take from cpt_info 
              				if($txnVal['value_1'] != '') {
                              $descDet = json_decode($txnVal['value_1'], true);
                              if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                if(!empty($descDet) && isset($descDet['charge_amt']) && trim($descDet['charge_amt']) != '') {
                                  $charges = trim($descDet['charge_amt']);
                                }
                              }
              				}
                            if ($responsibility == '')
                                $responsibility = 'Patient';
                            $total_charge = ($ins_bal + $pat_bal) - $adj;
                            break;

                        case 'Responsibility':
                            // Responsibility modified for CPT
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.transfer_to_ins_desc");
                            $responsibility = ($responsibility == '') ? 'Patient' : $responsibility;
                            $desc = str_replace("VAR_INS_NAME", $responsibility, $desc);

                            // Set old responsiblity 
                            $old_resp = ($txnVal['value_1'] > 0) ? self::GetInsuranceName($txnVal['value_1']) : 'Patient';
                            $responsibility = ($old_resp == "Self" || $old_resp == '' ) ? 'Patient': $old_resp;
                            $pmts = $charges = '';
                            break;

                        case 'Patient Payment':
                            // Patient Payment posted
                            //  - | - | 1/4/2018 | Patient | Pmt : Patient | - | 9 | 0 | 50 | 0
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.pat_pmt_paid_desc");
                            $patient_id = @$txnVal['claim_txn']->patient_id;
                            $pat_name = 'Patient';
                            $desc = str_replace("VAR_PAT_NAME", $pat_name, $desc);
                            $pmts = @$cptTxnDet->paid;
                            break;

                        case 'Insurance Payment':
                            // Insurance payment posted
                            // - | - | 1/4/2018 | AAR | Pmt : AAR | - | 0 | 0 | 0 | 56 
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_paid_desc");
                            $resp = ($txnVal['value_1'] > 0 && $txnVal['value_1'] != "") ? self::GetInsuranceName($txnVal['value_1']) : $responsibility;
                            $desc = str_replace("VAR_INS_NAME", $resp, $desc);
                            //$pmtInfo = PMTInfoV1::getPaymentInfoById($claimId, @$txnVal['payment_id']);
                            $pmts = @$cptTxnDet->paid;
                            $total_charge = ($ins_bal + $pat_bal) - $adj;

                            if (@$cptTxnDet->deduction != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_ded_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->deduction, $resp);
                            }
                            if (@$cptTxnDet->coins != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coins_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->coins, $resp);
                            }
                            if (@$cptTxnDet->copay != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coppay_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->copay, $resp);
                            }
                            //if (@$cptTxnDet->withheld != 0) {
								// Other adjustment details shown instead of withheld.
                                $adjs = SELF::getClaimOtherAdjDetails($claimId, @$cptTxnDet->id, @$cptTxnDet->claim_cpt_info_id);
                                $adj_resp = '';
                                if(!empty($adjs)) {
                                  foreach($adjs as $adjRec) {
                                    $adj_resp .="\n" .$adjRec['adj_code'].": ".Helpers::priceFormat($adjRec['adj_amt']);
                                  }
                                }
                                $desc .=$adj_resp;
                                /*
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.pmt_withheld_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->withheld, $resp);
                                */
                            //}
                            // if adjustment applied it needs to append
							
                            if (@$cptTxnDet->writeoff != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_adj_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->writeoff, $resp);
                            }
							
                            if($txnVal['value_2'] != '') {
                                $denial_desc = Lang::get("payments/claim_transaction.cpt_txn.denial_code_desc");
                                $denail_codes = implode(':', array_filter(array_unique(explode(',', $txnVal['value_2']))));
                                $desc .= "\n" . str_replace("VAR_CODES", rtrim($denail_codes, ':'), $denial_desc);
                            }
                            // if writeoff / withheld provided then its sum in adjustment column
                            if(@$cptTxnDet->writeoff !=0 || @$cptTxnDet->withheld != 0 ){
                                $adj = $cptTxnDet->writeoff + $cptTxnDet->withheld; // $pmtInfo->adj_amount;  
                            }
                            break;
                            
                        case 'Change Payment':
                            // Line item payment modified
                            break;

                        case 'Denials':
                            // Claim denied for the CPT
                            // Pmt : AAR?
                            // Denial/Remark Codes - PRM102,PRM100
                            $desc = '';
                            if ($responsibility != '') {
                                $desc = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_paid_desc");
                                $desc = "\n" . str_replace("VAR_INS_NAME", $responsibility, $desc);
                            }

                            if(isset($cptTxnDet->paid))
                              $pmts = @$cptTxnDet->paid;
                            if (@$cptTxnDet->deduction != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_ded_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->deduction, $resp);
                            }
                            if (@$cptTxnDet->coins != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coins_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->coins, $resp);
                            }
                            if (@$cptTxnDet->copay != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coppay_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->copay, $resp);
                            }
                            //if (@$cptTxnDet->withheld != 0) {
                            // Other adjustment details shown instead of withheld.
                                $adjs = SELF::getClaimOtherAdjDetails($claimId, @$cptTxnDet->id, @$cptTxnDet->claim_cpt_info_id);
                                $adj_resp = '';
                                if(!empty($adjs)) {
                                  foreach($adjs as $adjRec) {
                                    $adj_resp .="\n" .$adjRec['adj_code'].": ".Helpers::priceFormat($adjRec['adj_amt']);
                                  }
                                }
                                $desc .=$adj_resp;
                                /*
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.pmt_withheld_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->withheld, $resp);
                                */
                            //}
                            if (@$cptTxnDet->writeoff != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_adj_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->writeoff, $resp);
                            }
                            if (@$cptTxnDet->denial_code != '') {
                                $denial_desc = Lang::get("payments/claim_transaction.cpt_txn.denial_code_desc");
                                $denail_codes = implode(':', array_filter(array_unique(explode(',', $cptTxnDet->denial_code))));
                                $desc .= "\n" . str_replace("VAR_CODES", rtrim($denail_codes, ':'), $denial_desc);
                            }
                            // if writeoff / withheld provided then its sum in adjustment column
                            if(@$cptTxnDet->writeoff !=0 || @$cptTxnDet->withheld != 0 ){
                                $adj = $cptTxnDet->writeoff + $cptTxnDet->withheld; // $pmtInfo->adj_amount;  
                            } 
                            break;

                        case 'Insurance Refund':
                            // Refunded to insurance
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.refund_txn_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            //$pmtInfo = PMTInfoV1::getPaymentInfoById($claimId, @$txnVal['payment_id']);
                            $pmts = @$cptTxnDet->paid;
                            break;

                        case 'Patient Refund':
                            // Refunded to patient
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.refund_txn_desc");
                            $desc = str_replace("VAR_SHORT_NAME", 'Patient', $desc);
                            //$pmtInfo = PMTInfoV1::getPaymentInfoById($claimId, @$txnVal['payment_id']);
                            $pmts = $cptTxnDet->paid;
                            break;

                        case 'Patient Adjustment':
                            // Patient payment adjustmented
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.pat_adj_txn_desc"); // pat_adj_txn
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            $reason = (@$pmtInfo->pmtadjustment_details['adjustment_shortname'] != '' ) ?@$pmtInfo->pmtadjustment_details['adjustment_shortname'] : @$pmtInfo->pmtadjustment_details['adjustment_reason'];
                            $desc = str_replace("VAR_REASON", $reason, $desc);
                            $adj = @$cptTxnDet->writeoff + @$cptTxnDet->withheld; // $pmtInfo->adj_amount;
                            break;

                        case 'Insurance Adjustment':
                            // Insurance payment adjusted
                            /*$desc = Lang::get("payments/claim_transaction.cpt_txn.ins_adj_txn_desc"); // pat_adj_txn
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            $reason = (@$pmtInfo->pmtadjustment_details['adjustment_shortname'] != '' ) ?@$pmtInfo->pmtadjustment_details['adjustment_shortname'] : @$pmtInfo->pmtadjustment_details['adjustment_reason'];
                            $desc = str_replace("VAR_REASON", $reason, $desc);
                            $adj = @$cptTxnDet->writeoff + @$cptTxnDet->withheld; // $adjust_amt+$withheld_amt
                            break;*/
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.ins_adj_txn_desc");
                            $resp = ($txnVal['value_1'] > 0 && $txnVal['value_1'] != "") ? self::GetInsuranceName($txnVal['value_1']) : $responsibility;
                            $desc = str_replace("VAR_REASON", $resp, $desc);
                            //$pmtInfo = PMTInfoV1::getPaymentInfoById($claimId, @$txnVal['payment_id']);
                            $pmts = @$cptTxnDet->paid;
                            $total_charge = ($ins_bal + $pat_bal) - $adj;

                            if (@$cptTxnDet->deduction != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_ded_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->deduction, $resp);
                            }
                            if (@$cptTxnDet->coins != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coins_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->coins, $resp);
                            }
                            if (@$cptTxnDet->copay != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coppay_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->copay, $resp);
                            }
                            //if (@$cptTxnDet->withheld != 0) {
                			// Other adjustment details shown instead of withheld.
                                $adjs = SELF::getClaimOtherAdjDetails($claimId, @$cptTxnDet->id, @$cptTxnDet->claim_cpt_info_id);
                                $adj_resp = '';
                                if(!empty($adjs)) {
                                  foreach($adjs as $adjRec) {
                                    $adj_resp .="\n" .$adjRec['adj_code'].": ".$adjRec['adj_amt'];
                                  }
                                }
                                $desc .=$adj_resp;
                                /*
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.pmt_withheld_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->withheld, $resp);
                                */
                            //}
                            // if adjustment applied it needs to append
                            if (@$cptTxnDet->writeoff != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_adj_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $cptTxnDet->writeoff, $resp);
                            }
                            if($txnVal['value_2'] != '') {
                                $denial_desc = Lang::get("payments/claim_transaction.cpt_txn.denial_code_desc");
                                $denail_codes = implode(':', array_filter(array_unique(explode(',', $txnVal['value_2']))));
                                $desc .= "\n" . str_replace("VAR_CODES", rtrim($denail_codes, ':'), $denial_desc);
                            }
                            // if writeoff / withheld provided then its sum in adjustment column
                            if(@$cptTxnDet->writeoff !=0 || @$cptTxnDet->withheld != 0 ){
                                $adj = $cptTxnDet->writeoff + $cptTxnDet->withheld; // $pmtInfo->adj_amount;  
                            }
                            break;

                        case 'Edit Charge':
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.charge_updated_desc");
                            // Need to take charge amount from claim cpt desc value json string if available, otherwise take from cpt_info
                            if($txnVal['value_1'] != '') {
                			$descDet = json_decode($txnVal['value_1'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                if(!empty($descDet) && isset($descDet['charge_amt']) && trim($descDet['charge_amt']) != '') {
                                  $charges = trim($descDet['charge_amt']);
                                }
                              }
              				}
                            break;

                        case 'Wallet':
                            // Wallet transaction made for an CPT
                            //   - |  - | 1/4/2018 | Patient | Excess amount 17 moved to wallet?| - | -17 | 0 |  0 |  80
                            $pmts = number_format($txnVal['value_1'], 2);
                            $desc = Lang::get("payments/claim_transaction.claim_txn.excess_wallet_transfer_desc");
                            $pmtsVal = ($pmts < 0 ) ? -1 * $pmts : $pmts;
                            $desc = str_replace("VAR_AMOUNT", $pmtsVal, $desc);
                            $pmts = -1*$pmts;
                            break;

                        case 'Void Check':
                            if($txnVal['value_2'] != '' && $txnVal['value_2'] == 1)
                              $desc = Lang::get("payments/claim_transaction.claim_txn.void_ins_check_desc");
                            else
                              $desc = Lang::get("payments/claim_transaction.claim_txn.void_check_desc"); 
                            $pmts = @$cptTxnDet->paid;
                            $desc = str_replace("VAR_TXN_AMOUNT", $pmts, $desc);
                            break;

                        case 'Patient Credit Balance':
                        case 'Credit Balance':
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.pat_cr_bal_txn_desc");
                            // Handle credit balance
                            if($txnVal['value_1'] != "" ){
                              $descDet = json_decode($txnVal['value_1'], true);
                              if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                foreach ($descDet as $key => $detVal) {
                                  $pmtInfo = PMTInfoV1::with('checkDetails','creditCardDetails','eftDetails')
                                             // ->where('source_id', $claimId)
                                              ->where('id', @$detVal['pmt_info_id'])->first();
                                  if(!empty($pmtInfo)){
                                    if($pmtInfo->pmt_mode == 'Check'){
                                      $desc .= "\n Pmt: " .$detVal['amountApplied']." from CHK# ".@$pmtInfo->checkDetails['check_no'];  
                                    } elseif($pmtInfo->pmt_mode == 'EFT'){
                                      $desc .= "\n Pmt: " .$detVal['amountApplied']." from EFT# ".@$pmtInfo->eftDetails['eft_no'];  
                                    } elseif($pmtInfo->pmt_mode == 'Credit'){
                                      $desc .= "\n Pmt: " .$detVal['amountApplied']." from CREDIT# ".@$pmtInfo->creditCardDetails['card_last_4']; 
                                    } else {
                                      $desc .= "\n Pmt: " .$detVal['amountApplied']." from CASH"; 
                                    }
                                  }
                                }
                              } 
                            }
                            $pmts = @$cptTxnDet->paid;
                            // handle value2 txn ids and make it sum
                            if(trim($txnVal['value_2']) != "" ){
                              $txnIds = array_filter(array_unique(explode(',', $txnVal['value_2'])));
                              $pmts = 0;
                              foreach ($txnIds as $key => $txId) {
                                $txnAmt = PMTInfoV1::getClaimCptTxAmtById($txId);
                                $pmts +=$txnAmt; 
                              }
                            }
                            break;

                        case 'Submitted': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.submitted_edi_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Submitted Paper': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.submitted_paper_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
							break;

                        case 'Resubmitted': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.resubmitted_edi_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Resubmitted Paper': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.resubmitted_paper_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;      

                        case 'Payer Rejected':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_payer_rej_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Payer Accepted':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_payer_acc_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Clearing House Rejection':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_rej_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Clearing House Accepted': // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_acc_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        default:
                            $desc = $txnVal['transaction_type'];
                            break;
                    }
                                        
                    $txnArr['txn_details'] = $txnVal;
                    $txnArr['CPT'] = $cptId;
                    $txnArr['cpt_code'] = @$txnVal['claimcpt_info']->cpt_code;
                    $txnArr['responsibility'] = $responsibility;
                    $txnArr['resp_category'] = @$respCat;
                    $txnArr['resp_bg_class'] = @$resp_bg_class; // For apply class for response category
                    $txnArr['description'] = $desc;
                    $txnArr['charges'] = ($charges != 0) ? Helpers::priceFormat($charges) : "";
                    $txnArr['payments'] = Helpers::priceFormat($pmts);
                    $txnArr['adjustment'] = Helpers::priceFormat($adj);
                    $txnArr['pat_balance'] = Helpers::priceFormat($pat_bal);
                    $txnArr['ins_balance'] = Helpers::priceFormat($ins_bal);

                    if ($cptTxId > 0)
                        $descArr[] = $txnArr;
                    else
                        $descArr[$cptId][] = $txnArr;
                }
            }
            //dd($descArr);
            return $descArr;
        } catch (Exception $e) {
            $respMsg = "getClaimCptTxnDesc | Error Msg: " . $e->getMessage() . ". | Occured on Line# " . $e->getLine();
            $respMsg .= "Trace |" . $e->getTraceAsString();
            \Log::info(' Exception Occurred: >>' . $respMsg);
        }
    }

    public static function getInsTxnCategory($txn_id) {
        $ins_cat = PMTClaimTXV1::where('id', $txn_id)->pluck('ins_category')->first();
        return $ins_cat;
    }

    /* Checking Claim Submission error */

    public static function ClearingClaimErrors($type_id, $type = '') {
		
        Switch ($type) {
            case 'Patient':
                $calim_error_details = ClaimInfoV1::where('patient_id', $type_id)->where('error_message', '!=', '')->get()->toArray();
                foreach ($calim_error_details as $list) {
                    $total_error = '';
                    $error = explode('- ', $list['error_message']);
					
                    $count = 0;
                    foreach ($error as $error_list) { //echo trim(substr($error_list, 0, 10));
                        if (trim(substr($error_list, 0, 10)) != 'Patient |' && !empty(trim(substr($error_list, 0, 10)))) { 
                            $total_error .= '- ' . $error_list . "<br>";
                            $count++;
                        }
                    }
					
					$claim_update = ClaimInfoV1::find($list['id']);
                    if ($count != 0) {
                        $claim_update->error_message = $total_error;
                        $claim_update->save();
                        if ($claim_update->error_message == '') {
                            $claim_update->no_of_issues = 0;
                            $claim_update->save();
                        }
                    }else{
						$claim_update->no_of_issues = 0;
						$claim_update->error_message = '';
                        $claim_update->save();
					}
                }
                break;

            case 'Insurance':
                $calim_error_details = ClaimInfoV1::where('insurance_id', $type_id)->where('error_message', '!=', '')->get()->toArray();
                foreach ($calim_error_details as $list) {
                    $total_error = '';
                    $error = explode('- ', $list['error_message']);
                    $count = 0;
                    foreach ($error as $error_list) {
                        if (trim(substr($error_list, 0, 11)) != 'Insurance |' && !empty(trim(substr($error_list, 0, 11)))) {
                            $total_error .= '- ' . $error_list . "<br>";
                            $count++;
                        }
                    }
					
					if(strpos($list['error_message'], 'No insurance found') !== false){
						$count = 0;
					}
                    $claim_update = ClaimInfoV1::find($list['id']);
                    if ($count != 0) {
                        $claim_update->error_message = $total_error;
                        $claim_update->save();
                        if ($claim_update->error_message == '') {
                            $claim_update->no_of_issues = 0;
                            $claim_update->save();
                        }
                    }else{
						$claim_update->no_of_issues = 0;
						$claim_update->error_message = '';
                        $claim_update->save();
					}
                }
                break;
                
            case 'Billing':
                $calim_error_details = ClaimInfoV1::where('billing_provider_id', $type_id)->where('error_message', '!=', '')->get()->toArray();
                foreach ($calim_error_details as $list) {
                    $total_error = '';
                    $error = explode('- ', $list['error_message']);
                    $count = 0;
                    foreach ($error as $error_list) {
                        if (trim(substr($error_list, 0, 9)) != 'Billing |' && !empty(trim(substr($error_list, 0, 9)))) {
                            $total_error .= '- ' . $error_list . "<br>";
                            $count++;
                        }
                    }
                    $claim_update = ClaimInfoV1::find($list['id']);
                    if ($count != 0) {
                        $claim_update->error_message = $total_error;
                        $claim_update->save();
                        if ($claim_update->error_message == '') {
                            $claim_update->no_of_issues = 0;
                            $claim_update->save();
                        }
                    }else{
						$claim_update->no_of_issues = 0;
						$claim_update->error_message = '';
                        $claim_update->save();
					}
                }
                break;
                
            case 'Rendering':
                $calim_error_details = ClaimInfoV1::where('rendering_provider_id', $type_id)->where('error_message', '!=', '')->get()->toArray();
				
                foreach ($calim_error_details as $list) {
                    $total_error = '';
                    $error = explode('- ', $list['error_message']);
                    $count = 0;
                    foreach ($error as $error_list) {
                        if (trim(substr($error_list, 0, 11)) != 'Rendering |' && !empty(trim(substr($error_list, 0, 11)))) {
                            $total_error .= '- ' . $error_list . "<br>";
                            $count++;
                        }
                    }
                    $claim_update = ClaimInfoV1::find($list['id']);
                    if ($count != 0) {
                        $claim_update->error_message = $total_error;
                        $claim_update->save();
                        if ($claim_update->error_message == '') {
                            $claim_update->no_of_issues = 0;
                            $claim_update->save();
                        }
                    }else{
						$claim_update->no_of_issues = 0;
						$claim_update->error_message = '';
                        $claim_update->save();
					}
                }
                break;
                
            case 'Facility':
                $calim_error_details = ClaimInfoV1::where('facility_id', $type_id)->where('error_message', '!=', '')->get()->toArray();
                foreach ($calim_error_details as $list) {
                    $total_error = '';
                    $error = explode('- ', $list['error_message']);
                    $count = 0;
                    foreach ($error as $error_list) {
                        if (trim(substr($error_list, 0, 10)) != 'Facility |' && !empty(trim(substr($error_list, 0, 10)))) {
                            $total_error .= '- ' . $error_list . "<br>";
                            $count++;
                        }
                    }
                    $claim_update = ClaimInfoV1::find($list['id']);
                    if ($count != 0) {
                        $claim_update->error_message = $total_error;
                        $claim_update->save();
                        if ($claim_update->error_message == '') {
                            $claim_update->no_of_issues = 0;
                            $claim_update->save();
                        }
                    }else{
            			   $claim_update->no_of_issues = 0;
            			   $claim_update->error_message = '';
                        $claim_update->save();
					         }
                }
                break;
                
            default:
                // ...
                break;
        }
    }

    /* Checking Claim Submission error */

    public static function InsuranceOverPayment($claim_id) {
        //$insurance_paid = PaymentClaimCtpDetail::where('claim_id', $claim_id)->where('allowed_amt', '>', 'insurance_paid')->sum('insurance_paid');
        $excess_payment = PMTClaimFinV1::where('claim_id', $claim_id)->where('insurance_due', '<', '0')->sum('insurance_due');
        $excess_payment = ($excess_payment) ? abs($excess_payment) : "0.00";
        //$total_amt = $insurance_paid - $allowed_paid;
        return $excess_payment;
    }

    // $type = insurance_paid_amt /patient_paid_amt
    public static function getRefund($claim_id, $type = 'insurance_paid_amt') {
        $pmt_method = ($type == 'patient_paid_amt') ? 'Patient' : 'Insurance';
        if (!is_numeric($claim_id))
            $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
          
        $due = PMTClaimTXV1::whereHas('latest_payment', function($query) {
                    $query->where('void_check', NULL);
                })
                ->where('claim_id', $claim_id)
                ->where('pmt_method', $pmt_method)
                ->where('total_paid', '<', 0)
                ->where('pmt_type', "Refund") // consider only refund Payment
                //->whereNotIn('pmt_type', ["Addwallet"])
                ->sum('total_paid');

        return abs($due);
    }

    /*
     * PAtient statement view in claimwise transation 
     */

    public static function getClaimTransactionList($claimId = 0) {
        try {
            $claimFinDetails = PmtClaimFinV1::where('claim_id', $claimId)->first();
            if (!empty($claimFinDetails)) {
                $claim['total_charge'] = $claimFinDetails->total_charge;
                $claim['total_payments'] = $claimFinDetails->patient_paid + $claimFinDetails->insurance_paid;
                $claim['total_adjustment'] = $claimFinDetails->patient_adj + $claimFinDetails->insurance_adj;
                $claim['patient_balance'] = '';
                $claim['insurance_balance'] = '';
            }
            $descArr = [];
            $txnDesc = ClaimTXDESCV1::with('claim_info', 'claim_txn', 'insurance_details')
                            ->where('claim_id', $claimId)->orderBy('id', 'ASC')->get();
            if (COUNT($txnDesc) > 0) {
                foreach ($txnDesc as $txnKey => $txnVal) {

                    $txnArr['txn_date'] = Helpers::dateFormat($txnVal['created_at'], 'datedb');
                    $txnDet = isset($txnVal['claim_txn']) ? $txnVal['claim_txn'] : [];
                    $txnId = $txnVal['txn_id'];
                    $claimDetails = $txnVal['claim_info'];

                    $desc = $pmt_type = '';
                    $charges = $pmts = $adj = $pat_bal = $ins_bal = $responsibility = 0;
                    $pat_bal = $txnVal['pat_bal'];
                    $ins_bal = $txnVal['ins_bal'];
                    $responsibility = (!empty($txnVal['insurance_details'])) ? @$txnVal['insurance_details']->short_name : 'Patient'; // have to get the resp name
                    $respCat = (isset($txnDet->ins_category) ) ? @$txnDet->ins_category : "";
                    if($respCat =='Primary')
                        $cat_bg_class = "pri-bg";
                    elseif($respCat =='Secondary')
                        $cat_bg_class = "sec-bg";
                    elseif($respCat =='Tertiary')
                        $cat_bg_class = "ter-bg";
                    else
                        $cat_bg_class = "pri-bg";
                      
                    //  $claimDetails = $this->getClaimDetails($claimId);
                    switch ($txnVal['transaction_type']) {
                        case 'New Charge':                                  // Claim Created
                            $desc = Lang::get('payments/claim_transaction.claim_txn.charge_created_desc');
                            if(trim(@$txnVal['claim_info']->claim_reference) != '') {
                              $desc .= "\n Ref: ".trim($txnVal['claim_info']->claim_reference);
                            }
                            $charges = @$claimDetails->total_charge;
                            if ($responsibility == '')
                                $responsibility = 'Patient';
                            $total_charge = ($ins_bal + $pat_bal) - $adj;
                            break;

                        case 'Responsibility':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.transfer_to_ins_desc");
                            $responsibility = ($responsibility == '') ? 'Patient' : $responsibility;
                            $desc = str_replace("VAR_INS_NAME", $responsibility, $desc);

                            // Set old responsiblity
                            $old_resp = ($txnVal['value_1'] != 0) ? self::GetInsuranceName($txnVal['value_1']) : 'Patient';
                            $responsibility = ($old_resp == "Self" || $old_resp == '' ) ? 'Patient': $old_resp;
                            $pmts = $charges = '';
                            break;    

                        case 'Patient Payment':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.pat_pmt_paid_desc");
                            $claimDetails = $txnVal['claim_info'];
                            $total_charge = $claimDetails->total_charge;
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById( @$txnVal['payment_id']);

                            $pat_name = 'Patient';
                            $desc = str_replace("VAR_PAT_NAME", $pat_name, $desc);
                            $pmt_type = @$pmtInfo->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt_type = "Chk No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                               $pmt_type = "MO No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt_type = "EFT No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt_type = (@$pmtInfo->card_last_4 != '') ? "Credit Card No. " . @$pmtInfo->card_last_4 : "Credit Card";
                            }
                            if(trim(@$pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            $pmts = @$txnDet->total_paid; //@$pmtInfo->pmt_amt;
                            break;    

                        case 'Insurance Payment':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_paid_desc");
                            $resp = ($txnVal['value_1'] != 0 && $txnVal['value_1'] != "") ? self::GetInsuranceName($txnVal['value_1']) : $responsibility;
                            $desc = str_replace("VAR_INS_NAME", $resp, $desc);
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById( @$txnVal['payment_id']);

                            $pmt_type = @$pmtInfo->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt_type = "Chk No. " . @$pmtInfo->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt_type = "EFT No. " . @$pmtInfo->check_no;
                            }
                            $pmts = @$txnDet->total_paid; // @$pmtInfo->pmt_amt;
                            $total_charge = ($ins_bal + $pat_bal) - $adj;
 
                            if (@$txnDet->total_deduction != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_ded_txn_desc");
                                $resp = str_replace("PR01", "Deductible", $resp);
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_deduction, $resp);
                            }

                            if (@$txnDet->total_coins != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_coins_txn_desc");
                                $resp = str_replace("PR02", "Co-Insurance", $resp);
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_coins, $resp);
                            }

                            if (@$txnDet->total_copay != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_coppay_txn_desc");
                                $resp = str_replace("PR03", "Copay", $resp);
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_copay, $resp);
                            }

                            if (@$txnDet->total_withheld != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.pmt_withheld_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_withheld, $resp);
                            }

                            // if adjustment applied it needs to append
                            if (@$txnDet->total_writeoff != 0) {
                                $resp = Lang::get("payments/claim_transaction.claim_txn.ins_pmt_adj_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", @$txnDet->total_writeoff, $resp);
                            } 
                            if($txnVal['value_2'] != '') {
                                $denial_desc = Lang::get("payments/claim_transaction.cpt_txn.denial_code_desc");
                                $denail_codes = implode(':', array_unique(array_filter(explode(',', $txnVal['value_2']))));
                                $desc .= "\n" . str_replace("VAR_CODES", rtrim($denail_codes, ':'), $denial_desc);
                            }

                            if(isset($pmtInfo->reference) && trim($pmtInfo->reference) != '') {
                                $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }

                            // If withheld or writeoff provided then so it in adjustment column
                            if(@$txnDet->total_withheld != 0 || @$txnDet->total_writeoff != 0) {
                                $adj = $txnDet->total_withheld + $txnDet->total_writeoff;  
                            }
                            break;

                        case 'Denials':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.denial_txn_desc");
                            if(isset($txnDet->total_paid))
                              $pmts = @$txnDet->total_paid; // @$pmtInfo->pmt_amt; 
                            break;

                        case 'Insurance Refund':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.refund_txn_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            $pmtInfo = PMTInfoV1::getPaymentInfoById($claimId, @$txnVal['payment_id']);
                            if(isset($pmtInfo->reference) && trim($pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            $pmts = $txnDet->total_paid; // @$pmtInfo->pmt_amt; 
                            break;

                        case 'Patient Refund':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.refund_txn_desc");
                            $desc = str_replace("VAR_SHORT_NAME", 'Patient', $desc);
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            if(isset($pmtInfo->reference) && trim($pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            $pmts = $txnDet->total_paid; // @$pmtInfo->pmt_amt; 
                            break;

                        case 'Insurance Adjustment':
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            $reason = (@$pmtInfo->pmtadjustment_details['adjustment_shortname'] != '' ) ?@$pmtInfo->pmtadjustment_details['adjustment_shortname'] : @$pmtInfo->pmtadjustment_details['adjustment_reason'];
                            $desc = Lang::get("payments/claim_transaction.claim_txn.ins_adj_txn_desc");
                            $desc = str_replace("VAR_REASON", $reason, $desc);
                            if(isset($pmtInfo->reference) && trim($pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            $adj = @$txnDet->total_withheld+@$txnDet->total_writeoff;
                            break;    
                        
                        case 'Patient Adjustment':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.pat_adj_txn_desc"); // pat_adj_txn
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$txnVal['payment_id']);
                            $reason = (@$pmtInfo->pmtadjustment_details['adjustment_shortname'] != '' ) ? @$pmtInfo->pmtadjustment_details['adjustment_shortname'] : @$pmtInfo->pmtadjustment_details['adjustment_reason'];
                            $desc = str_replace("VAR_REASON", $reason, $desc);
                            if(isset($pmtInfo->reference) && trim($pmtInfo->reference) != '') {
                              $desc .= "\n Ref: ".trim($pmtInfo->reference);
                            }
                            $adj = @$txnDet->total_withheld+@$txnDet->total_writeoff;
                            break;

                        case 'Patient Credit Balance':
                        case 'Credit Balance':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.pat_cr_bal_txn_desc");
                            // Handle credit balance
                            if($txnVal['value_1'] != "" ){ 
                              $descDet = json_decode($txnVal['value_1'], true);
                              if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                foreach ($descDet as $key => $detVal) {
                                  $pmtInfo = PMTInfoV1::with('checkDetails','creditCardDetails','eftDetails') // ->where('source_id', $claimId)
                                              ->where('id', @$detVal['pmt_info_id'])->first();
                                  if(!empty($pmtInfo)) {            
                                    if($pmtInfo->pmt_mode == 'Check'){
                                      $desc .= "\n " .$detVal['amountApplied']." from CHK# ".@$pmtInfo->checkDetails['check_no'];  
                                    } elseif($pmtInfo->pmt_mode == 'EFT'){
                                      $desc .= "\n " .$detVal['amountApplied']." from EFT# ".@$pmtInfo->eftDetails['eft_no'];  
                                    } elseif($pmtInfo->pmt_mode == 'Credit'){
                                      $desc .= "\n " .$detVal['amountApplied']." from CREDIT# ".@$pmtInfo->creditCardDetails['card_last_4']; 
                                    } else {
                                      $desc .= "\n " .$detVal['amountApplied']." from CASH"; 
                                    }     
                                    if(trim($pmtInfo->reference) != '') {
                                      $desc .= "\n Ref: ".trim($pmtInfo->reference);
                                    }     
                                  }
                                }
                              }
                            }
                            $pmts = @$txnDet->total_paid;
                            
                            // handle value2 txn ids and make it sum
                            if(trim($txnVal['value_2']) != "" ){
                              $txnIds = array_filter(array_unique(explode(',', $txnVal['value_2'])));
                              $pmts = 0;
                              foreach ($txnIds as $key => $txId) {
                                $txnAmt = PMTInfoV1::getClaimTxAmtById($txId);
                                $pmts +=$txnAmt; 
                              }
                            }
                            break;

                        // No caluclation required
                        case 'Edit Charge':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.charge_updated_desc");
                            break;

                        case 'Submitted': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.submitted_edi_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Submitted Paper': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.submitted_paper_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Resubmitted': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.resubmitted_edi_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;
    
                        case 'Resubmitted Paper': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.resubmitted_paper_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;      

                        case 'Payer Rejected':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_payer_rej_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Payer Accepted':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_payer_acc_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Clearing House Rejection':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_rej_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Clearing House Accepted': // Handle ClearingHouse Status    
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_acc_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;    

                        case 'Wallet':
                            $pmts = number_format($txnVal['value_1'], 2);
                            $desc = Lang::get("payments/claim_transaction.claim_txn.excess_wallet_transfer_desc");
                            $pmtsVal = ($pmts < 0 ) ? -1 * $pmts : $pmts;
                            $desc = str_replace("VAR_AMOUNT", $pmtsVal, $desc);
                            $pmts = -1*$pmts;
                            break;    
    
                        case 'Void Check':
                            $desc = Lang::get("payments/claim_transaction.claim_txn.void_check_desc");
                            $pmts = @$txnDet->total_paid;
                            $desc = str_replace("VAR_TXN_AMOUNT", $pmts, $desc);
                            
                            break;

                        default:
                            $desc = $txnVal['transaction_type']; // Handle non handled types..
                            break;
                    }
                }
                $txnArr['txn_details'] = $txnVal;
                $txnArr['desc_id'] = $txnVal['id'];
                $txnArr['txn_id'] = $txnId;
                $txnArr['description'] = $desc;
                //$txnArr['payment_type'] = $pmt_type;
                $txnArr['charges'] = ($charges != 0) ? Helpers::priceFormat($charges) : "";
                $txnArr['payments'] =  Helpers::priceFormat($pmts) ;
                $txnArr['adjustment'] =  Helpers::priceFormat($adj);
                $txnArr['pat_balance'] = Helpers::priceFormat($pat_bal);
                $txnArr['ins_balance'] = Helpers::priceFormat($ins_bal);
                $txnArr['responsibility'] = $responsibility;
                $txnArr['resp_category'] = @$respCat;
                $txnArr['resp_bg_class'] = @$cat_bg_class; // For apply class for response category
                $txnArr['claim_fin_detail'] = @$claim['total_charge'];
                $txnArr['total_payments'] = @$claim['total_payments'];
                $txnArr['cpt_transaction'] = SELF::getClaimCptTxnList($claimId, '', $txnVal['id']);
                $txnArr['payment_trans'] = PMTInfoV1::getPaymentInfoById($claimId, @$txnVal['payment_id']);
                $txnArr['pmt_tra_amt'] = PMTClaimCPTFINV1::where('claim_id', $claimId)->first();
                $txnArr['pmt_trans'] = PMTClaimTXV1::where('claim_id', $claimId)->get()->toArray();
                $txnArr['total_balance'] = $txnArr['claim_fin_detail'] - $txnArr['total_payments']; //($txnArr['pmt_tra_amt']->with_held + $txnArr['pmt_tra_amt']->patient_paid + $txnArr['pmt_tra_amt']->insurance_paid) ;
                $txnArr['pmt_fins'] = PMTClaimFINV1::where('claim_id', $claimId)->first();
                $descArr[] = $txnArr;
            }
            //dd($txnArr);
            return $descArr;
        } catch (Exception $e) {
            $respMsg = "getClaimTxnDesc | Error Msg: " . $e->getMessage() . ". | Occured on Line# " . $e->getLine();
            $respMsg .= "Trace |" . $e->getTraceAsString();
            \Log::info(' Exception Occurred: >>' . $respMsg);
        }
    }

    public static function getCheckNo($check_name, $id) {
        return (PMTCheckInfoV1::where('id', $id)->pluck('check_no')->first());
    }

    public static function adj_reason($adj, $id) {
        if ($adj == 'Adjustment')
            return PMTADJInfoV1::where('id', $id) - pluck('adj_reason_id')->first();
        else
            return 0;
    }

    public static function getPatientPaidAmt($claim_id) {
        $claim_data = PmtClaimFinV1::where('claim_id', $claim_id)->pluck('patient_paid')->first();
        return $claim_data;
    }

    //$txn_for = create_charge / patient_payment / insurance_payment / patient_adjustment / insurance_adjustment / pending / submitted / rejection / hold / responsibility / edit_charge / Denied

    public static function updateClaimStatus($claim_id = 0, $txn_for='', $details_arr=[]) {
      $statusVal = '';
      try{
        $claimDet = ClaimInfoV1::find($claim_id);
        if($claim_id > 0 && !empty($claimDet)){        
          $txn_for = trim(str_replace(" ", "_", strtolower($txn_for))); 
          $claimFinDetails = PmtClaimFinV1::where('claim_id', $claim_id)->first();
          $total_pmt = (!empty($claimFinDetails)) ? (@$claimFinDetails->patient_paid+@$claimFinDetails->insurance_paid) : 0;
          $ins_due = (!empty($claimFinDetails)) ? (@$claimFinDetails->insurance_due) : 0;
          $pat_due = (!empty($claimFinDetails)) ? (@$claimFinDetails->patient_due) : 0;
          $pat_adj = (!empty($claimFinDetails)) ? (@$claimFinDetails->patient_adj) : 0;
          $ins_adj = (!empty($claimFinDetails)) ? (@$claimFinDetails->insurance_adj) : 0;
          // Total Adjustment = Pat Adj + Ins Adj + W held.
          $tot_adj = $pat_adj + $ins_adj + ( (!empty($claimFinDetails)) ? (@$claimFinDetails->withheld) : 0 );
          $total_charge = $claimDet->total_charge;
          $preStatus = strtolower($claimDet->status);
          $billed_to = ($claimDet->insurance_id > 0) ? 'Insurance' : 'Self';
          $tPaidAmt = $total_pmt+$tot_adj;
          // Claim_status: 'E-bill','Hold','Ready','Patient','Submitted','Paid','Denied','Pending','Rejection'
          // if responsibility is insurance - insurance_due is 0 => Paid / patient - patient_due is 0 => Paid
          // Comparision for double value of equal amount issue fixed using precision value 
            switch ($txn_for) {
              case 'create_charge':
                // While create charge, billed to Insurance then status will be 'Ready' otherwise 'Patient'
              case 'edit_charge':
              case 'responsibility':
                // If responsibility is changed from self to insurance, then status will be “Ready”
                //$statusVal = ($billed_to == 'Insurance') ? 'Ready' : 'Patient';
                if($billed_to == 'Insurance'){
                    $statusVal = ((abs($tPaidAmt) >= abs($total_charge) || (abs($tPaidAmt - $total_charge) < 0.0001)) && ceil($ins_due) <= 0 ) ? 'Paid' : 'Ready' ;
                } else {
                    $statusVal = ((abs($tPaidAmt) >= abs($total_charge) || (abs($tPaidAmt - $total_charge) < 0.0001)) && ceil($pat_due) <= 0 ) ? 'Paid' : 'Patient' ;
                }
                break;

              case 'patient_payment':
              case 'patient payment':
              case 'patient_refund':
              case 'patient_adjustment':
              case 'patient_credit_balance':
              case 'void_check':
                if($preStatus == 'pending' || $preStatus == 'submitted' || $preStatus == 'rejection' || $preStatus == 'denied' || $preStatus == 'hold'){
                  // If claim is in 'PENDING' / 'SUBMITTED' / 'REJECTION' / 'Denied') -> status should not change on pat pmt.
                } else {
                    if($billed_to == 'Insurance'){                  
                      $statusVal = ((abs($tPaidAmt) >= abs($total_charge) || (abs($tPaidAmt - $total_charge) < 0.0001)) && ceil($ins_due) <= 0 ) ? 'Paid' : 'Ready' ;
                    } else {
                      $statusVal = ((abs($tPaidAmt) >= abs($total_charge) || (abs($tPaidAmt - $total_charge) < 0.0001)) && ceil($pat_due) <= 0 ) ? 'Paid' : 'Patient' ; 
                    }  
                }
                break;  
              
              case 'insurance_payment':
              case 'insurance_refund':
              case 'insurance_adjustment':
                if($billed_to == 'Insurance'){
                  $statusVal = ((abs($tPaidAmt) >= abs($total_charge) || (abs($tPaidAmt - $total_charge) < 0.0001)) && ceil($ins_due) <= 0 ) ? 'Paid' : 'Ready' ;
                } else {
                  $statusVal = ((abs($tPaidAmt) >= abs($total_charge) || (abs($tPaidAmt - $total_charge) < 0.0001)) && ceil($pat_due) <= 0 ) ? 'Paid' : 'Patient'; 
                }
                break;

              case 'pending':
                $statusVal = 'PENDING';
                break;

              case 'submitted':
                $statusVal = 'SUBMITTED';
                break;

              case 'rejection':
                $statusVal = 'REJECTION';
                break;  

              case 'hold':
                $statusVal = 'Hold';
                break; 

              case 'denied':  
                $statusVal = 'Denied';
                break; 

              default:
                if($billed_to == 'Insurance'){
                  $statusVal = ((abs($tPaidAmt) >= abs($total_charge) || (abs($tPaidAmt - $total_charge) < 0.0001))) ? 'Ready' : 'Paid';
                } else {
                  $statusVal = ((abs($tPaidAmt) >= abs($total_charge) || (abs($tPaidAmt - $total_charge) < 0.0001))) ? 'Patient' : 'Paid';
                }
                break;
            }
        }

        if($statusVal != ''){
          $claimDet->status = $statusVal;
           $filed_date = date('0000-00-00 00:00:00');
          /* Filed date for ready status */
          if($statusVal == 'Ready'){
            //  $claimDet->filed_date = date('Y-m-d H:i:s');
            $filed_date = date('Y-m-d H:i:s');
          }
          /* Filed date for ready status */
          /* remove save() of model not working in 5.6 need update()*/
			if($statusVal != 'Hold')
				ClaimInfoV1::where('id',$claim_id)->update(['status' => $statusVal,'sub_status_id' => '' ,'hold_reason_id' => 0,'filed_date'=>$filed_date]);
			else
				ClaimInfoV1::where('id',$claim_id)->update(['status' => $statusVal,'sub_status_id' => '' ,'filed_date'=>$filed_date]);
        }
      } catch(Exception $e){
        \Log::info("Error occured on update claim status. Error Msg: ".$e->getMessage() );
      }
      return $statusVal;
      /****
      Arg: $claim_id, $txn_for = responsibility_change / patient_payment / insurance_payment / create_charge / pending, $claim_det 

      READY
      1.  If a charge is created, status will be “Ready”, -- Billed to Insurance
      2.  If payment has been done for a claim and if next responsibility is insurance, then status will be “Ready”
      3.  If responsibility is changed from self to insurance, then status will be “Ready”
      4.  If Edit and save the Rejection Claim,  then status will be “Ready”
      5.  If Edit and save the Hold Charges(uncheck the Hold option), then status will be “Ready”
      
      PENDING
      1.  In payment posting screen, if manually changed  Pending from claim status, then status will be “Pending”
      2.  In payment posting screen, if manually changed Insurance Pending from Hold dropdown, then status will be “Pending” by this time responsibility should not change if not provided next responsibility.
      
      DENIED
      1.  In the payment posting screen, if manually changed Denied from Claim Status, then status will be “Denied”
      2.  If Claim Denied and if we post payments, then responsibility should not changed next responsibility 
      
      PAID
      1.  If total billed is paid then status will be “paid “. (Based on responsibility)
      
      SUBMITTED
      1.  If a Claim is submitted either electronically or paper, then status will be submitted
      
      REJECTION
      1.  If Claim has been rejected from Clearing house, then status will be “Rejection”

      Patient 
      1.  “Patient” status should be displayed If Next responsibility = Patient
      2.  “Patient” status should be displayed If Billed = Patient

      Hold 
      If Check “hold “ option, Hold status is should be display. 

      ***/
    }


    public static function getClaimFinDetails($claimId, $totalCharge) {
        //$count = PMTClaimFINV1::where('claim_id', '=', $claimId)->count();
        $financialDatas = array(); 
          //if ($count == 1) {
        $totalPaid = 'patient_paid+insurance_paid';
        $totalAdjustment = 'patient_adj+insurance_adj';
        $balance = 'total_charge - (' . $totalPaid . '+' . $totalAdjustment . '+withheld)';

        $resultSet = PMTClaimFINV1::select('id', 'total_charge', 'withheld', 'patient_due', 'insurance_due', 'patient_paid', DB::raw("sum(" . $totalPaid . ") as totalPaid"), DB::raw("sum(" . $totalAdjustment . ") as totalAdjustment"), DB::raw("sum(" . $balance . ") as balance") )
                    ->where('claim_id', $claimId)->first();

        if (!empty($resultSet)) {
            $financialDatas['pmtClaimV1FinRowId'] = $resultSet['id'];
            $financialDatas['total_charge'] = $totalCharge; //$resultSet['total_charge'];
            $financialDatas['total_paid'] = $resultSet['totalPaid'];
            //Testing Team revamp -total adjustment included with withheld.
            $financialDatas['totalAdjustment'] = Helpers::priceFormat($resultSet['totalAdjustment'] + $resultSet['withheld']);
            $financialDatas['withheld'] = $resultSet['withheld'];
            $financialDatas['patient_due'] = $resultSet['patient_due'];
            $financialDatas['insurance_due'] = $resultSet['insurance_due'];
            $financialDatas['patient_paid'] = $resultSet['patient_paid'];
            $financialDatas['balance_amt'] = $resultSet['balance'];
        } else {
            $financialDatas['pmtClaimV1FinRowId'] = 0;
            $financialDatas['total_charge'] = $totalCharge;
            $financialDatas['total_paid'] = 0;
            $financialDatas['totalAdjustment'] = '0.00';
            $financialDatas['patient_paid'] = '0.00';
            $financialDatas['withheld'] = 0;
            $financialDatas['patient_due'] = 0;
            $financialDatas['insurance_due'] = 0;
            $financialDatas['balance_amt'] = 0;
        }
        return $financialDatas;
    }

    public static function getClaimCptFinBalance($totalCharge, $claimscptFinData) {
        if (!empty($claimscptFinData)) {
            $patientAdjusted  =  isset($claimscptFinData['patient_adj'])?$claimscptFinData['patient_adj']:$claimscptFinData['patient_adjusted'];
            $insurance_adjusted  =  isset($claimscptFinData['insurance_adj'])?$claimscptFinData['insurance_adj']:$claimscptFinData['insurance_adjusted'];
            $withheld = isset($claimscptFinData['withheld']) ? $claimscptFinData['withheld'] : $claimscptFinData['with_held'];
            $totalPaid = $claimscptFinData->patient_paid + $claimscptFinData->insurance_paid;
            $totalAdjustment =$patientAdjusted+ $insurance_adjusted;
            $balance = $totalCharge - ($totalPaid + $totalAdjustment + $withheld);
            $temp = array();
            $temp['totalPaid'] = $totalPaid;
            $temp['totalAdjustment'] = $totalAdjustment+$withheld;
            $temp['patient_due'] = $claimscptFinData['patient_due'];
            $temp['insurance_due'] = $claimscptFinData['insurance_due'];
            //balance also known as ArBalance
            //$temp['balance'] =Helpers::priceFormat($balance);
            $temp['balance'] =$balance;
            return $temp;
        } else {
            $temp = array();
            $temp['totalPaid'] = '0.00';
            $temp['totalAdjustment'] = '0.00';
            $temp['balance'] = '0.00';
			$temp['patient_due'] =  '0.00';
            $temp['insurance_due'] =  '0.00';
            return $temp;
        }
    }
    /**
     * To get claim InsuranceDetails
     * Params: $claimId: integer
     *
     */
    public static function getClaimInsuranceDetails($claim_id = '') {
        try {
            if (!empty($claim_id)) {
                $resultData = ClaimInfoV1::select('id','insurance_id', 'self_pay','insurance_category','claim_number')
                    ->where('id', $claim_id)->get()->first();
                return $resultData;
            }
        }catch (Exception $e) {
            $respMsg = "Trace |" . $e->getTraceAsString();
            \Log::info(' Exception Occurred: >>' . $respMsg);
        }
    }

    public static function checkIsAttornyAssignedClaim($claim_id=0, $patient_id) {
        if($claim_id != 0) {
          $isAttrClaim = PatientContact::whereRaw('(find_in_set("'.$claim_id.'", `attorney_claim_num`) )')
                        ->where('category','=','Attorney')->where('patient_id', $patient_id)
                        ->count();
          if($isAttrClaim)
            return true;
        }
        return false;
    }

    public static function deleteClaim($claim_id=''){
      try {
        $id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        $claim_data = ClaimInfoV1::where('id', $id)->select('id', 'patient_id', 'status')->first();

        if (!empty($claim_data)) {                        
            $pat_id = $patient_id = $claim_data->patient_id;
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
            $payment_count = PMTInfoV1::checkpaymentDone($id, 'patient');
            $notes_count = ClaimInfoV1::checkChargeActivity(@$id);
            $attorny_assigned = ClaimInfoV1::checkIsAttornyAssignedClaim($id, $pat_id);
            if (($claim_data->status = 'Ready' || $claim_data->status = 'Patient') && $payment_count == 0 && $notes_count == 0 && $attorny_assigned == 0) {
                
                DB::beginTransaction();

                ClaimInfoV1::where('id', $id)->delete();
                ClaimCPTInfoV1::where('claim_id', $id)->delete();
                ClaimAddDetailsV1::where('claim_id', $id)->delete();
                PMTClaimCPTFINV1::where('claim_id', $id)->delete();
                PMTClaimCPTTXV1::where('claim_id', $id)->delete();
                PMTClaimFINV1::where('claim_id', $id)->delete();
                PMTClaimTXV1::where('claim_id', $id)->delete();
                                
                //  claim_tx_desc_v1, claim_cpt_tx_desc_v1 -> Ignored tables
                DB::commit();
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.delete_msg"), 'data' => compact('patient_id')));
            } else {
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.error_msg"), 'data' => compact('patient_id')));
            }
        } else {
            DB::rollBack();
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.error_msg"),'data' => ''));
        }    

      } catch(Exception $e) {        
        $respMsg = "Trace |" . $e->getTraceAsString();
        \Log::info(' Exception Occurred on deleteClaim: >>' . $respMsg);
        return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
      }
    }

	/* To get claim other adjustment details */
    public static function getClaimOtherAdjDetails($claim_id=0, $txn_id=0, $cpt_id=0){
      $adj_resp = []; 

      if($cpt_id > 0) {
        //  CPT other adjustment details
        $adjDetails = ClaimCPTOthersAdjustmentInfoV1::with('adjustment_details')->where('claim_id', $claim_id)
                          ->where('claim_cpt_id', $cpt_id)
                          ->where('claim_cpt_tx_id', $txn_id)->get();
        //\Log::info("CPT ");\Log::info($adjDetails);
         if(!empty($adjDetails)) {
            foreach ($adjDetails as $adj) {              
              $adj_code = ($adj->adjustment_id != 0) ? @$adj->adjustment_details->adjustment_shortname : 'CO253';
              $adj_resp[] = ['cpt' => $adj->claim_cpt_id, 'adj_id' => $adj->adjustment_id, 'adj_code' => $adj_code, 'adj_amt' => $adj->adjustment_amt];              
            }
          }
      } else {
        // Claim other adjustment details
        $cpt_details = PMTClaimCPTTXV1::where('claim_id', $claim_id)->where('pmt_claim_tx_id', $txn_id)
                      ->select('claim_cpt_info_id', 'id')->get();

        if(!empty($cpt_details)) {
          foreach ($cpt_details as $key => $cpts) {
            $adjDetails = ClaimCPTOthersAdjustmentInfoV1::with('adjustment_details')
                          ->where('claim_id', $claim_id)
                          ->where('claim_cpt_id', $cpts->claim_cpt_info_id)
                          ->where('claim_cpt_tx_id', $cpts->id)->get();
            //\Log::info("Claim ");\Log::info($adjDetails);
            if(!empty($adjDetails)) {
              foreach ($adjDetails as $adj) {
                $adj_code = ($adj->adjustment_id != 0) ? @$adj->adjustment_details->adjustment_shortname : 'CO253';
                // Combine adjustment amount based on code
                $adj_resp[$adj_code]['adj_code'] = $adj_code;
                $adj_resp[$adj_code]['adj_amt'] = isset($adj_resp[$adj_code]['adj_amt']) ? ($adj_resp[$adj_code]['adj_amt']+$adj->adjustment_amt) : $adj->adjustment_amt;                
                //$adj_resp[] = ['cpt' => $adj->claim_cpt_id, 'adj_id' => $adj->adjustment_id, 'adj_code' => $adj_code, 'adj_amt' => $adj->adjustment_amt];                
              }
            } 
          } 
        }
      }
      return $adj_resp;
    }

    public static function getClaimLastCopayDetails($claim_id, $cpt_id=0){
      $resp = '';
      try {
        if($cpt_id == 0) {
          $txnDet = PMTClaimTXV1::where('claim_id', $claim_id)
                      ->where(function($qry){
                        $qry->where('total_deduction', '<>', 0)->orWhere('total_coins', '<>', 0)->orWhere('total_copay', '<>', 0);
                      })
                      ->orderBy('id', 'DESC')->first();
          if(!empty($txnDet)) {          
            if ($txnDet->total_deduction != 0) {
              $resp = "Deductible";
            }

            if ($txnDet->total_coins != 0) {
              $resp = ($resp != "") ? $resp." / "."Co-Insurance" :"Co-Insurance";
            }

            if ($txnDet->total_copay != 0) {
              $resp = ($resp != "") ? $resp." / "."Copay" : "Copay";
            }
          }            

        } else {

          $txnDet = PMTClaimCPTTXV1::where('claim_id', $claim_id)->where('claim_cpt_info_id', $cpt_id)
                      ->where(function($qry){
                        $qry->where('deduction', '<>', 0)->orWhere('coins', '<>', 0)->orWhere('copay', '<>', 0);
                      })
                      ->orderBy('id', 'DESC')->first();
          if(!empty($txnDet)) {
            
            if ($txnDet->deduction != 0) {
              $resp = "Deductible";
            }

            if ($txnDet->coins != 0) {
              $resp = ($resp != "") ? $resp." / "."Co-Insurance" :"Co-Insurance";
            }

            if ($txnDet->copay != 0) {
              $resp = ($resp != "") ? $resp." / "."Copay" : "Copay";
            }
          }
        }
      } catch(Exception $e){
        \Log::info("While get last deductible details error occured. msg: ".$e->getMessage() );
      }
      return $resp;
    }

    public static function getDeniedClaimCount() {
        $denied_count =   DB::table('claim_info_v1')->select(DB::raw('sum(status = "Denied") denied_count'))
                                ->whereNull('deleted_at')->value('denied_count');
        return $denied_count;
    }  
}