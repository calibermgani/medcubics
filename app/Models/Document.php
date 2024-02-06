<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Document_categories as Document_categories;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
use Lang;
use DB;
use Auth;

class Document extends Model 
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = ['practice_id','filesize','type_id','upload_type','document_type','document_extension','title','description','category','document_categories_id','user_email','mime','original_filename','created_by','update_by','temp_type_id','document_sub_type','main_type_id','payer','checkno','checkdate','checkamt','page','claim_number_data','payment_id','document_path','document_domain','filename'];

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

	public function document_categories()
	{
		return $this->belongsTo('App\Models\Document_categories','document_categories_id','id');
	}
	
	public function document_followup(){
		return $this->belongsTo('App\Models\Patients\DocumentFollowupList','id','document_id');
	}

	public function practice()
	{
		return $this->belongsTo('App\Models\Practice','practice_id','id');		
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
		//'category' 		=> 'required',
		'filefield' 	=> 'nullable|upload_mimes|upload_limit'
	];

	public static function messages(){
		return [
			'title.required' 		=> Lang::get("common.validation.title"),
			//'category.required' 	=> Lang::get("common.validation.category"),
			'description.required'	=> Lang::get("common.validation.description"),
			'filefield.upload_mimes'=> Lang::get("common.validation.upload_valid"),
			'filefield.upload_limit'=> Lang::get("common.validation.upload_limit"),
		];
	}

	public static function DocumentCategoriesId($doc_key)
	{
		$get_id = Document_categories::where("category_key",$doc_key)->pluck("id")->first();
		return $get_id;
	}

	public static function getDocumentCategoryName($key)
	{
		$get_id = Document_categories::where("category_key",$key)->pluck("category_value")->first();
		return $get_id;
	}

	public static function DocumentCountList()
    {		
		$practice_id =Session::get('practice_dbid');
		$result['practice'] = Document::where('document_type',"practice")->where('temp_type_id','')->where('practice_id',$practice_id)->count();
		$result['provider']	= Document::has('provider')->whereHas('provider', function($query){
			$query->where('status',"Active");})->where('document_type',"provider")->where('temp_type_id','')->where('practice_id',$practice_id)->count();
		$result['facility'] = Document::has('facility')->whereHas('facility', function($query) {
			$query->where('status',"Active");})->where('document_type',"facility")->where('temp_type_id','')->where('practice_id',$practice_id)->count();
		$result['patients'] = Document::has('patients')->whereHas('patients', function($query){
			$query->where('status',"Active");})->where('document_type',"patients")->orWhere('document_type','patient_document')->where('temp_type_id','')->where('practice_id',$practice_id)->count();
		$result['total'] 	= $result['practice']+$result['provider'] +$result['facility'] +$result['patients']; 
		$result['deleted'] 	= DB::table('documents')->where('deleted_at','!=','null')->count();
		return $result;
    }
	
		/* Getting overall practice assigned document count  */
	public static function getPracticeDocumentAssignedCount()
	{	
		//Practice Total Assigned Document Count - Ignores deleted document even if the document is in followup list
        //Revision 1 - Ref: MR-2583 31 July 2019: Selva
		$Practice_assigned_document_count	=	Document::whereHas('document_followup' , function($query){ 
												$query->where('status','!=','Completed')->where('Assigned_status','Active');
												})
												->with('user','document_categories','document_followup')
												->whereRaw('temp_type_id = "" and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->where('deleted_at',Null)
												->orderBy('id','DESC')										
												//->get()
												->count();
		return $Practice_assigned_document_count;
		
	}
	// Start sidebar notification by baskar -04/02/19
	public static function getPracticeDocumentAssignedCountByUser()
	{	
		$Practice_assigned_document_count	=	Document::whereHas('document_followup' , function($query){ 
												$query->where('assigned_user_id', Auth::user()->id)->where('status','!=','Completed')->where('Assigned_status','Active');
												})
												->with('user','document_categories','document_followup')
												->whereRaw('temp_type_id = "" and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->where('deleted_at',Null)
												->orderBy('id','DESC')										
												//->get()
												->count();
		return $Practice_assigned_document_count;
	}
	// End sidebar notification by baskar -04/02/19
	
}