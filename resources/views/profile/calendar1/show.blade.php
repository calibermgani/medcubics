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
               <a href="{{url('profile/calendar/show')}}"> <i class="fa fa-calendar js_profile_calendar"></i>
                <h5 class="profile-heading js_profile_calendar">Calendar</h5></a>
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
                <i class="fa fa-comments-o"></i>
                <a href=""> <h4 class="profile-heading">Blogs</h4></a>
            </div>                         
        </div>
        
    </div>
    <div class="calendar_update" ><!-- Calendar Update Starts -->
    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12" ><!-- Profile Header Starts -->
        <div class="box box-info no-shadow " style="border: 1px solid #ccc">
            <div class="box-body" style="background: #fcfefe; border-radius: 4px;">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">                   
                    <h4 class="no-margin med-green" style="border-bottom: 1px dotted #00877f; padding-bottom: 5px;">Calendar <span class="pull-right" style="font-size: 13px; color:#00877f !important">3 Events</span></h4>                                    
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10">
                    <div class="col-lg-1" style="">
                            <h4 class="no-bottom med-green " style="text-align: center; font-size: 12px; border-radius: 4px 4px 0px 0px; padding-top: 2px; padding-bottom: 2px; background: #f0f0f0; margin-top: 0px;">Jan</h4>
                            <h4 class="no-margin med-green font16" style=" padding-bottom: 3px; padding-top: 2px; background: #e7fbcb; border-radius: 0px 0px 4px 4px; text-align: center">23</h4>
                        </div>
                    <div class="col-lg-10">
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has</p>
                    </div>
                        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 med-orange" style="font-weight: 600">09:35</div>
                    </div>
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10">
                    <div class="col-lg-1">
                            
                             <h4 class="no-bottom med-orange " style="text-align: center; font-size: 12px; padding-top: 2px; padding-bottom: 2px; border-radius: 4px 4px 0px 0px; background: #f0f0f0; margin-top: 0px;">Jan</h4>
                            <h4 class="no-margin med-orange font16" style=" padding-bottom: 3px; padding-top: 2px; background: #fbf0cb; border-radius: 0px 0px 4px 4px; text-align: center">23</h4>
                    </div>
                    <div class="col-lg-10">
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has Lorem Ipsum has Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    </div>
                        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 med-orange" style="font-weight: 600">12:15</div>
                    </div>
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                        <div class="col-lg-1" style="">
                            <h4 class="no-bottom med-green " style="text-align: center; font-size: 12px; border-radius: 4px 4px 0px 0px; padding-top: 2px; padding-bottom: 2px; background: #f0f0f0; margin-top: 0px;">Jan</h4>
                            <h4 class="no-margin med-green font16" style=" padding-bottom: 3px; padding-top: 2px; background: #e7fbcb; border-radius: 0px 0px 4px 4px; text-align: center">23</h4>
                        </div>
                    <div class="col-lg-10">
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has Lorem Ipsum is simply dummy text of the printing and typesetting industry</p>
                    </div>
                        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 med-orange" style="font-weight: 600">18:30</div>
                    </div>
                </div>
                
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- Profile Header Ends -->
    
    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12" style="margin-top:-13px;"><!-- Tasks Header Starts -->
        <div class="box box-info no-shadow " style="border: 1px solid #00877f">
            <div class="box-body" style="background: #08b1a9; border-radius: 4px;">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">                   
                    <h4 class="no-margin" style="border-bottom: 1px dotted #fff; padding-bottom: 5px; color:#fff">Tasks <span class="pull-right" style="font-size: 13px">4 Tasks</span></h4>                                    
                    <h5 style="padding-bottom:2px; margin-left: 20px; color:#fff; "> <i class="fa fa-plus font10"></i> Send last bill to the insurance company Cigna</h5>
                    <h5 style="padding-bottom:2px; margin-left: 20px; color:#fff;"> <i class="fa fa-plus font10"></i>  Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has</h5>
                    <h5 style="padding-bottom:2px; margin-left: 20px; color:#fff;"> <i class="fa fa-plus font10"></i>  Get a book on how to create a new practice from Medcubics</h5>
                    <h5 style="padding-bottom:2px; margin-left: 20px; color:#fff;"> <i class="fa fa-plus font10"></i>  Send statement to Sophiya</h5>
                </div>
                
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- Tasks Header Ends -->
    
    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12" style="margin-top:-13px;" ><!-- Message Header Starts -->
        <div class="box box-info no-shadow no-border" style="background: #fcfefe;">
            <div class="box-body table-responsive profile" style="border: 1px solid #ccc; border-radius: 4px;">
                <h4 class="no-margin med-green" style="border-bottom: 1px dotted #00877f; padding-bottom: 5px;">Message <span class="pull-right" style="font-size: 13px; color:#00877f !important">4 Messages</span></h4>            
                <table id="example2" class="table">	

                    
                    <tbody>
                        <tr>
                            <td>03-28-2015</td>
                            <td>Empire Blue</td>                               
                            <td>Get a book on how to create a new practice</td>                                
                            <td class="med-green">06:40</td>                      
                        </tr>

                        <tr>
                            <td>01-13-2016</td>
                            <td>Willams</td>                               
                            <td>how to create a new Facility</td>                                
                            <td class="med-green">07:32</td>                      
                        </tr>
                        <tr>
                            <td>01-26-2016</td>
                            <td>George</td>                               
                            <td>Statement Sent</td>                                
                            <td class="med-green">07:32</td>                      
                        </tr>
                        <tr>
                            <td>03-28-2015</td>
                            <td>Empire Blue</td>                               
                            <td>Get a book on how to create a new practice</td>                                
                            <td class="med-green">16:26</td>                      
                        </tr>


                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- Message Header Ends -->
 
    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12"  style="margin-top:-13px;"><!-- Blog Header Starts -->
        <div class="box box-info no-shadow" style="border:1px solid #00877f;">
             <div class="box-body" style="background: #bff6f3; border-radius: 4px;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom"> 
                    <h4 class="no-margin med-green" style="padding-bottom: 5px; border-bottom: 1px dotted #00877f;">Blogs <span class="pull-right" style="font-size: 13px; color:#00877f !important">4 Messages</span></h4>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom" style="border-bottom: 1px dotted #00877f;">
                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-3">
                            {!! HTML::image('img/profile-pic.jpg',null,['class'=>'margin-r-20 space10','style'=>'width:50px; border-radius:50%; border:2px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                            -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                            box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:50px; float:left;']) !!}
                        </div>
                        
                        <div class="col-lg-11 col-md-11 col-sm-9 col-xs-9 no-bottom">
                            <h4 class="med-green">Heading of the blog goes here</h4>
                            <h6 class="space-m-t-7 med-orange"><i class="fa fa-clock-o"></i> 12-12-2015 | 08:20</h6>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley.</p>
                            <a href="" class="add-btn pull-right" style="margin-bottom:5px; font-size: 12px; margin-top: -10px;" >Know More</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px dotted #00877f;">
                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-3">
                            {!! HTML::image('img/del.jpg',null,['class'=>'margin-r-20 space10','style'=>'width:50px; border-radius:50%; border:2px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                            -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                            box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:50px; float:left;']) !!}
                        </div>
                        
                        <div class="col-lg-11 col-md-11 col-sm-9 col-xs-9">
                            <h4 class="med-green">Heading of the blog goes here</h4>
                            <h6 class="space-m-t-7 med-orange"><i class="fa fa-clock-o"></i> 12-12-2015 | 08:20</h6>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown.</p>
                            <a href="" class="add-btn pull-right" style="margin-bottom:5px; font-size: 12px; margin-top: -10px;" >Know More</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-3">
                            {!! HTML::image('img/profile-pic.jpg',null,['class'=>'margin-r-20 space10','style'=>'width:50px; border-radius:50%; border:2px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                            -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                            box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:50px; float:left;']) !!}
                        </div>
                        
                        <div class="col-lg-11 col-md-11 col-sm-9 col-xs-9">
                            <h4 class="med-green">Heading of the blog goes here</h4>
                            <h6 class="space-m-t-7 med-orange"><i class="fa fa-clock-o"></i> 12-12-2015 | 08:20</h6>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown.</p>
                            <a href="" class="add-btn pull-right" style="margin-bottom:5px; font-size: 12px; margin-top: -10px;" >Know More</a>
                        </div>
                    </div>
                </div>
                
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- Blog Header Ends -->
    </div><!-- Calendar Update Ends -->
    
    
</div><!-- Left side Outer body Ends -->
<div class="col-lg-3 col-md-3 col-sm-6  col-xs-12" style="padding-left: 0px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding">
		<div class="container">
			<div class="row">
				<div class="col-md-3" style="background:#fff;">
					<div class="calendar hidden-print">
						<header>
							<span class="month"></span>
							<a class="btn-prev fontawesome-angle-left ui-datepicker-next ui-corner-all" href="#"></a>
							<a class="btn-next fontawesome-angle-right" href="#"></a>
						</header>
						<table>
							<thead class="event-days">
								<tr></tr>
							</thead>
							<tbody class="event-calendar">
								<tr class="1" ></tr>
								<tr class="2" ></tr>
								<tr class="3"></tr>
								<tr class="4"></tr>
								<tr class="5"></tr>
							</tbody>
						</table>
						<div class="list" id="list">
							<?php $day_count=0; ?>
							
							@if(@$reminder)
								@foreach($reminder as $reminder)
								<?php $time  = strtotime($reminder->start_date);
										$time_in_12_hr_format  = date("g:i A", strtotime($reminder->start_time));
										$day = ltrim(date('d',$time), '0');
										$month = ltrim(date('m',$time), '0');
										$year  = date('Y',$time);
										$today_day =ltrim(date("d"), '0');
										$today_month =ltrim(date("m"), '0');
										$today_year =date("Y");
										$date=date('M j', strtotime($reminder->start_date));
										 ?>
									@if(($today_day == $day)&&($today_month == $month)&&($today_year == $year))
									<?php $day_count ++;  ?>
									<p id="today_name" class="med-orange center" @if($day_count == 1) style="display:bolck;" @else style="display:none;" @endif>Today Events</p>
									<div id="id{{$reminder->id}}" class="parent today day-event" date-day="{{$day}}" date-month="{{$month}}" date-year="{{$year}}"  data-number="2" style="display:block;">
										<div class="show_{{$reminder->reminder_type}}">
											<p class="med-green"><span id="type" style="background: #F9EFD3; padding: 2px 6px; color:#D98400">{{$reminder->reminder_type}}</span><kbd class="med-green">{{ $date }}</kbd><kbd class="med-orange"> &emsp;Time :{{$time_in_12_hr_format}}</kbd>
											<p id="title">{{$reminder->title}}</p>
											<p id="content">{{$reminder->description}}</p>
										</div>
									</div>
									@else
									<div id="id{{$reminder->id}}" class="parent day-event" date-day="{{$day}}" date-month="{{$month}}" date-year="{{$year}}"  data-number="2" style="display:none;">
										<div class="show_{{$reminder->reminder_type}}">
											<p class="med-green"><span id="type" style="background: #F9EFD3; padding: 2px 6px; color:#D98400">{{$reminder->reminder_type}}</span><kbd class="med-green">{{ $date }}</kbd><kbd class="med-orange"> &emsp;Time :{{$time_in_12_hr_format}}</kbd>
											<p id="title" class="med-green">{{$reminder->title}}</p>
											<p id="content">{{$reminder->description}}</p>
										</div>
									</div>
									@endif
								@endforeach
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
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
				<div class="box-tools pull-right" style="margin-top: 3px;"> </div>
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