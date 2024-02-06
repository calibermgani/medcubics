<?php

namespace App\Models;

use Config;
use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProviderScheduler as ProviderScheduler;
use App\Models\Medcubics\Users as Users;
use App\Models\Patients\Patient as Patient;

class Provider extends Model {

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
    
    public function degrees() {
        return $this->belongsTo('App\Models\Provider_degree', 'provider_degrees_id', 'id')->select('id','degree_name');
    }

    public function speciality() {
        return $this->belongsTo('App\Models\Speciality', 'speciality_id', 'id');
    }

    public function taxanomy() {
        return $this->belongsTo('App\Models\Taxanomy', 'taxanomy_id', 'id');
    }

    public function speciality2() {
        return $this->belongsTo('App\Models\Speciality', 'speciality_id2', 'id');
    }

    public function taxanomy2() {
        return $this->belongsTo('App\Models\Taxanomy', 'taxanomy_id2', 'id');
    }

    public function provider_types() {
        return $this->belongsTo('App\Models\Provider_type', 'provider_types_id', 'id')->select('id','name');
    }

    public function facility_details() {
        return $this->belongsTo('App\Models\Facility', 'def_facility', 'id');
    }

    public function provider_type_details() {
        return $this->belongsTo('App\Models\Provider_type', 'provider_types_id', 'id');
    }

    public function provider_user_details() {
        return $this->belongsTo('App\Models\Medcubics\Users', 'created_by', 'id');
    }

    public function providerscheduler() {
        return $this->belongsTo('App\Models\ProviderScheduler', 'id', 'provider_id');
    }

    public function providerschedulertime() {
        return $this->belongsTo('App\Models\ProviderSchedulerTime', 'id', 'provider_id');
    }

    public function renderingclaims() {
        return $this->hasMany('App\Models\Payments\ClaimInfoV1', 'rendering_provider_id', 'id');
    }

    public function billingclaims() {
        return $this->hasMany('App\Models\Payments\ClaimInfoV1', 'billing_provider_id', 'id');
    }

    //startscselva  provider_type_details & provider_user_details     

    /* public function provider_user_details()
      {
      return $this->belongsTo('App\Models\Medcubics\Users','created_by','id');
      } */
    // ends cselva
    protected $fillable = [
        'provider_name',
        'organization_name',
        'short_name',
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
        'practice_id',
        'customer_id'
    ];

    public static $rules = [
        'last_name' => 'required',
        //'short_name' 		=> 'required|min:3|max:3',
        //'npi' 			=> 'digits:10',
        //'phone' 			=> 'required',
        //'fax' 			=> 'required',
        'email' => 'nullable|email',
        'website' => 'nullable|url',
        'speciality_id' => 'nullable|different:speciality_id2',
        'speciality_id2' => 'nullable|different:speciality_id',
        //'address_1' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        //'address_2' => 'regex:/^[A-Za-z0-9 \t]*$/i',
        'city' => 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
        'state' => 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
        'digital_sign' => 'nullable|mimes:jpg,png,gif,jpeg',
            /* 'address_1' 		=> 'required',
              'city' 				=> 'required',
              'state' 			=> 'required',
              'zipcode5' 			=> 'required|digits:5' */
    ];

    public static $messages = [
        'last_name.required' => 'Enter your last name!',
        'provider_types_id.required' => 'Select provider type!',
        'provider_types_id.provider_type_validator' => 'Selected provider type already exists',
        'additional_provider_type.additional_provider_type_validator' => 'Selected provider type already exists',
        'npi.required' => 'Enter npi number!',
        'npi.digits' => 'Enter valid npi number!',
        'phone.required' => 'Enter phone!',
        'fax.required' => 'Enter fax!',
        'email.email' => 'Enter valid email!',
        'website.url' => 'Enter valid website!',
        'speciality_id.different' => 'Specialty 1 & Specialty 2 are same',
        'speciality_id2.different' => 'Specialty 1 & Specialty 2 are same',
        //'address1.regex' => 'Alpha numeric, space only allowed',
        //'address2.regex' => 'Alpha numeric, space only allowed',
        'city.regex' => 'Alpha numeric, space only allowed',
        'state.regex' => 'Alpha numeric, space only allowed',
        'digital_sign.mimes' => 'The selected file is not valid, it should be (png,jpg,jpeg,gif)',
            /* 'address_1.required'							=> 'Enter address1!',
              'city.required' 								=> 'Enter city!',
              'state.required' 								=> 'Enter state!',
              'zipcode5.required' 							=> 'Enter zipcode!',
              'zipcode5.digits' 								=> 'Enter valid zipcode5!', */
    ];

    public function ScopeGetResoucesById($query, $default_view_list_name, $default_view_list_id, $cur_date) {
        return $query->has('providerschedulertime')->whereHas('providerschedulertime', function($q) use($default_view_list_name, $default_view_list_id, $cur_date) {
                    $q->where($default_view_list_name, $default_view_list_id)->where('schedule_date', '>=', $cur_date);
                });
    }

    /*     * * Get all provider detail start  ** */

    public static function getAllprovider() {
        $get_all_provider = ProviderScheduler::groupBy('provider_id')->pluck('provider_id')->all();
        /* Provider + Provider degree and  Rendering provider */
        $provider = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.provider_types_id', 1)->where('p.status', 'Active')->where('p.deleted_at', NULL)->orderBy('p.provider_name', 'ASC')->pluck('concatname', 'p.id')->all();
        /* Provider only show and  Rendering provider */
        $provider_deg = DB::table('providers as p')->selectRaw('CONCAT(p.provider_name) as concatname, p.id')->where('p.provider_types_id', 1)->where('p.status', 'Active')->where('p.deleted_at', NULL)->orderBy('p.provider_name', 'ASC')->where('provider_degrees_id', "0")->pluck('concatname', 'p.id')->all();
        /* Join (provider+provider degree) + Provider name */
        return $provider + $provider_deg;
    }

    /*     * * Get all provider detail end  ** */

    /*     * * Get rendering,reffering and billing provider list start  ** */

    public static function typeBasedProviderlist($type) {
        $provider_type_id = Config::get('siteconfigs.providertype.' . $type);
        $provider_with_deg = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', '=', 'Active')->where('p.deleted_at', NULL)->where('provider_types_id', $provider_type_id)->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id')->all();
        $provider_without_deg = Provider::where('status', 'Active')->where('provider_types_id', $provider_type_id)->where('provider_degrees_id', "0")->pluck('provider_name', 'id')->all();
        $provider_list = $provider_with_deg + $provider_without_deg;
        return $provider_list;
    }

    /* Start- To get short name -name with degree of provider  */

    public static function typeBasedAllTypeProviderlist($type) {
		if(isset(Auth::user()->provider_access_id) && Auth::user()->provider_access_id == 0){
			$provider_type_id = Config::get('siteconfigs.providertype.' . $type);
			$provider_with_deg = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.short_name,"-",p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', '=', 'Active')->where('p.deleted_at', NULL)->where('provider_types_id', $provider_type_id)->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id')->all();
			$provider_without_deg = Provider::where('status', 'Active')->where('provider_types_id', $provider_type_id)->where('provider_degrees_id', "0")->selectRaw('CONCAT(short_name,"-",provider_name) as concatshortname, id')->pluck('concatshortname', 'id')->all();
			$provider_list = $provider_with_deg + $provider_without_deg;
		}else{
			//Provider Login: Billing Provider is not showing in reports module
			//Revision 1 - Ref: MR-2699 16 Aug 2019: Selva
			if($type == 'Rendering'){
				$provider_type_id = Config::get('siteconfigs.providertype.' . $type);
				$provider_with_deg = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.short_name,"-",p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', '=', 'Active')->where('p.deleted_at', NULL)->where('provider_types_id', $provider_type_id)->where('p.id',Auth::user()->provider_access_id)->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id')->all();
				
				//Added restriction for provider login 
				//Revision 1 - Ref: MR-2583 13 Aug 2019: Selva
				$provider_without_deg = Provider::where('status', 'Active')->where('id',Auth::user()->provider_access_id)->where('provider_types_id', $provider_type_id)->where('provider_degrees_id', "0")->selectRaw('CONCAT(short_name,"-",provider_name) as concatshortname, id')->pluck('concatshortname', 'id')->all();
				$provider_list = $provider_with_deg + $provider_without_deg;
			}else{
				$provider_type_id = Config::get('siteconfigs.providertype.' . $type);
				$provider_with_deg = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.short_name,"-",p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', '=', 'Active')->where('p.deleted_at', NULL)->where('provider_types_id', $provider_type_id)->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id')->all();
				$provider_without_deg = Provider::where('status', 'Active')->where('provider_types_id', $provider_type_id)->where('provider_degrees_id', "0")->selectRaw('CONCAT(short_name,"-",provider_name) as concatshortname, id')->pluck('concatshortname', 'id')->all();
				$provider_list = $provider_with_deg + $provider_without_deg;
			}
		}
        return $provider_list;
    }
    
    public static function filterReferringProviderlist($type, $query = null) {
        $referring_providers = array();

        $provider_type = array(Config::get('siteconfigs.providertype.Referring'), Config::get('siteconfigs.providertype.Ordering'), Config::get('siteconfigs.providertype.Supervising'));
        $pos = strpos($query, "-");
        $query = ($pos == FALSE) ? $query:substr($query, $pos+1);           
        $query = DB::table('providers as p')
                ->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')
                ->join('provider_types as pt', 'pt.id', '=', 'p.provider_types_id')
                ->selectRaw('CONCAT(SUBSTR(pt.name, 1,1),"-", p.short_name,"-", p.provider_name," ",pd.degree_name) as concatname, p.id')
                ->where('p.status', '=', 'Active')
                ->whereIn('provider_types_id', $provider_type)
                ->where('provider_name', 'LIKE', '%' . $query . '%')
                ->where('p.deleted_at', NULL);
        
        $referring_providers = $query->selectRaw("p.id AS id")->orderBy('provider_name', 'ASC')->pluck('concatname', 'id')->all();  
        return $referring_providers;  
    }

    public static function typeBasedAllReferringProviderlist($patient_id = 0, $query = null) {
         $referring_providers = array();

        $provider_type = array(Config::get('siteconfigs.providertype.Referring'), Config::get('siteconfigs.providertype.Ordering'), Config::get('siteconfigs.providertype.Supervising'));
        $pos = strpos($query, "-");
        $query = ($pos == FALSE) ? $query:substr($query, $pos+1);
        $query = DB::table('providers as p')
                ->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')
                ->join('provider_types as pt', 'pt.id', '=', 'p.provider_types_id')
                ->selectRaw('CONCAT(SUBSTR(pt.name, 1,1),"-", p.provider_name," ",pd.degree_name) as concatname, p.id')
                ->where('p.status', '=', 'Active')
                ->whereIn('provider_types_id', $provider_type)
                ->where('provider_name', 'LIKE', '%' . $query . '%')
                ->where('p.deleted_at', NULL);
        $referring_providers = $query->selectRaw("CONCAT(p.id,';',p.etin_type_number,';', p.npi) AS id")->orderBy('provider_name', 'ASC')->pluck('concatname', 'id')->all();  
        return $referring_providers;  
    }
	

    /* End- To get short name -name with degree of provider  */


	/* This function used for getting referrring provider list */
	
	public static function getReferringProviderList(){
		$provider_type = array(Config::get('siteconfigs.providertype.Referring'), Config::get('siteconfigs.providertype.Ordering'), Config::get('siteconfigs.providertype.Supervising'));
		$query = DB::table('providers as p')
                ->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')
                ->join('provider_types as pt', 'pt.id', '=', 'p.provider_types_id')
                ->selectRaw('CONCAT(SUBSTR(pt.name, 1,1),"-", p.provider_name," ",pd.degree_name) as concatname, p.id')
                ->where('p.status', '=', 'Active')
                ->whereIn('provider_types_id', $provider_type)
                ->where('p.deleted_at', NULL);
        $referring_providers = $query->selectRaw("CONCAT(p.id) AS id")->orderBy('provider_name', 'ASC')->pluck('concatname', 'id')->all();  
        return $referring_providers;  
	} 
	
	public static function getPatientReferringProvider($id = ''){
		$patientInfo = Patient::where('id',$id)->get()->first();
		$query = DB::table('providers as p')
                ->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')
                ->join('provider_types as pt', 'pt.id', '=', 'p.provider_types_id')
                ->selectRaw('CONCAT(SUBSTR(pt.name, 1,1),"-", p.provider_name," ",pd.degree_name) as concatname, p.id')
                ->where('p.status', '=', 'Active')
                ->where('p.id', '=', $patientInfo->referring_provider_id)
                ->where('p.deleted_at', NULL);
        $referring_providers = $query->selectRaw("CONCAT(p.id) AS id")->orderBy('provider_name', 'ASC')->get()->first();  
		$dataArr['id'] = (isset($referring_providers->id) ? $referring_providers->id : '');
		$dataArr['name'] = (isset($referring_providers->concatname) ? $referring_providers->concatname : '');
        return $dataArr;  
	} 
	
	/* This function used for getting referrring provider list */





    /*     * * Get rendering,reffering and billing provider list end  ** */


    /*     * * Get all provider detail start  ** */

    public static function getProviderlist() {
        $provider = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', 'Active')->where('p.deleted_at', NULL)->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id')->all();
        return $provider;
    }

    /*     * * Get all provider detail end  ** */

    /*     * * Get provider short name start  ** */

    public static function getProviderShortName($id) {
        $provider_short_name = Provider::where('status', 'Active')->where('id', $id)->pluck("short_name")->first();
        return $provider_short_name;
    }
    
    public static function getProviderFullName($id) {
        $provider_full_name = Provider::where('status', 'Active')->where('id', $id)->pluck("provider_name")->first();
        return $provider_full_name;
    }

    /*     * * Get provider short name end  ** */

    /*     * * Get provider name start  ** */

    public static function getProviderNamewithDegree($id) {
        $provider = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', 'Active')->where('p.deleted_at', NULL)->where('p.id', '=', $id)->pluck("concatname")->first();
        if ($provider == '')
            $provider = Provider::where('id', $id)->pluck("provider_name")->first();
        return $provider;
    }

    /*     * * Get provider name end  ** */

    public function ScopeGetResoucesByFacility($query, $facility_id, $cur_date) {
        return $query->has('providerschedulertime')->whereHas('providerschedulertime', function($q) use($facility_id, $cur_date) {
                    $q->where('facility_id', $facility_id)->where('schedule_date', '>=', $cur_date);
                });
    }

    public static function checkstatelicense($provider_id) {
        if($provider_id <> 0) {
            $statelicence = Provider::where('id', $provider_id)->select('id', 'statelicense', 'statelicense_2', 'specialitylicense')->first();
            if (!empty($statelicence->statelicense)) {
                return $statelicence->statelicense;
            } elseif (!empty($statelicence->statelicense_2)) {
                return $statelicence->statelicense_2;
            } elseif (!empty($statelicence->specialitylicense)) {
                return $statelicence->specialitylicense;
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    public static function allProviderShortName($name = '') {
        $provider = Provider::orderBy('id', 'ASC')->pluck('short_name', 'id')->all();
        if ($name == 'name') {
            @$provider = Provider::orderBy('id', 'ASC')->selectRaw('CONCAT(short_name,"-",provider_name) as provider_data, id')->pluck("provider_data", "id")->all();
        }

        return @$provider;
    }

    public static function getBillingAndRenderingProvider($fetch_all_provider_type = 'no', $type_id = null, $type_name = null) {
        $provider_type_id = !empty($type_id) ? $type_id : [Config::get('siteconfigs.providertype.Rendering'), Config::get('siteconfigs.providertype.Billing')];
        $query = DB::table('providers as p')
                ->leftjoin('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')
                ->leftjoin('provider_types as pt', 'pt.id', '=', 'p.provider_types_id')
                ->where('p.status', '=', 'Active')
                ->where('p.deleted_at', NULL);
        if (!is_null($type_name)) {
            $query->selectRaw('CONCAT(p.provider_name," ",COALESCE(pd.degree_name,"")) as concatname, p.id');
        } else {
            $query->selectRaw('CONCAT(p.provider_name," ",COALESCE(pd.degree_name,"")) as concatname, p.id');
        }
        if ($fetch_all_provider_type == 'no')
            $query->whereIn('provider_types_id', $provider_type_id);
        elseif ($fetch_all_provider_type == 'npi')
            $query->selectRaw("CONCAT(p.id,';',p.etin_type_number,';', p.npi) AS id")->orderBy('provider_name', 'ASC')->pluck('concatname', 'id')->all();
        elseif ($fetch_all_provider_type == 'yes')
            $query->where('provider_types_id', 1);
        return $query->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id', 'name')->all();
    }

    public static function getRenderingAndBillingProvider($fetch_all_provider_type = 'no', $type_id = null) {
        $provider_type_id = !empty($type_id) ? $type_id : [Config::get('siteconfigs.providertype.Rendering'), Config::get('siteconfigs.providertype.Billing')];

        $query = DB::table('providers as p')
                ->leftjoin('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')
                ->leftjoin('provider_types as pt', 'pt.id', '=', 'p.provider_types_id')
                ->selectRaw('CONCAT(p.provider_name," ",COALESCE(pd.degree_name,""),"-",pt.name) as concatname, p.id')
                ->where('p.status', '=', 'Active')
                ->where('p.deleted_at', NULL);
        if ($fetch_all_provider_type == 'no')
            $query->whereIn('provider_types_id', $provider_type_id);
        elseif ($fetch_all_provider_type == 'npi')
            $query->selectRaw("CONCAT(p.id,';',p.etin_type_number,';', p.npi) AS id")->orderBy('provider_name', 'ASC')->pluck('concatname', 'id')->all();
        elseif ($fetch_all_provider_type == 'yes')
            $query->whereIn('provider_types_id', [1, 5]);
        return $query->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id', 'name')->all();
    }

    /* Scheduler module provider empty */

    public static function providerCount() {
        $provider_count = Provider::count();
        return $provider_count;
    }

}