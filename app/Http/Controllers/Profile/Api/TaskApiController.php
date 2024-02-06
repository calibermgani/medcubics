<?php 
namespace App\Http\Controllers\Profile\Api;
use Auth;
use Request;
use Response;
use Input;
use Validator;
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
use App;
use DB;

class TaskApiController extends Controller {
    public function getindexApi()
    {   
        $user = Auth::user()->id;
        $start_date = date("Y-m-d");
		$login_user = User::orderBy('created_at', 'ASC')->where('id',$user)->get();
		
		// Get Events
        $events = ProfileEvents::where('start_date', '=', $start_date)->where('created_by',$user)->orderBy('created_at', 'DESC')->take(4)->get()->toArray();
		
		// Check blog based on public & private, group, selected user.
		$get_collect_array = array();
		// Get Blog Group based user.
		$bloggroup = Blog::whereHas('Blog_group', function($q) use($user)
		{
			$q->whereRaw('find_in_set(?, `group_users`)',[$user]);

		})->where('privacy','=','Group')->where('status','=','Active')->select(DB::raw('group_concat(id) as Blog_id'))->get();
		$getgroupblogid = explode(',',$bloggroup[0]['Blog_id']);
		$get_collect_array = array_merge($get_collect_array,$getgroupblogid);	
		
		// Get selected user & public list.	
		$blogselecteduser = Blog::whereRaw('(find_in_set("'.$user.'", `user_list`) and privacy="User") or privacy="Public"')->where('status','=','Active')->select(DB::raw('group_concat(id) as Blog_id'))->get();
		$getselectuserblogid = explode(',',$blogselecteduser[0]['Blog_id']);
		$get_collect_array = array_merge($get_collect_array,$getselectuserblogid);
		
		$collectblogids =  array_filter($get_collect_array);
		$blogs = Blog::whereIn('id', $collectblogids)->where('status','=','Active')->take(4)->get();
		
		//dd(Auth::user());
		$users_table = User::orderBy('created_at', 'ASC')->where('customer_id',Auth::user()->customer_id)->get();
		//dd($practices);
        $message_inbox_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->where('user_id',$user)->orderBy('created_at','DESC')->take(4)->get();
		$today_notes = PersonalNotes::where('deleted_at',Null)->where('user_id',$user)->get()->count();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('events','message_inbox_list_arr', 'blogs','user','users_table','login_user','today_notes')));
    }
	
	public function GetMessageApi() {
		$user_id 		= Auth::user()->id;
		$inbox_message	= array();
		
		$inbox_message 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->get();
		
		$message_count 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get()->count();
		
		$inbox_unread_count 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('read_status',0)->where('receiver_trash_status',0)->where('draft_message',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get()->count();
		
		$label_list = PrivateMessageLabelList::where('deleted_at',Null)->where('user_id',$user_id)->select('id','label_name','label_color','user_id')->get();
		
		$users_list_ori	= User::where('id','!=',$user_id)->orderBy('email','ASC')->pluck('email','id')->all();
		$users_list		= $users_list_ori;
		
		$today_notes = PersonalNotes::where('deleted_at',Null)->where('user_id',$user_id)->get()->count();
		
		return Response::json(array('status'=>'success','inbox_message'=>$inbox_message,'users_list'=>$users_list,'message_count'=>$message_count,'inbox_unread_count'=>$inbox_unread_count,'label_list'=>$label_list,'today_notes'=>$today_notes));			
	}	
}