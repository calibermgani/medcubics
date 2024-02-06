<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.icd") }}' />

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20" >
            <div class="box no-shadow">
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="doc-landscape"></i> <h3 class="box-title">Code Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"	tabindex="-1"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body  form-horizontal margin-l-10">                
                    <div class="form-group margin-t-0 margin-b-20">
                        {!! Form::label('gender', 'Gender', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12">
                            {!! Form::radio('sex', 'Male', null,['class'=>'','id'=>'a_male']) !!} {!! Form::label('a_male', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                            {!! Form::radio('sex', 'Female',null,['class'=>'','id'=>'a_female']) !!} {!! Form::label('a_female', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                            {!! Form::radio('sex', 'Others',null,['class'=>'','id'=>'a_others']) !!} {!! Form::label('a_others', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}
                        </div>                        
                    </div>

                    <div class="form-group">    
                        {!! Form::label('age_limit_lower', 'Age Limit Lower', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                        <div class="col-lg-2 col-md-4 col-sm-8 col-xs-10 @if($errors->first('age_limit_lower')) error @endif">
                            {!! Form::text('age_limit_lower',null,['maxlength'=>'2','class'=>'form-control dm-agelimit']) !!}
                            {!! $errors->first('age_limit_lower', '<p> :message</p>')  !!}
                        </div>                        
                    </div>

                    <div class="form-group">    
                        {!! Form::label('age_limit_upper', 'Age Limit Upper', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                        <div class="col-lg-2 col-md-4 col-sm-8 col-xs-10 @if($errors->first('age_limit_upper')) error @endif">
                            {!! Form::text('age_limit_upper',null,['maxlength'=>'3','class'=>'form-control dm-money']) !!}
                            {!! $errors->first('age_limit_upper', '<p> :message</p>')  !!}
                        </div>                        
                    </div>

                    <div class="form-group">
                        {!! Form::label('map_to_icd9', 'Map to ICD 9', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                        <div class="col-lg-4 col-md-4 col-sm-8 col-xs-10">
                            {!! Form::text('map_to_icd9',null,['class'=>'form-control','maxlength'=>6]) !!}
                        </div>                        
                    </div>
					<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                    @if(strpos($currnet_page, 'edit') !== false)
                    <?php @$icd->effectivedate = ($icd->effectivedate!='0000-00-00' && @$icd->effectivedate != '')? date('m/d/Y',strtotime($icd->effectivedate)) : '';  ?>
                    <?php @$icd->inactivedate  = ($icd->inactivedate!='0000-00-00' && @$icd->inactivedate != '')? date('m/d/Y',strtotime($icd->inactivedate)) : '';  ?>
                    @endif 
                    <div class="form-group">
                        {!! Form::label('effective date', 'Effective Date', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-4 col-sm-8 col-xs-10 @if($errors->first('effectivedate')) error @endif">
                             
                             {!! Form::text('effectivedate',null,['id'=>'effectivedate','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control dm-date']) !!}
                            {!! $errors->first('effectivedate', '<p> :message</p>')  !!}
                        </div>                        
                    </div>

                    <div class="form-group">
                        {!! Form::label('inactive date', 'Inactive Date', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                        <div class="col-lg-4 col-md-4 col-sm-8 col-xs-10 @if($errors->first('inactivedate')) error @endif">
                            {!! Form::text('inactivedate',null,['id'=>'inactivedate','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control dm-date']) !!}
                            {!! $errors->first('inactivedate', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
                </div>
            </div>
        </div>

		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20" >
			<div class="box no-shadow">
				<div class="box-block-header margin-b-10">
					<i class="livicon" data-name="doc-landscape"></i> <h3 class="box-title">Description</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"	tabindex="-1"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->

				<div class="box-body  form-horizontal margin-l-10">                
					<div class="form-group">
						{!! Form::label('short_description', 'Short Description', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                            
						<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 @if($errors->first('short_description')) error @endif ">
							{!! Form::text('short_description',null,['class'=>'form-control']) !!}
							{!! $errors->first('short_description', '<p> :message</p>')  !!}                               
						</div>
					</div>

					<div class="form-group">
						{!! Form::label('long_description', 'Long Description', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                            
						<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 @if($errors->first('long_description')) error @endif ">
							{!!Form::textarea('long_description', null,['class'=>'form-control']) !!} 
							{!! $errors->first('long_description', '<p> :message</p>')  !!}
						</div>
						<div class="col-sm-1"></div>
					</div>                   
				</div>
			</div>
		</div>

       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
			{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
			<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
			@if(strpos($currnet_page, 'edit') !== false && $checkpermission->check_adminurl_permission('admin/icd/{icd_id}/delete') == 1)
				<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure would you like to delete?" href="{{ url('admin/icd/'.$icd->id.'/delete') }}">Delete</a></center>                                                        
			@endif

			@if(strpos($currnet_page, 'edit') == false)
				<a href="javascript:void(0)" data-url="{{ url('admin/icd/') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif

			@if(strpos($currnet_page, 'edit') !== false)
				<a href="javascript:void(0)" data-url="{{ url('admin/icd/'.$icd->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif
		</div>
     

@push('view.scripts')
<script type="text/javascript">

    $(document).ready(function () {
$(function () {
            $("#effectivedate").datepicker({
                changeMonth: true,
                changeYear: true,
                onClose: function (selectedDate) {
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="effectivedate"]'));
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="inactivedate"]'));
                }

            });

            $("#inactivedate").datepicker({
                changeMonth: true,
                changeYear: true,
                onClose: function (selectedDate) {
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="effectivedate"]'));
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="inactivedate"]'));
                }

            });
        });

        $('#js-bootstrap-validator')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    excluded: ':disabled',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        short_description: {
                            message: '',
                            validators: {
                            }
                        },
                        medium_description: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/icd.validation.medium_des") }}'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9\s\.\-\,]{0,60}$/,
                                    message: '{{ trans("admin/icd.validation.medium_limit") }}'
                                }
                            }
                        },
                        long_description: {
                            message: '',
                            validators: {
                            }
                        },
                        statement_description: {
                            message: '',
                            validators: {
                            }
                        },
                        age_limit_lower: {
                            message: '',
							trigger: 'keyup change',
                            validators: {
                                regexp: {
                                    regexp: /^[0-9]{0,2}$/,
                                    message: '{{ trans("common.validation.numeric") }}'
                                },
								callback: {
									message: '',
									callback: function (value, validator) {
										var age_limit_upper_val = validator.getFieldElements('age_limit_upper').val();
										var age_limit_lower_val = Number(value);
										if(age_limit_upper_val!=''){
											if(value==''){
												return {
													valid: false,
													message: 'Enter age limit lower'
												};
											}
											else if(age_limit_lower_val > age_limit_upper_val){
												return {
													valid: false,
													message: 'Age limit lower value must less or equal to age upper value'
												};
											}
											return true;
										}
										return true;
									}
								}
                            }
                        },
                        age_limit_upper: {
                            message: '',
							trigger: 'keyup change',
                            validators: {
                                regexp: {
                                    regexp: /^[0-9]{0,3}$/,
                                    message: '{{ trans("common.validation.numeric") }}'
                                },
								callback: {
									message: '',
									callback: function (value, validator) {
										var age_limit_lower_val = Number(validator.getFieldElements('age_limit_lower').val());
										var age_limit_upper_val = Number(value);
										if(age_limit_lower_val!=''){
											if(value==''){
												return {
													valid: false,
													message: 'Enter age limit Upper'
												};
											}
											else if(age_limit_upper_val < age_limit_lower_val){
												return {
													valid: false,
													message: 'Age limit upper value must greater than or equal to age lower value'
												};
											}
											return true;
										}
										return true;
									}
								}
                            }
                        },
                        order: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]{0,5}$/,
                                    message: '{{ trans("admin/icd.validation.order_regex") }}'
                                }
                            }
                        },
                        icdid: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]{0,15}$/,
                                    message: '{{ trans("admin/icd.validation.id_regex") }}'
                                }
							}
                        },
                        icd_code: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/icd.validation.code") }}'
                                },
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
						map_to_icd9: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9.]{0,6}$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                },
								callback: {
									message: '',
									callback: function (value, validator) {
										var count = value.split(".").length - 1;
										if(count>1) {
											return {
												valid: false,
												message: '{{ trans("practice/practicemaster/icd.validation.map_to_icd9") }}'
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
										var stop_date = $('#inactivedate').val();
										var response = startDate(value,stop_date);
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
						inactivedate: {
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
										var eff_date = $('#effectivedate').val();
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
						}
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
$('#age_limit_lower, #age_limit_upper').on('keyup keypress change', function(e) {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="age_limit_lower"]'));
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="age_limit_upper"]'));
	});
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
</script>
@endpush