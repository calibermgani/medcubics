<?php

namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use View;
use Input;
use DB;
use Artisan;
use Session;
use App;
use Config;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Seeder;
use Nwidart\DbExporter\DbExportHandler as DbExportHandler;
use Nwidart\DbExporter\DbMigrations as DbMigrations;
use Nwidart\DbExporter\DbSeeding as DbSeeding;
use App\Models\Medcubics\Practice as Practices;
use App\Models\Practice as Practice;
use App\Http\Helpers\Helpers as Helpers;

class CustomerPracticesController extends Api\CustomerPracticesApiController {

    public function __construct() {
        View::share('heading', 'Customer');
        View::share('selected_tab', 'admin/customerpractices');
        View::share('heading_icon', Config::get('cssconfigs.admin.users'));
    }

    public function index($id) {
        $api_response = $this->getIndexApi($id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'error') {
            return redirect('/admin/customer')->with('message', $api_response_data->message);
        }
        $practices = $api_response_data->data->practices;
        $customer = $api_response_data->data->customer;
        $tabs = $api_response_data->data->tabs;
        $customer_id = $id;
        return view('admin/customer/customerpractices/index', compact('customer', 'practices', 'customer_id', 'tabs'));
    }

    public function create($id) {
        $api_response = $this->getCreateApi($id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'error') {
            return redirect('/admin/customer')->with('message', $api_response_data->message);
        }
        $customer = $api_response_data->data->customer;
        $specialities = $api_response_data->data->specialities;
        $speciality_id = $api_response_data->data->speciality_id;
        $languages = $api_response_data->data->language;
        $language_id = $api_response_data->data->language_id;
        $taxanomies = $api_response_data->data->taxanomies;
        $taxanomy_id = $api_response_data->data->taxanomy_id;
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['general'] = (array) $address_flags['general'];
        $address_flag['pta'] = (array) $address_flags['pta'];
        $address_flag['ma'] = (array) $address_flags['ma'];
        $address_flag['pa'] = (array) $address_flags['pa'];
        $npi_flag = (array) $api_response_data->data->npi_flag;
        $time = (array) $api_response_data->data->time;
        $apilist = $api_response_data->data->apilist;
        $api_name = json_decode(json_encode($api_response_data->data->api_name), True);
        $apilist_subcat = $api_response_data->data->apilist_subcat;
        $setapi = $api_response_data->data->setapi;

        return view('admin/customer/customerpractices/create', compact('customer', 'specialities', 'speciality_id', 'languages', 'language_id', 'taxanomies', 'taxanomy_id', 'address_flag', 'npi_flag', 'time', 'apilist', 'setapi', 'apilist_subcat', 'api_name'));
    }

    public function store($id, Request $request) {
        $practice_details = $request::all();
        $api_response = $this->getStoreApi($id, $request::all());
        $api_response_data = $api_response->getData();
        $practice_id = Helpers::getEncodeAndDecodeOfId($api_response_data->data, 'encode');
        if ($api_response_data->status == 'success') {
            return Redirect::to('admin/customer/' . $id . '/customerpractices/' . $practice_id)->with('success', $api_response_data->message);
        } else {            
            return Redirect::to('admin/customer/' . $id . '/customerpractices/create')->withInput()->with('error', $api_response_data->message);
        }
    }

    public function show($customer_id, $id) {
        $api_response = $this->getShowApi($customer_id, $id);
        $api_response_data = $api_response->getData();

        if ($api_response_data->status == 'error') {
            return redirect('/admin/customer')->with('message', $api_response_data->message);
        }

        $practice = $api_response_data->data->practice;
        $specialities = $api_response_data->data->specialities;
        $speciality_id = $api_response_data->data->speciality_id;
        $languages = $api_response_data->data->language;
        $language_id = $api_response_data->data->language_id;
        $taxanomies = $api_response_data->data->taxanomies;
        $taxanomy_id = $api_response_data->data->taxanomy_id;
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['pta'] = (array) $address_flags['pta'];
        $address_flag['ma'] = (array) $address_flags['ma'];
        $address_flag['pa'] = (array) $address_flags['pa'];
        $npi_flag = (array) $api_response_data->data->npi_flag;
        $customer = $api_response_data->data->customer;
        $apilist = $api_response_data->data->apilist;

        return view('admin/customer/customerpractices/show', compact('customer', 'customer_id', 'practice', 'specialities', 'speciality_id', 'languages', 'language_id', 'taxanomies', 'taxanomy_id', 'address_flag', 'npi_flag', 'apilist'));
    }

    public function edit($customer_id, $id) {
        $api_response = $this->getEditApi($customer_id, $id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'error') {
            return redirect('/admin/customer')->with('message', $api_response_data->message);
        }
        $practice = $api_response_data->data->practice;
        $specialities = $api_response_data->data->specialities;
        $speciality_id = $api_response_data->data->speciality_id;
        $languages = $api_response_data->data->language;
        $language_id = $api_response_data->data->language_id;
        $taxanomies = $api_response_data->data->taxanomies;
        $taxanomy_id = $api_response_data->data->taxanomy_id;
        $taxanomy_id = $api_response_data->data->taxanomy_id;
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['pta'] = (array) $address_flags['pta'];
        $address_flag['ma'] = (array) $address_flags['ma'];
        $address_flag['pa'] = (array) $address_flags['pa'];
        $npi_flag = (array) $api_response_data->data->npi_flag;
        $time = (array) $api_response_data->data->time;
        $apilist = $api_response_data->data->apilist;
        $api_name = json_decode(json_encode($api_response_data->data->api_name), True);
        $setapi = $api_response_data->data->setapi;
        $apilist_subcat = $api_response_data->data->apilist_subcat;

        return view('admin/customer/customerpractices/edit', compact('customer_id', 'practice', 'specialities', 'speciality_id', 'languages', 'language_id', 'taxanomies', 'taxanomy_id', 'address_flag', 'npi_flag', 'time', 'apilist', 'setapi', 'apilist_subcat', 'api_name'));
    }

    public function update($customer_id, $id, Request $request) {
        $api_response = $this->getUpdateApi($id, $request::all());
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            return Redirect::to('admin/customer/' . $customer_id . '/customerpractices/' . $id)->with('success', $api_response_data->message);
        } else {
            return Redirect::to('admin/customer/' . $customer_id . '/customerpractices/' . $id . '/edit')->withInput()->withErrors($api_response_data->message);
        }
    }

    public function avatarProvider($id, $avatar_name) {
        $api_response = $this->getDeleteApi($id, $avatar_name);
        $api_response_data = $api_response->getData();
        return Redirect::to('admin/customer/'. $id .'/customerpractices/'.$avatar_name.'/edit')->withInput()->withErrors($api_response_data->message);
    }

}
