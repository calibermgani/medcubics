<?php namespace App\Http\Controllers\Support;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Request;
use Redirect;

class FaqController extends Api\FaqApiController 
{
	public function __construct() { 
		View::share ( 'heading', 'FAQ' );  
		View::share ( 'selected_tab', 'faq' );
		View::share ( 'heading_icon', 'fa-question');
	} 

	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$faq_category_arr = $api_response_data->data->faq_category_arr;
		$search_keyword = $api_response_data->data->search_keyword;
		if(Request::ajax())
			return view('support/faq/faqlist', compact('faq_category_arr','search_keyword'));
		else
			return view('support/faq/faq', compact('faq_category_arr','search_keyword'));
	}	
}