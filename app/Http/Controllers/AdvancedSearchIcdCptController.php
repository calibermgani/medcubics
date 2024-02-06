<?php namespace App\Http\Controllers;

use Request;
use View;
use Config;

class AdvancedSearchIcdCptController extends Api\AdvancedSearchIcdCptApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'ICD' );  
		View::share ( 'selected_tab', 'imo' );
		View::share( 'heading_icon', Config::get('cssconfigs.common.icd'));
    } 
	
	/*** Search results starts here ***/
	public function AdvancedSearchIcdCpt(request $request)
	{
		$api_response = $this->getAdvancedSearchApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == "success")
		{
			$icd_cpt_result = $api_response_data->data->icd_cpt_result;
			$icd_cpt_column = $api_response_data->data->icd_cpt_column;
			$search_for = $api_response_data->data->search_for;
			return view('practice/search/search-result', compact('icd_cpt_result','icd_cpt_column','search_for'));
		}
		else
		{	
			$icd_cpt_result = "";
			$message =  $api_response_data->data;
			return view('practice/search/search-result', compact('icd_cpt_result','message'));
		}
	}
	/*** Search results ends here ***/
   


}
