<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class Customerusers extends Model {

protected $fillable=[
							'firstname','lastname','dob','gender','designation','department','language_id','ethnicity_id',
							'addressline1','addressline2','city','state','zipcode5','zipcode4','phone','fax','email'
        			];
					
					
					
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

public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}


public static $rules = [
						'firstname' 			=> 'required|regex:/^[A-Za-z \t]*$/i',
						'lastname' 				=> 'required|regex:/^[A-Za-z \t]*$/i',
						'designation' 			=> 'required',
						'department' 			=> 'required',
						'language_id'			=> 'required',
						'ethnicity_id'			=> 'required',
						'addressline1'			=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
						'addressline2'			=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
						'city' 					=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
						'state' 				=> 'nullable|max:2|regex:/^[A-Za-z0-9 \t]*$/i',			
						'zipcode5' 				=> 'digits:5',
						'phone' 				=> 'required',
						'zipcode4' 				=> 'digits:4',
						'email' 				=> 'required|email',

						];
public static $messages = [
						'firstname.required' 	=> 'Enter first name!',
						'lastname.required' 	=> 'Enter last name!',
						'lastname.regex'		=> 'Alpha, space only allowed',
						'firstname.regex'		=> 'Alpha, space only allowed',
						'designation.required' 	=> 'Enter your designation!',
						'department.required' 	=> 'Enter your department!',
						'language_id.required' 	=> 'Select your language!',
						'ethnicity_id.required' => 'Select your ethnicity!',
						'addressline1.required'	=> 'Enter address line1',
						'addressline1.regex'	=> 'Alpha numeric, space only allowed',
						'addressline2.regex'	=> 'Alpha numeric, space only allowed',
						'city.regex'			=> 'Alpha numeric, space only allowed',
						'state.regex'			=> 'Alpha numeric, space only allowed',
						'city.required'			=> 'Enter city',
						'state.required'		=> 'Enter state',
						'state.max'				=> 'State must be two digits',
						'zipcode5.required'		=> 'Enter zipcode',
						'zipcode5.digits'		=> 'Zipcode must be five digits',
						'phone.required'		=> 'Enter phone number',
						'zipcode4.digits'		=> 'Zipcode must be four digits',
						'email.required' 		=> 'Enter email!',
						'email.email' 			=> 'Enter valid email!',
						];

}
	
