<?php
namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Provider extends Model
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

	public function degree()
	{
		return $this->hasOne ( 'Degree' ); // this matches the Eloquent model
	}
        
        public function degrees()
	{
		return $this->belongsTo('App\Models\Medcubics\Provider_degree','provider_degrees_id','id');
	}	
        
	public function speciality()
	{
		return $this->belongsTo('App\Models\Medcubics\Speciality','speciality_id','id');
	}
        
        public function taxanomy()
	{
		return $this->belongsTo('App\Models\Medcubics\Taxanomy','taxanomy_id','id');
	}
        
        public function speciality2()
	{
		return $this->belongsTo('App\Models\Medcubics\Speciality','speciality_id2','id');
	}
        
        public function taxanomy2()
	{
		return $this->belongsTo('App\Models\Medcubics\Taxanomy','taxanomy_id2','id');
	}
        
	public function provider_types()
	{
		return $this->belongsTo('App\Models\Medcubics\Provider_type','provider_types_id','id');
	}
        
	public function facility_details()
	{
		return $this->belongsTo('App\Models\Medcubics\Facility','def_facility','id');
	}      
        
    public function provider_type_details()
    {
        return $this->belongsTo('App\Models\Medcubics\Provider_type','provider_types_id','id');
    }
        
	protected $fillable = [ 
                        'provider_name',
                        'short_name',
                        'customer_id',
                        'practice_id',
                        'organization_name',
						'first_name',
						'last_name',
						'middle_name',
			            'description',
						'provider_types_id',
						'provider_dob',
						'gender',
						'ssn',
						'provider_degrees_id',
						'job_title',
						'address_1',
						'address_2',
						'city',
						'state',
						'zipcode5',
						'zipcode4',
						'phone',
			            'phoneext',
						'fax',
						'etin_type',
						'etin_type_number',
						'npi',
						'tax_id',
						'speciality_id',
						'taxanomy_id',
			            'speciality_id2',
						'taxanomy_id2',
						'statelicense',
						'state_1',
						'statelicense_2',
						'state_2',
						'specialitylicense',
			            'state_speciality',
						'deanumber',
						'state_dea',
						'tat',
						'mammography',
						'careplan',
						'medicareptan',
						'medicaidid',
						'bcbsid',
						'aetnaid',
						'uhcid',
						'otherid',
						'otherid_ins',
						'otherid2',
						'otherid_ins2',
						'otherid3',
						'otherid_ins3',
						'req_super',
						'super_pro',
						'def_billprov',
						'def_facility',
						'stmt_add',
						'hospice_emp',
						'digital_sign',
						'status',
						'email',
						'website',
						'practice_db_provider_id',
						'provider_entity_type',
			
	];

	public static $rules = [ 
			'last_name' 	=> 'required|regex:/^[A-Za-z0-9 \t]*$/i',
			'first_name' 	=> 'required|regex:/^[A-Za-z0-9 \t]*$/i',
			'middle_name' 	=> 'nullable|regex:/^[A-Za-z \t]*$/i',
		//	'short_name' 	=> 'required|min:3|max:3',
			'address1'		=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
			'address2'		=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
			'city'			=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
			'state'			=> 'nullable|min:2|regex:/^[A-Za-z0-9 \t]*$/i',
			'digital_sign' 	=> 'mimes:jpg,png,gif,jpeg',
			//'npi' => 'digits:10',
            //'phone' => 'required',
            //'fax' => 'required',
            //'email' => 'email',
            //'website' => 'url',
            //'speciality_id' => 'different:speciality_id2',
            //'speciality_id2' => 'different:speciality_id',
			'host_ip'		=>'ip'	
          /*  'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode5' => 'required|digits:5' */
	];
	
	public static $messages = [
	 
			'last_name.required' 						=> 'Enter your last name!',
			'last_name.regex'							=> 'Alpha, space only allowed',
			'first_name.regex'							=> 'Alpha, space only allowed',
			'provider_types_id.required'				=> 'Select provider type!',	
			'provider_types_id.provider_type_validator' => 'Selected provider type already exists',	
            'additional_provider_type.additional_provider_type_validator' => 'Selected provider type already exists',	
			'npi.required'								=> 'Enter npi number!',
			'npi.digits' 								=> 'Enter valid npi number!',
			'phone.required' 							=> 'Enter work phone!',
			'fax.required' 								=> 'Enter fax!',
			'host_ip'									=>'Enter valid Ip address',
			'email.email' 								=> 'Enter valid email!',
			'website.url' 								=> 'Enter valid website!',
            'speciality_id.different' 					=> 'Speciality 1 & Speciality 2 are same',
            'speciality_id2.different' 					=> 'Speciality 1 & Speciality 2 are same',
			'address_1.regex'							=> 'Alpha numeric, space only allowed',
			'address_2.regex'							=> 'Alpha numeric, space only allowed',
			'state.regex'								=> 'Alpha numeric, space only allowed',
			'city.regex'								=> 'Alpha numeric, space only allowed',
			'digital_sign.mimes' 						=> 'The selected file is not valid, it should be (png,jpg,jpeg,gif)',
			/* 'address_1.required' => 'Enter address1!',
            		'city.required' => 'Enter city!',
			'state.required' => 'Enter state!',
			'zipcode5.required' => 'Enter zipcode!',
                        'zipcode5.digits' => 'Enter valid zipcode5!', */
	];
}
