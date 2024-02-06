<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClearingHouse extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $connection = 'responsive';
	protected $fillable = ['name','description','enable_837','ISA01','ISA02','ISA03','ISA04','ISA05','ISA06','ISA07','ISA08','ISA14','ISA15','contact_name','contact_phone','contact_fax','enable_eligibility','eligibility_ISA02','eligibility_ISA04','eligibility_ISA06','eligibility_ISA08','eligibility_web_service_url','eligibility_web_service_user_id','eligibility_web_service_password','eligibility_provider_npi','eligibility_payer_id_link_list','eligibility_call_type','ftp_address','ftp_port','ftp_user_id','ftp_password','ftp_folder','edi_report_folder','ftp_file_extension_professional','ftp_file_extension_institutional','status','created_by','updated_by'];
	protected $table = 'clearing_house';
	
	public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
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

   
    public static function rules($request){
		$rules = [
			'name' 			=> 	'required',
			'status' 		=> 	'required',
			'contact_name' 	=> 	'required',
			'contact_phone' => 	'required|Regex:/\(?[0-9]{3}\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}/',
			'contact_fax' 	=> 	'Regex:/\(?[0-9]{3}\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}/',
			'ftp_address'	=>	'required',
			'ftp_port'		=>	'required',
			'ftp_user_id'	=>	'required',
			'ftp_password'	=>	'required',
			'ftp_folder'	=>	'required',
			'edi_report_folder'	=>	'required'
			
		];
		if($request["enable_eligibility"] =="Yes") 
		{
			$rules = $rules+array(
							//'eligibility_ISA02' 			=> 	'required',
							//'eligibility_ISA04' 			=> 	'required',
							'eligibility_ISA06' 			=> 	'required',
							'eligibility_ISA08' 			=> 	'required',
							'eligibility_web_service_url' 	=> 	'required|url',
							'eligibility_web_service_user_id' 	=> 	'required',
							'eligibility_web_service_password' 	=> 	'required',
							);
		}
		if($request["enable_837"] =="Yes") 
		{
			$rules = $rules+array(
							'ISA01' 	=> 	'required',
							//'ISA02' 	=> 	'required',
							//'ISA04' 	=> 	'required',
							'ISA06' 	=> 	'required',
							'ISA08' 	=> 	'required',
							'ISA14' 	=> 	'required',
							'ISA15' 	=> 	'required',
							);
		}
		return $rules;
	}
	public static function messages(){
		return [
			'name.required' 			=> trans("admin/clearinghouse.validation.enter_name"),
			'status.required' 			=> trans("admin/clearinghouse.validation.status"),
			'contact_name.required' 	=> trans("admin/clearinghouse.validation.contact_name"),
			'contact_phone.Regex' 		=> trans("common.validation.phone_limit"),
			'contact_phone.required'	=> trans("common.validation.phone"),
			'contact_fax.required' 		=> trans("common.validation.fax"),
			'contact_fax.Regex' 		=> trans("common.validation.fax_limit"),
			'eligibility_ISA02.required' 	=> trans("admin/clearinghouse.validation.ISA02"),
			'eligibility_ISA04.required' 	=> trans("admin/clearinghouse.validation.ISA04"),
			'eligibility_ISA06.required' 	=> trans("admin/clearinghouse.validation.ISA06"),
			'eligibility_ISA08.required' 	=> trans("admin/clearinghouse.validation.ISA08"),
			'eligibility_web_service_url.required' 	=> trans("admin/clearinghouse.validation.ISA08"),
			'eligibility_web_service_url.url' 		=> trans("admin/clearinghouse.validation.web_url_format"),
			'eligibility_web_service_user_id.required' 	=> trans("admin/clearinghouse.validation.web_user_id"),
			'eligibility_web_service_password.required' => trans("admin/clearinghouse.validation.web_password"),
			'ISA01.required' => trans("admin/clearinghouse.validation.ISA01"),
			'ISA02.required' => trans("admin/clearinghouse.validation.ISA02"),
			'ISA04.required' => trans("admin/clearinghouse.validation.ISA04"),
			'ISA06.required' => trans("admin/clearinghouse.validation.ISA06"),
			'ISA08.required' => trans("admin/clearinghouse.validation.ISA08"),
			'ISA14.required' => trans("admin/clearinghouse.validation.ISA14"),
			'ISA15.required' => trans("admin/clearinghouse.validation.ISA15"),
			'ftp_address.required' => trans("admin/clearinghouse.validation.ftp_address"),
			'ftp_port.required' => trans("admin/clearinghouse.validation.ftp_port"),
			'ftp_user_id.required' => trans("admin/clearinghouse.validation.ftp_user_id"),
			'ftp_password.required' => trans("admin/clearinghouse.validation.ftp_password"),
			'ftp_folder.required' => trans("admin/clearinghouse.validation.ftp_folder"),
			'edi_report_folder.required' => trans("admin/clearinghouse.validation.edi_report_folder")
		];
	}
}
