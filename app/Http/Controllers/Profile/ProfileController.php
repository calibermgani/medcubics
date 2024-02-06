<?php namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Request;
use Input;
use Redirect;
use Auth;
use View;
use Config;
use App\Http\Helpers\Helpers as Helpers;

use App\Http\Controllers\Profile\Api\ProfileApiController as ProfileApiController;

class ProfileController extends ProfileApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	 
	 public function __construct() { 
	  
		View::share ( 'heading', 'Profile' );    
	    View::share ( 'selected_tab', 'profile' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
    }  
	 
    public function index() {
        // Redirect to profile page.
        $user_id = isset(Auth::user()->id) ? Auth::user()->id : 0;
        $id = Helpers::getEncodeAndDecodeOfId($user_id, 'encode');
        return Redirect::to('profile/personaldetailsview/' . $id);

        $api_response = $this->getindexApi();
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            $data = $api_response_data->data;
            $events = $data->events;
            $users = $data->users_table;
            $blogs = $data->blogs;
            $PrivateMessageDetails = $api_response_data->data->message_inbox_list_arr;
            return view('profile/profile', compact('events', 'blogs', 'PrivateMessageDetails', 'users'));
        } else {
            return view('profile/profile');
        }
    }

    public function profile1()
	{
		return view('profile/profile1');
	}

	###profile Password change###
	public function getchangepassword()
	{
		$hashedPassword = Auth::user()->password;
		$selected_tab = "changepassword";
		// $heading	= "Change PWD";
		$heading_icon = Config::get('cssconfigs.Practicesmaster.change_password');
		return view('profile/changepassword/changepassword',compact('user','selected_tab','heading','heading_icon'));	
	}
	####new password submit ####
	public function postchangepassword(Request $request) {
            $user_id = isset(Auth::user()->id) ? Auth::user()->id : 0;
            $id = Helpers::getEncodeAndDecodeOfId($user_id, 'encode');
            $api_response = $this->postchangepasswordApi($request::all());
            $api_response_data = $api_response->getData();
            if ($api_response_data->status == 'success') {
                ###Success page##
                return Redirect::to('profile/personaldetailsview/' . $id)->with('success', $api_response_data->message);                
            } else {
            ##Error page redirect##
            return Redirect::to('/profile/changepassword')->withInput()->with('error', $api_response_data->message);            
            }
        }

}