<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Practice as Practice;
use App\Models\Document as Document;
use App\Models\Facility as Facility;
use App\Models\Provider as Provider;
use App\Models\Registration as Registration;
use App\User as User;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response as Responseobj;
use App\Models\Document_categories as Document_categories;
use App\Models\Payments\ClaimInfoV1;
use Illuminate\Support\Collection;
use DB;
use View;
use Lang;
use Auth;
use Config;
use Request;
use Redirect;
use Session;
use Response;
use Validator;
use App\Models\Patients\DocumentFollowupList as DocumentFollowupList;
class DocumentApiController extends Controller 
{
	public function __construct()
    {
        $this->middleware('auth');
    }

	/*** Start Display a listing of the document ***/
	public function getIndexApi($type,$id='')
	{
		$practice_timezone = Helpers::getPracticeTimeZone();  
		$id 	= ($id !='') ? Helpers::getEncodeAndDecodeOfId($id,'decode') : '';
		if($type == 'practice')
		{
			$type_details 	= Practice::where("id",Session::get('practice_dbid'))->first();
			$id 			= $type_details->id;
		}
		elseif($type== 'facility') 
		{
			if(Facility::where('id','=',$id)->count())	
				$type_details = Facility::with('facility_address')->where('id',$id)->first();
			else										
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
		elseif($type== 'provider') 
		{
			if(Provider::where('id','=',$id)->count())	
				$type_details = Provider::with('degrees')->where('id', $id)->first();
			else										
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
		
		$documents = Document::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('user','document_categories','document_followup')->whereRaw('temp_type_id = "" and document_type = ? and ((document_sub_type = "" and type_id = ?) or (main_type_id = ? and document_sub_type = ""))', array($type,$id,$id))->orderBy('id','DESC')->get();
		//Encode ID for type_details
		$temp = new Collection($type_details);
		$temp_id = $temp['id'];
		$temp->pull('id');
		$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
		$temp->prepend($temp_encode_id, 'id');
		$data = $temp->all();
		$type_details = json_decode(json_encode($data), FALSE);
		//Encode ID for type_details
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('type_details', 'documents','type')));
	}
	/*** End Display a listing of the document ***/
		
	/*** Start Display document create page ***/
	public function getCreateApi($type,$id='')
	{
		$id 	= ($id !='') ? Helpers::getEncodeAndDecodeOfId($id,'decode') : '';
		$sub_category_type_list	= array();
		if($type == 'practice')
		{
		   $type_details 		= Practice::first();
		   $id 					= $type_details->id;
		   $sub_category_type_list['no_sub_type'] = "Practice";
		}
		elseif($type == 'facility')
		{
		   if(Facility::where('id','=',$id)->count())	
			   $type_details = Facility::with('facility_address')->where('id',$id)->first();
		   else											
			   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		   $sub_category_type_list['no_sub_type'] = "Facility";
		}
		elseif($type == 'provider')
		{
			if(Provider::where('id','=',$id)->count())	
				$type_details = Provider::with('degrees')->where('id', $id)->first();
			else											
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			$sub_category_type_list['no_sub_type'] = "Provider";
		}
		
		$sub_category_type_count = '';
		$cate_type_list_arr = DB::table('document_categories')->where(function($query)use ($type){ $query->where('module_name', '=', $type)->orWhere('module_name', 'LIKE', $type)->orWhere('module_name', 'LIKE', $type.',%')->orWhere('module_name', 'LIKE', '%,'.$type)->orWhere('module_name', 'LIKE', '%,'.$type.',%');})->where('deleted_at',NULL)->orderBy('category_value','ASC')->pluck('category_value', 'category_key')->all();
		
		$priority = array('high'=>'High','moderate'=>'Moderate','low'=>'Low');
			 
		$user_list = Helpers::user_list();
		
		//Encode ID for type_details
		$temp = new Collection($type_details);
		$temp_id = $temp['id'];
		$temp->pull('id');
		$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
		$temp->prepend($temp_encode_id, 'id');
		$data = $temp->all();
		$type_details = json_decode(json_encode($data), FALSE);
		//Encode ID for type_details
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('type_details','cate_type_list_arr','sub_category_type_count','sub_category_type_list','priority','user_list')));
	}
	/*** End Display document create page ***/

	/**** Start document add process in form***/
	public function getAddDocumentApi($type,$id='',$request='')
	{
		ini_set('max_execution_time', 0);
		$id 	= ($id !='') ? Helpers::getEncodeAndDecodeOfId($id,'decode') : '';
		$files 	= Request::file('filefield');
		if($request == '') 
			$req = Request::all();
		
		$set_err = '';
		if($type == 'facility')
		{
		   if(Facility::where('id','=',$id)->count()==0)	
			   $set_err="error";
		}
		elseif($type == 'provider')
		{
		   if(Provider::where('id','=',$id)->count()==0)	
			    $set_err="error";
		}
		if($set_err=="error")
			return Response::json(array('status'=>'error','message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));

		$i = 0;
		$len = count($files);
		foreach($files as $file) {
			$src 	= '';
			$request = $req;
			$response = $this->getValidation($file,$request);
			$request 	= 	$this->getReqValue("form",$type,$id,$response['max_size'],$file,$request);
			$ttl = $i + 1;
			if($len == 1) {
				$request['title']		= trim($request['title']);
			} else {
				$request['title']		= trim($request['title'])." - (".$ttl.")";
			}

			Validator::extend('chk_title_exists', function() use($request,$type)
			{
				$categories_id = Document_categories::where("category_key",$request['category'])->where('module_name',$type)->value("id");
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
				// return($response['status']=="error") ? false : true;
				return true;
			});
			$rules 		= array_merge(Document::$rules,array('title' => 'required|chk_title_exists'));
			$msg 		= Document::messages()+array('title.chk_title_exists' => Lang::get("common.validation.title_unique"));
			$validator  = Validator::make($request, $rules, $msg);
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$request 	= 	$this->getReqValue("form",$type,$id,$response['max_size'],$file,$request);
				if( $request =="error")
				{
					return Response::json(array('status'=>'error','message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));
				}
				else
				{
					DB::beginTransaction();
					try{
						$request["document_categories_id"] = Document_categories::where("category_key",$request['category'])->where('module_name',$type)->value("id");
						$request['description'] = @$request['notes'];
						$data 				= Document::create($request);
						$file_store_name 	= md5($data->id.strtotime(date('Y-m-d H:i:s'))).'.'.$request['ext'];
						$store_arr  	  	= Helpers::amazon_server_folder_check($type,$file,$file_store_name,$src);
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
							return Response::json(array('status'=>'success', 'message'=>"Documents added successfully",'data'=>''));
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
	/*** End document add process in form ***/
	
	/*** Start get show document ***/
	public function getGetApi($filename,$type,$id='')
	{
		$id 	= ($id !='') ? Helpers::getEncodeAndDecodeOfId($id,'decode') : '';
		if($type == 'practice')
		{
			$type_details = Practice::first();
			$id = $type_details->id;
		}
		if(Document::whereRaw('filename = ? and document_type = ? and ((document_sub_type = "" and type_id = ?) or (main_type_id = ? and document_sub_type = ""))', array($filename,$type,$id,$id))->count())
		{
			
			$picture = Document::whereRaw('filename = ? and document_type = ? and ((document_sub_type = "" and type_id = ?) or (main_type_id = ? and document_sub_type = ""))', array($filename,$type,$id,$id))->firstOrFail();
			return Response::json(array('status'=>'success', 'message' => null, 'data' => compact('picture')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/***  End Start get show document ***/
	
	/*** Start get required value for addinq process ***/
	public function getReqValue($from,$type,$id,$max_size,$file,$request)
	{
		$practice_id =Session::get('practice_dbid');
		if($type == 'practice' && $id=="") 
			$id = $practice_id;
		$request['practice_id'] = $practice_id;	
		$request['document_type'] 		= $type;
		$request['user_email'] 			= Auth::user()->email;
		$request['created_by'] 			= Auth::user()->id;
		$request['filesize']		    = str_replace(',','',$max_size);
		
		if(isset($request['temp_doc_id']))	
			$request['temp_type_id'] = $request['temp_doc_id'];
		else								
			$request['temp_type_id'] = "";
		if($from=="popup")
		{
			$request['type_id']	= $id;
		}
		else 
		{
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
			if($sub_category_avail=="no")
			{
				$request['type_id'] 	= $id;
			}
			else
			{
				$request['type_id'] 			= $request['sub_category_id'];
				$request['main_type_id'] 		= $id;
				//$request['document_sub_type'] 	= $request['sub_category_type'];
				$request['document_sub_type'] 	= '';
			}
		}
		if($request['upload_type'] == 'browse' && isset($request['filefield']))
		{
			$request['mime'] 	   		  	= $file->getClientMimeType();
			$request['original_filename'] 	= $file->getClientOriginalName();
			$request['ext']					= $file->getClientOriginalExtension();
			$request['filename'] 			= $file->getFilename().'.'.$request['ext'];
			$request['document_extension'] 	= $request['ext'];
		}		
		elseif($request['webcam_image'] == 1 && $request['upload_type'] == 'webcam') 
		{ // Get webcam image from temporary storage
			$src = url('/').'/media/'.$type.'/'.Auth::user()->id.'/'.$request['webcam_filename'];
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
	/*** Start delete document process ***/
	public function getDestroyApi($type,$id,$type_id='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$type_id = Helpers::getEncodeAndDecodeOfId($type_id,'decode'); 
		$set_err = '';
		if($type == 'facility' && $type_id!='')
		{
			if(Facility::where('id','=',$type_id)->count()==0)	
				$set_err="error";
		}
		elseif($type == 'provider' && $type_id!='')
		{
		   if(Provider::where('id','=',$type_id)->count()==0)	
			   $set_err="error";
		}
		if($set_err=="error")
			return Response::json(array('status'=>'error','message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));
		if (Document::where('id', $id)->count() && is_numeric($id)) 
		{
			$assigned_document_count = DocumentFollowupList::where('document_id',$id)->where('assigned_user_id',Auth::user()->id)->where('created_by',Auth::user()->id)->where('Assigned_status','Active')->where('status','Assigned')->get()->count();
			$total_count = DocumentFollowupList::where('document_id',$id)->get()->count();
			if($assigned_document_count == 1 && $total_count == 1){
				$getdata 	= Document::where('document_type',$type)->where('id',$id)->first();
				$file_size 	= $getdata->filesize;
				$getdata->delete();
				// To reduce from maximum upload size
				$affectedRows = User::where('id', Auth::user()->id)->decrement('maximum_document_uploadsize', str_replace(',', '', $file_size));
			}else{
				return Response::json(array('status' => 'error', 'message' => "Already assigned documents cannot be deleted.", 'data' => ''));
			}
			if($type == 'practice' || $type == 'facility' || $type == 'provider')
				$succ_message = Lang::get("common.validation.document_delete_msg");
			return Response::json(array('status'=>'success', 'message'=>$succ_message,'data'=>''));
		} 
		else 
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}	
	}
	/*** End delete document process ***/
	
	function SizeToKBUnits($bytes)
    {
      $bytes = number_format($bytes / 1024, 2) ;
      return $bytes;
	}
	
	// This function is for future reference and we are not using it anywhare
	function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }
        return $bytes;
	}
	
	/*** Start document modal popup show process ***/
	public function addDocumentmodelApi($type,$type_id,$category,$temp_doc_id="")
	{
		//dd($type,$type_id,$category,$temp_doc_id);
		$type_arr				= explode("::",$type);
		$document_type 			= $type_arr[0];
		$user_list = Helpers::user_list();
		//$document_sub_type 	=	(isset($type_arr[1]))? $type_arr[1] : '';
		$document_sub_type 	=	'';
		$main_type_id 		=	(isset($type_arr[2]))? $type_arr[2] : 0;
		//$main_type_id 		=	(isset($type_arr[2]))? $type_arr[2] : 0;
		$enc_type_id = $enc_main_type_id = 0;
		if($type_id!='0' && !is_numeric ($type_id))
		{
			$type_id = Helpers::getEncodeAndDecodeOfId($type_id,'decode');
			$enc_type_id = 1;
		}
		
		if($main_type_id!='0' && !is_numeric ($main_type_id))
		{
			$main_type_id = Helpers::getEncodeAndDecodeOfId($main_type_id,'decode');
			$enc_main_type_id = 1;
		}
		
		$document_list_count	= 0;
		$documents_list			= "";
		if($type_id!=0)
		{
			if($document_type=="patients"&&$category=="alldocument"){
				/* $documents_list = Document::whereRaw('document_type = ? and type_id = ? ', array($document_type,$type_id))->orderBy('created_at','DESC')->get(); */
				$documents_list		=	Document::whereHas('document_categories' , function($query) use($document_type){ 
										$query->where('module_name',$document_type);									
										})
										->with('user','document_categories','document_followup')
										->whereRaw('document_type = ? and type_id = ? ', array($document_type,$type_id))
										->orderBy('created_at','DESC')
										->get()
										->toArray();
			}
			else{
				/* $documents_list = Document::whereRaw('category = ? and document_type = ? and type_id = ? ', array($category,$document_type,$type_id))->orderBy('created_at','DESC')->get(); */
				$documents_list		=	Document::whereHas('document_categories' , function($query) use($document_type){ 
										$query->where('module_name',$document_type);					
										})
										->with('user','document_categories','document_followup')
										->whereRaw('category = ? and document_type = ? and type_id = ? ', array($category,$document_type,$type_id))
										->orderBy('created_at','DESC')
										->get()
										->toArray();
			}
			$document_list_count	= count($documents_list);
		}
		else
		{
			if($temp_doc_id!="")
			{
				$document_list_count	=	Document::whereRaw('category = ? and ((document_sub_type = "" and main_type_id = 0 and document_type = ? and temp_type_id = ?) or (document_sub_type = ? and main_type_id = ? and document_type = ? and temp_type_id = ?))', array($category,$document_type,$temp_doc_id,$document_sub_type,$main_type_id,$document_type,$temp_doc_id))->count();
				if($document_list_count>0)
				{
					/* $documents_list = Document::whereRaw('category = ? and ((document_sub_type = "" and main_type_id = 0 and document_type = ? and temp_type_id = ?) or (document_sub_type = ? and main_type_id = ? and document_type = ? and temp_type_id = ?))', array($category,$document_type,$temp_doc_id,$document_sub_type,$main_type_id,$document_type,$temp_doc_id))->orderBy('created_at','DESC')->get(); */
					
					$documents_list		=	Document::whereHas('document_categories' , function($query) use($document_type){ 
											$query->where('module_name',$document_type);						
											})
											->with('user','document_categories','document_followup')
											->whereRaw('category = ? and ((document_sub_type = "" and main_type_id = 0 and document_type = ? and temp_type_id = ?) or (document_sub_type = ? and main_type_id = ? and document_type = ? and temp_type_id = ?))', array($category,$document_type,$temp_doc_id,$document_sub_type,$main_type_id,$document_type,$temp_doc_id))
											->orderBy('created_at','DESC')
											->get()
											->toArray();
				}
				
			}
			else
			{
				$document_list_count	=	Document::whereRaw('category = ? and ((document_sub_type = "" and main_type_id = 0 and document_type = ? and type_id = ? and type_id!=0))', array($category,$document_type,$type_id))->count();
				if($document_list_count>0)
				{
					/* $documents_list = Document::whereRaw('category = ? and ((document_sub_type = "" and main_type_id = 0 and document_type = ? and type_id = ? and type_id!=0))', array($category,$document_type,$type_id))->orderBy('created_at','DESC')->get(); */
					$documents_list		=	Document::whereHas('document_categories' , function($query) use($document_type){ 
											$query->where('category','Patient Documents');						
											})
											->with('user','document_categories','document_followup')
											->whereRaw('category = ? and ((document_sub_type = "" and main_type_id = 0 and document_type = ? and type_id = ? and type_id!=0))', array($category,$document_type,$type_id))
											->orderBy('created_at','DESC')
											->get()
											->toArray();
				}
				
			}
		}
		$document_type_id		= $type_id;
		$document_category		= $category;
		
		
		$cate_type_list_arr = DB::table('document_categories')->where(function($query)use ($document_type){ $query->where('module_name', '=', $document_type)->orWhere('module_name', 'LIKE', $document_type)->orWhere('module_name', 'LIKE', $document_type.',%')->orWhere('module_name', 'LIKE', '%,'.$document_type)->orWhere('module_name', 'LIKE', '%,'.$document_type.',%');})->where('deleted_at',NULL)->orderBy('category_value','ASC')->pluck('category_value', 'category_key')->all();
		
		if($category == 'Authorization_Documents_Pre_Authorization_Letter'){
			$claim_number = ClaimInfoV1::where('patient_id', $type_id)->pluck('claim_number', 'claim_number')->all();
		}else{
			$claim_number = array();
		}
		
		if($type_id!='0' && $enc_type_id==1 && is_numeric($type_id))
		{
			$document_type_id = Helpers::getEncodeAndDecodeOfId($document_type_id,'encode');
		}
		if($main_type_id!='0' && $enc_main_type_id==1 && is_numeric($main_type_id))
		{
			$main_type_id = Helpers::getEncodeAndDecodeOfId($main_type_id,'encode');
		}
		$registration  = Registration::first();
		if(!empty($registration))
		{
			if(!empty($registration->driving_license =="0"))
				unset($cate_type_list_arr['driving_license']); 
			if(!empty($registration->insured_ssn =="0"))
				unset($cate_type_list_arr['insured_ssn']);
		}
		$priority = array('high'=>'High','moderate'=>'Moderate','low'=>'Low');
		
        return view ('practice/layouts/document_modal_popup', compact ( 'documents_list','document_list_count','document_type','document_sub_type','document_type_id','document_category','cate_type_list_arr','main_type_id','user_list','priority','claim_number'));
	}
	/*** End document modal popup show process ***/
	
	/*** Start document modal popup add process ***/
	public function getAddDocumentmodalApi($type,$id='',$request='')
	{	
		ini_set('max_execution_time', 0);
		if($id!='0' && !is_numeric ($id))
			$id 	= Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '') 
			$req= Request::all();
		$files = Request::file('filefield');	
		$i = 0;
		$len = count($files);
		foreach($files as $file) {
			$request = $req;
			$src 				= '';
			$type_arr			= explode("::",$type);
			$document_type 		= $type_arr[0];
			$request['document_sub_type'] 	=  (isset($type_arr[1])) ? $type_arr[1] : '';	
			$request['document_sub_type'] 	=  '';	
			$request['main_type_id'] 		=  ((isset($type_arr[2])) && (!is_numeric($type_arr[2]))) ? Helpers::getEncodeAndDecodeOfId($type_arr[2],'decode') : 0;
			$ttl = $i + 1;
			if($len == 1) {
			$request['title']		= trim($request['title']);
			} else {
			$request['title']		= trim($request['title'])." - (".$ttl.")";		
			}
			$response = $this->getValidation($file,$request);  
			$request 	= 	$this->getReqValue("form",$type,$id,$response['max_size'],$file,$request);
			$request['category'] = (@$request['category'])?	$request['category']: $request['document_category'] ;
			Validator::extend('chk_title_exists', function() use($request,$document_type)
			{
				$categories_id = Document_categories::where("category_key",$request['document_category'])->where('module_name',$document_type)->value("id");

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
				// return($response['status']=="error") ? false : true;
				return true;
			});
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
				$request 	= 	$this->getReqValue("popup",$document_type,$id,$response['max_size'],$file,$request);
				if( $request =="error")
				{
					return Response::json(array('status'=>'error','message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));
				}
				else
				{
					DB::beginTransaction();
					try{
						$request["document_categories_id"] = Document_categories::where("category_key",$request['category'])->where('module_name',$request['document_type'])->value("id");
						$request['main_type_id'] = ($request['main_type_id'] ==0 || $request['main_type_id'] =='') ? $request['type_id'] : $request['main_type_id'];
						if($request['document_sub_type'] == 'Authorization')
							$request['type_id'] = $request['main_type_id'];
						/* if($request['category'] == 'Authorization_Documents_Pre_Authorization_Letter')
							$request['type_id'] = $request['main_type_id']; */
						$request['document_sub_type'] 	=  '';	
						$request['description'] = @$request['notes'];
						if(!empty($request['claim_number']))
							$request['claim_number_data'] = @$request['claim_number'];
						
						$data 					= Document::create($request);
						$file_store_name 		= md5($data->id.strtotime(date('Y-m-d H:i:s'))).'.'.$request['ext'];
						$store_arr  	   		= Helpers::amazon_server_folder_check($document_type,$file,$file_store_name,$src);
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
						
						$affectedRows = User::where('id', Auth::user()->id)->increment('maximum_document_uploadsize', str_replace(',', '', $response['max_size']));
						DB::commit();
						if($i == $len - 1) {
							return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('ids')));
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
    /*** End document modal popup add process ***/
	public function getArray($error)
	{
		$singleArray = [];
		foreach ($error as $key => $value){
			$singleArray[$key] = $value[0];
		}
		return $singleArray;
	}
	/*** Start get Validation added process ***/
	public function getValidation($file,$request)
	{
		//To get maximum file upload size for each user to restrict file upload
		$response =[];
		$max_size_upload = config('siteconfigs.maximum_file_upload.size');
		$max_user_upload = '';
		$max_size = '';	 
		if(isset($request['filefield']) && $request['upload_type'] == 'browse')
		{
			$max_size            = $this->SizeToKBUnits($file->getClientSize());	
			$max_user_upload = $max_size+Auth::user()->maximum_document_uploadsize;
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
		if($max_user_upload > $max_size_upload)
		{
			// $response['status']= 'error';
			// $response['message']= Lang::get("common.validation.upload_limit");
		}
		return $response;
	}
	/*** Start get Validation added process ***/
	
	/*** Start get stored document in popup ***/
	public function getdocumentmodalApi($decode_id,$type,$filename)
	{
		$id = Helpers::getEncodeAndDecodeOfId($decode_id,'decode');
				$doc_details = Document::whereRaw('filename = ? and document_type = ? and (type_id = 0 or type_id = ? or main_type_id = ?)', array($filename,$type,$id,$id))->first();

		if(!empty($doc_details['filename']))
		{
			$picture = json_decode(json_encode($doc_details), true);
			$file = Helpers::amazon_server_get_file($picture['document_path'],$picture['filename']);
			return (new Responseobj ( $file, 200 ))->header ( 'Content-Type', $picture['mime']);
			
		}
		else
		{
			$path =  $type;
			if($type =="practice") 
				$path =  "/document";				
			if($id)
			{
				if($type =="facility") 
					$path =  $type."/".$decode_id."/facilitydocument";
				elseif($type =="provider") 
					$path =  $type."/".$decode_id."/providerdocuments";
				elseif($type =="patients") 
					$path =  $type."/".$decode_id."/documents";
			}
			
			return Redirect::to($path)->with('error',Lang::get("common.validation.empty_record_msg"));
		}
	}
	/*** End get stored document in popup ***/
	
	/*** Start get document sub category list ***/
	public function get_document_subgategory_list($id="")
	{
		if($id!="")
			$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$request 				 	= Request::all();	
		$document_type_datas_arr 	= explode("::",$request['document_type_datas']);
		$document_type			 	= $document_type_datas_arr[0];
		$sub_category_type		 	= $document_type_datas_arr[1];
		$result  					= array();	
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('result')));
	}
	/*** End get document sub category list ***/
	
	
	public function deletePopupDocumentApi($id)
	{
		if(Document::where('id', $id)->count() && is_numeric($id)) 
		{
			//$assigned_document_count = DocumentFollowupList::where('document_id',$id)->get()->count();
			// $assigned_document_count = DocumentFollowupList::where('document_id',$id)->where('assigned_user_id',Auth::user()->id)->where('created_by',Auth::user()->id)->where('Assigned_status','Active')->where('status','Assigned')->get()->count();
			// $total_count = DocumentFollowupList::where('document_id',$id)->get()->count();
				// if($assigned_document_count == 1 && $total_count == 1){
					$getdata = Document::where('id',$id)->first();
					$file_size = $getdata->filesize;
					$getdata->delete();
					// To reduce from maximum upload size
					$affectedRows = User::where('id', Auth::user()->id)->decrement('maximum_document_uploadsize', str_replace(',', '', $file_size));
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.document_delete_msg"),'data'=>''));
				// }else{
				// 	return Response::json(array('status' => 'error', 'message' => "Already assigned documents cannot be deleted.", 'data' => ''));
				// }
		} 	
	}
	/*** End delete document process ***/
	
	public function document_common_delete(){
		$request = Request::all();		
		if(isset($request['document_ids']) && !empty($request['document_ids'])){
			$document_ids = $request['document_ids'];
			$document_id_data = array_map(function($document_ids) {  return Helpers::getEncodeAndDecodeOfId($document_ids, 'decode');  }, $document_ids);
			$doc_count = 0;
			foreach($document_id_data as $document_id) {
				if(Document::where('id', $document_id)->count()>0) 
				{
					$user_type_check = Auth::user()->practice_user_type;	
					if ($user_type_check == 'practice_admin') {
						$assigned_document = DocumentFollowupList::where('document_id',$document_id)->first();
						$assigned_document->delete();
						// $assigned_user_id = $assigned_document['assigned_user_id'];
						// $created_by = $assigned_document['created_by'];
						// $assigned_document_count = DocumentFollowupList::where('document_id',$document_id)->where('assigned_user_id',$assigned_user_id)->where('created_by',$created_by)->where('Assigned_status','Active')->where('status','Assigned')->get()->count();
						// $total_count = DocumentFollowupList::where('document_id',$document_id)->get()->count();
						// if($assigned_document_count == 1 && $total_count == 1){
							$getdata = Document::where('id',$document_id)->first();
							$file_size = $getdata->filesize;
							$getdata->delete();
							$affectedRows = User::where('id', Auth::user()->id)->decrement('maximum_document_uploadsize', str_replace(',', '', $file_size));
						// } else{
						// 	$doc_count = $doc_count+1;						
						// }							
					}
					else {
						$assigned_document = DocumentFollowupList::where('document_id',$document_id)->first();
						$assigned_document->delete();
						// $assigned_document_count = DocumentFollowupList::where('document_id',$document_id)->where('assigned_user_id',Auth::user()->id)->where('created_by',Auth::user()->id)->where('Assigned_status','Active')->where('status','Assigned')->get()->count();
						// $total_count = DocumentFollowupList::where('document_id',$document_id)->get()->count();
						// if($assigned_document_count == 1 && $total_count == 1){
							$getdata = Document::where('id',$document_id)->first();
							$file_size = $getdata->filesize;
							$getdata->delete();
							$affectedRows = User::where('id', Auth::user()->id)->decrement('maximum_document_uploadsize', str_replace(',', '', $file_size));
						// } else{
						// 	$doc_count = $doc_count+1;						
						// }						
												
					}

				}	 else{
					return Response::json(array('status' => 'error', 'message' => "Already assigned documents cannot be deleted.", 'data' => ''));
				}
			}			
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.document_delete_msg"),'data'=>'', 'doc_count' => $doc_count));
			
		}else{
			if(Document::where('id', $request['doc_id'])->count()>0 && is_numeric($request['doc_id'])) 
			{
				$user_type_check = Auth::user()->practice_user_type;
				if($user_type_check == 'practice_admin') {
					$assigned_document = DocumentFollowupList::where('document_id',$request['doc_id'])->first();
					$assigned_document->delete();
					// $assigned_user_id = $assigned_document['assigned_user_id'];
					// $created_by = $assigned_document['created_by'];
					// $assigned_document_count = DocumentFollowupList::where('document_id',$request['doc_id'])
					// ->where('assigned_user_id',$assigned_user_id)->where('created_by',$created_by)->where('Assigned_status','Active')
					// ->where('status','Assigned')->get()->count();
					// $total_count = DocumentFollowupList::where('document_id',$request['doc_id'])->get()->count();
					// if($assigned_document_count == 1 && $total_count == 1){
						$getdata = Document::where('id',$request['doc_id'])->first();
						$file_size = $getdata->filesize;
						$getdata->delete();
						// To reduce from maximum upload size
						$affectedRows = User::where('id', Auth::user()->id)->decrement('maximum_document_uploadsize', str_replace(',', '', $file_size));
						return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.document_delete_msg"),'data'=>''));
					// }else{
					// 	return Response::json(array('status' => 'error', 'message' => "Already assigned documents cannot be deleted.", 'data' => ''));
					// }
				}
				else {
					$assigned_document = DocumentFollowupList::where('document_id',$request['doc_id'])->first();
					$assigned_document->delete();
					// $assigned_document_count = DocumentFollowupList::where('document_id',$request['doc_id'])
					// ->where('assigned_user_id',Auth::user()->id)->where('created_by',Auth::user()->id)->where('Assigned_status','Active')
					// ->where('status','Assigned')->get()->count();				
					// $total_count = DocumentFollowupList::where('document_id',$request['doc_id'])->get()->count();
					// if($assigned_document_count == 1 && $total_count == 1){
						$getdata = Document::where('id',$request['doc_id'])->first();
						$file_size = $getdata->filesize;	
						$getdata->delete();
						// To reduce from maximum upload size
						$affectedRows = User::where('id', Auth::user()->id)->decrement('maximum_document_uploadsize', str_replace(',', '', $file_size));
						return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.document_delete_msg"),'data'=>''));
					// }else{
					// 	return Response::json(array('status' => 'error', 'message' => "Already assigned documents cannot be deleted.", 'data' => ''));
					// }					
				}			

			}
			else 
			{
				return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
			}
		}

	}
	
	public function getdocumentdownloadApi($decode_id,$type,$filename)
	{
		$id = Helpers::getEncodeAndDecodeOfId($decode_id,'decode');
				$doc_details = Document::whereRaw('filename = ? and document_type = ? and (type_id = 0 or type_id = ? or main_type_id = ?)', array($filename,$type,$id,$id))->first();

		if(!empty($doc_details['filename']))
		{

			$picture = json_decode(json_encode($doc_details), true);
			$file = Helpers::amazon_server_get_file($picture['document_path'],$picture['filename']);
			$headers = [
							'Content-Type' => 'text/csv', 
							'Content-Description' => 'File Transfer',
							'Content-Disposition' => "attachment; filename={$picture['filename']}",
							'filename'=> $picture['filename']
						];

			return response($file, 200, $headers);
		}
		
	}
	
	public function document_common_title(){
		$request = Request::all();
		$type_id = Helpers::getEncodeAndDecodeOfId($request['type_id'],'decode');;
		$category = $request['category'];
		$title = $request['title'];
		if($type_id != '' && $category != '' && $title != ''){
			if($request['type'] == 'patient'){
				$document_count = Document::where('type_id', $type_id)->where('title',trim($title))->where('category',$category)->get()->count();
				if($document_count == 0)
					return 'true';
				else
					return 'false';
			}
		}
	}
	

	function __destruct() 
	{
    }

}