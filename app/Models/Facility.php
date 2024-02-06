<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Lang;
use Config;

class Facility extends Model 
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    public function facility_address() 
	{
        return $this->hasOne('App\Models\Facilityaddress', 'facilityid', 'id');
    }
    
	public static function connecttoadmindatabase() {
		$db = new DBConnectionController();
		$admin_database = getenv('DB_DATABASE');
		$db->configureConnectionByName($admin_database);
		Config::set('database.default',$admin_database);
	}

	public function connecttopracticedb() 
	{
		$db = new DBConnectionController();
		$db->connectPracticeDB(Session::get('practice_dbid'));
	} 	

    public function speciality_details()
	{
        return $this->belongsTo('App\Models\Speciality', 'speciality_id', 'id')->select('id','speciality');
    }

	public function facility_user_details()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','created_by','id');
	}

    public function pos_details()
	{
        return $this->belongsTo('App\Models\Pos', 'pos_id', 'id');
    }

	public function claim_unit_details(){
        return $this->belongsTo('App\Models\Payments\ClaimCPTInfoV1', 'id', 'claim_id');
    }	

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}

	public function claim_unit(){
		return $this->hasManyThrough(
            'App\Models\Payments\ClaimCPTInfoV1',
            'App\Models\Payments\ClaimInfoV1',
            'facility_id', // Foreign key on claims table...
            'claim_id', // Foreign key on claimdoscptdetail table...
            'id', // Local key on facility table...
            'claim_id' // Local key on Claimdoscptdetail table...
        );
	}

	public function claim_data()
    {
        return $this->hasMany('App\Models\Payments\ClaimInfoV1', 'facility_id', 'id')->with('pmt_info');
    }

    public function claimInfo()
    {
		return $this->claim_data();
	}	

    public function claimformat_details()
	{
        return $this->belongsTo('App\Models\Claimformat', 'claim_format', 'id');
    }

    public function provider_details() 
	{
        return $this->belongsTo('App\Models\Provider', 'default_provider_id', 'id');
    }
    
    public function county() 
	{
        return $this->belongsTo('App\Models\County', 'county', 'id');
    }

	public function fcounty() 
	{
        return $this->belongsTo('App\Models\County', 'county', 'id');
    }
    
    public function taxanomy_details()
    {
        return $this->belongsTo('App\Models\Taxanomy','taxanomy_id','id');
    }
	
    public function providerscheduler()
	{
        return $this->belongsTo('App\Models\ProviderScheduler', 'id', 'facility_id');
    }
    
    public function providerschedulertime() 
	{
        return $this->belongsTo('App\Models\ProviderSchedulerTime', 'id', 'facility_id');
    }
	
    protected $fillable = [
        'facility_name', 'short_name', 'description', 'speciality_id', 'taxanomy_id','phone','phoneext','fax','email','website','county', 'timezone', 'facility_tax_id', 'facility_npi', 'clia_number', 'pos_id', 'default_provider_id', 'fda', 'claim_format', 'scheduler', 'superbill', 'statement_address', 'medication_prescr', 'credit_cart_accepted',
        'no_of_visit_per_week', 'facility_manager', 'facility_manager_phone', 'facility_manager_ext', 'facility_manager_email', 'facility_biller', 'facility_biller_phone', 'facility_biller_ext',
        'facility_biller_email', 'status', 'monday_forenoon','tuesday_forenoon','wednesday_forenoon','thursday_forenoon','friday_forenoon','saturday_forenoon','sunday_forenoon','monday_afternoon','tuesday_afternoon','wednesday_afternoon','thursday_afternoon','friday_afternoon','saturday_afternoon','sunday_afternoon'
    ];
	
	public static $rules = [
	'facility_name' 		=> 	'required',
	//'short_name'			=>	'required|max:3|min:3',
	'email' 				=> 	'nullable|email',
	'facility_tax_id' 		=> 	'nullable|digits:9',
	'facility_npi' 			=> 	'nullable|digits:10',
	'facility_manager_email'=> 	'nullable|email',
	'website'				=> 	'nullable|url',
	'facility_biller_email' => 	'nullable|email'
	/*'description' => 'required',
	'phone' => 'required',
	'fax' => 'required',
	'speciality_id' => 'required',
	'taxanomy_id' => 'required',
	'facility_npi' => 'required|digits:10',
	'clia_number' => 'required',
	'claim_format' => 'required',
	'claim_format' => 'required',
	'claim_format' => 'required',
	'fda' => 'required|alpha_num|max:15',
	'facility_manager' => 'required',
	'facility_manager_phone' => 'required',
	'facility_biller' => 'required',
	'facility_biller_phone' => 'required',*/
	
	];
	
    public static function messages(){
		return [
			'facility_name.required'=> Lang::get("practice/practicemaster/facility.validation.facility_name"),
			'short_name.required'	=> Lang::get("practice/practicemaster/facility.validation.short_name"),
			'short_name.max'		=> Lang::get("common.validation.shortname_regex"),
			'short_name.min'		=> Lang::get("common.validation.shortname_regex"),
			'short_name.unique'		=> Lang::get("practice/practicemaster/facility.validation.short_name_unique"),
			'email.email' 			=> Lang::get("common.validation.email_valid"),
			'website.url' 			=> Lang::get("common.validation.website_valid"),
			'facility_tax_id.digits'=> Lang::get("practice/practicemaster/facility.validation.tax_id"),
			//'facility_npi.required' => Lang::get("common.validation.npi"),
			'facility_npi.digits' 	=> Lang::get("common.validation.npi_regex"),
			'pos_id.required' 		=> Lang::get("practice/practicemaster/facility.validation.pos"),
			'fda.alpha_num' 		=> Lang::get("common.validation.alphanumeric"),
			
			'facility_manager_email.email' 	=> Lang::get("common.validation.email_valid"),
			'facility_biller_email.email' 	=> Lang::get("common.validation.email_valid"),
			'pos_id.validatepos' => Lang::get("practice/practicemaster/facility.validation.exist_pos_msg"),
			'image.mimes'=>Config::get('siteconfigs.customer_image.defult_image_message'),
			/*'phone.required' => 'Enter phone number',
			'fax.required' => 'Enter fax',
			'fda.max' 				=> Lang::get("common.validation.alphanumeric"),
			'speciality_id.required' => 'Select your speciality!',
			'taxanomy_id.required' => 'Select your taxanomy!',
			'facility_tax_id.required' => 'Enter facility tax ID!',
			'fda.required' => 'Enter your FDA!',
			'clia_number.required' => 'Enter your CLIA number!',
			'claim_format.required' => 'Select claim Format!',
			'facility_manager.required' => 'Enter your facility manager name!',
			'facility_manager_phone.required' => 'Enter your facility manager phone number!',
			'facility_manager_email.required' => 'Enter your facility manager email!',
			'description.required' => 'Enter your facility description!',
			'email.required' => 'Enter email',
			'facility_biller.required' => 'Enter your facility biller name!',
			'facility_biller_phone.required' => 'Enter your facility biller phone number!',
			'facility_biller_email.required' => 'Enter your facility biller email!',*/
		];
	}
	
	public static function getfacilitydetail($id)
    {	
	   $facility = Facility::find($id);
	  // dd($facility);
	   $address = @$facility->facility_address->address1;	   
	   $city = @$facility->facility_address->city;
	   $pos = @$facility->pos_details->pos;
	   $value = new Facility();
	   $value->city = $city;
	   $value->address = $address;	
	   $value->pos = $pos;		  
	   return $value;
    } 

	public static function getFacilityName($id)
    {
		$facility = Facility::find($id);
		return  @$facility->facility_name;
    } 

	public static function allFacilityShortName($name='')
    {
		$facility = Facility::orderBy('id','ASC')->pluck('short_name','id')->all();
		if($name == 'name')
		{
		$facility = Facility::orderBy('id','ASC')->selectRaw('CONCAT(short_name,"-",facility_name) as facility_data, id')->pluck("facility_data", "id")->all();
		}
	
		return $facility;
    } 

	public static function getFacilityShortName($id)
    {
		$facility = Facility::find($id);
		$facility_short_name = Facility::where('status','Active')->where('id', $id)->pluck("short_name")->first();
		return $facility_short_name;
	} 

	/*** Get all facilites detail start  ***/
	public static function getAllfacilities()
    {
		$facility = Facility::orderBy('id','ASC')->pluck('facility_name','id')->all();
		return $facility;
    }
	/*** Get all facilites detail end  ***/

    public function ScopeGetResoucesById($query, $default_view_list_name, $default_view_list_id, $cur_date)
    {
        return $query->has('providerschedulertime')
        	->whereHas('providerschedulertime', function($q) use($default_view_list_name, $default_view_list_id, $cur_date){
        		$q->where($default_view_list_name, $default_view_list_id)->where('schedule_date','>=',$cur_date);
        	});
    }
    /* Scheduler module facility empty */
    public static function facilityCount() {
    	$facility_count = Facility::count();
    	return $facility_count;    	
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
}