<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use View;
use Input;

class CustomerUsersController extends Api\CustomerUsersApiController 
{

	public function __construct() 
	{ 
		View::share('heading','Customers');  
		View::share('selected_tab','admin/customerusers');  
		View::share('heading_icon','fa-users');
    }  
	
	/********************** Start Display a listing of the customer users ***********************************/
	public function index($id)
	{
		$api_response 		= $this->getIndexApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
		$customerusers 		= $api_response_data->data->customerusers;
		$customer 			= $api_response_data->data->customer;
		$language 			= $api_response_data->data->language;		
		$ethnicity 			= $api_response_data->data->ethnicity;
		$tabs 				= $api_response_data->data->tabs;	
		return view('admin/customer/customerusers/customerusers',  compact('customerusers','customer','language','ethnicity','tabs'));
	}
	/********************** End Display a listing of the customer users ***********************************/

	/********************** Start Display the customer user create page ***********************************/
	public function create($id)
	{
		$api_response 		= $this->getCreateApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
		$language 		= $api_response_data->data->language;
		$language_id 	= $api_response_data->data->language_id;
		$ethnicity 		= $api_response_data->data->ethnicity;
		$ethnicity_id 	= $api_response_data->data->ethnicity_id;
		$customer 		= $api_response_data->data->customer;
		$facility 	= $api_response_data->data->facility;
		$provider 	= $api_response_data->data->provider;
		
		$customer_practices = $api_response_data->data->customer_practices;
		$customer_practices_list = json_decode(json_encode($api_response_data->data->customer_practices_list), true);
		$tabs 			= $api_response_data->data->tabs;		
		$address_flags 	= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] = (array)$address_flags['general'];
		return view('admin/customer/customerusers/create',  compact('customer','language','language_id','ethnicity','ethnicity_id','address_flag','tabs','customer_practices','customer_practices_list','facility', 'provider'));
	}
	/********************** End Display the customer user create page ***********************************/

	/********************** Start customer user added process ***********************************/
	public function store($id, Request $request)
	{
		$api_response 		= $this->getStoreApi($id, $request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/customer/'.$id.'/customerusers/')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/customer/'.$id.'/customerusers/create')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/********************** End customer user added process ***********************************/

	/********************** Start display customer user details show page ***********************************/
	public function show($ids,$id)
	{
		$api_response 		= 	$this->getShowApi($ids,$id);
		$api_response_data 	= 	$api_response->getData();		
        if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
        $customerusers		= 	$api_response_data->data->customerusers;
		$customer 			= $api_response_data->data->customer;
		$practicelist 		= $api_response_data->data->practicelist;
		$user	 		= $api_response_data->data->user;
		$practices	 		= $api_response_data->data->practices;
		$address_flags 		= (array)$api_response_data->data->addressFlag;
		$customer_practices 		= (array)$api_response_data->data->customer_practices;
		$address_flag['general'] = (array)$address_flags['general'];
		$tabs 				= $api_response_data->data->tabs;
		if($api_response_data->status == 'success')
		{
			return view ( 'admin/customer/customerusers/show',compact('customerusers','user','practicelist','customer','address_flag','tabs','practices','customer_practices'));
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/********************** End display customer user details show page ***********************************/

	/********************** Start display customer user details edit page ***********************************/
	public function edit($ids,$id)
	{
		$api_response = $this->getEditApi($ids,$id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status=='error')
		{
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
        $customerusers 	= $api_response_data->data->customerusers;
		$customer 		= $api_response_data->data->customer;
		$tabs 			= $api_response_data->data->tabs;
		$language 		= $api_response_data->data->language;
		$user	 		= $api_response_data->data->user;
		$language_id 	= $api_response_data->data->language_id;
		$ethnicity 		= $api_response_data->data->ethnicity;
		$ethnicity_id 	= $api_response_data->data->ethnicity_id;
		$practices 	= $api_response_data->data->practices;
		@$facility 	= $api_response_data->data->facility;
		$provider 	= $api_response_data->data->provider;		
		$customer_practices = $api_response_data->data->customer_practices;
		$address_flags 	= (array)$api_response_data->data->addressFlag;
		$customer_practices_list = json_decode(json_encode($api_response_data->data->customer_practices_list), true);
		$address_flag['general'] = (array)$address_flags['general'];
		return view('admin/customer/customerusers/edit',  compact('customer','customerusers','language','language_id','ethnicity','user','ethnicity_id','address_flag','tabs','facility','customer_practices_list','customer_practices', 'provider','practices'));
	}
	/********************** End display customer user details edit page ***********************************/

	/********************** Start customer user details update process ***********************************/
	public function update($cust_id,$id,Request $request)
	{

		$api_response = $this->getUpdateApi($cust_id, $id,$request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success') {
			return Redirect::to('admin/customer/'.$cust_id.'/customerusers/'.$id)->with('success', $api_response_data->message);
		} else {
			return Redirect::to('admin/customer/'.$cust_id.'/customerusers/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/********************** End customer user details update process ***********************************/

	/********************** Start customer user deleted process ***********************************/
	public function destroy($cust_id,$id)
	{
		$api_response 		= $this->getDeleteApi($cust_id,$id);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('admin/customer/'.$cust_id.'/customerusers')->with('success',$api_response_data->message);
	}/********************** End customer user deleted process ***********************************/
	
}
