<?php 
namespace App\Http\Controllers\Profile\Api;
use Auth;
use Request;
use Response;
use Input;
use Validator;
use Config;
use Hash;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as User;
use App\Models\Profile\ProfileEvents as ProfileEvents;
use App\Models\Profile\Blog as Blog;
use App\Models\Profile\PrivateMessageDetails;
use App\Models\Profile\PrivateMessage;
use App\Models\Profile\MessageDetailStructure;
use App\Models\Profile\PrivateMessageLabelList;
use App\Models\Profile\PrivateMessageSettings;
use App\Models\Profile\PersonalNotes;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\UsersAppDetails as UsersAppDetails;
use App\Http\Controllers\HomeController as HomeController;
use App;
use DB;
use Session;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Users;
class MessageApiController extends Controller {
   
   /**
	 * This function showing the basic notification count in the message module.
	 *
	 * use App\Models\Profile\PrivateMessageDetails
	 * use App\Models\Profile\ProfileEvents
	 * @return the blog count ,message count and login user details
	 */
    public function getindexApi() {
        $user = Auth::user()->id;
        $start_date = date("Y-m-d");
        $login_user = User::orderBy('created_at', 'ASC')->where('id', $user)->get();

        // Getting Blog Count
        $getblogs_public = Blog::with('user')->where('user_id', '=', $user);
        $blogs = $getblogs_public->get();
        // Get Events
        $events = ProfileEvents::where('start_date', '=', $start_date)->where('created_by', $user)->orderBy('created_at', 'DESC')->take(4)->get()->toArray();
        /* Message tab : Users: Online: Get active user list - Anjukaselvan*/
        $users_table = HomeController::getActiveUserList();
        $message_inbox_list_arr = PrivateMessageDetails::with('user', 'PrivateMessage')->where('user_id', $user)->orderBy('created_at', 'DESC')->take(4)->get();
        $messages = MessageDetailStructure::with(['message_detail' => function($query) {
                        $query->select('subject', 'message_body', 'id', 'created_at', 'attachment_file');
                    }, 'sender_details' => function($query) {
                        $query->select('id', 'email', 'name', 'avatar_name', 'avatar_ext');
                    }])->where('receiver_id', $user)->where('deleted_at',Null)->orderBy('created_at', 'desc')->select('sender_id', 'receiver_id', 'message_id', 'read_status', 'created_at')->get();
        $inbox_unread_count 	= MessageDetailStructure::where('receiver_id',$user)->where('read_status',0)->count();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('events', 'message_inbox_list_arr', 'blogs', 'user', 'users_table', 'login_user', 'messages', 'inbox_unread_count')));
    }

    public function MailSettingsApi()
	{
		$user_id 		= Auth::user()->id;
		$result 		= array(); 
		$lastlabeldet	= array(); 
		if(PrivateMessageSettings::where('user_id', $user_id)->count())
		{
			$result = PrivateMessageSettings::where('user_id',$user_id)->first();
		}
		if(PrivateMessageLabelList::where('user_id', $user_id)->count())
		{
			$lastlabeldet = PrivateMessageLabelList::where('user_id',$user_id)->get();
		}
		$message_inbox_list_unread_count = PrivateMessageDetails::whereRaw('user_id = ? and recipient_deleted = "0" and label_list_type = "0" and recipient_read = "0" ', array($user_id))->orderBy('created_at','DESC')->groupBy('parent_message_id')->get()->count();
		$users_list_ori	= User::where('id','!=',$user_id)->orderBy('email','ASC')->pluck('email','id')->all();
		$users_list		= $users_list_ori;
		$users_list_arr = "'".implode("','", $users_list_ori)."'";
		return Response::json(array('status'=>'success','result'=>$result,'message_inbox_list_unread_count'=>$message_inbox_list_unread_count,'lastlabeldet'=>$lastlabeldet,'users_list'=>$users_list,'users_list_arr'=>$users_list_arr));
	}

	
	/*** Getting Basic information on loading ***/
	
	/**
	 * This function showing the message details in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * use App\Models\Profile\PrivateMessageLabelList
	 * use App\Models\Profile\PersonalNotes
	 * @return the blog count ,message count,note lists and login user details
	 */
	
	
	public function GetMessageApi() {
		
		$user_id 		= Auth::user()->id;
		$customer_id = Auth::user()->customer_id;
		$inbox_message	= array();
		
		$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
		
		$message_count 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get()->count();
		
		$inbox_unread_count 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('read_status',0)->where('receiver_trash_status',0)->where('draft_message',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get()->count();
		
		$label_list = PrivateMessageLabelList::where('deleted_at',Null)->where('user_id',$user_id)->select('id','label_name','label_color','user_id')->get();
		
		$today_notes = PersonalNotes::where('deleted_at',Null)->where('user_id',$user_id)->get()->count();
		
		$users_list_ori	= User::where('id','!=',$user_id)->where('customer_id',$customer_id)->orderBy('email','ASC')->pluck('email','id')->all();
		$users_list		= $users_list_ori;
		return Response::json(array('status'=>'success','inbox_message'=>$inbox_message,'users_list'=>$users_list,'message_count'=>$message_count,'inbox_unread_count'=>$inbox_unread_count,'label_list'=>$label_list,'today_notes'=>$today_notes));
	}
	
	/**
	 * This function showing the particular section message details in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * use App\Models\Profile\PrivateMessageLabelList
	 * use App\Models\Profile\PersonalNotes
	 * @return the message list and message details
	 */
	
	public function getMessageDataApi($request=''){
		$request = Request::all();
		$user_id = Auth::user()->id;
		if(strtolower($request['type']) == 'inbox') {
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('message_id',$request['message_id'])->where('draft_message',0)->where('label_id',0)->where('receiver_trash_status',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();			
			$update_status = DB::table('message_detail_structure')->where('message_id','=',$request['message_id'])->update(array('read_status' => 1));
		} elseif(strtolower($request['type']) == 'send') {
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); },'receiver_details'=>function($query){ $query->select('id','email','name');}])->where('sender_id',$user_id)->where('message_id',$request['message_id'])->where('draft_message',0)->where('label_id',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();			
		} elseif(strtolower($request['type']) == 'draft') {
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('sender_id',$user_id)->where('message_id',$request['message_id'])->where('draft_message',1)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
		} elseif(strtolower($request['type']) == 'trash') {			
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->whereRaw('sender_id ='.$user_id. ' OR receiver_id ='.$user_id)->where('message_id',$request['message_id'])->whereRaw('sender_trash_status = 1 OR receiver_trash_status = 1')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','sender_trash_status','receiver_trash_status','created_at')->get();			
		} else {
			$label_details = PrivateMessageLabelList::where('label_name',strtolower($request['type']))->where('user_id',$user_id)->select('id')->first();
			$label_id = $label_details->id;			
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject',	'message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); },'label_detail'=>function($query){ $query->select('label_color','id','label_id');}])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->where('label_id',$label_id)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','sender_trash_status','receiver_trash_status','created_at','label_id')->get();			
		}			
		return Response::json(array('status'=>'success','inbox_message'=>$inbox_message,'current_id'=>$request['current_id']));
	}
	
	/**
	 * This function showing the particular section message details in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * use App\Models\Profile\PrivateMessageLabelList
	 * use App\Models\Profile\PersonalNotes
	 * @return the message list and message details
	 */
	
	public function getMessageTypeDataApi($request=''){
		$request 		= Request::all();
		$user_id 		= Auth::user()->id;
		$inbox_message	= array();
		if(strtolower($request['type']) == 'inbox') {
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject',	'message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->where('receiver_trash_status',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			$message_count 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('draft_message',0)->where('label_id',0)->where('receiver_trash_status',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get()->count();
		} elseif(strtolower($request['type']) == 'send') {
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject',	'message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); },'receiver_details'=>function($query){ $query->select('id','email','name');}])->where('sender_id',$user_id)->where('draft_message',0)->where('sender_trash_status',0)->where('label_id',0)->where('deleted_at',Null)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			$message_count 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('sender_id',$user_id)->where('draft_message',0)->where('sender_trash_status',0)->where('label_id',0)->where('deleted_at',Null)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get()->count();
		} elseif(strtolower($request['type']) == 'draft') {
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject',	'message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('sender_id',$user_id)->where('draft_message',1)->where('sender_trash_status',0)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
			$message_count 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('sender_id',$user_id)->where('draft_message',0)->where('sender_trash_status',0)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get()->count();
		} elseif(strtolower($request['type']) == 'trash') {
			
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject',	'message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->whereRaw('(sender_trash_status = 1 OR receiver_trash_status = 1)')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','sender_trash_status','receiver_trash_status','created_at')->get();
			
			$message_count 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->whereRaw('(sender_trash_status = 1 OR receiver_trash_status = 1)')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','sender_trash_status','receiver_trash_status','created_at')->get()->count();
		} else {
			
			$label_details = PrivateMessageLabelList::where('label_name',strtolower($request['type']))->where('user_id',$user_id)->select('id')->first();
			$label_id = $label_details->id;			
			$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject',	'message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); },'label_detail'=>function($query){ $query->select('label_color','id','label_id');}])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->where('label_id',$label_id)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','sender_trash_status','receiver_trash_status','created_at','label_id')->get();		
			
			$message_count 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject',	'message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); },'label_detail'=>function($query){ $query->select('label_color','id','label_id');}])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->where('label_id',$label_id)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','sender_trash_status','receiver_trash_status','created_at','label_id')->get()->count();
		}
		return Response::json(array('status'=>'success','inbox_message'=>$inbox_message,'message_count'=>$message_count));
	}
	
	/**
	 * This function count the unread message in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * @return the message list and message details
	 */
	
	public function getInboxCountApi($request=''){
            $user_id = Auth::user()->id;
            $inbox_unread_count 	= MessageDetailStructure::where('receiver_id',$user_id)->where('read_status',0)->count();
            return Response::json(array('status'=>'success','inbox_unread_count'=>$inbox_unread_count));
	}
		
	/**
	 * This function moving the message to trash in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * use App\Models\Profile\PrivateMessageLabelList
	 * use App\Models\Profile\PersonalNotes
	 * @return the status and message type
	 */
	public function getSetTrashApi($request=''){
            $user_id = Auth::user()->id;
            $request 		= Request::all();
            $check_type = DB::table('message_detail_structure')->where('message_id',$request['message_id'])->first();

            if($check_type->sender_id == $user_id){
                $status = 1 + $check_type->sender_trash_status;
                $update_status = MessageDetailStructure::where('message_id',$request['message_id'])->update(array('sender_trash_status'=>$status,'deleted_at' => date("Y-m-d h:i:s")));
            } else {
                $status = 1 + $check_type->receiver_trash_status;
                $update_status = MessageDetailStructure::where('message_id',$request['message_id'])->update(array('receiver_trash_status'=>$status,'deleted_at' => date("Y-m-d h:i:s")));
            }
            return Response::json(array('status'=>'success','check_type'=>$check_type,'msgCnt'=>MessageDetailStructure::where('receiver_id',$user_id)->count()));
	}
	
	/**
	 * This function moving the message to particular label in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * @return the status 
	 */
	
	public function setLabelApi($request){
		$user_id = Auth::user()->id;
		$request = Request::all();
		$update_label = MessageDetailStructure::where('id',$request['message_structure_id'])->update(array('label_id'=>$request['label_id']));
		return Response::json(array('status'=>'success'));
	}
	
	/**
	 * This function using to search the email or subject based result will be provided in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * 
	 * @return the status and message
	 */
	public function getSearchmessageApi($request){
		$user_id = Auth::user()->id;
		$request = Request::all();
		$key = $request['searchkey'];
		$page_type = $request['page_type'];
		$inbox_message	= array();
		$user_ids = User::where('email', 'LIKE', '%'.$key.'%')->pluck('id')->all();
		$message_ids = PrivateMessage::where('subject', 'LIKE', '%'.$key.'%')->pluck('id')->all();
		if($key == ''){
			//if($page_type == 'inbox'){
				$inbox_message = MessageDetailStructure::where('receiver_id',$user_id)->with(['message_detail', 'sender_details'])->where('receiver_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->where('deleted_at',Null)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
				$message_count 	= MessageDetailStructure::where('receiver_id',$user_id)->with(['message_detail', 'sender_details'])->where('receiver_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->where('deleted_at',Null)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
			/* }elseif($page_type == 'send'){
				$inbox_message = MessageDetailStructure::where('sender_id',$user_id)->with(['message_detail', 'sender_details'])->where('sender_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
				$message_count 	= MessageDetailStructure::where('sender_id',$user_id)->with(['message_detail', 'sender_details'])->where('sender_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
			}elseif($page_type == 'draft'){
				$inbox_message = MessageDetailStructure::where('sender_id',$user_id)->with(['message_detail', 'sender_details'])->where('sender_id',$user_id)->where('draft_message',1)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
				$message_count 	= MessageDetailStructure::where('sender_id',$user_id)->with(['message_detail', 'sender_details'])->where('sender_id',$user_id)->where('draft_message',1)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
			}elseif($page_type == 'trash'){
				$inbox_message = MessageDetailStructure::whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->with(['message_detail', 'sender_details'])->where('draft_message',0)->whereRaw('sender_trash_status = 1 OR receiver_trash_status = 1')->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
				$message_count 	= MessageDetailStructure::whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->with(['message_detail', 'sender_details'])->where('draft_message',0)->whereRaw('sender_trash_status = 1 OR receiver_trash_status = 1')->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
				
			}else{
				$inbox_message = MessageDetailStructure::whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->with(['message_detail', 'sender_details'])->where('draft_message',0)->whereRaw('sender_trash_status = 1 OR receiver_trash_status = 1')->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
				$message_count 	= MessageDetailStructure::whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->with(['message_detail', 'sender_details'])->where('draft_message',0)->whereRaw('sender_trash_status = 1 OR receiver_trash_status = 1')->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
			} */		
		} else {
			//if($page_type == 'inbox'){
				$inbox_message = MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
				->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
						
				$message_count 	= MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
				->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
			/* }elseif($page_type == 'send'){
				$inbox_message = MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
				->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->where('sender_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
			
				$message_count 	= MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
			->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->where('sender_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
			}elseif($page_type == 'draft'){
				$inbox_message = MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
				->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->where('sender_id',$user_id)->where('draft_message',1)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
			
				$message_count 	= MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
			->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->where('sender_id',$user_id)->where('draft_message',1)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
			}elseif($page_type == 'trash'){
				$inbox_message = MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
				->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->where('draft_message',0)->whereRaw('sender_trash_status = 1 OR receiver_trash_status = 1')->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
			
				$message_count 	= MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
			->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->where('draft_message',0)->whereRaw('sender_trash_status = 1 OR receiver_trash_status = 1')->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
			}else{
				$inbox_message = MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
				->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->where('receiver_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
			
			
				$message_count 	= MessageDetailStructure::where(function ($query) use ($message_ids){ 
					if($message_ids)
						$query->whereIn('message_id', $message_ids);
					else
						$query->whereIn('message_id', array('0'));
					})
			->orwhere(function ($query)use ($user_ids) { 
					if($user_ids)
						$query->whereIn('sender_id', $user_ids);
				})->with(['message_detail', 'sender_details'])->where('receiver_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->groupBy('message_id')->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->count();
			} */
		}
		return Response::json(array('status'=>'success','inbox_message'=>$inbox_message,'message_count'=>$message_count));
	}
	/*** Getting Basic information on loading ***/

	/*** Message send process start ***/
	
	/**
	 * This function using to send a message to other user in the same practises in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * use App\Models\Profile\PrivateMessage
	 * 
	 * @return the status and message type
	 */
	public function messagesendprocess()
	{
		$request = Request::all();
		
		$rules =array('attachment_file'=>Config::get('siteconfigs.file_uplode.defult_file_attachment'));
		$messages = array('attachment_file.mimes'=>Config::get('siteconfigs.file_uplode.defult_file_message'));
		$validator = Validator::make($request, $rules,$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			$attachment_file_err = $errors->get('attachment_file');
			$status = json_encode(array('status'=>'errors', 'message'=>$attachment_file_err[0]));
			return $status;		
		}
		else
		{
			$ins_datas  				= array();
			$user_id					= Auth::user()->id;
			$ins_datas['message_id'] 	= md5("M".$user_id.strtotime(date('Y-m-d H:i:s')));
			$ins_datas['subject'] 		= $request['mail_subject'];
			$ins_datas['message_body'] 	= $request['compose-textarea'];
			$ins_datas['send_user_id'] 	= $user_id;
			$check_stared	 			= "no";
			if($request['to_address']!="")
			{
				$ins_datas['recipient_users_id'] 	= $request['to_mail_id'];
				$to_address				=explode(",",$request['to_mail_id']);
			}
			if($request['mail_sent_type']=="new" || $request['mail_sent_type']=="draft") {
				$parent_message_id = $ins_datas['message_id'];
			} elseif($request['mail_sent_type']=="reply") {
				$curr_msg_det 		= PrivateMessageDetails::where('message_id',$request['curr_mail_id'])->first()->toArray();
				$parent_message_id 	= $curr_msg_det['parent_message_id'];
				$check_stared	 	= "yes";
			}
			
			if(Input::hasFile('attachment_file')) {
				$file 		= Input::file('attachment_file');
				$filename 	= md5($user_id.strtotime(date('Y-m-d H:i:s')));
				$extension = $file->getClientOriginalExtension();
				$filestoreName = $filename .'.'.$extension;
				$path = 'media/private_message';
				$file->move($path, $filestoreName); 
				$ins_datas['attachment_file'] = $filestoreName;
				$ins_datas['draft_message'] = 0;
			} elseif(isset($request['curr_mail_id'])) {
				$ins_datas['attachment_file'] = PrivateMessage::Where('message_id',$request['curr_mail_id'])->pluck("attachment_file")->first();
			}
			if($request['mail_sent_type']=="draft")	{
				PrivateMessage::Where('message_id',$request['curr_mail_id'])->delete();
			}
			$result = PrivateMessage::create($ins_datas);
			
			$ins_datas_recep = array();
			foreach($to_address as $kk=>$vv)
			{
				$ins_datas_recep['message_id'] 			= $ins_datas['message_id'];
				$ins_datas_recep['parent_message_id'] 	= $parent_message_id;
				$ins_datas_recep['send_user_id'] 		= $user_id;
				$ins_datas_recep['user_id'] 			= $vv;
				$ins_datas_recep['sender_stared']		= 0;
				$ins_datas_recep['recipient_stared'] 	= 0;
				if($check_stared == "yes") {
					if(PrivateMessageDetails::Where(function($query)use ($vv){ $query->whereRaw('user_id = ? and recipient_stared = "1"', array($vv))->orwhereRaw('send_user_id = ? and sender_stared = "1"', array($vv));})->Where(function($query1)use ($parent_message_id){ $query1->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->count()){
						$ins_datas_recep['recipient_stared'] 	= 1;
					}
					
					if(PrivateMessageDetails::Where(function($query)use ($user_id){ $query->whereRaw('user_id = ? and recipient_stared = "1"', array($user_id))->orwhereRaw('send_user_id = ? and sender_stared = "1"', array($user_id));})->Where(function($query1)use ($parent_message_id){ $query1->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->count()){
						$ins_datas_recep['sender_stared'] 	= 1;
					}				
				}				
				$res = PrivateMessageDetails::create($ins_datas_recep);
				unset($ins_datas_recep);
			}
			$status = json_encode(array('status'=>'success'));
			return $status;
		}	
    }
	/*** Message send process end ***/


	/*** selvakumar  Message Process start ***/
	
	
	/**
	 * This function using to send a message to other user in the same practises in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * use App\Models\Profile\PrivateMessage
	 * 
	 * @return the status and message type
	 */
	public function messageinsert(){
		
		$request = Request::all();
		$rules =array('attachment_file'=>Config::get('siteconfigs.file_uplode.defult_file_attachment'));
		$messages = array('attachment_file.mimes'=>Config::get('siteconfigs.file_uplode.defult_file_message'));
		$validator = Validator::make($request, $rules,$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			$attachment_file_err = $errors->get('attachment_file');
			$status = json_encode(array('status'=>'errors', 'message'=>$attachment_file_err[0]));
			return $status;
			
		}
		else
		{
			$private_message  					= array();
			$user_id							= Auth::user()->id;
			
			$private_message['message_id'] 		= md5("M".$user_id.strtotime(date('Y-m-d H:i:s')));
			$private_message['subject'] 		= $request['mail_subject'];
			$private_message['message_body'] 	= $request['compose-textarea'];
			$private_message['send_user_id'] 	= $user_id;
			if($request['to_address']!="")
			{
				$private_message['recipient_users_id'] 	= $request['to_mail_id'];
				$to_address				=explode(",",$request['to_mail_id']);
			}
			$private_message['attachment_file'] = '';
			if(Input::hasFile('attachment_file'))
			{
				$file 		= Input::file('attachment_file');
				$filename 	= md5($user_id.strtotime(date('Y-m-d H:i:s')));
				$extension = $file->getClientOriginalExtension();
				$filestoreName = $filename .'.'.$extension;
				$path = 'media/private_message';
				$file->move($path, $filestoreName); 
				$private_message['attachment_file'] = $filestoreName;
				$message_deatil_structure['attachment_file'] = $filestoreName;
			}
			if($request['page_type'] == 'draft' && $request['current_message_id'] !=''){
				
				$stored_data = PrivateMessage::where('id',$request['current_message_id'])->update($private_message);
			} else
				$stored_data = PrivateMessage::create($private_message);
			
			foreach($to_address as $list_of_id){
				$message_deatil_structure = array();
				$message_deatil_structure['sender_id'] 		=  Auth::user()->id;
				$message_deatil_structure['parent_message_id'] 	=  $request['pre_message_id'];
				if($request['page_type'] == 'draft' && $request['current_message_id'] !=''){
					$message_deatil_structure['message_id'] 	=  $request['current_message_id'];	
				} else {
					$message_deatil_structure['message_id'] 	=  $stored_data->id;
				}
				$message_deatil_structure['receiver_id'] 	=  $list_of_id;
				if($request['message_type'] == 'draft') {
					$message_deatil_structure['draft_message'] 	= 1;
				} else {
					$message_deatil_structure['draft_message'] 	= 0;
				}
				if($private_message['attachment_file'] !='')
					$message_deatil_structure['attachment_file'] = $private_message['attachment_file'];
				if($request['page_type'] == 'draft' && $request['current_message_id'] !='')
					MessageDetailStructure::where('message_id',$request['current_message_id'])->update($message_deatil_structure);
				else
					MessageDetailStructure::create($message_deatil_structure);
			}
			$status = json_encode(array('status'=>'success'));
			return $status;
		}
	}
	
	
	/**
	 * This function using to gettting paritcule message details in the message module.
	 *
	 * use App\Models\Profile\MessageDetailStructure
	 * use App\Models\Profile\PrivateMessage
	 * 
	 * @return the status and message details
	 */
	
	public function getMessageDetailsApi($id){
		$user_id = Auth::user()->id;
		$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject',	'message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->whereRaw('(sender_id ='.$user_id. ' OR receiver_id ='.$user_id.')')->where('message_id',$id)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get();

		return Response::json(array('status'=>'success','message_details'=>$inbox_message));
	}
	/*** selvakumar  Message Process end ***/
	
	
}