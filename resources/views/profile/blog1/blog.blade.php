@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-book font14"></i> Blogs</small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('cpt') }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
           <li><a href="{{ url('profile/blog/create') }}"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></a></li>            
           <li><a href="#js-help-modal" data-url="{{url('help/blog')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop


@section('practice')

 <?php $favarray = json_decode(json_encode($favcountarray),true); 
    $commentarray = json_decode(json_encode($commentcountarray),true);
    $blogpartarray = json_decode(json_encode($blogpartcountarray),true);
    $favblogarray = json_decode(json_encode($favblogarray),true);
    $blog_vote = json_decode(json_encode($blog_vote),true);
    
    $checkfavarray = array_keys($favarray);
    $checkcommantarray = array_keys($commentarray);
    $checkblogpartarray = array_keys($blogpartarray);
    ?>  

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">
        <a href="{{ url('profile/bloggroup')}}" class="pull-right" style="font-size: 13px;margin-bottom: 7px; border-radius: 4px; border:1px solid #00877f; padding: 0px 10px 0px 10px; cursor: pointer;">Manage Group</a>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">        
        @if(Session::get('message')!== null)
        <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
        @endif
        </div>

        
		<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12 space20 no-padding">
			 <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12 no-padding margin-t-m-10"><!-- Blog Header Starts -->
				<div class="box box-view no-shadow">
					<div class="box-header-view">
						<h3 class="box-title">Blogs</h3>
						<div class="box-tools pull-right">
							<h5 style="margin-top: 5px;" class="med-orange">{{ $total_record }} Blogs</h5>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header --> 
					<div class="box-body" style=" border-radius: 4px;">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom"> 
							
							@if($total_record == '0')
							<h4>No Blogs Found</h4>
							@else
								@foreach($blogs as $blog)    
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-blogs">
									<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">

										{!! HTML::image('img/profile-pic.jpg',null,['class'=>'  margin-r-20 space10','style'=>'width:50px; margin-top:12px; border-radius:50%; border:2px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
										-webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
										box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:50px;']) !!}

										<h4 style="margin-top: 20px; margin-left: 5px;"><span class="font600" ><a href="javascript:void(0);" data-id="{{ $blog->id }}" class="blog_favourite" id="blog_fav{{ $blog->id }}"><?php echo ($favblogarray[$blog->id] == 1) ? '<i class="fa tooltips fa-star font16 med-orange font600" data-toggle="tooltip" data-title="Remove from favourite"></i>' : '<i class="fa tooltips fa-star-o font16 med-orange font600" data-toggle="tooltip" data-original-title="Add to favourite"></i>' ?></a> @if(in_array($blog->id,$checkfavarray)) <span class="favourite_count{{$blog->id}} font600"> {{ @$favarray[$blog->id] }} </span> @else <span class="favourite_count{{$blog->id}} font600">0</span> @endif</span></h4>

									</div>
									<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">
										<h4 class="med-green space10"><a href="{{ url('profile/blog/'.$blog->id) }}">{{ ucwords($blog->title)  }}</a></h4>
										<h4 class="font14" style="margin-top: -5px;">{{ ucwords(@$blog->user->name) }} - <span class="med-orange font13">{{ App\Http\Helpers\Helpers::dateFormat($blog->updated_at,'datetime') }}</span> <span class="pull-right"><a href="{{ url('profile/blog/'.$blog->id.'/edit') }}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> </a> | <a class="js-delete-confirm" data-text="Are you sure would you like to delete this blog?" href="{{ url('profile/blog/delete/'.$blog->id) }}"><i class="fa fa-trash" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i> </a> </span></h4>


										<p style='margin-bottom: 20px;'>{{ str_limit(strip_tags($blog->description), 350)  }}</p>

										<span class=" font600">Comments (@if(in_array($blog->id,$checkcommantarray)){{$commentarray[$blog->id]}}@else 0 @endif)</span> |
										<span class=" font600">Participants (@if(in_array($blog->id,$checkblogpartarray)){{ @$blogpartarray[$blog->id] }}@else 0 @endif)</span> |


										 <span><a class="js-vote cur-pointer" name="up" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="up{{$blog->id}} fa fa-thumbs-o-up font600 font16 {{ (@$blog_vote[$blog->id]['up']==1)? 'med-orange' : 'med-green' }} " ></i> 
												 <span class="up{{$blog->id}} vote_up{{$blog->id}} font600 {{ (@$blog_vote[$blog->id]['up']==1)? 'med-orange' : 'med-green' }}">{{ $blog->up_count }} Votes</span></a> |</span>
										<span><a class="js-vote cur-pointer" name="down" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="down{{$blog->id}} fa fa-thumbs-o-down font600 font16 {{ (@$blog_vote[$blog->id]['down']==1)? 'med-orange' : 'med-green' }}"  ></i>
											<span class="down{{$blog->id}} vote_down{{$blog->id}} font600 {{ (@$blog_vote[$blog->id]['down']==1)? 'med-orange' : 'med-green' }}">{{ $blog->down_count }} Votes</span>
												</span>
										<a href="{{ url('profile/blog/'.$blog->id) }}" class="pull-right" style="font-size: 13px;margin-bottom: 7px; border-radius: 4px; border:1px solid #00877f; padding: 0px 10px 0px 10px; cursor: pointer;" >Read More</a>
									</div>
								</div>
								@endforeach
							@endif
							
							<div id="results"></div>
							@if($total_page_count>1) 
							<div align="center">
								<button class="load_more btn btn-info" style="margin-top: 20px;" data-checkpage="2" data-totalrecord="{{$total_record }}" data-totalpage="{{$total_page_count }}" id="load_more_button">load More</button>
							<div class="animation_image" style="display:none;"><img src="{{ url('img/ajax-loader.gif') }}"> Loading...</div>
							</div>
							@endif
							
						</div>
						
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div><!-- Blog Header Ends -->
			
		</div>
				
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" style="padding-right:0px;">        
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding">

				<div class="box box-info no-shadow space20">
					<div class="box-header with-border">
						<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Team Members</h3>
						<div class="box-tools pull-right" style="margin-top: 3px;">
							  
						</div>
					</div><!-- /.box-header -->
					<div class="box-body">										
						
						<ul class="no-padding" style="list-style-type: none; line-height: 45px;">
							<li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'online','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Melanie Willams <span class="pull-right" style="font-size: 11px; color:#ccc">Admin</span></li>
							<li>{!! HTML::image('img/del.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Mackenzie George <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
							<li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Melanie Andrews <span class="pull-right" style="font-size: 11px; color:#ccc">Front Desk</span></li>
							<li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'online','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Isabelle Joseph <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
							<li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'online','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Annabelle Violet <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
							<li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'online','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Brooklyn <span class="pull-right" style="font-size: 11px; color:#ccc">Admin</span></li>
							<li>{!! HTML::image('img/del.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Gabriella <span class="pull-right" style="font-size: 11px; color:#ccc">Front Desk</span></li>
							<li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Mackenzie John <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
							<li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Penelope <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>
							<li>{!! HTML::image('img/profile-pic.jpg',null,['class'=>'ideal','style'=>'width:30px; border-radius:50%; height:30px;']) !!} Melanie Andrews <span class="pull-right" style="font-size: 11px; color:#ccc">Doctor</span></li>

						</ul>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>    
		</div>
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->
<!--End-->
@include('practice/layouts/favourite_modal')
@stop