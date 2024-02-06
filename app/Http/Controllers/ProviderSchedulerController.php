<?php namespace App\Http\Controllers;
use Auth;
use Request;
use Redirect;
use View;
use Config;
use App\Http\Controllers\Api\ProviderSchedulerApiController as ProviderSchedulerApiController;
use PDF;
use Excel;
use App\Exports\BladeExport;

class ProviderSchedulerController extends ProviderSchedulerApiController 
{
	public function __construct()
	{
        View::share('heading', 'Practice');
        View::share('selected_tab', 'scheduler');
        View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
	}
	
	/********************** Start Display a listing of the provider scheduler ***********************************/
    public function index() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $providers = $api_response_data->data->providers;

        if (Request::ajax()) {
            return view('practice/scheduler/providertablelist', compact('providers'));
        } else {
            //$all_short_nam 		= $api_response_data->data->all_short_nam;
            //$all_prov_nam 		= $api_response_data->data->all_prov_nam;
            return view('practice/scheduler/index', compact('providers'));
        }
    }

    /********************** End Display a listing of the provider scheduler ***********************************/
        
    public function getProviderSchedulerExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $providers_scheduler = $api_response_data->data->providers;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Providers_Scheduler_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/scheduler/provider_scheduler_export_pdf', compact('providers_scheduler', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/scheduler/provider_scheduler_export';
            $data['providers_scheduler'] = $providers_scheduler;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            //return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/scheduler/provider_scheduler_export';
            $data['providers_scheduler'] = $providers_scheduler;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
    /********************** Start view provider scheduler list page ***********************************/
	public function viewProviderScheduler($provider_id)
    {
        $api_response 		= $this->viewProviderSchedulerApi($provider_id);
        $api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success') {
			$providerschedulers = $api_response_data->data->providerschedulers;
			$provider 			= $api_response_data->data->provider;
			return view('practice/scheduler/provider_scheduler',compact('providerschedulers','provider'));
		} else {
			 return Redirect::to('practiceproviderschedulerlist')->with('error', $api_response_data->message);
		}
    }
	/********************** End view provider scheduler list page ***********************************/
	
	public function providerScheduledListExport($provider_id = '', $export='') {
        $api_response = $this->viewProviderSchedulerApi($provider_id);
        $api_response_data = $api_response->getData();
        $providerschedulers = $api_response_data->data->providerschedulers;
        $provider = $api_response_data->data->provider;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Provider_Scheduled_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/scheduler/provider_scheduled_list_export_pdf', compact('provider', 'providerschedulers', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/scheduler/provider_scheduled_list_export';
            $data['provider'] = $provider;
            $data['providerschedulers'] = $providerschedulers;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            //return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/scheduler/provider_scheduled_list_export';
            $data['provider'] = $provider;
            $data['providerschedulers'] = $providerschedulers;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
	/********************** Start add provider scheduler process ***********************************/
    public function addProviderScheduler($provider_id,$scheduler_id='')
    {
        $api_response 		= $this->addProviderSchedulerApi($provider_id,$scheduler_id);
        $api_response_data 	= $api_response->getData();		
        $facilities 		= $api_response_data->data->facilities;
        $provider_id 		= $api_response_data->data->provider_id;
        $api 		= $api_response_data->data->api;
        $facility_arr 		= $api_response_data->data->facility_arr;
        return view('practice/scheduler/add_scheduler_facility',compact('facilities','provider_id','facility_arr','api'));
    }
	/********************** End add provider scheduler process ***********************************/
	
	/********************** Start view provider scheduler details individual ***********************************/
    public function viewProviderSchedulerDetailsById($provider_id, $id)
    {
        $api_response 							= $this->viewProviderSchedulerDetailsByIdApi($provider_id, $id);
        $api_response_data 						= $api_response->getData();

		if ($api_response_data->status == 'success') 
		{
			$provider_schedulers_dates_listing_arr 	= $api_response_data->data->provider_schedulers_dates_listing_arr;
			$providerschedulers 					= $api_response_data->data->providerschedulers;
			$provider 								= $api_response_data->data->provider;
			return view ( 'practice/scheduler/provider_scheduler_details', compact ('provider_schedulers_dates_listing_arr','providerschedulers','provider') );
		} 
		else 
		{
            return Redirect::to('practiceproviderschedulerlist')->with('error', $api_response_data->message);
        }
    }
	/********************** End view provider scheduler details individual ***********************************/
	public function destroy($provider_id, $id)
	{
		$api_response 		= 	$this->getDeleteApi($provider_id, $id);
		$api_response_data 	= 	$api_response->getData();
		//dd($api_response_data->status);
		if($api_response_data->status == 'success')
		{
			return Redirect::to('practicescheduler/provider/'.$provider_id)->with('success', $api_response_data->message );
		}
		else
		{
			return Redirect::to('practicescheduler/provider/'.$provider_id)->with('error', $api_response_data->message);
		}
	}
}