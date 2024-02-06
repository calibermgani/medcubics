<?php namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Helpers\Helpers as Helpers;

class PatientInsurance extends Model
{	
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'patient_insurance';
	
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
		
	public function insurance_details() 
	{
        return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id')->select()->with('insurancetype');
    }

	public function patient_authorization()
	{
		return $this->belongsTo('App\Models\Patients\PatientAuthorization', 'insurance_id','insurance_id');
	}

	public function insurance_type_details() 
	{
        return $this->belongsTo('App\Models\Insurancetype', 'insurancetype_id', 'id');
    }

    public function patient() 
	{
        return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id')->select('id', 'gender', 'address1', 'address2', 'city', 'state', 'zip5', 'zip4', 'phone', 'last_name', 'first_name', 'middle_name');
    }

	protected $fillable = [ 	
		'patient_id',
		'insurance_id',
		'medical_secondary_code',
		'category',
		'relationship',
		'last_name',
		'first_name',
		'middle_name',
		'insured_ssn',
		'insured_dob',
		'insured_address1',
		'insured_address2',
		'insured_city',
		'insured_state',
		'insured_zip5',
		'insured_zip4',
		'policy_id',
		'group_name',
		'phone',
		'insured_gender',
		'effective_date',
		'termination_date',
		'adjustor_ph',
		'adjustor_fax',
		'accept_assignment',
		'release_of_information',
		'insurance_notes',
		'orderby_category',
		'document_save_id',
		'same_patient_address',
		'active_from',
		'active_to',
		'updated_by',
		'created_by'			
	];
	
	public static $rules = [ 
			'category' => 'required',
			'phone' =>	'nullable|min:10'	
	];
		
	public static $messages = [	
		'category.required' => 'Select your category!',
		'first_name.required' => 'Select your category!',
		'phone.min' => 'Phone number is 10 digits'
	];

	public static function gettabinsurance($patient_id)
    {
        $patient_insurances = PatientInsurance::with(array('insurance_details'=>function($query){ 
	        					$query->select('id','insurance_name');
	        				}))
            				->orderBy('category', 'ASC')->where('patient_id', $patient_id)->get();   //To get patient insurance on tabs 
        $tab_insurance = array(); 
        if(!empty($patient_insurances)) {        
            foreach($patient_insurances as $patient_insurance) {                   
                    $tab_insurance[$patient_insurance->category]['insurancename'] = @$patient_insurance->insurance_details->insurance_name;
            }
        }
        return $tab_insurance;
    }

    public static function CheckAndReturnInsuranceName($patient_id, $is_need_primary = 'no')
    {
    	$query = PatientInsurance::with('insurance_details')->where('patient_id',$patient_id);
    	$insured_details = $query->where('category','Primary')->whereNull('deleted_at')->first();

    	if($is_need_primary == 'yes')
    	{
    		$insurance = [];
    		if($insured_details)
	    	{
	    		$insurance['name'] = str_limit(@$insured_details->insurance_details->insurance_name,30,'...');
	    		$insurance['policy_id'] = $insured_details->policy_id;
				$insurance['eligibility_verification'] = $insured_details->eligibility_verification;
				$insurance['id'] 			= $insured_details->id;
				$insurance['patient_id'] 	= $insured_details->patient_id;
				$insurance['category'] 		= $insured_details->category;
	    	} 
	    	return $insurance;
    	}
    	else
    	{
	    	if(!$insured_details)
	    	{
	    		$insured_details = PatientInsurance::with('insurance_details')->where('patient_id',$patient_id)->where('category','Secondary')->whereNull('deleted_at')->first();
	    		if(!$insured_details)
	    			$insured_details = PatientInsurance::with('insurance_details')->where('patient_id',$patient_id)->whereNull('deleted_at')->orderBy('id','ASC')->first();
	    	}
	    	$insurance_name = '';
	    	if($insured_details)
	    		$insurance_name = str_limit(@$insured_details->insurance_details->insurance_name,30,'...');
	        return $insurance_name;
	    }
    }

    public static function getAllPatientInsuranceList(){
    	$insurance_data = $primary = $secondary= $others = $all = [];
    	//$insurance_names = Helpers::getInsuranceNameLists();
    	$primary_insurance = PatientInsurance::with('insurance_details')->where('category','Primary')->whereNull('deleted_at')->groupBy('patient_id')->orderBy('patient_id', 'ASC')->orderBy('insurance_id', 'ASC')->select('patient_id','insurance_id')->get();
    	if(!empty($primary_insurance)) {        			
    		foreach($primary_insurance as $primary_ins){ 	
    			$ins_name = @$primary_ins->insurance_details->short_name;
    			$primary[$primary_ins->patient_id] =  $all[$primary_ins->patient_id] =  str_limit(@$ins_name,30,'...');
    		}
    	}

    	$secondary_insurance = PatientInsurance::with('insurance_details')->where('category','Secondary')->whereNull('deleted_at')->groupBy('patient_id')->orderBy('patient_id', 'ASC')->orderBy('insurance_id', 'ASC')->select('patient_id','insurance_id')->get();
    	if(!empty($secondary_insurance)) {        			
    		foreach($secondary_insurance as $secondary_ins){ 	
    			$ins_name = @$secondary_ins->insurance_details->short_name;
    			$secondary[$secondary_ins->patient_id] =  str_limit(@$ins_name,30,'...');
				if(empty($all[$secondary_ins->patient_id]))
					$all[$secondary_ins->patient_id] =  str_limit(@$ins_name,30,'...');
    		}
    	}

    	$other_insurance = PatientInsurance::with('insurance_details')->where('category','<>','Primary')->where('category','<>','Secondary')->whereNull('deleted_at')->groupBy('patient_id')->orderBy('patient_id', 'ASC')->orderBy('insurance_id', 'ASC')->select('patient_id','insurance_id')->get();
    	if(!empty($other_insurance)) {        
			foreach($other_insurance as $other_ins){ 
				$ins_name = @$other_ins->insurance_details->short_name;
				$others[$other_ins->patient_id] =  str_limit(@$ins_name,30,'...');
				if(empty($all[$other_ins->patient_id]))
					$all[$other_ins->patient_id] =  str_limit(@$ins_name,30,'...');
			}
		}
    	
    	$insurance_data['primary'] = $primary;
    	$insurance_data['secondary'] = $secondary;
    	$insurance_data['others'] = $others;
    	$insurance_data['all'] = $all;
    	return $insurance_data; 
	}
	
	public static function getAllPatientInsuranceFullNameList(){
    	$insurance_data = $primary = $secondary= $others = $all = [];
    	//$insurance_names = Helpers::getInsuranceNameLists();
    	$primary_insurance = PatientInsurance::with('insurance_details')->where('category','Primary')->whereNull('deleted_at')->groupBy('patient_id')->orderBy('patient_id', 'ASC')->orderBy('insurance_id', 'ASC')->select('patient_id','insurance_id')->get();
    	if(!empty($primary_insurance)) {        			
    		foreach($primary_insurance as $primary_ins){ 	
    			$ins_name = @$primary_ins->insurance_details->insurance_name;
    			$primary[$primary_ins->patient_id] =  $all[$primary_ins->patient_id] =  str_limit(@$ins_name,30,'...');
    		}
    	}

    	$secondary_insurance = PatientInsurance::with('insurance_details')->where('category','Secondary')->whereNull('deleted_at')->groupBy('patient_id')->orderBy('patient_id', 'ASC')->orderBy('insurance_id', 'ASC')->select('patient_id','insurance_id')->get();
    	if(!empty($secondary_insurance)) {        			
    		foreach($secondary_insurance as $secondary_ins){ 	
    			$ins_name = @$secondary_ins->insurance_details->insurance_name;
    			$secondary[$secondary_ins->patient_id] =  str_limit(@$ins_name,30,'...');
				if(empty($all[$secondary_ins->patient_id]))
					$all[$secondary_ins->patient_id] =  str_limit(@$ins_name,30,'...');
    		}
    	}

    	$other_insurance = PatientInsurance::with('insurance_details')->where('category','<>','Primary')->where('category','<>','Secondary')->whereNull('deleted_at')->groupBy('patient_id')->orderBy('patient_id', 'ASC')->orderBy('insurance_id', 'ASC')->select('patient_id','insurance_id')->get();
    	if(!empty($other_insurance)) {        
			foreach($other_insurance as $other_ins){ 
				$ins_name = @$other_ins->insurance_details->insurance_name;
				$others[$other_ins->patient_id] =  str_limit(@$ins_name,30,'...');
				if(empty($all[$other_ins->patient_id]))
					$all[$other_ins->patient_id] =  str_limit(@$ins_name,30,'...');
			}
		}
    	
    	$insurance_data['primary'] = $primary;
    	$insurance_data['secondary'] = $secondary;
    	$insurance_data['others'] = $others;
    	$insurance_data['all'] = $all;
    	return $insurance_data; 
    }

    public static function getPatientInsuranceDetailsById($patient_id, $patient_insurance_id, $category)
    {
    	return PatientInsurance::where('id',$patient_insurance_id)->first();
    }
    public static function getPatientInsuranceById($patient_id, $patient_insurance_id)
    {
    	return PatientInsurance::where('patient_id',$patient_id)->where('id',$patient_insurance_id)->first();
    }

	public static function getAllPatientInsurance()
    {
		$patient_insurances = PatientInsurance::with(array('insurance_details'=>function($query){ $query->select('id','insurance_name');}))->whereHas('insurance_details', function($q){ $q->where('status','Active'); })->orderBy('insurance_id', 'ASC')->get();
		$insurance = array(); 
		if(!empty($patient_insurances)) {        
			foreach($patient_insurances as $patient_insurance){                   
				$insurance[$patient_insurance->insurance_id] = @$patient_insurance->insurance_details->insurance_name;
			}
		}
		return $insurance;
    }
	
	/*** getting patient id for coverage detail starts ***/
	public static function getPatientCoverageDetail()
    {
		$all_patient = Patient::pluck('id')->all();
		$all_patient_claim = ClaimInfoV1::pluck('id')->all();
		$primary_patient = PatientInsurance::where('category','Primary')->pluck('patient_id')->all();
		//$other_patient = PatientInsurance::where('category',"!=",'Primary')->pluck('patient_id')->all();
		$other_patient = array_diff($all_patient,$primary_patient);
		$result['primary_ins_patient'] = $primary_patient;
		$result['other_ins_patient'] = $other_patient;
		return $result;
    }
	/*** getting Last App Date for patients ends ***/
	
	/*** getting patient id for Patient detail starts ***/
	public static function CheckPatientStatus($type)
    {
		$all_patient = Patient::pluck('id')->all();
		$existing_id = ClaimInfoV1::pluck('patient_id')->all();
		$new_patient = array_diff($all_patient,$existing_id);
		return ($type =="new") ? $new_patient : $existing_id;
    }
	/*** getting Last App Date for patients ends ***/
	public function getPatientclaimDetail()
	{
		$all_patient = Patient::pluck('id')->all();
		$all_patient_claim = ClaimInfoV1::pluck('id')->all();
		$other_patient = array_diff($all_patient,$all_patient_claim);
		if($other_patient)
			$result = "Existing";
		else
			$result = "New";
		return $result;
	}

	public function PatientCheckwithClaimBasis($id)
	{
		$claim_count = ClaimInfoV1::where("patient_id",$id)->count();
		$result = ($claim_count >0) ?  "Existing" : "New";
		return $result;
	}

	/*** Get Type Based Insurance Name Start***/
	public static function getTypeBasedInsuranceName($type)
    {
    	$patient_insurances = PatientInsurance::with(array('insurance_details'=>function($query){ $query->select('id','insurance_name')->where("status","Active");}))->where('category',$type)->get();
		$insurance = array(); 
		if(!empty($patient_insurances)) {        
			foreach($patient_insurances as $patient_insurance){                   
					$insurance[$patient_insurance->insurance_id] = @$patient_insurance->insurance_details->insurance_name;
			}
		}
		return $insurance;
    }
	/*** Get Type Based Insurance Name End***/
	
	/*** Patient Insurance tab model  self or Insurance button click ***/
	public static function patientInsCount($patient_id) 
	{
		$pat_ins_count = PatientInsurance::where('patient_id',$patient_id)->whereIn('category',['primary','Secondary','Tertiary'])->get();
		$count_pat_ins = count($pat_ins_count);
		return $count_pat_ins;
	}
	
	public static function getInsurance($patient_id) {
         $getpatientinsurances = PatientInsurance::whereIn('category', ['Primary', 'Secondary', 'Tertiary'])
								->with('insurance_details')->where('patient_id', $patient_id)
		                        ->orderBy('orderby_category', 'asc')->get();

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
}