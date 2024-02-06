@if(@$reminder) 
	<?php $total_count  = 0;$pastday_count  = 0;$yesterday_count  = 0; $futureday_count  = 0;?>
	@foreach(@$reminder as $reminderr)
		@foreach(@$reminderr as $reminder_cnt)
			<?php $total_count ++ ?>
		@endforeach
	@endforeach
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">                   
		<h4 class="no-margin med-green" style="border-bottom: 1px dotted #00877f; padding-bottom: 5px;">Calendar <span class="pull-right" style="font-size: 13px; color:#00877f !important">{{ @$total_count }} Events</span></h4> 
		<div id="js_list_replace">
			@foreach(@$reminder as $reminders)
			<?php $array_count = 0;  ?>
			<div class="box box-view no-shadow">	
			@foreach(@$reminders as $reminder_more)
			<?php 
				$time  = strtotime(@$reminder_more->start_date);
				$time_in_12_hr_format  = date("g:i A", strtotime(@$reminder_more->start_time));
				$day = ltrim(date('d',$time), '0');
				$month_orgin = ltrim(date('M',$time), '0');
				$week_day = ltrim(date('l',$time), '0');
				$month = ltrim(date('m',$time), '0');
				$array_count ++;
				 ?>
				@if(@$reminder_more->arrange == 0)
				<?php $pastday_count ++;  ?>
				<p class="med-orange" @if($pastday_count  == 1) style="display:block;" @else style="display:none;" @endif>Events</p>
					<div id="js_delete_list{{@$reminder_more->id}}" class="js_delete_list">
						<div id="form-modal-edit{{@$reminder_more->id}}" class="modal fade in" aria-labelledby="myModalLabel" aria-hidden="true"></div>
						<div class="box-header-view @if($array_count >1) hide @else show @endif">
							<h3 class="box-title">{{$day}} {{$month_orgin}}</h3>
							<div class="box-tools pull-right">
								<h5 style="margin-top: 5px;" class="med-orange"> {{ $week_day}}</h5>
							</div><!-- /.box-tools -->
						</div><!-- /.box-header --> 
						<div class="box-body">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 js_count_list" id="event_list_{{@$reminder_more->id}}">
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
									<p class="med-orange font600 font20 pull-right margin-t-5">{{$time_in_12_hr_format}}</p>
								</div>
								<div class="col-lg-9 col-md-9 col-sm-8 col-xs-8">
									<p id="title" class="med-green font14 font600">{{@$reminder_more->title}}</p>
									<p id="content" class="margin-t-m-10">{{@$reminder_more->description}}</p>
								</div>
								<div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 med-orange">
									<p class=" pull-right">
									@if($checkpermission->check_url_permission('calendar/event/update/{id}') == 1)
										<a data-toggle="modal" href="#form-modal-edit{{@$reminder_more->id}}" name="{{@$reminder_more->id}}" class="edit_events"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a>&emsp;
									@endif
									@if($checkpermission->check_url_permission('calendar/event/delete/{id}') == 1)
										<a data-toggle="modal" href="#form-modal-delete" name="{{ @$reminder_more->id }}" id="js_delete_confirm" date-day="{{$day}}" class="js-delete-confirm"><i class="fa fa-trash-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
									@endif
									</p>
								</div>
							</div>
						</div>
					</div>
				@elseif(@$reminder_more->arrange == 1)
				<?php $yesterday_count ++;  ?>
				<p class="med-orange" @if($yesterday_count == 1) style="display:block;" @else style="display:none;" @endif>Yesterday Events</p>
				<div id="js_delete_list{{@$reminder_more->id}}" class="js_delete_list">
					<div id="form-modal-edit{{@$reminder_more->id}}" class="modal fade in" aria-labelledby="myModalLabel" aria-hidden="true"></div>
					<div class="box-header-view @if($array_count >1) hide @else show @endif">
						<h3 class="box-title">{{$day}} {{$month_orgin}}</h3>
						<div class="box-tools pull-right">
							<h5 style="margin-top: 5px;" class="med-orange"> {{ $week_day}}</h5>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header --> 
					<div class="box-body">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 js_count_list" id="event_list_{{@$reminder_more->id}}">
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
								<p class="med-orange font600 font20 pull-right margin-t-5">{{$time_in_12_hr_format}}</p>
							</div>
							<div class="col-lg-9 col-md-9 col-sm-8 col-xs-8">
								<p id="title" class="med-green font14 font600">{{@$reminder_more->title}}</p>
								<p id="content" class="margin-t-m-10">{{@$reminder_more->description}}</p>
							</div>
							<div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 med-orange">
								<p class=" pull-right">
									<a data-toggle="modal" href="#form-modal-edit{{ @$reminder_more->id }}" name="{{@$reminder_more->id}}" class="edit_events"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a>&emsp;
									<a data-toggle="modal" href="#form-modal-delete" name="{{ @$reminder_more->id }}" id="js_delete_confirm" date-day="{{$day}}" class="js-delete-confirm"><i class="fa fa-trash-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
								</p>
							</div>
						</div>
					</div>
				</div>
				@elseif(@$reminder_more->arrange == 3)
				<?php $futureday_count ++;  ?>
				<p class="med-orange" @if($futureday_count == 1) style="display:block;" @else style="display:none;" @endif>Future Events</p>
				<div id="js_delete_list{{@$reminder_more->id}}" class="js_delete_list">
					<div id="form-modal-edit{{@$reminder_more->id}}" class="modal fade in" aria-labelledby="myModalLabel" aria-hidden="true"></div>
					<div class="box-header-view @if($array_count >1) hide @else show @endif">
						<h3 class="box-title">{{$day}} {{$month_orgin}}</h3>
						<div class="box-tools pull-right">
							<h5 style="margin-top: 5px;" class="med-orange"> {{ $week_day}}</h5>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header --> 
					<div class="box-body">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 js_count_list" id="event_list_{{@$reminder_more->id}}">
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
								<p class="med-orange font600 font20 pull-right margin-t-5">{{$time_in_12_hr_format}}</p>
							</div>
							<div class="col-lg-9 col-md-9 col-sm-8 col-xs-8">
								<p id="title" class="med-green font14 font600">{{@$reminder_more->title}}</p>
								<p id="content" class="margin-t-m-10">{{@$reminder_more->description}}</p>
							</div>
							<div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 med-orange">
								<p class=" pull-right">
									<a data-toggle="modal" href="#form-modal-edit{{ @$reminder_more->id }}" name="{{@$reminder_more->id}}" class="edit_events"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a>&emsp;
									<a data-toggle="modal" href="#form-modal-delete" name="{{ @$reminder_more->id }}" id="js_delete_confirm" date-day="{{$day}}" class="js-delete-confirm"><i class="fa fa-trash-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
								</p>
							</div>
						</div>
					</div>
				</div>
			@endif
		@endforeach
		</div>
		@endforeach
		</div>
	</div>
@endif