<?php namespace App\Http\Controllers;

use View;
use Config;
use PDF;
use Excel;
use Request;
use Session;
use Response;
use Url;
use Auth;
use Redirect;
use Illuminate\Http\Response as Responses;
use App\Models\MultiFeeschedule as MultiFeeschedule;
use App\Http\Helpers\Helpers as Helpers;
use App\Exports\BladeExport;

class FeescheduleController extends Api\FeescheduleApiController 
{
	public function __construct() 
	{      
        View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'feeschedule' );
		View::share( 'heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
    }  

	/*** List page start ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$feeschedules = $api_response_data->data->feeschedules;
		return view('practice/feeschedule/feeschedule',  compact('feeschedules'));
	}
	/*** List page end***/
	
	/*** Feeschedule page create start ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$feeschedules = $api_response_data->data->feeschedules;
		$fav_count = $api_response_data->data->fav_count;
		return view('practice/feeschedule/create',  compact('feeschedules','fav_count'));
	}
	/*** Feeschedule page create end ***/

	/*** Feeschedule page form submission start ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi();
		$api_response_data = $api_response->getData();
		if(Request::ajax()) {
			if($api_response_data->status == 'success')
			{
			$msg = 'Successfully Imported';
			$url = 'feeschedule';
			$api_response_data = $api_response_data->status;
			return $api_response_data;
			// return Redirect::to('feeschedule')->with('success', $msg);
			// return Response::json(array('status'=>'success', 'message'=>$msg, 'data' => compact('url')));		
			}
			else
			{
				if ($api_response_data->status == 'error') {
					//$api_response_data = "uploadError";
					return $api_response_data->message;
				}
				else {
					$inputs = Request::all();
					$errors = $api_response_data->message;
					$url = "feeschedule/create";
					return Response::json(array('status'=>'error', 'message'=> $errors, 'data' => compact('inputs')));		
					// return Redirect::to('feeschedule/create')->withInput()->withErrors($api_response_data->message);
				}
			} 
		}
		else {
			return Redirect::to('feeschedule/create')->withInput()->withErrors($api_response_data->message);
		}		
     
	}
	/*** Feeschedule page form submission end ***/

	/*** Get Feeschedule sample upload file start ***/
	public function get_feeschedule_file($type)
	{
		$api_response = $this->api_get_feeschedule_file($type);
		if($type == 'sample' || $type == 'cptcode'){
		$api_response_data = $api_response->getData();
		$feeschedules = $api_response_data->data->collect_array;
		$heading = $api_response_data->data->columnheading;
		$date = date('m-d-Y');
        $name = 'Favorite_CPT_HCPCS_' . $date;
        $data['feeschedules'] = $feeschedules;
        $data['heading'] = $heading;
        $data['date'] = $date;
		$filePath = 'practice/feeschedule/fav_cpt';
		ob_end_clean();
		return Excel::download(new BladeExport($data,$filePath), $name.'.xlsx');
		}else{
			$file_path = $this->api_get_feeschedule_file($type);
			return (new Responses($file_path,200))->header('Content-Type','application/vnd.ms-excel');
		}
	}
	/*** Get Feeschedule sample upload file End ***/
	
	/********************** Start feeschedule deleted process ***********************************/
	public function destroy($id)
	{
		$api_response 		= $this->getdestroyApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('feeschedule')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('feeschedule')->with('error', $api_response_data->message);
		}
	}
	/********************** End feeschedule deleted process ***********************************/
	public function statusChange($id,$status){
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		MultiFeeschedule::where('fee_schedule_id',$id)->update(['status'=>$status]);
		return Redirect::to('feeschedule')->with('success','Successfully '.$status);
	}
        
    public function getReport($export = "", $data = []) {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $feeschedules = $api_response_data->data->feeschedules;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Fee_Schedule_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : '';
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        if ($export == 'pdf') {
            $html = view('practice/feeschedule/feeschedule_export_pdf', compact('feeschedules', 'createdBy', 'practice_id', 'export'));
            $type = '.pdf';
            $path = storage_path('app/Report/exports/');
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/feeschedule/feeschedule_export';
            $data['feeschedules'] = $feeschedules;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['file_path'] = $filePath;
            return $data;
            //return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        } elseif ($export == 'csv') {
            $filePath = 'practice/feeschedule/feeschedule_export';
            $data['feeschedules'] = $feeschedules;
            $data['createdBy'] = $createdBy;
			$data['practice_id'] = $practice_id;
			ob_end_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
            $type = '.csv';
        }
    }

}
