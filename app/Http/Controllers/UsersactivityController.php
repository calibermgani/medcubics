<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Config;
use Session;
use App\Models\Medcubics\Practice;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class UsersactivityController extends Api\UsersactivityApiController 
{

    public function __construct() 
    {      
        View::share('heading','Practice');  
        View::share('selected_tab','usersactivity');
        View::share('heading_icon', Config::get('cssconfigs.admin.users'));
    }  

    /** Display a listing of the resource. */
    public function index()
    {     
        $api_response       = $this->getIndexApi();
        $api_response_data  = $api_response->getData();
        $chargeslogdata          = $api_response_data->data->chargeslist;
        $pagination               = $api_response_data->data->pagination;
        $chargeslog = $chargeslogdata->data;
        $tabs               = $api_response_data->data->tabs;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];     
        return view('practice/usersactivity/chargeslisting',  compact('chargeslog','tabs','pagination'));
    } 
}
