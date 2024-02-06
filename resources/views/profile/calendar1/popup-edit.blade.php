{!! HTML::script('js/bootstrapValidator.js ') !!}
<?php 
	$start_time  = date("g:i A", strtotime($reminder->start_time));
	$end_time  = date("g:i A", strtotime($reminder->end_time));
	$reminder->start_date  = date("m/d/Y",strtotime($reminder->start_date));
	$reminder->end_date  = date("m/d/Y", strtotime($reminder->end_date));
	$reminder->reminder_date=(($reminder->reminder_date =='')||($reminder->reminder_date =='0000-00-00'))?$reminder->start_date : $reminder->reminder_date;
?>
	 
<div class="modal-dialog">
	<div class="modal-content col-lg-9 col-md-9 col-sm-12 col-xs-12 no-padding">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			<h4 class="modal-title">Edit Event </h4>
		</div>
		<div class="modal-body">
			<div class="no-shadow">
				<div class="box-body form-horizontal">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">   
						{!! Form::open(array('id' => 'js-bootstrap-validator', 'class' => 'event-info-form','name' => 'event-info-form')) !!}
						<div class="form-group">
							{!! Form::label('Title', 'Title', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!}
							<div class="col-lg-8 col-md-8 col-sm-12 col-xs-10">
								{!! Form::text('title',@$reminder->title,['class'=>'form-control',"placeholder"=>"Title,location,etc..."]) !!}
							</div>	
						</div>
						<div class="form-group">
							{!! Form::label('Date', 'Date', ['class'=>'col-lg-4 col-md-4 col-sm-12 col-xs-12 control-label']) !!} 
							<div class="col-lg-3 col-md-3 col-sm-5 col-xs-5" style="width:31.3%;">
								{!! Form::text('start_date',@$reminder->start_date,['class'=>'form-control form-cursor js_date_picker','id'=>"start_date","style"=>"font-size:13px;"]) !!}
							</div>	
							<div class="col-lg-2 col-md-2 col-sm-1 col-xs-1" style="width:4%;top: 2px;padding: 0px;"><b class="med-green">&hArr;</b></div>
							<div class="col-lg-3 col-md-3 col-sm-5 col-xs-5" style="width:31.3%;">
								{!! Form::text('end_date',@$reminder->end_date,['class'=>'form-control form-cursor end_date js_date_picker' ,'id'=>"end_date","style"=>"font-size:13px;"]) !!}
							</div>	
							<input type="hidden" value ="{{@$reminder->end_date}}" id="end_date_hide" />
						</div>
						<div class="form-group">
							{!! Form::label('Time', 'Time', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label']) !!} 
							<div class="col-lg-3 col-md-3 col-sm-5 col-xs-5" style="width:31.3%;">
								{!! Form::text('start_time',$start_time,['class'=>'form-control form-cursor','id'=>"start_time"]) !!}
							</div>	
							<div class="col-lg-2 col-md-2 col-sm-1 col-xs-1" style="width:4%;top: 2px;padding: 0px;"><b class="med-green">&hArr;</b></div>
							<div class="col-lg-3 col-md-3 col-sm-5 col-xs-5" style="width:31.3%;">
								{!! Form::text('end_time',$end_time,['class'=>'form-control form-cursor end_time','id'=>"end_time", 'placeholder'=>"00:00"]) !!}
							</div>	
						</div>
						<div class="form-group">
							{!! Form::label('Invites', 'Invites', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!} 
							<div class="col-lg-8 col-md-8 col-sm-10 col-xs-10">
							{!! Form::select('participants[]', (array)@$select_list, @$reminder->participants, ['multiple'=>'multiple', 'class' => 'form-control select2','id'=>"select_box"]) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('Reminder', 'Reminder', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!} 
							<div class="col-lg-8 col-md-8 col-sm-10 col-xs-10">
								{!! Form::radio('reminder_type', 'one-time',(@$reminder->reminder_type =="one-time")?true:null,['class'=>'flat-red js_reminder_type','id'=>'js_reminder_type']) !!} One Time&emsp;
								{!! Form::radio('reminder_type', 'repeat',(@$reminder->reminder_type =="repeat")?true:null,['class'=>'flat-red js_reminder_type','id'=>'js_reminder_type']) !!} Repeat 
							</div>
						</div>
						
						<div class="reminder_type @if(@$reminder->reminder_type !='repeat') hide @endif" id="js_reminder_type_repeat">
							<div class="form-group">
								{!! Form::label('', '', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!} 
								<div class="col-lg-8 col-md-8 col-sm-10 col-xs-12 control-label">
									{!! Form::radio('reminder_type_repeat', 'on',(@$reminder->reminder_type =="never")?null:true,['class'=>'flat-red col-lg-2 col-md-2 col-sm-6 col-xs-6 js_reminder_type','id'=>"js_reminder_type_repeat"]) !!}On 
									{!! Form::radio('reminder_type_repeat', 'never',(@$reminder->reminder_type =="never")?true:null,['class'=>'col-lg-2 col-md-2 col-sm-12 col-xs-12 flat-red js_reminder_type','id'=>"js_reminder_type_repeat"]) !!} Never
									
								</div>
							</div>
							<div class="reminder_type_repeat" id="js_reminder_type_repeat_on">
								<div class="form-group">
									{!! Form::label('Repeated By', 'Repeated By', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!} 
									<div class="col-lg-4 col-md-4 col-sm-10 col-xs-10">
									{!! Form::select('repeated_by',[''=>'-- Select --','Daily' => 'Daily','Weekly' => 'Weekly','Monthly' => 'Monthly','Yearly' => 'Yearly'],@$reminder->repeated_by,['class'=>'select2 form-control js_reminder_type_repeated_by','id'=>'js_reminder_type_repeated_by']) !!}
									</div>
									<div class="col-lg-4 col-md-4 col-sm-10 col-xs-10"></div>
								</div>
								<div class="repeated_by @if(@$reminder->repeated_by =='Weekly')show @else hide @endif" id="js_reminder_type_repeated_by_Weekly">
									<div class="form-group">
										{!! Form::label('Reminder Days', 'Reminder Days', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!} 
										<div class="col-lg-8 col-md-8 col-sm-10 col-xs-10"><span id="weekCal" style="margin-top:5px;"></span>
										<input type="hidden" name="reminder_days" value="{{@$reminder->reminder_days}}" id="reminder_days" /></div>
									</div>
								</div>
								<div class="form-group repeated_by @if(@$reminder->repeated_by =='Monthly')show @else hide @endif" id="js_reminder_type_repeated_by_Monthly">
									{!! Form::label('Reminder Date', 'Reminder Date', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!} 
									<div class="col-lg-4 col-md-4 col-sm-10 col-xs-10">
										<i class="fa fa-calendar-o form-icon"></i> 
										{!! Form::text('reminder_date',@$reminder->reminder_date,['id'=>'repeat_type_month_reminder_date','placeholder'=>'mm/dd/yyyy','readonly',
										'class'=>'form-control form-cursor js_date_picker']) !!}
									</div>
									<div class="col-lg-4 col-md-4 col-sm-1 col-xs-1"></div>
								</div>
								<div class="form-group repeated_by @if(@$reminder->repeated_by =='Yearly')show @else hide @endif" id="js_reminder_type_repeated_by_Yearly">
									{!! Form::label('Reminder Date', 'Reminder Date', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!} 
									<div class="col-lg-4 col-md-4 col-sm-10 col-xs-10">
										<i class="fa fa-calendar-o form-icon"></i> 
										{!! Form::text('reminder_date',@$reminder->reminder_date,['id'=>'repeat_type_year_reminder_date','placeholder'=>'mm/dd/yyyy','readonly',
										'class'=>'form-control form-cursor js_date_picker']) !!}
									</div>
									<div class="col-lg-4 col-md-4 col-sm-1 col-xs-1"></div>
								</div>	
							</div>
						</div>
						<div class="form-group">                
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">                             
								{!! Form::textarea('description',@$reminder->description,['class'=>'form-control textarea-patient','placeholder'=>'Event Description','style'=>'border-color: #d2d6de;']) !!}  
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-12 col-md-12 col-sm-10 col-xs-10" style="text-align: center;">
								<input type="hidden" value="true" id="form_submit" />
								<input type="hidden" value="update" id="form_type" />
								<input type="hidden" value="{{ @$reminder->id }}" id="event_id" />
								<input type="submit" value="Save" class="btn btn-medcubics update" style="font-size: 12px;" />
								<input type="button" value="Cancel" class="btn btn-medcubics event-cancel" style="font-size: 12px;" data-dismiss="modal" />
							</div>	
						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

@push('view.scripts') 
	<script>
	$(".js_date_picker").datepicker({
						minDate: 0,
						changeMonth: true,
						changeYear: true
					});
	</script>
@endpush