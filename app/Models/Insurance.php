<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Lang;
use Config;

class Insurance extends Model
{	
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

    protected $table = "insurances";
    protected $fillable = [ 
		'insurance_name', 
		'short_name',
		'insurance_desc',
		'avatar_name',
		'avatar_ext',
		'address_1',
		'address_2',
		'city',
		'state',
		'zipcode5',
		'zipcode4',
		'phone1',
		'phoneext',
		'fax',
		'email',
		'website',
		'enrollment',
		'insurancetype_id',
		'insuranceclass_id',
		'managedcareid',
		'medigapid',
		'payerid',
 		'era_payerid',			
 		'eligibility_payerid',
 		'feeschedule',
 		'primaryfiling',
		'secondaryfiling',
		'appealfiling',
		'claim_ph',
		'claimtype',		
		'claim_ext',		
		'eligibility_ph',
		'eligibility_ext',
		'eligibility_ph2',
		'eligibility_ext2',
		'enrollment_ph',
		'enrollment_ext',
		'prior_ph',
		'prior_ext',
		'claim_fax',
		'claimtype',
		'eligibility_fax',
		'eligibility_fax2',
		'enrollment_fax',
		'prior_fax',
		'status'	];


	public function insurancetype()
	{		
		return $this->belongsTo('App\Models\Insurancetype','insurancetype_id', 'id')->select('id','code','type_name','cms_type');
	}
	
	public function insuranceclass()
	{
		return $this->belongsTo('App\Models\Insuranceclass','insuranceclass_id','id');
	}
	
	public function claimtype()
    {
        return $this->belongsTo('App\Models\Claimtype','claimtype_id','id');
    }        
	
    public function claimformat()
    {
        return $this->belongsTo('App\Models\Claimformat','claimformat_id','id');
    }
	
	public function patient_insurance() 
	{		
        return $this->belongsto('App\Models\Patients\PatientInsurance', 'id', 'insurance_id');
    }
    public function patient_authorization() 
	{		
        return $this->belongsto('App\Models\Patients\PatientAuthorization', 'id', 'insurance_id');
    }
    public function patient_document_insurance() {
        return $this->hasMany('App\Models\Document', 'payer', 'id');
    }
	
	public static $rules = 
	[
		'insurance_name' 	=> 'required',
		//'short_name' 		=> 'min:3|max:13',
		//'address_1' 		=> 'required|regex:/^[A-Za-z0-9 ]+$/i',
		//'address_2' 		=> 'regex:/^[A-Za-z0-9 ]+$/i',
		'city' 				=> 'required|regex:/^[A-Za-z0-9 ]+$/i',
		'state' 			=> 'required|regex:/^[A-Za-z]+$/ii',
		'zipcode5' 			=> 'required|digits:5',
		'zipcode4' 			=> 'nullable|min:4|max:4',
		'email' 			=> 'nullable|email',
		'website' 			=> 'nullable|url'	
	];
	
	public static function messages()
	{
		return [
			'insurance_name.required' 	=> Lang::get("practice/practicemaster/insurance.validation.insurance_name"),
			//'short_name.required'		=> Lang::get("practice/practicemaster/insurance.validation.short_name"),
			//'short_name.max'			=> Lang::get("common.validation.shortname_regex"),
			//'short_name.min'			=> Lang::get("common.validation.shortname_regex"),
			'address_1.required' 		=> Lang::get("common.validation.address1_required"),
			'address_1.insuniqueaddress'=> Lang::get('practice/practicemaster/insurance.validation.address_unique'),
			//'address_1.regex' 			=> Lang::get("common.validation.alphanumericspac"),
			//'address_2.regex' 			=> Lang::get("common.validation.alphanumericspac"),
			'city.required'				=> Lang::get("common.validation.city_required"),
			'state.required'			=> Lang::get("common.validation.state_required"),
			'city.regex'				=> Lang::get("common.validation.alphanumericspac"),
			'state.regex'				=> Lang::get("common.validation.alpha"),
			'zipcode5.required'			=> Lang::get("common.validation.zipcode5_required"),
			'zipcode5.digits'			=> Lang::get("common.validation.zipcode5_limit"),
			'zipcode4.min'				=> Lang::get("common.validation.zipcode4_limit"),
			'zipcode4.max'				=> Lang::get("common.validation.zipcode4_limit"),
			'email.email'				=> Lang::get("common.validation.email_valid"),
			'website.url'				=> Lang::get("common.validation.website_valid"),
			'image.mimes'				=> Config::get('siteconfigs.customer_image.defult_image_message')
			
		];
	}
	
	public static function getInsuranceName($insuranceId)
	{
		if($insuranceId > 0) {
			$insurance_name = Insurance::where('id',$insuranceId)->pluck('insurance_name')->first();
		} else {
			$insurance_name = '';
		}
		return $insurance_name;
	}

	public static function getInsuranceshortName($insuranceId)
	{
		if($insuranceId > 0) {
			$insurance_name = Insurance::where('id',$insuranceId)->pluck('short_name')->first();
		} else {
			$insurance_name = '';
		}
		return $insurance_name;
	}
	
	/*** Export in patient side ***/
	public static function InsuranceName($insurance_id)
    {
		if($insurance_id ==0 || $insurance_id ==null|| $insurance_id =='')	{
			$result = "Self";
		} else	{
			$result = Insurance::where('id',$insurance_id)->pluck("insurance_name")->first();
		}
		return $result;
    }
	/*** Export in patient side ***/
	
	public function scopeGetInsurancePayerId($query, $insurance_id)
	{
		return $query->where('id', $insurance_id)->where('status','Active');
	}
	
	public static function shortNameUnique($short_name)
	{
		$unique_val = Insurance::where('short_name',$short_name)->count();
		return $unique_val;		
	}

}