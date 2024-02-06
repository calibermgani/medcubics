<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.budgetplan") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >

    <div class="box box-info no-shadow">        
        <!-- form start -->
        <?php $patient_data = !is_null($patient_id) ? App\Models\Patients\Patient::getPatienttabData($patient_id) : "0.00";       
         $patient_balance = $patient_data['patient_due'];
        ?>
        {!! Form::open(['url'=>'patients/'.$patient_id.'/budgetplan','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
        <div class="box-body  form-horizontal">
            <div class="form-group">
                {!! Form::label('Patient Balance', 'Patient Balance', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-6 control-label']) !!} 
                <div class="col-lg-4 col-md-7 col-sm-7 col-xs-6 med-orange font600">
                    {!! Form::hidden('patient_balance',$patient_balance,['class'=>'form-control']) !!} {!! App\Http\Helpers\Helpers::priceFormat($patient_balance) !!}
                </div>
            </div>  
			<div class="form-group">
                {!! Form::label('budget_total', 'Budget Total', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-6 control-label']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10 med-orange font600">
                   {!! Form::text('budget_total',null,['class'=>'form-control js_amount_separation']) !!}
                </div>
            </div>  
            <div class="form-group">
                {!! Form::label('Budget Plan', 'Budget Plan', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10 @if($errors->first('plan')) error @endif">
                    {!! Form::select('plan',[''=>'-- Select --','Weekly' => 'Weekly','Biweekly' => 'Bi-Weekly','Monthly' => 'Monthly','Bimonthly'=>'Bi-Monthly'],null,['class'=>'select2 form-control']) !!}
                    {!! $errors->first('plan', '<p> :message</p>')  !!}
                </div>
            </div>  
            <div class="form-group">
                {!! Form::label('Budget Amount', 'Budget Amount', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10 @if($errors->first('budget_amt')) error @endif">
                    {!! Form::text('budget_amt',null,['class'=>'form-control js_amount_separation']) !!}
                    {!! $errors->first('budget_amt', '<p> :message</p>')  !!}
                </div>

            </div>
            <div class="form-group">
                {!! Form::label('Statement Start Date', 'Statement Start Date', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10 @if($errors->first('statement_start_date')) error @endif">
                    <i class="fa fa-calendar-o form-icon"></i>
                    {!! Form::text('statement_start_date',null,['id'=>'statementstartdate','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor dm-date']) !!}
                    {!! $errors->first('statement_start_date', '<p> :message</p>')  !!}
                </div>
            </div>
			<div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-10 @if($errors->first('plan')) error @endif">
                    {!! Form::select('status',['Active' => 'Active','Inactive' => 'Inactive'],null,['class'=>'select2 form-control']) !!}
                    {!! $errors->first('plan', '<p> :message</p>')  !!}
                </div>
            </div> 

            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            {!! Form::button("Save", ['class'=>'btn btn-medcubics']) !!}
            <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>

           <!--  @if(strpos($currnet_page, 'edit') !== false) 
            <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure you want to delete?" href="{{ url('patients/'.$patient_id.'/budgetplan/'.$patientbudget_id.'/delete') }}">Delete</a>
            @endif -->
            <a href="javascript:void(0)">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
        </div>
        </div><!-- /.box-body -->
        {!! Form::close() !!}

        

    </div><!-- /.box -->


</div><!--/.col (left) -->

@push('view.scripts')
<script type="text/javascript">
$(document).on('change','input[type="budget_total"]',function(){
	$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="budget_total"]'));
})
    $(document).ready(function () {

        $("#statementstartdate").datepicker({
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true,
            minDate: 0,
            onClose: function (selectedDate) {
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="statement_start_date"]'));
            }
        });

        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                plan: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/patients/patient_budget.validation.budgetplan") }}'
                        }
                    }
                },
                budget_amt: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/patients/patient_budget.validation.budgetamount") }}'
                        },
                        regexp: {
                            regexp: /^[0-9.,]+$/,
                            message: '{{ trans("common.validation.numeric") }}'
                        },
                        callback: {
                            message: '{{ trans("practice/patients/patient_budget.validation.budgetamount_regex") }}',
                            callback: function (value, validator, $field) {
                                var patient_balance = $('#js-bootstrap-validator').find('[name="patient_balance"]').val();
                                var budget_amt = $('#js-bootstrap-validator').find('[name="budget_amt"]').val();
                                var count = value.split(".").length - 1;
                                var re = new RegExp(/^[0.]+$/);
                                if (re.test(budget_amt)) {
                                     return {
										valid: false, 
										message: '{{ trans("practice/patients/patient_budget.validation.validamount") }}'
									};
                                }
                                else if (budget_amt > patient_balance) {
                                    return {
										valid: false, 
										message: '{{ trans("practice/patients/patient_budget.validation.budgetamount_regex") }}'
									};
                                }
                                else if (count > 1) {
                                    return {
										valid: false, 
										message: '{{ trans("practice/patients/patient_budget.validation.validamount") }}'
									};
                                }
                                else {
                                    return true;
                                }
                            }
                        }
                    }
                },
                statement_start_date: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/patients/patient_budget.validation.statementstartdate") }}'
                        },
                        callback: {
                            message: '{{ trans("common.validation.date_format") }}',
                            callback: function (value, validator, $field) {
								if(value != ''){
									var userdate = new Date(value);
									
									if (userdate != 'Invalid Date' && value.length == 10)
									{
										var now = new Date();
										var month = now.getMonth() + 1;
										var day = now.getDate();

										var currentdate = (month < 10 ? '0' : '') + month + '/' +
											(day < 10 ? '0' : '') + day + '/' + now.getFullYear();
										
										var getdate = daydiff(parseDate(currentdate), parseDate(value));
										
										if (getdate >= 0)
										{
											return true;
										}
										else
										{
											return {
												valid: false, 
												message: '{{ trans("common.validation.date_future") }}'
											};
										}
									}
									else {
										return {
												valid: false, 
												message: '{{ trans("common.validation.date_format") }}'
											};
									}
								} else {
									return true;
								}									
                            }
                        }
                    }
                },
				budget_total: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/patients/patient_budget.validation.budgetamount") }}'
                        },
                        regexp: {
                            regexp: /^[0-9.,]+$/,
                            message: '{{ trans("common.validation.numeric") }}'
                        },
                        callback: {
                            message: '{{ trans("practice/patients/patient_budget.validation.budgetamount_regex") }}',
                            callback: function (value, validator, $field) {
                                var patient_balance = $('#js-bootstrap-validator').find('[name="patient_balance"]').val();
                                var budget_amt = $('#js-bootstrap-validator').find('[name="budget_total"]').val();
                                var count = value.split(".").length - 1;
                                var re = new RegExp(/^[0.]+$/);
                                if (re.test(budget_amt)) {
                                     return {
										valid: false, 
										message: '{{ trans("practice/patients/patient_budget.validation.validamount") }}'
									};
                                }
                                else if (budget_amt > patient_balance) {
                                    return {
										valid: false, 
										message: '{{ trans("practice/patients/patient_budget.validation.budgetamount_regex") }}'
									};
                                }
                                else if (count > 1) {
                                    return {
										valid: false, 
										message: '{{ trans("practice/patients/patient_budget.validation.validamount") }}'
									};
                                }
                                else {
                                    return true;
                                }
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
        var mdy = str.split('/');
        return new Date(mdy[2], mdy[0] - 1, mdy[1]);
    }

</script>
@endpush