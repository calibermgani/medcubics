<?php
namespace App\Http\Controllers;
use Auth;
use View;
use Input;
use Session;
use Request;
use Config;
use Redirect;
use App\Http\Controllers\Api\ProviderManagedcareApiController as ProviderManagedCareApiController;
use PDF;
use Excel;
use App\Exports\BladeExport;

class ProviderManagecareController extends ProviderManagedCareApiController
{
	public function __construct()
	{
		View::share('heading','Practice');
		View::share('selected_tab','provider');
		View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
	}	
	
	public function index($providerid)
	{
		$api_response 		=	$this->getIndexApi($providerid);
		$api_response_data 	=	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			$provider 			=	$api_response_data->data->provider;
			$managecare			=	$api_response_data->data->managecare;
			return view ( 'practice/provider/managecare/managecare', compact ( 'provider', 'managecare') );
		}
		else
		{
			return Redirect::to('provider')->with('error', $api_response_data->message);
		}
	}
	
	public function providerManagedCareExport($providerid = '', $export='') {
        $api_response = $this->getIndexApi($providerid);
        $api_response_data = $api_response->getData();
        $provider = $api_response_data->data->provider;
        $managecare = $api_response_data->data->managecare;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Provider_Managed_Care_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/provider/managecare/managecare_export_pdf', compact('provider', 'managecare', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/provider/managecare/managecare_export';
            $data['provider'] = $provider;
            $data['managecare'] = $managecare;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/provider/managecare/managecare_export';
            $data['provider'] = $provider;
            $data['managecare'] = $managecare;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
	public function create($providerid)
	{
		$api_response 		= 	$this->getCreateApi($providerid);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$provider 		= 	$api_response_data->data->provider;
			$insurances 		= 	$api_response_data->data->insurances;		
			$insurance_id 		=	'';
			$providers_id 		=	'';
			return view ( 'practice/provider/managecare/create_managecare', compact ( 'insurances', 'provider', 'insurance_id', 'providers_id' ) );
		}
		else
		{
			return Redirect::to('provider')->with('error', $api_response_data->message);
		}
		
	}
	
	public function store($providerid, Request $request)
	{		
		$api_response 		= 	$this->getStoreApi($providerid);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') {
			return Redirect::to('provider')->with('error', $api_response_data->message);
		}
		
		$providerid 		= Input::get ( 'providers_id' );
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$providerid.'/providermanagecare/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	
	public function show($ids,$id)
	{
		$api_response 		= 	$this->getShowApi($ids,$id);
		$api_response_data 	= 	$api_response->getData();
       
        if($api_response_data->status=='failure')
		{
			return redirect('/provider')->with('message',$api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			$managedcare		= 	$api_response_data->data->managedcare;
			$provider		 	= 	$api_response_data->data->provider;
			return view ( 'practice/provider/managecare/show',compact('managedcare','provider'));
		}
		else
		{
			return redirect('/provider/'.$ids.'/providermanagecare')->with('message',$api_response_data->message);
		}
	}
	
	public function edit($providerid, $id)
	{
		$api_response 		= 	$this->getEditApi($providerid,$id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status=='failure')
		{
			return redirect('/provider')->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			$managecare 			= 	$api_response_data->data->managecare;
			$provider 				= 	$api_response_data->data->provider;
			$insurances 			= 	$api_response_data->data->insurances;
			$insurance_id 			= 	$api_response_data->data->insurance_id;
			return View ( 'practice/provider/managecare/edit_managecare', compact ('managecare', 'provider', 'insurances', 'insurance_id' ) );
		}
		else
		{
			return redirect('/provider/'.$providerid.'/providermanagecare')->with('message',$api_response_data->message);    
		}
	}
	
	public function update($providerid,$id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($providerid, $id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status=='failure')
		{
			return redirect('/provider')->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status=='failure_care')
		{
			return redirect('provider/'.$providerid.'/providermanagecare')->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$providerid.'/providermanagecare/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	
	public function destroy($providerid,$id)
	{
		$api_response 		= 	$this->getDeleteApi($providerid,$id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status=='failure')
		{
			return redirect('/provider')->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$providerid.'/providermanagecare')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('provider/'.$providerid.'/providermanagecare')->with('error', $api_response_data->message);
		}
	}
	
}