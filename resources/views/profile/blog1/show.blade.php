@extends('admin')

@section('toolbar')
<script>
    var comments_itemperpage = {{ config('app.comments_itemperpage') }};</script>
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-user font14"></i> Blogs</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" onclick="history.go(-1);return false;"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <?php
            $userid = Auth::user()->id;
            $blog_ownerid = $blog->user_id;
            ?>

            @if($userid == $blog_ownerid)
            <li><a href="{{ url('profile/blog/'.$blog->id.'/edit') }}"><i class="fa fa-edit" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            @endif
            <li><a href="#js-help-modal" data-url="{{url('help/code')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice')

<div class="col-md-12 margin-t-m-20"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="code"></i> <h3 class="box-title">{{ ucwords($blog->title) }}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                 <?php
                    $filename = $blog->title;
                    $avatar_url = App\Http\Helpers\Helpers::checkAndGetAvatar('user', $filename);
                ?>
                <div class="box-body table-responsive">
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                            <h3>{{ ucwords($blog->title) }}</h3>
                            <p> <?php echo $blog->description; ?></p>
                                                       
                        </div>
                        
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 no-padding" style="border-left:1px dashed #ccc;">
                            <?php $ext = pathinfo(url("/").'/media/blog/'.$blog->attachment, PATHINFO_EXTENSION) ?>
                               
                             <div> 
                                 <p class="font600">{!! HTML::image($avatar_url,null,['class'=>'margin-r-20','style'=>'width:40px; margin-left:10px; margin-bottom:10px; border-radius:50%; border:2px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                            -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                        box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:40px; float:left;']) !!} {{ @ucwords($blog->user->name) }}</p>
                                 <p style="margin-top:-10px;"><span class="space-m-t-15 med-orange">{{ App\Http\Helpers\Helpers::dateFormat($blog->updated_at,'datetime') }}</span></p>
                                 
                                 <p style="margin-left:10px;">@if($blog->attachment!='' && file_exists('media/blog/'.$blog->attachment) == '1')
                                     @if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' ) 
                                     <a href="#form-address-modal" data-toggle="modal" class="font600"><i class="fa fa-paperclip font16 font600" data-toggle="tooltip" data-original-title="View attachment"></i> View Attachment</a> 
                                     @else 
                                     <a href="{{ url('/') }}/media/blog/{{$blog->attachment}}" target="_blank" class="font600"><i class="fa fa-paperclip font16 font600" data-toggle="tooltip" data-original-title="View attachment"></i>View Attachment</a> 
                                     @endif 
                                     | @endif 
                                     <a href="javascript:void(0);" data-id="{{ $blog->id }}" class="blog_favourite" id="blog_fav{{ $blog->id }}"><?php echo ($blog_favourite == 1) ? '<i class="fa tooltips fa-star font16 med-orange font600" data-toggle="tooltip" data-title="Remove from favourite"></i>' : '<i class="fa tooltips fa-star-o font16 med-orange font600" data-toggle="tooltip" data-original-title="Add to favourite"></i>' ?></a>
                                     <span class="favourite_count{{$blog->id}} font600"> {{ count($blog_favcount) }}</span>
                                     <span class="font600 js-comments_count"> | Comments ({{ count($blog_commentcount) }}) </span>
                                     <span class="font600 js-participants"> | Participants ({{ count((array)$blogpart) }})</span>
                                 </p> 
                                 <p style="margin-left:10px;"><span>  <a class="js-vote cur-pointer" name="up" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="up{{ $blog->id }} fa fa-thumbs-o-up font600 font16 {{ (@$blog_vote->up==1)? 'med-orange' : 'med-green' }} "  ></i> 
                                             <span class="up{{ $blog->id }} vote_up{{ $blog->id }} font600 {{ (@$blog_vote->up==1)? 'med-orange' : 'med-green' }}">{{ $blog->up_count }} Votes</span> </a></span>
                                     | <span><a class="js-vote cur-pointer" name="down" data-id="{{ $blog->id }}"  data-toggle="tooltip" data-original-title="Add vote"><i class="down{{ $blog->id }} fa fa-thumbs-o-down font600 font16 {{ (@$blog_vote->down==1)? 'med-orange' : 'med-green' }}"  ></i>
                                             <span class="down{{ $blog->id }} vote_down{{ $blog->id }} font600 {{ (@$blog_vote->down==1)? 'med-orange' : 'med-green' }}">{{ $blog->down_count }} Votes</span></a>
                                     </span>
                                 </p>
                             </div>                                                                       
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
                            @if($blog->url!='')                                     
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border:1px solid #f0f0f0">
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4" style="border-right:1px solid #f0f0f0">
										<?php 
											if($blog_url->image!='' && !filter_var($blog_url->image, FILTER_VALIDATE_URL) === false) {    
											echo '<a href="' . $blog->url . '" target="_blank"><img src="' . $blog_url->image . '" style="width: 150px; text-align:center;"></a>';
											}     
										?>
									</div>
									<div class="col-lg-10 col-md-10 col-sm-2 col-xs-4">
										<div>  <a href="{{ $blog->url }}" style="color:#868686" target="_blank">{{ ($blog_url->title!='')? $blog_url->title : '' }}</a> </div>  
									   <div> {{ ($blog_url->description!='')? $blog_url->description : '' }}
										<p> <a href="{{ $blog->url }}" target="_blank" class="med-orange">{{ $blog->url }} </a>  </p>
									   </div>
								   </div>
										
									 
								</div>
							@endif
                        </div>
                    </div>
                    
                    
                </div><!-- /.box-body -->
                
                <div class="box-header no-background" style="border-bottom:1px solid #00877f">
                    <i class="livicon" data-name="code"></i> <h3 class="box-title">Comments</h3>
                    <div class="box-tools pull-right">
                        <h5 style="margin-top:5px;" class="font600 med-orange">{{ count($blog_commentcount) }}</h5>
                    </div>
                </div><!-- /.box-header -->
                
                <div class="box-body  form-horizontal" style="max-height:300px; overflow-y: scroll">
                        <div class="show_comments">
                        </div>
                </div>
            </div><!-- /.box Ends-->
            
            <div class="col-lg-12 col-md-12 margin-t-10">
                <a href="javascript:void(0);" class="replylink med-orange" style="font-size: 13px; margin-bottom: 7px; border-radius: 4px; border:1px solid #00877f; padding: 0px 10px 2px 10px; cursor: pointer;">Leave a Reply</a>
                </div>
                <div class="box-body  form-horizontal leavereplybox" style="display: none;">
                    
                    {!! Form::open(['url'=>'profile/blog/comments','id'=>'js-bootstrap-validator','files'=>true]) !!}    
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 no-padding margin-t-5 no-bottom" style="margin-bottom:-20px">
                        <div class="box no-border no-shadow">
                            
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="blog_id" value="{{ $blog->id }}" id='blog_id' />
                        <input type="hidden" name="owner_id" value="{{ $blog->user_id }}" id='owner_id' />
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" id='user_id' />
                        
                            <div class="box-body no-bottom">
                     <div class="form-group">
                                 
                        <div class="col-lg-7 col-md-10 col-sm-10 col-xs-20">
                            {!! Form::textarea('comments',null,['class'=>'form-control comments','placeholder'=>'Type your comments here ...']) !!}
                            <span class="error" id='error_comment' style="display: none"><p>Enter your comments<p></span>
                        </div>
                    </div>
                     <div class="form-group">
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10">
                            {!! Form::file('attachment',null,['class'=>'form-control','id'=>'attachment','placeholder'=>'Attachment']) !!}
                            <span class="error" id='error_attachment' style="display: none"><p>The selected file is not valid<p></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-5 col-md-8 col-sm-12 col-xs-12">
                                        <a class="btn btn-medcubics js-post-comment" href="javascript:void(0);">Post Comment</a>
                        </div>
                    </div>
                        </div>
                    </div>
                    </div>
                    {!! Form::close() !!}  
                    
                </div><!-- /.box-body -->
            
        </div><!--  Left side Content Ends -->

        
    </div>
</div>
{{ $get_org_img = '' }}
<?php
	if($blog->attachment != '') 
		if(file_exists('media/blog/'.$blog->attachment) == '1') {
			$image_size = getimagesize(url().'/media/blog/'.$blog->attachment);
			$get_org_img = $image_size[0]+30;
		}
}
?>
 <!-- Modal Light Box starts -->
<div id="form-address-modal" class="modal fade in">
    <div class="modal-dialog" style="width: {{ $get_org_img }}px;">
        <div class="modal-content">
            <div class="modal-header" style="background: none; border: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <img src="{{ url('/media/blog/') }}{{$blog->attachment}}" />
                <p id="modal_show_error_message"  class="hide" ></p>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

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

<!--End-->
<style>
    .url_box { border: 1px solid gray;
padding: 4px;
width: 500px;
display: inherit;}
</style>

@stop 

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        var blog_id = $('#blog_id').val();
        var owner_id = $('#owner_id').val();
        var user_id = $('#user_id').val();
        get_comments(blog_id,owner_id,user_id);
        blog_comments_fav(blog_id);
        blog_comments_fav_count(blog_id);
    });
    var comments_itemperpage = {{ config('app.comments_itemperpage') }};
</script>
@endpush