<div class="col-sm-12 col-xs-12 margin-t-13 no-padding childcomment{{ $addnewrpycommend->id }}" style="margin-top:30px;">
	<div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
	 
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
			$img_details['data-original-title']='mackenzie john';
			$image_tag = App\Http\Helpers\Helpers::checkandgetavatar($img_details);
		?>	
		{!! $image_tag !!}	
	</div>
	<div class="col-lg-11 col-md-10 col-sm-10 col-xs-10">
		<p class="no-bottom"><span class="med-green font14 font600"> {{ ucwords(Auth::user ()->name) }}</span> <span class="pull-right font12 med-gray">{{ App\Http\Helpers\Helpers::dateFormat($addnewrpycommend->created_at,'datetime') }}</span></p>
		<p class="blog-text">{{ $addnewrpycommend->comments }}</p>
	   
		<div class="blog-list">
			<ul>
				<li data-toggle="tooltip" data-title="Vote Up"><a class="js_vote_replycomments" name="up" data-commentid="{{ $addnewrpycommend->id }}" data-id="{{ $addnewrpycommend->comment_id }}"><span class="vote_upreply{{ $addnewrpycommend->id }}">{{ $addnewrpycommend->up_count }}</span><i class="fa fa-chevron-up"></i></a></li>
				<li data-toggle="tooltip" data-title="Vote Down"><a class="js_vote_replycomments" name="down" data-commentid="{{ $addnewrpycommend->id }}" data-id="{{ $addnewrpycommend->comment_id }}"><span class="vote_downreply{{ $addnewrpycommend->id }}">{{ $addnewrpycommend->down_count }}</span><i class="fa fa-chevron-down"></i></a></li>                                        
				<li data-toggle="tooltip" data-original-title="Delete"><a href="javascript:void(0)" data-parentid="{{ $addnewrpycommend->comment_id }}" data-replyid="{{ $addnewrpycommend->id }}" class="js_delete_reply"><i class="fa fa-trash"></i> Delete</a></li>	
			</ul>
		</div>
	</div>
</div>