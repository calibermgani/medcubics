<?php namespace App\Http\Controllers;

use View;
use Redirect;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class QuestionnariesTemplateController extends Api\QuestionnariesTemplateApiController
 {
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );    
		View::share ( 'selected_tab', 'questionnaire/template' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/*** List Function Start ***/
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$questionnaries		= 	$api_response_data->data->questionnaries;
		return view ( 'practice/questionnaires/questionnaires_template', compact ('questionnaries'));
	}
	/*** List Function End ***/
	
    public function getQuestionnariesTemplateExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $questionnaries = $api_response_data->data->questionnaries;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Questionnaires_Template_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/questionnaires/questionnaires_template_export_pdf', compact('questionnaries', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/questionnaires/questionnaires_template_export';
            $data['questionnaries'] = $questionnaries;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/questionnaires/questionnaires_template_export';
            $data['questionnaries'] = $questionnaries;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
        
	/*** Create Function Start ***/
	public function create()
	{
		return view ( 'practice/questionnaires/create');
	}
	/*** Create Function End ***/
	
	/*** Store Function Start ***/
	public function store()
	{
		$api_response 		= 	$this->getStoreApi();
		$api_response_data 	= 	$api_response->getData();
		$data = json_encode($api_response_data);
		print_r($data);exit;
	}
	/*** Store Function End ***/
	
	/*** Show Function Start ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			$questionnaries		= 	$api_response_data->data->questionaries;
			return view ( 'practice/questionnaires/show', compact ('questionnaries','id'));
		}
		else
		{
			return redirect('/questionnaire/template')->with('error',$api_response_data->message);
		}	
	}
	/*** Show Function End ***/
	
	/*** Edit page Start ***/ 
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$questionnaries		= 	$api_response_data->data->questionaries;
			$title				= 	$api_response_data->data->questionaries_title;
			return view ( 'practice/questionnaires/edit', compact ('questionnaries','id','title'));
		}
		else
		{
			return redirect('/questionnaire/template')->with('error',$api_response_data->message);
		}
	}
	/*** Edit page End ***/
	
	/*** Update Function Start ***/
	public function update($id)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		$data = json_encode($api_response_data);
		print_r($data);exit;
	}
	/*** Update Function End ***/
	
	/*** Delete Function Start ***/
	public function destroy($id)
	{
		$api_response 		= 	$this->getDestroyApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('questionnaire/template')->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->with('error',$api_response_data->message);
		}
	}
	/*** Delete Function End ***/
	
	/*** Ajax Delete Function Start ***/
	public function quesansdelete()
	{
		$api_response 		= 	$this->getQuesansdeleteApi();
		$api_response_data 	= 	$api_response->getData();
		print_r($api_response_data->message);exit;
	}
	/*** Delete Function End ***/
}
