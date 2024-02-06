<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Config;
use Illuminate\Http\Request;

class UserHistoryController extends Api\UserHistoryApiController {

	  public function __construct()
	{      
        View::share( 'heading', 'Users' );  
        View::share( 'selected_tab', 'practice/userhistory' );
        View::share( 'heading_icon', Config::get('cssconfigs.common.user'));
    } 
	
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$history = $api_response_data->data->history;
		return view('practice/userhistory/userhistorylist',  compact('history'));
	}

}
