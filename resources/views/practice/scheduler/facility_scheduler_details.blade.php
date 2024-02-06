@extends('admin')

@section('toolbar')
<div class="row toolbar-header" >
	<?php $encode_facility_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'encode'); ?>	
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.scheduler')}} font14"></i> Scheduler Preference <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Facility <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> List <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('facilityscheduler/facility/'.$encode_facility_id) }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a>
            </li>	
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('practice/scheduler/facility_tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  margin-t-10">
    <div class="box box-info no-shadow">

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title"> Schedule Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive p-b-12">
                    <table class="table-responsive table-striped-view table">                    
                        <tbody>
                            <tr>
								
                                <td>Provider</td>
                                <td>{{@$facilityschedulers->provider->provider_name.' '.@$facilityschedulers->provider->degrees->degree_name}}</td>
                            </tr>
                            <tr>
                                <td>Start Date</td>
                                <td><span @if(@$facilityschedulers->start_date !='')class="bg-date"@endif>{{ App\Http\Helpers\Helpers::dateFormat($facilityschedulers->start_date,'date') }}</span></td>
                            </tr>
                            <tr>
                                <td>End Date</td>
                                <td>
                                    @if($facilityschedulers->end_date_option != 'never') 
                                    <span @if(@$facilityschedulers->end_date !='')class="bg-date"@endif>
                                        {{ App\Http\Helpers\Helpers::dateFormat($facilityschedulers->end_date,'date') }}
                                    </span>
                                    @else 
                                    Never
                                    @endif
                                </td>                             
                            </tr>
                            <tr>
                                <td>End Date Option</td>
                                <td><span class="patient-status-bg-form @if(ucfirst($facilityschedulers->end_date_option)) label-success @else label-danger @endif">{{ucfirst($facilityschedulers->end_date_option)}}</span></td>                             
                            </tr>
                            
                            <tr>
                                <td>No of Occurrence</td>
                                <td>@if($facilityschedulers->end_date_option == 'after')<span class="bg-number">{{$facilityschedulers->no_of_occurrence}}</span> @else <span class="nill"> - Nil -</span> @endif</td>                             
                            </tr>
                            <tr>
                                <td>Repeat Every</td>
                                <td>
                                    @if($facilityschedulers->repeat_every > 1){{$facilityschedulers->repeat_every}} @endif 
                                    @if($facilityschedulers->schedule_type == 'Daily')Days 
                                    @elseif($facilityschedulers->schedule_type == 'Weekly')Weeks 
                                    @elseif($facilityschedulers->schedule_type == 'Monthly')Months 
                                    @endif
                                </td>                             
                            </tr>
                            <tr>
                                <td>Schedule Type</td>
                                <td>{{$facilityschedulers->schedule_type}}</td>                             
                            </tr>
                        </tbody>
                    </table>
                    @if($facilityschedulers->schedule_type == 'Weekly')
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-13">&emsp;</div>
                    @endif
                    
                    @if($facilityschedulers->schedule_type == 'Monthly')
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5">&emsp;
                        <p class="margin-t-8">&emsp;</p>
                    </div>                     
                    @endif
                     
                </div><!-- /.box-body -->
            </div>
            <!-- Hided For 1st Version -->
            <div class="box box-view no-shadow hide"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="alarm"></i> <h3 class="box-title">Reminder</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive p-b-20">
                    <table class="table-responsive table-striped-view table">                    
                        <tbody>
                            <tr>
                                <td class="font600"></td>
                                <td class="font600 med-green">SMS</td>                             
                                <td class="font600 med-green">Phone</td> 
                                <td class="font600 med-green">Email</td>                             
                            </tr>
                            <tr>
                                <td>Remind Me</td>
                                <td> @if($facilityschedulers->provider_reminder_sms == 'off') <span class="patient-status-bg-form label-danger"> No<span> @else <span class="patient-status-bg-form label-success">Yes </span>@endif</td>                             
                                <td> @if($facilityschedulers->provider_reminder_phone == 'off') <span class="patient-status-bg-form label-danger"> No<span> @else <span class="patient-status-bg-form label-success">Yes </span>@endif</td>                             
                                <td> @if($facilityschedulers->provider_reminder_email == 'off') <span class="patient-status-bg-form label-danger"> No<span> @else <span class="patient-status-bg-form label-success">Yes </span>@endif</td>                             
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div> 
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20">
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="calendar"></i> <h3 class="box-title">{{$facilityschedulers->schedule_type}} Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive  p-b-15">
                    <table class="table-responsive table-striped-view table">                    
                        <tbody>
                            @if($facilityschedulers->schedule_type == 'Weekly')
                            <tr>
                                <td>Weekly Visit</td>
                                <td>{{ucwords(str_replace(',',', ',$facilityschedulers->weekly_available_days))}}</td>
                            </tr>
                            @elseif($facilityschedulers->schedule_type == 'Monthly')
                            <tr>
                                <td>Monthly Visit By</td>
                                <td>{{ucfirst($facilityschedulers->monthly_visit_type)}}</td>                             
                            </tr>
                            @if($facilityschedulers->monthly_visit_type == 'date')
                            <tr>
                                <td>Visit Date</th><td>{{$facilityschedulers->monthly_visit_type_date}}</td>
                            </tr>
                            @endif
                            @if($facilityschedulers->monthly_visit_type == 'week')
                            <tr>
                                <td>Visit Week</th><td>{{$facilityschedulers->monthly_visit_type_week}} week</td>
                            </tr>
                            @endif
                            @if($facilityschedulers->monthly_visit_type == 'day')
                            <tr>
                                <td>Visit Day</th><td>{{ucfirst($facilityschedulers->monthly_visit_type_day_dayname)}}</td>
                            </tr>
                            @endif
                            @endif
                            <tr class="bg-f0fdfc">
                                <td>Monday</th><td>{{$facilityschedulers->monday_selected_times}}</td>
                            </tr>
                            <tr>
                                <td>Tuesday</th><td>{{$facilityschedulers->tuesday_selected_times}}</td>
                            </tr>
                            <tr class="bg-f0fdfc">
                                <td>Wednesday</th><td>{{$facilityschedulers->wednesday_selected_times}}</td>
                            </tr>
                            <tr>
                                <td>Thursday</th><td>{{$facilityschedulers->thursday_selected_times}}</td>
                            </tr>
                            <tr class="bg-f0fdfc">
                                <td>Friday</th><td>{{$facilityschedulers->friday_selected_times}}</td>
                            </tr>
                            <tr>
                                <td>Saturday</th><td>{{$facilityschedulers->saturday_selected_times}}</td>
                            </tr>
                            <tr class="bg-f0fdfc">
                                <td>Sunday</th><td>{{$facilityschedulers->sunday_selected_times}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div>

            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="fa fa-sticky-note"></i> <h3 class="box-title">Notes</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <div class="scheduler-notes">
                        <p>@if($facilityschedulers->notes){{$facilityschedulers->notes}}@else <p class="text-center font600">No notes available</p> @endif</p>                                           
                    </div>
                </div><!-- /.box-body -->
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Visiting Dates</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body  time-table table-responsive">
                    <div class="scheduler-timetable">
                        <table class="table no-bottom">                    
                            <thead>
                                <tr>
                                    <th>Monday</th>
                                    <th>Tuesday</th>
                                    <th>Wednesday</th>
                                    <th>Thursday</th>
                                    <th>Friday</th>
                                    <th>Saturday</th>
                                    <th>Sunday</th>
                                </tr> 
                            </thead>

                            <tbody>

                                <tr>
                                    @if(count($facility_schedulers_dates_listing_arr->monday) > 0)
                                    <td class="sch-col-bg" valign="top">
                                        @foreach($facility_schedulers_dates_listing_arr->monday as $monday_dates)
                                        <div class="time-table-date">{{App\Http\Helpers\Helpers::dateFormat($monday_dates->schedule_date,'date')}}</div>
                                        @endforeach
                                    </td>
                                    @else
                                    <td valign="top" class="sch-col-na">
                                        Not Available
                                    </td>
                                    @endif

                                    @if(count($facility_schedulers_dates_listing_arr->tuesday) > 0)
                                    <td valign="top">   
                                        @foreach($facility_schedulers_dates_listing_arr->tuesday as $tuesday_dates)
                                        <div class="time-table-date">{{App\Http\Helpers\Helpers::dateFormat($tuesday_dates->schedule_date,'date')}}</div>
                                        @endforeach
                                    </td>
                                    @else
                                    <td valign="top" class="sch-col-na">
                                        Not Available
                                    </td>
                                    @endif

                                    @if(count($facility_schedulers_dates_listing_arr->wednesday) > 0)
                                    <td class="sch-col-bg" valign="top">
                                        @foreach($facility_schedulers_dates_listing_arr->wednesday as $wednesday_dates)
                                        <div class="time-table-date">{{App\Http\Helpers\Helpers::dateFormat($wednesday_dates->schedule_date,'date')}}</div>
                                        @endforeach
                                    </td>
                                    @else
                                    <td valign="top" class="sch-col-na">
                                        Not Available
                                    </td>
                                    @endif

                                    @if(count($facility_schedulers_dates_listing_arr->thursday) > 0)
                                    <td valign="top">                                           
                                        @foreach($facility_schedulers_dates_listing_arr->thursday as $thursday_dates)
                                        <div class="time-table-date">{{App\Http\Helpers\Helpers::dateFormat($thursday_dates->schedule_date,'date')}}</div>
                                        @endforeach
                                    </td>
                                    @else
                                    <td valign="top" class="sch-col-na">
                                        Not Available
                                    </td>
                                    @endif

                                    @if(count($facility_schedulers_dates_listing_arr->friday) > 0)
                                    <td class="sch-col-bg" valign="top">                                            
                                        @foreach($facility_schedulers_dates_listing_arr->friday as $friday_dates)
                                        <div class="time-table-date">{{App\Http\Helpers\Helpers::dateFormat($friday_dates->schedule_date,'date')}}</div>
                                        @endforeach
                                    </td>
                                    @else
                                    <td valign="top" class="sch-col-na">
                                        Not Available
                                    </td>
                                    @endif

                                    @if(count($facility_schedulers_dates_listing_arr->saturday) > 0)
                                    <td valign="top">
                                        @foreach($facility_schedulers_dates_listing_arr->saturday as $saturday_dates)
                                        <div class="time-table-date">{{App\Http\Helpers\Helpers::dateFormat($saturday_dates->schedule_date,'date')}}</div>
                                        @endforeach
                                    </td>
                                    @else
                                    <td valign="top" class="sch-col-na">
                                        Not Available
                                    </td>
                                    @endif

                                    @if(count($facility_schedulers_dates_listing_arr->sunday) > 0)
                                    <td class="sch-col-bg" valign="top">
                                        @foreach($facility_schedulers_dates_listing_arr->sunday as $sunday_dates)
                                        <div class="time-table-date">{{App\Http\Helpers\Helpers::dateFormat($sunday_dates->schedule_date,'date')}}</div>
                                        @endforeach
                                    </td>
                                    @else
                                    <td valign="top" class="sch-col-na">
                                        Not Available
                                    </td>
                                    @endif
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div><!-- /.box-body -->
            </div>  
        </div>
    </div><!-- /.box -->
</div>

@stop   