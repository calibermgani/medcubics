<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class ModifierLevelController extends Api\ModifierLevelApiController 
{
	public function __construct() 
	{      
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'modifiers' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }
	
	/*** lists page Starts ***/
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$modifiers 			= 	$api_response_data->data->modifiers;
		return view ( 'practice/modifier/modifierlevel2/modifier', compact ( 'modifiers') );
		
	}
	/*** lists page Ends ***/ 
    
    public function getModifierExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $modifiers = $api_response_data->data->modifiers;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Modifier_Level_II_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/modifier/modifierlevel2/modifier_export_pdf', compact('modifiers', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/modifier/modifierlevel2/modifier_export';
            $data['modifiers'] = $modifiers;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/modifier/modifierlevel2/modifier_export';
            $data['modifiers'] = $modifiers;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	/*** Create page Starts ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$modifierstype = $api_response_data->data->modifierstype;
		$modifiers_type_id = $api_response_data->data->modifiers_type_id;
		return view('practice/modifier/modifierlevel2/create',  compact('modifierstype','modifiers_type_id'));
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function store(Request $request)
	{
			 
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$id = $api_response_data->data->id;
			$type_id = $api_response_data->data->type_id;
			return Redirect::to('modifierlevel'.$type_id.'/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('modifierlevel2/create')->withInput()->withErrors($api_response_data->message);
		} 
	}
	/*** Store Function Ends ***/
	
	/*** Show page Starts ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')
		{
			$modifiers		 	= 	$api_response_data->data->modifiers;
			return view ( 'practice/modifier/modifierlevel2/show', ['modifiers' => $modifiers] );
		}
		else
		{
			return Redirect::to('modifierlevel2')->with('error', $api_response_data->message);
		}
	}
	/*** Show Function Ends ***/
	
	/*** Edit page Starts ***/
	public function edit($id)
	{		
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$modifiers = $api_response_data->data->modifiers;
			$modifierstype = $api_response_data->data->modifierstype;
			$modifiers_type_id = $api_response_data->data->modifiers_type_id;
			return view('practice/modifier/modifierlevel2/edit', compact('modifiers','modifierstype','modifiers_type_id'));
		}
		else
		{
			return Redirect::to('modifierlevel2')->with('error', $api_response_data->message);
		}
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function update($id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('modifierlevel2')->with('error', $api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			$type_id = $api_response_data->data;
			return Redirect::to('modifierlevel'.$type_id.'/'.$id)->with('success', $api_response_data->message);
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
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('modifierlevel2')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('modifierlevel2')->with('error', $api_response_data->message);
		}
	}
	/*** Delete Function Ends ***/
	
	/*** Search Function Starts ***/
	public function searchIndexlist()
	{
		$request  = Request::all();
		$api_response = $this->getIndexApi('',$request);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$modifiers 			= 	$api_response_data->data->modifiers;
			$type 			= 	'modifierlevel2';
			return view ( 'practice/modifier/modifier-list', compact ('modifiers','type') );
		}
		else
		{
			print_r($api_response_data->message);
			exit;
		}
	}
	/*** Search Function Ends ***/
}
