@if(@$reminder) 

<?php $total_count  = 0;$pastday_count  = 0;$yesterday_count  = 0;$today_count  = 0; $futureday_count  = 0;?>
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
				<?php $time  = strtotime($reminders->start_date);
						$time_in_12_hr_format  = date("g:i A", strtotime($reminders->start_time));
						$day = ltrim(date('d',$time), '0');
						$month_orgin = ltrim(date('M',$time), '0');
						$year  = date('Y',$time);
						 ?>
					<div id="form-modal-edit{{@$reminders->id}}" class="modal fade in" aria-labelledby="myModalLabel" aria-hidden="true"></div>
					@if($reminders->arrange == 0)
					<?php $pastday_count ++;  ?>
					<p class="med-orange" @if($pastday_count  == 1) style="display:block;" @else style="display:none;" @endif>Events</p>
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
								<a data-toggle="modal" href="#form-modal-delete" name="{{ $reminders->id }}" id="js_delete_confirm" date-day="{{$day}}" class="js-delete-confirm"><i class="fa fa-trash-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
							</p>
							<p>{{$time_in_12_hr_format}}</p>
						</div>
					</div>
					@elseif($reminders->arrange == 1)
					<?php $yesterday_count ++;  ?>
					<p class="med-orange" @if($yesterday_count == 1) style="display:block;" @else style="display:none;" @endif>Yesterday Events</p>
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
							<a data-toggle="modal" href="#form-modal-delete" name="{{ $reminders->id }}" id="js_delete_confirm" date-day="{{$day}}" class="js-delete-confirm"><i class="fa fa-trash-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
						</p>
						<p>{{$time_in_12_hr_format}}</p>
						</div>
					</div>
					@elseif($reminders->arrange == 2)
					<?php $today_count ++;  ?>
					<p class="med-orange" @if($today_count == 1) style="display:block;" @else style="display:none;" @endif>Today Events</p>
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
					@elseif($reminders->arrange == 3)
					<?php $futureday_count ++;  ?>
					<p class="med-orange" @if($futureday_count == 1) style="display:block;" @else style="display:none;" @endif>Future Events</p>
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
@endif