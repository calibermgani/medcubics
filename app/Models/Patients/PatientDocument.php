<?php namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
class patientDocument extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'documents';

	protected $fillable = ['practice_id','filesize','type_id','upload_type','document_type','document_extension','title','description','category','document_categories_id','user_email','mime','original_filename','created_by','update_by','temp_type_id','document_sub_type','main_type_id', 'claim_number_data','page','checkno','checkdate','checkamt','payer','document_path','document_domain','filename'];
	
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
	
	public function user(){
		return $this->belongsTo('App\User','created_by','id');
	}

	public function document_categories()
	{
		return $this->belongsTo('App\Models\Document_categories','document_categories_id','id');		
	}

	public function document_followup(){
		return $this->belongsTo('App\Models\Patients\DocumentFollowupList','id','document_id');
	}
	
	public function facility()
	{
		return $this->belongsTo('App\Models\Facility','type_id','id')->where('status',"Active");		
	}

	public function provider()
	{
		return $this->belongsTo('App\Models\Provider','type_id','id')->where('status',"Active");		
	}

	public function patients()
	{
		return $this->belongsTo('App\Models\Patients\Patient','type_id','id')->where('status',"Active");
	}

	public static $rules = [
		//'title' 		=> 'required', defined in controller
		'category' 		=> 'required',
		'filefield' 	=> 'nullable|upload_mimes|upload_limit'
	];
	
	public static function messages(){
		return [
			'title.required' 		=> Lang::get("common.validation.title"),
			'category.required' 	=> Lang::get("common.validation.category"),
			'description.required'	=> Lang::get("common.validation.description"),
			'filefield.upload_mimes'=> Lang::get("common.validation.upload_valid"),
			'filefield.upload_limit'=> Lang::get("common.validation.upload_limit"),
		];
	}
	
	/***  Check its active or not in documents *** /
	public static function CheckIsActiveDoc($id,$sub_type,$type_id)
	{
		$decode_id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$decode_type_id = Helpers::getEncodeAndDecodeOfId($type_id,'decode');
		$id	=	($decode_id =='') ? $id : $decode_id;
		$type_id	=	($decode_type_id =='') ? $id : $decode_type_id;
		$responce  = 1;
		if($sub_type == "insurance")
		{
			$responce = PatientInsurance::where("patient_id",$id)->where("id",$type_id)->count();
		}
		if($sub_type == "Authorization")
		{
			$responce = PatientAuthorization::where("patient_id",$id)->where("id",$type_id)->count();
		}
		return ($responce >0)? 1 : 0;
	}
	/***  Check its active or not in documents ***/

}