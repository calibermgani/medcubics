<?php namespace App\Http\Controllers;

use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use Session;
use View;
use Config;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use App\Http\Helpers\Helpers as Helpers;

class PracticeDocumentsController extends Api\DocumentApiController 
{
	public $note_type = 'practice';
	public function __construct() 
	{  
    	View::share('heading','Practice');
		View::share('selected_tab','practice');  
		View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
	}  

	/********************** Start Display a listing of the document ***********************************/
	public function index() 
	{
		$api_response 		= 	$this->getIndexApi($this->note_type);
		$api_response_data 	= 	$api_response->getData();
		$practice 			= 	$api_response_data->data->type_details;
		$pictures 			= 	$api_response_data->data->documents;
		return view('practice/practice/document/index',compact('pictures','practice' ) );
	}
	/********************** End Display a listing of the document ***********************************/

	/********************** Start Display document create page ***********************************/
	public function create() 
	{
		$api_response 		= 	$this->getCreateApi($this->note_type);
		$api_response_data 	= 	$api_response->getData();
		$practice 			= 	$api_response_data->data->type_details;
		$cate_type_list_arr	= 	$api_response_data->data->cate_type_list_arr;
		$sub_category_type_count 	= 	$api_response_data->data->sub_category_type_count;
		$sub_category_type_list 	= 	$api_response_data->data->sub_category_type_list;
		return view('practice/practice/document/create',  compact('practice','cate_type_list_arr','sub_category_type_count','sub_category_type_list'));
	}
	/********************** End Display document create page ***********************************/
	
	/********************** Start document add process ***********************************/
	public function addDocument() 
	{
		$api_response 		= 	$this->getAddDocumentApi($this->note_type);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('document')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($api_response_data->message);
		} 
	}
	/********************** End document add process ***********************************/
	
	/********************** Start get stored document ***********************************/
	public function get($filename)
	{
		$api_response 		= 	$this->getGetApi($filename,$this->note_type);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$picture	= 	$api_response_data->data->picture;
			$file 		= 	Helpers::amazon_server_get_file($picture->document_path,$picture->filename);
			return (new Response ( $file, 200 ))->header ( 'Content-Type', $picture->mime );
		}
		else
		{
		   return Redirect::to('document');		
		}
	}
	/********************** End get stored document ***********************************/
	
	/********************** Start delete document process ***********************************/
	public function destroy($id)
	{
		$api_response 		= $this->getDestroyApi($this->note_type,$id);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('document')->with('success',$api_response_data->message);
	}
	/********************** End delete document process ***********************************/

	/********************** Start document modal popup show process ***********************************/
	public function adddocumentmodel($type,$type_id,$category,$temp_doc_id="")
	{
		$api_response 			= $this->addDocumentmodelApi($type,$type_id,$category,$temp_doc_id);
		$api_response_data 		= $api_response->getData();		
		$documents_list 		= $api_response_data->data->documents_list;
		$document_list_count 	= $api_response_data->data->document_list_count; 
		$document_type			= $type;
		$document_type_id		= $type_id;
		$document_category		= $category;
		return view('practice/layouts/	ment_modal_popup',compact('documents_list','document_list_count','document_type','document_type_id','document_category'));
	}
	/********************** End document modal popup show process ***********************************/

}
