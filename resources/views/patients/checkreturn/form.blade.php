<input type="hidden" name="valid_npi_bootstrap" value="" />
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!--  Left side Content Starts -->   
	<div class="box box-info no-shadow"><!-- General Info Box Starts -->
		<div class="box-block-header with-border">
			<i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		  <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
		  <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.returncheck") }}' />
		<div class="box-body form-horizontal margin-l-10">
			<div class="form-group">
				{!! Form::label('Check No', 'Check No', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}
				<div class="col-lg-2 col-md-2 col-sm-8 col-xs-12 @if($errors->first('check_no')) error @endif">
					{!! Form::text('check_no',null,['class'=>'form-control ','id'=>'js_check_no','maxlength'=>'20']) !!}
					{!! $errors->first('check_no', '<p> :message</p>')  !!}							
				</div>                
			</div>   
			 <div class="form-group">
				
				{!! Form::label('check date', 'Check Date', ['class'=>'ccol-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}
					
				<div class="col-lg-2 col-md-2 col-sm-8 col-xs-12 ">
				<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
				  {!! Form::text('check_date',null,['id'=>'dateofbirth','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control dm-date doi form-cursor']) !!}  
					{!! $errors->first('check_date', '<p> :message</p>')  !!}                          
				</div>                
			</div> 
			<div class="form-group">
				{!! Form::label('Financial Charge', 'Financial Charge', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}                                                  
				<div class="col-lg-2 col-md-2 col-sm-8 col-xs-12">  
					 {!! Form::text('financial_charges',null,['class'=>'form-control allownumericwithdecimal js_amt_format ','id'=>'js_financial_charge']) !!}
					 {!! $errors->first('financial_charges', '<p> :message</p>')  !!}    
				</div>                
			</div>
			
			<div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
			{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
			@if(strpos($currnet_page, 'edit') !== false)
				@if($checkpermission->check_url_permission('patients/'.$patient_id.'returncheck/{id}/delete') == 1)
					<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure you want to delete?" href="{{ url('patients/'.$patient_id.'/returncheck/'.$returncheck->id.'/delete') }}">Delete</a>
				@endif
			<a href="javascript:void(0)" data-url="{{ url('patients/'.$patient_id.'/returncheck/'.$returncheck->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@else
				<a href="javascript:void(0)" data-url="{{ url('patients/'.$patient_id.'/returncheck') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif
		</div>	
		</div><!-- /.box-body -->
		
	</div><!-- General info box Ends-->
</div><!--  Left side Content Ends -->   

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
		var id = $('').attr('dateofbirth');
        $("#dateofbirth").datepicker({
            yearRange: '1900:+0',
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true,
            maxDate: '0',
            onClose: function (selectedDate) {
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="check_date"]'));
            }
        });
		$("#dob_new").datepicker({
            yearRange: '1900:+0',
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true,
            maxDate: '0',
            onClose: function (selectedDate) {
                $('#js-bootstrap-validator1').bootstrapValidator('revalidateField', $('input[name="check_date"]'));
            }
        });
        $('#js-bootstrap-validator').bootstrapValidator({
		
			message : 'This value is not valid',
			excluded : ':disabled',
			feedbackIcons : {
				valid : 'glyphicon glyphicon-ok',
				invalid : 'glyphicon glyphicon-remove',
				validating : 'glyphicon glyphicon-refresh'
			},
            fields: {
                 check_no: {
					validators: {
						notEmpty: {
						message: '{{ trans("practice/patients/payments.validation.check_no") }}'
					},
					regexp:{
						regexp: /^[A-Za-z0-9]+$/,
						message: '{{ trans("common.validation.alphaspace") }}'
					},                                                       
					}

				},
                check_date: {
					message: '',
					validators: {
						notEmpty: {
						message: '{{ trans("practice/patients/checkReturn.validation.check_date") }}'
						},
						date: {
							format: 'MM/DD/YYYY',
							message: '{{ trans("common.validation.date_format") }}'
						},
						callback: {
							message: 'Future date not allowed',
							callback: function (value, validator, $field) {
								var check_date = $('#js-bootstrap-validator').find('[name="check_date"]').val();
								var current_date = new Date(check_date);
								var d = new Date();
								return (check_date != '' && d.getTime() < current_date.getTime()) ? false : true;
							}
						}
					}
				},
                 financial_charges: {
					validators: {
						notEmpty: {
						message: '{{ trans("practice/patients/checkReturn.validation.financial_charges") }}'
						},
						 callback: {
							message: '',
							callback: function (value, validator) {
								var message = 'test';
								var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,6}$/:/^[0-9.]{0,9}$/;
								var count = value.split(".").length - 1;
								if(count>1 || value.length >=11) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
									}; 
								}
								else if(value.length ==11){
								 return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
									};
								}
								return (!regexp.test(value)) ? false:true;
								return true;
							}
						}					                                   
					}
				},
            }
        });
    });
</script>
@endpush