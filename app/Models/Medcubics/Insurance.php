<?php
namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Lang;
use Config;

class Insurance extends Model
{	
    use SoftDeletes;
    protected $dates = ['deleted_at'];
	protected $connection = 'responsive';	
	
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
	
	public function insurancetype()
	{
		return $this->belongsTo('App\Models\Medcubics\Insurancetype','insurancetype_id','id');
	}
	public function insuranceclass()
	{
		return $this->belongsTo('App\Models\Medcubics\Insuranceclass','insuranceclass_id','id');
	}
	public function claimtype()
	{
		return $this->belongsTo('App\Models\Medcubics\Claimtype','claimtype_id','id');
	}        
	public function claimformat()
	{
		return $this->belongsTo('App\Models\Medcubics\Claimformat','claimformat_id','id');
	}
	public function patient_insurance() 
	{		
        return $this->belongsto('App\Models\Patients\PatientInsurance', 'id', 'insurance_id');
    }
	protected $fillable = [ 
            'insurance_name',
            'short_name',
			'insurance_desc',
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
			'payerid',
			'managedcareid',
			'medigapid',
			'era_payerid',
			'eligibility_payerid',
            'feeschedule',
            'primaryfiling',
			'secondaryfiling',
			'appealfiling',
			'claimtype',
			'claim_ph',
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
			'eligibility_fax',
			'eligibility_fax2',
			'enrollment_fax',
			'prior_fax',
			'status'
	];
	
	public static $rules = [
			'insurance_name' => 'required',
			'short_name'	 => 'required|min:3|max:7',
			//'address_1' 	 => 'required|regex:/^[A-Za-z0-9 \t]*$/i',
			'address_2'  	 =>'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
			'city' 			 => 'required|regex:/^[A-Za-z0-9 \t]*$/i',
			'state'  		 => 'required|min:2|regex:/^[A-Za-z0-9 \t]*$/i',
			'zipcode5' 		 => 'required|digits:5',
			'zipcode4' 		 => 'nullable|min:4|max:4',
			'email' 		 => 'nullable|email',
			'website'  		 => 'nullable|url'	
	];

	public static function messages()
	{
		return [
			'insurance_name.required' 	=> Lang::get("admin/insurance.validation.insurance_name"),
			'short_name.required'		=> Lang::get("admin/insurance.validation.short_name"),
			'short_name.max'			=> Lang::get("common.validation.shortname_regex"),
			'short_name.min'			=> Lang::get("common.validation.shortname_regex"),
			'address_1.required' 	=> Lang::get("common.validation.address1_required"),
			'address_1.insuniqueaddress'=> Lang::get('admin/insurance.validation.address_unique'),
			'address_1.regex' 		=> Lang::get("common.validation.alphanumericspac"),
			'address_2.regex' 		=> Lang::get("common.validation.alphanumericspac"),
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
			'image.mimes'				=> Config::get('siteconfigs.customer_image.defult_image_message'),
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
}
