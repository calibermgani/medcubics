<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
use Config;

class Customer extends Model 
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $connection = "responsive";
	
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
	
    public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}

	public function practice()
	{
		return $this->hasMany('App\Models\Medcubics\Practice','customer_id','id');
	}
    
	protected $fillable=[
						'customer_name','customer_desc','customer_type','contact_person','designation','email','addressline1','addressline2','phone','phoneext','mobile','fax','city','state','zipcode5','zipcode4','status','avatar_name','avatar_ext','firstname','lastname','gender','short_name'
	];

	public static $rules = [
						'customer_name' 		=> 'required',
						'customer_desc' 		=> 'required',
						'customer_type' 		=> 'required',
						'contact_person' 		=> 'required',
						'designation'			=> 'required',
						'addressline1'			=> 'required|regex:/^[A-Za-z0-9 \t]*$/i',
						'addressline2'			=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
						'city' 					=> 'required|regex:/^[A-Za-z0-9 \t]*$/i',
						'state' 				=> 'required|max:2|regex:/^[A-Za-z0-9 \t]*$/i',			
						'zipcode5' 				=> 'required|digits:5',
						'zipcode4' 				=> 'nullable|digits:4',
						'short_name'            => 'required',	
						];
						
			public static function messages()
			{		
				return [
						'customer_name.required' 	=> Lang::get("admin/customer.validation.customer_name"),
						'customer_desc.required' 	=> Lang::get("common.validation.description"),
						'customer_type.required' 	=> Lang::get("admin/customer.validation.customer_type"),
						'contact_person.required' 	=> Lang::get("admin/customer.validation.contact_person"),
						'designation.required' 		=> Lang::get("admin/customer.validation.designation"),
						'addressline1.required'		=> Lang::get("common.validation.address1_required"),
						'city.required'				=> Lang::get("common.validation.city_required"),
						'state.required'			=> Lang::get("common.validation.state_required"),
						'state.max'					=> Lang::get("common.validation.state_limit"),
						'zipcode5.required'			=> Lang::get("common.validation.zipcode5_required"),
						'zipcode5.digits'			=> Lang::get("common.validation.zipcode5_limit"),
						'zipcode4.digits'			=> Lang::get("common.validation.zipcode4_limit"),
						'email.required' 			=> Lang::get("common.validation.email"),
						'email.email' 				=> Lang::get("common.validation.email_valid"),
						'email.unique' 				=> Lang::get("admin/customer.validation.email_unique"),
						'password.required' 		=>Lang::get("admin/customer.validation.password"),
						'con_password.required' 	=> Lang::get("admin/customer.validation.confirmpassword"),
						'con_password.same' 		=> Lang::get("admin/customer.validation.passwordconfirmsame"),
						'image.mimes'				=> Config::get('siteconfigs.customer_image.defult_image_message')
					];	
			}
	
	

}
