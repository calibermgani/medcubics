<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use DB;
use Config;
use App\Models\Icd as Icd;
use PDF;
use Excel;
use App\Exports\BladeExport;

class IcdController extends Api\IcdApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'icd' );
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
	}  
	/*** Icd lists page Starts ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$icd_arr = $api_response_data->data->icd_arr;
		return view('practice/icd/icd',  compact('icd_arr'));
	}
	/*** Icd lists page Ends ***/
	
    public function getIcdExport($export=''){
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 300);
        $api_response = $this->getIcdListApi();
        $api_response_data = $api_response->getData();
        $icd_arr = $api_response_data->data->icd_arr;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'ICD_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/icd/icd_export_pdf', compact('icd_arr', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/icd/icd_export';
            $data['icd_arr'] = $icd_arr;
			$data['export'] = $export;
			$data['file_path'] = $filePath;
			return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/icd/icd_export';
            $data['icd_arr'] = $icd_arr;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
        
	/*** Icd create page Starts ***/
	public function create()
	{
		/*$api_response = $this->getCreateApi();
                dd($api_response);
		$api_response_data = $api_response->getData();
		$icd = $api_response_data->data->icd;*/
		return view('practice/icd/create');
	}
	/*** Icd create page Ends ***/
	
	/*** Icd form submission Starts ***/
	public function store(Request $request)
	{	  
        $api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('icd')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('icd/create')->withInput()->withErrors($api_response_data->message);
		}                    
	}
	/*** Icd form submission Ends ***/
	
	/*** Icd details show page Starts ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$icd = $api_response_data->data->icd;
			return view('practice/icd/show',  compact('icd','heading'));		
		}
		else
		{
			return Redirect::to('icd')->with('error','Invalid icd');
		}
	}
	/*** Icd form submission Ends ***/
	
	/*** Icd details edit page Starts ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$icd = $api_response_data->data->icd;
			return view('practice/icd/edit', compact('icd'));	
		}
		else
		{
			return Redirect::to('icd')->with('error','Invalid icd');
		}
	}
	/*** Icd details edit page Ends ***/
	
	/*** Icd details update Starts ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('icd')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('icd/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('icd/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Icd details update Ends ***/
	
	/*** Icd details delete Starts ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('icd')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('icd')->with('error', $api_response_data->message);
		}
	}
	/*** Icd details delete Ends ***/
	
	/*** Icd details search page Starts ***/
	public function searchIndex()
	{
        return view('practice/icd/search');
	}
	/*** Icd details search page Ends ***/
        
    /*** Search Icd 
     * 
     */
    public function searchIcd($str) {
        $database_name	= 'responsive';
       	//$count = Icd::on($database_name)->where('icd_code', $str)->count();
       	$count = Icd::where('icd_code', $str)->count();
        if($count == 0){
           echo "Icd Available";
        } else {
            echo "Diagnosis code already exists. Please enter a new code.";
        }
    }
    
    /*** Cpt import from master Starts ***/
	public function importMasterIcd(){
		return $this->massImportIcd();
    }
	/*** Cpt import from master Ends ***/    
        
}