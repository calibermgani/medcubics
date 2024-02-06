<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
use Config;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Patients\Patient as Patient;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Customer as Customer;
use Auth;
use Lang;
use DB;
use Cache;

class Practice extends Model {

    use SoftDeletes;

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

    public function speciality_details() {
        return $this->belongsTo('App\Models\Speciality', 'speciality_id', 'id');
    }

    public function taxanomy_details() {
        return $this->belongsTo('App\Models\Taxanomy', 'taxanomy_id', 'id');
    }

    public function languages_details() {
        return $this->belongsTo('App\Models\Language', 'language_id', 'id');
    }

    protected $fillable = [
        'practice_name', 'email', 'phone', 'phoneext', 'fax', 'website', 'facebook', 'twitter', 'practice_description', 'practice_link', 'doing_business_s', 'entity_type', 'billing_entity', 'tax_id', 'speciality_id', 'taxanomy_id', 'language_id', 'entity_type', 'tax_id', 'group_tax_id', 'npi', 'group_npi', 'medicare_ptan', 'medicaid', 'mail_add_1',
        'mail_add_2', 'mail_city', 'mail_state', 'mail_zip5', 'mail_zip4', 'pay_add_1', 'pay_add_2', 'pay_city', 'pay_state', 'pay_zip5', 'pay_zip4', 'primary_add_1', 'primary_add_2', 'primary_city', 'primary_state', 'primary_zip5', 'primary_zip4', 'monday_forenoon', 'tuesday_forenoon', 'wednesday_forenoon', 'thursday_forenoon', 'friday_forenoon', 'saturday_forenoon', 'sunday_forenoon', 'monday_afternoon', 'tuesday_afternoon', 'wednesday_afternoon', 'thursday_afternoon', 'friday_afternoon', 'saturday_afternoon', 'sunday_afternoon','bcbs_id','backDate', 'timezone', 'icd_autopopulate'
    ];
    public static $rules = [
        'doing_business_s' => 'required|max:100',
        'speciality_id' => 'required|not_in:0',
        'taxanomy_id' => 'required|not_in:0',
        'billing_entity' => 'required',
        //'mail_add_1' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        //'mail_add_2' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        'mail_city' => 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
        'mail_state' => 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
        'entity_type' => 'required',
        //'pay_add_1' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        //'pay_add_2' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        'pay_state' => 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
        'pay_city' => 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
        'pay_zip5' => 'nullable|numeric|min:5',
        //'primary_add_1' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        //'primary_add_2' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        'primary_state' => 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
        'primary_city' => 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
        'mail_zip5' => 'nullable|numeric|min:5',
        'primary_zip5' => 'nullable|numeric|min:5'
    ];

    public static function messages() {
        return [
            'doing_business_s.required' => Lang::get("practice/practicemaster/practice.validation.doingbusinessus"),
            'doing_business_s.max' => Lang::get("practice/practicemaster/practice.validation.doingbusinessus_limit"),
            'speciality_id.required' => Lang::get("practice/practicemaster/practice.validation.speciality"),
            'speciality_id.not_in' => Lang::get("practice/practicemaster/practice.validation.speciality"), 'taxanomy_id.required' => Lang::get("practice/practicemaster/practice.validation.taxanomy"),
            'taxanomy_id.not_in' => Lang::get("practice/practicemaster/practice.validation.taxanomy"),
            'billing_entity.required' => Lang::get("practice/practicemaster/practice.validation.billing_entity"),
            'tax_id.required' => Lang::get("practice/practicemaster/practice.validation.taxid"),
            'tax_id.digits' => Lang::get("practice/practicemaster/practice.validation.taxid"),
            //'npi.required' 			=> Lang::get("common.validation.npi"),
            //'npi.digits' 			 => Lang::get("common.validation.npi"),
            'group_tax_id.required' => Lang::get("practice/practicemaster/practice.validation.group_tax_id"),
            'group_tax_id.digits' => Lang::get("common.validation.numeric"),
            'group_npi.required' => Lang::get("practice/practicemaster/practice.validation.billing_entity"),
            'group_npi.digits' => Lang::get("common.validation.numeric"),
            'medicare_ptan.required' => Lang::get("practice/practicemaster/practice.validation.billing_entity"),
            'medicare_ptan.digits' => Lang::get("common.validation.numeric"),
            'medicaid.required' => Lang::get("practice/practicemaster/practice.validation.billing_entity"),
            'medicaid.digits' => Lang::get("common.validation.numeric"),
            'entity_type.required' => Lang::get("practice/practicemaster/practice.validation.billing_entity"),
            //'mail_add_1.regex' => Lang::get("common.validation.alphanumericspac"),
            //'mail_add_2.regex' => Lang::get("common.validation.alphanumericspac"),
            'mail_city.regex' => Lang::get("common.validation.alphanumericspac"),
            'mail_state.regex' => Lang::get("common.validation.alpha"),
            'mail_zip5.numeric' => Lang::get("common.validation.zipcode5_limit"),
            'mail_zip5.min' => Lang::get("common.validation.zipcode5_limit"),
            //'pay_add_1.regex' => Lang::get("common.validation.alphanumericspac"),
            //'pay_add_2.regex' => Lang::get("common.validation.alphanumericspac"),
            'pay_city.regex' => Lang::get("common.validation.alphanumericspac"),
            'pay_state.regex' => Lang::get("common.validation.alpha"),
            'pay_zip5.numeric' => Lang::get("common.validation.zipcode5_limit"),
            'pay_zip5.min' => Lang::get("common.validation.zipcode5_limit"),
            //'primary_add_1.regex' => Lang::get("common.validation.alphanumericspac"),
            //'primary_add_2.regex' => Lang::get("common.validation.alphanumericspac"),
            'primary_city.regex' => Lang::get("common.validation.alphanumericspac"),
            'primary_state.regex' => Lang::get("common.validation.alpha"),
            'primary_zip5.numeric' => Lang::get("common.validation.zipcode5_limit"),
            'primary_zip5.min' => Lang::get("common.validation.zipcode5_limit")
        ];
    }

    public static function getPracticeDetails() {
        $id = Session::get('practice_dbid');
        if ($id == '' || $id == 0)
            $id = 4;
        $practice = Cache::remember('practice_details'.$id , 30, function() use($id) {            
            $practice = Practice::where('id', $id)->select('practice_name', 'timezone','avatar_name', 'avatar_ext')->first();
            return $practice;
        });
        $details['practice_name'] = @$practice->practice_name;
        $details['practice_image'] = [@$practice->avatar_name, @$practice->avatar_ext];

        return $details;
    }
	
	public static function getPracticeIcdInfo(){
		$id = Session::get('practice_dbid');
		$practice = Practice::where('id', $id)->select('icd_autopopulate')->first();
		$details['icd_autopopulate'] = @$practice->icd_autopopulate;

        return $details;
	}
	

    public static function getPracticeName($practice_id = '') {
        if(!empty($practice_id))
			$id = $practice_id;
		else
			$id = (Session::has('practice_dbid')) ? Session::get('practice_dbid') : '';
        if ($id == '' || $id == 0)
            $id = 4;

        $practice = Cache::remember('practice_details'.$id , 30, function() use($id) {            
            $practice = Practice::where('id', $id)->select('practice_name', 'timezone','avatar_name', 'avatar_ext')->first();
            return $practice;
        });        
        return @$practice->practice_name;
    }

    public static function getPracticeImg() {
        $id = Session::get('practice_dbid');
        if ($id == '' || $id == 0)
            $id = 4;
        $practice = Cache::remember('practice_details'.$id , 30, function() use($id) {            
            $practice = Practice::where('id', $id)->select('practice_name', 'timezone','avatar_name', 'avatar_ext')->first();
            return $practice;
        });        
        return [@$practice->avatar_name, @$practice->avatar_ext];
    }

    public static function getCustomerImg() {
        $id = Auth::user()->customer_id;
        if ($id == '0') {
            $id = Auth::user()->id;
        }
        $customer = Customer::where('id', $id)->first();
        return [$customer->avatar_name, $customer->avatar_ext];
    }

    public static function getProviderCount($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
        $practice_name = Practice::where('id', $practice_id)->pluck('practice_name')->first();
        $practice_name = str_replace(" ", "_", strtolower($practice_name));
        //$providers 		= DB::select("select count(*) as provider_count where from $practice_name.providers where deleted_at is null AND provider_types_id is 1 ");
        $providers = Provider::where('provider_types_id', 1)->where('practice_id',$practice_id)->count();
        return $providers;
    }

    public static function getFacilityCount($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
        $practice_name = Practice::where('id', $practice_id)->pluck('practice_name')->first(); 
        $practice_name = str_replace(" ", "_", strtolower($practice_name));
        // $facilities = DB::select("select count(*) as facility_name from $practice_name.facilities where deleted_at is null ");
		// return $facilities[0]->facility_name;
		$facilities = Facility::where('status', 'Active')->count();
        return $facilities;
    }

    public static function getPatientrCount($practice_id) {
        return Patient::where('status', 'Active')->count();
    }

    public static function getVistiCount($practice_id) {
        return PatientAppointment::whereIn('status', ['In Session', 'Complete'])->count();
    }

    public static function getClaimCount($practice_id) {
        return ClaimInfoV1::count();
    }

    public static function getCollectionCount($practice_id) {
        return '$' . Helpers::priceFormat(ClaimInfoV1::sum('total_paid'));
    }

    public static function getmediapracticenamefromdb($practice_id) {
        $practice = Cache::remember('practice_details'.$practice_id , 30, function() use($practice_id) {
            $practice = Practice::where('id', $practice_id)->select('practice_name', 'timezone','avatar_name', 'avatar_ext')->first();            
            return $practice;
        });
        //$name = Practice::where('id', $practice_id)->pluck('practice_name')->first();
        Config::set('app.practice_name', $practice->practice_name);
    }

    public static function getPracticeUserCount($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
        return Users::where('id',Auth::id())->count()+Setpracticeforusers::where('practice_id',$practice_id)->count()+Users::whereRaw('FIND_IN_SET("'.$practice_id.'",admin_practice_id)')->where('practice_user_type','practice_admin ')->count();
    }
    // Practice Show page stats icon 
    public static function getPatientOSamount() {
        $patientOutstandingAR = Helpers::getOutstandingPateientAr();
        return '$' . Helpers::priceFormat($patientOutstandingAR);
    }

    public static function getInsOSamount() {
        $insuranceOutstandingAR = Helpers::getOutstandingInsuranceAr();
        return '$' . Helpers::priceFormat($insuranceOutstandingAR);
        
    }

}
