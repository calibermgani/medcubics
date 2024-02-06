@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="user"></i> Profile </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('profile/calendar') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding">
	<div class="col-lg-9 col-md-9 col-xs-8 col-xs-12"><!-- Left side Outer body starts -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding" id="update_event_list">
			<div class="fullcalendar hidden-print" style="background:#fff;">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 calendars hidden-print" style="background:#fff;margin:10px;">
					<header>
						<span class="month med-orange"></span>
						<a class="btn-prev fontawesome-angle-left" href="#"></a>
						<a class="btn-next fontawesome-angle-right" href="#"></a>
						<a class="add_events" data-url="{{url('profile/calendar/events/add')}}" data-backdrop="false" data-toggle="modal" data-target="#form_modal_add" id="add_events" style="float: right;position: relative;top:4px;left: 4px;">
						<i class="fa fa-plus-circle"  data-placement="bottom" data-toggle="tooltip" data-original-title="Add"></i></a>
					</header>
					<table>
						<thead class="event-days">
							<tr></tr>
						</thead>
						<tbody class="event-calendar">
							<tr class="1"></tr>
							<tr class="2"></tr>
							<tr class="3"></tr>
							<tr class="4"></tr>
							<tr class="5"></tr>
						</tbody>
					</table>
				</div>
				
				
				@foreach(@$total_date as $total_date)
					<?php  $time  = strtotime($total_date);
							$event_day = ltrim(date('d',$time), '0');
							$event_month = ltrim(date('m',$time), '0');
							$event_year  = date('Y',$time);
							 ?>
					<div class="parent today day-event hide" date-day="{{$event_day}}" date-month="{{$event_month}}" date-year="{{$event_year}}"></div>
				@endforeach	
				<div class="list" id="list">
				<?php //dd($reminder);?>
					@if(@$reminder) 
					<?php $today_count= 0; ?>
					<?php $total_day_count= 0; ?>
					<?php $total_count= 0; ?>
					@foreach(@$reminder as $reminderr)
					
					<?php $total_count ++ ?>
					@endforeach
					
					<div class="col-lg-12 col-md-12 col-xs-12 col-xs-12" ><!-- Profile Header Starts -->
						<div class="box box-info no-shadow " style="border: 1px solid #ccc">
							<div class="box-body" style="background: #fcfefe; border-radius: 4px;">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">                   
									<h4 class="no-margin med-green" style="border-bottom: 1px dotted #00877f; padding-bottom: 5px;">Calendar <span class="pull-right" style="font-size: 13px; color:#00877f !important">{{ @$total_count }} Events</span></h4> 
									<div id="list_replace">
									@foreach($reminder as $reminders)
									<div id="form-modal-edit{{@$reminders->id}}" class="modal fade in" aria-labelledby="myModalLabel" aria-hidden="true"></div>
									<?php $time  = strtotime($reminders->start_date);
											$time_in_12_hr_format  = date("g:i A", strtotime($reminders->start_time));
											$day = ltrim(date('d',$time), '0');
											$month = ltrim(date('m',$time), '0');
											$month_orgin = ltrim(date('M',$time), '0');
											$year  = date('Y',$time);
											$today_day =ltrim(date("d"), '0');
											$today_month =ltrim(date("m"), '0');
											$today_year =date("Y");
											$date=date('M j', strtotime($reminders->start_date));
											 ?>
									
										@if(($today_day == $day)&&($today_month == $month)&&($today_year == $year))
										<?php $today_count ++;  ?>
										<p class="med-orange" @if(@$today_count == 1) style="display:block;" @else style="display:none;" @endif>Today Events</p>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 js_count_list" id="event_list_{{$reminders->id}}">
											<div class="col-lg-1">
												<h4 class="no-bottom med-orange " style="text-align: center; font-size: 12px; padding-top: 2px; padding-bottom: 2px; border-radius: 4px 4px 0px 0px; background: #f0f0f0; margin-top: 0px;">{{$month_orgin}}</h4>
												<h4 class="no-margin med-orange font16" style=" padding-bottom: 3px; padding-top: 2px; background: #fbf0cb; border-radius: 0px 0px 4px 4px; text-align: center">{{$day}}</h4>
											</div>
											<div class="col-lg-9">
												<p id="title" class="med-green">{{$reminders->title}}</p>
												<p id="content">{{$reminders->description}}</p>
											</div>
											<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 med-orange" style="font-weight:600;text-align:right;">
												<p>
													<a data-toggle="modal" href="#form-modal-edit{{@$reminders->id}}" name="{{$reminders->id}}" class="edit_events"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a>&emsp;
													<a data-toggle="modal" href="#form-modal-delete" name="{{ $reminders->id }}" id="js_delete_confirm" date-day="{{$day}}" class="js-delete-confirm"><i class="fa fa-trash-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
												</p>
												<p>{{$time_in_12_hr_format}}</p>
											</div>
										</div>
										@else
										<?php $total_day_count ++;  ?>
										<p class="med-orange" @if($total_day_count == 1) style="display:block;" @else style="display:none;" @endif>Next one week Events</p>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 js_count_list" id="event_list_{{$reminders->id}}">
											<div class="col-lg-1">
											<h4 class="no-bottom med-orange " style="text-align: center; font-size: 12px; padding-top: 2px; padding-bottom: 2px; border-radius: 4px 4px 0px 0px; background: #f0f0f0; margin-top: 0px;">{{$month_orgin}}</h4>
											<h4 class="no-margin med-orange font16" style=" padding-bottom: 3px; padding-top: 2px; background: #fbf0cb; border-radius: 0px 0px 4px 4px; text-align: center">{{$day}}</h4>
											</div>
											<div class="col-lg-9">
											<p id="title" class="med-green">{{$reminders->title}}</p>
											<p id="content">{{$reminders->description}}</p>
											</div>
											<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 med-orange" style="font-weight:600;text-align:right;">
											<p>
												<a data-toggle="modal" href="#form-modal-edit{{@$reminders->id}}" name="{{$reminders->id}}" class="edit_events"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a>&emsp;
												<a data-toggle="modal" href="#form-modal-delete" name="{{ $reminders->id }}" id="js_delete_confirm" date-day="{{$day}}" class="js-delete-confirm"><i class="fa fa-trash-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
											</p>
											<p>{{$time_in_12_hr_format}}</p>
											</div>
										</div>
										@endif
									@endforeach
									</div>
								</div>
							</div>
						</div><!-- /.box-body -->
					</div><!-- /.box -->
					@else
					<div class="col-lg-12 col-md-12 col-xs-12 col-xs-12" ><!-- Profile Header Starts -->
						<div class="box box-info no-shadow " style="border: 1px solid #ccc">
							<div class="box-body" style="background: #fcfefe; border-radius: 4px;">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">                   
									<h4 class="no-margin med-green" style="border-bottom: 1px dotted #00877f; padding-bottom: 5px;">Calendar <span class="pull-right" style="font-size: 13px; color:#00877f !important">0 Events</span></h4> 
									<p class="med-orange" style="display:block;" >No Events</p>
								</div>
							</div><!-- /.box-body -->
						</div> <!-- /.box -->
					</div><!-- Profile Header Ends -->
					@endif
				</div>
			</div>
		</div>
	</div>  
	<div class="col-lg-3 col-md-3 col-sm-6  col-xs-12" style="padding-left: 0px;">
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
</div>
	<!--End-->
<div id="form-modal-delete" class="modal fade in">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">Are you sure would you like to delete this contact?</div>
			<div class="modal-footer">
				<button class="btn btn-medcubics-small"  name="" date-day="" data-dismiss="modal" id="delete_conformation_link">Yes</button>
				<button  class="cancel btn btn-medcubics-small" type="button" data-dismiss="modal">Cancel</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->  
				
<div id="form_modal_add" class="modal fade in"> 
	
</div><!-- Left side Outer body Ends -->
@stop