@extends('admin')
@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Blogs <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Manage Blogs <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span> View </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" onclick="history.go(-1);
                    return false;"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
					
            <?php 
				$userid = Auth::user()->id;
				$blog_ownerid = $blog->user_id;
				$blog_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($blog->id,'encode'); 
			?>	
            @if($userid == $blog_ownerid)

            @endif
            <li><a href="#js-help-modal" data-url="{{url('help/blog')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice')

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 margin-t-m-13"><!-- Left side Outer body starts -->
    @include('profile/layouts/tabs')
    @include('profile/blog/tabs')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Inner Content for full width Starts -->
        <div class="box-body-block"><!--Background color for Inner Content Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->


                    <div class="box-body table-responsive">
                        @if($blog->attachment != '')
                        <?php 
							$img_details = [];
							$img_details['module_name']='user';
							$img_details['file_name']=$blog->attachment;
							$img_details['practice_name']="admin";
							$img_details['need_url']="yes";					
							$get_blogimage_url = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details); 
							$ext = pathinfo($get_blogimage_url, PATHINFO_EXTENSION) 
						?>
                        @else
                        <?php 
							$get_blogimage_url = '';
							$ext = ''; 
						?>
                        @endif	
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                            <h3>{{  ucwords($blog->title) }}
                            
								@if($checkpermission->check_url_permission('profile/blog/{blog}/edit') == 1)	
									@if($blog->user_id == Auth::user()->id)
									<a href="{{ url('profile/blog/'.$blog_id.'/edit') }}" class="med-green font14 pull-right"><i class="fa fa-edit"></i> Edit</a>

									@endif
								@endif
                            </h3>
                            <p class="margin-t-m-8 med-gray"><i class="fa fa-user"></i> {{ @ucwords($blog->user->name) }} <i class="fa fa-calendar-o margin-l-10"></i> {{ App\Http\Helpers\Helpers::dateFormat($blog->updated_at,'datetime') }}</p>
                            <p> <?php echo $blog->description; ?></p>
                            <p class="med-gray">@if($blog->attachment!='')
                                @if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' ) 
                                <a href="#form-address-modal" data-toggle="modal" class=""><i class="fa fa-paperclip font13" data-toggle="tooltip" data-original-title="View attachment"></i> View Attachment</a> 
                                @else 
                                <a href=" {{ $get_blogimage_url }} " target="_blank" class=""><i class="fa fa-paperclip font13" data-toggle="tooltip" data-original-title="View attachment"></i>View Attachment</a> 
                                @endif 
                                | @endif 
                                <a href="javascript:void(0);" data-id="{{ $blog->id }}" class="blog_favourite med-gray" id="blog_fav{{ $blog->id }}"><?php echo ($blog_favourite == 1) ? '<i class="fa tooltips fa-star font13 med-orange" data-toggle="tooltip" data-title="Remove from favourite"></i>' : '<i class="fa tooltips fa-star-o font14 med-orange" data-toggle="tooltip" data-original-title="Add to favourite"></i>' ?></a> 
                                <span class="favourite_count{{$blog->id}} med-gray"> {{ count($blog_favcount) }}</span> Favourites                                        

                                |<span>  <a class="js-vote cur-pointer" name="up" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Vote Up"><i class="up{{ $blog->id }} fa fa-thumbs-o-up font13 {{ (@$blog_vote->up==1)? 'med-gray' : 'med-gray' }} "  ></i> 
                                        <span class="up{{ $blog->id }} vote_up{{ $blog->id }} med-gray">{{ $blog->up_count }} Votes</span> </a></span>
                                |<span> <a class="js-vote cur-pointer" name="down" data-id="{{ $blog->id }}"  data-toggle="tooltip" data-original-title="Vote Down"><i class="down{{ $blog->id }} fa fa-thumbs-o-down  font13 {{ (@$blog_vote->down==1)? 'med-gray' : 'med-gray' }}"  ></i>
                                        <span class="down{{ $blog->id }} vote_down{{ $blog->id }} med-gray">{{ $blog->down_count }} Votes</span></a>
                                </span>
                                |<span> <span class="med-gray form-cursor" id="js_show_blogreply_box"><i class="fa fa-reply"></i> Reply</span></span>
                            </p> 


                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  margin-t-10 no-padding" id="blogreplypanel" style="display: none;">
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
                                <div class="col-lg-11 col-md-11 col-sm-10 col-xs-10 form-horizontal no-padding">

                                    {!! Form::textarea('comments',null,['class'=>'form-control comments','placeholder'=>'Type your comments here ...','style'=>'min-height:60px;']) !!}
                                    <span class="error" id='js_error_comment' style="display:none;"  ><p>Enter your comments</p></span>		
                                    <p class="margin-t-10">
                                        <span class=" fileContainer" style="margin-left:0px;">
                                            {!! Form::file('attachment',null,['class'=>'form-control','id'=>'attachment','placeholder'=>'Attachment']) !!} &emsp;Upload&emsp;                                
                                        </span>
                                        <span class="js-display-attachment"></span>
                                        <a class="med-btn pull-right js-post-comment" href="javascript:void(0);">Post Comment</a>
                                    </p>  
                                    <span class="error" id='error_attachment' style="display:none;"><p>The selected file is not valid<p></span>
                                </div>                                
                            </div>
                            <i class="fa fa-spinner fa-spin med-green" style="display:none" > </i> 
                            <div class="add_comments"></div>

                            <input type="hidden" name="blog_id" value="{{ $blog->id }}" id='blog_id' />
                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" id='user_id' />
						
                            @foreach($blog_comments as $pcomments)  
                            <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 no-padding js_load_more_control parentcomment{{ $pcomments->id }}" style="margin-top:30px;">
                                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                    <?php
										$filename = @$pcomments->user->avatar_name.'.'.@$pcomments->user->avatar_ext;
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
                                    <p class="no-bottom"><span class="med-green font14 font600"> {{ ucwords(@$pcomments->user->name) }}</span> <span class="pull-right font12 med-gray">{{ App\Http\Helpers\Helpers::dateFormat($pcomments->datetime,'datetime') }}</span></p>
                                    <p class="blog-text">{{ $pcomments->comments }}</p>

                                    <div class="blog-list">
                                        <ul>
                                            <li data-toggle="tooltip" data-title="Vote Up"><a class="js-vote_comments" name="up" data-commentid="{{ $pcomments->id }}" data-id="{{ $blog->id }}"><span class="vote_up{{ $pcomments->id }}">{{ $pcomments->up_count }}</span><i class="fa fa-chevron-up"></i></a></li>
                                            <li data-toggle="tooltip" data-title="Vote Down"><a class="js-vote_comments" name="down" data-commentid="{{ $pcomments->id }}" data-id="{{ $blog->id }}"><span class="vote_down{{ $pcomments->id }}">{{ $pcomments->down_count }}</span> <i class="fa fa-chevron-down"></i></a></li>

                                            <?php
												$filename = @$pcomments->attachment;
												$img_details = [];
												$img_details['module_name']='blog_comments';
												$img_details['file_name']=$filename;
												$img_details['practice_name']="admin";
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
                                            <li data-toggle="tooltip" data-original-title="Delete"><a href="javascript:void(0)" data-blogid="{{ $blog->id }}" data-id="{{ $pcomments->id }}" class="js_check_delete"><i class="fa fa-trash"></i> Delete</a></li>		
                                            @endif 

                                            <li><a href="javascript:void(0)" class="js_show_commentreply_box" data-id="{{ $pcomments->id }}"  ><i class="fa fa-reply"></i> Reply</a></li>
                                        </ul>
                                    </div>
                                </div> 


                                <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12 col-lg-offset-1 col-md-offset-1 margin-t-10 no-padding" id="commentreplypanel{{ $pcomments->id }}" style="display: none;">


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

                                    <div class="col-lg-11 col-md-11 col-sm-10 col-xs-10 form-horizontal no-padding">

                                        <textarea name="reply_comments{{ $pcomments->id }}" class="form-control reply_comments{{ $pcomments->id }}" placeholder="Type your comments here ..." style="min-height:60px;" ></textarea> 
                                        <span class="error" id='js_error_replycomment{{ $pcomments->id }}' style="float:left; display:none;"  ><p>Enter your comments</p></span>	

                                        <p class="margin-t-10">
                                            <a class="med-btn pull-right js-post-replycomment" data-commentid="{{ $pcomments->id }}"  href="javascript:void(0);">Post Comment</a>
                                        </p>                                     
                                    </div>                                
                                </div>
                                <div class="col-lg-11 col-lg-offset-1 med-green"><i class="fa fa-spinner fa-spin blogreplycommentload{{ $pcomments->id }} med-green"  style="display:none"  ></i></div>

                                <div class="col-md-11 col-md-offset-1 col-lg-11 col-lg-offset-1 add_replycomments{{ $pcomments->id }}">

                                </div> 
								
                                <?php	 
									$getReplyComments = App\Http\Controllers\Profile\BlogController::getReplyComments($pcomments->id); 
								?>     
								
                                @foreach(@$getReplyComments['replycomment'] as $rpycomments)	
                                <div class="col-lg-11 col-md-11 col-lg-offset-1 col-md-offset-1 col-sm-12 col-xs-12 margin-t-13 no-padding childcomment{{ $rpycomments->id }}"  style="margin-top:30px;">
                                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                        <?php
											$filename = @$rpycomments->user->avatar_name.'.'.@$rpycomments->user->avatar_ext;
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
                                        <p class="no-bottom"><span class="med-green font14 font600"> {{ ucwords(@$rpycomments->user->name) }}</span> <span class="pull-right font12 med-gray">{{ App\Http\Helpers\Helpers::dateFormat($rpycomments->created_at,'datetime') }}</span></p>
                                        <p class="blog-text">{{ $rpycomments->comments }}</p>

                                        <div class="blog-list">
                                            <ul>
                                                <li data-toggle="tooltip" data-title="Vote Up"><a class="js_vote_replycomments" name="up" data-commentid="{{ $rpycomments->id }}" data-id="{{ $pcomments->id }}"><span class="vote_upreply{{ $rpycomments->id }}">{{ $rpycomments->up_count }}</span><i class="fa fa-chevron-up"></i></a></li>
                                                <li data-toggle="tooltip" data-title="Vote Down"><a class="js_vote_replycomments" name="down" data-commentid="{{ $rpycomments->id }}" data-id="{{ $pcomments->id }}"><span class="vote_downreply{{ $rpycomments->id }}">{{ $rpycomments->down_count }}</span><i class="fa fa-chevron-down"></i></a></li>                                        

                                                @if($userid == $rpycomments->user->id)
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
                                    <button class="load_more_replycomment replycomment{{ $pcomments->id }} med-btn bg-white margin-t-10" data-totalrecord="{{ $getReplyComments['reply_total_record'] }}" data-commentid="{{ $pcomments->id }}" data-blogownerid="{{ $blog_ownerid }}" data-totalpage="{{ $getReplyComments['reply_total_page_count'] }}" data-value="1" id="reply_load_more_button">Load More</button>
                                    <div class="animation_image{{ $pcomments->id }} med-green font16" style="display:none;"><i class="fa fa-spinner fa-spin"></i> </div>
                                </div>
                                @endif


                            </div>
                            @endforeach

                            <div id="results"></div>
                            @if($total_page_count>1) 
                            <div align="center">
                                <a class="load_more_comment margin-t-10 form-cursor font600" data-totalrecord="{{$total_record }}" data-blogid="{{ $blog->id }}" data-ownerid="{{ $blog->user_id }}" data-totalpage="{{$total_page_count }}" id="load_more_button">Load More ...</a>
                                <div class="animation_image med-green font16" style="display:none;"><i class="fa fa-spinner fa-spin"></i></div>
                            </div>
                            @endif

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
                                @if(@$blog_url!='') 

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border:1px solid #f0f0f0">

                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4" style="border-right:1px solid #f0f0f0">
                                        <?php
											if($blog_url->image!='' && !filter_var($blog_url->image, FILTER_VALIDATE_URL) === false) {    
											echo '<a href="' . $blog_url->url . '" target="_blank"><img src="' . $blog_url->image . '" style="width: 150px; text-align:center;"></a>';
											}     
                                        ?>
                                    </div>
                                    <div class="col-lg-10 col-md-10 col-sm-2 col-xs-4">
                                        <div>  <a href="{{ $blog_url->url }}" style="color:#868686" target="_blank">{{ ($blog_url->title!='')? $blog_url->title : '' }}</a> </div>  
                                        <div> {{ ($blog_url->description!='')? $blog_url->description : '' }}
                                            <p> <a href="{{ $blog_url->url }}" target="_blank" class="med-orange">{{ $blog_url->url }} </a>  </p>
                                        </div>
                                    </div>
                                </div>
								@endif
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box Ends-->
            </div><!--  Left side Content Ends -->
        </div>
    </div>
</div>
<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow no-bottom no-border">
       @include('profile/layouts/rightside-tabs')
        @include('profile/blog/recentcomments',['blogid'=>$blog->id])
        @include('profile/blog/mostviewed')
    </div><!-- /.box -->
</div>
{{ $get_org_img = '' }}
@if($blog->attachment != '') 
	<?php $get_blogimage_url = '';  ?>
	@if($get_blogimage_url != '')
		<?php  
			$image_size = getimagesize($get_blogimage_url);
			$get_org_img = $image_size[0]+30;
		?>
	@endif
@endif


<!-- Modal Light Box starts -->
<div id="form-address-modal" class="modal fade in">
    <div class="modal-dialog" style="width: {{ $get_org_img }}px;">
        <div class="modal-content">
            <div class="modal-header" style="background: none; border: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">

                <p id="modal_show_error_message"  class="hide" ></p>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
<?php $commentimg_url = App\Http\Helpers\Helpers::getPracticeBlogImgUrl('blog_comments'); ?>
<span id='selector_comment_url' data-commenturl='{{ $commentimg_url }}'></span>

<div id="form-address-modal_comment" class="modal fade in">
    <div class="modal-dialog set_comment_width">
        <div class="modal-content">
            <div class="modal-header" style="background: none; border: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body comments_img">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->


@stop