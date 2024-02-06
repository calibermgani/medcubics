<div class="modal-md">
	<div class="modal-content col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			<h4 class="modal-title">Add New Event </h4>
		</div>
		<div class="modal-body no-padding">
			<div class="no-shadow">
				<div class="box-body form-horizontal m-b-m-5">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-border-bottom">   
						{!! Form::open(array('id' => 'js-bootstrap-validator', 'class' => 'event-info-form','name' => 'event-info-form')) !!}
						<div class="form-group">
							{!! Form::label('Title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-popup']) !!} 
							<div class="col-lg-9 col-md-9 col-sm-12 col-xs-10">
								{!! Form::text('title',null,['class'=>'form-control input-sm-header-billing',"placeholder"=>"Title,location,etc..."]) !!}
							</div>	
						</div>
						<div class="form-group">
							{!! Form::label('Date', 'Date', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-popup']) !!} 
							<div class="col-lg-4 col-md-4 col-sm-5 col-xs-5">
								{!! Form::text('start_date',null,['class'=>'form-control input-sm-header-billing  js_date_picker font13','id'=>"start_date"]) !!}
							</div>																
							<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><i class="fa fa-arrows-h med-green"></i></div>
							<div class="col-lg-4 col-md-4 col-sm-5 col-xs-5">
								{!! Form::text('end_date',null,['class'=>'form-control input-sm-header-billing form-cursor end_date js_date_picker font13','id'=>"end_date"]) !!}
							</div>	
							<input type="hidden" id="end_date_hide" />
						</div>
						<div class="form-group">
							{!! Form::label('Time', 'Time', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-popup']) !!} 
							<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
								{!! Form::text('start_time',null,['class'=>'form-control input-sm-header-billing form-cursor','id'=>"start_time", 'placeholder'=>"00:00"]) !!}
							</div>	
							<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><i class="fa fa-arrows-h med-green"></i></div>
							<div class="col-lg-4 col-md-4 col-sm-5 col-xs-5">
								{!! Form::text('end_time',null,['class'=>'form-control input-sm-header-billing form-cursor end_time','id'=>"end_time", 'placeholder'=>"00:00"]) !!}
							</div>	
						</div>
						<div class="form-group">
							{!! Form::label('Invites', 'Invites', ['class'=>'col-lg-3 col-md-3 col-sm-10 col-xs-12 control-label-popup']) !!} 
							<div class="col-lg-9 col-md-9 col-sm-9 col-xs-10">
							{!! Form::select('participants[]',(array)$select_list, null, ['multiple'=>'multiple', 'class' => 'form-control select2','id'=>"select_box"]) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('Reminder', 'Reminder', ['class'=>'col-lg-3 col-md-3 col-sm-10 col-xs-12 control-label-popup']) !!} 
							<div class="col-lg-9 col-md-9 col-sm-10 col-xs-10">
								{!! Form::radio('reminder_type', 'one-time',true,['class'=>'flat-red js_reminder_type','id'=>'js_reminder_type']) !!} One Time&emsp;
								{!! Form::radio('reminder_type', 'repeat',null,['class'=>'flat-red js_reminder_type','id'=>'js_reminder_type']) !!} Repeat 
							</div>
						</div>
						
						<div class="reminder_type hide" id="js_reminder_type_repeat">
							<div class="form-group">
								{!! Form::label('', '', ['class'=>'col-lg-3 col-md-3 col-sm-10 col-xs-12 control-label']) !!} 
								<div class="col-lg-8 col-md-8 col-sm-10 col-xs-12">
									{!! Form::radio('reminder_type_repeat', 'on',true,['class'=>'flat-red col-lg-2 col-md-2 col-sm-6 col-xs-6 js_reminder_type','id'=>"js_reminder_type_repeat"]) !!}On 
									{!! Form::radio('reminder_type_repeat', 'never',null,['class'=>'col-lg-2 col-md-2 col-sm-12 col-xs-12 flat-red js_reminder_type','id'=>"js_reminder_type_repeat"]) !!} Never
									
								</div>
							</div>
							<div class="reminder_type_repeat" id="js_reminder_type_repeat_on">
								<div class="form-group">
									{!! Form::label('Repeated By', 'Repeated By', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label-popup med-green']) !!} 
									<div class="col-lg-4 col-md-4 col-sm-10 col-xs-10 select2-white-popup">
									{!! Form::select('repeated_by',[''=>'-- Select --','Daily' => 'Daily','Weekly' => 'Weekly','Monthly' => 'Monthly','Yearly' => 'Yearly'],'Daily',['class'=>'select2 form-control js_reminder_type_repeated_by','id'=>'js_reminder_type_repeated_by']) !!}
									</div>
									<div class="col-lg-4 col-md-4 col-sm-10 col-xs-10"></div>
								</div>
								<div class="repeated_by hide" id="js_reminder_type_repeated_by_Weekly">
									<div class="form-group margin-t-10">
										{!! Form::label('Reminder Days', 'Reminder Days', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label-popup med-green']) !!} 
										<div class="col-lg-8 col-md-8 col-sm-10 col-xs-10 "><span id="weekCal"></span>
										<input type="hidden" name="reminder_days" value="" id="reminder_days" /></div>
									</div>
								</div>
								<div class="form-group repeated_by hide" id="js_reminder_type_repeated_by_Monthly">
									{!! Form::label('Reminder Date', 'Reminder Date', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!} 
									<div class="col-lg-4 col-md-4 col-sm-10 col-xs-10">
										<i class="fa fa-calendar-o form-icon"></i> 
										{!! Form::text('reminder_date',null,['id'=>'repeat_type_month_reminder_date','placeholder'=>'mm/dd/yyyy','readonly', 'class'=>'form-control form-cursor js_date_picker']) !!}
									</div>
									<div class="col-lg-4 col-md-4 col-sm-1 col-xs-1"></div>
								</div>
								<div class="form-group repeated_by hide" id="js_reminder_type_repeated_by_Yearly">
									{!! Form::label('Reminder Date', 'Reminder Date', ['class'=>'col-lg-4 col-md-4 col-sm-10 col-xs-12 control-label']) !!} 
									<div class="col-lg-4 col-md-4 col-sm-10 col-xs-10">
										<i class="fa fa-calendar-o form-icon"></i> 
										{!! Form::text('reminder_date',null,['id'=>'repeat_type_year_reminder_date','placeholder'=>'mm/dd/yyyy','readonly', 'class'=>'form-control form-cursor js_date_picker']) !!}
									</div>
									<div class="col-lg-4 col-md-4 col-sm-1 col-xs-1"></div>
								</div>	
							</div>
						</div>
						<div class="form-group">                
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">                             
								{!! Form::textarea('description',null,['class'=>'form-control textarea-patient','placeholder'=>'Event Description','style'=>'border-color: #d2d6de;']) !!}  
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-12 col-md-12 col-sm-10 col-xs-10 text-center">
								<input type="hidden" value="true" id="form_submit" />
								<input type="hidden" value="create" id="form_type" />
								<input type="submit" value="Save" class="btn btn-medcubics-small update" />
								<a href=" "><input type="button" value="Cancel" class="btn btn-medcubics-small event-cancel" data-dismiss="modal"></a>
							</div>	
						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->