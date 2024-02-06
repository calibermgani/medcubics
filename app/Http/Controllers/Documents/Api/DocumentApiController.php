<?php
namespace App\Http\Controllers\Documents\Api;
use App\Http\Controllers\Controller;
use App\Models\Practice as Practice;
use App\Models\Document as Document;
use App\Models\Facility as Facility;
use App\Models\Provider as Provider;
use App\Models\Insurance as Insurance;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Registration as Registration;
use App\Models\Patients\Patient as Patients;
use App\Models\Patients\DocumentFollowupList as DocumentFollowupList;
use App\Models\Patients\Patient as Patient;
use App\Models\Document_categories as Document_categories;
use App\Models\DocumentCategories as DocumentCategories;
use App\Models\Medcubics\Users as Users;
use App\Models\Payments\ClaimInfoV1;
use App\User as User;
use Auth;
use Response;
use Request;
use Validator;
use Lang;
use DB;
use Session;
use Config;
use App;
use ZipArchive;

class DocumentApiController extends Controller {

	/*** Documents list function starts ***/
    public function getIndexApi($request='') 
	{	
		$practice_id =Session::get('practice_dbid');
		$module 	= (Request::ajax()) ? $request : "practice";
		
		$qry = Document::where('temp_type_id','')->with('document_categories','user',$module)->where('practice_id',$practice_id);

		if($module =="provider") 
			$qry ->with('provider.degrees','provider.provider_types')->whereHas('provider', function($q) {
			$q->where('status',"Active");});
			//:$qry ->with('document_categories','user',$module);
		if($module =="facility") 
			$qry ->with('facility.facility_address','facility.speciality_details','facility.pos_details'); 
		if($module =="patients") 
			$qry->where("document_type","patients")->orWhere("document_type","patient_document");
		else
			$qry->where("document_type",$module);
		$documents_list 	=	$qry->orderBy('created_at', 'desc')->get();//Get list without temp document id
		$document_data = $this->document_ajax_summery('summery');
		
		// removed ->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
		$total_document_count 		=	Document::whereHas('document_categories' , function($query){ 					
										})
										->with('user','document_categories','document_followup')
										->whereRaw('temp_type_id = "" and  ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
										->where('deleted_at',Null)
										->orderBy('id','DESC')
										->get()
										->count();
										
							
		$assigned_document_count	=	Document::whereHas('document_followup' , function($query){ 
											$query->where('status','Assigned')->where('Assigned_status','Active');
										})
										->with('user','document_categories','document_followup')
										->whereRaw('temp_type_id = "" and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
										->where('deleted_at',Null)
										->orderBy('id','DESC')										
										->get()
										->count();
										
												
		
					
										
		$inprocess_document_count	=	Document::whereHas('document_followup' , function($query){ 
											$query->where('status','Inprocess')->where('Assigned_status','Active');
										})
										->with('user','document_categories','document_followup')
										->whereRaw('temp_type_id = "" and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
										->where('deleted_at',Null)
										->orderBy('id','DESC')
										->get()
										->count();
										
		
		$pending_document_count		=	Document::whereHas('document_followup' , function($query){ 
											$query->where('status','Pending')->where('Assigned_status','Active');
										})
										->with('user','document_categories','document_followup')
										->whereRaw('temp_type_id = "" and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
										->where('deleted_at',Null)
										->orderBy('id','DESC')
										->get()
										->count();
										
		
		$review_document_count		=	Document::whereHas('document_followup' , function($query){ 
											$query->where('status','Review')->where('Assigned_status','Active');
										})
										->with('user','document_categories','document_followup')
										->whereRaw('temp_type_id = "" and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
										->where('deleted_at',Null)
										->orderBy('id','DESC')
										->get()
										->count();

		$completed_document_count	=	Document::whereHas('document_followup' , function($query){ 
											$query->where('status','Completed')
										->groupBy('document_id')->where('Assigned_status','Active');
										})
										->with('user','document_categories','document_followup')
										->whereRaw('temp_type_id = "" and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
										->where('deleted_at',Null)
										->orderBy('id','DESC')
										->get()
										->count();
										
		$users = Users::where('customer_id', Auth::user()->customer_id)->where('status', 'Active')->pluck('name', 'id')->all();		
		$categories = [];		
		//	$categories = DocumentCategories::where('category', '!=', '')->groupBy('category')->pluck('category', 'id');
		
		$patients = Patient::Has('patient_document')->where('status', 'Active')->select(DB::raw('CONCAT(last_name, ", ", first_name, " ", middle_name) AS full_name'), 'id')->pluck('full_name','id')->all();			
		$insurances	= Insurance::Has('patient_document_insurance')->where('status', 'Active')->pluck('short_name', 'id')->all();		
		return Response::json(array('status'=>'success', 'data'=>compact('documents_list',"module",'document_data','total_document_count','assigned_document_count','inprocess_document_count','pending_document_count','review_document_count','completed_document_count', 'users', 'categories', 'patients','insurances')));
    } 
	/*** Documents list function end ***/
	
	/*** Documents create module function starts ***/
	public function getCreateApi() 
	{
		$practice 	= Practice::where("id",Session::get("practice_dbid"))->value('id');
		$claim_number = [];//ClaimInfoV1::select(DB::raw("CONCAT(claim_number, ' - ', DATE_FORMAT(date_of_service, '%m/%d/%Y')) as claim_number_concat"), 'claim_number')->pluck('claim_number_concat', 'claim_number')->all();		
		$priority = array('high'=>'High','moderate'=>'Moderate','low'=>'Low');
		$user_list = Helpers::user_list();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practice','claim_number','priority','user_list')));
    }
	/*** Documents create module function end ***/
	
	/*** Documents store function starts ***/
	public function getStoreApi($request='') 
	{ 
		if($request!='')
			$req 	  	= Request::all();
		
		if($req["document_type"]=='group'){
			$req['type_id'] = 1;
		}
		
		if($req["document_type"]=='patient'){
			$req['document_type']	= "patients";
			$req['type_id']	= $req['patient_id'];
		}
		$files 	= Request::file('filefield');
		$i = 0;
		$len = count($files);
		foreach($files as $file) {
			$request = $req;
			$set_err =	$src 	= '';
			$response = $this->getValidation($file,$request);  
			$request 	= 	$this->getReqValue($response['max_size'],$file,$request);//Get file extesion and path info
			if(Document::where('title',$request['title'])->count() != 0){
				$ttl = $i + 1;
				$request['title']		= trim($request['title'])." - (".$ttl.")";
			}else{
				$request['title']		= trim($request['title']);
			}
			Validator::extend('chk_title_exists', function() use($request)
			{
				$categories_id = Document_categories::where("category_key",$request['category'])->where('module_name',$request['document_type'])->value("id");
				if(($request['type_id'] == 0 || empty($request['type_id'])) && !empty($request['temp_doc_id'])){
					return (Document::where('title',$request['title'])->where('document_categories_id',$categories_id)->where('temp_type_id',$request['temp_doc_id'])->count()>0) ? false : true;
				}else{
					return (Document::where('title',$request['title'])->where('document_categories_id',$categories_id)->where('type_id',$request['type_id'])->count()>0) ? false : true;
				}
			});
			Validator::extend('upload_mimes', function() use($request)
			{
				$attachement = config('siteconfigs.file_uplode.defult_file_attachment');
				$file_ext_arr = explode(",",str_replace("mimes:","",$attachement));
				return(in_array($request['ext'],$file_ext_arr)) ? true:false;
			});
			Validator::extend('upload_limit', function() use($response)
			{
				return($response['status']=="error") ? false : true;
			});
			//Checking uploaded doc size and return error
			$rules 		= array_merge(Document::$rules,array('title' => 'required|chk_title_exists'));
			
			$msg 		= Document::messages()+array('title.chk_title_exists' => Lang::get("common.validation.title_unique"));
			$validator  = Validator::make($request, $rules, $msg);
			if ($validator->fails())
			{
				$errors = $this->getArray($validator->errors()->getmessages());
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$request 	= 	$this->getReqValue($response['max_size'],$file,$request);//Get file extesion and path info
				if( $request =="error")
				{
					return Response::json(array('status'=>'error','message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));
				}
				else
				{
					DB::beginTransaction();
					try{
						$request["document_categories_id"] = Document_categories::where("category_key",$request['category'])->where('module_name',$request['document_type'])->value("id");				
						if(!empty($request['checkdate']))
									$request['checkdate'] = date('Y-m-d',strtotime($request['checkdate']));							
						$request['claim_number_data'] = isset($request['claim_number'])?$request['claim_number']:"";	
						$data 				= Document::create($request);
						$file_store_name 	= md5($data->id.strtotime(date('Y-m-d H:i:s'))).'.'.$request['ext'];
						$store_arr  	  	= Helpers::amazon_server_folder_check($request['document_type'],$file,$file_store_name,$src);
						$data->filename 		= $file_store_name;
						$data->document_path   	= $store_arr[0];
						$data->document_domain 	= $store_arr[1];
						$data->save ();
						DocumentFollowupList::where('document_id',$data->id)->update(['Assigned_status'=>'Inactive']);
						$assign_data['document_id'] = $data->id;
						$assign_data['patient_id'] = $data->type_id;
						$assign_data['claim_id'] = '';
						$assign_data['assigned_user_id'] = $request['assigned'];
						$assign_data['notes'] = $request['notes'];
						$assign_data['priority'] = ucfirst($request['priority']);
						$assign_data['followup_date'] = date('y-m-d',strtotime($request['followup']));
						$assign_data['status'] = ucfirst($request['status']);
						$assign_data['created_by'] = Auth::user()->id;
						$assigned_data = DocumentFollowupList::create($assign_data);
						$assigned_data->save();						
						
						$affectedRows = User::where('id', Auth::user()->id)->increment('maximum_document_uploadsize',str_replace(',', '', $response['max_size']));
						DB::commit();
						if($i == $len - 1) {
							return Response::json(array('status'=>'success', 'message'=>'Document added successfully','data'=>$request['document_type']));
						}
					}
					catch(\Exception $e){
						echo 'Error message: ' .$e->getMessage();die;
						DB::rollback();
					}
				}	
			}
			$i++;
		}
    }
	/*** Documents store function ends ***/
	
	/*** Documents select category based list starts ***/
	public function getCategoryApi($type)
	{
		$type= ($type=="patient") ? "patients" : $type;
		$cat_list = DB::table('document_categories')->where(function($query)use ($type){ $query->where('module_name', '=', $type)->orWhere('module_name', 'LIKE', $type)->orWhere('module_name', 'LIKE', $type.',%')->orWhere('module_name', 'LIKE', '%,'.$type)->orWhere('module_name', 'LIKE', '%,'.$type.',%');})->orderBy('category_value','ASC')->pluck('category_value', 'category_key')->all();
		$type_list = [];		
		if($type == "facility")
			$type_list 	= Facility::where("status","Active")->pluck('facility_name',"id")->all();
		if($type == "provider")
			$type_list 	= Provider::getProviderlist();
		if($type == "patients")
		{
			$type_list 	= [];//Patients::getAllpatients();//Getting all patients detail;
			$registration  = Registration::select('driving_license', 'insured_ssn')->first();
			if(!empty($registration))
			{
				if(!empty($registration->driving_license =="0"))
					unset($cat_list['driving_license']); 
				if(!empty($registration->insured_ssn =="0"))
					unset($cat_list['insured_ssn']);
			}
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('cat_list','type_list')));
    }
	/*** Documents select category based list end ***/
	
	/*** Documents delete function starts ***/
	public function getDestroyApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if (Document::where('id', $id)->count() && is_numeric($id)) 
		{
			$getdata 	= Document::where('id',$id)->first();
			$file_size 	= $getdata->filesize;
			$getdata->delete();
			// To reduce from maximum upload size
			User::where('id', Auth::user()->id)->decrement('maximum_document_uploadsize', str_replace(',', '', $file_size));
			if($getdata)
			{
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.document_delete_msg"),'data'=>''));	
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.error_msg"),'data'=>''));
			}
		} 
		else 
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}	
	}
	/*** Documents delete function end ***/
	
	/*** Start get Validation added process ***/
	public function getValidation($file,$request)
	{
		//To get maximum file upload size for each user to restrict file upload
		$response =[];
		$max_size_upload = config('siteconfigs.maximum_file_upload.size');
		$max_user_upload = '';
		$max_size = 0;	 
		if($request['practice_id'] =='')
			$response['status']= 'error';
		elseif($request['document_type'] =='')
			$response['status']= 'error';
		elseif($request['document_type'] !='practice' && $request['type_id'] =='')
			$response['status']= 'error';
		if(isset($request['filefield']) && isset($request['upload_type']) && $request['upload_type'] == 'browse')
		{
			$max_size            = $this->SizeToKBUnits($file->getClientSize());	
			$max_user_upload =  Auth::user()->maximum_document_uploadsize;
		}
		elseif($request['webcam_image'] == 1 && $request['upload_type'] == 'webcam')
		{
			if(App::environment() ==  Config::get('siteconfigs.production.defult_production'))
				$path = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
			else
				$path = public_path().'/';
			$file_size		= filesize($path.'/media/'.$type.'/'.Auth::user()->id.'/'.$request['webcam_filename']);
		    $max_size		= $this->SizeToKBUnits($file_size);
		    $max_user_upload = $max_size+Auth::user()->maximum_document_uploadsize;
		}
		elseif($request['scanner_image'] == 1 && $request['upload_type'] == 'scanner')
		{
            $max_size = '1';
		    $max_user_upload = '1';
		}
				
		if($request['upload_type'] == 'webcam' && $request['webcam_image'] == 1)
		{
			unset(Document::$rules['filefield']);
		}
		
        if($request['upload_type'] == 'scanner' && $request['scanner_image'] == 1)
		{
			unset(Document::$rules['filefield']);
		}
		$response['status']= 'success';
		$response['max_size']=$max_size;
		if($request['upload_type'] == 'webcam' && $request['webcam_image'] == '')
		{
			$response['filefield']= '';
		}
		if($max_user_upload > $max_size_upload)
		{
			//$response['status']= 'error';
		}

		return $response;
	}
	/*** End get Validation added process ***/
	
	/*** Get KB format function starts ***/
	function SizeToKBUnits($bytes)
    {
      $bytes = number_format($bytes / 1024, 2) ;
      return $bytes;
	}
	/*** Get KB format function end ***/
	
	/*** Start get file related value for adding process ***/
	public function getReqValue($max_size,$file,$request)
	{
		$request['user_email'] 			= Auth::user()->email;
		$request['created_by'] 			= Auth::user()->id;
		$request['filesize']		    = str_replace(',','',$max_size);
		if(isset($request['filefield']) && $request['filefield'] && $request['upload_type'] == 'browse')
		{
			$request['mime'] 	   		  	= $file->getClientMimeType();
			$request['original_filename'] 	= $file->getClientOriginalName();
			$request['ext']					= $file->getClientOriginalExtension();
			$request['filename'] 			= $file->getFilename().'.'.$request['ext'];
			$request['document_extension'] 	= $request['ext'];
		}
		
		elseif($request['webcam_image'] == 1 && $request['upload_type'] == 'webcam') 
		{ // Get webcam image from temporary storage
			$src = url().'/media/'.$request['document_type'].'/'.Auth::user()->id.'/'.$request['webcam_filename'];
			$mime_type = getimagesize($src);
			$request['ext'] = pathinfo($src, PATHINFO_EXTENSION);
			$request['mime'] 	   		  	= $mime_type['mime'];
			$request['original_filename'] 	= $request['webcam_image'];
			$request['filename'] 			= $request['webcam_filename'];			 
			$request['document_extension'] 	= $request['ext'];
		} 
		elseif($request['scanner_image'] == 1 && $request['upload_type'] == 'scanner') 
		{ // Get webcam image from temporary storage
			$src 		= $request['scanner_filename'];
			$file 		= ''; 
			$request['ext']	= pathinfo($src, PATHINFO_EXTENSION);
		}
		else 
		{
			return 'error';	
		}
		
		return $request;
	}
	/*** Start get file related value for adding process ***/
	
	public function getArray($error)
	{
		$singleArray = [];
		foreach ($error as $key => $value){
			$singleArray[$key] = $value[0];
		}
		return $singleArray;
	}
	
	public function getDynamicDocumentApi(){
		$request = Request::all();
		$title = $request['title'];
		$document_data = '';
		if(strpos($title,"Eligibility") !== false) {
			$title = "Eligibility & Benefits";
		}	
		switch ($request['type']) {
			case 'summery':
				$document_data = $this->document_ajax_summery($title);
				break;
			case 'assigned':
				$document_data = $this->document_ajax_assigned($title);
				break;
			case 'all':
				$document_data = $this->document_ajax_all($title);
				break;
			default:
				$document_data = $this->document_ajax_common($title);
		}
		$users = Users::where('customer_id', Auth::user()->customer_id)->where('status', 'Active')->pluck('name', 'id')->all();
		$categories = [];
		if(isset($request['model'])) {
			$categories = DocumentCategories::distinct('category')->where('category', '!=', '')->where('module_name', $request['model'])->pluck('category')->all();
		}		
		$patients = Patient::Has('patient_document')->where('status', 'Active')->select(DB::raw('CONCAT(last_name, ", ", first_name, " ", middle_name) AS full_name'), 'id')->value('full_name');
		$insurances	= Insurance::where('status', 'Active')->pluck('short_name')->all();
		return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('document_data', 'users', 'categories', 'patients', 'insurances')));	
	}
	
	public function document_ajax_summery($title){
		
		$result_data['other_document']			=	Document::whereHas('document_categories' , function($query){ 
													$query->where('category','Other Documents');						
													})
													->with('user','document_categories','document_followup')
													->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
													->orderBy('id','DESC')
													->get()
													->count();
								
		$result_data['prescription_document']	=	Document::whereHas('document_categories' , function($query){ 
													$query->where('category','Prescription');						
													})
													->with('user','document_categories','document_followup')
													->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
													->orderBy('id','DESC')
													->get()
													->count();
								
		$result_data['patient_corresp_document']	=	Document::whereHas('document_categories' , function($query){ 
														$query->where('category','Patient Letters');						
														})
														->with('user','document_categories','document_followup')
														->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
														->orderBy('id','DESC')
														->get()
														->count();
								
		$result_data['payer_document']				=	Document::whereHas('document_categories' , function($query){ 
														$query->where('category','Payer Reports');						
														})
														->with('user','document_categories','document_followup')
														->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
														->orderBy('id','DESC')
														->get()
														->count();
								
		$result_data['edi_document']				=	Document::whereHas('document_categories' , function($query){ 
														$query->where('category','EDI Reports');						
														})
														->with('user','document_categories','document_followup')
														->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
														->orderBy('id','DESC')
														->get()
														->count();
								
		$result_data['procedure_document']			=	Document::whereHas('document_categories' , function($query){ 
														$query->where('category','Procedure Documents');						
														})
														->with('user','document_categories','document_followup','document_followup')
														->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
														->orderBy('id','DESC')
														->get()
														->count();
								
		$result_data['clinical_document']			=	Document::whereHas('document_categories' , function($query){ 
														$query->where('category','Clinical Documents');						
														})
														->with('user','document_categories','document_followup')
														->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
														->orderBy('id','DESC')
														->get()
														->count();
								
		$result_data['authorization_document']		=	Document::whereHas('document_categories' , function($query){ 
														$query->where('category','Authorization Documents');						
														})
														->with('user','document_categories','document_followup')
														->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
														->orderBy('id','DESC')
														->get()
														->count();
								
		$result_data['eligibility_document']	=	Document::whereHas('document_categories' , function($query){ 
													$query->where('category','Eligibility & Benefits');						
													})
													->with('user','document_categories','document_followup')
													->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
													->orderBy('id','DESC')
													->get()
													->count();
								
		$result_data['patient_document']	=	Document::whereHas('document_categories' , function($query){ 
												$query->where('category','Patient Documents');						
												})
												->with('user','document_categories','document_followup')
												->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
												->orderBy('id','DESC')
												->get()
												->count();
												
		$result_data['facility_document']	=	Document::whereHas('document_categories' , function($query){ 
												$query->where('module_name','facility');						
												})
												->with('user','document_categories','document_followup')
												->whereRaw('(document_type = "facility" or document_type = "facility_document") and (document_sub_type = "" and type_id != "")')
												->orderBy('id','DESC')
												->get()
												->count();
												
												
		$result_data['provider_document']	=	Document::whereHas('document_categories' , function($query){ 
													$query->where('module_name','provider');						
												})
												->with('user','document_categories','document_followup')
												->whereRaw('(document_type = "provider" or document_type = "provider_document") and (document_sub_type = "" and type_id != "")')
												->orderBy('id','DESC')
												->get()
												->count();
												
		$result_data['group_document']	=	Document::whereHas('document_categories' , function($query){ 
													$query->where('module_name','group');						
												})
												->with('user','document_categories','document_followup')
												->whereRaw('(document_type = "group" or document_type = "common") and (document_sub_type = "" and type_id != "")')
												->orderBy('id','DESC')
												->get()
												->count();
												
		$result_data['type'] 				= 'summery'; 
		$result_data['title'] 				= $title; 
		
		return $result_data;			
	}
	
	public function document_ajax_assigned($title, $request=''){
		$result_data['type'] 				= 'assigned'; 
		$result_data['title'] 				= $title;		
		/*if(isset($request) && !empty($request)){
			$patient_document = $faclility_document = $provider_document = $this->common_search_filter($request);
		} */
		if(isset($request) && !empty($request)){ 
			$patient_document = $this->common_search_filter($request);
			$assigned_patient_document 			=	$patient_document->whereHas('document_followup',function($query){
												$query->where('assigned_user_id',Auth::user()->id)->where('status','!=','Completed')->where('Assigned_status','Active');	
												})
												->with('user','document_categories','document_followup','patients')
												->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->orderBy('id','DESC')
												->get()
												->toArray();

			$faclility_document  =$this->common_search_filter($request);
			$assigned_faclility_document 		=	$faclility_document->whereHas('document_followup',function($query){
													$query->where('assigned_user_id',Auth::user()->id)->where('status','!=','Completed')->where('Assigned_status','Active');	
													})
													->with('user','document_categories','document_followup','facility')
													->whereRaw('temp_type_id = "" and (document_type = "facility" or document_type = "facility_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
													->orderBy('id','DESC')
													->get()
													->toArray();
													
			$provider_document = $this->common_search_filter($request);
			$assigned_provider_document 		=	$provider_document->whereHas('document_followup',function($query){
													$query->where('assigned_user_id',Auth::user()->id)->where('status','!=','Completed')->where('Assigned_status','Active');	
													})
													->with('user','document_categories','document_followup','provider')
													->whereRaw('temp_type_id = "" and (document_type = "provider" or document_type = "provider_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
													->orderBy('id','DESC')
													->get()
													->toArray();

		} else{
		$from_date = date('Y-m-01');
		$to_date = date('Y-m-d');//dd($from_date);
		$from_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($from_date);
		$to_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($to_date);
			$assigned_patient_document 			=Document::whereHas('document_followup',function($query){
												$query->where('assigned_user_id',Auth::user()->id)->where('status','!=','Completed')->where('Assigned_status','Active');	
												})
												->with('user','document_categories','document_followup','patients')
												->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->whereRaw("DATE(created_at) >= '$from_date' and DATE(created_at) <= '$to_date'")
												->orderBy('id','DESC')
												->get()
												->toArray();

			
			$assigned_faclility_document 		=	Document::whereHas('document_followup',function($query){
												$query->where('assigned_user_id',Auth::user()->id)->where('status','!=','Completed')->where('Assigned_status','Active');	
												})
												->with('user','document_categories','document_followup','facility')
												->whereRaw('temp_type_id = "" and (document_type = "facility" or document_type = "facility_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->whereRaw("DATE(created_at) >= '$from_date' and DATE(created_at) <= '$to_date'")
												->orderBy('id','DESC')
												->get()
												->toArray();
			$assigned_provider_document 		=	Document::whereHas('document_followup',function($query){
												$query->where('assigned_user_id',Auth::user()->id)->where('status','!=','Completed')->where('Assigned_status','Active');	
												})
												->with('user','document_categories','document_followup','provider')
												->whereRaw('temp_type_id = "" and (document_type = "provider" or document_type = "provider_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->whereRaw("DATE(created_at) >= '$from_date' and DATE(created_at) <= '$to_date'")
												->orderBy('id','DESC')
												->get()
												->toArray();
		}
			
		
		$result_data['document_dynamic_patient_list'] = $assigned_patient_document;
		$result_data['document_dynamic_facility_list'] = $assigned_faclility_document;
		$result_data['document_dynamic_provider_list'] = $assigned_provider_document;
		return $result_data;
	}
	
	public function document_ajax_all($title, $request = ''){
		

		$result_data['type'] 				= 'all'; 
		$result_data['title'] 				= $title; 				
	/*	if(isset($request) && !empty($request)){
			$patient_document = $faclility_document = $provider_document =  $this->common_search_filter($request);
		}	*/
		if(isset($request) && !empty($request)){
			$patient_document = $this->common_search_filter($request);
			$assigned_patient_document 			=	$patient_document->with('user','document_categories','document_followup','patients')
												->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->orderBy('id','DESC')
												->get()
												->toArray();

		$faclility_document = $this->common_search_filter($request);
		$assigned_faclility_document 		=	$faclility_document->with('user','document_categories','document_followup','facility')
												->whereRaw('temp_type_id = "" and (document_type = "facility" or document_type = "facility_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->orderBy('id','DESC')
												->get()
												->toArray();
		$provider_document = $this->common_search_filter($request);										
		$assigned_provider_document 		=	$provider_document->with('user','document_categories','document_followup','provider')
												->whereRaw('temp_type_id = "" and (document_type = "provider" or document_type = "provider_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->orderBy('id','DESC')
												->get()
												->toArray();
		
		$group_document = $this->common_search_filter($request);											
		$assigned_group_document 		=	$group_document->with('user','document_categories','document_followup')
												->whereRaw('temp_type_id = "" and (document_type = "group" or document_type = "group_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->orderBy('id','DESC')
												->get()
												->toArray();
		
		
		} else{
		$from_date = date('Y-m-01');
		$to_date = date('Y-m-d');//dd($from_date);
		$from_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($from_date);
		$to_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($to_date);
		$assigned_patient_document 			=	Document::with('user','document_categories','document_followup','patients')
												->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->whereRaw("DATE(created_at) >= '$from_date' and DATE(created_at) <= '$to_date'")
												->orderBy('id','DESC')
												->get()
												->toArray();

		$assigned_faclility_document 		=	Document::with('user','document_categories','document_followup','facility')
												->whereRaw('temp_type_id = "" and (document_type = "facility" or document_type = "facility_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->whereRaw("DATE(created_at) >= '$from_date' and DATE(created_at) <= '$to_date'")
												->orderBy('id','DESC')
												->get()
												->toArray();
												
		$assigned_provider_document 		=	Document::with('user','document_categories','document_followup','provider')
												->whereRaw('temp_type_id = "" and (document_type = "provider" or document_type = "provider_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->whereRaw("DATE(created_at) >= '$from_date' and DATE(created_at) <= '$to_date'")
												->orderBy('id','DESC')
												->get()
												->toArray();
												
		$assigned_group_document 		=	Document::with('user','document_categories','document_followup')
												->whereRaw('temp_type_id = "" and (document_type = "group" or document_type = "group_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->whereRaw("DATE(created_at) >= '$from_date' and DATE(created_at) <= '$to_date'")
												->orderBy('id','DESC')
												->get()
												->toArray();
		}
		
		$result_data['document_dynamic_patient_list'] = $assigned_patient_document;
		$result_data['document_dynamic_facility_list'] = $assigned_faclility_document;
		$result_data['document_dynamic_provider_list'] = $assigned_provider_document;
		$result_data['document_dynamic_group_list'] = $assigned_group_document;
		return $result_data;
	}

	public function common_search_filter ($request){

		$document = Document::orderBy('id','DESC')->where("deleted_at", NULL);	
			
		if(!empty($request['date_option']) || (!empty($request['from_date']) && !empty($request['to_date']))){			
			 $from_date = date('Y-m-d', strtotime($request['from_date']));
             $to_date = date('Y-m-d', strtotime($request['to_date']));//dd($from_date);
       		 $from_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($from_date);
             $to_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($to_date);
             $document = $document->whereRaw("DATE(created_at) >= '$from_date' and DATE(created_at) <= '$to_date'");
		}

		if(!empty($request['checkdate_start']) && !empty($request['checkdate_end'])){			
			 $from_date = date('Y-m-d', strtotime($request['checkdate_start']));
             $to_date = date('Y-m-d', strtotime($request['checkdate_end']));
             $document = $document->whereRaw("DATE(checkdate) >= '$from_date' and DATE(checkdate) <= '$to_date'");
		}

		if(!empty($request['followup_start']) && !empty($request['followup_end'])){			
			 $from_date = date('Y-m-d', strtotime($request['followup_start']));
             $to_date = date('Y-m-d', strtotime($request['followup_end']));
             $document = $document->whereHas('document_followup' , function($query) use($from_date, $to_date){ 
				$query->whereRaw("DATE(followup_date) >= '$from_date' and DATE(followup_date) <= '$to_date'")->where('Assigned_status', 'Active');
			});
		}		

		if(!empty($request['insurance'])) {
		    if($request['insurance'] == "all") {
		     	$document = $document->where('payer', '!=', '');
		    } else{
		     	$document = $document->where('payer', $request['insurance']);	
		    }             
		}

		if(!empty($request['check_number'])){								
            $document = $document->where('checkno', 'like', '%'.$request['check_number'].'%');
		}

		if(!empty($request['check_amt_start']) || !empty($request['check_amt_end'])){
			$amt_start =$request['check_amt_start'];
         	$amt_end = $request['check_amt_end'];
         	if(empty($amt_end) && !empty($amt_start)){
         		$document = $document->where("checkamt",">=", $amt_start)->where('checkamt', '!=', '');
         	} else {
         		$document = $document->whereRaw("checkamt >= ? and checkamt <= ?", array($amt_start, $amt_end))->where('checkamt', '!=', '');
         	}             	
		}	
			
		if(!empty($request['user'])){								
             $document = $document->where('created_by', $request['user']);
		}

		if(!empty($request['assigned_to'])){             	
         	$assigned_to = $request['assigned_to'];
         	$document = $document->whereHas('document_followup', function($query) use($assigned_to){
         			$query->where('assigned_user_id', $assigned_to)->where('Assigned_status', "Active");
         	});
		}

		if(!empty($request['patient']))	{
            $document = $document->where('type_id', $request['patient'])->where('document_type', 'patients');
		}

		if(!empty($request['file_type'])){				
            $document = $document->whereIn('document_extension', $request['file_type']);
		}	

		if(!empty($request['status'])){	
			$status = $request['status'];
         	$document = $document->whereHas('document_followup', function($query) use($status){
         			$query->where('status', $status)->where('Assigned_status', "Active");
         	});
		}
		
		if(!empty($request['category'])){	
			$category = $request['category'];
         	$document = $document->where('category',$category);         	
		}	

		if(!empty($request['document_type'])){	
			$document_type = $request['document_type'];
         	$document = $document->where('document_type',$document_type);
		}	
		return $document;	
	}

	public function document_ajax_common($title, $request=null){
		$result_data['type'] 				= 'common'; 
		$result_data['title'] 				= $title;
		$patient_document = $faclility_document = $provider_document = $group_document = '';
		$document = Document::orderBy('id','DESC')->where("deleted_at", NULL);
		if(isset($request) && !empty($request)){
			$document = $this->common_search_filter($request);
		}	
		
		if($title == 'Facility Documents'){
			$faclility_document	=	$document->whereHas('document_categories' , function($query) use($title){ 
										$query->where('module_name','facility');						
									})
									->with('user','document_categories','document_followup','facility')
									->whereRaw('(document_type = "facility" or document_type = "facility_document") and (document_sub_type = "" and type_id != "")')
									->orderBy('id','DESC')
									->get()
									->toArray();
			$result_data['category_type']	= "facility";
		}elseif($title == 'Provider Documents'){
			$provider_document	=	$document->whereHas('document_categories' , function($query) use($title){ 
										$query->where('module_name','provider');						
									})
									->with('user','document_categories','document_followup','provider')
									->whereRaw('(document_type = "provider" or document_type = "provider_document") and (document_sub_type = "" and type_id != "")')
									->orderBy('id','DESC')
									->get()
									->toArray();
			$result_data['category_type']	= "provider";
		}elseif($title == 'Group Documents'){
			$group_document	=	$document->whereHas('document_categories' , function($query) use($title){ 
									$query->where('module_name','group');						
									})
									->with('user','document_categories','document_followup','provider')
									->whereRaw('(document_type = "group" or document_type = "group_document") and (document_sub_type = "")')
									->orderBy('id','DESC')
									->get()
									->toArray();
			$result_data['category_type']	= "group";
		}else{								
			$patient_document	=	$document->whereHas('document_categories' , function($query) use($title){ 
										$query->where('category',$title);						
									})
									->with('user','document_categories','document_followup','patients')
									->whereRaw('(document_type = "patients" or document_type = "patient_document") and (document_sub_type = "" and type_id != "")')
									->orderBy('id','DESC')
									->get()
									->toArray();
									//print_r($patient_document);
			$result_data['category_type']	= "patient";
		}		
		$result_data['document_dynamic_patient_list'] = $patient_document;
		$result_data['document_dynamic_facility_list'] = $faclility_document;
		$result_data['document_dynamic_provider_list'] = $provider_document;	
		$result_data['document_dynamic_group_list'] = $group_document;
		return $result_data;
	}		
	
	public function getDynamicFilterDocumentApi(){
		$request = Request::all();
		$type = $request['doc_type'];
		$title = $request['title'];
		$document_data = '';
		if(strpos($title,"Eligibility") !== false) {
			$title = "Eligibility & Benefits";
		}		
		switch ($type) {
			case 'summery':
				$document_data = $this->document_ajax_summery($title);
				break;
			case 'assigned':
				$document_data = $this->document_ajax_assigned($title,$request);
				break;
			case 'all':
				$document_data = $this->document_ajax_all($title,$request);
				break;
			default:
				$document_data = $this->document_ajax_common($title, $request);
		}
		$users = Users::where('customer_id', Auth::user()->customer_id)->where('status', 'Active')->pluck('name', 'id')->all();
		
		$categories = [];
		if(isset($request['model'])) {
			$categories = DocumentCategories::distinct('category')->where('category', '!=', '')->where('module_name', $request['model'])->pluck('category')->all();
		}		
		$patients = Patient::Has('patient_document')->where('status', 'Active')->select(DB::raw('CONCAT(last_name, ", ", first_name, " ", middle_name) AS full_name'), 'id')->pluck('full_name')->all();	
		$insurances	= Insurance::where('status', 'Active')->pluck('short_name')->all();
		return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('document_data', 'users', 'categories', 'patients', 'insurances')));	
	}
	
	public function document_ajax_filter_all($title,$request){
		
		$result_data['type'] 				= 'all'; 
		$result_data['title'] 				= $request['title']; 
		$assigned_patient_document 			=	Document::with('user','document_categories','document_followup','patients')
												->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))');
												
		if($request['from_date'] != '' && $request['to_date'] != ''){
			$assigned_patient_document->where('created_at','>=',date("Y-m-d",strtotime($request['from_date'])))->where('created_at','<=',date("Y-m-d",strtotime($request['to_date'])));
		}										
																								
		$ass_pat_doc = $assigned_patient_document->orderBy('id','DESC')->get()->toArray();										
												
		$assigned_faclility_document 		=	Document::with('user','document_categories','document_followup','facility')
												->whereRaw('temp_type_id = "" and (document_type = "facility" or document_type = "facility_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->orderBy('id','DESC')
												->get()
												->toArray();
		$assigned_provider_document 		=	Document::with('user','document_categories','document_followup','provider')
												->whereRaw('temp_type_id = "" and (document_type = "provider" or document_type = "provider_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->orderBy('id','DESC')
												->get()
												->toArray();
												
		$assigned_group_document 		=	Document::with('user','document_categories','document_followup')
												->whereRaw('temp_type_id = "" and (document_type = "group" or document_type = "group_document") and ((document_sub_type = "" and type_id != "") or (main_type_id != "" and document_sub_type = ""))')
												->orderBy('id','DESC')
												->get()
												->toArray();
												
		$result_data['document_dynamic_patient_list'] = $ass_pat_doc;
		$result_data['document_dynamic_facility_list'] = $assigned_faclility_document;
		$result_data['document_dynamic_provider_list'] = $assigned_provider_document;
		$result_data['document_dynamic_group_list'] = $assigned_group_document;
		return $result_data;
	}

  	function downloadBulkDocument($document_ids){
		$default_view = Config::get('siteconfigs.production.defult_production');  
		$user_id = Auth::user()->id;
		if(App::environment() == $default_view)
			$path = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
		else
			$path = public_path().'/';
		$path_archive = $path.'/media/document/'.$user_id;
		$zipname  = time().'.zip'; 
		$i = 0;
		$document_ids = explode(",", $document_ids);
		foreach($document_ids as $document_id){
			$id = Helpers::getEncodeAndDecodeOfId($document_id,'decode');
			if(Document::where("id", $id)->count() >0)   {
			 	$picture = Document::where("id", $id)->first()->toArray(); 			
				$document_path = str_replace("//", "/", $picture['document_path']);
				$file[$i] = $picture['document_domain'].'/'.$document_path.$picture['filename'];
				$i++;
			}		 
		}        
		$this->create_zip($file, $path_archive, $zipname);
  	}

  	function create_zip($files = array(),$destination = '',$zipname) {         
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }                             
        $outputzippath = $destination.'/'.$zipname;  
		ob_end_clean();
        $zip = new ZipArchive;                
        $zip->open($outputzippath, ZipArchive::CREATE);                
        $i = 1;        
		$contextOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false
			)
		);
        foreach ($files as $key=>$filess) 
        {                    
            $currentdate = date('Y-m-d');
            $path_parts = pathinfo($filess);
			
            $content = file_get_contents($filess, false, stream_context_create($contextOptions));     
           	$zip->addFromString($key.'_'.$path_parts['filename'].'.'.$path_parts['extension'], $content);                        
            $i++;   
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($outputzippath));
        readfile($outputzippath);
    }

	function getPatientClaimsApi($patient_id){
		//$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		
		$claim_number = ClaimInfoV1::where('patient_id', $patient_id)->select(DB::raw("CONCAT(claim_number, ' - ', DATE_FORMAT(date_of_service, '%m/%d/%Y')) as claim_number_concat"), 'claim_number')->pluck('claim_number_concat', 'claim_number')->all();
		$claim_number['claim_number'] = $claim_number;
		return json_encode($claim_number);
	}

	public function paymentPostingUploadApi(){
		$request = Request::all();
		DB::beginTransaction();
		try{
			$file 	= Request::file('filefield');
			$set_err =	$src 	= '';
			$request['practice_id'] = Session::get('practice_dbid');
			$request['document_type'] = 'patients';
			$request['type_id'] = 0;
			$request['category'] = 'Payer_Reports_ERA_EOB';
			$response = $this->getValidation($file,$request);  
			$request 	= 	$this->getReqValue($response['max_size'],$file,$request);//Get file extesion and path info
			$request["document_categories_id"] = Document_categories::where("category_key",'Payer_Reports_ERA_EOB')->where('module_name','patients')->pluck("id")->first();		
			$temp_type_id = $request['temp_type_id'] =  substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 50)), 0, 50);				
			$data 				= Document::create($request);
			$file_store_name 	= md5($data->id.strtotime(date('Y-m-d H:i:s'))).'.'.$request['ext'];
			$store_arr  	  	= Helpers::amazon_server_folder_check($request['document_type'],$file,$file_store_name,$src);
			$data->filename 		= $file_store_name;
			$data->document_path   	= $store_arr[0];
			$data->document_domain 	= $store_arr[1];
			$data->save ();
			DocumentFollowupList::where('document_id',$data->id)->update(['Assigned_status'=>'Inactive']);
			$assign_data['document_id'] = $data->id;
			$assign_data['assigned_user_id'] = $request['assigned'];
			$assign_data['notes'] = $request['notes'];
			$assign_data['priority'] = ucfirst($request['priority']);
			$assign_data['followup_date'] = date('y-m-d',strtotime($request['followup']));
			$assign_data['status'] = ucfirst($request['status']);
			$assign_data['created_by'] = Auth::user()->id;
			$assigned_data = DocumentFollowupList::create($assign_data);
			$assigned_data->save();
			
			$affectedRows = User::where('id', Auth::user()->id)->increment('maximum_document_uploadsize',str_replace(',', '', $response['max_size']));
			DB::commit();
			return Response::json(array('status'=>'success', 'message'=>'Document added successfully','data'=>compact('temp_type_id')));
		}
		catch(\Exception $e){
			echo 'Error message: ' .$e->getMessage();die;
			DB::rollback();
		}
	}

}