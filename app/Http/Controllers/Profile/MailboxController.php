<?php namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Request;
use Redirect;
use Auth;
use View;
use Session;
use Config;
use App\Http\Controllers\Profile\Api\MailboxApiController as MailboxApiController;

class MailboxController extends MailboxApiController 
{
	public function __construct() 
	{ 
        View::share( 'heading', 'Messages' );    
	    View::share( 'selected_tab', 'profile' ); 
		View::share( 'heading_icon', Config::get("cssconfigs.common.appealaddress"));
		View::share( 'message_draft_list_count', $this->getDraftMaillistApi()->getData()->data->message_draft_list_count);
		/*View::share( 'message_inbox_list_unread_count', $this->MailSettingsApi()->getData()->message_inbox_list_unread_count);
		View::share( 'message_sent_list_count', $this->getSendMaillistApi()->getData()->data->message_sent_list_count);
		View::share( 'message_draft_list_count', $this->getDraftMaillistApi()->getData()->data->message_draft_list_count);
		View::share( 'message_trash_list_count', $this->getTrashMaillistApi()->getData()->data->message_trash_list_count);*/
	}  
	
	public function __destruct() 
	{ 
        $api_response	    = $this->MailSettingsApi();
		$api_response_data 	= $api_response->getData();
		View::share( 'mail_settings_datas', $api_response_data->result);
		View::share( 'message_inbox_list_unread_count', $api_response_data->message_inbox_list_unread_count);
		View::share( 'lastlabeldet', $api_response_data->lastlabeldet);
		View::share( 'users_list_arr', $api_response_data->users_list_arr);
		View::share( 'users_list', $api_response_data->users_list);
    }
	
	/*** Inbox list starts ***/
	public function getMaillist()
	{	
		if(Session::get('practice_dbid')=='') {
			return Redirect::to('profile');
		}
		$api_response		= $this->getMaillistApi();
        $api_response_data 	= $api_response->getData();
		$message_inbox_list_count = $api_response_data->data->message_inbox_list_count;
		$message_inbox_list = $api_response_data->data->message_inbox_list;
		$mail_list_option	= "inbox"; 
		$getorder			= "date";
		return view('profile/maillist/maillist', compact('mail_list_option','getorder','message_inbox_list_count','message_inbox_list'));
	}
	/*** Inbox list ends ***/
	
	/*** Send list starts ***/
	public function getSendMaillist()
	{
		$api_response				= $this->getSendMaillistApi();
        $api_response_data 			= $api_response->getData();
		$message_sent_list_count	= $api_response_data->data->message_sent_list_count;
		$message_sent_list			= $api_response_data->data->message_sent_list;
		$mail_list_option			= "send";
		$getorder			= "date";
		return view('profile/maillist/maillist', compact('mail_list_option','getorder','message_sent_list_count','message_sent_list'));
	}
	/*** Send list ends ***/
	
	/*** Draft list starts ***/
	public function getDraftMaillist()
	{
		$api_response				= $this->getDraftMaillistApi();
		$api_response_data 			= $api_response->getData();
		$message_draft_list_count	= $api_response_data->data->message_draft_list_count;
		$message_draft_list			= $api_response_data->data->message_draft_list;
		$mail_list_option			= "draft";
		return view('profile/maillist/maillist', compact('mail_list_option','getorder','message_draft_list_count','message_draft_list'));
	}
	/*** Draft list ends ***/
	
	/*** Trash list starts ***/
	public function getTrashMaillist()
	{
		$api_response				= $this->getTrashMaillistApi();
		$api_response_data 			= $api_response->getData();
		$message_trash_list_count	= $api_response_data->data->message_trash_list_count;
		$message_trash_list			= $api_response_data->data->message_trash_list;
		$mail_list_option			= "trash";
		$getorder					= "date";
		return view('profile/maillist/maillist', compact('mail_list_option','getorder','message_trash_list_count','message_trash_list'));
		
	}
	/*** Trash list ends ***/
	
	/*** Inbox mail view start ***/
	public function getInboxMailViewDet($mail_id)
	{
		$api_response				= $this->getInboxMailViewDetApi($mail_id);
        $api_response_data 			= $api_response->getData();
		$api_response	   			= $this->MailSettingsApi();
		$unread_msg_count 			= $api_response->getData()->message_inbox_list_unread_count;
		$mail_subject		 		= $api_response_data->data->mail_subject;
		$view_msg_det		 		= $api_response_data->data->view_msg_det;
		$curr_mail_id				= $mail_id;
		$mail_list_option			= "view";
		$reply_mail_from			= "inbox";
		return view('profile/maillist/viewlist', compact('mail_list_option','mail_subject','view_msg_det','curr_mail_id','unread_msg_count','reply_mail_from'));
	}
	/*** Inbox mail view ends ***/
	
	/*** Draft mail view start ***/
	public function getDraftMailViewDet($mail_id)
	{
		$api_response				= $this->getdraftMailViewDetApi($mail_id);
		$api_response_data 			= $api_response->getData();
		$mail_subject		 		= $api_response_data->data->mail_subject;
		$view_msg_det		 		= $api_response_data->data->view_msg_det;
		$curr_mail_id				= $mail_id;
		$mail_list_option			= "view";
		$reply_mail_from			= "Draft";
		return view('profile/maillist/viewlist', compact('mail_list_option','mail_subject','view_msg_det','curr_mail_id','reply_mail_from'));
	}
	/*** Draft mail view ends ***/
	
	/*** Send mail view start ***/
	public function getSendMailViewDet($mail_id)
	{
		$api_response				= $this->getSendMailViewDetApi($mail_id);
        $api_response_data 			= $api_response->getData();
		$mail_subject		 		= $api_response_data->data->mail_subject;
		$view_msg_det		 		= $api_response_data->data->view_msg_det;
		$curr_mail_id				= $mail_id;
		$mail_list_option			= "view";
		$reply_mail_from			= "sent";
		return view('profile/maillist/viewlist', compact('mail_list_option','mail_subject','view_msg_det','curr_mail_id','reply_mail_from'));
	}
	/*** Send mail view ends ***/
	
	/*** Trash mail view start ***/
	public function getTrashMailViewDet($mail_id)
	{
		$api_response				= $this->getTrashMailViewDetApi($mail_id);
        $api_response_data 			= $api_response->getData();
		$mail_subject		 		= $api_response_data->data->mail_subject;
		$view_msg_det		 		= $api_response_data->data->view_msg_det;
		$curr_mail_id				= $mail_id;
		$mail_list_option			= "view";
		$reply_mail_from			= "trash";
		return view('profile/maillist/viewlist', compact('mail_list_option','mail_subject','view_msg_det','curr_mail_id','reply_mail_from'));
	}
	/*** Trash mail view ends ***/
	
	/*** Label mail list view start ***/
	public function getotherLabelMaillist($label_id)
	{
		$api_response		= $this->getotherLabelMaillistApi($label_id);
        $api_response_data 	= $api_response->getData();
		$message_label_list_count = $api_response_data->data->message_label_list_count;
		$message_label_list = $api_response_data->data->message_label_list;
		$mail_list_option	= "label";
		$mail_list_option_val	= $api_response_data->data->mail_list_option;
		$getorder			= "date";
		return view('profile/maillist/maillist', compact('mail_list_option','getorder','message_label_list_count','message_label_list','mail_list_option_val','label_id'));
	}
	/*** Label mail list view ends ***/
	
	/*** Label mail view start ***/
	public function getLabelMailViewDet($mail_id)
	{
		$api_response				= $this->getInboxMailViewDetApi($mail_id);
        $api_response_data 			= $api_response->getData();
		$api_response	   			= $this->MailSettingsApi();
		$unread_msg_count 			= $api_response->getData()->message_inbox_list_unread_count;
		$mail_subject		 		= $api_response_data->data->mail_subject;
		$view_msg_det		 		= $api_response_data->data->view_msg_det;
		$curr_mail_id				= $mail_id;
		$mail_list_option			= "view";
		$reply_mail_from			= "label";
		return view('profile/maillist/viewlist', compact('mail_list_option','mail_subject','view_msg_det','curr_mail_id','reply_mail_from'));
	}
	/*** Label mail view ends ***/
	
	/*** Signature page start ***/
	public function MailSettings()
	{
		return view('profile/maillist/mailsetting');
		
	}
	/*** Signature page end ***/
	
	/*** Signature store start ***/
	public function MailSettingsstore()
	{
		$api_response 		= $this->MailSettingsstoreApi();
		return Redirect::to('profile/maillist')->with('success','Settings Updated Successfully');
		
	}
	/*** Signature store end ***/
	
	/*** Compose page view start ***/
	public function getComposemail()
	{
		return view('profile/maillist/composemail');
		
	}
	/*** Compose page view end ***/
	
	/*** Reply page view start ***/
	public function getReplymail($mail_id,$reply_all_type='')
	{
		$request= [];
		$request['mail_id']= $mail_id;
		if(isset($reply_all_type)) $request['reply_all_type']= $reply_all_type;
		$api_response				= $this->replymailprocess($request);
        $api_response_data 			= $api_response->getData();
		$exists_content				= $api_response_data->data->reply_exists_content;
		$to_address		 			= $api_response_data->data->reply_ids_arr;
		$mail_subject		 		= $api_response_data->data->subject;
		$chk_draft		 			= $api_response_data->data->chk_draft;
		$attachment_file		 			= $api_response_data->data->attachment_file;
		return view('profile/maillist/replymail', compact('to_address','attachment_file','chk_draft','mail_id','mail_subject','exists_content'));
	}
	/*** Reply page view end ***/
	
	/*** Show page view start ***/
	public function getShowmail($mail_id)
	{
		$api_response				= $this->getShowmailApi($mail_id);
        $api_response_data 			= $api_response->getData();
		$from_details				= $api_response_data->data->from_details;
		$msg_details		 		= $api_response_data->data->msg_details[0];
		$to_details		 			= $api_response_data->data->to_details;
		return view('profile/maillist/showmail', compact('mail_id','from_details','to_details','msg_details'));
	}
	/*** Show page view end ***/
	
	/*** Keyword search start ***/
	public function getKeywordsearch(request $request)
	{
		$api_response				= $this->getKeywordsearchApi($request);
        $api_response_data 			= $api_response->getData();
		$page						= $api_response_data->data->page;
		$page_count ="message_".$page."_list_count";
		$page_val ="message_".$page."_list";
		$$page_count  = $api_response_data->data->message_details_count;
		$$page_val  = $api_response_data->data->message_details;
		$getorder  = $api_response_data->data->getorder;
		return view('profile/maillist/'.$page.'list', compact('getorder',$page,$page_val,$page_count));
	}
	/*** Keyword search end ***/
	
	/*** Keyword filter start ***/
	public function getKeywordfilter(request $request)
	{
		$api_response				= $this->getKeywordfilterApi($request);
        $api_response_data 			= $api_response->getData();
		$page						= $api_response_data->data->page;
		$page_count ="message_".$page."_list_count";
		$page_val ="message_".$page."_list";
		$$page_count  = $api_response_data->data->message_details_count;
		$$page_val  = $api_response_data->data->message_details;
		$getorder  = $api_response_data->data->getorder;
		return view('profile/maillist/'.$page.'list', compact('getorder',$page,$page_val,$page_count));
	}
	/*** Keyword filter end ***/
	
	/*** Sorting option start ***/
	public function getUnreadmail($status_read,$page,$getorder,$order_by,$label_id='')
	{
		$request = [];
		$request['status_read'] = 	$status_read; 
		$request['page'] 		=	$page; 
		$request['getorder'] 	=	$getorder; 
		$request['order_by'] 	=	$order_by; 
		$request['label_id'] 	=	$label_id; 
		$api_response				= $this->getUnreadmailApi($request);
        $api_response_data 			= $api_response->getData();
		$page_count ="message_".$page."_list_count";
		$page_val ="message_".$page."_list";
		$$page_count  = $api_response_data->data->message_details_count;
		$$page_val  = $api_response_data->data->message_details;
		return view('profile/maillist/'.$page.'list', compact('getorder',$page,$page_val,$page_count));
	}
	/*** Sorting option end ***/
}