<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model {

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

    public function facility_address() {
        return $this->hasOne('App\Models\Facilityaddress', 'facilityid', 'id');
    }

    public function speciality_details() {
        return $this->belongsTo('App\Models\Speciality', 'speciality_id', 'id');
    }

    public function pos_details() {
        return $this->belongsTo('App\Models\Pos', 'pos_id', 'id');
    }

    public function provider_details() {
        return $this->belongsTo('App\Models\Provider', 'default_provider_id', 'id');
    }
    
    public function county() {
        return $this->belongsTo('App\Models\County', 'county', 'id');
    }
    
    public function taxanomy_details()
    {
        return $this->belongsTo('App\Models\Taxanomy','taxanomy_id','id');
    }
    
    protected $fillable = [
        'facility_name', 'description', 'speciality_id', 'taxanomy_id','phone','fax','email','website','county', 'facility_tax_id', 'facility_npi', 'clia_number', 'pos_id', 'default_provider_id', 'fda', 'claim_format', 'scheduler', 'superbill', 'statement_address', 'medication_prescr', 'credit_cart_accepted',
        'no_of_visit_per_week', 'facility_manager', 'facility_manager_phone', 'facility_manager_ext', 'facility_manager_email', 'facility_biller', 'facility_biller_phone', 'facility_biller_ext',
        'facility_biller_email', 'status', 'monday_forenoon','tuesday_forenoon','wednesday_forenoon','thursday_forenoon','friday_forenoon','saturday_forenoon','sunday_forenoon','monday_afternoon','tuesday_afternoon','wednesday_afternoon','thursday_afternoon','friday_afternoon','saturday_afternoon','sunday_afternoon'
    ];
    public static $rules = [
        'facility_name' => 'required|unique:facilities,facility_name',
       // 'description' => 'required',
        //'phone' => 'required',
        //'fax' => 'required',
        'email' => 'email',
        //'speciality_id' => 'required',
       // 'taxanomy_id' => 'required',
        'facility_tax_id' => 'digits:9',
        'facility_npi' => 'required|digits:10',
       // 'clia_number' => 'required',
        'claim_format' => 'required',
        'pos_id' => 'required',
       // 'fda' => 'required|alpha_num|max:15',
        //'facility_manager' => 'required',
        //'facility_manager_phone' => 'required',
        'facility_manager_email' => 'email',
        //'facility_biller' => 'required',
        //'facility_biller_phone' => 'required',
        'facility_biller_email' => 'email'
    ];
    public static $messages = [
        'facility_name.required' => 'Enter your facility name!',
		'facility_name.unique' => 'Must be unique',
       // 'description.required' => 'Enter your facility description!',
        //'email.required' => 'Enter email',
        'email.email' => 'Enter valid email',
        //'phone.required' => 'Enter phone number',
        //'fax.required' => 'Enter fax',
        'website.url' => 'Enter valid website',
        //'speciality_id.required' => 'Select your speciality!',
        //'taxanomy_id.required' => 'Select your taxanomy!',
        //'facility_tax_id.required' => 'Enter facility tax ID!',
        'facility_tax_id.digits' => 'Your facility tax ID will be 9 digits!',
        'facility_npi.required' => 'Enter facility NPI!',
        'facility_npi.digits' => 'Your NPI will be 10 digits!',
        //'clia_number.required' => 'Enter your CLIA number!',
        'claim_format.required' => 'Select claim Format!',
        'pos_id.required' => 'Select your place of service (POS)!',
        //'fda.required' => 'Enter your FDA!',
        'fda.alpha_num' => 'FDA should contain only letters and numbers!',
        'fda.max' => 'FDA should not be greater than 15 characters!',
        //'facility_manager.required' => 'Enter your facility manager name!',
       // 'facility_manager_phone.required' => 'Enter your facility manager phone number!',
       // 'facility_manager_email.required' => 'Enter your facility manager email!',
        'facility_manager_email.email' => 'Enter valid facility manager email!',
       // 'facility_biller.required' => 'Enter your facility biller name!',
        //'facility_biller_phone.required' => 'Enter your facility biller phone number!',
       // 'facility_biller_email.required' => 'Enter your facility biller email!',
        'facility_biller_email.email' => 'Enter valid facility biller email!'
    ];
  public static function getfacilitydetail($id)
   {
	   $facility = Facility::find($id);
	   $address = $facility->facility_address->address1;
	   $city = $facility->facility_address->city;
	   $pos = $facility->pos_details->pos;
	   $value = new Facility();
	   $value->city = $city;
	   $value->address = $address;	
	   $value->pos = $pos;		  
	   return $value;
   }  

}
