<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.cpt") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
	<div class="box  no-shadow">
		<div class="box-block-header margin-b-10">
			<i class="livicon" data-name="doc-portrait"></i> <h3 class="box-title">Procedure Description</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		
		<div class="box-body  form-horizontal margin-l-10">
			<div class="form-group">                
				{!! Form::label('short_description', 'Short Description', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                            
				<div class="col-lg-5 col-md-5 col-sm-7 col-xs-10 @if($errors->first('short_description')) error @endif ">
					{!! Form::text('short_description',null,['class'=>'form-control','maxlength'=>28]) !!}  
					{!! $errors->first('short_description', '<p> :message</p>')  !!}                               
				</div>						                         
			</div>
			<div class="form-group">                
				{!! Form::label('long_description', 'Long Description', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                            
				<div class="col-lg-5 col-md-5 col-sm-7 col-xs-10 @if($errors->first('long_description')) error @endif ">
					{!! Form::textarea('long_description',null,['class'=>'form-control',]) !!}  
					{!! $errors->first('long_description', '<p> :message</p>')  !!}                               
				</div>						
			</div>
		</div>               
	</div>
</div>                        
<!--2nd Data-->
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
	<div class="box no-shadow">
		<div class="box-block-header margin-b-10">
			<i class="livicon" data-name="code"></i> <h3 class="box-title">Codes</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body  form-horizontal margin-l-10">
			<div class="form-group">        
				{!! Form::label('type_of_service', 'Type of service', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}                                                                                 
				<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('type_of_service')) error @endif ">
				{!! Form::text('type_of_service',null,['class'=>'form-control','maxlength'=>'50']) !!}
				{!! $errors->first('type_of_service', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">
				{!! Form::label('POS', 'POS', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('pos_id')) error @endif ">
					{!! Form::select('pos_id', array('' => '-- Select --') + (array)$pos,  null,['class'=>'form-control select2']) !!}
					{!! $errors->first('pos_id', '<p> :message</p>')  !!}  
				</div>
				<div class="col-sm-1"></div>
			</div> 
                    <div class="form-group">
                        {!! Form::label('applicable_sex', 'Applicable Sex', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                        <div class="control-group col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            {!! Form::radio('applicable_sex', 'Male',null,['class'=>'','id'=>'c-male']) !!} {!! Form::label('c-male', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                            {!! Form::radio('applicable_sex', 'Female',null,['class'=>'','id'=>'c-female']) !!} {!! Form::label('c-female', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                            {!! Form::radio('applicable_sex', 'Others',null,['class'=>'','id'=>'c-others']) !!} {!! Form::label('c-others', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}
                        </div>						
                    </div>
                    <div class="form-group">
                        {!! Form::label('Referring provider', 'Referring Provider', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="control-group col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            {!! Form::radio('referring_provider', 'Yes',null,['class'=>'','id'=>'c-r-y']) !!} {!! Form::label('c-r-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                            {!! Form::radio('referring_provider', 'No',true,['class'=>'','id'=>'c-r-n']) !!} {!! Form::label('c-r-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                        </div>
                    </div>
			<div class="form-group">        
				{!! Form::label('age_limit', 'Age Limit', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-2 col-md-4 col-sm-7 col-xs-10 @if($errors->first('age_limit')) error @endif ">
					{!! Form::text('age_limit',null,['maxlength' => '3', 'class'=>'form-control rvu_number']) !!}
					{!! $errors->first('age_limit', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('modifier', 'Modifier', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}                                                                                 
				<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('modifier')) error @endif ">
					 {!! Form::select('modifier_id[]',(array)$modifier,null,['class'=>'form-control select2','multiple']) !!}
					{!! $errors->first('modifier', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('revenue_code', 'Revenue Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('revenue_code')) error @endif ">
					{!! Form::text('revenue_code',null,['maxlength' => '5','class'=>'form-control']) !!}
					{!! $errors->first('revenue_code', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('drug_name', 'Drug Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('drug_name')) error @endif ">
					{!! Form::text('drug_name',null,['maxlength' => '250','class'=>'form-control js-letters-caps-format']) !!}
					{!! $errors->first('drug_name', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<?php $current_page  = Route::getFacadeRoot()->current()->uri(); ?>
			{!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
			<div class="form-group">        
				{!! Form::label('ndc_number', 'NDC Number', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('ndc_number')) error @endif ">
					{!! Form::text('ndc_number',null,['class'=>'form-control','maxlength'=>11]) !!}
					{!! $errors->first('ndc_number', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1">
				</div>
			</div>
			<div class="form-group">        
				{!! Form::label('min units', 'Min Units', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-2 col-md-4 col-sm-7 col-xs-10">
					{!! Form::text('min_units',null,['class'=>'form-control','maxlength'=>6]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('max units', 'Max Units', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-2 col-md-4 col-sm-7 col-xs-10">
					{!! Form::text('max_units',null,['class'=>'form-control', 'maxlength'=>6]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('anesthesia_unit', 'Anesthesia Base Unit', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-2 col-md-4 col-sm-7 col-xs-10 @if($errors->first('anesthesia_unit')) error @endif ">
					{!! Form::text('anesthesia_unit',null,['class'=>'form-control', 'maxlength'=>6]) !!}
					{!! $errors->first('anesthesia_unit', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('service_id_qualifier', 'Service ID Qualifier', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('service_id_qualifier')) error @endif ">
					{!! Form::select('service_id_qualifier', array('' => '-- Select --') + (array)$qualifier,null,['class'=>'form-control select2']) !!}
					{!! $errors->first('service_id_qualifier', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>                                                     
		</div>
	</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
	<div class="box no-shadow">
		<div class="box-block-header margin-b-10">
			<i class="livicon" data-name="credit-card"></i> <h3 class="box-title">Billing</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body  form-horizontal margin-l-10">
			<div class="form-group">        
				{!! Form::label('allowed_amount', 'Allowed Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('allowed_amount')) error @endif ">
					{!! Form::text('allowed_amount',null,['class'=>'form-control js_amount_separation','autocomplete'=>'off']) !!}
					{!! $errors->first('allowed_amount', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('billed_amount', 'Billed Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('billed_amount')) error @endif ">
					{!! Form::text('billed_amount',null,['class'=>'form-control js_amount_separation','autocomplete'=>'off']) !!}
					{!! $errors->first('billed_amount', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">
				{!! Form::label('required_clia_id', 'Required CLIA ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}                                                                                 
				<div class="control-group col-lg-6 col-md-6 col-sm-6">
				{!! Form::radio('required_clia_id', 'Yes',null,['class'=>'js_required_clia_id','id'=>'yes']) !!} {!! Form::label('yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
				{!! Form::radio('required_clia_id', 'No',true,['class'=>'js_required_clia_id','id'=>'no']) !!} {!! Form::label('no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
				</div>
				<div class="col-sm-1"></div>
			</div> 
			
			<div class='form-group js_required_clia_id_show @if(@$cpt->required_clia_id != "Yes") hide @endif'>        
				{!! Form::label('clia_id', 'CLIA ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('clia_id',null,['maxlength'=>'15','class'=>'form-control']) !!}
				</div>
				<div class="col-sm-1">
				</div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('workrvu', 'Work RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('work_rvu',null,['class'=>'form-control rvu_number','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('facility_practicervu', 'Facility practice RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('facility_practice_rvu',null,['class'=>'form-control rvu_number','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('nonfacility_practicervu', 'Non Facility practice RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('nonfacility_practice_rvu',null,['class'=>'form-control rvu_number','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('plirvu', 'PLI RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('pli_rvu',null,['class'=>'form-control rvu_number','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('total_facilityrvu', 'Total facility RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('total_facility_rvu',null,['class'=>'form-control rvu_number','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('total_nonfacilityrvu', 'Total Nonfacility RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('total_nonfacility_rvu',null,['class'=>'form-control rvu_number','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
                    <div class="form-group">
                        {!! Form::label('', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}                                                                                 
                        <div class="control-group col-lg-6 col-md-6 col-sm-6">
                            {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'a_active']) !!} {!! Form::label('a_active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                            {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'a_inactive']) !!} {!! Form::label('a_inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
			<div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>
			<div class="bottom-space-15 hidden-sm hidden-xs">&emsp;</div>
		</div>      
	</div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
	{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
	@if(strpos($current_page, 'edit') !== false &&$checkpermission->check_adminurl_permission('admin/cpt/{cpt_id}/delete') == 1)
		<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure would you like to delete?" href="{{ url('admin/cpt/'.$cpt->id.'/delete') }}">Delete</a></center>
	@endif
	@if(strpos($current_page, 'edit') == false)
		 <a href="javascript:void(0)" data-url="{{url('admin/cpt/')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	@endif
	@if(strpos($current_page, 'edit') !== false)
		 <a href="javascript:void(0)" data-url="{{url('admin/cpt/'.$cpt->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	@endif
</div>

	

@push('view.scripts')
<script type="text/javascript">
$(document).on('blur', '.js_amount_separation', function (e) {
    var response_val = $(this).val();
    if (response_val.length > 0) {
        var count_length = response_val.split(".").length - 1;
        if (count_length > 0) {
            var value = response_val.split(".");
            var replaced_str = (value[0].length == 0) ? 0 + "." + value[1] : parseFloat(response_val).toFixed(2);
            var replaced_str = (value[1].length == 1) ? response_val + 0 : parseFloat(response_val).toFixed(2);
        } else {
            replaced_str = response_val + ".00";
        }
        var start = this.selectionStart;
        end = this.selectionEnd;
        $(this).val(replaced_str);
        this.setSelectionRange(start, end);
    }
});

$(document).on('keydown', '.js_amount_separation', function (e) {
    if ($.inArray(e.keyCode, [116, 45, 189, 46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A,Ctrl+C,Ctrl+V, Command+A
        ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        if ((e.keyCode == 189 || e.keyCode == 45) && $(this).val().indexOf('-') != -1) {
            event.preventDefault();
        }
        else
            return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});
$(document).on( 'keypress', '#ndc_number, #min_units, #max_units,#age_limit, .rvu_number', function (e) {
//if the letter is not digit then display error and don't type anything
	if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
		return false;
	}
});
$(document).on( 'keypress', '.js_amount_separation, #anesthesia_unit,input[name="min_units"],input[name="max_units"]', function (e) {
	if (e.which != 8 && e.which != 46 && e.which != 0 && (e.which < 48 || e.which > 57)) {
		return false;
	}
});
$(document).on( 'ifToggled click', '.js_required_clia_id', function () {
	var chk = $(this).is(":checked");
	if(chk== true) {
		var current_id = $(this).attr("id");
		if(current_id == 'yes')
			$(".js_required_clia_id_show").removeClass("hide");
		else
			$(".js_required_clia_id_show").addClass("hide");
	}
});	
$(document).on( 'keyup', '[name="min_units"]', function () {
	$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="max_units"]'));
});	
$(document).ready(function() {
    
    $(function() {
          $("#effectivedate").datepicker({
              changeMonth: true,
              changeYear: true,
              onClose: function (selectedDate) {
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="effectivedate"]'));
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="terminationdate"]'));
              }
          });

        $("#terminationdate").datepicker({
              changeMonth: true,
              changeYear: true,
              onClose: function (selectedDate) {
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="effectivedate"]'));
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="terminationdate"]'));
              }

		});
    });
    $('#js-bootstrap-validator')                                              
    .bootstrapValidator({
        message: 'This value is not valid',
        excluded: ':disabled',
        feedbackIcons: {
            valid: '',
            invalid: '',
            validating: ''
        },
        fields: {
           /*  pos_id:{
                message:'pos field is invalid',
                validators:{
                    notEmpty:{
                        message: 'Select place of service!'
                    }
                }
            },
			medicare_global_period:{
                message:'',
                validators:{
                    notEmpty:{
                        message: 'Enter medicare Global Period'
            },
					 integer: {
                            message: 'medicare Global Period should be numeric',
                            thousandsSeparator: '',
                            decimalSeparator: '.'
                        }                    
                }
                    }, 
            medicare_allowable: {
				message:'',
                validators:{
					 integer: {
                            message: '{{ trans("admin/cpt.validation.units") }}',
                            thousandsSeparator: '',
                            decimalSeparator: '.'
                        }                    
                }
			},
			allowed_amount: {
				message:'',
                validators:{
					 integer: {
                            message: '{{ trans("admin/cpt.validation.units") }}',
                            thousandsSeparator: '',
                            decimalSeparator: '.'
                        }                    
                }
			},
		    billed_amount: {
				message:'',
                validators:{
					 integer: {
                            message: '{{ trans("admin/cpt.validation.units") }}',
                            thousandsSeparator: '',
                            decimalSeparator: '.'
                        }                    
                }
			},
			min_units: {
				message:'',
                validators:{
					 integer: {
                            message: '{{ trans("admin/cpt.validation.units") }}',
                            thousandsSeparator: '',
                            decimalSeparator: '.'
                        }                    
                }
			},
			max_units: {
				message:'',
                validators:{
					 integer: {
                            message: '{{ trans("admin/cpt.validation.units") }}',
                            thousandsSeparator: '',
                            decimalSeparator: '.'
                        }                    
                }
			},*/
			min_units: {
					message: '',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								if($.trim(value).length >0) {
									if(/^[0-9.]+$/.test(value)) {
										var count = value.split(".").length - 1;
										if(count>1) {
											return {
												valid: false,
												message: '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}'
											}; 
										}
										return true;
									}
									else {
										return {
												valid: false,
												message: '{{ trans("common.validation.numeric") }}'
											}; 
									}
								}
								return true;
							}
						}
					}
				},
				max_units: {
					message: '',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								if($.trim(value).length >0) {
									if(/^[0-9.]+$/.test(value)) {
										var min_value = validator.getFieldElements('min_units').val();
										var error_msg = '{{ trans("practice/practicemaster/cpt.validation.max_unit_limit") }}';
										var response = getMinAlert(min_value,value,error_msg);
										var count = value.split(".").length - 1;
										if(count>1) {
											return {
												valid: false,
												message: '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}'
											}; 
										}
										if(response != true){
											return {
												valid: false,
												message: response
											}; 
										} 
										return true;
									}
									else {
										return {
												valid: false,
												message: '{{ trans("common.validation.numeric") }}'
											}; 
									}
								}
								return true;
							}
						}
					}
				},
			'modifier_id[]': {
				message: '',
				validators: {
					callback: {
						message: '{{ trans("admin/cpt.validation.modifier_max_length") }}',
						callback: function (value, validator) {
							if(value =='' || value ==null) return true;
							return (value.length > 4) ? false : true;
						}
					}
				}
			},
            medium_description:{
                message:'',
                validators:{
                    notEmpty:{
                        message: '{{ trans("admin/cpt.validation.medium_des") }}'
                    },
                }
            }, 
			drug_name:{
				message:'',
				validators:{
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message:  '{{ trans("common.validation.alphaspace") }}'
					}
				}
			},
			code_type:{
				message:'',
				validators:{
					regexp:{
						regexp: /^[A-Za-z 0-9]+$/,
						message:  '{{ trans("common.validation.alphanumericspac") }}'
					}
				}
			},
			revenue_code: {
				message: '',
				validators: {
					regexp: {
						regexp: /^[0-9a-zA-Z]+$/,
						message: '{{ trans("common.validation.alphanumeric") }}'
					}
				}
			},
            clia_id:{
				message:'',
				trigger: 'change keyup',
				validators:{
					regexp:{
                        regexp: /^[a-zA-Z0-9\.\s]{0,15}$/,
                        message:  '{{ trans("admin/cpt.validation.clia_id") }}'
                    }			
				}
			},  			
            cpt_hcpcs:{
                message:'',
                validators:{
                    notEmpty:{
                        message: '{{ trans("admin/cpt.validation.cpt_hcpcs") }}'
                    },
                   regexp:{
                        regexp: /^[a-zA-Z0-9]{0,6}$/,
                        message: '{{ trans("admin/cpt.validation.cpt_hcpcs_regex") }}'
                    }
                }
            },
			billed_amount:{
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var message = '';
								var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
								var count = value.split(".").length - 1;
								if(count>1) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
									}; 
								}
								else if(value.length ==14){
								 return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
									};
								}
								return (!regexp.test(value)) ? false:true;
								return true;
							}
						}					                                   
					}
				},
				allowed_amount:{
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var message = '';
								var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
								var count = value.split(".").length - 1;
								if(count>1) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
									}; 
								}
								else if(value.length ==14){
								 return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
									};
								}
								return (!regexp.test(value)) ? false:true;
								return true;
							}
						}					                                   
					}
				},
            icd: {
				message: '',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var err = 0;
							var msg_1 = '{{ trans("admin/icd.validation.code_regex") }}';
							var msg_2 = '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}';
							if(value.length > 1  && value.length < 3)
								err = 1;
							var regexp = (value.indexOf(".")== -1) ? /^[a-zA-Z0-9]{0,7}$/:/^[a-zA-Z0-9.]{0,8}$/;
							if (!regexp.test(value)) 
								err = 1;
							else {
								var val_arr = value.split(".");
								var count = value.split(".").length - 1;
								if(count>1) 
									err = 2;
								if(val_arr.length > 1 && val_arr[0].length < 3) 
									err = 1;						
							}
							if(err > 0) {
								var issue =eval("msg_"+err);
								return {
									valid: false,
									message: issue
								};
							}
							return true;
						}
					}
				}
			},
			anesthesia_unit: {
				message: '',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var count = value.split(".").length - 1;
							if(count>1) {
								return {
									valid: false,
									message: '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}'
								}; 
							}
							return true;
						}
					}
				}
			},
            effectivedate: {
				message: '',
				trigger: 'keyup change',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: '{{ trans("common.validation.date_format") }}'
					},
					callback: {
						message: '{{ trans("common.validation.effectivedate") }}',
						callback: function (value, validator) {
							var termination_date = validator.getFieldElements('terminationdate').val();
							var response = startDate(value,termination_date);
							if (response != true){
								return {
									valid: false,
									message: response
								}; 
							} 
							return true;
						}
					}
				}
			},
			terminationdate: {
				message: '',
				trigger: 'keyup change',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: '{{ trans("common.validation.date_format") }}'
					},
					callback: {
						message: '',
						callback: function (value, validator) {
							var eff_date = validator.getFieldElements('effectivedate').val();
							var ter_date = value;
							var response = endDate(eff_date,ter_date);
							if (response != true){
								return {
									valid: false,
									message: response
								}; 
							} 
							return true;
						}

					}
				}
			},
			work_rvu:{
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var message = '';
							var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
							var count = value.split(".").length - 1;
							if(count>1) {
								return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
								}; 
							}
							else if(value.length ==14){
							 return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
								};
							}
							return (!regexp.test(value)) ? false:true;
							return true;
						}
					}					                                   
				}
			},
			facility_practice_rvu:{
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var message = '';
							var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
							var count = value.split(".").length - 1;
							if(count>1) {
								return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
								}; 
							}
							else if(value.length ==14){
							 return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
								};
							}
							return (!regexp.test(value)) ? false:true;
							return true;
						}
					}					                                   
				}
			},
			nonfacility_practice_rvu:{
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var message = '';
							var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
							var count = value.split(".").length - 1;
							if(count>1) {
								return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
								}; 
							}
							else if(value.length ==14){
							 return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
								};
							}
							return (!regexp.test(value)) ? false:true;
							return true;
						}
					}					                                   
				}
			},
			pli_rvu:{
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var message = '';
							var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
							var count = value.split(".").length - 1;
							if(count>1) {
								return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
								}; 
							}
							else if(value.length ==14){
							 return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
								};
							}
							return (!regexp.test(value)) ? false:true;
							return true;
						}
					}					                                   
				}
			},
			total_facility_rvu:{
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var message = '';
							var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
							var count = value.split(".").length - 1;
							if(count>1) {
								return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
								}; 
							}
							else if(value.length ==14){
							 return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
								};
							}
							return (!regexp.test(value)) ? false:true;
							return true;
						}
					}					                                   
				}
			},
			total_nonfacility_rvu:{
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var message = '';
							var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
							var count = value.split(".").length - 1;
							if(count>1) {
								return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
								}; 
							}
							else if(value.length ==14){
							 return {
									valid: false,
									message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
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

/*** Date function check start here ***/
	function startDate(start_date,end_date) {
		var date_format = new Date(end_date);
		if (end_date != '' && date_format !="Invalid Date") {
			return (start_date == '') ? '{{ trans("common.validation.eff_date_required") }}':true;
		}
		return true;
	}
	function endDate(start_date,end_date) {
		var eff_format = new Date(start_date);
		var ter_format = new Date(end_date);
		if (ter_format !="Invalid Date" && end_date != '' && eff_format !="Invalid Date" && end_date.length >7 && checkvalid(end_date)!=false) {
			var getdate = daydiff(parseDate(start_date), parseDate(end_date));
			return (getdate > 0) ? true : '{{ trans("common.validation.inactivedate") }}';
		}
		else if (start_date != '' && eff_format !="Invalid Date") {
			return (end_date == '') ? '{{ trans("common.validation.inactdate_required") }}':true;
		
		}
		return true;
	}
	function daydiff(first, second) {
		return Math.round((second-first)/(1000*60*60*24));
	}
	function parseDate(str) {
		var mdy = str.split('/')
		return new Date(mdy[2], mdy[0]-1, mdy[1]);
	}
	function checkvalid(str) {
		var mdy = str.split('/');
		if(mdy[0]>12 || mdy[1]>31 || mdy[2].length<4 || mdy[0]=='00' || mdy[0]=='0' || mdy[1]=='00' || mdy[1]=='0' || mdy[2]=='0000') {
			return false;
		}
	}
	/*** Date function check end here ***/
</script>
@endpush