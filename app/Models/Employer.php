<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
use Config;

class Employer extends Model 
{
	use SoftDeletes;
	protected $dates	= 	['deleted_at'];	
	protected $fillable	=	['employer_organization_name','employer_occupation','employer_student_status','employer_name','address1','address2','city','state','zip5','zip4','work_phone','work_phone_ext','work_phone1','work_phone_ext1','fax','emailid','created_by','updated_by','deleted_at','created_at','updated_at'];
	
	public static $rules = ['employer_name'=>'required','address1'=>'required','city'=>'required','zip5'=>'required|numeric|digits:5','state'=>'required'];

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
	/* public static $rules = [
		'employer_name' 		=> 'required',
		'address_line_1' 		=> 'regex:/^[A-Za-z0-9 \t]*$/i',
		'address_line_2' 		=> 'regex:/^[A-Za-z0-9 \t]*$/i',
		'employer_city' 		=> 'regex:/^[A-Za-z0-9 \t]*$/i',			
		'employer_phone' 		=> 'regex:/^[0-9-() \t]*$/i',			
		'employer_fax' 			=> 'regex:/^[0-9-() \t]*$/i',		
		'contact_person'		=> 'regex:/^[A-Za-z0-9 \t]*$/i',			
		'contact_phone'			=> 'regex:/^[0-9-() \t]*$/i',		
		'employer_zip_code_5' 	=> 'digits:5',
		'employer_zip_code_4' 	=> 'min:4',
		'employer_email' 		=> 'email',
		'contact_email' 		=> 'email',
	];
	public static function messages(){
		return [
			'employer_name.required' 	=> Lang::get("practice/practicemaster/employer.validation.name"),
			'employer_zip_code_5.digits'=> Lang::get("common.validation.zipcode5_limit"),
			'employer_zip_code_4.min'	=> Lang::get("common.validation.zipcode4_limit"),
			'address_line_1.regex' 		=> Lang::get("common.validation.alphanumericspac"),
			'address_line_1.max' 		=> Lang::get("common.validation.alphanumericspac"),
			'address_line_2.regex' 		=> Lang::get("common.validation.alphanumericspac"),
			'employer_state.regex'		=> Lang::get("common.validation.alpha"),
			'employer_city.regex'		=> Lang::get("common.validation.alphanumericspac"),
			'contact_person.regex'		=> Lang::get("common.validation.alphanumericspac"),
			'employer_phone.regex'		=> Lang::get("common.validation.numeric"),
			'employer_fax.regex'		=> Lang::get("common.validation.numeric"),
			'contact_phone.regex'		=> Lang::get("common.validation.numeric"),
			'employer_email.email'		=> Lang::get("common.validation.email_valid"),
			'contact_email.email'		=> Lang::get("common.validation.email_valid"),
			'image.mimes'				=>Config::get('siteconfigs.customer_image.defult_image_message')
		]; 
	}*/
}