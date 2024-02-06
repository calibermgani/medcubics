<?php 
namespace App\Http\Controllers\Profile\Api;
use Auth;
use Request;
use Response;
use Input;
use Config;
use Validator;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as User;
use App\Models\Profile\PrivateMessage;
use App\Models\Profile\PrivateMessageDetails;
use App\Models\Profile\PrivateMessageLabelList;
use App\Models\Profile\PrivateMessageSettings;
use App\Http\Helpers\Helpers as Helpers;

class MailboxApiController extends Controller 
{
    /*** Inbox list starts ***/
	public function getMaillistApi()
	{
		$date ='date';
		$order ='DESC';
		$message_inbox_list		    =  $this->getinboxValues($date,$order,"All"); 
		$message_inbox_list_count	=  count($message_inbox_list);		
		return Response::json(array('data'=>compact('message_inbox_list_count','message_inbox_list')));
    }
	/*** Inbox list ends ***/
	
	
	public function getotherLabelMaillistApi($label_id)
	{
		$label_id			= Helpers::getEncodeAndDecodeOfId($label_id,'decode');
		$date ='date';
		$order ='DESC';
		$user_id 		  	= 	Auth::user()->id;
		$currlabeldet 	  	= 	PrivateMessageLabelList::where('user_id',$user_id)->where('id',$label_id)->first();
		$mail_list_option 	= 	$currlabeldet['label_name'];
		$message_label_list		    =  $this->getlabelValues($label_id,$date,$order,"All"); 
		$message_label_list_count	=  count($message_label_list);
		return Response::json(array('data'=>compact('message_label_list_count','message_label_list','mail_list_option')));
    }
	
	/*** Send list starts ***/
	public function getSendMaillistApi()
	{
		$date ='date';
		$order ='DESC';
		$message_sent_list		    =  $this->getsentValues($date,$order,"send");
		$message_sent_list_count	=  count($message_sent_list);		
		return Response::json(array('data'=>compact('message_sent_list_count','message_sent_list')));
    }
	/*** Send list ends ***/
	
	/*** Draft list starts ***/
	public function getDraftMaillistApi()
	{
		$message_draft_list		    =  $this->getdraftValues(); 
		$message_draft_list_count	=  count($message_draft_list);		
		return Response::json(array('data'=>compact('message_draft_list_count','message_draft_list')));
	}
	/*** Draft list ends ***/
	
	/*** Trash list starts ***/
	public function getTrashMaillistApi()
	{
		$date ='date';
		$order ='DESC';
		$message_trash_list		    =  $this->gettrashValues($date,$order,"All"); 
		$message_trash_list_count	=  count($message_trash_list);		
		return Response::json(array('data'=>compact('message_trash_list_count','message_trash_list')));
    }
	/*** Trash list ends ***/
	
	/*** Inbox mail view start ***/
	public function getInboxMailViewDetApi($mail_id)
	{
		$user_id 		= Auth::user()->id;
		// Start Get current last conversation message details 
		$curr_msg_det = PrivateMessageDetails::with('PrivateMessage')->where('message_id',$mail_id)->Where('user_id',$user_id)->Where('recipient_deleted',0)->first()->toArray();
		if($curr_msg_det['message_id'] == $curr_msg_det['parent_message_id']) 	$parent_message_id = $curr_msg_det['message_id'];
		else	$parent_message_id = $curr_msg_det['parent_message_id'];
		// End Get current last conversation message details 
		
		// Start Update read view status 
		$read_time = date('Y-m-d H:i:s');
		PrivateMessageDetails::where('user_id',$user_id)->Where(function($query)use ($parent_message_id){ $query->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->update(['recipient_read' => '1','recipient_read_time' => $read_time]);
		// End Update read view status 
		
		$par_msg_det_arr = PrivateMessageDetails::with('PrivateMessage')->where(function($query)use ($user_id){ $query->whereRaw('send_user_id = ? and sender_deleted = "0"', array($user_id))->orwhereRaw('user_id = ? and recipient_deleted = "0"',array($user_id));})->where(function($query1)use ($parent_message_id){ $query1->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->orderBy('created_at','DESC')->groupBy('message_id')->get()->toArray();
		
		$view_msg_det = $this->getMailListview($par_msg_det_arr,$user_id); 
		$mail_subject = $curr_msg_det['private_message']['subject'];
		return Response::json(array('data'=>compact('mail_subject','view_msg_det')));
    }
	/*** Inbox mail view ends ***/
	
	/*** Draft mail view start ***/
	public function getdraftMailViewDetApi($mail_id)
	{
		$user_id 	  = Auth::user()->id;
		$curr_msg_det  		  = PrivateMessage::where('message_id',$mail_id)->where('send_user_id',$user_id)->get()->toArray();
		$mail_subject = $curr_msg_det[0]['subject'];
		
		$view_msg_det = $curr_msg_det;
		$from_add_arr = User::where('id',$curr_msg_det[0]['recipient_users_id'])->first();
		$draft_msg_det['mail_add_cont'] 	   		   = "To : ".$from_add_arr['email'];
		$draft_msg_det['mail_add_cont_header'] 	   	   = "From : ".Auth::user()->email;
		$draft_msg_det['sent_datetime_dis'] 		   = date('D m/d/Y H:i A', strtotime($curr_msg_det[0]['created_at']));
		$view_msg_det[] = array_merge($view_msg_det[0], $draft_msg_det);
		unset($view_msg_det[0]);
		return Response::json(array('data'=>compact('mail_subject','view_msg_det')));
	}
	/*** Draft mail view ends ***/
	
	/*** Send mail view start ***/
	public function getSendMailViewDetApi($mail_id)
	{
		$user_id 		= Auth::user()->id;
        $email 		= Auth::user()->email;
        $name 		= Auth::user()->name;
		
		// Start Get current last conversation message details 
		$curr_msg_det = PrivateMessageDetails::with('PrivateMessage')->where('message_id',$mail_id)->Where('send_user_id',$user_id)->Where('sender_deleted',0)->first()->toArray();
		if($curr_msg_det['message_id'] == $curr_msg_det['parent_message_id']) 	$parent_message_id = $curr_msg_det['message_id'];
		else	$parent_message_id = $curr_msg_det['parent_message_id'];
		// End Get current last conversation message details 
		
		// Start Update read view status 
		$read_time = date('Y-m-d H:i:s');
		PrivateMessageDetails::where('user_id',$user_id)->Where(function($query)use ($parent_message_id){ $query->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->update(['recipient_read' => '1','recipient_read_time' => $read_time]);
		// End Update read view status 
		
		$par_msg_det_arr = PrivateMessageDetails::with('PrivateMessage')->where(function($query)use ($user_id){ $query->whereRaw('send_user_id = ? and sender_deleted = "0"', array($user_id))->orwhereRaw('user_id = ? and recipient_deleted = "0"',array($user_id));})->where(function($query1)use ($parent_message_id){ $query1->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->orderBy('created_at','ASC')->groupBy('message_id')->get()->toArray();
		
		$view_msg_det = $this->getMailListview($par_msg_det_arr,$user_id);
		
		$mail_subject = $curr_msg_det['private_message']['subject'];
		$view_msg_det = array_reverse($view_msg_det);
		return Response::json(array('data'=>compact('mail_subject','view_msg_det')));
    }
	/*** Send mail view ends ***/
	
	/*** Trash mail view start ***/
	public function getTrashMailViewDetApi($mail_id)
	{
        $user_id 		= Auth::user()->id;
		// Start Get current last conversation message details 
		$curr_msg_det = PrivateMessageDetails::with('PrivateMessage')->where('message_id',$mail_id)->Where('user_id',$user_id)->Where('recipient_deleted',1)->orWhere('sender_deleted',1)->first()->toArray();
		if($curr_msg_det['message_id'] == $curr_msg_det['parent_message_id']) 	$parent_message_id = $curr_msg_det['message_id'];
		else	$parent_message_id = $curr_msg_det['parent_message_id'];
		// End Get current last conversation message details 
		
		$par_msg_det_arr = PrivateMessageDetails::with('PrivateMessage')->where('message_id',$mail_id)->where(function($query)use ($user_id){ $query->whereRaw('send_user_id = ? and sender_deleted = "1"', array($user_id))->orwhereRaw('user_id = ? and recipient_deleted = "1"',array($user_id));})->orderBy('created_at','ASC')->groupBy('message_id')->get()->toArray();
		
		$view_msg_det = $this->getMailListview($par_msg_det_arr,$user_id);
		
		$mail_subject = $curr_msg_det['private_message']['subject'];
		$view_msg_det = array_reverse($view_msg_det);

		return Response::json(array('data'=>compact('mail_subject','view_msg_det')));
    }
	
	/*** getMailList Start ***/
	public function getMailListview($par_msg_det_arr,$user_id)
	{
		if($par_msg_det_arr[0]['user_id'] == $user_id && $par_msg_det_arr[0]['recipient_deleted'] =="0" && $par_msg_det_arr[0]['label_list_type'] ==0) $page="recipient_category_id";
		elseif($par_msg_det_arr[0]['send_user_id'] == $user_id && $par_msg_det_arr[0]['sender_deleted'] =="0") $page="send_category_id";
		elseif($par_msg_det_arr[0]['user_id'] == $user_id && $par_msg_det_arr[0]['recipient_deleted'] =="0" && $par_msg_det_arr[0]['label_list_type'] !=0) $page="label_category_id";
		elseif($par_msg_det_arr[0]['user_id'] == $user_id || $par_msg_det_arr[0]['send_user_id'] == $user_id || $par_msg_det_arr[0]['recipient_deleted'] =="1" || $par_msg_det_arr[0]['sender_deleted'] =="1") $page="trash_category_id";
		$view_msg_det = $loop_msg_det = array();
		$email 		= Auth::user()->email;
        $name 		= Auth::user()->name;
		
		foreach($par_msg_det_arr as $kk=>$vv)
		{
			$loop_msg_det['message_id'] 		= $vv['private_message']['message_id'];
			$loop_msg_det['subject'] 			= $vv['private_message']['subject'];
			$loop_msg_det['from'] 				= $page;
			$category_id				 		= (isset($vv[$page]))? $vv[$page] : '';
			$labeldet = ($category_id !='')? PrivateMessageLabelList::where('user_id',$user_id)->where('id',$category_id)->first(): '';
			$loop_msg_det['category_id'] 		= $labeldet;
			$loop_msg_det['recipient_users_id'] = $vv['private_message']['recipient_users_id'];
			$loop_msg_det['send_user_id'] 		= $vv['private_message']['send_user_id'];
			$loop_msg_det['message_body'] 		= $vv['private_message']['message_body'];
			$loop_msg_det['attachment_file'] 	= $vv['private_message']['attachment_file'];
			$loop_msg_det['sent_datetime_dis'] 	= date('D m/d/Y H:i A', strtotime($vv['private_message']['created_at']));
			$loop_msg_det['reply_all_process']  = "no";
			$to_add_arr_emails 	= "";
			$to_add_arr_name 	= "";
			$to_add_arr 		= User::wherein('id',explode(",",$vv['private_message']['recipient_users_id']))->orderBy('name','ASC')->get()->toArray();
			foreach($to_add_arr as $to_add_arr_email)
			{
				if($to_add_arr_email['id']!= $user_id)	
				{
					$to_add_arr_emails .= "&nbsp;".$to_add_arr_email['email'].",";
					$to_add_arr_name .= "&nbsp;".$to_add_arr_email['name'].",";
				}
				else
				{
					$to_add_arr_emails .= $to_add_arr_email['email'].",";
					$to_add_arr_name .= $to_add_arr_email['name'].",";
				}
			}
			$to_add_arr_emails 	= trim($to_add_arr_emails,',');
			$to_add_arr_emails  = (strlen($to_add_arr_emails) > 53) ? substr($to_add_arr_emails,0,53).'...' : $to_add_arr_emails ; 
			$to_add_arr_name 	= trim($to_add_arr_name,',');
			$to_add_arr_name  	= (strlen($to_add_arr_name) > 53) ? substr($to_add_arr_name,0,53).'...' : $to_add_arr_name ; 
			if($vv['private_message']['send_user_id']==$user_id)
			{
				$loop_msg_det['mail_add_cont'] 	   		= "";
				$loop_msg_det['mail_add_cont_header'] 	= "To : $to_add_arr_emails";
			}
			else
			{
				if(count($to_add_arr)>1)	$loop_msg_det['reply_all_process']  = "yes";
				$from_add_arr = User::where('id',$vv['private_message']['send_user_id'])->first()->toArray();
				$loop_msg_det['mail_add_cont'] 	   		   = "To : $to_add_arr_emails";
				$loop_msg_det['mail_add_cont_header'] 	   = "From : ".$from_add_arr['email'];
			}
			
			array_push($view_msg_det, $loop_msg_det);
			unset($loop_msg_det);
		}
		return $view_msg_det;
	}
	/*** getMailList End ***/
	
	/*** Mail send process start ***/
	public function mailsendprocess()
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
			if($request['mail_sent_type']=="new" || $request['mail_sent_type']=="draft")
			{
				$parent_message_id = $ins_datas['message_id'];
			}
			elseif($request['mail_sent_type']=="reply")
			{
				$curr_msg_det 		= PrivateMessageDetails::where('message_id',$request['curr_mail_id'])->first()->toArray();
				$parent_message_id 	= $curr_msg_det['parent_message_id'];
				$check_stared	 	= "yes";
			}
			
			if(Input::hasFile('attachment_file'))
			{
				$file 		= Input::file('attachment_file');
				$filename 	= md5($user_id.strtotime(date('Y-m-d H:i:s')));
				$extension = $file->getClientOriginalExtension();
				$filestoreName = $filename .'.'.$extension;
				$path = 'media/private_message';
				$file->move($path, $filestoreName); 
				$ins_datas['attachment_file'] = $filestoreName;
				$ins_datas['draft_message'] = 0;
			}
			elseif(isset($request['curr_mail_id']))
			{
				$ins_datas['attachment_file'] = PrivateMessage::Where('message_id',$request['curr_mail_id'])->pluck("attachment_file")->first();
			}
			if($request['mail_sent_type']=="draft")
			{
				PrivateMessage::Where('message_id',$request['curr_mail_id'])->delete();
			}
			$result = PrivateMessage::create($ins_datas);
			//$url = "http://localhost/medcubics/profile/maillist/show/".$result->message_id;
			//$this->user_activity("maillist","add",$ins_datas['subject'],$url); //User activity
			
			$ins_datas_recep = array();
			foreach($to_address as $kk=>$vv)
			{
				$ins_datas_recep['message_id'] 			= $ins_datas['message_id'];
				$ins_datas_recep['parent_message_id'] 	= $parent_message_id;
				$ins_datas_recep['send_user_id'] 		= $user_id;
				$ins_datas_recep['user_id'] 			= $vv;
				$ins_datas_recep['sender_stared']		= 0;
				$ins_datas_recep['recipient_stared'] 	= 0;
				if($check_stared == "yes")
				{
					if(PrivateMessageDetails::Where(function($query)use ($vv){ $query->whereRaw('user_id = ? and recipient_stared = "1"', array($vv))->orwhereRaw('send_user_id = ? and sender_stared = "1"', array($vv));})->Where(function($query1)use ($parent_message_id){ $query1->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->count()){
						$ins_datas_recep['recipient_stared'] 	= 1;
					}
					
					if(PrivateMessageDetails::Where(function($query)use ($user_id){ $query->whereRaw('user_id = ? and recipient_stared = "1"', array($user_id))->orwhereRaw('send_user_id = ? and sender_stared = "1"', array($user_id));})->Where(function($query1)use ($parent_message_id){ $query1->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->count()){
						$ins_datas_recep['sender_stared'] 	= 1;
					}
				
				}
				
				$res = PrivateMessageDetails::create($ins_datas_recep);
				//$url = "http://localhost/medcubics/profile/maillist/show/".$res->message_id;
				//$this->user_activity("maillist","add",$ins_datas['subject'],$url); //User activity
				unset($ins_datas_recep);
			}
			$status = json_encode(array('status'=>'success'));
			return $status;
		}	
    }
	/*** Mail send process end ***/
	
	/*** Draft mail save process start ***/
	public function draftmailprocess()
	{
		$request 					= Request::all();	
		$user_id					= Auth::user()->id;
		$ins_datas  				= array();
		$ins_datas['message_id'] 	= md5("M".$user_id.strtotime(date('Y-m-d H:i:s')));
		$ins_datas['subject'] 		= $request['mail_subject'];
		$ins_datas['message_body'] 	= $request['compose-textarea'];
		$ins_datas['send_user_id'] 	= Auth::user()->id;
		if($request['to_address']!="")
		{
			$ins_datas['recipient_users_id'] 	= $request['to_mail_id'];
		}
		if(Input::hasFile('attachment_file'))
		{
			$file 		= Input::file('attachment_file');
			$filename 	= md5($user_id.strtotime(date('Y-m-d H:i:s')));
			$extension = $file->getClientOriginalExtension();
			$filestoreName = $filename .'.'.$extension;
			$path = 'media/private_message';
			$file->move($path, $filestoreName); 
			$ins_datas['attachment_file'] = $filestoreName;
		}
		$ins_datas['draft_message'] = "1";
		if($request['mail_sent_type']=="reply" || $request['mail_sent_type']=="new")
		{
			$result  			= PrivateMessage::create($ins_datas);
		}
		elseif($request['mail_sent_type']=="draft")
		{
			$update_msg_id 		= $request['curr_mail_id'];
			$recipient_users_id = "";
			if($request['to_address']!="")
			{
				$recipient_users_id = $ins_datas['recipient_users_id'];
			}
			PrivateMessage::where('message_id',$update_msg_id)->where('send_user_id',Auth::user()->id)->update(['subject' => $ins_datas['subject'],'message_body' => $ins_datas['message_body'],'attachment_file' => $ins_datas['attachment_file'],'recipient_users_id' => $recipient_users_id]);
		}
		$status = json_encode(array('status'=>'success'));
		return $status;
    }
	/*** Draft mail save process end ***/
	
	/*** Reply,reply all mail process start ***/
	public function replymailprocess($request='')
	{
		$user_id 	  = Auth::user()->id;
		$mail_id 	  = $request['mail_id'];
		
		$msg_det_arr  = PrivateMessage::where('message_id', '=', $mail_id)->where(function($query)use ($user_id){ $query->where('send_user_id', '=', $user_id)->orWhere('recipient_users_id', 'LIKE', $user_id)->orWhere('recipient_users_id', 'LIKE', $user_id.',%')->orWhere('recipient_users_id', 'LIKE', '%,'.$user_id)->orWhere('recipient_users_id', 'LIKE', '%,'.$user_id.',%');})->get()->toArray();
		$from_add_arr 		= User::where('id',$msg_det_arr[0]['send_user_id'])->first()->toArray();
		
		$get_split_toemail = explode(",",$msg_det_arr[0]['recipient_users_id']);	
		if(count($get_split_toemail)>0)
		{
			$get_to_email 	  = User::whereIn('id',$get_split_toemail)->pluck('email')->all();
			$to_email		  = implode(',',$get_to_email);
		}
		
		$request['reply_all_type'] = trim($request['reply_all_type']);
		if($request['reply_all_type']!= "Forward")
		{
			if($msg_det_arr[0]['send_user_id']==$user_id)
			{
				$reply_ids	= $msg_det_arr[0]['recipient_users_id'];
			}
			elseif($request['reply_all_type']=="Reply All")
			{
				$reply_ids	= $msg_det_arr[0]['send_user_id'].",".$msg_det_arr[0]['recipient_users_id'];
			}
			elseif($request['reply_all_type']=="Reply")
			{
				$reply_ids	= $msg_det_arr[0]['send_user_id'];
			}
		}
		else
		{
			$reply_ids	= '';
		}
		$reply_ids_arr = User::whereIn('id',explode(",",$reply_ids))->where('id','!=',$user_id)->orderBy('name','ASC')->pluck('id')->all();
		if(count($reply_ids_arr)>0 && count(PrivateMessageDetails::where('message_id',$mail_id )->get())>0 )
		{
			$chk_draft = "reply";
			$from_add_content 	= '<hr><strong>From : </strong>'.$from_add_arr['name']." [mailto:".$from_add_arr['email']."] "."<br>"."<strong>Sent : </strong>".date('D m/d/Y H:i A', strtotime($msg_det_arr[0]['created_at']))."<br>"."<strong>To : </strong>".$to_email;
		}
		else
		{
			if($request['reply_all_type']=="Forward")
			{
				$chk_draft = "new";
				$from_add_content 	= '<hr><p>--------------Forward-----------</p><strong>From : </strong>'.$from_add_arr['name']." [mailto:".$from_add_arr['email']."] "."<br>"."<strong>Sent : </strong>".date('D m/d/Y H:i A', strtotime($msg_det_arr[0]['created_at']))."<br>"."<strong>To : </strong>".$to_email;
			}
			else
			{
				$from_add_content 	= '';
				$chk_draft = "draft";
			}
			
		}
		$reply_exists_content = $from_add_content.'<br>'.$msg_det_arr[0]['message_body'];
		$attachment_file = $msg_det_arr[0]['attachment_file'];
		$subject = $msg_det_arr[0]['subject'];
		return Response::json(array('status'=>'success','data'=>compact('reply_exists_content','attachment_file','chk_draft', 'reply_ids_arr','subject')));	
	}
	/*** Reply,reply all mail process end ***/
	
	/*** New label name add process start ***/
	public function newmaillabeladd()
	{
		$request 	= Request::all();
		$request['user_id'] 	= Auth::user()->id;
		$label_name	= $request['label_name'];
		if(PrivateMessageLabelList::where('user_id', $request['user_id'])->where('label_name',$label_name)->count())
		{
			$status 	= "failure";
			$curr_label_id = "";
		}
		else
		{
			if(Input::hasFile('label_image'))
			{
				$file 		= Input::file('label_image');
				$filename 	= md5($request['user_id'].strtotime(date('Y-m-d H:i:s')));
				$extension = $file->getClientOriginalExtension();
				$filestoreName = $filename .'.'.$extension;
				$path = 'media/labelimage';
				$file->move($path, $filestoreName); 
				$request['label_image'] = $filestoreName;
			}
			$lastlabel = PrivateMessageLabelList::where('user_id',$request['user_id'])->orderBy('label_id','DESC')->pluck('label_id')->first();
			$label_id = ($lastlabel !='') ? $lastlabel+1 : 1;
			$request['label_id'] = $label_id;
			$result = PrivateMessageLabelList::create($request);
			$curr_label_id = $result->id;
			$status 	= "success";
		}
		return Response::json(array('status'=>$status,'curr_label_id'=>$curr_label_id));
	}
	/*** New label name add process end ***/
	
	/*** Selected mail read,unread, stared,unstared apply process start ***/
	public function msglist_applyprocess(){
		$request 		= Request::all();
		$apply_msg_type	= $request['apply_msg_type'];
		$sel_mail_ids	= explode(",",$request['sel_mail_ids']);
		$user_id 		= Auth::user()->id;
		$read_time 		= date('Y-m-d H:i:s');
		if($apply_msg_type == "mark_as_read")
		{
			foreach($sel_mail_ids as $sel_mail_ids_val)
			{
				$curr_mail_det = PrivateMessageDetails::where('message_id',$sel_mail_ids_val)->Where(function($query)use ($user_id){ $query->where('send_user_id',$user_id)->orWhere('user_id',$user_id);})->first();
				$parent_msg_id = $curr_mail_det['parent_message_id'];
				PrivateMessageDetails::where('user_id',$user_id)->where('recipient_read','0')->Where(function($query)use ($parent_msg_id){ $query->where('message_id', '=', $parent_msg_id)->orWhere('parent_message_id', '=', $parent_msg_id);})->update(['recipient_read' => '1','recipient_read_time' => $read_time]);
			}
		}
		elseif($apply_msg_type == "categorize")
		{			
			$label_id=($request['categorize_id'] !="remove_all") ? Helpers::getEncodeAndDecodeOfId($request['categorize_id'],'decode') : '';
			if($request['from_page'] =="inbox") 
			{
				$field = 'recipient_category_id';
				$value = $label_id;
				if($request['categorize_id'] !="remove_all")
				{
					foreach($sel_mail_ids as $sel_id_val)
					{
						PrivateMessageDetails::where('message_id',$sel_id_val)->orWhere('parent_message_id',$sel_id_val)->where("user_id",$user_id)->where("recipient_deleted","0")->where("label_list_type","0")->update([$field=>$value]);
					}
				}
				else
				{
					PrivateMessageDetails::where("user_id",$user_id)->where("recipient_deleted","0")->where("label_list_type","0")->update([$field=>"0"]);
				}
			}
			elseif($request['from_page'] =="label") 
			{
				$field = $request['from_page'].'_category_id';
				$value = $label_id;
				if($request['categorize_id'] !="remove_all")
				{
					foreach($sel_mail_ids as $sel_id_val)
					{
						PrivateMessageDetails::where('message_id',$sel_id_val)->orWhere('parent_message_id',$sel_id_val)->where("user_id",$user_id)->where("recipient_deleted","0")->where("label_list_type","!=","0")->update([$field=>$value]);
					}
				}
				else
				{
					PrivateMessageDetails::where("user_id",$user_id)->where("recipient_deleted","0")->where("label_list_type","!=","0")->update([$field=>"0"]);
				}
			}
			elseif($request['from_page'] =="send") 
			{
				$field = $request['from_page'].'_category_id';
				$value = $label_id;
				if($request['categorize_id'] !="remove_all")
				{
					foreach($sel_mail_ids as $sel_id_val)
					{
						PrivateMessageDetails::where('message_id',$sel_id_val)->orWhere('parent_message_id',$sel_id_val)->where("send_user_id",$user_id)->where("sender_deleted","0")->update([$field=>$value]);
					}
				}
				else
				{
					PrivateMessageDetails::where("send_user_id",$user_id)->where("sender_deleted","0")->update([$field=>"0"]);
				}
			}
			elseif($request['from_page'] =="trash") 
			{
				$field = $request['from_page'].'_category_id';
				$value = $label_id;
				if($request['categorize_id'] !="remove_all")
				{
					foreach($sel_mail_ids as $sel_id_val)
					{
						PrivateMessageDetails::where('message_id',$sel_id_val)->orWhere('parent_message_id',$sel_id_val)->where("user_id",$user_id)->orWhere("send_user_id",$user_id)->where("recipient_deleted","1")->orWhere("sender_deleted","1")->update([$field=>$value]);
					}
				}
				else
				{
					PrivateMessageDetails::where("user_id",$user_id)->orWhere("send_user_id",$user_id)->where("recipient_deleted","1")->orWhere("sender_deleted","1")->update([$field=>"0"]);
				}
			}
			return Response::json(array('status'=>'success','label_id'=>$label_id));
		}
		elseif($apply_msg_type == "mark_as_unread")
		{
			foreach($sel_mail_ids as $sel_mail_ids_val)
			{
				$curr_mail_det = PrivateMessageDetails::where('message_id',$sel_mail_ids_val)->Where(function($query)use ($user_id){ $query->where('send_user_id',$user_id)->orWhere('user_id',$user_id);})->first();
				$parent_msg_id = $curr_mail_det['parent_message_id'];
				PrivateMessageDetails::where('user_id',$user_id)->where('recipient_read','1')->Where(function($query)use ($parent_msg_id){ $query->where('message_id', '=', $parent_msg_id)->orWhere('parent_message_id', '=', $parent_msg_id);})->update(['recipient_read' => '0','recipient_read_time' => '0000-00-00 00:00:00']);
			}
		}
		elseif($apply_msg_type == "mark_as_stared" || $apply_msg_type == "mark_as_unstared")
		{
			if($apply_msg_type == "mark_as_stared")
			{
				$recipient_stared = 1;
			}
			elseif($apply_msg_type == "mark_as_unstared")
			{
				$recipient_stared = 0;
			}
			
			foreach($sel_mail_ids as $sel_mail_ids_val)
			{
				$curr_mail_det = PrivateMessageDetails::where('message_id',$sel_mail_ids_val)->Where(function($query)use ($user_id){ $query->where('send_user_id',$user_id)->orWhere('user_id',$user_id);})->first();
				$parent_msg_id = $curr_mail_det['parent_message_id'];
				
				PrivateMessageDetails::where('user_id',$user_id)->Where(function($query)use ($parent_msg_id){ $query->where('message_id', '=', $parent_msg_id)->orWhere('parent_message_id', '=', $parent_msg_id);})->update(['recipient_stared' => $recipient_stared]);
				
				PrivateMessageDetails::where('send_user_id',$user_id)->Where(function($query)use ($parent_msg_id){ $query->where('message_id', '=', $parent_msg_id)->orWhere('parent_message_id', '=', $parent_msg_id);})->update(['sender_stared' => $recipient_stared]);
			}
		}
		
		return Response::json(array('result'=>'success'));
	}
	/*** Selected mail read,unread, stared,unstared apply process end ***/
	
	/*** Mail move process start ***/
	public function msgmoveprocess()
	{
		$request['msg_to'] ='';
		
		$request 		= Request::all();
		
		$label_id		= $request['label_id'];
		$sel_mail_ids	= explode(",",$request['sel_mail_ids']);
		$user_id 		= Auth::user()->id;
		foreach($sel_mail_ids as $sel_mail_ids_val)
		{
			$curr_mail_det = PrivateMessageDetails::where('message_id',$sel_mail_ids_val)->Where(function($query)use ($user_id){ $query->where('send_user_id',$user_id)->orWhere('user_id',$user_id);})->first();
			
			$parent_msg_id = $curr_mail_det['parent_message_id'];
			if(isset($request['msg_from']))
			{
				$result=[];
				$res = PrivateMessageDetails::where('message_id',$parent_msg_id)->orWhere('parent_message_id',$parent_msg_id)->where('recipient_deleted',"!=",0)->get()->toArray();
				
				if($request['msg_to'] == "inbox"){
					$result["recipient_deleted"]=0;
					$result["recipient_deleted_time"]="0000:00:00 00:00:00";
					$result["label_list_type"]=0;
					$result["recipient_category_id"]=$res[0]['trash_category_id'];
					$result["label_category_id"]=0;
					$result["trash_category_id"]=0;
				}
				if($request['msg_to'] == "label"){
					$result["label_list_type"]=Helpers::getEncodeAndDecodeOfId($request['label_id'],'decode');
					$result["label_category_id"]=$res[0]['trash_category_id'];
					$result["recipient_category_id"]=0;
					$result["recipient_deleted"]=0;
					$result["trash_category_id"]=0;
					$result["recipient_deleted_time"]="0000:00:00 00:00:00";
				}
				$res = PrivateMessageDetails::where('message_id',$parent_msg_id)->orWhere('parent_message_id',$parent_msg_id)->where('recipient_deleted',"!=",0)->update($result);
			}
			else
			{
				$result=[];
				$res = PrivateMessageDetails::where('user_id',$user_id)->where(function($query)use ($parent_msg_id,$sel_mail_ids_val){ $query->where('message_id',$sel_mail_ids_val)->orWhere('parent_message_id',$parent_msg_id);})->get()->toArray();
				
				if(isset($request['from']))
				{
					$result["label_list_type"]=Helpers::getEncodeAndDecodeOfId($request['label_id'],'decode');
				}
				else
				{
					if($request['label_id'] !="0") 
					{
						$result["label_list_type"]=Helpers::getEncodeAndDecodeOfId($request['label_id'],'decode');
						$result["label_category_id"]=$res[0]['recipient_category_id'];
						$result["recipient_category_id"]=0;
					}
					else
					{
						$result["label_list_type"]=0;
						$result["recipient_category_id"]=$res[0]['label_category_id'];
						$result["label_category_id"]=0;
					}
				}
				
				$res = PrivateMessageDetails::where('user_id',$user_id)->where(function($query)use ($parent_msg_id,$sel_mail_ids_val){ $query->where('message_id',$sel_mail_ids_val)->orWhere('parent_message_id',$parent_msg_id);})->update($result);
				
			}
		}
		return Response::json(array('label_id'=>$label_id));
	}
	/*** Mail move process end ***/
	
	/*** Mail Delete process start ***/
	public function message_del_list()
	{
		$request 		= Request::all();
		$from			= $request['from'];
		$sel_mail_ids	= explode(",",$request['sel_mail_ids']);
		$user_id 		= Auth::user()->id;
		$deleted_time 	= date('Y-m-d H:i:s');
		
		if($from=="inboxlist" || $from=="labellist")
		{
			foreach($sel_mail_ids as $sel_mail_ids_val)
			{
				$curr_msg_det = PrivateMessageDetails::with('PrivateMessage')->whereRaw('message_id = ? and user_id = ? and recipient_deleted = "0" ', array($sel_mail_ids_val,$user_id))->first()->toArray();
				
				$parent_message_id = $curr_msg_det['parent_message_id'];
				
				
				if($from=="inboxlist")
				{
					$cat_id = (isset($curr_msg_det['recipient_category_id']))?$curr_msg_det['recipient_category_id']:'';
					PrivateMessageDetails::whereRaw('(message_id = ? or parent_message_id = ?) and (user_id = ? and recipient_deleted = "0")', array($parent_message_id,$parent_message_id,$user_id))->update(['recipient_deleted' => '1','recipient_deleted_time' => $deleted_time,'trash_category_id' => $cat_id]);
				}
				elseif($from=="labellist")
				{
					$cat_id = (isset($curr_msg_det['label_category_id']))?$curr_msg_det['label_category_id']:'';
					PrivateMessageDetails::whereRaw('(message_id = ? or parent_message_id = ?) and (user_id = ? and recipient_deleted = "0")', array($parent_message_id,$parent_message_id,$user_id))->where('label_list_type',"!=",0)->update(['recipient_deleted' => '1','recipient_deleted_time' => $deleted_time,'trash_category_id' => $cat_id]);
				}
				//$url = "http://localhost/medcubics/profile/maillist/show/".$parent_message_id;
				//$this->user_activity("maillist","delete",$curr_msg_det['private_message']['subject'],$url); //User activity
			}
		}
		elseif($from=="sentlist")
		{
			foreach($sel_mail_ids as $sel_mail_ids_val)
			{
				$curr_msg_det = PrivateMessageDetails::with('PrivateMessage')->whereRaw('message_id = ? and send_user_id = ? and sender_deleted = "0" ', array($sel_mail_ids_val,$user_id))->first()->toArray();
				
				$parent_message_id = (isset($curr_msg_det['parent_message_id']))?$curr_msg_det['parent_message_id']:'';
				$cat_id = (isset($curr_msg_det['send_category_id']))?$curr_msg_det['send_category_id']:'';
				
				PrivateMessageDetails::whereRaw('(message_id = ? or parent_message_id = ?) and send_user_id = ? and sender_deleted = "0" ', array($parent_message_id,$parent_message_id,$user_id))->update(['sender_deleted' => '1','sender_deleted_time' => $deleted_time,'trash_category_id' => $cat_id]);
				
				//$url = "http://localhost/medcubics/profile/maillist/show/".$parent_message_id;
				//$this->user_activity("maillist","delete",$curr_msg_det['private_message']['subject'],$url); //User activity
			
			}
		}
		elseif($from=="trashlist")
		{
			foreach($sel_mail_ids as $sel_mail_ids_val)
			{
				$curr_msg_det = PrivateMessageDetails::with('PrivateMessage')->whereRaw('message_id = ? and ( user_id = ? or send_user_id = ? )', array($sel_mail_ids_val,$user_id,$user_id))->first()->toArray();
		
				$parent_message_id = (isset($curr_msg_det['parent_message_id']))?$curr_msg_det['parent_message_id']:'';
		
				$remain_msg_det_arr = PrivateMessageDetails::whereRaw('(message_id = ? or parent_message_id = ?) and ((user_id = ? and recipient_deleted = "1") or (send_user_id = ? and sender_deleted = "1"))', array($parent_message_id,$parent_message_id,$user_id,$user_id))->get()->toArray();
				
				foreach($remain_msg_det_arr as $kk=>$vv)
				{
					if($vv['send_user_id']==$user_id)
					{
						PrivateMessageDetails::where('message_id',$vv['message_id'])->where('send_user_id',$user_id)->update(['sender_deleted' => '2','sender_deleted_time' => $deleted_time]);
						//$url = "http://localhost/medcubics/profile/maillist/show/".$parent_message_id;
						//$this->user_activity("maillist","delete",$curr_msg_det['private_message']['subject'],$url); //User activity
					}
					if($vv['user_id']==$user_id)
					{
						PrivateMessageDetails::where('message_id',$vv['message_id'])->where('user_id',$user_id)->update(['recipient_deleted' => '2','recipient_deleted_time' => $deleted_time]);
						//$url = "http://localhost/medcubics/profile/maillist/show/".$parent_message_id;
						//$this->user_activity("maillist","delete",$curr_msg_det['private_message']['subject'],$url); //User activity
					}
				}
			}
		}
		elseif($from=="draftlist")
		{
			foreach($sel_mail_ids as $sel_mail_ids_val)
			{
				PrivateMessage::Where('message_id',$sel_mail_ids_val)->Where('send_user_id',$user_id)->delete();
			}
		}
		
		return Response::json(array('deleted'=>'success'));
	}
	/*** Mail Delete process end ***/
	
	/*** Mail signature page start ***/
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
	/*** Mail signature page start ***/
	
	/*** Mail signature store process start ***/
	public function MailSettingsstoreApi()
	{
		$request 		= Request::all();
		$user_id 		= Auth::user()->id;
		if(PrivateMessageSettings::where('user_id', $user_id)->count())
		{
			PrivateMessageSettings::where('user_id',$user_id)->update(['signature' => $request['signature'],'signature_content' => $request['signature_content']]);
		}
		else
		{
			$request['user_id'] = $user_id;
			PrivateMessageSettings::create($request);
		}
		return Response::json(array('status'=>'success'));
	}
	/*** Mail signature store process start ***/
	
	/*** Individual mail stared function start ***/
	public function message_stared_list()
	{
		$request 		= Request::all();
		$user_id 		= Auth::user()->id;
		$star_msg_id	= $request['star_msg_id'];
		$from			= $request['from'];
		if($from == "recipient_stared")
		{
			$curr_msg_det = PrivateMessageDetails::with('PrivateMessage')->where('message_id',$star_msg_id)->Where('user_id',$user_id)->Where('recipient_deleted',0)->first()->toArray();
			$parent_message_id = $curr_msg_det['parent_message_id'];
			
			if($curr_msg_det['recipient_stared']=="0")
			{
				$recipient_stared = 1;
				$star_fill		  = "yes"; 
			}
			else
			{
				$recipient_stared = 0;
				$star_fill		  = "no";
			}
		
			PrivateMessageDetails::where('user_id',$user_id)->Where(function($query)use ($parent_message_id){ $query->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->update(['recipient_stared' => $recipient_stared]);
			
			PrivateMessageDetails::where('send_user_id',$user_id)->Where(function($query)use ($parent_message_id){ $query->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->update(['sender_stared' => $recipient_stared]);
		
		}
		
		elseif($from == "sender_stared")
		{
			$curr_msg_det = PrivateMessageDetails::with('PrivateMessage')->where('message_id',$star_msg_id)->Where('send_user_id',$user_id)->Where('sender_deleted',0)->first()->toArray();
			$parent_message_id = $curr_msg_det['parent_message_id'];
			
			if($curr_msg_det['sender_stared']=="0")
			{
				$sender_stared = 1;
				$star_fill		  = "yes"; 
			}
			else
			{
				$sender_stared = 0;
				$star_fill		  = "no";
			}
		
			PrivateMessageDetails::where('send_user_id',$user_id)->Where(function($query)use ($parent_message_id){ $query->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->update(['sender_stared' => $sender_stared]);
			
			PrivateMessageDetails::where('user_id',$user_id)->Where(function($query)use ($parent_message_id){ $query->where('message_id', '=', $parent_message_id)->orWhere('parent_message_id', '=', $parent_message_id);})->update(['recipient_stared' => $sender_stared]);
		
		}
		
		return Response::json(array('status'=>'success','star_fill'=>$star_fill));
	}
	/*** Individual mail stared function end ***/
	
	/*** Add space for subject start ***/
	public function addextraspace($str,$length)
	{
		if(strlen($str)<$length)
		{
			$spc_length = $length - strlen($str);
			$str = $str.str_repeat("&nbsp;",$spc_length); 
		}
		return $str;
	}
	/*** Add space for subject end ***/
	
	/*** Show page view start ***/
	public function getShowmailApi($mail_id)
	{
		$user_id		= Auth::user()->id; 
		
			$message_id		= $mail_id;
		if(PrivateMessageDetails::where('parent_message_id',$mail_id)->count()>0)
			$message_id		= PrivateMessageDetails::where('parent_message_id',$mail_id)->orderBy('id', 'DESC')->pluck('message_id')->first();  
		 
		$msg_details  	= PrivateMessage::where('message_id', '=', $message_id)->where(function($query)use ($user_id){ 
								$query->where('send_user_id', '=', $user_id)
								->orWhere('recipient_users_id', 'LIKE', $user_id)
								->orWhere('recipient_users_id', 'LIKE', $user_id.',%')
								->orWhere('recipient_users_id', 'LIKE', '%,'.$user_id)
								->orWhere('recipient_users_id', 'LIKE', '%,'.$user_id.',%');
							})->get()->toArray(); 
		
		$label_id = PrivateMessageDetails::where('message_id',$message_id)->get()->toArray();
		$category_id	= '';
		if(count($label_id)>0)
		{
			$msg_details[0]['label_id'] = $label_id[0]['label_list_type'];
			$msg_details[0]['showed_from']='';
			$msg_details[0]['moved_to']='';
			if($user_id==$label_id[0]['user_id'] && $label_id[0]['label_list_type'] ==0 && $label_id[0]['recipient_deleted'] == 0)
			{
				$msg_details[0]['showed_from']="inbox";
				$category_id	 = 	$label_id[0]['recipient_category_id'];
			}				
			elseif($user_id==$label_id[0]['user_id'] && $label_id[0]['label_list_type'] !=0 && $label_id[0]['recipient_deleted'] == 0)
			{
				$msg_details[0]['showed_from']="label";
				$category_id 	= 	$label_id[0]['label_category_id'];
			}
			elseif($user_id == $label_id[0]['send_user_id'] && $label_id[0]['sender_deleted'] == 0) 
			{
				$msg_details[0]['showed_from']="send";
				$category_id 	= 	$label_id[0]['send_category_id'];
			}
			elseif($user_id == $label_id[0]['user_id'] && $label_id[0]['recipient_deleted'] != 0) 
			{
				$msg_details[0]['showed_from']="trash";
				$msg_details[0]['moved_to']="inbox";
				$category_id 	= 	$label_id[0]['trash_category_id'];
			}
			$labeldet = ($category_id !='')? PrivateMessageLabelList::where('user_id',$user_id)->where('id',$category_id)->first(): '';
			$msg_details[0]['category_id']		= $labeldet;
		}
		$msg_details[0]['sent_time'] = date('D m/d/Y H:i A', strtotime($msg_details[0]['created_at']));
		$from_detail		= User::whereIn('id',explode(",",$msg_details[0]['recipient_users_id']))->pluck('email')->all();
		$from_details		= implode(",",$from_detail);
		
		$to_details 	= User::where('id',$msg_details[0]['send_user_id'])->first();
		
		return Response::json(array('status'=>'success','data'=>compact('from_details','to_details','msg_details')));	
	}
	/*** Show page view end ***/
	
	/*** Keyword search start ***/
	public function getKeywordsearchApi($request='')
	{
		$request 	  	= Request::all();
		$page 	  		= $request['from_access'];
		$getorder 	  	= $request['getorder'];
		$functionname =	"get".$page."Values";
		if(isset($request['label_id']))
		{
			$message_inbox_list		=  	$this->$functionname($request['label_id'],$getorder,$request['order_by'],$request['status_read']);
		}
		else
		{
			if($page =="inbox" || $page =="trash")
				$message_inbox_list		=  $this->$functionname($getorder,$request['order_by'],$request['status_read']);
			else
				$message_inbox_list		=  $this->$functionname($getorder,$request['order_by'],"All");
		}
		if($request['search_keyword'] !='')
		{
			$message_details=$message_detail_arr=[];
			foreach($message_inbox_list as $val)
			{
				$val['keyword_check'] = "no";
				foreach($val as $key_detail=>$val_detail)
				{
					if(!is_array($val_detail))
					{
						if(preg_match('/'.$request['search_keyword'].'/', $val_detail, $matches))
						{
							$val['keyword_check'] = "yes";
						}
					}
				}
				if($val['keyword_check'] =="yes")
				{
					$message_details[] = $val;
				}
			}
		}
		else
		{
			$message_details =$message_inbox_list;
		}
		
		$message_details_count		=  count($message_details); 
		return Response::json(array('status'=>'success','data'=>compact('getorder','message_details','message_details_count','page')));	
	}
	/*** Keyword search end ***/
	
	/*** Keyword filter start ***/
	public function getKeywordfilterApi($request='')
	{
		$request 	  	= Request::all();
		$page 	  	= $request['from_access'];
		$functionname =	"get".$page."Values";
		if(isset($request['label_id']))
		{
			$message_details		=  	$this->$functionname($request['label_id'],$request['search_keyword'],$request['order'],$request['status_read']);
			
		}
		else
		{
			if($page =="inbox" || $page =="trash")
				$message_details		=  $this->$functionname($request['search_keyword'],$request['order'],$request['status_read']);
			else
				$message_details		=  $this->$functionname($request['search_keyword'],$request['order'],"All");
		}
		
		$message_details_count		=  count($message_details); 
		$getorder		=  $request['search_keyword']; 
		return Response::json(array('status'=>'success','data'=>compact('getorder','message_details','message_details_count','page')));	
	}
	/*** Keyword filter end ***/
	
	/*** Sorting option start ***/
	public function getUnreadmailApi($request='')
	{
		$page 	  		= 	$request['page'];
		$functionname	=	"get".$page."Values";
		if(isset($request['label_id']) && $request['label_id'] !="undefined")
		{
			$message_details		=  	$this->$functionname($request['label_id'],$request['status_read'],$request['getorder'],$request['order_by']);
		}
		else 
		{
			if($page =="inbox" || $page =="trash")
				$message_details		=  $this->$functionname($request['getorder'],$request['order_by'],$request['status_read']);
			else
				$message_details		=  $this->$functionname($request['getorder'],$request['order_by'],"All");
		}
		$message_details_count		=  count($message_details); 
		$getorder		=  $request['getorder']; 
		return Response::json(array('status'=>'success','data'=>compact('getorder','message_details','message_details_count','page')));	
    }
	/*** Sorting option end ***/
	
	/*** Inbox mail details get function start ***/
	public function getinboxValues($date,$order,$read_status)
	{
		$user_id 		= Auth::user()->id;
		if($read_status=="Unread") $read = 0;
		elseif($read_status=="Read") $read = 1;
		if($read_status=="Unread" || $read_status=="Read")
			$message_inbox_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->whereRaw('user_id = ? and recipient_deleted = ? and label_list_type = "0" and recipient_read = ?', array($user_id,'0',$read))->orderBy('created_at',$order)->groupBy('parent_message_id')->get()->toArray();
		elseif($read_status=="All")
			$message_inbox_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->whereRaw('user_id = ? and recipient_deleted = ? and label_list_type = "0"', array($user_id,'0'))->orderBy('created_at',$order)->groupBy('parent_message_id')->get()->toArray();
			
		$message_inbox_list_count	 = 	count($message_inbox_list_arr);
		$name_val = ''; 
		$message_inbox_list = $message_inbox_list_val = array(); 
		if($message_inbox_list_count > 0)
		{
			foreach($message_inbox_list_arr as $k=>$v)
			{
				$message_inbox_list_val						= $v;
				$message_inbox_list_val['subject'] 	  	  	= $v['private_message']['subject']; 
				$category_id				 				= (isset($v['recipient_category_id']))? $v['recipient_category_id'] : '';
				$labeldet = ($category_id !='')? PrivateMessageLabelList::where('user_id',$user_id)->where('id',$category_id)->first() : '';
				
				if($labeldet!='')
					$labeldet = json_decode(json_encode($labeldet), True);
				
				$message_inbox_list_val['category_id'] 		= $labeldet;
				$message_inbox_list_val['attachment_file']	= $v['private_message']['attachment_file']; 
				$message_inbox_list_val['from_add_email'] 	= $this->addextraspace($v['user']['email'],35);
				$messagecontent_list = strip_tags($v['private_message']['message_body']);
				$messagecontent_list = (strlen($messagecontent_list) > 23) ? substr($messagecontent_list,0,20).'...' : $messagecontent_list;
				$message_inbox_list_val['messagecontent_list'] 	= $this->addextraspace($messagecontent_list,45);
				$time_res_arr  =  $this->getTimeAgoDiff(date('Y-m-d H:i:s'),$v['private_message']['created_at']); 
				if($date =="date")
					$message_inbox_list_val['messagetimeago'] 	= $time_res_arr['time_ago'];
				elseif($date =="from")
				{
					$name_val[$k]								= $message_inbox_list_val['user']['email'];
					$message_inbox_list_val['messagetimeago'] 	= $message_inbox_list_val['user']['name'];
				}					
				elseif($date =="to")
				{
					$name_val[$k]						 		= 	Auth::user()->email;
					$message_inbox_list_val['messagetimeago'] 	= 	Auth::user()->name;
				}
				elseif($date =="subject")
				{
					$name_val[$k]						 		= 	$v['private_message']['subject'];
					$message_inbox_list_val['messagetimeago'] 	= 	$v['private_message']['subject'];
				}
				elseif($date =="categorize")
				{
					if($labeldet =='') 
					{
						$name_val[$k]						 		= 	"Uncategorise mail";
						$message_inbox_list_val['messagetimeago'] 	= 	"Uncategorise mail";
					} 
					else
					{
						$name_val[$k]						 		= 	$labeldet['label_name'];
						$message_inbox_list_val['messagetimeago'] 	= 	$labeldet['label_name'];
					}
				}
				
				$message_inbox_list_val['received_time'] 	= $time_res_arr['received_date_sf'];
				array_push($message_inbox_list, $message_inbox_list_val);
				unset($message_inbox_list_val);
			}
		}
		if(isset($name_val))
		{
			if($date !="date" && count($name_val)>1)
			{
				if($date !="subject") asort($name_val);
				foreach ($name_val as $key => $val) 
				{
					$message_inbox_list_geting[] =$message_inbox_list[$key];
				}
				$message_inbox_list = $message_inbox_list_geting;
			}
		}
		return $message_inbox_list;
	}
	/*** Inbox mail details get function end ***/
	
	/*** Send mail details get function start ***/
	public function getsentValues($date,$order,$read_status)
	{
		$user_id 		= Auth::user()->id;
		$sent_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->whereRaw('send_user_id = ? and sender_deleted = ?', array($user_id,'0'))->orderBy('id',$order)->get()->toArray();
		$message_sent_list_arr=[];
		$sorting_arr=[];
		foreach($sent_list_arr as $kr=>$vr)
		{
			$sorting_arr[$vr['id']]=$vr['parent_message_id'];
		}
		$sorting_arr = array_unique($sorting_arr);
		
		if($read_status=="send")
		{
			
			foreach($sorting_arr as $key=>$value)
			{
				$list_arr = PrivateMessageDetails::with('user','PrivateMessage')->where('id',$key)->get()->toArray();
				$message_sent_list_arr[] = $list_arr[0];
			}
		}
		else
		{
			foreach($sorting_arr as $key=>$value)
			{
				$list_arr = PrivateMessageDetails::with('user','PrivateMessage')->where('parent_message_id',$value)->where('send_user_id',$user_id)->get()->toArray();
				foreach($list_arr as $list_key=>$list_value)
				{
					$combine_subject[] = $list_value["private_message"]["message_body"];
				}
				$combine_subject_merge[] = implode('',$combine_subject);
			}
			$message_sent_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->whereRaw('send_user_id = ? and sender_deleted = ?', array($user_id,'0'))->orderBy('id',$order)->groupBy('parent_message_id')->get()->toArray();
			foreach($message_sent_list_arr as $final_key_arr=>$list_value_arr)
			{
				$message_sent_list_arr[$final_key_arr]["private_message"]["message_body"]=$combine_subject_merge[$final_key_arr];
			}
		}
		$message_sent_list_count  = count($message_sent_list_arr);
		$message_sent_list = $message_sent_list_val = array();
		if($message_sent_list_count > 0)
		{
			foreach($message_sent_list_arr as $k=>$v)
			{
				$message_sent_list_val						= $v;
				$message_sent_list_val['message_id'] 	  	= $v['message_id']; 
				$category_id				 				= (isset($v['send_category_id']))? $v['send_category_id'] : '';
				$labeldet = ($category_id !='')? PrivateMessageLabelList::where('user_id',$user_id)->where('id',$category_id)->first(): '';
				$message_sent_list_val['category_id'] 		= ($labeldet !='') ? $labeldet->toArray():[];
				$message_sent_list_val['subject'] 	  	  	= $v['private_message']['subject']; 
				$message_sent_list_val['attachment_file']	= $v['private_message']['attachment_file']; 
				
				$to_mail_id 		= User::select('email','name')->where('id',$v['user_id'])->get()->toArray();
				$to_add_arr = User::whereIn('id',explode(",",$v['private_message']['recipient_users_id']))->orderBy('name','ASC')->pluck('email','name')->all();
				$to_add_arr_emails 	= "";
				$to_add_arr_emails  =  implode(",", $to_add_arr);
				$to_add_arr_emails	= (strlen($to_add_arr_emails) > 23) ? substr($to_add_arr_emails,0,20).'...' : $to_add_arr_emails;
				$message_sent_list_val['to_add_arr_emails'] = $this->addextraspace($to_add_arr_emails,35);
				
				$messagecontent_list = strip_tags($v['private_message']['message_body']);
				$message_sent_list_val['messagecontent'] 	= $messagecontent_list;
				$messagecontent_list = (strlen($messagecontent_list) > 23) ? substr($messagecontent_list,0,20).'...' : $messagecontent_list;
				$message_sent_list_val['messagecontent_list'] 	= $this->addextraspace($messagecontent_list,45);
				
				$time_res_arr  =  $this->getTimeAgoDiff(date('Y-m-d H:i:s'),$v['private_message']['created_at']); 
				
				if($date =="date")
					$message_sent_list_val['messagetimeago'] 	= $time_res_arr['time_ago'];
				elseif($date =="from")
				{
					$name_val[$k]						 		= 	Auth::user()->email;
					$message_sent_list_val['messagetimeago'] 	= 	Auth::user()->name;
				}					
				elseif($date =="to")
				{
					$name_val[$k]						 		= 	$to_mail_id[0]['email'];
					$message_sent_list_val['messagetimeago'] 	= 	$to_mail_id[0]['name'];
				}
				elseif($date =="subject")
				{
					$name_val[$k]						 		= 	$v['private_message']['subject'];
					$message_sent_list_val['messagetimeago'] 	= 	$v['private_message']['subject'];
				}
				elseif($date =="categorize")
				{
					if($labeldet =='') 
					{
						$name_val[$k]						 		= 	"Uncategorise mail";
						$message_sent_list_val['messagetimeago'] 	= 	"Uncategorise mail";
					} 
					else
					{
						$name_val[$k]						 		= 	$labeldet['label_name'];
						$message_sent_list_val['messagetimeago'] 	= 	$labeldet['label_name'];
					}
				}
				$message_sent_list_val['received_time'] 	= $time_res_arr['received_date_sf'];
				array_push($message_sent_list, $message_sent_list_val);
				unset($message_sent_list_val);
			}
		}
		if(isset($name_val))
		{
			if($date !="date" && count($name_val)>1)
			{
				if($date !="subject") asort($name_val);
				foreach ($name_val as $key => $val) 
				{
					$message_inbox_list_geting[] =$message_sent_list[$key];
				}
				$message_sent_list = $message_inbox_list_geting;
			}
		}
		
		return $message_sent_list;
	}
	/*** Send mail details get function end ***/
	
	/*** Label mail details get function start ***/
	public function getlabelValues($label_id,$date,$order,$read_status)
	{
		$user_id 		= Auth::user()->id;
		if($read_status=="Unread") $read = 0;
		elseif($read_status=="Read") $read = 1;
		if($read_status=="Unread" || $read_status=="Read")
			$message_label_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->whereRaw('user_id = ? and recipient_deleted = ? and label_list_type = ? and recipient_read = ?', array($user_id,'0',$label_id,$read))->orderBy('created_at',$order)->groupBy('parent_message_id')->get()->toArray();
		elseif($read_status=="All")
			$message_label_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->whereRaw('user_id = ? and recipient_deleted = ? and label_list_type = ?', array($user_id,'0',$label_id))->orderBy('created_at',$order)->groupBy('parent_message_id')->get()->toArray();
			
		$message_label_list_count = count($message_label_list_arr);
		$message_label_list = $message_label_list_val = array(); 
		if($message_label_list_count > 0)
		{
			foreach($message_label_list_arr as $k=>$v)
			{
				$message_label_list_val						= $v;
				$message_label_list_val['message_id'] 	  	= $v['message_id'];
				$category_id				 				= $v['label_category_id'];
				
				$labeldet = ($category_id !='')? PrivateMessageLabelList::where('user_id',$user_id)->where('id',$category_id)->first():'';
				
				if($labeldet != '')
					$labeldet = json_decode(json_encode($labeldet), True);
				
				$message_label_list_val['category_id'] 		= $labeldet;
				$message_label_list_val['subject'] 	  	  	= $v['private_message']['subject']; 
				$message_label_list_val['attachment_file']	= $v['private_message']['attachment_file']; 
				$message_label_list_val['from_add_email'] 	= $this->addextraspace($v['user']['email'],35);
				$messagecontent_list = strip_tags($v['private_message']['message_body']);
				$messagecontent_list = (strlen($messagecontent_list) > 23) ? substr($messagecontent_list,0,20).'...' : $messagecontent_list;
				$message_label_list_val['messagecontent_list'] 	= $this->addextraspace($messagecontent_list,45);
				$time_res_arr  =  $this->getTimeAgoDiff(date('Y-m-d H:i:s'),$v['private_message']['created_at']); 
				if($date =="date")
					$message_label_list_val['messagetimeago'] 	= $time_res_arr['time_ago'];
				elseif($date =="from")
				{
					$name_val[$k]								= $message_label_list_val['user']['email'];
					$message_label_list_val['messagetimeago'] 	= $message_label_list_val['user']['name'];
				}					
				elseif($date =="to")
				{
					$name_val[$k]						 		= 	Auth::user()->email;
					$message_label_list_val['messagetimeago'] 	= 	Auth::user()->name;
				}
				elseif($date =="subject")
				{
					$name_val[$k]						 		= 	$v['private_message']['subject'];
					$message_label_list_val['messagetimeago'] 	= 	$v['private_message']['subject'];
				}
				elseif($date =="categorize")
				{
					if($labeldet =='') 
					{
						$name_val[$k]						 		= 	"Uncategorise mail";
						$message_label_list_val['messagetimeago'] 	= 	"Uncategorise mail";
					} 
					else
					{
						$name_val[$k]						 		= 	$labeldet['label_name'];
						$message_label_list_val['messagetimeago'] 	= 	$labeldet['label_name'];
					}
				}
				$message_label_list_val['received_time'] 	= $time_res_arr['received_date_sf'];
				array_push($message_label_list, $message_label_list_val);
				unset($message_label_list_val);
			}
		}
		if(isset($name_val))
		{
			if($date !="date" && count($name_val)>1)
			{
				if($date !="subject") asort($name_val);
				foreach ($name_val as $key => $val) 
				{
					$message_inbox_list_geting[] =$message_label_list[$key];
				}
				$message_label_list = $message_inbox_list_geting;
			}
		}
		return $message_label_list;
	}
	/*** Label mail details get function end ***/
	
	/*** Draft mail details get function start ***/
	public function getdraftValues()
	{
		$user_id 		= Auth::user()->id;
		$message_draft_list_arr   = PrivateMessage::where('send_user_id',$user_id)->Where('draft_message',"1")->orderBy('created_at','DESC')->get()->toArray();
		$message_draft_list_count = count($message_draft_list_arr);
		$message_draft_list = $message_draft_list_val = array();
		if($message_draft_list_count > 0)
		{
			foreach($message_draft_list_arr as $kk=>$vv)
			{
				$message_draft_list_val['message_id'] = $vv['message_id'];
				$message_draft_list_val['attachment_file']	= $vv['attachment_file']; 
				if($vv['recipient_users_id']!="")
				{
					$to_add_arr 		= User::wherein('id',explode(",",$vv['recipient_users_id']))->orderBy('name','ASC')->pluck('email','name')->all();
					$to_add_arr_emails 	= "";
					$to_add_arr_emails 	= implode(",",$to_add_arr);
					$to_add_arr_emails	= (strlen($to_add_arr_emails) > 23) ? substr($to_add_arr_emails,0,20).'...' : $to_add_arr_emails;
					$message_draft_list_val['to_add_arr_emails'] 	= $this->addextraspace($to_add_arr_emails,23);
				}
				else
				{
					$message_draft_list_val['to_add_arr_emails'] = $this->addextraspace("no recipient",23);
				}
				
				if($vv['subject']!="")	$message_draft_list_val['subject'] = $vv['subject'];
				else					$message_draft_list_val['subject'] = "no subject";
				
				if($vv['message_body']!="")
				{
					$messagecontent_list = strip_tags($vv['message_body']);
					$messagecontent_list = (strlen($messagecontent_list) > 23) ? substr($messagecontent_list,0,20).'...' : $messagecontent_list;
					$message_draft_list_val['message_body'] 	= $this->addextraspace($messagecontent_list,45);
				}
				else
				{
					$message_draft_list_val['message_body'] = "no content";
				}
				$time_res_arr  =  $this->getTimeAgoDiff(date('Y-m-d H:i:s'),$vv['created_at']); 
				$message_draft_list_val['messagetimeago'] 	= $time_res_arr['time_ago'];
				array_push($message_draft_list, $message_draft_list_val);
				unset($message_draft_list_val);
			}
		}
		return $message_draft_list;
	}
	/*** Draft mail details get function end ***/
	
	/*** Trash mail details get function start ***/
	public function gettrashValues($date,$order,$read_status)
	{
		$user_id 		= Auth::user()->id;
		if($read_status=="Unread") $read = 0;
		elseif($read_status=="Read") $read = 1;
		
		if($read_status=="Unread" || $read_status=="Read")
			$message_trash_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->whereRaw('user_id = ? and recipient_deleted = "1" and recipient_read = ? ', array($user_id,$read))->orderBy('recipient_deleted_time',$order)->groupBy('parent_message_id')->get()->toArray();
		elseif($read_status=="All")
			$message_trash_list_arr = PrivateMessageDetails::with('user','PrivateMessage')->whereRaw('send_user_id = ? and sender_deleted = "1"', array($user_id))->orwhereRaw('user_id = ? and recipient_deleted = "1"', array($user_id))->orderBy('recipient_deleted_time',$order)->groupBy('parent_message_id')->get()->toArray();
		
		$message_trash_list_count = count($message_trash_list_arr);
		$message_trash_list = $message_trash_list_val = array(); 
		if($message_trash_list_count > 0)
		{
			foreach($message_trash_list_arr as $k=>$v)
			{
				$message_trash_list_val						= $v;
				$message_trash_list_val['message_id'] 	  	= $v['message_id'];
				$category_id						 		= $v['trash_category_id']; 
				
				$labeldetarr = ($category_id !='')? PrivateMessageLabelList::where('user_id',$user_id)->where('id',$category_id)->first(): '';
				$labeldet = json_decode(json_encode($labeldetarr), True);
				
				$message_trash_list_val['category_id'] 		= $labeldet;
				$message_trash_list_val['subject'] 	  	  	= $v['private_message']['subject']; 
				$message_trash_list_val['attachment_file']	= $v['private_message']['attachment_file']; 
				$message_trash_list_val['from_add_email'] 	= $this->addextraspace($v['user']['email'],35);
				$messagecontent_list = strip_tags($v['private_message']['message_body']);
				$messagecontent_list = (strlen($messagecontent_list) > 23) ? substr($messagecontent_list,0,20).'...' : $messagecontent_list;
				$message_trash_list_val['messagecontent_list'] 	= $this->addextraspace($messagecontent_list,45);
				$delete_from =($v['recipient_deleted'] ==1) ? $v['recipient_deleted_time']:$v['sender_deleted_time'];
				$time_res_arr  =  $this->getTimeAgoDiff(date('Y-m-d H:i:s'),$delete_from); 
				if($date =="date")
					$message_trash_list_val['messagetimeago'] 	= $time_res_arr['time_ago'];
				elseif($date =="from")
				{
					$name_val[$k]								= $message_trash_list_val['user']['email'];
					$message_trash_list_val['messagetimeago'] 	= $message_trash_list_val['user']['name'];
				}					
				elseif($date =="to")
				{
					$name_val[$k]						 		= 	Auth::user()->email;
					$message_trash_list_val['messagetimeago'] 	= 	Auth::user()->name;
				}
				elseif($date =="subject")
				{
					$name_val[$k]						 		= 	$v['private_message']['subject'];
					$message_trash_list_val['messagetimeago'] 	= 	$v['private_message']['subject'];
				}
				elseif($date =="categorize")
				{
					if($labeldet =='') 
					{
						$name_val[$k]						 		= 	"Uncategorise mail";
						$message_trash_list_val['messagetimeago'] 	= 	"Uncategorise mail";
					} 
					else
					{
						$name_val[$k]						 		= 	$labeldet['label_name'];
						$message_trash_list_val['messagetimeago'] 	= 	$labeldet['label_name'];
					}
				}
				$message_trash_list_val['received_time'] 	= $time_res_arr['received_date_sf'];
				array_push($message_trash_list, $message_trash_list_val);
				unset($message_trash_list_val);
			}
		}
		if(isset($name_val))
		{
			if($date !="date" && count($name_val)>1)
			{
				if($date !="subject") asort($name_val);
				foreach ($name_val as $key => $val) 
				{
					$message_inbox_list_geting[] =$message_trash_list[$key];
				}
				$message_trash_list = $message_inbox_list_geting;
			}
		}
		return $message_trash_list;
	}
	/*** Trash mail details get function end ***/
	
	/*** Time calculation function start ***/
	public static function getTimeAgoDiff($starttime,$endtime)
	{
		$endtime = date("Y-m-d",strtotime($endtime));
		$starttime = date("Y-m-d",strtotime($starttime));
		$result	 = array();
		
		$seconds = strtotime($starttime) - strtotime($endtime);
		$days_count    = (int)floor($seconds / 86400);
				
		$day_of_week = date('N'); //today on weeek
		
		$result['received_date_sf'] = date("n/d/Y", strtotime($endtime));
		$result['received_date_lf'] = date("D n/d/Y", strtotime($endtime));
		$week_count = (int)ceil(($days_count-$day_of_week)/7);
		if($day_of_week == 0)
		{
			if($days_count == 0)
			{
				$result['time_ago'] = "Today";	
			}
			elseif($days_count >0)
			{
				if($week_count <= 3)
				{
					$result['time_ago'] = $week_count." week ago";	
				}
				elseif($week_count > 3 && $week_count <= 7)
				{
					$week_count = $week_count-3;
					$result['time_ago'] = $week_count." month ago";	
				}
				elseif($week_count > 7)
				{
					$result['time_ago'] = "older message";	
				}	
			}
		}
		if($day_of_week == 1)
		{
			if($days_count == 0)
			{
				$result['time_ago'] = "Today";	
			}
			elseif($days_count == 1)
			{
				$result['time_ago'] = "Yesterday";	
			}
			elseif($days_count >1)
			{
				if($week_count <= 3)
				{
					$result['time_ago'] = $week_count." week ago";	
				}
				elseif($week_count > 3 && $week_count <= 7)
				{
					$week_count = $week_count-3;
					$result['time_ago'] = $week_count." month ago";	
				}
				elseif($week_count > 7)
				{
					$result['time_ago'] = "older message";	
				}	
			}
		}
		if($day_of_week == 2)
		{
			if($days_count == 0)
			{
				$result['time_ago'] = "Today";	
			}
			elseif($days_count == 1)
			{
				$result['time_ago'] = "Yesterday";	
			}
			elseif($days_count == 2)
			{
				$result['time_ago'] = date("l", strtotime($endtime));
			}
			elseif($days_count >2)
			{
				if($week_count <= 3)
				{
					$result['time_ago'] = $week_count." week ago";	
				}
				elseif($week_count > 3 && $week_count <= 7)
				{
					$week_count = $week_count-3;
					$result['time_ago'] = $week_count." month ago";	
				}
				elseif($week_count > 7)
				{
					$result['time_ago'] = "older message";	
				}	
			}
		}
		if($day_of_week == 3)
		{
			if($days_count == 0)
			{
				$result['time_ago'] = "Today";	
			}
			elseif($days_count == 1)
			{
				$result['time_ago'] = "Yesterday";	
			}
			elseif($days_count > 1 && $days_count <=3)
			{
				
				$result['time_ago'] = date("l", strtotime($endtime));	
			}
			elseif($days_count >3)
			{
				if($week_count <= 3)
				{
					$result['time_ago'] = $week_count." week ago";	
				}
				elseif($week_count > 3 && $week_count <= 7)
				{
					$week_count = $week_count-3;
					$result['time_ago'] = $week_count." month ago";	
				}
				elseif($week_count > 7)
				{
					$result['time_ago'] = "older message";	
				}	
			}
		}
		if($day_of_week == 4)
		{
			if($days_count == 0)
			{
				$result['time_ago'] = "Today";	
			}
			elseif($days_count == 1)
			{
				$result['time_ago'] = "Yesterday";	
			}
			elseif($days_count > 1 && $days_count <=4)
			{
				
				$result['time_ago'] = date("l", strtotime($endtime));	
			}
			elseif($days_count >4)
			{
				if($week_count <= 3)
				{
					$result['time_ago'] = $week_count." week ago";	
				}
				elseif($week_count > 3 && $week_count <= 7)
				{
					$week_count = $week_count-3;
					$result['time_ago'] = $week_count." month ago";	
				}
				elseif($week_count > 7)
				{
					$result['time_ago'] = "older message";	
				}	
			}
		}
		if($day_of_week == 5)
		{
			if($days_count == 0)
			{
				$result['time_ago'] = "Today";	
			}
			elseif($days_count == 1)
			{
				$result['time_ago'] = "Yesterday";	
			}
			elseif($days_count > 1 && $days_count <=5)
			{
				
				$result['time_ago'] = date("l", strtotime($endtime));	
			}
			elseif($days_count >5)
			{
				if($week_count <= 3)
				{
					$result['time_ago'] = $week_count." week ago";	
				}
				elseif($week_count > 3 && $week_count <= 7)
				{
					$week_count = $week_count-3;
					$result['time_ago'] = $week_count." month ago";	
				}
				elseif($week_count > 7)
				{
					$result['time_ago'] = "older message";	
				}	
			}
		}
		if($day_of_week == 6)
		{
			if($days_count == 0)
			{
				$result['time_ago'] = "Today";	
			}
			elseif($days_count == 1)
			{
				$result['time_ago'] = "Yesterday";	
			}
			elseif($days_count > 1 && $days_count <=6)
			{
				
				$result['time_ago'] = date("l", strtotime($endtime));	
			}
			elseif($days_count >6)
			{
				if($week_count <= 3)
				{
					$result['time_ago'] = $week_count." week ago";	
				}
				elseif($week_count > 3 && $week_count <= 7)
				{
					$week_count = $week_count-3;
					$result['time_ago'] = $week_count." month ago";	
				}
				elseif($week_count > 7)
				{
					$result['time_ago'] = "older message";	
				}	
			}
		}
		$result['days'] 	= $week_count;
		return $result;
	}
	/*** Time calculation function end ***/
}