{!! Form::open(['name'=>'myform','id'=>'js-bootstrap-validator','class'=>'popupmedcubicsform']) !!}

<?php
	$facility_id = '';
	$hideClass = 'hide';
	$scheduler_id = '';

	if ($facility_arr != '') {
		$scheduler_details = $facility_arr->scheduler_details;

		$scheduler_details->start_date = date("m/d/Y", strtotime($scheduler_details->start_date));
		$scheduler_details->end_date = date("m/d/Y", strtotime($scheduler_details->end_date));

		$facility_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($scheduler_details->facility_id, 'encode');
		$facility_details = $facility_arr->facility_details;

		$scheduler_id = $scheduler_details->id;
		//$days = $facility_arr->days;        
		$hideClass = $scheduler_details->hideClass;
		if ($facility_details->sunday_available_time == 'Not available' || $facility_details->monday_available_time == 'Not available' || $facility_details->tuesday_available_time == 'Not available' || $facility_details->wednesday_available_time == 'Not available' || $facility_details->friday_available_time == 'Not available' || $facility_details->saturday_available_time == 'Not available' || $facility_details->thursday_available_time == 'Not available')
			$disabled_all_week_class = 'disabled';
	} else {
		$weekly_available_days = [];
		$scheduler_details['weekly_available_days'] = $weekly_available_days;
		$scheduler_details['start_date'] = date("m/d/Y");
		$scheduler_details['end_date'] = date("m/d/Y", strtotime('+2 Years'));
		$scheduler_details['end_date_option'] = 'on';
		$scheduler_details['no_of_occurrence'] = 1;
		$scheduler_details = (object) $scheduler_details;
		$disabled_all_week_class = '';
	}
	if ($facility_id != '')
		$scheduler_details->facility_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($scheduler_details->facility_id, 'encode');
	if ($provider_id != '')
		$provider_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider_id, 'encode');
?>
{!! Form::hidden('provider_id',$provider_id,['class'=>'form-control input-sm','id'=>'provider_id']) !!}
{!! Form::hidden('scheduler_id',$scheduler_id,['class'=>'form-control input-sm','id'=>'scheduler_id']) !!}	

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow no-border">
        <div class="box-header transparent">          
            <div class="form-group-billing">                             
                {!! Form::label('Choose Facility', 'Select Facility ', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label no-padding med-green font600 star']) !!}                           
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    {!! Form::select('facility_id',array(''=>'-- Select --')+(array)$facilities,@$scheduler_details->facility_id,['class'=>'form-control pop-dropdown input-sm-modal sch-cus-dropdown','id'=>'js-provider-scheduler-facility']) !!}
                </div>                        
            </div>  

            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="scheduler box-body no-padding js-show-by-facility {{$hideClass}}" id="js-facility-details">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding modal-header-bg border-radius-4">                        
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                    <div class="box-block transparent">
                        <div class="box-body">
                            <div class="col-md-2 hidden-sm no-padding">
								
								<?php
									$filename = @$facility_details->filename;
									$img_details = [];
									$img_details['module_name']='facility';
									$img_details['file_name']=$filename;
									$img_details['practice_name']="";
									$img_details['need_url'] = 'yes';
									$img_details['alt'] = 'facility-image';
									$img_details['class']='';
									$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
								?>
								
                                {!! HTML::image(@$image_tag,null,['class'=>'img-border-sm margin-t-0 no-bottom','id'=>'js-facility-details-icon']) !!}
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 med-right-border">
                                <p class="no-bottom margin-t-m-5"><small class="med-green font600" id="js-facility-details-name">{{@$facility_details->name}}</small></p>
                                <p class="push"><i class="fa fa-map-marker"></i> <span id="js-facility-details-address">{{@$facility_details->address}}</span></p>
                                <p class="push margin-t-m-10 m-b-m-10" id="js-facility-details-zipcode"> {{@$facility_details->zipcode}}</p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <p class="push"><i class="fa fa-envelope"></i> <span id="js-facility-details-email"> @if(@$facility_details->email !='') {{@$facility_details->email}} @else - Nil - @endif</span> </p>
                                <p class="push margin-t-m-8"><i class="fa {{Config::get('cssconfigs.common.phone')}}"></i> <span id="js-facility-details-phone"> @if(@$facility_details->phone !='') {{@$facility_details->phone}} @else - Nil - @endif <span> </p>
                            </div>  
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>
        </div><!-- /.box-body -->  
    </div><!-- /.box -->
</div>

<div class="js-show-by-facility {{$hideClass}}">    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-radius-4 margin-t-m-10 no-bottom" >
        <div class="box box-view no-shadow yes-border">
            <div class="box-header-view">
                <i class="fa  fa-table" data-name="table"></i> <h3 class="box-title">Schedule Details</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body form-horizontal m-b-m-15">
                <div class="form-group-billing">                             
                    {!! Form::label('Start Date', 'Start Date', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label-popup star']) !!}                           
                    <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-10 no-margin">
                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick="iconclick('start_date')"></i>
                        {!! Form::text('start_date',@$scheduler_details->start_date,['id'=>'start_date','autocomplete'=>'off','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control input-sm-header-billing form-cursor dm-date']) !!}
                        <small class="help-block hide" id="js-error-start_date"></small>
                    </div>                        
                    <div class="col-sm-1"></div>
                </div>  

                <div class="form-group-billing bottom-space-10">
                    {!! Form::label('End Date', 'End Date', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-popup star']) !!}                                                  
                    <div class="col-md-6 col-sm-6 col-sm-6 col-xs-12 line-height-26">  
                        
                        <div class="form-group col-md-12 col-sm-6 no-padding no-margin">  
                            {!! Form::radio('end_date_option', 'after',(@$scheduler_details->end_date_option=='after')?true:null,['class'=>'flat-red js-end_date_option','id'=>'c-after']) !!} {!! Form::label('c-after', 'After',['class'=>'med-darkgray font600 form-cursor']) !!} 
                            @if($scheduler_details->end_date_option=='after')
                                <input type="number" min="1" name="no_of_occurrence" class="form-control input-sm-header-billing dm-per-week" style="width: 35%; display: inline" value="{{@$scheduler_details->no_of_occurrence}}", disabled= 'disabled' > occurrence&nbsp;(s)
                            @else                                
                                <input type="number" min="1" name="no_of_occurrence" class="form-control input-sm-header-billing dm-per-week" style="width: 35%; display: inline" value="{{@$scheduler_details->no_of_occurrence}}"> occurrence&nbsp;(s)
                            @endif    
                        </div>
                        <div class="form-group col-md-12 col-sm-6 no-padding no-margin">  
                            {!! Form::radio('end_date_option', 'on',(@$scheduler_details->end_date_option=='on')?true:null,['class'=>'flat-red js-end_date_option','id'=>'c-on']) !!} {!! Form::label('c-on', 'On',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp; &emsp;{!! Form::text('end_date',@$scheduler_details->end_date,['style'=>'width:45%; display:inline','id'=>'end_date','autocomplete'=>'off','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control input-sm-header-billing form-cursor dm-date']) !!}  
                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon margin-l-5" onclick="iconclick('end_date')"></i> 
                        </div>
                        <div class="form-group col-md-12 col-sm-6 no-padding no-margin"> 
                            {!! Form::radio('end_date_option', 'never',(@$scheduler_details->end_date_option=='never')?true:null,['class'=>'flat-red js-end_date_option','id'=>'c-never']) !!} {!! Form::label('c-never', 'Never',['class'=>'med-darkgray font600 form-cursor']) !!} 
                        </div>
                    </div>                
                </div> 
                <div class="form-group-billing">
                    {!! Form::label('Scheduled By', 'Scheduled By', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label-popup']) !!} 
                    <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                        {!! Form::select('schedule_type',array('Daily'=>'Daily','Weekly'=>'Weekly','Monthly'=>'Monthly'),@$scheduler_details->schedule_type,['class'=>'form-control input-sm-header-billing js-schedule-type','id'=>'schedule_type']) !!}    
                    </div>
                </div>
                <div class="form-group-billing">
                    {!! Form::label('Repeat every ', 'Repeat Every', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label-popup']) !!} 
                    <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3 ">
                        {!! Form::select('repeat_every',array_slice(range(0,30), 1, NULL, TRUE),@$scheduler_details->repeat_every,['class'=>'select2 form-control input-sm-header-billing']) !!}
                    </div>   
                    <div class="col-lg-1 col-md-1 col-sm-1 js-repeat-caption modal-text-height p-l-0">@if(@$scheduler_details->schedule_type == 'Weekly') week&nbsp;(s) @elseif(@$scheduler_details->schedule_type == 'Monthly') month&nbsp;(s) @else day&nbsp;(s) @endif</div> 				
                </div>        
                <div class="form-group-billing" id="js-monday-div_1">
                    {!! Form::label('Appointment Slot', 'Appointment Slot', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label-popup']) !!} 
                    <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                        
						<?php 
							$appointment_slot_arr = array_combine(range(5, 60, 5), range(5, 60, 5)); 
							$sel_slot_tim = (@$scheduler_details->appointment_slot != '')?@$scheduler_details->appointment_slot:'15';
						?>
						
						{!! Form::select('appointment_slot',@$appointment_slot_arr,@$sel_slot_tim,['class'=>'form-control input-sm-header-billing','id'=>'appointment_slot']) !!}
						
						<!--{!! Form::text('appointment_slot',@$scheduler_details->appointment_slot,['maxlength'=>'2','id'=>'appointment_slot','class'=>'form-control input-sm-header-billing dm-per-week']) !!}-->   
                    </div>
					<div class="col-lg-1 col-md-1 col-sm-1 modal-text-height p-l-0">mins</div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-schedule-type-options margin-t-m-10 border-radius-4">
        <small class="med-orange hide" id="js-error-msg"></small>	
        <div class="box box-view no-shadow">
            <div class="box-header-view">
                <span id="js-schedule-type-title-daily" class="js-schedule-type-title-cls @if(@$scheduler_details->schedule_type == 'Weekly' || @$scheduler_details->schedule_type == 'Monthly')hide @endif"><i class="fa {{Config::get('cssconfigs.scheduler.daily')}}"></i> <h3 class="box-title">Daily</h3></span>
                <span id="js-schedule-type-title-weekly" class="js-schedule-type-title-cls @if(@$scheduler_details->schedule_type != 'Weekly')hide @endif"><i class="fa {{Config::get('cssconfigs.scheduler.weekly')}}" data-name="columns"></i> <h3 class="box-title">Weekly</h3></span>
                <span id="js-schedule-type-title-monthly" class="js-schedule-type-title-cls @if(@$scheduler_details->schedule_type != 'Monthly')hide @endif"><i class="fa  {{Config::get('cssconfigs.scheduler.monthly')}}" data-name="table"></i> <h3 class="box-title">Monthly</h3></span>
                <div class="box-tools pull-right">                      
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->

            <div class="box-body form-horizontal">  
                <div id="js-schedule-type-option-weekly" class="js-schedule-type-option-cls @if(@$scheduler_details->schedule_type != 'Weekly')hide @endif">   
                    <div class="form-group">
                        {!! Form::label('Weekly ', 'Weekly', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label-popup star']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-10 modal-weekly @if($errors->first('medium_description')) error @endif">                               
                            <div class="btn-group js-weekly" data-toggle="buttons-checkbox">
                                <p class="btn {{@$disabled_all_week_class}} @if(in_array('all',@$scheduler_details->weekly_available_days)) active @endif" name="weekly_all" id="js-weekly_all" href="javascript:void(0);">
                                    {!! Form::checkbox('weekly_available_days[]','all',(in_array('all',@$scheduler_details->weekly_available_days))?true:null,['class'=>'hide','id'=>'weekly_all']) !!} All
                                </p>
                                <p class="btn js-weekly-day-selection  @if(@$facility_details->monday_available_time == 'Not available') disabled  @endif @if(in_array('monday',@$scheduler_details->weekly_available_days)) active @endif" name="weekly_monday" href="javascript:void(0);" id="weekly_monday_p_tag">
                                    {!! Form::checkbox('weekly_available_days[]','monday',(in_array('monday',@$scheduler_details->weekly_available_days))?true:null,['class'=>'hide js-cls-weekly-day-selection','id'=>'weekly_monday']) !!} Mon
                                </p>
                                <p class="btn js-weekly-day-selection  @if(@$facility_details->tuesday_available_time == 'Not available') disabled  @endif @if(in_array('tuesday',@$scheduler_details->weekly_available_days)) active @endif" name="weekly_tuesday" href="javascript:void(0);" id="weekly_tuesday_p_tag">
                                    {!! Form::checkbox('weekly_available_days[]','tuesday',(in_array('tuesday',@$scheduler_details->weekly_available_days))?true:null,['class'=>'hide js-cls-weekly-day-selection','id'=>'weekly_tuesday']) !!} Tue
                                </p>
                                <p class="btn js-weekly-day-selection  @if(@$facility_details->wednesday_available_time == 'Not available') disabled  @endif @if(in_array('wednesday',@$scheduler_details->weekly_available_days)) active @endif" name="weekly_wednesday" href="javascript:void(0);" id="weekly_wednesday_p_tag">
                                    {!! Form::checkbox('weekly_available_days[]','wednesday',(in_array('wednesday',@$scheduler_details->weekly_available_days))?true:null,['class'=>'hide js-cls-weekly-day-selection','id'=>'weekly_wednesday']) !!} Wed
                                </p>
                                <p class="btn js-weekly-day-selection  @if(@$facility_details->thursday_available_time == 'Not available') disabled  @endif @if(in_array('thursday',@$scheduler_details->weekly_available_days)) active @endif" name="weekly_thursday" href="javascript:void(0);" id="weekly_thursday_p_tag">
                                    {!! Form::checkbox('weekly_available_days[]','thursday',(in_array('thursday',@$scheduler_details->weekly_available_days))?true:null,['class'=>'hide js-cls-weekly-day-selection','id'=>'weekly_thursday']) !!} Thu
                                </p>
                                <p class="btn js-weekly-day-selection  @if(@$facility_details->friday_available_time == 'Not available') disabled  @endif @if(in_array('friday',@$scheduler_details->weekly_available_days)) active @endif" name="weekly_friday" href="javascript:void(0);" id="weekly_friday_p_tag">
                                    {!! Form::checkbox('weekly_available_days[]','friday',(in_array('friday',@$scheduler_details->weekly_available_days))?true:null,['class'=>'hide js-cls-weekly-day-selection','id'=>'weekly_friday']) !!} Fri
                                </p>
                                <p class="btn js-weekly-day-selection  @if(@$facility_details->saturday_available_time == 'Not available') disabled  @endif  @if(in_array('saturday',@$scheduler_details->weekly_available_days)) active @endif" name="weekly_saturday" href="javascript:void(0);" id="weekly_saturday_p_tag">
                                    {!! Form::checkbox('weekly_available_days[]','saturday',(in_array('saturday',@$scheduler_details->weekly_available_days))?true:null,['class'=>'hide js-cls-weekly-day-selection','id'=>'weekly_saturday']) !!} Sat
                                </p>
                                <p class="btn js-weekly-day-selection @if(@$facility_details->sunday_available_time == 'Not available') disabled  @endif @if(in_array('sunday',@$scheduler_details->weekly_available_days)) active @endif" name="weekly_sunday" href="javascript:void(0);" id="weekly_sunday_p_tag">
                                    {!! Form::checkbox('weekly_available_days[]','sunday',(in_array('sunday',@$scheduler_details->weekly_available_days))?true:null,['class'=>'hide js-cls-weekly-day-selection','id'=>'weekly_sunday']) !!} Sun
                                </p>
                            </div>                              
                        </div>
                    </div>                          
                </div>                    

                <div id="js-schedule-type-option-monthly" class="js-schedule-type-option-cls @if(@$scheduler_details->schedule_type != 'Monthly')hide @endif">  
                    <div class="form-group">
                        {!! Form::label('Visit Type', 'Visit Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label-popup star']) !!} 
                        <div class="col-lg-01 col-md-10 col-sm-6">
                            {!! Form::select('monthly_visit_type',array(''=>'-- Select --','date'=>'Date','day'=>'Day','week'=>'Week'),@$scheduler_details->monthly_visit_type,['class'=>'form-control input-sm-header-billing','id'=>'js-visit-by']) !!}    
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group @if(@$scheduler_details->monthly_visit_type != 'date')hide @endif" id="js-monthly-visit-option-date">
                        {!! Form::label('Visit every ', 'Visit every', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-6">
                            {!! Form::select('monthly_visit_type_date',App\Http\Helpers\Helpers::getDateswithSuffix(),@$scheduler_details->monthly_visit_type_date,['class'=>'select2 form-control input-sm-header-billing modal-text-height']) !!}
                        </div>   
                        <div class="col-lg-2 col-md-2 col-sm-2 modal-text-height p-l-0">in a month</div>                   
                    </div>

                    <div class="form-group-billing @if(@$scheduler_details->monthly_visit_type != 'day')hide @endif" id="js-monthly-visit-option-day">
                        {!! Form::label('Visit every ', 'Visit every', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('monthly_visit_type_day_dayname',['monday'=>'Mon','tuesday'=>'Tue','wednesday'=>'Wed','thursday'=>'Thu','friday'=>'Fri','saturday'=>'Sat','sunday'=>'Sun'],@$scheduler_details->monthly_visit_type_day_dayname,['class'=>'select2 form-control input-sm-header-billing','id'=>'js-monthly-day-option']) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 modal-text-height p-l-0">in a month</div>                            
                        <div class="col-sm-1">&nbsp;</div>
                    </div>        

                    <div class="form-group-billing @if(@$scheduler_details->monthly_visit_type != 'week')hide @endif"  id="js-monthly-visit-option-week">
                        {!! Form::label('Visit every ', 'Visit every', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('monthly_visit_type_week',['1'=>'1st','2'=>'2nd','3'=>'3rd','4'=>'4th','5'=>'5th'],@$scheduler_details->monthly_visit_type_week,['class'=>'select2 form-control input-sm-header-billing']) !!}
                        </div>   
                        <div class="col-lg-3 col-md-3 col-sm-3 modal-text-height p-l-0">week in a month</div> 
                    </div>                            
                </div>                  

                <p class="alert alert-danger hide"><span class="close normal_popup_form js_rmv_err_alt">×</span>Select From and To time before add more</p>
				
				<div id="js-day-parent-monday" class="js-display-days-cls @if((@$scheduler_details->schedule_type == 'Weekly' && !in_array('monday',@$scheduler_details->weekly_available_days)) || (@$scheduler_details->schedule_type == 'Monthly' && @$scheduler_details->monthly_visit_type == 'day' && @$scheduler_details->monthly_visit_type_day_dayname != 'monday')) hide @endif">                        
                    <div class="form-group-billing  timing-hover" id="js-monday-div_1">                                    
                        {!! Form::label('Monday', 'Mon', ['class'=>'col-lg-1 col-md-1 col-sm-3 col-xs-3 control-label-popup star']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('monday_from[1]',array(''=>'-- From --')+(array)@$scheduler_details->monday->from_option1,@$scheduler_details->monday->selected_from_time1,['id' => 'monday_from1', 'class'=>'form-control input-sm-header-billing js-day-timings-monday-from js-from-selection js-add-more-disable_1 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('monday_to[1]',array(''=>'-- To --')+(array)@$scheduler_details->monday->to_option1,@$scheduler_details->monday->selected_to_time1,['id' => 'monday_to1', 'class'=>'form-control input-sm-header-billing js-day-timings-monday-to js-add-more-disable_1 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="1" class="js-add-more js-add-more-icon_1 @if(@$scheduler_details->monday->selected_from_time2 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                            <span class="popup-timing js-show-available-timings-monday">{{@$facility_details->monday_available_time}}</span>
                        </div>                        
                    </div> 
                    <div class="form-group-billing  js-additional-timing-set @if(@$scheduler_details->monday->selected_from_time2 == '') hide @endif " id="js-monday-div_2">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('monday_from[2]',array(''=>'-- From --')+(array)@$scheduler_details->monday->from_option2,@$scheduler_details->monday->selected_from_time2,['id' => 'monday_from2', 'class'=>'form-control input-sm-header-billing js-day-timings-monday-from js-from-selection js-add-more-disable_2 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('monday_to[2]',array(''=>'-- To --')+(array)@$scheduler_details->monday->to_option2,@$scheduler_details->monday->selected_to_time2,['id' => 'monday_to2', 'class'=>'form-control input-sm-header-billing js-day-timings-monday-to js-add-more-disable_2 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="1" data-add_more_hide="2" class="js-delete-more js-add-more-icon_2 @if(@$scheduler_details->monday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                            <a href="javascript:void(0);" data-add_more_show="3" data-add_more_hide="2" class="js-add-more js-add-more-icon_2 @if(@$scheduler_details->monday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                        </div>                        
                    </div>  
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->monday->selected_from_time3 == '')hide @endif " id="js-monday-div_3">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('monday_from[3]',array(''=>'-- From --')+(array)@$scheduler_details->monday->from_option3,@$scheduler_details->monday->selected_from_time3,['id' => 'monday_from3', 'class'=>'form-control input-sm-header-billing js-day-timings-monday-from js-from-selection js-add-more-disable_3 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('monday_to[3]',array(''=>'-- To --')+(array)@$scheduler_details->monday->to_option3,@$scheduler_details->monday->selected_to_time3,['id' => 'monday_to3', 'class'=>'form-control input-sm-header-billing js-day-timings-monday-to js-add-more-disable_3 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="3" class="js-delete-more"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                        </div>                        
                    </div> 
                    <p class="help-block hide margin-l-m-5 margin-t-m-25 font11" id="js-error-monday"></p>
                </div>  

                <div id="js-day-parent-tuesday" class="js-display-days-cls @if((@$scheduler_details->schedule_type == 'Weekly' && !in_array('tuesday',@$scheduler_details->weekly_available_days)) || (@$scheduler_details->schedule_type == 'Monthly' && @$scheduler_details->monthly_visit_type == 'day' && @$scheduler_details->monthly_visit_type_day_dayname != 'tuesday')) hide @endif">
                    <div class="form-group-billing timing-hover" id="js-tuesday-div_1">                                    
                        {!! Form::label('Tuesday', 'Tue', ['class'=>'col-lg-1 col-md-1 col-sm-3 col-xs-3 control-label-popup star']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('tuesday_from[1]',array(''=>'-- From --')+(array)@$scheduler_details->tuesday->from_option1,@$scheduler_details->tuesday->selected_from_time1,['id' => 'tuesday_from1', 'class'=>'form-control input-sm-header-billing js-day-timings-tuesday-from js-from-selection js-add-more-disable_1 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('tuesday_to[1]',array(''=>'-- To --')+(array)@$scheduler_details->tuesday->to_option1,@$scheduler_details->tuesday->selected_to_time1,['id' => 'tuesday_to1', 'class'=>'form-control input-sm-header-billing js-day-timings-tuesday-to js-add-more-disable_1 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="1" class="js-add-more js-add-more-icon_1 @if(@$scheduler_details->tuesday->selected_from_time2 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                            <span class="popup-timing js-show-available-timings-tuesday">{{@$facility_details->tuesday_available_time}}</span>
                        </div>                        
                    </div> 
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->tuesday->selected_from_time2 == '')hide @endif" id="js-tuesday-div_2">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('tuesday_from[2]',array(''=>'-- From --')+(array)@$scheduler_details->tuesday->from_option2,@$scheduler_details->tuesday->selected_from_time2,['id' => 'tuesday_from2', 'class'=>'form-control input-sm-header-billing js-day-timings-tuesday-from js-from-selection js-add-more-disable_2 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('tuesday_to[2]',array(''=>'-- To --')+(array)@$scheduler_details->tuesday->to_option2,@$scheduler_details->tuesday->selected_to_time2,['id' => 'tuesday_to2', 'class'=>'form-control input-sm-header-billing js-day-timings-tuesday-to js-add-more-disable_2 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="1" data-add_more_hide="2" class="js-delete-more js-add-more-icon_2 @if(@$scheduler_details->tuesday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                            <a href="javascript:void(0);" data-add_more_show="3" data-add_more_hide="2" class="js-add-more js-add-more-icon_2 @if(@$scheduler_details->tuesday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                        </div>                        
                    </div>  
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->tuesday->selected_from_time3 == '')hide @endif" id="js-tuesday-div_3">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('tuesday_from[3]',array(''=>'-- From --')+(array)@$scheduler_details->tuesday->from_option3,@$scheduler_details->tuesday->selected_from_time3,['id' => 'tuesday_from3', 'class'=>'form-control input-sm-header-billing js-day-timings-tuesday-from js-from-selection js-add-more-disable_3 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('tuesday_to[3]',array(''=>'-- To --')+(array)@$scheduler_details->tuesday->to_option3,@$scheduler_details->tuesday->selected_to_time3,['id' => 'tuesday_to3', 'class'=>'form-control input-sm-header-billing js-day-timings-tuesday-to js-add-more-disable_3 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="3" class="js-delete-more"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                        </div>                        
                    </div> 
                    <p class="help-block hide margin-l-m-5 margin-t-m-25 font11" id="js-error-tuesday"></p>
                </div>

                <div id="js-day-parent-wednesday" class="js-display-days-cls @if((@$scheduler_details->schedule_type == 'Weekly' && !in_array('wednesday',@$scheduler_details->weekly_available_days)) || (@$scheduler_details->schedule_type == 'Monthly' && @$scheduler_details->monthly_visit_type == 'day' && @$scheduler_details->monthly_visit_type_day_dayname != 'wednesday')) hide @endif">
                    <div class="form-group-billing timing-hover" id="js-wednesday-div_1">                                    
                        {!! Form::label('Wednesday', 'Wed', ['class'=>'col-lg-1 col-md-1 col-sm-3 col-xs-3 control-label-popup star']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('wednesday_from[1]',array(''=>'-- From --')+(array)@$scheduler_details->wednesday->from_option1,@$scheduler_details->wednesday->selected_from_time1,['id' => 'wednesday_from1', 'class'=>'form-control input-sm-header-billing js-day-timings-wednesday-from js-from-selection js-add-more-disable_1 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('wednesday_to[1]',array(''=>'-- To --')+(array)@$scheduler_details->wednesday->to_option1,@$scheduler_details->wednesday->selected_to_time1,['id' => 'wednesday_to1', 'class'=>'form-control input-sm-header-billing js-day-timings-wednesday-to js-add-more-disable_1 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="1" class="js-add-more js-add-more-icon_1 @if(@$scheduler_details->wednesday->selected_from_time2 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                            <span class="popup-timing js-show-available-timings-wednesday">{{@$facility_details->wednesday_available_time}}</span>
                        </div>                        
                    </div> 
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->wednesday->selected_from_time2 == '')hide @endif" id="js-wednesday-div_2">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('wednesday_from[2]',array(''=>'-- From --')+(array)@$scheduler_details->wednesday->from_option2,@$scheduler_details->wednesday->selected_from_time2,['id' => 'wednesday_from2', 'class'=>'form-control input-sm-header-billing js-day-timings-wednesday-from js-from-selection js-add-more-disable_2 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('wednesday_to[2]',array(''=>'-- To --')+(array)@$scheduler_details->wednesday->to_option2,@$scheduler_details->wednesday->selected_to_time2,['id' => 'wednesday_to2', 'class'=>'form-control input-sm-header-billing js-day-timings-tuesday-to js-add-more-disable_2 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="1" data-add_more_hide="2" class="js-delete-more js-add-more-icon_2 @if(@$scheduler_details->wednesday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                            <a href="javascript:void(0);" data-add_more_show="3" data-add_more_hide="2" class="js-add-more js-add-more-icon_2 @if(@$scheduler_details->wednesday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                        </div>                        
                    </div>  
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->wednesday->selected_from_time3 == '')hide @endif" id="js-wednesday-div_3">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('wednesday_from[3]',array(''=>'-- From --')+(array)@$scheduler_details->wednesday->from_option3,@$scheduler_details->wednesday->selected_from_time3,['id' => 'wednesday_from3', 'class'=>'form-control input-sm-header-billing js-day-timings-wednesday-from js-from-selection js-add-more-disable_3 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('wednesday_to[3]',array(''=>'-- To --')+(array)@$scheduler_details->wednesday->to_option3,@$scheduler_details->wednesday->selected_to_time3,['id' => 'wednesday_to3', 'class'=>'form-control input-sm-header-billing js-day-timings-wednesday-to js-add-more-disable_3 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="3" class="js-delete-more"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                        </div>                        
                    </div> 
                    <p class="help-block hide margin-l-m-5 margin-t-m-25 font11" id="js-error-wednesday"></p>
                </div>

                <div id="js-day-parent-thursday" class="js-display-days-cls @if((@$scheduler_details->schedule_type == 'Weekly' && !in_array('thursday',@$scheduler_details->weekly_available_days)) || (@$scheduler_details->schedule_type == 'Monthly' && @$scheduler_details->monthly_visit_type == 'day' && @$scheduler_details->monthly_visit_type_day_dayname != 'thursday')) hide @endif">
                    <div class="form-group-billing timing-hover" id="js-thursday-div_1">                                    
                        {!! Form::label('Thursday', 'Thu', ['class'=>'col-lg-1 col-md-1 col-sm-3 col-xs-3 control-label-popup star']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('thursday_from[1]',array(''=>'-- From --')+(array)@$scheduler_details->thursday->from_option1,@$scheduler_details->thursday->selected_from_time1,['id' => 'thursday_from1', 'class'=>'form-control input-sm-header-billing js-day-timings-thursday-from js-from-selection js-add-more-disable_1 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('thursday_to[1]',array(''=>'-- To --')+(array)@$scheduler_details->thursday->to_option1,@$scheduler_details->thursday->selected_to_time1,['id' => 'thursday_to1', 'class'=>'form-control input-sm-header-billing js-day-timings-thursday-to js-add-more-disable_1 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="1" class="js-add-more js-add-more-icon_1 @if(@$scheduler_details->thursday->selected_from_time2 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                            <span class="popup-timing js-show-available-timings-thursday">{{@$facility_details->thursday_available_time}}</span>
                        </div>                        
                    </div> 
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->thursday->selected_from_time2 == '')hide @endif" id="js-thursday-div_2">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('thursday_from[2]',array(''=>'-- From --')+(array)@$scheduler_details->thursday->from_option2,@$scheduler_details->thursday->selected_from_time2,['id' => 'thursday_from2', 'class'=>'form-control input-sm-header-billing js-day-timings-thursday-from js-from-selection js-add-more-disable_2 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('thursday_to[2]',array(''=>'-- To --')+(array)@$scheduler_details->thursday->to_option2,@$scheduler_details->thursday->selected_to_time2,['id' => 'thursday_to2', 'class'=>'form-control input-sm-header-billing js-day-timings-thursday-to js-add-more-disable_2 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="1" data-add_more_hide="2" class="js-delete-more js-add-more-icon_2 @if(@$scheduler_details->thursday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                            <a href="javascript:void(0);" data-add_more_show="3" data-add_more_hide="2" class="js-add-more js-add-more-icon_2 @if(@$scheduler_details->thursday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                        </div>                        
                    </div>  
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->thursday->selected_from_time3 == '')hide @endif" id="js-thursday-div_3">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('thursday_from[3]',array(''=>'-- From --')+(array)@$scheduler_details->thursday->from_option3,@$scheduler_details->thursday->selected_from_time3,['id' => 'thursday_from3', 'class'=>'form-control input-sm-header-billing js-day-timings-thursday-from js-from-selection js-add-more-disable_3 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('thursday_to[3]',array(''=>'-- To --')+(array)@$scheduler_details->thursday->to_option3,@$scheduler_details->thursday->selected_to_time3,['id' => 'thursday_to3', 'class'=>'form-control input-sm-header-billing js-day-timings-thursday-to js-add-more-disable_3 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="3" class="js-delete-more"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                        </div>                        
                    </div> 
                    <p class="help-block hide margin-l-m-5 margin-t-m-25 font11" id="js-error-thursday"></p>
                </div>

                <div id="js-day-parent-friday" class="js-display-days-cls @if((@$scheduler_details->schedule_type == 'Weekly' && !in_array('friday',@$scheduler_details->weekly_available_days)) || (@$scheduler_details->schedule_type == 'Monthly' && @$scheduler_details->monthly_visit_type == 'day' && @$scheduler_details->monthly_visit_type_day_dayname != 'friday')) hide @endif">
                    <div class="form-group-billing timing-hover" id="js-friday-div_1">                                    
                        {!! Form::label('Friday', 'Fri', ['class'=>'col-lg-1 col-md-1 col-sm-3 col-xs-3 control-label-popup star']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('friday_from[1]',array(''=>'-- From --')+(array)@$scheduler_details->friday->from_option1,@$scheduler_details->friday->selected_from_time1,['id' => 'friday_from1', 'class'=>'form-control input-sm-header-billing js-day-timings-friday-from js-from-selection js-add-more-disable_1 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('friday_to[1]',array(''=>'-- To --')+(array)@$scheduler_details->friday->to_option1,@$scheduler_details->friday->selected_to_time1,['id' => 'friday_to1', 'class'=>'form-control input-sm-header-billing js-day-timings-friday-to js-add-more-disable_1 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="1" class="js-add-more js-add-more-icon_1 @if(@$scheduler_details->friday->selected_from_time2 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                            <span class="popup-timing js-show-available-timings-friday">{{@$facility_details->friday_available_time}}</span>
                        </div>                        
                    </div> 
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->friday->selected_from_time2 == '')hide @endif" id="js-friday-div_2">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('friday_from[2]',array(''=>'-- From --')+(array)@$scheduler_details->friday->from_option2,@$scheduler_details->friday->selected_from_time2,['id' => 'friday_from2', 'class'=>'form-control input-sm-header-billing js-day-timings-friday-from js-from-selection js-add-more-disable_2 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('friday_to[2]',array(''=>'-- To --')+(array)@$scheduler_details->friday->to_option2,@$scheduler_details->friday->selected_to_time2,['id' => 'friday_to2', 'class'=>'form-control input-sm-header-billing js-day-timings-friday-to js-add-more-disable_2 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="1" data-add_more_hide="2" class="js-delete-more js-add-more-icon_2 @if(@$scheduler_details->friday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                            <a href="javascript:void(0);" data-add_more_show="3" data-add_more_hide="2" class="js-add-more js-add-more-icon_2 @if(@$scheduler_details->friday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                        </div>                        
                    </div>  
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->friday->selected_from_time3 == '')hide @endif" id="js-friday-div_3">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('friday_from[3]',array(''=>'-- From --')+(array)@$scheduler_details->friday->from_option3,@$scheduler_details->friday->selected_from_time3,['id' => 'friday_from3', 'class'=>'form-control input-sm-header-billing js-day-timings-friday-from js-from-selection js-add-more-disable_3 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('friday_to[3]',array(''=>'-- To --')+(array)@$scheduler_details->friday->to_option3,@$scheduler_details->friday->selected_to_time3,['id' => 'friday_to3', 'class'=>'form-control input-sm-header-billing js-day-timings-friday-to js-add-more-disable_3 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="3" class="js-delete-more"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                        </div>                        
                    </div> 
                    <p class="help-block hide margin-l-m-5 margin-t-m-25 font11" id="js-error-friday"></p>
                </div>                    

                <div id="js-day-parent-saturday" class="js-display-days-cls @if((@$scheduler_details->schedule_type == 'Weekly' && !in_array('saturday',@$scheduler_details->weekly_available_days)) || (@$scheduler_details->schedule_type == 'Monthly' && @$scheduler_details->monthly_visit_type == 'day' && @$scheduler_details->monthly_visit_type_day_dayname != 'saturday')) hide @endif">
                    <div class="form-group-billing timing-hover" id="js-saturday-div_1">                                    
                        {!! Form::label('Saturday', 'Sat', ['class'=>'col-lg-1 col-md-1 col-sm-3 col-xs-3 control-label-popup star']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('saturday_from[1]',array(''=>'-- From --')+(array)@$scheduler_details->saturday->from_option1,@$scheduler_details->saturday->selected_from_time1,['id' => 'saturday_from1', 'class'=>'form-control input-sm-header-billing js-day-timings-saturday-from js-from-selection js-add-more-disable_1 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('saturday_to[1]',array(''=>'-- To --')+(array)@$scheduler_details->saturday->to_option1,@$scheduler_details->saturday->selected_to_time1,['id' => 'saturday_to1', 'class'=>'form-control input-sm-header-billing js-day-timings-saturday-to js-add-more-disable_1 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="1" class="js-add-more js-add-more-icon_1 @if(@$scheduler_details->saturday->selected_from_time2 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                            <span class="popup-timing js-show-available-timings-saturday">{{@$facility_details->saturday_available_time}}</span>
                        </div>                        
                    </div> 
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->saturday->selected_from_time2 == '')hide @endif" id="js-saturday-div_2">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('saturday_from[2]',array(''=>'-- From --')+(array)@$scheduler_details->saturday->from_option2,@$scheduler_details->saturday->selected_from_time2,['id' => 'saturday_from2', 'class'=>'form-control input-sm-header-billing js-day-timings-saturday-from js-from-selection js-add-more-disable_2 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('saturday_to[2]',array(''=>'-- To --')+(array)@$scheduler_details->saturday->to_option2,@$scheduler_details->saturday->selected_to_time2,['id' => 'saturday_to2', 'class'=>'form-control input-sm-header-billing js-day-timings-saturday-to js-add-more-disable_2 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="1" data-add_more_hide="2" class="js-delete-more js-add-more-icon_2 @if(@$scheduler_details->saturday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                            <a href="javascript:void(0);" data-add_more_show="3" data-add_more_hide="2" class="js-add-more js-add-more-icon_2 @if(@$scheduler_details->saturday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                        </div>                        
                    </div>  
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->saturday->selected_from_time3 == '')hide @endif" id="js-saturday-div_3">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('saturday_from[3]',array(''=>'-- From --')+(array)@$scheduler_details->saturday->from_option3,@$scheduler_details->saturday->selected_from_time3,['id' => 'saturday_from3', 'class'=>'form-control input-sm-header-billing js-day-timings-saturday-from js-from-selection js-add-more-disable_3 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('saturday_to[3]',array(''=>'-- To --')+(array)@$scheduler_details->saturday->to_option3,@$scheduler_details->saturday->selected_to_time3,['id' => 'saturday_to3', 'class'=>'form-control input-sm-header-billing js-day-timings-saturday-to js-add-more-disable_3 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="3" class="js-delete-more"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                        </div>                        
                    </div> 
                    <p class="help-block hide margin-l-m-5 margin-t-m-25 font11" id="js-error-saturday"></p>
                </div>

                <div id="js-day-parent-sunday" class="js-display-days-cls @if((@$scheduler_details->schedule_type == 'Weekly' && !in_array('sunday',@$scheduler_details->weekly_available_days)) || (@$scheduler_details->schedule_type == 'Monthly' && @$scheduler_details->monthly_visit_type == 'day' && @$scheduler_details->monthly_visit_type_day_dayname != 'sunday')) hide @endif">
                    <div class="form-group-billing timing-hover" id="js-sunday-div_1">                                    
                        {!! Form::label('Sunday', 'Sun', ['class'=>'col-lg-1 col-md-1 col-sm-3 col-xs-3 control-label-popup star']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('sunday_from[1]',array(''=>'-- From --')+(array)@$scheduler_details->sunday->from_option1,@$scheduler_details->sunday->selected_from_time1,['id' => 'sunday_from1', 'class'=>'form-control input-sm-header-billing js-day-timings-sunday-from js-from-selection js-add-more-disable_1 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('sunday_to[1]',array(''=>'-- To --')+(array)@$scheduler_details->sunday->to_option1,@$scheduler_details->sunday->selected_to_time1,['id' => 'sunday_to1', 'class'=>'form-control input-sm-header-billing js-day-timings-sunday-to js-add-more-disable_1 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="1" class="js-add-more js-add-more-icon_1 @if(@$scheduler_details->sunday->selected_from_time2 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                            <span class="popup-timing js-show-available-timings-sunday">{{@$facility_details->sunday_available_time}}</span>
                        </div>                        
                    </div> 
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->sunday->selected_from_time2 == '')hide @endif" id="js-sunday-div_2">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('sunday_from[2]',array(''=>'-- From --')+(array)@$scheduler_details->sunday->from_option2,@$scheduler_details->sunday->selected_from_time2,['id' => 'sunday_from2', 'class'=>'form-control input-sm-header-billing js-day-timings-sunday-from js-from-selection js-add-more-disable_2 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('sunday_to[2]',array(''=>'-- To --')+(array)@$scheduler_details->sunday->to_option2,@$scheduler_details->sunday->selected_to_time2,['id' => 'sunday_to2', 'class'=>'form-control input-sm-header-billing js-day-timings-sunday-to js-add-more-disable_2 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="1" data-add_more_hide="2" class="js-delete-more js-add-more-icon_2 @if(@$scheduler_details->sunday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                            <a href="javascript:void(0);" data-add_more_show="3" data-add_more_hide="2" class="js-add-more js-add-more-icon_2 @if(@$scheduler_details->sunday->selected_from_time3 != '') hide @endif"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add More"></i></a> 
                        </div>                        
                    </div>  
                    <div class="form-group-billing js-additional-timing-set @if(@$scheduler_details->sunday->selected_from_time3 == '')hide @endif" id="js-sunday-div_3">                                    
                        {!! Form::label('', '', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-3 control-label-popup']) !!} 
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('sunday_from[3]',array(''=>'-- From --')+(array)@$scheduler_details->sunday->from_option3,@$scheduler_details->sunday->selected_from_time3,['id' => 'sunday_from3', 'class'=>'form-control input-sm-header-billing js-day-timings-sunday-from js-from-selection js-add-more-disable_3 js-from-selection-check']) !!}
                        </div>
                        <div class="col-lg-01 col-md-01 col-sm-3 col-xs-3">
                            {!! Form::select('sunday_to[3]',array(''=>'-- To --')+(array)@$scheduler_details->sunday->to_option3,@$scheduler_details->sunday->selected_to_time3,['id' => 'sunday_to3', 'class'=>'form-control input-sm-header-billing js-day-timings-sunday-to js-add-more-disable_3 js-to-selection-check']) !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-2">
                            <a href="javascript:void(0);" data-add_more_show="2" data-add_more_hide="3" class="js-delete-more"><i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                        </div>                        
                    </div> 
                    <p class="help-block hide margin-l-m-5 margin-t-m-25 font11" id="js-error-sunday"></p>
                </div>                  
            </div>             
        </div>
    </div><!-- /.box-body --> 
</div>
<!-- Hided For 1st Version -->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-radius-4 margin-t-m-10 hide">
    <div class="box box-view no-shadow collapsed-box">
        <div class="box-header-view no-border">
            <i class="fa {{Config::get('cssconfigs.common.clock')}}"></i> <h3 class="box-title">Reminder</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body form-horizontal">
            <div class="form-group">                  
                                        
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                    <!--@if(@$api->practice_sms_enable =="1") <span class="med-green font600">SMS</span> &nbsp;{!! Form::checkbox('provider_reminder_sms','sms',@$api->provider_reminder_sms,['class'=>'js-onoff-checkbox','data-size'=>'mini']) !!}  &emsp;&emsp;@endif @if(@$api->practice_call_enable =="1")
                    <span class="med-green font600">Phone</span> &nbsp;{!! Form::checkbox('provider_reminder_phone','phone',@$api->provider_reminder_phone,['class'=>'js-onoff-checkbox','data-size'=>'mini']) !!}   &emsp;&emsp;@endif-->
					 <span class="med-green font600">SMS</span> &nbsp;{!! Form::checkbox('provider_reminder_sms','sms',@$scheduler_details->provider_reminder_sms,['class'=>'js-onoff-checkbox','data-size'=>'mini']) !!}  &emsp;&emsp;
                    <span class="med-green font600">Phone</span> &nbsp;{!! Form::checkbox('provider_reminder_phone','phone',@$scheduler_details->provider_reminder_phone,['class'=>'js-onoff-checkbox','data-size'=>'mini']) !!}   &emsp;&emsp;
                    <span class="med-green font600">Email</span> &nbsp;{!! Form::checkbox('provider_reminder_email','email',@$scheduler_details->provider_reminder_email,['class'=>'js-onoff-checkbox','data-size'=>'mini']) !!}   
                </div>                        
                <div class="col-sm-1"></div>
            </div>   
        </div>
    </div>
</div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10 border-radius-4">
        <div class="box box-view  no-shadow  collapsed-box">
            <div class="box-header-view">
                <i class="fa  {{Config::get('cssconfigs.Practicesmaster.notes')}}"></i> <h3 class="box-title">Notes</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button>
                </div>
            </div><!-- /.box-header -->

            <div class="box-body form-horizontal">
                <div class="form-group">                             
                    <div class="col-lg-12 col-md-12 col-sm-6">
                        {!! Form::textarea('notes',@$scheduler_details->notes,['id'=>'notes','class'=>'form-control']) !!}
                    </div>                        
                    <div class="col-sm-1"></div>
                </div>  
            </div>
        </div>
    </div>

    <div class="modal-footer js_practice_sch_footer">
        {!! Form::submit('Save', ['class'=>'btn btn-medcubics js-submit-btn']) !!}
        {!! Form::button('Cancel', ['class'=>'btn btn-medcubics','data-dismiss'=>'modal']) !!}
    </div>
	<div class="modal-footer js_practice_sch_footer_load hide">
		<img style="width: 50px; height: 50px;" src="{{ url('img/ajax-loader.gif') }}"> Loading...
	</div>
{!! Form::close() !!}