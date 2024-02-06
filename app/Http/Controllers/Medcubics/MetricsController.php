<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use Request;
use Redirect;
use Auth;
use View;
use DB;
use Config;
use Route;

class MetricsController extends Api\MetricsApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	 public function __construct() 
	{ 
		View::share ( 'heading', 'Metrics' );  
		View::share ( 'selected_tab', 'admin/metrics' );
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  

	public function index()
	{	
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$search_fields = $api_response_data->data->search_fields;
		$heading = "Metrics";
		$selected_tab = "admin/metrics";
		return view('admin/metrics/index', compact('search_fields', 'heading', 'selected_tab'));
	}

	public function getcustomers() {
		$api_response = $this->getcustomersApi();
		$api_response_data = $api_response->getData();
		$cust = $api_response_data->data->customers;
		// $cust_count = (isset($cust))? count((array)$cust):0;
		// if($cust_count == 1) {
		// 	$customers = $cust;
		// }
		// else {
			$customers = $cust;
		// }
		$page = $api_response_data->data->page;
		$metrics = $api_response_data->data->metrics;
		return view('admin/metrics/'. $page, compact('customers', 'page', 'metrics'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
