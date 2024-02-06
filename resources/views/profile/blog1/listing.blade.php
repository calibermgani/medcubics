@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="user"></i> Profile </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')

<div class="col-lg-9 col-md-9 col-xs-8 col-xs-12" style="margin-top:-10px;"><!-- Left side Outer body starts -->
    
    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12"><!-- Profile Header Starts -->
        <div class="box box-info no-shadow" style="border: 1px solid #85E2E6">
            <div class="box-body" style="margin-bottom: -7px;">


                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 tab-border-bottom">
                    {!! HTML::image('img/profile-pic.jpg',null,['class'=>'  margin-r-20 space10','style'=>'width:90px; margin-top:12px; border-radius:50%; border:3px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                    -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                    box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:90px; float:left;']) !!}

                    <h5 class="med-green no-margin">Sophia, Mortaza</h5>
                    <h5 class=""><span class="med-orange sm-size"><i class="med-orange med-gender fa fa-female margin-r-5 "></i> Feb 12,1975, 41 years</span></h5>                
                    <h5 class="space20" style="margin-top: 30px;">1001 W Fayette St, Suite 400,</h5>
                    <h5>Syracuse - NY, 13204 - 2859</h5>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 med-left-border">
                    <ul class="icons push no-padding">					
                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green">Phone : </span>(315) 472-1488</li>

                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green">Mobile : </span>(415) 682-4234</li>

                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green">Email : </span><a href="" class="med-orange">sophiya@gmail.com</a></li>

                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12 space10">
                            <a href=""><i class="fa fa-facebook-square font20 facebook"></i></a> 
                            <a href=""><i class="fa fa-twitter-square font20 margin-l-5 twitter"></i></a> 
                            <a href=""><i class="fa fa-linkedin-square font20 margin-l-5 linkedin"></i></a>
                            <a href=""><i class="fa fa-google-plus-square font20 margin-l-5 gplus"></i></a>
                        </li>					                    
                    </ul>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- Profile Header Ends -->
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  style="margin-top: -30px; ">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 ">
            <div class="med-profile">
                <i class="fa fa-calendar"></i>
                <h5 class="profile-heading">Calendar</h5>
            </div>                        
        </div>
        
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
            <div class="med-profile">
               <i class="fa fa-tasks"></i>
                <h4 class="profile-heading">Tasks</h4>
            </div>            
            
        </div>
        
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
            <div class="med-profile">
                <i class="fa fa-envelope"></i>
                 <h4 class="profile-heading">Messages</h4>
            </div>                       
        </div>
        
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
            <div class="med-profile">               
                <i class="fa fa-comments-o @if($selected_tab == 'blog_listing') active @endif"></i>
                <a href="{{ url() }}/profile/blogs"> <h4 class="profile-heading">Blogs</h4></a>
            </div>                         
        </div>
        
    </div>
    <?php 
		$favarray = json_decode(json_encode($favcountarray),true); 
		$commentarray = json_decode(json_encode($commentcountarray),true);
		$blogpartarray = json_decode(json_encode($blogpartcountarray),true);
		$favblogarray = json_decode(json_encode($favblogarray),true);
		$blog_vote = json_decode(json_encode($blog_vote),true);
		
		$checkfavarray = array_keys($favarray);
		$checkcommantarray = array_keys($commentarray);
		$checkblogpartarray = array_keys($blogpartarray);
    ?>
    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12"  ><!-- Blog Header Starts -->
        <div class="box box-info no-shadow" style="border:1px solid #00877f;">
             <div class="box-body" style="background: #bff6f3; border-radius: 4px;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom"> 
                    <h4 class="no-margin med-green" style="padding-bottom: 5px; border-bottom: 1px dotted #00877f;">Blogs 
                        <span class="pull-right" style="font-size: 13px; color:#00877f !important">  <a href="{{ url('profile/blog/create') }}"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="Add Blog"></i></a> {{ $total_record }} Blogs</span></h4>
                @if($total_record == '0')
                         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom" style="border-bottom: 1px dotted #00877f;">
                           No Blogs Found
                        </div>
                @else    
                    @foreach($blogs as $blog)  
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom" style="border-bottom: 1px dotted #00877f;">
                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-3">
						<?php 
							$img_details = [];
							$img_details['module_name']='user';
							$img_details['file_name']=$blog->title;
							$img_details['practice_name']="";
							
							$img_details['class']='margin-r-20 space10';
							$img_details['style']='width:50px; border-radius:50%; border:2px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                            -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                            box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:50px; float:left;';
							$img_details['alt']='blog-image';
							$image_tag = app\http\helpers\helpers::checkandgetavatar($img_details);
						?>	
						{!! $image_tag !!}		
                       </div>
                        
                        <div class="col-lg-11 col-md-11 col-sm-9 col-xs-9 no-bottom">
                            <h4 class="med-green"><a href="{{ url('profile/blog/'.$blog->id) }}">{{ ucwords($blog->title) }}</a></h4>
                            <span>By {{ ucwords(@$blog->user->name) }}</span>
                            <span class="space-m-t-7 med-orange pull-right">{{ App\Http\Helpers\Helpers::dateFormat($blog->updated_at,'datetime') }}</span>
                            <p>{{ str_limit(strip_tags($blog->description), 220)  }}</p>
                            
                            <span class="med-green">Comments (@if(in_array($blog->id,$checkcommantarray)){{$commentarray[$blog->id]}}@else 0 @endif)</span> |
                            
                            <span class="med-green">Participants (@if(in_array($blog->id,$checkblogpartarray)){{ @$blogpartarray[$blog->id] }}@else 0 @endif)</span> |
                           
                            <a href="javascript:void(0);" data-id="{{ $blog->id }}" class="blog_favourite" id="blog_fav{{ $blog->id }}"><?php echo ($favblogarray[$blog->id] == 1) ? '<i class="fa tooltips fa-star font16 med-orange font600" data-toggle="tooltip" data-title="Remove from favourite"></i>' : '<i class="fa tooltips fa-star-o font16 med-orange font600" data-toggle="tooltip" data-original-title="Add to favourite"></i>' ?></a>@if(in_array($blog->id,$checkfavarray))<span class="favourite_count{{$blog->id}} font600">{{ @$favarray[$blog->id] }}  </span> @else <span class="favourite_count{{$blog->id}} font600">0</span> @endif
                            
                            | <span> <a class="js-vote cur-pointer" name="up" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="up{{ $blog->id }} fa fa-thumbs-o-up font600 font16 {{ (@$blog_vote[$blog->id]['up']==1)? 'med-orange' : 'med-green' }} " ></i> 
                                         <span class="up{{ $blog->id }} vote_up{{ $blog->id }} font600 {{ (@$blog_vote[$blog->id]['up']==1)? 'med-orange' : 'med-green' }}">{{ $blog->up_count }} Votes</span></a> </span> |
                                <span><a class="js-vote cur-pointer" name="down" data-id="{{ $blog->id }}" data-toggle="tooltip" data-original-title="Add vote"><i class="down{{ $blog->id }} fa fa-thumbs-o-down font600 font16 {{ (@$blog_vote[$blog->id]['down']==1)? 'med-orange' : 'med-green' }}"  ></i>
                                    <span class="down{{ $blog->id }} vote_down{{ $blog->id }} font600 {{ (@$blog_vote[$blog->id]['down']==1)? 'med-orange' : 'med-green' }}">{{ $blog->down_count }} Votes</span>
                                        </span>
                            
                            
                            
                            <a href="{{ url('profile/blog/'.$blog->id) }}" class="add-btn pull-right" style="margin-bottom:5px; font-size: 12px; margin-top: -10px;" >Know More</a>
                        </div>
                    </div>
                    @endforeach
                @endif    
                    
                    <div id="results"></div>
                    @if($total_page_count>1) 
                    <div align="center">
                        <button class="load_more btn btn-info" style="margin-top: 20px;" data-checkpage="1" data-totalrecord="{{$total_record }}" data-totalpage="{{$total_page_count }}" id="load_more_button">load More</button>
                    <div class="animation_image" style="display:none;"><img src="{{ url('img/ajax-loader.gif') }}"> Loading...</div>
                    </div>
                    @endif
                </div>
               
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- Blog Header Ends -->
    
    
</div><!-- Left side Outer body Ends -->
<div class="col-lg-3 col-md-3 col-sm-6  col-xs-12" style="padding-left: 0px;">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding">
    <div id="dob" style="margin-top:-3px;" ><div class="ui-datepicker-inline ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="display: block; background: #fff; border-radius: 4px; border:1px solid #85E2E6 !important"><div class="ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all"><a class="ui-datepicker-prev ui-corner-all" data-handler="prev" data-event="click" title="Prev"><span class="ui-icon ui-icon-circle-triangle-w">Prev</span></a><a class="ui-datepicker-next ui-corner-all" data-handler="next" data-event="click" title="Next"><span class="ui-icon ui-icon-circle-triangle-e">Next</span></a><div class="ui-datepicker-title"><span class="ui-datepicker-month">January</span>&nbsp;<span class="ui-datepicker-year">2016</span></div></div><table class="ui-datepicker-calendar"><thead><tr><th scope="col" class="ui-datepicker-week-end"><span title="Sunday">Sun</span></th><th scope="col"><span title="Monday">Mon</span></th><th scope="col"><span title="Tuesday">Tue</span></th><th scope="col"><span title="Wednesday">Wed</span></th><th scope="col"><span title="Thursday">Thu</span></th><th scope="col"><span title="Friday">Fri</span></th><th scope="col" class="ui-datepicker-week-end"><span title="Saturday">Sat</span></th></tr></thead><tbody><tr><td class=" ui-datepicker-week-end ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">1</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">2</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">3</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">4</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">5</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">6</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">7</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">8</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">9</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">10</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">11</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">12</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">13</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">14</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">15</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">16</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">17</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">18</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">19</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">20</a></td><td class=" ui-datepicker-days-cell-over  ui-datepicker-current-day ui-datepicker-today" data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default ui-state-highlight ui-state-active ui-state-hover" href="#">21</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">22</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">23</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">24</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">25</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">26</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">27</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">28</a></td><td class=" " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">29</a></td><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">30</a></td></tr><tr><td class=" ui-datepicker-week-end " data-handler="selectDay" data-event="click" data-month="0" data-year="2016"><a class="ui-state-default" href="#">31</a></td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-week-end ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td></tr></tbody></table></div></div>
</div>     


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 weather-bg space10">
    <h5 style="color:#fff; text-align: center">Today's Weather</h5>
    <h4 style="color:#fff; text-align: center">32 &#8451;</h4>
    <h5 style="color:#fff;">24<sup>&#111;</sup><span style="font-size:10px;">min</span><span class="pull-right">34<sup>&#111;</sup><span style="font-size:10px;">max</span></span></h5>
    <h5>{!! HTML::image('img/weather.jpg',null,['class'=>'img-responsive']) !!}</h5>
    <h5 id="time" style="color:#fff; text-align: center"></h5>
</div>

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
<!--End-->
@stop