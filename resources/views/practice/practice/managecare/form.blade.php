<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.practice_managecare") }}' />
   <?php 
        if(!isset($get_default_timezone)){
           $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
        }                  
   ?>
<div class="col-xxxl-6 col-xxl-6 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!-- Col Starts -->
    <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
    {!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
    <div class="box no-shadow"><!-- General Information Box Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title"> General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body  form-horizontal margin-l-10 p-b-20"><!-- Box Body Starts -->
            <div class="form-group">
                {!! Form::label('insurance', 'Insurance', ['class'=>'col-xl-2 col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10 @if($errors->first('insurance_id')) error @endif">
                    {!! Form::select('insurance_id', array(''=>'-- Select --')+(array)$insurances,  $insurance_id,['class'=>'form-control input-view-border1 select2','id'=>'billinginsurance']) !!}
                    {!! $errors->first('insurance_id', '<p> :message</p>')  !!}
                </div>                        
            </div>
            <div class="form-group">
                {!! Form::label('provider', 'Provider', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10 @if($errors->first('providers_id')) error @endif">
                    {!! Form::select('providers_id', array(''=>'-- Select --')+(array)$providers,  $provider_id,['class'=>'form-control input-view-border1 select2 js-sel-provider-change','id'=>'billingprovider']) !!}
                    <p class="js-sel-provider-type-dis hide no-bottom font12"></p>
                    {!! $errors->first('providers_id', '<p> :message</p>')  !!}
                </div>                       
            </div>

            <div class="form-group">
                {!! Form::label('credential', 'Credential', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
                    {!! Form::radio('enrollment', 'Par',true,['class'=>'','id'=>'c-par']) !!} {!! Form::label('c-par', 'Par',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('enrollment', 'Non-Par',null,['class'=>'','id'=>'c-nonpar']) !!} {!! Form::label('c-nonpar', 'Non-Par',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('EntityType', 'Entity Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
                    {!! Form::radio('entitytype', 'Group',null,['class'=>'','id'=>'c-group']) !!} {!! Form::label('c-group', 'Group',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp;&nbsp; &nbsp;
                    {!! Form::radio('entitytype', 'Individual',true,['class'=>'','id'=>'c-individual']) !!} {!! Form::label('c-individual', 'Individual',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('provider_id', 'Provider ID', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10 @if($errors->first('providers_id')) error @endif">
                    {!! Form::text('provider_id',null,['class'=>'form-control dm-medicaid input-view-border1']) !!}
                    {!! $errors->first('provider_id', '<p> :message</p>')  !!}
                </div>                        
            </div>
            @if(strpos($currnet_page, 'edit') !== false)
            <?php 
				$managecare->effectivedate = ($managecare->effectivedate=='0000-00-00')? '' : date("m/d/Y",strtotime($managecare->effectivedate));
				$managecare->terminationdate =  ($managecare->terminationdate == '0000-00-00')? '':date("m/d/Y",strtotime($managecare->terminationdate)) 
			?>
            @endif
            <div class="form-group">
                {!! Form::label('effectivedate', 'Effective Date', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-4 col-sm-5 col-xs-10 @if($errors->first('effectivedate')) error @endif">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i> {!! Form::text('effectivedate',null,['class'=>'form-control input-view-border1 dm-date form-cursor','id'=>'effectivedate', 'autocomplete'=>'off','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                    {!! $errors->first('effectivedate', '<p> :message</p>')  !!}
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('terminationdate', 'Termination Date', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-4 col-sm-5 col-xs-10 @if($errors->first('terminationdate')) error @endif">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i>{!! Form::text('terminationdate',null,['class'=>'form-control input-view-border1 dm-date form-cursor','id'=>'terminatedate', 'autocomplete'=>'off','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                    {!! $errors->first('terminationdate', '<p> :message</p>')  !!}
                </div>                       
            </div>

            <div class="form-group">
                {!! Form::label('feeschedule', 'Fee Schedule', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10 @if($errors->first('feeschedule')) error @endif">
                    {!! Form::textarea('feeschedule',null,['class'=>'form-control input-view-border1']) !!}
                </div>                        
            </div>
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
				{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}

				@if(strpos($currnet_page, 'edit') !== false)
					@if($checkpermission->check_url_permission('managecare/delete/{managecare}') == 1) 
						<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure to delete the entry?"
					   href="{{ url('managecare/delete/'.$managecare->id) }}">Delete</a>
					@endif
				@endif
				<a href="javascript:void(0)" data-url="{{ url('managecare')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			</div>
        </div><!-- /.box-body ends -->
    </div><!-- General Information Box Ends -->
</div><!--/.col ends -->

@push('view.scripts')
<script type="text/javascript">
    $('#effectivedate').attr('autocomplete','off');
    $('#terminatedate').attr('autocomplete','off');
    $(document).ready(function () {
        $(function () {
            <?php if(isset($get_default_timezone)){ ?>
                var get_default_timezone = '<?php echo $get_default_timezone; ?>';
            <?php }else{?>
                var get_default_timezone = '';
            <?php }?>
            var eventDates = {};
            eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
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
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="terminationdate"]'));
                }
            });

            $("#terminatedate").datepicker({
               changeMonth: true,
               changeYear: true,
               beforeShowDay: function(d) {
                    setTimeout(function() {
                    $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
                     }, 10);

                    var highlight = eventDates[d];
                        if( highlight ) {
                             return [true, "ui-state-highlight", 'Tooltip text'];
                        } else {
                           
                             return [true, '', ''];
                        }
                },
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
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					insurance_id: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("common.validation.insurance_required") }}'
							}
						}
					},
					enrollment: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/managecare.validation.enrollment") }}'
							}
						}
					},
					entitytype: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/managecare.validation.entitytype") }}'
							}
						}
					},
					/*providers_id: {
					 message: '',
					 validators: {
					 notEmpty: {
					 message: '{{ trans("common.validation.provider_required") }}'
					 
					 }
					 }
					 },*/
					provider_id: {
						message: '',
						validators: {
							regexp: {
								regexp: /^[a-zA-Z0-9]+$/,
								message: '{{ trans("common.validation.alphanumeric") }}'
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
							}/*,
							callback: {
								message: '{{ trans("common.validation.effectivedate") }}',
								callback: function (value, validator) {
									var stop_date = validator.getFieldElements('terminationdate').val();
									var response = startDate(value, stop_date);
									if (response != true) {
										return {
											valid: false,
											message: response
										};
									}
									return true;
								}
							}*/
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
									var response = endDate(eff_date, ter_date);
									if (response != true) {
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
					feeschedule: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var msg = lengthValidation(value, 'feeschedule');
									if (msg != true) {
										return {
											valid: false,
											message: msg
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

    function startDate(start_date, end_date) {
        var date_format = new Date(end_date);
        if (end_date != '' && date_format != "Invalid Date") {
            return (start_date == '') ? '{{ trans("common.validation.eff_date_required") }}' : true;
        }
        return true;
    }
	
    function endDate(start_date, end_date) {
        var eff_format = new Date(start_date);
        var ter_format = new Date(end_date);
        if (ter_format != "Invalid Date" && end_date != '' && eff_format != "Invalid Date" && end_date.length > 7 && checkvalid(end_date) != false) {
            var getdate = daydiff(parseDate(start_date), parseDate(end_date));
            return (getdate > 0) ? true : '{{ trans("common.validation.terminationdate") }}';
        }
        return true;
    }
	
    function daydiff(first, second) {
        return Math.round((second - first) / (1000 * 60 * 60 * 24));
    }
	
    function parseDate(str) {
        var mdy = str.split('/')
        return new Date(mdy[2], mdy[0] - 1, mdy[1]);
    }
	
    function checkvalid(str) {
        var mdy = str.split('/');
        if (mdy[0] > 12 || mdy[1] > 31 || mdy[2].length < 4 || mdy[0] == '00' || mdy[0] == '0' || mdy[1] == '00' || mdy[1] == '0' || mdy[2] == '0000') {
            return false;
        }
    }

</script>
@endpush