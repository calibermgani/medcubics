<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Request;
use Redirect;
use Auth;
use View;
use Config;
use PDF;
use Excel;
use App\Exports\BladeExport;

class StatementHoldReasonController extends Api\StatementHoldReasonApiController {

	public function __construct() 
    {      
        View::share('heading', 'Practice');   
        View::share('selected_tab', 'statementholdreason');
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
        $statementholdreason     = $api_response_data->data->statementholdreason;
        return view('practice/statementholdreason/statementholdreason',compact('statementholdreason'));
	}
        
    public function getStatementHoldReasonExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $statementholdreason = $api_response_data->data->statementholdreason;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Statementholdreason_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/statementholdreason/statementholdreason_export_pdf', compact('statementholdreason', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/statementholdreason/statementholdreason_export';
            $data['statementholdreason'] = $statementholdreason;
            $data['export'] = $export;
            ob_clean();
            return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/statementholdreason/statementholdreason_export';
            $data['statementholdreason'] = $statementholdreason;
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
		return view('practice/statementholdreason/create');
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
            return Redirect::to('statementholdreason/'.$api_response_data->data)->with('success', $api_response_data->message);
        } else {
            return Redirect::to('statementholdreason/create')->withInput()->withErrors($api_response_data->message);
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
			$statementholdreason = $api_response_data->data->statementholdreason;
			return view ( 'practice/statementholdreason/show',compact('statementholdreason'));
		} else {
			return Redirect::to('statementholdreason')->with('error', $api_response_data->message);
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
			$statementholdreason = $api_response_data->data->statementholdreason;
			return view('practice/statementholdreason/edit',  compact('statementholdreason'));
		} else {
			return Redirect::to('statementholdreason')->with('error', $api_response_data->message);
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
			return Redirect::to('statementholdreason')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success') {
			return Redirect::to('statementholdreason/'.$id)->with('success',$api_response_data->message);
		} else {
			return Redirect::to('statementholdreason/'.$id.'/edit')->withInput()->with('error', $api_response_data->message);;
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
			return Redirect::to('statementholdreason')->with('success',$api_response_data->message);
		} else {
			return Redirect::to('statementholdreason')->with('error', $api_response_data->message);
		}
	}

}