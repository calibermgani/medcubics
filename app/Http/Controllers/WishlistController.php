<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use Request;
use Redirect;
use Auth;
use View;
use Config;

class WishlistController extends Api\WishlistApiController
{
	public function __construct() 
	{ 
       View::share ( 'heading', 'Wishlists' );  
	   View::share ( 'selected_tab', 'Wishlists' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.admin.role'));
    }  

	/*** Start to List the API ***/
	public function index()
	{
		//
	}
	/*** End to List the API ***/
	
	/*** Start to Create the API List ***/
	public function create()
	{
		//
	}
	/*** End to Create the API List	 ***/

	/*** Start to store the API List ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
			return $api_response_data->message;
		else
			return $api_response_data->message;
	}
	/*** End to store the API List	 ***/

	/*** Start to show the API List ***/
	public function show($id)
	{
		//
	}
	/*** End to show the API List	 ***/
	
	/*** Start to edit the API List ***/
	public function edit($id)
	{
		//
	}
	/*** End to edit the API List	 ***/
	
	/*** Start to update the API List ***/
	public function update($id,Request $request)
	{
		//
	}
	/*** End to update the API List	 ***/
	
	/*** Start to delete the API List ***/
	public function destroy(Request $request)
	{
		$api_response = $this->getDeleteApi(Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
			return $api_response_data->message;
		else
			return $api_response_data->message;
	}
	/*** End to delete the API List	 ***/
}
