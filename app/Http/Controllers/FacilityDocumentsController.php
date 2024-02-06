<?php namespace App\Http\Controllers;

use View;
use Auth;
use Input;
use Session;
use Request;
use Redirect;
use Config;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Helpers\Helpers as Helpers;

class FacilityDocumentsController extends Api\DocumentApiController
{
	public $note_type = 'facility';
	
	public function __construct() 
	{      
       View::share ( 'heading', 'Practice' );    
	   View::share ( 'selected_tab', 'facility' );
	   View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  

	/*** Start Display a listing of the document ***/
	public function index($facility_id)
	{
        $api_response 			= 	$this->getIndexApi($this->note_type,$facility_id);
		$api_response_data 		= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
		   $facility 			= 	$api_response_data->data->type_details;
		   $pictures 			= 	$api_response_data->data->documents;
		   $type 			= 	$api_response_data->data->type;
		   return view ( 'practice/facility/document/index', compact ( 'pictures', 'facility','type') );
		}
		else
		{
		   return Redirect::to('facility')->with('message', $api_response_data->message);
		}
	}
	/*** End Display a listing of the document ***/

	 /*** Start Display document create page  ***/
	public function create($facility_id)
	{	
      	$api_response 				= 	$this->getCreateApi($this->note_type,$facility_id);
		$api_response_data 			= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
		   $facility 				= 	$api_response_data->data->type_details;
		   $cate_type_list_arr		= 	$api_response_data->data->cate_type_list_arr;
		   $sub_category_type_count = 	$api_response_data->data->sub_category_type_count;
		   $sub_category_type_list 	= 	$api_response_data->data->sub_category_type_list;
		   $priority 	= 	$api_response_data->data->priority;
		   $user_list 	= 	$api_response_data->data->user_list;
		   return view ( 'practice/facility/document/create', compact ( 'facility','cate_type_list_arr','sub_category_type_count','sub_category_type_list','priority','user_list') );
		}
		else
		{
		   return Redirect::to('facility')->with('error', $api_response_data->message);
		}
	}
	/*** End Display document create page  ***/

	/***  Start document add process  ***/
	public function addDocument($facility_id)
	{
		$api_response 		= 	$this->getAddDocumentApi($this->note_type,$facility_id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('facility/' . $facility_id . '/facilitydocument')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** End document add process  ***/
	
	/***  Start get stored document  ***/
	public function get($facility_id,$filename)
	{
		$api_response 			= 	$this->getGetApi($filename,$this->note_type,$facility_id);
		$api_response_data 		= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$picture 			= 	$api_response_data->data->picture;
			$file = Helpers::amazon_server_get_file($picture->document_path,$picture->filename);
			return (new Response ( $file, 200 ))->header ( 'Content-Type', $picture->mime );
		}
		else
		{
		  return Redirect::to('facility/'.$facility_id.'/facilitydocument');		
		}
	}
	/***  End get stored document  ***/
	
	/***  Start delete document process  ***/
	public function destroy($facility_id, $id)
	{
		$api_response 		= 	$this->getDestroyApi($this->note_type,$id,$facility_id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('facility')->with('error', $api_response_data->message);
		}
		
		return Redirect::to('facility/' . $facility_id . '/facilitydocument')->with('success',$api_response_data->message);
	}
	/***  End delete document process  ***/
}
