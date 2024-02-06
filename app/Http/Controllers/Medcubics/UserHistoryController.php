<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Response;
use Redirect;
use Auth;
use View;
use Config;

class UserHistoryController extends Api\UserHistoryApiController {

	public function __construct()
	{
        View::share( 'heading', 'User History' );
        View::share( 'selected_tab', 'admin/userhistory' );
        View::share( 'heading_icon', Config::get('cssconfigs.admin.users'));
    }

	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$history = $api_response_data->data->history;
		$customers = $api_response_data->data->customers;
		$heading = 'Customers';
		return view('admin/userhistory/userhistorylist',  compact('history','heading', 'customers'));
	}

	public function getList()
	{
		$data = Request::all();
		$api_response = $this->getListIndexApi($data);

		$api_response_data = $api_response->getData();
		$history = $api_response_data->data->history;
		//$customers = $api_response_data->data->customers;

		$view_html = Response::view('admin/userhistory/userhistorylistAjax', compact('history'));

        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        $request = Request::all();
        if (!empty($request['draw']))
            $data['draw'] = $request['draw'];

        $data['data'] = $content;
        $data['datas'] = ['id' => 2];
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;

        return Response::json($data);
	}

}
