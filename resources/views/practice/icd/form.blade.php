<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.icd") }}' />
<?php 
    if(!isset($get_default_timezone)){
       $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
    }      
?>
<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 no-padding">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10"><!-- Code Info Col Starts -->
        <div class="box no-shadow margin-b-10"><!-- Box Starts -->
            <div class="box-block-header with-border">
                <i class="livicon" data-name="code"></i> <h3 class="box-title">Code Details</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" tabindex="-1"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body  form-horizontal margin-l-10 p-b-40"><!-- Box Body Starts -->
                
                <div class="form-group bottom-space-10">    
                    {!! Form::label('sex', 'Gender', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        {!! Form::radio('sex', 'Male', null,['class'=>'','id'=>'c-male']) !!} {!! Form::label('c-male', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp; 
                        {!! Form::radio('sex', 'Female',null,['class'=>'','id'=>'c-female']) !!} {!! Form::label('c-female', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp;
                        {!! Form::radio('sex', 'Others',null,['class'=>'','id'=>'c-others']) !!} {!! Form::label('c-others', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">    
                    {!! Form::label('age_limit_lower', 'Age Limit Lower', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-2 col-md-3 col-sm-8 col-xs-4 @if($errors->first('age_limit_lower')) error @endif">
                        {!! Form::text('age_limit_lower',null,['class'=>'form-control dm-agelimit','autocomplete'=>'nope']) !!}
                        {!! $errors->first('age_limit_lower', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">    
                    {!! Form::label('age_limit_upper', 'Age Limit Upper', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-2 col-md-3 col-sm-8 col-xs-4 @if($errors->first('age_limit_upper')) error @endif">
                        {!! Form::text('age_limit_upper',null,['class'=>'form-control dm-money', 'autocomplete'=>'nope']) !!}
                        {!! $errors->first('age_limit_upper', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">    
                    {!! Form::label('map_to_icd9', 'Map to ICD 9', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-8 col-xs-4">
                        {!! Form::text('map_to_icd9',null,['class'=>'form-control','maxlength'=>6]) !!} 
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                @if(strpos($currnet_page, 'edit') !== false)
                <?php 
					@$icd->effectivedate = ($icd->effectivedate!='0000-00-00' && @$icd->effectivedate != '')? date('m/d/Y',strtotime($icd->effectivedate)) : '';
					@$icd->inactivedate  = ($icd->inactivedate!='0000-00-00' && @$icd->inactivedate != '')? date('m/d/Y',strtotime($icd->inactivedate)) : '';  
				?>
                @endif  
                <div class="form-group">    
                    {!! Form::label('effectivedate_label', 'Effective Date', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-8 col-xs-4 @if(@$errors->first('effectivedate')) error @endif">
                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i>
                        {!! Form::text('effectivedate',null,['id'=>'effectivedate','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control dm-date']) !!}
                        {!! $errors->first('effectivedate', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">    
                    {!! Form::label('inactivedate_label', 'Inactive Date', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-8 col-xs-4 @if($errors->first('inactivedate')) error @endif">
                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i>
                        {!! Form::text('inactivedate',null,['id'=>'inactivedate','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control dm-date']) !!}
                        {!! $errors->first('inactivedate', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

              
            </div><!-- Box Body Ends -->
        </div><!-- Box Starts -->
    </div><!-- Code Info Col Ends -->


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Description Col Starts -->
        <div class="box no-shadow margin-b-10"><!-- Box Starts -->
            <div class="box-block-header with-border">
                <i class="livicon" data-name="doc-landscape"></i> <h3 class="box-title">Description</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" tabindex="-1"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->

            <div class="box-body  form-horizontal margin-l-10">                
                <div class="form-group">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 med-green font600">
                        Short Description
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        
                       
                    </div>                        
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 @if($errors->first('short_description')) error @endif ">
                        {!! Form::text('short_description',null,['class'=>'form-control', 'id'=>'short_description']) !!}  
                        {!! $errors->first('short_description', '<p> :message</p>')  !!}
                    </div>
                </div>

                <div class="form-group space20">                
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 med-green font600">
                        Long Description
                    </div>	
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                     
                        
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 @if($errors->first('long_description')) error @endif ">
                        {!!Form::textarea('long_description', null,['class'=>'form-control']) !!} 
                        {!! $errors->first('long_description', '<p> :message</p>')  !!}
                    </div>
                </div>

                <div class="form-group space20">                
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 med-green font600">
                        Statement Description
                    </div>	
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                       
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 @if($errors->first('statement_description')) error @endif ">
                        {!!Form::textarea('statement_description', null,['class'=>'form-control']) !!} 
                        {!! $errors->first('statement_description', '<p> :message</p>')  !!}
                    </div>
                </div>
            </div><!-- Box Body Ends -->                        
        </div><!-- Box Ends -->
    </div><!-- Description Col Ends -->
</div>
    
    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 text-center">
        {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
        @if(strpos($currnet_page, 'edit') !== false)
        <a href="javascript:void(0)" data-url="{{ url('icd/'.$icd->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
        @else
        <a href="javascript:void(0)" data-url="{{ url('icd') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
        @endif
    </div>
  
@push('view.scripts')
<script type="text/javascript">

    $(document).ready(function () {
        $('#age_limit_lower').attr('autocomplete','off');
        $('#age_limit_upper').attr('autocomplete','off');
        $('#effectivedate').attr('autocomplete','off');
        $('#short_description').attr('autocomplete','off');
        $(function () {
            var eventDates = {};
            eventDates[ new Date( '<?php echo $get_default_timezone; ?>' )] = new Date( '<?php echo $get_default_timezone; ?>' );
            $("#effectivedate").datepicker({
                changeMonth: true,
                changeYear: true,
                beforeShowDay: function(d) {
                setTimeout(function() {
                $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
                 }, 10);
                var highlight = eventDates[d];
                    if( highlight ) {
                         return [true, "ui-state-highlight", ''];
                    } else {
                       
                         return [true, '', ''];
                    }
                },
                onClose: function (selectedDate) {
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="effectivedate"]'));
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="inactivedate"]'));
                }

            });

            $("#inactivedate").datepicker({
                changeMonth: true,
                changeYear: true,
                beforeShowDay: function(d) {
                setTimeout(function() {
                $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
                 }, 10);
                var highlight = eventDates[d];
                    if( highlight ) {
                         return [true, "ui-state-highlight", ''];
                    } else {
                       
                         return [true, '', ''];
                    }
                },
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
                        valid: '',
                        invalid: '',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
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
                        /*icdid: {
                            message: 'ID field is invalid',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]{0,15}$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                }

                            }
                        },*/
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
										var stop_date = validator.getFieldElements('inactivedate').val();
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
						}
					}
                });
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
	
	function checkvalid(str) {
		var mdy = str.split('/');
		if(mdy[0]>12 || mdy[1]>31 || mdy[2].length<4 || mdy[0]=='00' || mdy[0]=='0' || mdy[1]=='00' || mdy[1]=='0' || mdy[2]=='0000') {
			return false;
		}
	}
	
	$('#age_limit_lower, #age_limit_upper').on('keyup keypress change', function(e) {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="age_limit_lower"]'));
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="age_limit_upper"]'));
	});

</script>
@endpush