<?php namespace App\Http\Controllers;
use View;
use Redirect;
use Request;
use App\Models\Template;
use Auth;
use Config;
use Lang;
use PDF;
use Excel;
use App\Exports\BladeExport;

class AppTemplatesController extends Api\TemplatesApiController 
{
	public function __construct() 
	{
		View::share( 'heading', 'Practice' );
		View::share( 'selected_tab', 'apptemplate' );
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
	}
	
	/*** Start to listing the Templates  ***/
	public function index()
	{
		$type = 'App';
		$export = '';
		$api_response 		= 	$this->getIndexApi($type,$export);
		$api_response_data 	= 	$api_response->getData();		
		$templates 			= 	$api_response_data->data->templates;
		return view ( 'practice/apptemplate/template', compact ('templates') );
	}
	/*** End to listing the Templates  ***/
	
    public function getTemplatesExport($type, $export=''){
        //$type = 'App';
        $api_response = $this->getIndexApi($type);
        $api_response_data = $api_response->getData();
        $templates = $api_response_data->data->templates;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Templates_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/apptemplate/template_export_pdf', compact('templates', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/apptemplate/template_export';
            $data['templates'] = $templates;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/apptemplate/template_export';
            $data['templates'] = $templates;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
        
	/*** Start to Show the Templates ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$templates			= 	$api_response_data->data->templates;
			if($templates->templatetype->templatetypes =="App")
				return view ( 'practice/apptemplate/show', ['templates' => $templates] );
			else
				return Redirect::to('apptemplate')->with('error', Lang::get("common.validation.empty_record_msg"));
		}
		else
		{
			return Redirect::to('apptemplate')->with('error', $api_response_data->message);
		}
	}
	/*** End to Show the Templates	 ***/
	
	/*** Start to Edit the Templates	 ***/
	public function edit($id)
	{
		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$templates 			= $api_response_data->data->templates;
			$templatestype 		= $api_response_data->data->templatestype;
			$template_type_id 	= $api_response_data->data->templates_type_id;
			$templatepairs 		= $api_response_data->data->templatepairs;
			if($templates->templatetype->templatetypes =="App")
				return view('practice/apptemplate/edit', compact('templates','templatestype','template_type_id','templatepairs'));
			else
				return Redirect::to('apptemplate')->with('error', Lang::get("common.validation.empty_record_msg"));
			
		}
		else
		{
			return Redirect::to('apptemplate')->with('error', $api_response_data->message);
		}
	}
	/*** End to Edit the Templates ***/
	
	/*** Start to Update the Templates ***/
	public function update($id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($id, $request);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('apptemplate')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('apptemplate/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Update the Templates ***/
}