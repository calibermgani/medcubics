@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="calendar"></i> Calendar </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('profile/calendar') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 no-padding"><!-- Left side Outer body starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" id="update_event_list">
                    <div class="fullcalendar hidden-print">
                        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 calendars hidden-print no-padding">
                            <header>
                                <span class="month med-orange"></span>
                                <a class="btn-prev" href="#"><i class="fa fa-arrow-circle-left"></i></a>
                                <a class="btn-next" href="#"><i class="fa fa-arrow-circle-right"></i></a>

                            </header>
                            <table style="background: #fff; border-radius: 0px 0px 4px 4px; border:1px solid #c8f4f1; border-top:0px;">
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
							@if($checkpermission->check_url_permission('profile/calendar/event/create') == 1)
                            <button class="add_events btn btn-medcubics line-height-26 margin-t-5" data-url="{{url('profile/calendar/events/add')}}" data-backdrop="false" data-toggle="modal" data-target="#form_modal_add" id="add_events">
                                <i class="fa fa-plus-circle"  data-placement="bottom" data-toggle="tooltip" data-original-title="Add"></i> Add Event</button>
							@endif
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                            <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12 no-padding"><!-- Blog Header Starts -->
                                <div class="box box-view no-shadow">
                                    <div class="box-header-view">
                                        <h3 class="box-title">Today's Event</h3>
                                        <div class="box-tools pull-right">
                                            <h5 class="margin-t-5 med-orange"></h5>
                                        </div><!-- /.box-tools -->
                                    </div><!-- /.box-header --> 
                                    <div class="box-body border-radius-4" style=" min-height:220px; max-height:220px; overflow-y:scroll">
                                        <?php //dd($reminder); ?>
										@if($today_events)
                                        @foreach($today_events as $today_events)
                                        <div id="js_delete_list{{@$reminder_more->id}}" class="js_delete_list">
                                            <div id="form-modal-edit{{@$today_events->id}}" class="modal fade in col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5" aria-labelledby="myModalLabel" aria-hidden="true"></div>
                                            <?php 
												$time  = strtotime(@$today_events->start_date);
												$time_in_12_hr  = date("g:i", strtotime(@$today_events->start_time));
												$time_in_12_hr_zone  = date("A", strtotime(@$today_events->start_time));
												$day = ltrim(date('d',$time), '0');
												$month = ltrim(date('m',$time), '0');
												$month_orgin = ltrim(date('M',$time), '0');
												$year  = date('Y',$time);
												$date=date('M j', strtotime(@$today_events->start_date));
											?>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 green-border-bottom js_count_list" id="event_list_{{$today_events->id}}">
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-sx-12">
                                                    <p class="med-orange-light font600 "><span class="font20">{{$time_in_12_hr}}</span> {{$time_in_12_hr_zone}}</p> 
                                                </div>
                                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                                    <p id="title" class="med-green font600">{{@$today_events->title}}
                                                        <span class="pull-right">
														@if($checkpermission->check_url_permission('profile/calendar/event/update/{id}') == 1)
                                                            <a data-toggle="modal" href="#form-modal-edit{{@$today_events->id}}" name="{{@$today_events->id}}" class="edit_events"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a>&emsp;
														@endif
														@if($checkpermission->check_url_permission('profile/calendar/event/delete/{id}') == 1)
                                                            <a data-toggle="modal" href="#form-modal-delete" name="{{ @$today_events->id }}" id="js_delete_confirm" date-day="{{$day}}" class="js-delete-confirm" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
														@endif
                                                        </span>
                                                    </p>
                                                    <p id="content" class="margin-t-m-10">{{@$today_events->description}}</p>
                                                </div> 
                                            </div> 
                                        </div> 
                                        @endforeach
                                        @endif
                                    </div>  
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- Blog Header Ends -->
                    </div>
                    @foreach(@$total_date as $total_date)
                    <?php  
						$time  = strtotime($total_date);
						$event_day = ltrim(date('d',$time), '0');
						$event_month = ltrim(date('m',$time), '0');
						$total_day_count = 0; 
						$total_count = 0; 
						$event_year  = date('Y',$time);
					?>
                    <div class="parent today day-event hide" date-day="{{$event_day}}" date-month="{{$event_month}}" date-year="{{$event_year}}"></div>
                    @endforeach	
                    @foreach(@$reminder as $reminderr)
                    @foreach($reminderr as $reminder_total)
                    <?php $total_count ++ ?>
                    @endforeach
                    @endforeach
                    <div class="col-lg-12 col-md-12 col-xs-12 col-xs-12 no-padding margin-t-10 p-l-0"><!-- Profile Header Starts -->
                        <div class="box box-info no-shadow no-border "  id="js_list">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">                   
                                <h4 class="med-green" style="border-bottom: 1px dotted #00877f; padding-bottom: 5px; margin-top:0px; margin-bottom:10px;">Calendar <span style="font-size:14px; color:#f07d08 !important" @if(@$total_day_count == 1) @else style="display:none;" @endif> - This week Events</span><span class="pull-right" style="font-size: 13px; color:#00877f !important; margin-top:5px;">{{ @$total_count }} Events</span></h4> 

                                <div id="js_list_replace">
                                    @foreach($reminder as $reminders)
                                    <?php $total_day_count ++;  ?>
                                    <?php $array_count = 0;  ?>

                                    <div class="box box-view no-shadow">
                                        @foreach($reminders as $reminder_more)
                                        <?php 
											$time  = strtotime(@$reminder_more->start_date);
											$time_in_12_hr  = date("g:i", strtotime(@$reminder_more->start_time));
											$time_in_12_hr_zone  = date("A", strtotime(@$reminder_more->start_time));
											$day = ltrim(date('d',$time), '0');
											$week_day = ltrim(date('l',$time), '0');
											$month = ltrim(date('m',$time), '0');
											$month_orgin = ltrim(date('M',$time), '0');
											$year  = date('Y',$time);
											$date=date('M j', strtotime(@$reminder_more->start_date));
											$array_count ++;
										?>
                                        <div id="js_delete_list{{@$reminder_more->id}}" class="js_delete_list">
                                            <div id="form-modal-edit{{@$reminder_more->id}}" class="modal fade in" aria-labelledby="myModalLabel" aria-hidden="true"></div>
                                            <div class="box-header-view margin-b-10 @if($array_count >1) hide @else show @endif">
                                                <h3 class="box-title">{{$day}} {{$month_orgin}}</h3>
                                                <div class="box-tools pull-right">
                                                    <h5 class="margin-t-5 med-orange"> {{ $week_day}}</h5>
                                                </div><!-- /.box-tools -->
                                            </div><!-- /.box-header --> 
                                            <div class="box-body margin-t-m-10">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 green-border-bottom js_count_list " id="event_list_{{@$reminder_more->id}}" >

                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                                                        <p class="med-orange-light font600 "><span class="font20">{{$time_in_12_hr}}</span> {{$time_in_12_hr_zone}}</p>
                                                    </div>
                                                    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-8">
                                                        <p id="title" class="med-green font14 font600">{{$reminder_more->title}}</p>
                                                        <p id="content" class="margin-t-m-10">{{@$reminder_more->description}}</p>
                                                    </div>
                                                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 med-orange">
                                                        <p class=" pull-right">
														@if($checkpermission->check_url_permission('profile/calendar/event/update/{id}') == 1)
                                                            <a data-toggle="modal" href="#form-modal-edit{{@$reminder_more->id}}" name="{{@$reminder_more->id}}" class="edit_events"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a>&emsp;
														@endif
														@if($checkpermission->check_url_permission('profile/calendar/event/delete/{id}') == 1)
                                                            <a data-toggle="modal" href="#form-modal-delete" name="{{ @$reminder_more->id }}" id="js_delete_confirm" date-day="{{$day}}" class="js-delete-confirm"><i class="fa fa-trash-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
														@endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6  col-xs-12 margin-t-m-13 p-l-0 p-r-0">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding">
                    <div class="box box-info no-shadow space20">
                        <div class="box-header with-border">
                            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Team Members</h3>
                           
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
    </div>
</div>
<!--End-->
<div id="form-modal-delete" class="modal fade in">
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h4 class="modal-title">Alert</h4></div>
            <div class="modal-body text-center med-green font600">Are you sure would you like to delete this contact?</div>
            <div class="modal-footer">
                <button class="btn btn-medcubics-small width-60"  name="" date-day="" data-dismiss="modal" id="delete_conformation_link">Yes</button>
                <button  class="cancel btn btn-medcubics-small width-60" type="button" data-dismiss="modal">No</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->  

<div id="form_modal_add" class="modal fade in"> 

</div><!-- Left side Outer body Ends -->
@stop
@push('view.scripts') 
@endpush