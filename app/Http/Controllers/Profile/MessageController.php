<?php namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Request;
use Input;
use Redirect;
use Auth;
use View;
use Config;
use App\Http\Controllers\Profile\Api\MessageApiController as MessageApiController;

class MessageController extends MessageApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	 
    public function __construct() {
        $api_response	    = $this->GetMessageApi();
        $api_response_data 	= $api_response->getData();
        View::share( 'users_list', $api_response_data->users_list);
        View::share( 'inbox_message', $api_response_data->inbox_message);
        View::share( 'message_count', $api_response_data->message_count);
        View::share( 'inbox_unread_count', $api_response_data->inbox_unread_count);
        View::share( 'label_list', $api_response_data->label_list);
        View::share( 'today_notes', $api_response_data->today_notes);
        View::share( 'heading', 'Messages' );    
        View::share( 'selected_tab', 'message' ); 
        View::share( 'heading_icon', Config::get('cssconfigs.common.message'));
    }
	
	/**
	 * Display Message page and relavant information.
	 *
	 * 
	 * @return to the view page
	 */
    public function index(){
        $api_response = $this->getindexApi();
        $api_response_data = $api_response->getData();
        $login_user = $api_response_data->data->login_user;
        $blogs1 = $api_response_data->data->blogs;
        $total_record     = count($blogs1);
        $blogs = $blogs1;
        $inbox_message = $api_response_data->data->messages;
        $message_count = count($inbox_message);
        $total_messages = count($inbox_message);
        $inbox_unread_count = $api_response_data->data->inbox_unread_count;
        if ($api_response_data->status == 'success') {
            $data=$api_response_data->data;
            $events=$data->events;
            $users=$data->users_table;
            $blogs=$data->blogs;
            $PrivateMessageDetails = $api_response_data->data->message_inbox_list_arr;
            return view('profile/message/message', compact('events','blogs','PrivateMessageDetails','users','login_user','total_record','inbox_message','message_count', 'inbox_unread_count', 'total_messages'));
        } else {
            return view('profile/profile');            
        }
    }
	
	/**
	 * Create a new message controller instance.
	 *
	 * 
	 * @return void
	 */
	 
	public function getComposemail()
	{	
		return view('profile/message/composemail');
		
	}
	
	/**
	 * Getting particular message data.
	 *
	 * @para message type, current page and user id
	 * @return to the view
	 */
	public function getMessageData(Request $request){ 
            $getresponseApi = $this->getMessageDataApi($request);
            $api_response_data 	= $getresponseApi->getData();
            $inbox_message = $api_response_data->inbox_message;
            $current_id = $api_response_data->current_id;
            $request 		= Request::all();
            $request_type = $request['type'];
            return view('profile/message/dynamicdetails',compact('inbox_message','current_id','request_type'));
	}
	
	/**
	 * Getting message data based on request type .
	 *
	 * @para message type, current page and user id
	 * @return to the view
	 */
	public function getMessageTypeData(Request $request){
		$getresponseApi = $this->getMessageTypeDataApi($request);
		$api_response_data 	= $getresponseApi->getData();
		$inbox_message = $api_response_data->inbox_message;
		$message_count = $api_response_data->message_count;
		$request 		= Request::all();
		$request_type = $request['type'];
		return view('profile/message/dynamiclisting',compact('inbox_message','message_count','request_type'));
	}
	
	/**
	 * Getting unread message count.
	 *
	 * @param  user_id
	 * @return to the view
	 */
	public function getInboxCount(Request $request){
            $getresponseApi = $this->getInboxCountApi($request);
            $api_response_data 	= $getresponseApi->getData();
            $inbox_unread_count = $api_response_data->inbox_unread_count;
            return response()->json(['msg'=>$inbox_unread_count]);
	}
	
	/**
	 * Moving the messages to trash section.
	 *
	 * @param  meesage id and user_id
	 * @return to the view
	 */
	public function getSetTrash(Request $request){
            $getresponseApi = $this->getSetTrashApi($request);
            $api_response_data 	= $getresponseApi->getData();
            return response()->json(['data'=>$api_response_data]);
	}
	
	/**
	 * Replaying the message to already sended users controller instance.
	 *
	 * 
	 * @return void
	 */
	public function getreplaymail($id){
		$getresponseApi = $this->getMessageDetailsApi($id);
		$api_response_data 	= $getresponseApi->getData();
		$message_data = $api_response_data->message_details;
		return view('profile/message/replaymail',compact('message_data'));
	}
	
	/**
	 * Fowarding the message to new users controller instance.
	 *
	 * 
	 * @return void
	 */
	public function getforwardmail($id){
            $getresponseApi = $this->getMessageDetailsApi($id);
            $api_response_data 	= $getresponseApi->getData();
            $message_data = $api_response_data->message_details;
            return view('profile/message/forwardmail',compact('message_data'));
	}
	
	/**
	 * Moving the message to draft section in the message module.
	 *
	 * 
	 * @return to the view
	 */
	public function getdraftmail($id){
		$getresponseApi = $this->getMessageDetailsApi($id);
		$api_response_data 	= $getresponseApi->getData();
		$message_data = $api_response_data->message_details;
		
		return view('profile/message/draftmail',compact('message_data','id'));
	}
	
	/**
	 * Moving the message in particular label section.
	 *
	 * 
	 * @return to the view
	 */
	public function setLabel(Request $request){
		$getresponseApi = $this->setLabelApi($request);
		$api_response_data 	= $getresponseApi->getData();
		echo $api_response_data->status;
	}
	
	/**
	 * searching the email or subject based on in the search module.
	 *
	 * 
	 * @return to the view
	 */
	public function searchmessage(Request $request){
		$getresponseApi = $this->getSearchmessageApi($request);
		$api_response_data 	= $getresponseApi->getData();
		$inbox_message = $api_response_data->inbox_message;
		$message_count = $api_response_data->message_count;
		$request_type = 'inbox';
		return view('profile/message/fliterlisting',compact('inbox_message','message_count','request_type'));
	}
	
}