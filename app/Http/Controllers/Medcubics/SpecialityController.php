<?php namespace App\Http\Controllers\Medcubics;

use Request;
use View;
use Redirect;
use Config;
use App\Http\Controllers\Medcubics\Api\SpecialityApiController as SpecialityApiController;

class SpecialityController extends SpecialityApiController 
{
    public function __construct()
    {
        View::share('heading', 'Customers');
		View::share('selected_tab', 'admin/speciality');
		View::share('heading_icon', Config::get('cssconfigs.admin.users'));
    }
   
	/*** List the Speciality Start ***/
    public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();		
		$specialities		= 	$api_response_data->data->specialities;
		return view('admin/speciality/speciality',compact('specialities'));
	}
	/*** List the Speciality End ***/
	
	/*** Create Speciality Start ***/
	public function create()
	{
        $api_response 		= $this->getCreateApi();
        $api_response_data 	= $api_response->getData();
        $specialities 		= $api_response_data->data->specialities;
        return view('admin/speciality/create',compact('specialities'));
	}
	/*** Create Speciality End ***/
	
	/*** Store Speciality details Start ***/
	public function store()
	{
		$api_response 		= 	$this->getStoreApi();
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/speciality/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** Store Speciality details End ***/
	
	/*** Show Speciality details Start ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')
		{
			$speciality		= 	$api_response_data->data->speciality;
			return view('admin/speciality/show',['speciality' => $speciality]);
		}
		else
		{
			return redirect('admin/speciality')->with('message',$api_response_data->message);
		}
	}
	/*** Show Speciality details End ***/
	
	/*** Edit Speciality details Start ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$speciality		= 	$api_response_data->data->speciality;
			return view('admin/speciality/edit',compact('speciality'));
		}
		else
		{
			return redirect('admin/speciality')->with('message',$api_response_data->message);
		}
	}
	/*** Edit Speciality details End ***/
	
	/*** Update Speciality details Start ***/
	public function update($id)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/speciality/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** Update Speciality details Start ***/
	
	/*** Delete Speciality detail Start ***/
	public function destroy($id)
	{
		$api_response 		= 	$this->getDeleteApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
			return Redirect::to('admin/speciality')->with('success',$api_response_data->message);
		else
			return redirect()->back()->with('error',$api_response_data->message);
	}
	/*** Delete Speciality detail Start ***/
}
