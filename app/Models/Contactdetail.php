<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Contactdetail extends Model 
{
	use SoftDeletes;
	protected $dates 	= ['deleted_at'];
	protected $fillable	= [
        'practiceceo','mobileceo','phoneceo','faxceo','emailceo',
		'practicemanager','mobilemanager','phonemanager','faxmanager','emailmanager',
		'companyname','contactperson','address1','address2','city','state','zipcode5','zipcode4','phone','fax','emailid','website','phoneceo_ext','phonemanager_ext','phone_ext'
	];

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

	public static $rules = [
			//'practiceceo' 	=> 'required',
			'phoneceo_ext' 	=> 'nullable|numeric',
			'emailceo' 		=> 'nullable|email',
		//'practicemanager' 	=> 'required',
			//'mobilemanager' => 'required',
			'emailmanager' 	=> 'nullable|email',
		//	'companyname' 	=> 'required',
			//'address1' 		=> 'regex:/^[A-Za-z0-9 \t]*$/i',
			//'address2' 		=> 'regex:/^[A-Za-z0-9 \t]*$/i',
			'city' 			=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
			'state' 		=> 'nullable|regex:/^[A-Za-z0-9 \t]*$/i',
			'zipcode5'	 	=> 'nullable|digits:5',
			'zipcode4' 		=> 'nullable|digits:4',
			//'contactperson' => 'required',
			'phone_ext' 	=> 'nullable|numeric',
			'emailid' 		=> 'nullable|email',
			'website' 		=> 'nullable|url',
	];
	
	public static function messages(){
		return [
			'practiceceo.required' 		=> Lang::get("practice/practicemaster/contactdetail.validation.practiceceo"),
			'mobileceo.digits' 			=> Lang::get("common.validation.cell_phone_limit"),
			'phoneceo_ext.numeric' 		=> Lang::get("common.validation.work_phone"),
			'emailceo.email' 			=> Lang::get("common.validation.email_valid"),
			'practicemanager.required'	=> Lang::get("practice/practicemaster/contactdetail.validation.practicemanager"),
			'mobilemanager.required'	=> Lang::get("practice/practicemaster/contactdetail.validation.mobilemanager"),
			'emailmanager.required'		=> Lang::get("common.validation.email"),
			'emailmanager.email'		=> Lang::get("common.validation.email_valid"),
			'companyname.required'		=> Lang::get("practice/practicemaster/contactdetail.validation.companyname"),
			//'address1.required'			=> Lang::get("common.validation.address1_required"),
			//'address1.regex'			=> Lang::get("common.validation.alphanumericspac"),
			//'address2.regex'			=> Lang::get("common.validation.alphanumericspac"),
			'city.regex'				=> Lang::get("common.validation.alphanumericspac"),
			'state.regex'				=> Lang::get("common.validation.alpha"),
			'city.required'				=> Lang::get("common.validation.city_required"),
			'state.required'			=> Lang::get("common.validation.state_required"),
			'zipcode5.required'			=> Lang::get("common.validation.zipcode5_required"),
			'zipcode5.digits'			=> Lang::get("common.validation.zipcode5_limit"),
			'zipcode4.digits'			=> Lang::get("common.validation.zipcode4_limit"),
			'contactperson.required'	=> Lang::get("practice/practicemaster/contactdetail.validation.contactperson"),
			'phone_ext.numeric'			=> Lang::get("common.validation.work_phone_limit"),
			'emailid.required'			=> Lang::get("common.validation.email"),
			'emailid.email'				=> Lang::get("common.validation.email_valid"),
			'website.url'				=> Lang::get("common.validation.website_valid"),
		];
	}
		
}