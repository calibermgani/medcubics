<?php $userid = auth::user()->id; ?>
<div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 no-padding parentcomment{{ $addnewcommend->id }}" style="margin-top:30px;">
	<div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
			<?php
				$filename = @Auth::user ()->avatar_name.'.'.@Auth::user ()->avatar_ext;
				$img_details = [];
				$img_details['module_name']='user';
				$img_details['file_name']=$filename;
				$img_details['practice_name']="admin";
				
				$img_details['class']='ideal blog-user-list  pull-right';
				$img_details['alt']='practice-image';
				$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
			?>
			{!! $image_tag !!} 
	</div>
	<div class="col-lg-11 col-md-10 col-sm-10 col-xs-10">
		<p class="no-bottom"><span class="med-green font14 font600"> {{ ucwords(auth::user ()->name) }}</span> <span class="pull-right font12 med-gray">{{ App\Http\Helpers\Helpers::dateformat($addnewcommend->datetime,'datetime') }}</span></p>
		<p class="blog-text">{{ $addnewcommend->comments }}</p>
		
		<div class="blog-list">
		<ul>
			<li data-toggle="tooltip" data-title="vote up"><a class="js-vote_comments" name="up" data-commentid="{{ $addnewcommend->id }}" data-id="{{ $addnewcommend->blog_id }}"><span class="vote_up{{ $addnewcommend->id }}">{{ $addnewcommend->up_count }}</span><i class="fa fa-chevron-up"></i></a></li>
			<li data-toggle="tooltip" data-title="vote down"><a class="js-vote_comments" name="down" data-commentid="{{ $addnewcommend->id }}" data-id="{{ $addnewcommend->blog_id }}"><span class="vote_down{{ $addnewcommend->id }}">{{ $addnewcommend->down_count }}</span> <i class="fa fa-chevron-down"></i></a></li>
			<?php
				$filename = @$addnewcommend->attachment;
				$img_details = [];
				$img_details['module_name']='blog_comments';
				$img_details['file_name']=$filename;
				$img_details['practice_name']="admin";
				$img_details['need_url']='yes';
				$get_commentimg_url = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				 $ext = pathinfo($get_commentimg_url, PATHINFO_EXTENSION) 
			?>
			@if($addnewcommend->attachment!='')
				 @if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' ) 
				<li><a href="#form-address-modal_comment" class="get_commentimg" data-toggle="modal" data-commentimg="{{ $addnewcommend->attachment }}"><i class="fa fa-paperclip"></i> attachment</a></li>
				 @else 
				<li><a href="{{ $get_commentimg_url }}" target="_blank"><i class="fa fa-paperclip"></i> attachment</a></li>	 
				 @endif 
			@endif 
		   
		  <li data-toggle="tooltip" data-original-title="delete"><a href="javascript:void(0)" data-blogid="{{ $addnewcommend->blog_id }}" data-id="{{ $addnewcommend->id }}" class="js_check_delete"><i class="fa fa-trash"></i> Delete</a></li>
		  <li><a href="javascript:void(0)" class="js_show_commentreply_box" data-id="{{ $addnewcommend->id }}"  ><i class="fa fa-reply"></i> reply</a></li>
		</ul>
		</div>
	</div> 

	{!! Form::open(['url'=>'profile/blog/comments','id'=>'js-bootstrap-validator','files'=>true]) !!}    
		<div class="col-lg-11 col-md-11 col-sm-12 col-xs-12 col-lg-offset-1 col-md-offset-1 margin-t-10 no-padding" id="commentreplypanel{{ $addnewcommend->id }}" style="display: none;">
		
		<div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
				   
			<?php
				$filename = @Auth::user ()->avatar_name.'.'.@Auth::user ()->avatar_ext;
				$img_details = [];
				$img_details['module_name']='user';
				$img_details['file_name']=$filename;
				$img_details['practice_name']="admin";
				
				$img_details['class']='ideal blog-user-list  pull-right';
				$img_details['alt']='practice-image';
				$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
			?>
			{!! $image_tag !!} 	   
		</div>
		
		<input type="hidden" name="blog_id" value="{{ $addnewcommend->blog_id }}" id='blog_id' />
		<input type="hidden" name="owner_id" value="{{ $addnewcommend->user_id }}" id='owner_id' />
		<input type="hidden" name="user_id" value="{{ auth::user()->id }}" id='user_id' />
		
		<div class="col-lg-11 col-md-11 col-sm-10 col-xs-10 form-horizontal no-padding">
			
			<textarea name="reply_comments{{ $addnewcommend->id }}" class="form-control reply_comments{{ $addnewcommend->id }}" placeholder="type your comments here ..." style="min-height:60px;" ></textarea> 
			<span class="error" id='js_error_replycomment{{ $addnewcommend->id }}' style="float:left; display:none;"  ><p>enter your comments</p></span>	
									
			 <p class="margin-t-10">
				 <a class="med-btn pull-right js-post-replycomment" href="javascript:void(0);" data-commentid="{{ $addnewcommend->id }}">post comment</a>
			 </p>                                     
		</div>                                
		</div>
	{!! Form::close() !!}
	<div class="col-lg-11 col-lg-offset-1"><i class="fa fa-spinner fa-spin blogreplycommentload{{ $addnewcommend->id }}" style="display:none" ></i></div>
	<div class="col-md-11 col-md-offset-1 col-lg-11 col-lg-offset-1 add_replycomments{{ $addnewcommend->id }}">
	</div>

 </div>