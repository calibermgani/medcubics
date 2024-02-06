<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class CodeController extends Api\CodeApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'code' );  
		View::share ( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  

	/*** Listing the code start ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$codes = $api_response_data->data->codes;	
		return view('practice/code/code',  compact('codes'));
	}
	/*** Listing the code end ***/
	
    public function getCodeExport($export=''){
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 300);
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $codes = $api_response_data->data->codes;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Remittance_Code_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/code/code_export_pdf', compact('codes', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/code/code_export';
            $data['codes'] = $codes;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/code/code_export';
            $data['codes'] = $codes;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
    
	/*** Create new the code start ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$codecategory = $api_response_data->data->codecategory;
		return view('practice/code/create',  compact('codecategory'));
	}
	/*** Create new the code end ***/
	
	/*** Store the code start ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$insertid = $api_response_data->data;
			return Redirect::to('code/'.$insertid)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('code/create')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Store the code end ***/
	
	/*** Edit the code start ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$code = $api_response_data->data->code;
			$codecategory = $api_response_data->data->codecategory;
			$codecategory_id = $api_response_data->data->codecategory_id;
			return view('practice/code/edit',  compact('code','codecategory','codecategory_id'));
		}
		else
		{
			return Redirect::to('code')->with('error',$api_response_data->message);
		}
		
	}
	/*** Edit the code end ***/
	
	/*** Update the code start ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') {
			return Redirect::to('code')->with('error', $api_response_data->message);
		} elseif($api_response_data->status == 'success')	{
			return Redirect::to('code/'.$id)->with('success',$api_response_data->message);
		} else {
			return Redirect::to('code/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Update the code end ***/

	/*** View the code start ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')
		{
			$code		 	= 	$api_response_data->data->code;
			return view ( 'practice/code/show', ['code' => $code] );
		}
		else
		{
			return Redirect::to('code')->with('error',$api_response_data->message);
		}
	}
	/*** View the code end ***/
	
	/*** Delete the code start ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('code')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('code')->with('error',$api_response_data->message);
		}	
	}
	/*** Delete the code end ***/
	
	/*** Search Function Starts ***/
	public function searchIndexlist()
	{
		$request  = Request::all();
		$api_response = $this->getIndexApi('',$request);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$codes 			= 	$api_response_data->data->codes;
			return view ( 'practice/code/codes-list', compact ('codes') );
		}
		else
		{
			print_r($api_response_data->message);
			exit;
		}
	}
	/*** Search Function Ends ***/

	/*** Code import from master starts ***/
	public function importMasterCodes(){
		return $this->massImportCode();
    }
	/*** Code import from master ends ***/
}
