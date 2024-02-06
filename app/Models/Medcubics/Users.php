<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Medcubics\Practice as Practice;
use Auth;
use Lang;
use Request;
use Config;
class Users extends Model 
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

	protected $fillable=[
				'customer_id','role_id','firstname','lastname','user_type','dob','gender','designation','department','language_id','ethnicity_id',
				'addressline1','addressline2','city','state','zipcode5','zipcode4','phone','fax','email','password','practice_user_type','admin_practice_id','name','facebook_ac', 'twitter', 'googleplus', 'linkedin','avatar_name','avatar_ext','last_access_date','status','useraccess','practice_access_id','facility_access_id','login_attempt','attempt_updated','password_change_time','short_name','app_name', 'provider_access_id'];
	protected $connection = 'responsive';			
	public function customer()
	{
		return $this->belongsTo('App\Models\Medcubics\Customer','customer_id','id');
	}
	public function practice()
	{
		return $this->belongsTo('App\Models\Medcubics\Practice','customer_id','id');
	}

	public function ethnicity()
	{
		return $this->belongsTo('App\Models\Medcubics\Ethnicity','ethnicity_id','id');
	}

	public function language()
	{
		return $this->belongsTo('App\Models\Medcubics\Language','language_id','id');
	}

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}

	public function role()
	{
		return $this->belongsTo('App\Models\Medcubics\Roles','role_id','id');
	}        
			
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}

	public function adminPracticeId()
	{
		return $this->belongsTo('App\Models\Medcubics\Practice','admin_practice_id','id');
	}
		
	public function SetAdminPagePermissions()
	{
		return $this->belongsTo('App\Models\Medcubics\SetAdminPagePermissions','role_id','role_id');
	}
        //practice name list
        public function practiceName()
	{
            return $this->belongsTo('App\Models\Medcubics\Practice','practice_access_id','id');
	}
        //admin user practice name
        public static function adminPracticeName($ids) {
            if ($ids != "") {
                $id_arr = explode(',', $ids);
                $adminpractice = Practice::whereIn('id', $id_arr)->pluck('practice_name')->all();
                return implode(", ", $adminpractice);
            }
        }

    public static function getActiveUsers()
	{
		$active_users_list = Users::where('customer_id', Auth::user()->customer_id)->where('status', 'Active')->where('deleted_at', Null)->pluck('name','id')->all();
		return $active_users_list;
	}

	
	public static $rules = [
		'firstname' 			=> 'required|regex:/^[A-Za-z \t]*$/i',
		'lastname' 				=> 'required|regex:/^[A-Za-z \t]*$/i',
		//'addressline1'			=> 'required|regex:/^[A-Za-z0-9 \t]*$/i',
		'addressline2'			=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
		//'city' 					=> 'required|regex:/^[A-Za-z0-9 \t]*$/i',
		//'state' 				=> 'required|max:2|regex:/^[A-Za-z]*$/i',			
		//'zipcode5' 				=> 'required|digits:5',
		'zipcode4' 				=> 'nullable|digits:4',
		'short_name' 			=> 'required'
	];
	public static function messages(){
		return [
		'firstname.required' 	=> Lang::get("admin/user.validation.firstname"),
		'lastname.required' 	=> Lang::get("admin/user.validation.lastname"),
		'lastname.regex'		=> Lang::get("common.validation.alphaspace"),
		'firstname.regex'		=> Lang::get("common.validation.alphaspace"),
		'email.required' 		=> Lang::get("common.validation.email"),
		'password.required' 	=> Lang::get("admin/user.validation.password"),
		'confirmpassword.required'	=> Lang::get("admin/user.validation.con_password"),
		//'designation.required' 	=> 'Enter your designation!',
		//'department.required' 	=> 'Enter your department!',
		//'language_id.required' 	=> 'Select your language!',
		//'ethnicity_id.required' => 'Select your ethnicity!',
		'addressline1.required'	=> Lang::get("common.validation.address1_required"),
		'addressline1.regex'	=> Lang::get("common.validation.alphanumericspac"),
		'addressline2.regex'	=> Lang::get("common.validation.alphanumericspac"),
		'city.required'			=> Lang::get("common.validation.city_required"),
		'city.regex'			=> Lang::get("common.validation.alphanumericspac"),
		'state.required'		=> Lang::get("common.validation.state_required"),
		'state.regex'			=> Lang::get("common.validation.alpha"),
		
		'state.max'				=> Lang::get("common.validation.state_limit"),
		'zipcode5.required'		=> Lang::get("common.validation.zipcode5_required"),
		'zipcode5.digits'		=> Lang::get("common.validation.zipcode5_limit"),
		'phone.required'		=> Lang::get("common.validation.phone"),
		'zipcode4.digits'		=> Lang::get("common.validation.zipcode4_limit"),
		'email.email' 			=> Lang::get("common.validation.email_valid")
		];
	}
					
						
	public static $adminuser_rules = [
			//'role_id' 			=> 'required',
			// 'addressline1'		=> 'required',
			'confirmpassword' => 'same:password',
			'email'             => 'unique:users,email,NULL,id,deleted_at,NULL',
			// 'facebook_ac'          => 'url',
			// 'twitter'              => 'url',
			// 'linkedin'             => 'url',
			// 'googleplus'           => 'url',
							];
							
	public  static function adminuser_messages() { 
		return [
				'role_id.required' 	=>  Lang::get("admin/adminuser.validation.roletype"),
				'name.required' 	=> Lang::get("admin/adminuser.validation.name"),
				'password.required' => Lang::get("admin/adminuser.validation.password"),
				'confirmpassword.required' 	=> Lang::get("admin/adminuser.validation.confirmpassword"),
				'confirmpassword.same' => Lang::get("admin/adminuser.validation.passwordidentical"),
				'email.required' 	=> Lang::get("admin/adminuser.validation.email"),
				'addressline1.required' 	=> Lang::get("common.validation.address1_required"),
				'email.unique'  	=> Lang::get("admin/adminuser.validation.email_unique"),
				'email.email' 		=> Lang::get("admin/adminuser.validation.email_email"),
				'language_id.required' 	=> Lang::get("admin/adminuser.validation.language_id"),
		];
	}									
}
	
