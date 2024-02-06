<?php namespace App\Http\Controllers\Scheduler;

use View;
use Request;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Scheduler\Api\ListingApiController as ListingApiController;


class ListingController extends ListingApiController 
{
    public function __construct()
    {
		//Icon, selected tab
        View::share ( 'heading', 'Scheduler' );
        View::share ( 'selected_tab', 'Reports' );
        View::share( 'heading_icon', 'fa-calendar-o');
    }
	
	/**** Listing page starts ***/ 
    public function index($pro_id="",$fac_id="",$date="",$pat_id="",$request='')
    {	
		$api_response 		= 	$this->getIndexApi($pro_id,$fac_id,$date,$pat_id,$request);
        $api_response_data 	= 	$api_response->getData();
		$app_list  	= 	$api_response_data->data->app_list;
		$facility  	= 	$api_response_data->data->facility_list;
		$facility 	 = array_flip(json_decode(json_encode($facility), True));  
		$facility	 = array_flip(array_map(array($this,'myfunction'),$facility));
		$provider  	= 	$api_response_data->data->provider_list;
		$provider 	 = array_flip(json_decode(json_encode($provider), True));  
		$provider	 = array_flip(array_map(array($this,'myfunction'),$provider));
		$patients  	= 	$api_response_data->data->patients_list;
		$patients 	 = array_flip(json_decode(json_encode($patients), True));  
		$patients	 = array_flip(array_map(array($this,'myfunction'),$patients));
		$view_type  = 	$api_response_data->data->view_type;
		if($view_type !='')
		{
			$page = ($view_type =="tableview") ? "listview":"gridview";
			return view ( 'scheduler/listing/'.$page, compact ('app_list','view_type') );
		}
		else
		{
			return view ( 'scheduler/listing/index', compact ('pro_id','fac_id','pat_id','app_list','facility','provider','patients') );
		}
    }
	/**** Listing page ends ***/

	function myfunction($num)
	{
	  return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}	
	/**** Export function page starts ***/ 
	public function getExport($export,$pro_id='',$fac_id='',$date='',$pat_id='',$status='')
    {	
		$pro_id 		= ($pro_id =="empty") ? "" : $pro_id;
		$fac_id 		= ($fac_id =="empty") ? "" : $fac_id;
		$date 			= ($date =="empty") ? "" : $date;
		$pat_id 		= ($pat_id =="empty") ? "" : $pat_id;
		$request['status']	= ($status =="empty") ? "":$status;
		$api_response 		= 	$this->getIndexApi($pro_id,$fac_id,$date,$pat_id,$export,$request);
    }
	/**** Export function page ends ***/ 
}