<?php namespace App\Http\Controllers\Patients\Api;
use App;
use Input;
use Auth;
use Response;
use Session;
use DB;
use Request;
use Validator;
use Lang;
use App\Http\Controllers\Controller;
use App\Models\Patients\Patient as Patient;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Document as Document;
use App\Models\Practice as Practice;
use App\User as User;
use App\Models\Patients\PatientDocument as PatientDocument;
use App\Models\Patients\DocumentFollowupList as DocumentFollowupList;
use App\Models\Registration as Registration;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Document_categories as Document_categories;
use App\Models\DocumentCategories as DocumentCategories;
use Illuminate\Support\Collection;

use App\Http\Controllers\Documents\Api\DocumentApiController as DocumentApiController;
use App\Models\Insurance as Insurance;
use App\Models\Medcubics\Users as Users;

class PatientDocumentApiController extends Controller 
{
	/*** lists page Starts ***/
    public function getIndexApi($id)
	{
		$patient_id = $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(Patient::where('id', $id)->count()>0 && is_numeric($id)) 
		{
			
			$type_details = Patient::findOrFail($id); // Patients details 
			
			$temp = new Collection($type_details);
			$type_id = $temp['id'];
			$temp->pull('id');
			$typ_id = Helpers::getEncodeAndDecodeOfId($type_id, 'encode');
			$temp->prepend($typ_id, 'id');
			$typ = $temp->all();
			$type_details = json_decode(json_encode($typ), FALSE);

			// $type_details->id = Helpers::getEncodeAndDecodeOfId($type_details->id,'encode');
			$registration  = Registration::first();
			$type_details->registration = $registration;
			
			$claim_number = ClaimInfoV1::where('patient_id', $id)->select(DB::raw("CONCAT(claim_number, ' - ', DATE_FORMAT(date_of_service, '%m/%d/%Y')) as claim_number_concat"), 'claim_number')->pluck('claim_number_concat', 'claim_number')->all();
			
			$priority = array('high'=>'High','moderate'=>'Moderate','low'=>'Low');
			
			$category_list = DB::table('document_categories')->where(function($query){ $query->where('module_name', '=', 'patients');})->orderBy('category_value','ASC')->pluck('category_value', 'category_key')->all();
			 
			$user_list = Helpers::user_list();
			
			
			$other_document				=	PatientDocument::whereHas('document_categories' , function($query){ 
											$query->where('category','Other Documents');						
											})->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
									
			$prescription_document		=	PatientDocument::whereHas('document_categories' , function($query){ 
											$query->where('category','Prescription');						
											})->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
									
			$patient_corresp_document	=	PatientDocument::whereHas('document_categories' , function($query){ 
											$query->where('category','Patient Letters');						
											})->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
									
			$payer_document				=	PatientDocument::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->whereHas('document_categories' , function($query){ 
											$query->where('category','Payer Reports');						
											})
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
									
			$edi_document				=	PatientDocument::whereHas('document_categories' , function($query){ 
											$query->where('category','EDI Reports');						
											})->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
									
			$procedure_document			=	PatientDocument::whereHas('document_categories' , function($query){ 
											$query->where('category','Procedure Documents');						
											})->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
									
			$clinical_document			=	PatientDocument::whereHas('document_categories' , function($query){ 
											$query->where('category','Clinical Documents');						
											})->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
									
			$authorization_document		=	PatientDocument::whereHas('document_categories' , function($query){ 
											$query->where('category','Authorization Documents');						
											})->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
									
			$eligibility_document		=	PatientDocument::whereHas('document_categories' , function($query){ 
											$query->where('category','Eligibility & Benefits');						
											})->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
									
			$patient_document			=	PatientDocument::whereHas('document_categories' , function($query){ 
											$query->where('category','Patient Documents');						
											})->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
											
			$total_document_count 			=	PatientDocument::whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))										
											->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->orderBy('id','DESC')
											->get()
											->count();
							
			$assigned_document_count	=	DocumentFollowupList::whereHas('document',function($query) use($patient_id){
											$query->where('document_type','patients')->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($patient_id))
											->orderBy('id','DESC');	
											})
											->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->where('assigned_user_id',Auth::user()->id)
											->where('status','!=','Completed')->where('Assigned_status','Active')
											->groupBy('document_id')
											->get()
											->count();
											
			$inprocess_document_count	=	DocumentFollowupList::whereHas('document',function($query) use($patient_id){
											$query->where('type_id',$patient_id)->where('document_type','patients');	
											})
											->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->where('status','Inprocess')
											->groupBy('document_id')
											->get()
											->count();
			
			$pending_document_count		=	DocumentFollowupList::whereHas('document',function($query) use($patient_id){
											$query->where('type_id',$patient_id)->where('document_type','patients');	
											})
											->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->where('status','Pending')
											->groupBy('document_id')
											->get()
											->count();
			
			$review_document_count		=	DocumentFollowupList::whereHas('document',function($query) use($patient_id){
											$query->where('type_id',$patient_id)->where('document_type','patients');	
											})
											->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))	
											->where('status','Review')
											->groupBy('document_id')
											->get()
											->count();

			$completed_document_count	=	DocumentFollowupList::whereHas('document',function($query) use($patient_id){
											$query->where('type_id',$patient_id)->where('document_type','patients');	
											})
											->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))
											->where('status','Completed')
											->groupBy('document_id')
											->get()
											->count();
			
			
			/* Search page query  */
			
			$total_document 			=	PatientDocument::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('user','document_categories','document_followup','patients')->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();	
			
			/* Search page query  */
			
			
			/* Assigned Document list Query*/
			
			$assigned_document 			=	PatientDocument::whereHas('document_followup',function($query) use($id){
											$query->where('assigned_user_id',Auth::user()->id)->where('Assigned_status','Active')->where('status','!=','Completed');	
											})
											->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))	
											->with('user','document_categories','document_followup','patients')
											->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))
											->orderBy('id','DESC')
											->get()
											->toArray();
			
			/* Assigned Document list Query*/
			
			$pictures		=	PatientDocument::with('user','document_categories')->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($id))->orderBy('id','DESC')->get();
			//dd($pictures);
			$categories = [];
			$users = Users::where('customer_id', Auth::user()->customer_id)->where('status', 'Active')->pluck('name', 'id')->all();
			
			$categories = DocumentCategories::where('category', '!=', '')->where('module_name', 'patients')->pluck('category_value', 'category_key')->all();
			//dd($categories)					;
			$insurances	= Insurance::Has('patient_document_insurance')->where('status', 'Active')->pluck('short_name', 'id')->all();		
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('type_details','pictures', 'category_list', 'claim_number', 'priority','user_list','other_document','prescription_document','patient_corresp_document','payer_document','edi_document','procedure_document','clinical_document','authorization_document','eligibility_document','patient_document','total_document_count','assigned_document_count','inprocess_document_count','pending_document_count','review_document_count','completed_document_count','assigned_document','total_document', 'users', 'categories', 'patients','insurances')));
		}
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	} 
	/*** lists page Ends ***/ 
	
	/*** 	Author	 	:: Selvakumar V  
			Date 		:: 27-DEC-2017  
			Purpose	 	:: This function using to show the category based document lists and count.  
			Status		:: Start The Function Here
	***/
	
	
	public function getDocumentListingApi($id){ 
	
		$assigned_document_count = DocumentFollowupList::where('document_id',$id)->get()->count();
		if($assigned_document_count == 0){
			PatientDocument::where('id',$id)->update(['deleted_at'=>date('y-m-d h:i:s')]);
		}else{
			echo "Already assigned documents cannot be deleted.";
		}
		
		echo "<pre>";print_r($assigned_document);die;
	}
	
	
	/*** 	Author	 	:: Selvakumar V  
			Date 		:: 27-DEC-2017  
			Purpose	 	:: This function using to show the category based document lists and count.  
			Status		:: End The Function Here
	***/
	

	/*** Create page Starts ***/
	public function getCreateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode'); 
		if(Patient::where('id', $id)->count()>0 && is_numeric($id)) 
		{
			$category_list = DB::table('document_categories')->where(function($query){ $query->where('module_name', '=', 'patients')->orWhere('module_name', 'LIKE', 'patients')->orWhere('module_name', 'LIKE', 'patients,%')->orWhere('module_name', 'LIKE', '%,patients')->orWhere('module_name', 'LIKE', '%,patients,%');})->orderBy('category_value','ASC')->pluck('category_value', 'category_key')->all();
			$patients = Patient::findOrFail($id); 
			$registration  = Registration::first();
			if(!empty($registration))
			{
				if(!empty($registration->driving_license =="0"))
					unset($category_list['driving_license']); 
				if(!empty($registration->insured_ssn =="0"))
					unset($category_list['insured_ssn']);
			}
			$patients->id = Helpers::getEncodeAndDecodeOfId($patients->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('patients','category_list')));
		}
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function getAddDocumentApi($type,$id='',$request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Patient::where('id', $id)->count()>0 && is_numeric($id)) 
		{	
			$files = Request::file('filefield');
			if($request == '') $req = Request::all();
			$i = 0;
			$len = count($files);	
			foreach($files as $file) {
				$request = $req;
				$src = '';
				if(isset($request['sub_category_type']))
				{
					if($request['sub_category_type']!="no_sub_type" && $request['sub_category_type']!="")
					{
						$sub_category_avail = "yes";
					}
					else
					{
						$sub_category_avail = "no";
					}
				}
				else
				{
					$sub_category_avail = "no";
				}

				//To get maximum file upload size for each user to restrict file upload
				$max_size_upload = config('siteconfigs.maximum_file_upload.size');

				$max_user_upload = '';
				$max_size = '';	  
				if(Input::hasFile('filefield') && $request['upload_type'] == 'browse') {
					$max_size            = $this->SizeToKBUnits($file->getClientSize());
					if(is_numeric($max_size) && is_numeric(Auth::user()->maximum_document_uploadsize))
						$max_user_upload = $max_size+Auth::user()->maximum_document_uploadsize;
					else
						$max_user_upload = '1';
				} elseif($request['webcam_image'] == 1 && $request['upload_type'] == 'webcam') {
					if(App::environment() == 'production')
						$path = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
					else
						$path = public_path().'/';
					$file_size = filesize($path.'/media/'.$type.'/'.Auth::user()->id.'/'.$request['webcam_filename']);
					$max_size            = $this->SizeToKBUnits($file_size);
					if(is_numeric($max_size) && is_numeric(Auth::user()->maximum_document_uploadsize))
						$max_user_upload = $max_size+Auth::user()->maximum_document_uploadsize;
					else 
						$max_user_upload = '1';	
				} elseif($request['scanner_image'] == 1 && $request['upload_type'] == 'scanner') {
					$max_size = '1';
					$max_user_upload = '1';
				}
						
				if($request['upload_type'] == 'webcam' && $request['webcam_image'] == 1)
				{
					unset(PatientDocument::$rules['filefield']);
				}
				if($request['upload_type'] == 'scanner' && $request['scanner_image'] == 1)
				{
					unset(PatientDocument::$rules['filefield']);
				}

				/*** Get Image Details from Upload ***/
				$practice_id =Session::get('practice_dbid');
					$request['practice_id'] 		= $practice_id;	
					$request['document_type'] 		= $type;
					$request['user_email'] 			= Auth::user()->email;
					$request['created_by'] 			= Auth::user()->id;
					$request['main_type_id']		= $request['type_id']	= $id;
					$ttl = $i + 1;
					if($len == 1) {
						$request['title']		= $request['title'];
					} else {
						$request['title']		= $request['title']." - (".$ttl.")";
					}
					
					$request['filesize']		    = str_replace(',','',$max_size);					
					if(Input::hasFile('filefield') && $request['upload_type'] == 'browse')
					{
						$request['mime'] 	   		  	= $file->getClientMimeType();
						$request['original_filename'] 	= $file->getClientOriginalName();
						$extension 						= $file->getClientOriginalExtension();
						$request['filename'] 			= $file->getFilename().'.'.$extension;
						$request['document_extension'] 	= $extension;
					}
					elseif($request['webcam_image'] == 1 && $request['upload_type'] == 'webcam') 
					{ 
						// Get webcam image from temporary storage
						$src = url().'/media/'.$type.'/'.Auth::user()->id.'/'.$request['webcam_filename'];
						$mime_type = getimagesize($src);
						$ext = pathinfo($src, PATHINFO_EXTENSION);
						$request['mime'] 	   		  	= $mime_type['mime'];
						$request['original_filename'] 	= $request['webcam_image'];
						$extension 						= $ext;
						$request['filename'] 			= $request['webcam_filename'];			 
						$request['document_extension'] 	= $extension;
					}
					elseif($request['scanner_image'] == 1 && $request['upload_type'] == 'scanner') 
					{ 
						// Get webcam image from temporary storage
						$src 	= $request['scanner_filename'];
						$file 	= ''; 
						$ext 	= pathinfo($src, PATHINFO_EXTENSION);
						$extension 	= $ext;
					}
					else 
					{
						return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.upload"),'data'=>''));	
					}
					
					
				/*** Get Image Details from Upload ***/
				Validator::extend('chk_title_exists', function() use($request)
				{
					$categories_id = Document_categories::where("category_key",$request['category'])->where('module_name',$request['document_type'])->value("id");

					if(($request['type_id'] == 0 || empty($request['type_id'])) && !empty($request['temp_doc_id'])){
						return (PatientDocument::where('title',$request['title'])->where('document_categories_id',$categories_id)->where('temp_type_id',$request['temp_doc_id'])->count()>0) ? false : true;
					}else{
						return (PatientDocument::where('title',$request['title'])->where('document_categories_id',$categories_id)->where('type_id',$request['type_id'])->count()>0) ? false : true;
					}
				});
				Validator::extend('upload_mimes', function() use($request)
				{
					$attachement = config('siteconfigs.file_uplode.defult_file_attachment');
					$file_ext_arr = explode(",",str_replace("mimes:","",$attachement));
					return(in_array($request['document_extension'],$file_ext_arr)) ? true:false;
				});
				Validator::extend('upload_limit', function() use($max_user_upload,$max_size_upload)
				{
					return true;
				});
				$rules 		= array_merge(PatientDocument::$rules,array('title' => 'required|chk_title_exists'));
				$msg 		= PatientDocument::messages()+array('title.chk_title_exists' => Lang::get("common.validation.title_unique"));
				$validator  = Validator::make($request, $rules, $msg);

				if($validator->fails())
				{
					$errors = $validator->errors();
					return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
				}
				else
				{
					DB::beginTransaction();
					try{
					
						if(isset($request['temp_doc_id']))	
							$request['temp_type_id'] = $request['temp_doc_id'];
						else								
							$request['temp_type_id'] = "";
						$request["document_categories_id"] = Document_categories::where("category_key",$request['category'])->where('module_name',$request['document_type'])->value("id");
						
						if(!empty($request['claim_number'])) {
							foreach($request['claim_number'] as $claim_number) { 
								$request['description'] = @$request['notes'];
								if(!empty($request['checkdate']))
									$request['checkdate'] = date('Y-m-d',strtotime($request['checkdate']));
								$data 				= PatientDocument::create($request);
								$file_store_name 	= md5($data->id.strtotime(date('Y-m-d H:i:s'))).'.'.$extension;
								$store_arr  	   	= Helpers::amazon_server_folder_check($type,$file,$file_store_name,$src);
								$data->filename     = $file_store_name;
								$data->document_path   = $store_arr[0];
								$data->document_domain = $store_arr[1];
								$data->claim_number_data = $claim_number;
								$data->claim_id = $claim_number;
								$data->save ();
								DocumentFollowupList::where('document_id',$data->id)->update(['Assigned_status'=>'Inactive']);
								$assign_data['document_id'] = $data->id;
								$assign_data['patient_id'] = $data->type_id;
								$assign_data['claim_id'] = $claim_number;
								$assign_data['assigned_user_id'] = $request['assigned'];
								$assign_data['priority'] = $request['priority'];
								$assign_data['notes'] = @$request['notes'];
								$assign_data['followup_date'] = date('y-m-d',strtotime($request['followup']));
								$assign_data['status'] = ucfirst($request['status']);
								$assign_data['created_by'] = Auth::user()->id;
								$assigned_data = DocumentFollowupList::create($assign_data);
								$assigned_data->save();
							}
							
						} else{
							$request['description'] = @$request['notes'];
							$data 				= PatientDocument::create($request);
							$file_store_name 	= md5($data->id.strtotime(date('Y-m-d H:i:s'))).'.'.$extension;
							$store_arr  	   	= Helpers::amazon_server_folder_check($type,$file,$file_store_name,$src);
							$data->filename 		  = $file_store_name;
							$data->document_path   = $store_arr[0];
							$data->document_domain = $store_arr[1];
							$data->save ();
							
							DocumentFollowupList::where('document_id',$data->id)->update(['Assigned_status'=>'Inactive']);
							$assign_data['document_id'] = $data->id;
							$assign_data['patient_id'] = $data->type_id;
							$assign_data['claim_id'] = '';
							$assign_data['assigned_user_id'] = $request['assigned'];
							$assign_data['priority'] = $request['priority'];
							$assign_data['notes'] = @$request['notes'];
							$assign_data['followup_date'] = date('y-m-d',strtotime($request['followup']));
							$assign_data['status'] = ucfirst($request['status']);
							$assign_data['created_by'] = Auth::user()->id;
							$assigned_data = DocumentFollowupList::create($assign_data);
							$assigned_data->save();
						}				
						
						$affectedRows = User::where('id', Auth::user()->id)->increment('maximum_document_uploadsize', str_replace(',', '', $max_size));	  
						DB::commit();						
						if($i == $len - 1) {
							return Response::json(array('status'=>'success', 'message'=>'Documents added successfully','data'=>null));
						}
					}
					catch(\Exception $e){
						dd($e);
						echo 'Error message: ' .$e->getMessage();die;
						DB::rollback();
					}
				}
			$i++;
			}
		}
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	
	}
	/*** Store Function Ends ***/
	
	/*** Size conversion Starts ***/
	function SizeToKBUnits($bytes)
    {
		$bytes = number_format($bytes / 1024, 2) ;
		return $bytes;
	}
	/*** Size conversion Ends ***/
	
	/*** Delete Function Starts ***/
	public function getDestroyApi($type,$id,$patient_id)
	{
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id)) 
		{
			if(PatientDocument::where('id', $id)->count()>0 && is_numeric($id)) 
			{
				$assigned_document_count = DocumentFollowupList::where('document_id',$id)->where('assigned_user_id',Auth::user()->id)->where('created_by',Auth::user()->id)->where('Assigned_status','Active')->where('status','Assigned')->get()->count();
				if($assigned_document_count == 1){
					$getdata = PatientDocument::where('id',$id)->first();
					$file_size = $getdata->filesize;
					$getdata->delete();
					// To reduce from maximum upload size
					$affectedRows = User::where('id', Auth::user()->id)->decrement('maximum_document_uploadsize', str_replace(',', '', $file_size));
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
				}else{
					return Response::json(array('status' => 'failure_document', 'message' => "Already assigned documents cannot be deleted.", 'data' => ''));
				}
			}
			else 
			{
				return Response::json(array('status' => 'failure_document', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
			}
		}
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}	
	}
	/*** Delete Function Starts ***/
	
	
	public function getAssignedApiList($patient_id,$id){
		$practice_timezone = Helpers::getPracticeTimeZone();
		$assigned_document				=	DocumentFollowupList::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->where('document_id',$id)->orderBy('id','DESC')->get()->toArray();
		$practice = $user_list = Helpers::user_list();
		$claims_number	= ClaimInfoV1::where('id',$id)->pluck('claim_number')->first();
		return Response::json(array('status' => 'success', 'message' => null,'data' => compact('practice','assigned_document','patient_id','claims_number','id')));
	}
	
	public function getStoreAssignedApiList($doc_id){
		$request = Request::all();
		$document_details = PatientDocument::where('id',$doc_id)->first()->toArray();		
		DocumentFollowupList::where('document_id',@$document_details['id'])->update(['Assigned_status'=>'Inactive']);
		$assign_data['document_id'] = @$document_details['id'];
		$assign_data['patient_id'] = @$document_details['type_id'];
		$assign_data['assigned_user_id'] = @$request['assign_user_id'];
		$assign_data['priority'] = @$request['priority'];
		$assign_data['followup_date'] = date('y-m-d',strtotime($request['fllowup_date']));
		$assign_data['status'] = ucfirst(@$request['status']);
		$assign_data['notes'] = @$request['notes'];
		$assign_data['created_by'] = Auth::user()->id;
		$assigned_data = DocumentFollowupList::create($assign_data);
		$update_info = [];		                               
		if($assigned_data){
			$followuplists = DocumentFollowupList::with('assigned_user')->where("id", $assigned_data->id)->first();			
            $class = ((date("m/d/y") >= date("m/d/y",strtotime($request['fllowup_date'])))?"med-red":(date("m/d/y") == date("m/d/y",strtotime($request['fllowup_date'])))?"med-orange":"med-gray");
            $priority = $followuplists->priority;
             $fclass = ($priority == "Low")?"fa-arrow-down":(($priority == "High")?"fa-arrow-up":"fa-arrows-h");
			$priority_data = '<span class="'.$priority.'"><i class="fa '.$fclass.' data-toggle="tooltip" data-original-title="'.$priority.'" aria-hidden="true"></i></span>'   ; 
			$update_info['jsfollowup']= '<span class="'.$class.'">'.date("m/d/y",strtotime($request['fllowup_date'])).'</span>';
			$update_info['jspriority']= $priority_data;
			$update_info['jsstatus']= '<span class="'.$followuplists->status.'">'.$followuplists->status.'</span>';
			$update_info['jsuser']= $followuplists->assigned_user->short_name;
		}
		return json_encode($update_info);
		
	}
	public function getFiltersearchDocument($patient_id){
		$request = Request::all();
		$practice_timezone = Helpers::getPracticeTimeZone();
		$document = new DocumentApiController();
		$document = $document->common_search_filter($request);
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		$total_document = $document->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('user','document_categories','document_followup','patients')->where('type_id', $patient_id)->where('document_type', 'patients')->get()->toArray();
		return Response::json(array('status' => 'success', 'message' => null,'data' => compact('total_document')));
		
	}
	 public function getPatientDocumentApi($patient_id)
	{	
		$practice_timezone = Helpers::getPracticeTimeZone();
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		if(Patient::where('id', $patient_id)->count()>0) 
		{
			$total_document = Document::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('user','document_categories','document_followup','patients')->where('type_id', $patient_id)->where('document_type', 'patients')->get()->toArray();
			return Response::json(array('status' => 'success', 'message' => null,'data' => compact('total_document')));
			
		}
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	} 


	
}