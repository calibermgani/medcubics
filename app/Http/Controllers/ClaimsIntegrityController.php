<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Excel;
use App\Imports\WorkRvuImport;
use Input;
use User;
use DB;
use Config;
use App\Models\Cpt;
use App\Models\ProcedureCategory;
use App\Models\Insurance;
use App\Models\MultiFeeschedule as MultiFeeschedule;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use PDF;
use Validator;
use App\Exports\BladeExport;
use App\Http\Controllers\Documents\Api\DocumentApiController;

class ClaimsIntegrityController extends Api\ClaimsIntegrityApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'claimsintegrity' );
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/*** Cpt lists page Starts ***/
	public function claimsintegrity()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$mismatchedclaims = $api_response_data->data->mismatchedclaims;
		return view('practice/claimsintegrity/claimsintegrity',  compact('mismatchedclaims'));
	}

	/*** Show index file ***/

	public function index(Request $request){

		return view('admin.claimsintegrity.index');
	} 	

	public function getdynamicdocument(){

		$documentApi =	new DocumentApiController();
		$api_response 		= $documentApi->getDynamicDocumentApi();
		$api_response_data = $api_response->getData();
		$document_data		=	$api_response_data->data->document_data;
		$users		=	  $api_response_data->data->users;
		$categories		=	$api_response_data->data->categories; 
		$patients		=	$api_response_data->data->patients; 
		$insurances		=	$api_response_data->data->insurances;
		
		return view('admin/claimsintegrity/ajax_integrity', compact('document_data', 'users', 'categories', 'patients', 'insurances'));
	}

	public function getCategory($category)
	{        
        $api_response 		= 	$this->getCategoryApi($category);
        $api_response_data 	= 	$api_response->getData();
		$category_details	=	$api_response_data->data;
		$cat_list = json_encode($category_details);
		print_r($cat_list);exit;
    }	
}
