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
use App\Http\Helpers\Helpers as Helpers;

class ProviderDocumentsController extends Api\DocumentApiController
{
	
	public $note_type = 'provider';
	
	public function __construct()
	{
		View::share('heading','Practice');
		View::share('selected_tab','provider');
		View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
	}
	
	public function index($providerid)
	{
		$api_response 		= 	$this->getIndexApi($this->note_type,$providerid);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
		   $provider 			= 	$api_response_data->data->type_details;
		   $pictures 			= 	$api_response_data->data->documents;
		   $type 			= 	$api_response_data->data->type;
		   return view ( 'practice/provider/document.index', compact ( 'pictures', 'provider','type') );
		}
		else
		{
		   return Redirect::to('provider')->with('error', $api_response_data->message);	
		}
		
	}
	
	public function create($providerid)
	{
		$api_response 		= 	$this->getCreateApi($this->note_type,$providerid);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
		   $provider 			= 	$api_response_data->data->type_details;
		   $cate_type_list_arr	= 	$api_response_data->data->cate_type_list_arr;
		   $sub_category_type_count = 	$api_response_data->data->sub_category_type_count;
		   $sub_category_type_list 	= 	$api_response_data->data->sub_category_type_list;
		   $priority 	= 	$api_response_data->data->priority;
		   $user_list 	= 	$api_response_data->data->user_list;
		   return view ( 'practice/provider/document/create', compact ( 'provider','cate_type_list_arr','sub_category_type_count','sub_category_type_list','priority','user_list') );
		}
		else
		{
		   return Redirect::to('provider')->with('error', $api_response_data->message);	
		}
		
	}
	
	public function store($providerid)
	{
		$api_response 		= 	$this->getAddDocumentApi($this->note_type,$providerid);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') {
			return Redirect::to('provider')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$providerid.'/providerdocuments')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($api_response_data->message);
		}        
	}
	
	public function get($providerid,$filename)
	{
		$api_response 		= 	$this->getGetApi($filename,$this->note_type,$providerid);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$picture 			= 	$api_response_data->data->picture;
			$file = Helpers::amazon_server_get_file($picture->document_path,$picture->filename);
			return (new Response ( $file, 200 ))->header ( 'Content-Type', $picture->mime );
		}
		else
		{
			return Redirect::to('provider/'.$providerid.'/providerdocuments');		
		}
	}
	
	public function destroy($providerid,$id)
	{
		$api_response 		= 	$this->getDestroyApi($this->note_type,$id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/' . $providerid . '/providerdocuments')->with ( 'success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('provider')->with('error', $api_response_data->message);	
		}
	}
	
}
