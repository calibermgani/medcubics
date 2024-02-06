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
use App\Models\Profile\PersonalNotes;
use App\Models\Profile\MessageDetailStructure;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\UsersAppDetails as UsersAppDetails;
use App;
use DB;
use Cache;

class ProfileApiController extends Controller {
    public function getindexApi()
    {   
        $user = Auth::user()->id;
        $start_date = date("Y-m-d");
		
		// Get Events
        $events = ProfileEvents::where('start_date', '=', $start_date)->where('created_by',$user)->orderBy('created_at', 'DESC')->take(4)->get()->toArray();
		
		// Check blog based on public & private, group, selected user.
		$get_collect_array = array();
		// Get Blog Group based user.
		$bloggroup = Blog::whereHas('Blog_group', function($q) use($user) {
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
        $message_inbox_list_arr = MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); },'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user)->where('draft_message',0)->where('receiver_trash_status',0)->where('label_id',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('id','sender_id','receiver_id','message_id','read_status','created_at')->take(4)->get();
		return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('events','message_inbox_list_arr', 'blogs','user','users_table')));
    }
    
	public static function getProfileModuleCount($user_id){
       	$module_count = array();
       	// Implemented Cache for hold module count       	
       	$module_count = Cache::remember('user_module_cnt_'.$user_id , 10, function() use($user_id) {
            $module_count['event'] = ProfileEvents::where('start_date','=',date('Y-m-d'))->where('created_by','=',$user_id)->count();	   
			$module_count['message'] 	= MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); }, 'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$user_id)->where('read_status',0)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get()->count();
			
			$module_count['today_note_count']  = PersonalNotes::where('deleted_at',Null)->where('user_id',$user_id)->where('date',date('Y-m-d'))->count();
	       	$module_count['blog'] = Blog::where('user_id','=',$user_id)->count();
            return $module_count;
        });       	 

       return $module_count;
    }
	
	###Old password and new password set in Database####
	public function postchangepasswordApi($request=''){
		$request = Request::all();
		$db_pwd = Auth::user()->password;
		$db_email = Auth::user()->email;
		$validation_rule = array('cpassword' => 'required','password' => 'required|min:6|same:password', 'confirmpassword' => 'required|min:6|same:password');
		$validator = Validator::make($request, $validation_rule, User::messages());
		//Data base value and current password is same
		$table_val=Hash::check( $request['cpassword'],$db_pwd);
		##validation error or Not error##
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
		}
		else
		{
			if($table_val== true) { 
				$user1 = User::where('email',$db_email)->first();
				##update in databse##
				$user1->update(['password'=>Hash::make($request['password']),'password_change_time'=>date('Y-m-d H:i:s')]);
				UsersAppDetails::where('user_id',$user1['id'])->update(['authentication_id'=>'']);
				return Response::json(array('status'=>'success', 'message'=>'password update successfully','data'=>''));
			} else { 
				##Erorr status##
				return Response::json(array('status'=>'error', 'message'=>'Old password is not match','data'=>''));
			}
		}
	}	
}