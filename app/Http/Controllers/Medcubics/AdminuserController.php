<?php

namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use Config;

class AdminuserController extends Api\AdminuserApiController {

    public function __construct() {
        View::share('heading', 'Admin');
        View::share('selected_tab', 'admin/adminuser');
        View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
    }

    /*     * * Start to Listing the Admin User ** */

    public function index() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $adminusers = $api_response_data->data->adminusers;
        return view('admin/adminuser/adminuserlist', compact('adminusers'));
    }

    /*     * * End to Listing the Admin User 	 ** */

    /*     * * Start to Create the Admin User  ** */

    public function create() {

        $api_response = $this->getCreateApi();
        $api_response_data = $api_response->getData();
        $adminusers = $api_response_data->data->adminusers;
        $adminrolls = $api_response_data->data->adminrolls;
        $practicerolls = $api_response_data->data->practicerolls;
        $language = $api_response_data->data->language;
        $language_id = $api_response_data->data->language_id;
        $ethnicity = $api_response_data->data->ethnicity;
        $ethnicity_id = $api_response_data->data->ethnicity_id;
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['general'] = (array) $address_flags['general'];
        $customers = json_decode(json_encode($api_response_data->data->customers), true);
        $customer_practices = $api_response_data->data->customer_practices;
        $customer_practices_list = $api_response_data->data->customer_practices_list;
        $facility = $api_response_data->data->facility;
        $provider = $api_response_data->data->provider;
		$ip_group = $api_response_data->data->ip_group;
		$ip_user_group = $api_response_data->data->ip_user_group;
        return view('admin/adminuser/create', compact('adminusers', 'adminrolls', 'language', 'language_id', 'ethnicity', 'ethnicity_id', 'address_flag', 'customers', 'customer_practices', 'customer_practices_list', 'facility', 'provider','practicerolls','ip_group','ip_user_group'));
    }

    /*     * * End to Create the Admin User 	 ** */

    /*     * * Start to Store the Admin User 	 ** */

    public function store(Request $request) {
        $api_response = $this->getStoreApi($request::all());
        $api_response_data = $api_response->getData();
        $insertid = $api_response_data->data;
        if ($api_response_data->status == 'success') {
            return Redirect::to('admin/adminuser/' . $insertid)->with('success', $api_response_data->message);
        } else {
            return Redirect::to('admin/adminuser/create')->withInput()->withErrors($api_response_data->message);
        }
    }

    /*     * * End to Store the Admin User 	 ** */

    /*     * * Start to Show the Admin User 	 ** */

    public function show($id) {
        $api_response = $this->getShowApi($id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'error') {
            return redirect('admin/adminuser/')->with('message', $api_response_data->message);
        }
        $adminusers = $api_response_data->data->adminusers;
        $practicelist = $api_response_data->data->practicelist;
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['general'] = (array) $address_flags['general'];
        $facility = $api_response_data->data->facility;
        $provider = $api_response_data->data->provider;
        if ($api_response_data->status == 'success') {
            return view('admin/adminuser/show', compact('adminusers', 'address_flag', 'practicelist', 'facility', 'provider'));
        } else {
            return redirect()->back()->withInput()->withErrors($api_response_data->message);
        }
    }

    /*     * * End to Show the Admin User  ** */

    /*     * * Start to Edit the Admin User 	 ** */

    public function edit($id) {
        $api_response = $this->getEditApi($id);
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
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['general'] = (array) $address_flags['general'];

        $customers = json_decode(json_encode($api_response_data->data->customers), true);
        $customer_practices = $api_response_data->data->customer_practices;
        $customer_practices_list = $api_response_data->data->customer_practices_list;
        $facility = $api_response_data->data->facility;
        $provider = $api_response_data->data->provider;
		$ip_group = $api_response_data->data->ip_group;
		$ip_user_group = (array)$api_response_data->data->ip_user_group;
        return view('admin/adminuser/edit', compact('adminusers', 'adminrolls', 'language', 'language_id', 'ethnicity', 'ethnicity_id', 'address_flag', 'customers', 'customer_practices', 'customer_practices_list', 'facility', 'provider','practicerolls','ip_group','ip_user_group'));
    }

    /*     * * End to Edit the Admin User  ** */

    /*     * * Start to Update the Admin User  ** */

    public function update($id, Request $request) {
        $api_response = $this->getUpdateApi($id, $request::all());
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            return Redirect::to('admin/adminuser/' . $id)->with('success', $api_response_data->message);
        } else {
            return Redirect::to('admin/adminuser/' . $id . '/edit')->withInput()->withErrors($api_response_data->message);
        }
    }

    /*     * * End to Update the Admin User  ** */

    /*     * * Start to Destory the Admin User  ** */

    public function destroy($id) {
        $api_response = $this->getDeleteApi($id);
        $api_response_data = $api_response->getData();
        return Redirect::to('admin/adminuser/')->with('success', $api_response_data->message);
    }

    /*     * * End to Destory the Admin User 	 ** */
}
