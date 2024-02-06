@yield('registration')

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding"><!--  Col-12 Starts -->
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->
	<div class="box box-view no-shadow"><!--  Box Starts -->
		<div class="box-header-view" id="js_demographic">
			<h3 class="box-title">&emsp;<input type="checkbox" id="js_demographic_check" class="js_menu flat-red" @if((@$registration->email_id ==1)&&(@$registration->driving_license ==1)&&(@$registration->primary_care_provider ==1)&&(@$registration->ethnicity ==1)&&(@$registration->primary_facility ==1)&&(@$registration->race ==1)&&(@$registration->send_email_notification ==1)&&(@$registration->preferred_language ==1)&&(@$registration->auto_phone_call_reminder ==1)&&(@$registration->marital_status ==1)&&(@$registration->preferred_communication ==1)) checked="true" @endif /> Demographic</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body table-responsive line-height-26" id="js_demographic_check">
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('email_id','1',null,['id'=>'email_id','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Email ID 							
				{!! $errors->first('email_id', '<p> :message</p>')  !!}
			</div>
			
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('driving_license', '1',null,['id'=>'driving_license','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Driving License 
				{!! $errors->first('driving_license', '<p> :message</p>')  !!}
			</div>
			
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('primary_care_provider', '1',null,['id'=>'primary_care_provider','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Primary Care Provider
				{!! $errors->first('primary_care_provider', '<p> :message</p>')  !!}
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('ethnicity', '1',null,['id'=>'ethnicity','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Ethnicity
				{!! $errors->first('ethnicity', '<p> :message</p>')  !!}
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('primary_facility', '1',null,['id'=>'primary_facility','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Primary Facility
				{!! $errors->first('primary_facility', '<p> :message</p>')  !!}
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('race', '1',null,['id'=>'race','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Race
				{!! $errors->first('race', '<p> :message</p>')  !!}
			</div>
			
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('send_email_notification', '1',null,['id'=>'send_email_notification','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Send Email Notification
				{!! $errors->first('send_email_notification', '<p> :message</p>')  !!}
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('preferred_language', '1',null,['id'=>'preferred_language','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Preferred Language
				{!! $errors->first('preferred_language', '<p> :message</p>')  !!}
			</div>
			
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('auto_phone_call_reminder', '1',null,['id'=>'gender_m','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Auto Phone Call Reminders
				{!! $errors->first('auto_phone_call_reminder', '<p> :message</p>')  !!}
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('marital_status', '1',null,['id'=>'marital_status','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Marital Status
				{!! $errors->first('marital_status', '<p> :message</p>')  !!}
			</div>
			
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('preferred_communication', '1',null,['id'=>'preferred_communication','class'=>'js_demographic_check js_submenu flat-red']) !!}&emsp;Preferred Communication
				{!! $errors->first('preferred_communication', '<p> :message</p>')  !!}
			</div>

		</div><!-- /.box Ends-->
	</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
	<div class="box box-view no-shadow"><!--  Box Starts -->
		<div class="box-header-view" id="js_authetication">    
			<h3 class="box-title">&emsp;<input type="checkbox" id="js_authetication_check" class="js_menu flat-red" @if((@$registration->alert_on_appointment ==1)&&(@$registration->allowed_visit ==1)) checked="true" @endif /> Authorization</h3>

			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body table-responsive line-height-26" id="js_authetication_check">
			<!--
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('alert_on_appointment', '1',null,['class'=>'js_authetication_check js_submenu flat-red']) !!}&emsp;Alert on Appointment 
				{!! $errors->first('alert_on_appointment', '<p> :message</p>')  !!}
			</div>
		-->	
		{!! Form::hidden('alert_on_appointment', 1) !!}		
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::checkbox('allowed_visit', '1',null,['class'=>'js_visit_rel_fields js_authetication_check js_submenu flat-red']) !!}&emsp;Allowed Visits 
				{!! $errors->first('allowed_visit', '<p> :message</p>')  !!}
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box-body -->
	</div><!--  Right side Content Ends -->	
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
				<h3 class="box-title">&emsp;<input type="checkbox" id="js_insurance_check" class="js_menu flat-red" @if((@$registration->insured_ssn ==1)&&(@$registration->insured_dob ==1)&&(@$registration->group_name_id ==1)) checked="true" @endif /> Insurance</h3>

				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive line-height-26" id="js_insurance_check">

				<div class="col-lg-6 col-md-6 col-sm-6">
					{!! Form::checkbox('insured_ssn', '1',null,['class'=>'js_insurance_check js_submenu flat-red']) !!}&emsp;Insured SSN
					{!! $errors->first('insured_ssn', '<p> :message</p>')  !!}
				</div>
				
				<div class="col-lg-6 col-md-6 col-sm-6">
					{!! Form::checkbox('insured_dob', '1',null,['class'=>'js_insurance_check js_submenu flat-red']) !!}&emsp;Insured DOB
					{!! $errors->first('insured_dob', '<p> :message</p>')  !!}
				</div>

				<div class="col-lg-6 col-md-6 col-sm-6">
					{!! Form::checkbox('group_name_id', '1',null,['class'=>'js_insurance_check js_submenu flat-red']) !!}&emsp;Group Name / ID
					{!! $errors->first('group_name_id', '<p> :message</p>')  !!}
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box-body -->
		</div><!--  Left side Content Ends -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view" id="js_contacts">
				<h3 class="box-title">&emsp;<input type="checkbox" id="js_contacts_check" class="js_menu flat-red" @if((@$registration->guarantor ==1)&&(@$registration->emergency_contact ==1)&&(@$registration->employer ==1)) checked="true" @endif /> Contacts</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive line-height-26" id="js_contacts_check">

				<div class="col-lg-6 col-md-6 col-sm-6">
					{!! Form::checkbox('guarantor', '1',null,['class'=>'js_contacts_check js_submenu flat-red']) !!}&emsp;Guarantor Contact
					{!! $errors->first('guarantor', '<p> :message</p>')  !!}
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6">
					{!! Form::checkbox('emergency_contact', '1',null,['class'=>'js_contacts_check js_submenu flat-red']) !!}&emsp;Emergency Contact
					{!! $errors->first('emergency_contact', '<p> :message</p>')  !!}
				</div>

				<div class="col-lg-6 col-md-6 col-sm-6">
					{!! Form::checkbox('employer', '1',null,['class'=>'js_contacts_check js_submenu flat-red']) !!}&emsp;Employer Contact
					{!! $errors->first('employer', '<p> :message</p>')  !!}
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box-body -->
		</div><!--  Right side Content Ends -->
	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
		{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
		<a href="{{ url('registration')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
	</div>
</div><!-- Col 12 Ends -->

@push('view.scripts')
<script type="text/javascript">
$(document).on('ifToggled', ".js_visit_rel_fields", function () {
	if($(this).is(':checked')){
		$(".js_visit_rel_fields").prop('checked', true);
	}
	else {
		$(".js_visit_rel_fields").prop('checked', false);
	}
	$('.js_visit_rel_fields').iCheck('update');
	menuCheck('js_authetication_check');
});
</script>
@endpush