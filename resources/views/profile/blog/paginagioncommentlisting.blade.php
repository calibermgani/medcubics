 <?php $userid = Auth::user()->id; ?>
   <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" id='user_id' />
   @foreach($blog_comments as $pcomments)  
   <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 no-padding parentcomment{{ $pcomments->id }}" style="margin-top:30px;">
	<div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
		<?php
			$filename = @$pcomments->user->avatar_name.'.'.@$pcomments->user->avatar_ext;
			$img_details = [];
			$img_details['module_name']='user';
			$img_details['file_name']=$filename;
			$img_details['practice_name']="admin";
			
			$img_details['class']='ideal blog-user-list  pull-right';
			$img_details['data-placement']='bottom';
			$img_details['data-toggle']='tooltip';
			$img_details['alt']='blog-image';
			$img_details['data-original-title']='Mackenzie John';
			$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
		?>
		{!! $image_tag !!}		
	</div>
	<div class="col-lg-11 col-md-10 col-sm-10 col-xs-10">
		<p class="no-bottom"><span class="med-green font14 font600"> {{ ucwords(@$pcomments->user->name) }}</span> <span class="pull-right font12 med-gray">{{ App\Http\Helpers\Helpers::dateFormat($pcomments->datetime,'datetime') }}</span></p>
		<p class="blog-text">{{ $pcomments->comments }}</p>
		
		<div class="blog-list">
		<ul>
			<li data-toggle="tooltip" data-title="Vote Up"><a class="js-vote_comments" name="up" data-commentid="{{ $pcomments->id }}" data-id="{{ $pcomments->blog_id }}"><span class="vote_up{{ $pcomments->id }}">{{ $pcomments->up_count }}</span><i class="fa fa-chevron-up"></i></a></li>
			<li data-toggle="tooltip" data-title="Vote Down"><a class="js-vote_comments" name="down" data-commentid="{{ $pcomments->id }}" data-id="{{ $pcomments->blog_id }}"><span class="vote_down{{ $pcomments->id }}">{{ $pcomments->down_count }}</span> <i class="fa fa-chevron-down"></i></a></li>
				
			<?php
			$filename = @$pcomments->attachment;
			$img_details = [];
			$img_details['module_name']='user';
			$img_details['file_name']=$filename;
			$img_details['practice_name']="admin";
			
			$img_details['class']='ideal blog-user-list  pull-right';
			$img_details['alt']='practice-image';
			$img_details['need_url']='yes';
			$get_commentimg_url = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
			 $ext = pathinfo($get_commentimg_url, PATHINFO_EXTENSION) 
			?>	
			
			@if($pcomments->attachment!='' && $get_commentimg_url!='img/noimage.png')
				 @if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' ) 
				<li><a href="#form-address-modal_comment" class="get_commentimg" data-toggle="modal" data-commentimg="{{ $pcomments->attachment }}"><i class="fa fa-paperclip"></i> Attachment</a></li>
				 @else 
				<li><a href="{{ $get_commentimg_url }} target="_blank"><i class="fa fa-paperclip"></i> Attachment</a></li>	 
				 @endif 
			@endif 
			
			@if($userid == $pcomments->user_id)
				<li data-toggle="tooltip" data-original-title="Delete"><a href="javascript:void(0)" data-blogid="{{ $pcomments->blog_id }}" data-id="{{ $pcomments->id }}" class="js_check_delete"><i class="fa fa-trash"></i> Delete</a></li>
			@elseif($userid == $blogownid)
				<li data-toggle="tooltip" data-original-title="Delete"><a href="javascript:void(0)" data-blogid="{{ $pcomments->blog_id }}" data-id="{{ $pcomments->id }}" class="js_check_delete"><i class="fa fa-trash"></i> Delete</a></li>		
			@endif 
			 
			<li><a href="javascript:void(0)" class="js_show_commentreply_box" data-id="{{ $pcomments->id }}"  ><i class="fa fa-reply"></i> Reply</a></li>
		</ul>
		</div>
	</div> 
		
	 
	<div class="col-lg-11 col-md-11 col-sm-12 col-xs-12 col-lg-offset-1 col-md-offset-1 margin-t-10 no-padding" id="commentreplypanel{{ $pcomments->id }}" style="display: none;">
	<?php
		$filename = @Auth::user ()->avatar_name.'.'.@Auth::user ()->avatar_ext;
		$img_details = [];
		$img_details['module_name']='user';
		$img_details['file_name']=$filename;
		$img_details['practice_name']="admin";
		
		$img_details['class']='ideal blog-user-list  pull-right';
		$img_details['data-placement']='bottom';
		$img_details['data-toggle']='tooltip';
		$img_details['alt']='blog-image';
		$img_details['data-original-title']='Mackenzie John';
		$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
	?>
	
	<div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
		{!! $image_tag !!}                     
	</div>
	
	<div class="col-lg-11 col-md-11 col-sm-10 col-xs-10 form-horizontal no-padding">
		
		<textarea name="reply_comments{{ $pcomments->id }}" class="form-control reply_comments{{ $pcomments->id }}" placeholder="Type your comments here ..." style="min-height:60px;" ></textarea> 
		<span class="error" id='js_error_replycomment{{ $pcomments->id }}' style="float:left; display:none;"  ><p>Enter your comments</p></span>	
		
		 <p class="margin-t-10">
			 <a class="med-btn pull-right js-post-replycomment" data-commentid="{{ $pcomments->id }}"  href="javascript:void(0);">Post Comment</a>
		 </p>                                     
	</div>                                
	</div>
   <div class="col-lg-11 col-lg-offset-1"><i class="fa fa-spinner fa-spin blogreplycommentload{{ $pcomments->id }}"  style="display:none"  ></i></div>
   
   <div class="col-md-11 col-md-offset-1 col-lg-11 col-lg-offset-1 add_replycomments{{ $pcomments->id }}">
		
   </div>
    <?php	 
		$getReplyComments = App\Http\Controllers\Profile\BlogController::getReplyComments($pcomments->id); 
	?> 
   	@foreach(@$getReplyComments['replycomment'] as $rpycomments)
		<div class="col-lg-11 col-md-11 col-lg-offset-1 col-md-offset-1 col-sm-12 col-xs-12 margin-t-13 no-padding childcomment{{ $rpycomments->id }}"  style="margin-top:30px;">
		<div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
			
			<?php
				$filename = @$pcomments->user->avatar_name.'.'.@$pcomments->user->avatar_ext;
				$img_details = [];
				$img_details['module_name']='user';
				$img_details['file_name']=$filename;
				$img_details['practice_name']="admin";
				
				$img_details['class']='ideal blog-user-list  pull-right';
				$img_details['data-placement']='bottom';
				$img_details['data-toggle']='tooltip';
				$img_details['alt']='blog-image';
				$img_details['data-original-title']='Mackenzie John';
				$get_rpyuserimage = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
			?>
			{!! $get_rpyuserimage !!}                     
		</div>
		<div class="col-lg-11 col-md-10 col-sm-10 col-xs-10">
			<p class="no-bottom"><span class="med-green font14 font600"> {{ ucwords(@$pcomments->user->name) }}</span> <span class="pull-right font12 med-gray">{{ App\Http\Helpers\Helpers::dateFormat($rpycomments->created_at,'datetime') }}</span></p>
			<p class="blog-text">{{ $rpycomments->comments }}</p>
		   
			<div class="blog-list">
			<ul>
				<li data-toggle="tooltip" data-title="Vote Up"><a class="js_vote_replycomments" name="up" data-commentid="{{ $rpycomments->id }}" data-id="{{ $pcomments->id }}"><span class="vote_upreply{{ $rpycomments->id }}">{{ $rpycomments->up_count }}</span><i class="fa fa-chevron-up"></i></a></li>
				<li data-toggle="tooltip" data-title="Vote Down"><a class="js_vote_replycomments" name="down" data-commentid="{{ $rpycomments->id }}" data-id="{{ $pcomments->id }}"><span class="vote_downreply{{ $rpycomments->id }}">{{ $rpycomments->down_count }}</span><i class="fa fa-chevron-down"></i></a></li>                                        
				
				@if($userid == $rpycomments->user_id)
					<li data-toggle="tooltip" data-original-title="Delete"><a href="javascript:void(0)" data-parentid="{{ $pcomments->id }}" data-replyid="{{ $rpycomments->id }}" class="js_delete_reply"><i class="fa fa-trash"></i> Delete</a></li>
				@elseif($userid == $blogownid)
					<li data-toggle="tooltip" data-original-title="Delete"><a href="javascript:void(0)" data-parentid="{{ $pcomments->id }}" data-replyid="{{ $rpycomments->id }}" class="js_delete_reply"><i class="fa fa-trash"></i> Delete</a></li>		
				@endif 	
				
			</ul>
			</div>
		</div>
		</div>
	@endforeach
	<div id="replyresults{{ $pcomments->id }}"></div>
							
	@if(@$getReplyComments['reply_total_page_count']>1) 
	<div class="col-lg-11 col-lg-offset-1">
		<button class="load_more_replycomment replycomment{{ $pcomments->id }} med-btn bg-white margin-t-10" data-totalrecord="{{ $getReplyComments['reply_total_record'] }}" data-commentid="{{ $pcomments->id }}" data-blogownerid="{{ $blogownid }}" data-totalpage="{{ $getReplyComments['reply_total_page_count'] }}" data-value="1" id="reply_load_more_button">Load More</button>
		<div class="animation_image{{ $pcomments->id }} med-green font16" style="display:none;"><i class="fa fa-spinner fa-spin"></i></div>
	</div>
	@endif
	
	</div>
	@endforeach