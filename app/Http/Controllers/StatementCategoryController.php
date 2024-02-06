<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\STMTHoldReason;

use Request;
use Redirect;
use Auth;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class StatementCategoryController extends Api\StatementCategoryApiController {

	public function __construct() 
    {      
        View::share('heading', 'Practice');   
        View::share('selected_tab', 'statementcategory');
        View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }

	/**
	 * Display a listing of statement hold reason
	 *
	 * @return Response
	 */
	public function index()
	{
		$api_response       = $this->getIndexApi();
        $api_response_data  = $api_response->getData();		
        //dd($api_response_data);
        $statementcategory     = $api_response_data->data->statementcategory;
        return view('practice/statementcategory/statementcategory',compact('statementcategory'));
	}
        
    public function getStatementCategoryExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $statementcategory = $api_response_data->data->statementcategory;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'StatementCategory_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/statementcategory/statementcategory_export_pdf', compact('statementcategory', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/statementcategory/statementcategory_export';
            $data['statementcategory'] = $statementcategory;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/statementcategory/statementcategory_export';
            $data['statementcategory'] = $statementcategory;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

	/**
	 * Show the form for creating a statement hold reason.
	 *
	 * @return Response
	 */
	public function create()
	{		
		$stmt_holdreason = STMTHoldReason::where('status', 'Active')->pluck('hold_reason','id')->all();
		return view('practice/statementcategory/create', compact('stmt_holdreason'));
	}

	/**
	 * Store a newly created statement hold reason.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$api_response           = $this->getStoreApi($request::all());
        $api_response_data      = $api_response->getData();
        if($api_response_data->status == 'success') {
            return Redirect::to('statementcategory/'.$api_response_data->data)->with('success', $api_response_data->message);
        } else {
            return Redirect::to('statementcategory/create')->withInput()->withErrors($api_response_data->message);
        }
	}

	/**
	 * Display the specified statement hold reason.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success') {
			$statementcategory = $api_response_data->data->statementcategory;
			return view ( 'practice/statementcategory/show',compact('statementcategory'));
		} else {
			return Redirect::to('statementcategory')->with('error', $api_response_data->message);
		}
	}

	/**
	 * Show the form for editing the statement hold reason.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
			$statementcategory = $api_response_data->data->statementcategory;
			$stmt_holdreason = STMTHoldReason::where('status', 'Active')->pluck('hold_reason','id')->all();
			return view('practice/statementcategory/edit',  compact('statementcategory','stmt_holdreason'));
		} else {
			return Redirect::to('statementcategory')->with('error', $api_response_data->message);
		}
	}

	/**
	 * Update the Statement hold reason.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$api_response = $this->getUpdateApi( $id,Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') {
			return Redirect::to('statementcategory')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')	{
			return Redirect::to('statementcategory/'.$id)->with('success',$api_response_data->message);
		} else {
			return Redirect::to('statementcategory/'.$id.'/edit')->withInput()->with('error', $api_response_data->message);
		}
	}

	/**
	 * Remove the specified statement hold reason.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success') {
			return Redirect::to('statementcategory')->with('success',$api_response_data->message);
		} else {
			return Redirect::to('statementcategory')->with('error', $api_response_data->message);
		}
	}

}
