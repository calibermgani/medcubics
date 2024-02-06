<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use Config;
use Session;
use App\Models\Medcubics\Practice;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class CustomerController extends Api\CustomerApiController {

	public function __construct() {
		View::share('heading','Customers');  
		View::share('selected_tab','customer');
		View::share('heading_icon', Config::get('cssconfigs.admin.users'));
    }  

	/** Display a listing of the resource. */
	public function index() {
		$api_response 		= $this->getIndexApi();
		$api_response_data 	= $api_response->getData();
		$customers 			= $api_response_data->data->customers;
		$tabs 				= $api_response_data->data->tabs;
		return view('admin/customer/customerlist',  compact('customers','tabs'));
	}
	
	/** Show the form for creating a new resource */
	public function create() {
		$api_response 			= $this->getCreateApi();
		$api_response_data 		= $api_response->getData();
		$customers 				= $api_response_data->data->customers;
		$address_flags 			= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] = (array)$address_flags['general'];		
		return view('admin/customer/create',  compact('customers','address_flag'));
	}

	/* Store a newly created resource in storage */
	public function store(Request $request) {
		$api_response 		= $this->getStoreApi($request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success') {
			return Redirect::to('admin/customer/'.$api_response_data->data)->with('success', $api_response_data->message);
		} else {
			return Redirect::to('admin/customer/create')->withInput()->withErrors($api_response_data->message);
		}      
	}

	/* Display the specified resource */
	public function show($id) {
		$api_response 		= $this->getShowApi($id);
		$api_response_data 	= $api_response->getData();
        if($api_response_data->status=='error') {
			return redirect('/admin/customer')->with('message',$api_response_data->message);
		}
        $customers 					= $api_response_data->data->customers;
		$address_flags 				= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] 	= (array)$address_flags['general'];
		$tabs 						= $api_response_data->data->tabs;		
		if($api_response_data->status == 'success') {
			return view ( 'admin/customer/show', ['customer' => $customers,'address_flag'=>$address_flag,'tabs'=>$tabs] );
		} else {
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}

	/* Show the form for editing the specified resource */
	public function edit($id) {
		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
        if($api_response_data->status=='error') {
			return redirect('admin/customer')->with('message',$api_response_data->message);
		}
        $customers 					= $api_response_data->data->customers;
		$address_flags 				= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] 	= (array)$address_flags['general'];
		$tabs 						= "";
		return view('admin/customer/edit', compact('customers','address_flag','tabs'));
	}

	/* Update the specified resource in storage */
	public function update($id, Request $request) {
		$api_response 		= $this->getUpdateApi($id, $request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success') {
			return Redirect::to('admin/customer/'.$id)->with('success',$api_response_data->message);
		} else {
			return Redirect::to('admin/customer/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}       
	}

	/* Remove the specified resource from storage */
	public function destroy($id) {
		$api_response 		= $this->getDestroyApi($id);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('admin/customer')->with('success',$api_response_data->message);
	}
	
	public function setPractice($practice_id) {
		$api_response 		= $this->setPracticeApi($practice_id);
		$api_response_data 	= $api_response->getData();
		$timezone = Practice::where('id',base64_decode($practice_id))->value('timezone');
		$timezone = ($timezone !=NULL) ? $timezone : 'UTC';
		Session::put('timezone', $timezone);
		// Redirect to previous url, after login
		$prevUrl = Session::get('preLink');
		$prevUrl = rtrim($prevUrl,"/"); 
		//dd($prevUrl);
		Session::forget('preLink');

		$dbconnection = new DBConnectionController();
        $dbconnection->setAccessCache(Auth::user()->id);

		// If root URL  then redirect to dashboard
		$excUrl = [URL('/auth/logout'), URL('/auth/login'), URL('/'), 'https://pms.medcubics.com', 'https://avec.medcubics.com', URL('analytics/providers')];
		$prevUrl = in_array($prevUrl, $excUrl) ? ((Auth::user()->isProvider()) ? URL('analytics/providers') : URL('analytics/practice')) : $prevUrl;
		
		// Practice access check - Practice page not allowed for provider user type.
		if(stripos($prevUrl, '/practice/') && !$dbconnection->checkAllowToAccess('practice')) {
			$prevUrl = (Auth::user()->isProvider()) ? URL('analytics/providers') :  URL('analytics/practice');
		}

		if($prevUrl != '' && $prevUrl != URL('/') && trim($prevUrl) != 'https://medcubics.com/') {			 
			// Needs to handle edit of particular resource needs to redirect to listing page instead of that edit page to handle multi practices
			$prevUrl = (stripos($prevUrl, '/admin/') !== false) ? ((Auth::user()->isProvider()) ? URL('analytics/providers') : URL('analytics/practice')): ((Auth::user()->isProvider()) ? URL('analytics/providers') : $prevUrl);
			return Redirect::to($prevUrl);
		}
		$redirect = (Auth::user()->isProvider()) ? 'analytics/providers' : 'analytics/practice';
		return Redirect::to($redirect);
	}

    public function customerAvatar($id,$picture_name) {
		$api_response 		= $this->CustomerAvatarapi($id,$picture_name);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('admin/customer/'.$id.'/edit')->with($api_response_data->message);
	}
}