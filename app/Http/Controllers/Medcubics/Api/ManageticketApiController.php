<?php namespace App\Http\Controllers\Medcubics\Api;

use Response;
use Validator;
use Request;
use Auth;
use Lang;
use App\Http\Controllers\Controller;
use App\Models\Support\Ticket as Ticket;
use App\Models\EmailTemplate;
use App\Models\Support\TicketDetail as TicketDetail;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Users as Users;
use App\Models\Support\AssignTicketHistory as AssignTicketHistory;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;


class ManageticketApiController extends Controller {
	/***   Ticket List page start   ***/
	public function getIndexApi($export='')
	{
		$ticket = Ticket::with('get_assignee')->orderBy('id','DESC')->get();
		
		if($export != "") {
			$exportparam 	= 	array(
				'filename'		=>	'ticket',
				'heading'		=>	'',
				'fields' 		=>	array(
					'ticket_id'			=>	'Ticket ID',
					'name'				=>	'Name',
					'email_id'			=>	'Email',
					'title'				=>	'Title',
					'status'			=>	'Status',
					'notification_sent'	=>	'Notification Sent' )
			);
            $callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $ticket, $export); 
        }
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('ticket')));
	}
	/***   Ticket List page end   ***/
	
	/*** My Ticket List Page start ***/
	public function manageMyticketApi()
	{
		$user = Auth::user()->id;
		$ticket = Ticket::with('get_assignee')->where('assigned',$user)->orderBy('id','DESC')->get();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('ticket')));
	}
	/*** My Ticket List Page end ***/
	
	/***   Ticket show page start   ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$user = Auth::user()->id;
		
		if(Ticket::where('id', $id )->count() > 0) {
			$ticket = Ticket::with('get_assignee','get_assignedby')->where('id',$id)->first();
			if($ticket->assigned == $user || $ticket->assigned == 0 ) {
				Ticket::where('id',$id)->update(['read' => '1']);	
			}
			
			$ticket_detail = TicketDetail::with('posted_user')->where('ticket_id',$ticket->ticket_id)->get();
			$get_assigneelist = AssignTicketHistory::with('get_assignedto','get_assignedby')
								->where('ticket_id',$ticket->ticket_id)->orderBy('id','DESC')->get();
			
			$ticket_id = Helpers::getEncodeAndDecodeOfId($ticket->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('ticket','ticket_id','ticket_detail','get_assigneelist')));	
		} else {
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/***   Ticket show page end   ***/
	
	/***   Ticket reply page start   ***/
	public function getEditApi($id){
            $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
            if(Ticket::where('id',$id)->count() && Request::ajax()) {
                $ticket = Ticket::find($id);
                if($ticket->status =="Open") {
                    $ticket_id = Helpers::getEncodeAndDecodeOfId($ticket->id,'encode');
                    return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('ticket','ticket_id','last_msg')));                    
                } else {
                    return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.ticket_closed_msg"),'data'=>null));                    
                }
            } else {
                return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));                
            }
	}
	/***   Ticket reply page end   ***/
	
	/***   Ticket Update page start   ***/
	public function getUpdateApi($id, $request='')
	{
		$ticket_id = Helpers::getEncodeAndDecodeOfId($id,'decode');  
		if($request == '')
		$request = Request::all();
		if(Ticket::where('id',$ticket_id)->count()) {			
			$validator = Validator::make($request, Ticket::$admin_rules, Ticket::messages());
			if ($validator->fails()) {	
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>null));	
			} else {	
				$filestoreName = '';
				$file_getmimetype = '';
				if ($request['attachment'])	{ 
					$image              = $request['attachment'];
					$filename           = rand(11111,99999);
					$extension          = $image->getClientOriginalExtension();
					$filestoreName      = $filename .'.'.$extension;
					$file_getmimetype 	= $image->getMimeType();
					Helpers::mediauploadpath('support','ticket',$image,'',$filestoreName); 
				}
				$user = Auth::user()->id;
				$ticket = Ticket::findOrFail($ticket_id);
				$ticket_id = $ticket->ticket_id; 
				$req = [];
				$req['attach_details'] = $filestoreName;
				$req['image_type'] = $file_getmimetype;
				$req['ticket_id']=$ticket_id;
				$req['description']=$request['description'];
				$req['posted_by']='Admin';
				$req['postedby_id']=$user;
				
				$tic_detail = TicketDetail::create($req);
			
				if($request['status'] =="Closed") {
					$ticket->status = $request['status'];
					$ticket->updated_by = $user;
					$ticket->save ();
				}
			
				// mail send to user
				if(EmailTemplate::where('template_for','admin_reply')->count()>0) {
					$templates = EmailTemplate::where('template_for','admin_reply')->first();
					$get_Email_Template = $templates->content;					
					$arr = [
						"##FIRSTNAME##" =>$ticket->name,
						"##TICKETID##" => $ticket_id,
						"##ADMINNAME##" => Auth::user()->name,
					]; 
					$email_content = strtr($get_Email_Template, $arr);	
					$res = array('email'=>	$ticket->email_id,
						'subject'	=>	$templates->subject,
						'msg'		=>	$email_content,
						'name'		=>	$ticket->name
					);	
					$msg_status = CommonApiController::connectEmailApi($res);
				}
				$ticket_detail = TicketDetail::with('posted_user')->where('ticket_id',$ticket->ticket_id)->get();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.mail_send_msg"),'data'=>compact('ticket','ticket_detail')));					
			}
		} else {
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/***   Ticket update page end   ***/
	
	/***   Ticket delete page start   ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Ticket::where('id',$id)->count()) {
			Ticket::Where('id',$id)->delete();
			TicketDetail::Where('ticket_id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		} else {
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/***   Ticket delete page end   ***/
	
	
	/***   Ticket Notification send page start   ***/
	public static function getNotificationSendApi($alertby,$alert_before,$status_before)
	{
		if(Ticket::Where('notification_sent',"No")->count()>0 )
		{
			if($alertby =="hour") {
				$alert_identify 	= 	'-'.$alert_before.' hours';
				$status_identify 	= 	'-'.$status_before.' hours';
			}	

			if($alertby =="day") {
				$alert_identify 	= 	'-'.$alert_before.' day';
				$status_identify 	= 	'-'.$status_before.' day';
			}				
			$intimate_date 		=  	date('Y-m-d', strtotime($alert_identify));
			$status_date 		=  	date('Y-m-d', strtotime($status_identify));
			if($intimate_date) {
				$ticket_id = Ticket::where("notification_sent","No")->where("status","Open")->orderBy("updated_at","ASC")->take(10)
							->pluck("ticket_id")->all();
				$ticket = TicketDetail::getLastTicket($ticket_id,$intimate_date);
			}

			if($status_date) {
				$ticket_id = Ticket::where("notification_sent","Yes")->where("status","Open")->where("updated_at","<=",$status_date)->orderBy("created_at","ASC")->take(10)->pluck("id")->all();
				$ticket = Ticket::whereIn('id',$ticket_id)->update(["status"=>"Closed"]);
			}
			return Response::json(array('status'=>'success', 'message'=>'','data'=>null));
		} else {
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/***   Ticket Notification send page end   ***/
	
	
	/*** Get medcubics user list start ***/
	public function getUserListApi($user_id='')
	{
		$userlist = Users::where('id','!=','1')->where('user_type','Medcubics');
		if($user_id!='') {
			$user_id = Helpers::getEncodeAndDecodeOfId($user_id,'decode');
			$userlist = $userlist->where('id','!=',$user_id);
		}
		
		$userlist	= $userlist->pluck('name','id')->all();
		$userlist 		= array_flip($userlist);  
		$userlist	 	= array_flip(array_map(array($this,'getEncodeDecode'),$userlist));
		return Response::json(array('status'=>'success','message'=>'','data'=>compact('userlist')));
	}
	/*** Get medcubics user list end ***/
	
	function getEncodeDecode($num)
	{
	  	return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}
	
	/*** Get assign ticket start ***/
	public function assignTicketApi($ticketid,$user_id)
	{
		$userid = Helpers::getEncodeAndDecodeOfId($user_id,'decode');	
		Ticket::where('ticket_id',$ticketid)->update(['read' => '0']);
		$user = Auth::user()->id;
		$currentdate = date('Y-m-d');
		Ticket::where('ticket_id',$ticketid)->update(['assigned' => $userid,'assignedby'=>$user,'assigneddate'=>$currentdate]);
		$req = [];
		$req['ticket_id'] = $ticketid;
		$req['assigned'] = $userid;
		$req['assigned_by']=$user;
		$tic_detail = AssignTicketHistory::create($req);
		
		$user_name = Users::where('id',$userid)->first()->name;
		
		$message = $user_name.'<br> <a data-url="'.url('admin/assignticket').'" data-userid="'.$user_id.'" data-ticketid="'.$ticketid.'" data-toggle="modal" class="js_ticketassign tooltips" data-target="#ticketassign_modal" href="">Reassign</a>';
		return Response::json(array('status'=>'success','message'=>'','data'=>compact('message')));
	}
	/*** Get assign ticket end ***/
	
	
}