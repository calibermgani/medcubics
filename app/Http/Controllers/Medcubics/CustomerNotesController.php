<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use View;
use Session;
use Config;

class CustomerNotesController extends Api\CustomerNotesApiController 
{
	public function __construct() 
	{      
		View::share('heading','Customers');  
		View::share('selected_tab','admin/customernotes');
		View::share('heading_icon', Config::get('cssconfigs.admin.users'));
    }  
	
	/********************** Start Display a listing of the customer notes ***********************************/
	public function index($id)
	{
		$api_response 		= $this->getIndexApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
		$notes 			= $api_response_data->data->notes;
		$customer 		= $api_response_data->data->customer;
		$tabs			= $api_response_data->data->tabs;
		return view('admin/customer/customernotes/notes',compact('notes','customer','tabs'));
	}
	/********************** End Display a listing of the customer notes ***********************************/

	/********************** Start Display the customer notes create page ***********************************/
	public function create($id)
	{
		$api_response 		= $this->getCreateApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
		$customer 		= $api_response_data->data->customer;
		$tabs 			= $api_response_data->data->tabs;
		return view('admin/customer/customernotes/create',compact('customer','tabs'));
	}
	/********************** End Display the customer notes create page ***********************************/
	
	/********************** Start customer notes added process ***********************************/
	public function store($id, Request $request)
	{
		$api_response 		= $this->getStoreApi($id, $request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/customer/'.$id.'/customernotes')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/customer/'.$id.'/customernotes/create')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/********************** End customer notes added process ***********************************/

	/********************** Start customer notes edit page display ***********************************/
	public function edit($ids,$id)
	{
 		$api_response 		= $this->getEditApi($ids,$id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
		$customer 	= $api_response_data->data->customer;
		$notes 		= $api_response_data->data->customernotes;
		$tabs 		= $api_response_data->data->tabs;
		return view('admin/customer/customernotes/edit',compact('notes','customer','tabs'));
	}
	/********************** End customer notes edit page display ***********************************/
	
	/********************** Start customer notes update process ***********************************/
	public function update($cust_id,$id, Request $request)
	{ 
		$api_response 		= $this->getUpdateApi($cust_id, $id, $request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/customer/'.$cust_id.'/customernotes')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/customer/'.$cust_id.'/customernotes/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/********************** End customer notes update process ***********************************/

	/********************** Start customer notes deleted process ***********************************/
	public function destroy($cust_id,$id)
	{
		$api_response 		= $this->getDeleteApi($cust_id,$id);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('admin/customer/'.$cust_id.'/customernotes')->with('success',$api_response_data->message);
	}
	/********************** End customer notes deleted process ***********************************/

}
