<div class="box box-view no-shadow no-border"><!--  Box Starts -->
{!! Form::open(['url'=>'patients/billing_employer','id' => 'ModelForm', 'files' => true,'class'=>'popupmedcubicsform']) !!}
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Left side Content Starts -->
		<div class="form-horizontal"><!-- Box Starts -->
			<div class="form-group-billing">			
				{!! Form::label('employer_status', 'Employment Status', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label-popup']) !!} 
				<div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
				{!! Form::select('employer_status', [''=>'-- Select --','Employed' => 'Employed','Self Employed' => 'Self Employed','Retired' => 'Retired','Active Military Duty'=>'Active Military Duty','Employed(Full Time)'=>'Employed(Full Time)','Employed(Part Time)'=>'Employed(Part Time)','Unknown'=>'Unknown'],@null,['class'=>'select2 form-control input-sm-modal-billing']) !!}
				</div>
			</div>
			<div class="form-group-billing">
				{!! Form::label('Employer Name', 'Employer Name', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label-popup']) !!}
				<div class="col-lg-6 col-md-6 col-sm-7 col-xs-10"> 
					{!! Form::text('employer_name',null,['class'=>'form-control input-sm-modal-billing']) !!}
				</div>
			</div> 
			<div class="form-group-billing">
				{!! Form::label('Home Phone', 'Work Phone / Ext', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label-popup']) !!}
				<div class="col-md-4 col-sm-5 col-xs-6">  
					{!! Form::text('employer_work_phone',null,['class'=>'form-control js-number dm-phone']) !!}
				</div>
				<div class="col-md-2 col-sm-2 col-xs-4"> 
					{!! Form::text('employer_phone_ext',null,['class'=>'form-control input-sm-modal-billing js-number dm-phone-ext']) !!}
				</div>
			</div> 
                    
                    
            <div class=" js-address-class" id="js-address-employer-address">
			 	{!! Form::hidden('patient_id',$patient_id) !!}
			 	{!! Form::hidden('category','Employer') !!}
				{!! Form::hidden('emp_type','patients',['class'=>'js-address-type']) !!}
				{!! Form::hidden('emp_type_id',null,['class'=>'js-address-type-id']) !!}
				{!! Form::hidden('type_category','employer_address',['class'=>'js-address-type-category']) !!}
				{!! Form::hidden('emp_address1',@$address_flag['empa']['address1'],['class'=>'js-address-address1']) !!}
				{!! Form::hidden('emp_city',@$address_flag['empa']['city'],['class'=>'js-address-city']) !!}
				{!! Form::hidden('emp_state',@$address_flag['empa']['state'],['class'=>'js-address-state']) !!}
				{!! Form::hidden('emp_zip5',@$address_flag['empa']['zip5'],['class'=>'js-address-zip5']) !!}
				{!! Form::hidden('emp_zip4',@$address_flag['empa']['zip4'],['class'=>'js-address-zip4']) !!}
				{!! Form::hidden('emp_is_address_match',@$address_flag['empa']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
				{!! Form::hidden('emp_error_message',@$address_flag['empa']['error_message'],['class'=>'js-address-error-message']) !!}

				<div class="form-group-billing">
					{!! Form::label('Address 1', 'Address 1', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label-popup']) !!}
					<div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 ">
						{!! Form::text('employer_address1',null,['maxlength'=>'25','id'=>'address1','class'=>'form-control input-sm-modal-billing js-address-check']) !!}                          
					</div>					
				</div> 

				<div class="form-group-billing">
					{!! Form::label('Address 2', 'Address 2', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label-popup']) !!}
					<div class="col-lg-6 col-md-7 col-sm-7 col-xs-10 ">                            
						{!! Form::text('employer_address2',null,['maxlength'=>'25','class'=>'form-control input-sm-modal-billing']) !!}
					</div>					
				</div> 

				<div class="form-group-billing">
					{!! Form::label('City / State', 'City / State', ['class'=>'col-lg-4  col-md-4 col-sm-3 col-xs-12 control-label-popup']) !!}
					<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">  
						{!! Form::text('employer_city',null,['maxlength'=>'25','class'=>'form-control input-sm-modal-billing js-address-check']) !!}
					</div>
					<div class="col-lg-2 col-md-3 col-sm-2 col-xs-4"> 
						{!! Form::text('employer_state',null,['class'=>'form-control input-sm-modal-billing js-address-check','maxlength'=>'2']) !!}
					</div>
				</div>   
				<div class="form-group-billing">
					{!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label-popup']) !!}
					<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">                             
						{!! Form::text('employer_zip5',null,['class'=>'form-control input-sm-modal-billing dm-zip5 js-number js-address-check','maxlength'=>'5']) !!}
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">                             
						{!! Form::text('employer_zip4',null,['class'=>'form-control input-sm-modal-billing dm-zip5 js-number js-address-check','maxlength'=>'4']) !!}                           
					</div>
					<div class="col-md-1 col-sm-2">            
						<span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
						<span class="js-address-success @if(@$address_flag['empa']['is_address_match'] != 'Yes') hide @endif">
							<a data-toggle="modal" href="#empa-address-modal{{ @$contact_count }}"><i class="fa {{Config::get('cssconfigs.charges.submit_check')}} icon-green-form"></i></a></span>	
						<span class="js-address-error @if(@$address_flag['empa']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#empa-address-modal{{ @$contact_count }}"><i class="fa {{Config::get('cssconfigs.common.close')}} icon-red-form"></i></a></span>
					</div> 
					<div class="col-md-1 col-sm-1 col-xs-2">            
					</div> 
				</div>       
			</div>   
                    
		</div>
	</div><!--  Left side Content Ends --> 

	 <!---address Information-->
	 
	 <div class="modal-footer m-b-m-15">
		{!! Form::submit("Submit", ['class'=>'btn btn-medcubics  form-group js-submit-popup-employer']) !!}
		<button class="btn btn-medcubics close_popup" type="button">Cancel</button>
	</div>
	{!! Form::close() !!} 
</div> 
<script>
	$(document).ready(function() {
		$('#ModelForm').bootstrapValidator({
			framework: 'bootstrap',
			excluded: ':disabled',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				employer_name: {
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/popup_employer.validation.employer_name") }}'
						}
					}
				}, 
				 employer_address1: {
					message: '',
					validators: {
						regexp: {
							regexp: /^[a-zA-Z0-9\s\.\-\,]{0,50}$/,
							message: '{{ trans("common.validation.address1_regex") }}'
						}
					}
				},
			}
		});
	});
</script>