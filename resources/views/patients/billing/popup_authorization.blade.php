<div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
    <div class="box-body form-horizontal no-padding">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
            <div class="box box-view no-shadow no-padding  yes-border">
                
                <div class="box-body no-padding table-responsive margin-t-10">
                    <table class="table table-striped table-separate" id='auth_result'>    
                        <thead>
                            <tr>
                                @if(!$is_hide_process) 
                                <th>&emsp;</th>
                                @endif
                                <th>Auth No</th>
                                <th>Insurance</th> 
                                <th>POS</th>
                                <th>Allowed Visits</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($exist_authorizations))
                            @foreach($exist_authorizations as $exist_authorization)
                            <?php $auth_number_value[] = $exist_authorization->authorization_no; ?>
                            <tr>
                                 @if(!$is_hide_process) 
                                <td class="<?php echo 'insurance-' . @$exist_authorization->insurance_details->id ?>">{!! Form::radio('status', 'Cash',null,['class'=>'js-popupauth', 'id' => $exist_authorization->id, 'data-val' => $exist_authorization->authorization_no]) !!}<label for='{{$exist_authorization->id}}'>&nbsp; </label></td>
                                 @endif
                                <td class="js-authno-<?php echo $exist_authorization->id ?>">{{$exist_authorization->authorization_no}}</td>
                                <td>{!!App\Http\Helpers\Helpers::getInsuranceName(@$exist_authorization->insurance_details->id)!!}</td>
                                <td>{{@$exist_authorization->pos_detail->code }}</td>
                                <td>{{@$exist_authorization->allowed_visit }}</td>
                                <td>{{(!empty($exist_authorization->start_date) && ($exist_authorization->start_date != '1970-01-01') && ($exist_authorization->start_date != '0000-00-00')?App\Http\Helpers\Helpers::dateFormat($exist_authorization->start_date):'-')}}</td>
                                <td>{{(!empty($exist_authorization->end_date) && ($exist_authorization->end_date != '1970-01-01')&& ($exist_authorization->end_date != '0000-00-00')?App\Http\Helpers\Helpers::dateFormat($exist_authorization->end_date):'-')}}</td>
                                <td class="js-startdate-<?php echo $exist_authorization->id ?> hide">{{(!empty($exist_authorization->start_date) && ($exist_authorization->start_date != '1970-01-01')?App\Http\Helpers\Helpers::dateFormat($exist_authorization->start_date, 'dob'):'-')}}</td>
                                <td class="js-enddate-<?php echo $exist_authorization->id ?> hide">{{(!empty($exist_authorization->end_date) && ($exist_authorization->end_date != '1970-01-01')?App\Http\Helpers\Helpers::dateFormat($exist_authorization->end_date, 'dob'):'-')}}</td>
                            </tr>
                            @endforeach 
                            @else
                            <tr><td colspan="7" class="med-gray-dark text-center line-height-26">{{ trans("common.validation.no_record") }}</td></tr>
                            @endif  
                            <?php $auth_val = isset($auth_number_value)?json_encode($auth_number_value):"";?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div> 
        @if(!$is_hide_process)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-15 margin-t-m-10"><!--  Left side Content Starts -->
            <div class="box box-view no-shadow collapsed-box yes-border"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="users-add"></i> <h3 class="box-title">Create Authorization</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool js-collapse" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button>
                    </div>
                </div><!-- /.box-header -->                                 
                <div class="box-body js-collpased p-b-0 form-horizontal">
                    {!! Form::open(['url'=>'patients/billing_authorization/add_auth','id' => 'js-auth-pop', 'class' => 'js-auth-form popupmedcubicsform']) !!}
                    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                         <div class="form-group">                             
                            {!! Form::label('pos', 'POS', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label star']) !!}
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                                {!! Form::select('pos_id', array('' => '-- Select --') + (array)$pos, null,['class'=>'select2 form-control', 'id' => 'js_pos']) !!}
                            </div>                                                    
                        </div>
                        <div class="form-group">                             
                            {!! Form::label('authorization_no', 'Auth No', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label star']) !!}                           
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                {!! Form::text('authorization_no',null,['class'=>'form-control input-sm-header-billing', 'maxlength'=>'29']) !!}
                                <span id='auth_authorization_no_err' style='display:none;'><small class='help-block med-red' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>Enter Auth#No!</small></span>                                  
                            </div>                                                    
                        </div>
                        <div class="form-group">
                            {!! Form::label('start_date', 'Start Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label']) !!}
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>    
                                {!! Form::text('start_date',null,['class'=>'form-control dm-date input-sm-header-billing js-auth_datepicker','placeholder'=>Config::get('siteconfigs.default_date_format'), 'id' => 'start_date']) !!} 
                            </div>                                                    
                        </div>
                        <div class="form-group">
                            {!! Form::label('end_date', 'End Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label']) !!}
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>  
                                {!! Form::text('end_date',null,['class'=>'form-control dm-date input-sm-header-billing js-auth_datepicker','placeholder'=>Config::get('siteconfigs.default_date_format'), 'id' => 'end_date']) !!} 
                            </div>                                                   
                        </div>
                        {!!Form::hidden('patient_id', $patient_id) !!}                        
                        <div class="form-group" @if(@$registration->allowed_visit !=1) style="display:none;" @endif>                             
                            {!! Form::label('allowed_visit', 'Allowed Visits', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label']) !!}                           
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                {!! Form::text('allowed_visit',null,['class'=>'form-control input-sm-header-billing js_allowed_visit dm-auth-visits']) !!} 
                            </div>                                                    
                        </div> 
                        <div class="form-group margin-b-10" @if(@$registration->allowed_visit !=1) style="display:none;" @endif>
                             {!! Form::label('Auth Notes', 'Auth Notes', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label']) !!} 
                             <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">
                                {!! Form::textarea('authorization_notes',null, ['class' => 'form-control input-sm-header-billing','maxlength'=>500]) !!}   
                            </div>                                
                        </div>
                        <!--
                        <div class="form-group" @if(@$registration->alert_on_appointment !=1) style="display:none;" @endif>                             
                            {!! Form::label('alert_on_appointment', 'Alert On Appt', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label']) !!}                           
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                {!! Form::radio('alert_appointment', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; 
                                {!! Form::radio('alert_appointment', 'No', true,['class'=>'flat-red']) !!} No                                       
                            </div>                                                    
                        </div>
                        -->                      
                    </div>
                    {!! Form::hidden('alert_appointment','Yes') !!} 
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group hide">                           
                            {!! Form::hidden('insurance_id',null,['id'=>'auth_insurance_id']) !!}
                            <div class="col-md-1 col-sm-1 col-xs-2"></div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="modal-footer">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small']) !!} 
                          <!--  <button class="btn btn-medcubics-small js_popup_commonform_reset" type="button">Cancel</button> -->
                            <button class="btn btn-medcubics-small close_popup" type="button">Close</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div><!-- /.box Ends-->
            </div>
        </div>
         @endif
    </div>
</div>
@if(!$is_hide_process)
<script>
	$(document).mapKey('Alt+E',function(e){
	    if (!$("body").hasClass("modal-open")) { 
	        var crAuth = $(".js-collpased").css('display');
	        if(crAuth == 'none'){
	            $(".js-collpased").css("display","block");
	            $("#js_pos").select2('open');
	        }
	    }
	});
	
	$(document).mapKey('Alt+s', function (e) {
	    if (!$("body").hasClass("modal-open")) { 
			var auth = $("#auth").css('display');
			if(auth == 'block'){
				county = 0;
				$("#js-auth-pop").submit();
			} else {
	         //removed this functionality
	         // $('form[name="chargeform"]').submit();
	         // window.onbeforeunload = UnPopIt;
			}
			return false;
	    }
	});
	
	var auth_value = '<?php echo $auth_val;?>';
	$(document).ready(function () {
		$('#start_date').on('change', function () {
			$('#js-auth-pop').bootstrapValidator('revalidateField', 'start_date');
			$('#js-auth-pop').bootstrapValidator('revalidateField', 'end_date');
		});
		$('#end_date').on('change', function () {                
			$('#js-auth-pop').bootstrapValidator('revalidateField', 'end_date');
			$('#js-auth-pop').bootstrapValidator('revalidateField', 'start_date');
		});                          
		$("#js_pos").on('change', function () { 
			 $('span#pos_name').remove();
		});
		$("input[name='authorization_no']").on('keyup', function () {  
			$('span#auth_no').remove();
		});
		$('#js-auth-pop').bootstrapValidator({
			framework: 'bootstrap',
			excluded: ':disabled',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				 authorization_no: {
					validators: {
						notEmpty: {
							message: auth_no
						},
						regexp: {
							regexp: /^[A-Za-z0-9]+$/,
							message: alphanumeric_lang_err_msg
						},
						callback: {
							message: auth_limit,
							callback: function (value, validator) { 
								auth_value_def  = [];
								if(auth_value)
								auth_value_def = JSON.parse(auth_value);
								if($.inArray(value, auth_value_def) > -1){
									return {
										valid:false,
										message:'{{ trans("practice/patients/popup_authorization.validation.auth_no_exist") }}'
									}
								}
								var authorization_no_value = value.trim();
								var add_length = authorization_no_value.length;
								return (add_length>29) ? false : true;
							}
						}
					}
				},
				allowed_visit: {
					message: '',
					trigger: 'keyup change',
					validators: {
						numeric: {
							message: only_numeric_lang_err_msg
						},
						regexp: {
							regexp:  /^[1-9][0-9]*$/,
							message: "Enter valid number"
						},
					}
				},
				pos_id: {
					validators: {
						notEmpty: {
							message: pos
						}               
					}
				},
			   /* start_date: {
					message: '',
					trigger: 'keyup change',
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: date_format
						},
						callback: {
							message: eff_date_valid,
							callback: function(value, validator, $field) {
								var start_date = $('.js-auth-form').find('[name="start_date"]').val();
								var end_date = validator.getFieldElements('start_date').val();
								var current_date=new Date(start_date);
								var d=new Date(end_date);   
								return (start_date !='' && d.getTime() > current_date.getTime() && end_date != '')? false : true;
							}
						}
					}
				},
				end_date: {
					message: '',
					trigger: 'keyup change',
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: date_format
						},
						callback: {
							message: end_date,
							callback: function (value, validator) {
								var m = validator.getFieldElements('start_date').val();
								var n = value;
								var current_date=new Date(n);
								if(current_date != 'Invalid Date' && n != '' && m != '') {
									var getdate = daydiff(parseDate(m), parseDate(n));
									return (getdate >= 0)? true : false; 
								} else if(current_date != 'Invalid Date' && n != '' && m == '') {
									return {
										valid:false,
										message: '{{ trans("practice/patients/billing.validation.start_date") }}'
									}
								} 
								else  return true;
							}
						}
					}
				}, */
				alert_visit_remains: {
					message: '',
					trigger: 'keyup change',
					validators: {
						numeric: {
							message: only_numeric_lang_err_msg
						},
						between: {
							min: 0,
							max: 'allowed_visit',
							message: visit_remains
						}
					}
				}, 
				start_date: {
					message: '',
					trigger: 'keyup change',
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: 'Enter valid date format'//'{{ trans("common.validation.date_format") }}'
						},
						callback: {
							message: '',
							callback: function (value, validator) { 
								console.log("value"+value);
								var end_date = validator.getFieldElements('end_date').val();
								if(value != '' && end_date != ''){
									var response = startDate(value,end_date);
									if (response != true){
										return {
											valid: false,
											message: 'This date is not before end date'
										}; 
									}   
								}
								return true;
							}
						}
					}
				},
				end_date: {
					message: '',
					trigger: 'keyup change',
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: 'Enter valid date format'//'{{ trans("common.validation.date_format") }}'
						},
						callback: {
							message: '',
							callback: function (value, validator) {
								var start_date = validator.getFieldElements('start_date').val();
								var end_date = value;
								
								if(end_date != ''){
									var response = endDate(start_date,end_date);
									if (response != true){
										return {
											valid: false,
											message: 'This date is not after start date'
										}; 
									} 
								}
								return true;
							}
						}
					}
				},
			}
		});
	});
	
	function daydiff(first, second) {
		return Math.round((second - first) / (1000 * 60 * 60 * 24));
	}
	
	function parseDate(str) {
		var mdy = str.split('/')
		return new Date(mdy[2], mdy[0] - 1, mdy[1]);
	}
	var today_practice = '{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), "m/d/Y") }}'; 
</script>
@endif