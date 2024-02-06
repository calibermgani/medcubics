<?php namespace App\Http\Controllers\Medcubics;


use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use PokitDok\Platform\PlatformClient;
use Request;
use Redirect;
use Auth;
use View;
use Config;

class PracticeUserController extends Api\PracticeUserApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 * 
	 */
	public function __construct() 
	{      
		View::share('heading','Customers');  
		View::share('selected_tab','customer');
		View::share('heading_icon', Config::get('cssconfigs.admin.users'));
	} 
	

	public function index($customer_id,$practice_id)
	{	
		$api_response = $this->getIndexApi($customer_id,$practice_id);
		$api_response_data = $api_response->getData();
		$adminusers = $api_response_data->data->adminusers;
		$practice = $api_response_data->data->practice;
		$customers = $api_response_data->data->customers;
		$customer_practices = $api_response_data->data->customer_practices;
		$customer_practices_list = $api_response_data->data->customer_practices_list;
		$facility = $api_response_data->data->facility;
		$provider = $api_response_data->data->provider;
		$adminrolls = $api_response_data->data->adminrolls;
        $practicerolls = $api_response_data->data->practicerolls;
        $language = $api_response_data->data->language;
        $language_id = $api_response_data->data->language_id;
        $ethnicity = $api_response_data->data->ethnicity;
        $ethnicity_id = $api_response_data->data->ethnicity_id;
        $address_flags = (array) $api_response_data->data->addressFlag;
		$address_flag['general'] = (array) $address_flags['general'];
		$cust_id = Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$prac_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$practice->id = Helpers::getEncodeAndDecodeOfId($practice->id,'encode');

		$selected_tab= 'users';
		return view('admin/customer/practiceusers/create', compact('customer_id', 'practice_id', 'practice', 'selected_tab', 'customers', 'customer_practices', 'customer_practices_list', 'facility','provider', 'adminrolls', 'language', 'language_id', 'ethnicity', 'ethnicity_id', 'address_flag',
					'practicerolls', 'cust_id', 'prac_id'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($customer_id, $practice_id)
	{
		// $api_response = $this->getCreateApi($customer_id, $practice_id);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{	
		$request = $request::all();
		$api_response = $this->getStoreApi($request);
		$api_response_data = $api_response->getData();
		$practice_user_id = $api_response_data->data->practice_user_id;
		$practice_id = $api_response_data->data->practice_id;
		$customer_id = $api_response_data->data->customer_id;
		$practice = $api_response_data->data->practice;
		$cust_id = Helpers::getEncodeAndDecodeOfId($customer_id,'encode');
		$prac_id = Helpers::getEncodeAndDecodeOfId($practice_id,'encode');
        if ($api_response_data->status == 'success') {
			// return view('admin/customer/practiceusers/show', compact('practice_id', 'customer_id', 'practice'))->with('success', $api_response_data->message);
			return Redirect::to('admin/customer/'.$cust_id.'/practice/'.$prac_id.'/practiceusers/show/'.$practice_user_id)
				   ->with('success', $api_response_data->message);
        } else {
			// return Redirect::to('admin/adminuser/create')->withInput()->withErrors($api_response_data->message);
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
        }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($customer_id, $practice_id, $practice_user_id)
	{	
		$api_response = $this->getShowApi($customer_id,$practice_id, $practice_user_id);
		$api_response_data = $api_response->getData();
		$practice = $api_response_data->data->practice;
		$practiceusers = $api_response_data->data->practiceusers;
		$practicelist = $api_response_data->data->practicelist;
		$customer_id = $api_response_data->data->customer_id;
		$practice_id = $api_response_data->data->practice_id;
		$practices = $api_response_data->data->practices;
		$address_flags 		= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] = (array)$address_flags['general'];
		// $tabs = $api_response_data->data->tabs;
		$selected_tab= 'users';
		// $cust_id = Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		// $prac_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$practice_user_id = $api_response_data->data->pra_user_id;
		$practice_user_id = Helpers::getEncodeAndDecodeOfId($practice_user_id,'encode');
		return view('admin/customer/practiceusers/show', compact('cust_id', 'prac_id','practice_id', 'customer_id', 'practice', 'practiceusers', 'address_flag', 'selected_tab','practice_user_id', 'practicelist','practices'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($customer_id, $practice_id, $id)
	{
		$api_response = $this->getEditApi($customer_id, $practice_id, $id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'error') {
            return redirect('admin/adminuser/')->with('message', $api_response_data->message);
        }
        $adminusers = $api_response_data->data->adminusers;
        $practicerolls = $api_response_data->data->practicerolls;
        $adminrolls = $api_response_data->data->adminrolls;
        $language = $api_response_data->data->language;
        $language_id = $api_response_data->data->language_id;
        $ethnicity = $api_response_data->data->ethnicity;
        $ethnicity_id = $api_response_data->data->ethnicity_id;
        $practices = $api_response_data->data->practices;
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['general'] = (array) $address_flags['general'];

        $customers = json_decode(json_encode($api_response_data->data->customers), true);
        $customer_practices = $api_response_data->data->customer_practices;
        $customer_practices_list = $api_response_data->data->customer_practices_list;
        $facility = $api_response_data->data->facility;
		$provider = $api_response_data->data->provider;
		$practice = $api_response_data->data->practice;
		
		$cust_id = Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$prac_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$selected_tab= 'users';
		$practice_user_id = $id;
        return view('admin/customer/practiceusers/edit', compact('practice_user_id','selected_tab','cust_id', 'prac_id','practice','id','customer_id', 'practice_id', 'adminusers', 'adminrolls', 'language', 'language_id', 'ethnicity', 'ethnicity_id', 'address_flag', 'customers', 'customer_practices', 'customer_practices_list', 'facility', 'provider','practicerolls','practices'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($customer_id, $practice_id, $id, Request $request)
	{
        $api_response = $this->getUpdateApi($customer_id, $practice_id, $id, $request::all());
		$api_response_data = $api_response->getData();
		$cust_id = Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$prac_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');		
        if ($api_response_data->status == 'success') {
			return Redirect::to('admin/customer/'.$customer_id.'/practice/'.$practice_id.'/practiceusers/show/'.$id)
					->with('success', $api_response_data->message);
        } else {
            return Redirect::to('admin/customer/'.$customer_id.'/practice/'.$practice_id.'/practiceusers/'.$practice_user_id.'/edit')->withInput()->withErrors($api_response_data->message);
        }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($customer_id, $practice_id, $id)
	{
		$api_response = $this->getDeleteApi($id);
        $api_response_data = $api_response->getData();
        return Redirect::to('admin/customer/'.$customer_id.'/practice/'.$practice_id.'/users')->with('success', $api_response_data->message);
	}

}
