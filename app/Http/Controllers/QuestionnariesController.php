<?php namespace App\Http\Controllers;

use View;
use Redirect;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class QuestionnariesController extends Api\QuestionnariesApiController
 {
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );    
		View::share ( 'selected_tab', 'questionnaires' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/*** List page Starts ***/
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$questionnaries		= 	$api_response_data->data->questionnaries;
		return view ( 'practice/questionnaires/set_questionnaires/questionnaires', compact ( 'questionnaries'));
	}
	/*** List page Ends ***/
	
    public function getQuestionnariesExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $questionnaries = $api_response_data->data->questionnaries;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Questionnaires_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/questionnaires/set_questionnaires/questionnaires_export_pdf', compact('questionnaries', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/questionnaires/set_questionnaires/questionnaires_export';
            $data['questionnaries'] = $questionnaries;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/questionnaires/set_questionnaires/questionnaires_export';
            $data['questionnaries'] = $questionnaries;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
        
	/*** Create page Starts ***/
	public function create()
	{
		$api_response 		= 	$this->getCreateApi();
		$api_response_data 	= 	$api_response->getData();
		$provider			= 	$api_response_data->data->provider_list;
		$facility			= 	$api_response_data->data->facility_list;
		$questionnaires		= 	$api_response_data->data->questionnaires_list;
		return view ( 'practice/questionnaires/set_questionnaires/create', compact ('provider','facility','questionnaires'));
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function store()
	{
		$api_response 		= 	$this->getStoreApi();
		$api_response_data 	= 	$api_response->getData();
		$id 				= 	$api_response_data->data;
		if($api_response_data->status == 'success')
		{
			return Redirect::to('questionnaires/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** Store Function Ends ***/
	
	/*** Show Function Starts ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			$questionnaries		= 	$api_response_data->data->questionaries;
			return view ( 'practice/questionnaires/set_questionnaires/show', compact ('questionnaries','id'));
		}
		else
		{
			return redirect('/questionnaires')->with('message',$api_response_data->message);
		}	
	}
	/*** Show Function Ends ***/
	
	/*** Edit page Starts ***/ 
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			$questionaries		= 	$api_response_data->data->questionaries;
			$provider			= 	$api_response_data->data->provider_list;
			$facility			= 	$api_response_data->data->facility_list;
			$questionnaires		= 	$api_response_data->data->questionnaires_list;
			return view ( 'practice/questionnaires/set_questionnaires/edit', compact ('questionaries','questionnaires','id','provider','facility'));
		}
		else
		{
			return redirect('/questionnaires')->with('message',$api_response_data->message);
		}
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function update($id)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') {
				 return Redirect::to('questionnaires')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('questionnaires/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function destroy($id)
	{
		$api_response 		= 	$this->getDestroyApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('questionnaires')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('questionnaires')->with('error', $api_response_data->message);
		}
	}
	/*** Delete Function Ends ***/
}
