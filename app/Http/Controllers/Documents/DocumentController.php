<?php namespace App\Http\Controllers\Documents;
use App;
use View;
use Redirect;
use Request;
use Session;
use App\Models\Document as Document;
use App\Models\Document_categories as Document_categories;

class DocumentController extends Api\DocumentApiController {

    public function __construct() 
	{
        View::share('heading', 'Documents');
        View::share('selected_tab', 'documents');
        View::share('heading_icon', 'folders');
    }
	
    /*** Documents lists page starts ***/
	public function index()
	{    
		$request = Request::all();     
		// Added session for create appopintment from patient
        if(isset($request['doctype']) ){ 
            session::put('doctype', $request['doctype']);
            return Redirect::to('documents');
        }   
        $api_response = $this->getIndexApi();
        $api_response_data 	=	$api_response->getData();
		$documents_list		=	$api_response_data->data->documents_list;
		$module_name		=	$api_response_data->data->module;
		$document_data		=	$api_response_data->data->document_data;
		$total_document_count		=	$api_response_data->data->total_document_count;
		$assigned_document_count	=	$api_response_data->data->assigned_document_count;
		$inprocess_document_count	=	$api_response_data->data->inprocess_document_count;
		$pending_document_count		=	$api_response_data->data->pending_document_count;
		$review_document_count		=	$api_response_data->data->review_document_count;
		$completed_document_count	=	$api_response_data->data->completed_document_count;
		$users		=	  $api_response_data->data->users;
		$categories		=	$api_response_data->data->categories; 
		$patients		=	$api_response_data->data->patients; 
		$insurances		=	$api_response_data->data->insurances;
		$doctype =  session::get('doctype',0);
        session::forget('doctype');
       // $patient = ($doctype != "0" ) ? ($doctype): "";
        $doctype = ($doctype != "0" ) ? $doctype : "";
        return view('documents/documents/document', compact('documents_list','module_name','document_data','total_document_count','assigned_document_count','inprocess_document_count','pending_document_count','review_document_count','completed_document_count','users', 'categories', 'patients', 'insurances','doctype'));
    }	
	/*** Documents lists page end ***/
	
	/*** Documents create page starts ***/
	public function create()
	{        
        $api_response 		= 	$this->getCreateApi();
        $api_response_data 	= 	$api_response->getData();
		$practice_id		=	$api_response_data->data->practice;
		$claim_number		=	$api_response_data->data->claim_number;
		$priority		=	$api_response_data->data->priority;
		$user_list		=	$api_response_data->data->user_list;
		return view('documents/documents/document_popup', compact('practice_id','claim_number','priority','user_list'));
    }
	/*** Documents create page end ***/
	
	/*** Documents store page starts ***/
	public function store(Request $request)
	{        
        $api_response 		= $this->getStoreApi($request::all());
        $api_response_data 	= $api_response->getData();
		$data = json_encode($api_response_data);
		print_r($data);exit;
    }
	/*** Documents store page end ***/
	public function setactivetab(Request $request)
	{        
        
    }
	/*** Documents search module based list starts ***/
	public function getList($module)
	{        
		$api_response 		= 	$this->getIndexApi($module);
        $api_response_data 	= 	$api_response->getData();
		$documents_list		=	$api_response_data->data->documents_list;
		$module_name		=	$api_response_data->data->module;
        return view('documents/documents/document_list', compact('documents_list','module_name','document_data'));
    }
	/*** Documents search module based list ends ***/	
	
	/*** Documents select category based list starts ***/
	public function getCategory($category)
	{        
        $api_response 		= 	$this->getCategoryApi($category);
        $api_response_data 	= 	$api_response->getData();
		$category_details	=	$api_response_data->data;
		$cat_list = json_encode($category_details);
		print_r($cat_list);exit;
    }	
	/*** Documents select category based list end ***/
	
	/*** Documents select category based list starts ***/
	public function getStatsDetail()
	{        
        return view('documents/documents/stats');
    }	
	/*** Documents select category based list end ***/
	
	/*** Documents delete function starts ***/
	public function destroy($id) 
	{        
        $api_response 		= $this->getDestroyApi($id);
        $api_response_data 	= $api_response->getData();
		$cat_list = json_encode($api_response_data);
		print_r($cat_list);exit;
    }	
	/*** Documents delete function end ***/
	
	
	
	/* 
		ModuleName 	: Main Document Dynamic Data Loading
		Author		: Selvakumar
		Created On	: 08-JAN-18
		Function	: This Function used to load the dynamic data
	*/
	
	public function getdynamicdocument(){
		$api_response 		= $this->getDynamicDocumentApi();
		$api_response_data = $api_response->getData();
		$document_data		=	$api_response_data->data->document_data;
		$users		=	  $api_response_data->data->users;
		//echo "<pre>";		
		$categories		=	$api_response_data->data->categories; 
		$patients		=	$api_response_data->data->patients; 
		$insurances		=	$api_response_data->data->insurances;
		return view('documents/documents/ajax_document_list', compact('document_data', 'users', 'categories', 'patients', 'insurances'));
		 
	}
	
	public function getdynamicfilterdocument(){
		$api_response 		= $this->getDynamicFilterDocumentApi();
		$api_response_data = $api_response->getData();
		$document_data		=	$api_response_data->data->document_data;
		$users		=	$api_response_data->data->users;
		$categories		=	$api_response_data->data->categories; 
		$patients		=	$api_response_data->data->patients; 
		$insurances		=	$api_response_data->data->insurances;
		return view('documents/documents/ajax_document_list', compact('document_data', 'users', 'categories', 'patients', 'insurances'));
		 
	}
	
	
	/* 
		ModuleName 	: Main Document Payment Posting Upload Document
		Author		: Selvakumar
		Created On	: 16-JUL-18
		Function	: This Function used store the document in payment posting
	*/

	public function paymentPostingUpload(){
		$api_response 		= $this->paymentPostingUploadApi();
		$api_response_data = $api_response->getData();
		$temp_type_id = $api_response_data->data->temp_type_id;
		return $temp_type_id;
	}
	
	public function getDocumentTitle(){
		$request = Request::all();
		if(isset($request['category_id'])){
			$categories_id = Document_categories::where("category_key",$request['category_id'])->value("id");
			if(Document::where('title',$request['title'])->where('document_categories_id',$categories_id)->count() > 0)
				return json_encode(array('valid' => "false"));
			else
				return json_encode(array('valid' => "true"));
		}
	}
	
	
}
