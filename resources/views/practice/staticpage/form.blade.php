<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.help_page") }}' />
<div class="col-md-12" >
   
    <div class="box box-info no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
        </div><!-- /.box-header -->
        <!-- form start -->       
            <div class="box-body  form-horizontal margin-l-10">
                <div class="form-group @if($errors->first('type')) error @endif">
                    {!! Form::label('Module Name', 'Module Name', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label star']) !!}  
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-10 @if($errors->first('facilities_id')) error @endif">
                        {!! Form::select('type', [
						   ''	         		=> '-- Select --',
						   'practice' 			=> 'Practice',
						   'facility' 			=> 'Facility',
						   'provider' 			=> 'Provider',
						   'insurance'			=> 'Insurance',
						   'modifiers' 			=> 'Modifiers',
						   'codes'     			=> 'Codes',
						   'cpt'				=> 'CPT',
						   'icd'				=> 'ICD',
						   'edi'				=> 'EDI',
						   'patientstatementsettings'=> 'Patient Statement',
						   'employer' 			=> 'Employer',
						   'templates' 			=> 'Templates',
						   'fee_schedule' 		=> 'Fee Schedule',   
						   'scheduler'			=> 'Scheduler',
						   'registration'		=> 'Registration',
						   'superbills'			=> 'Superbills',
						   'hold_option'		=> 'Hold Option',
						   'insurance_types'	=> 'Insurance Types',
						   'email_template'		=> 'Email Templates',
						   'adjustmentreason'	=> 'Adjustment Reason',
						   'reason_for_visit'	=> 'Reason For Visit',
						   'clinicalcategories'	=> 'Clinical Categories',
						   'app_templates'		=> 'App Templates',
						   'charges'			=> 'Charges',
						   'payments'			=> 'Payments',
						   'claims'				=> 'Claims',
						   'documents'			=> 'Documents',
						   'reports'			=> 'Reports',
						   'messages'			=> 'Messages',
						   'user_activity'		=> 'User Activity',
						   'userapisettings'	=> 'User API Settings',
						   'apisettings'	=> 'API Settings',
						   'userhistory'			=> 'User History',
						   'history'			=> 'History',
						   
						   'patients'				=>  'Patients',
						   'patient_registration'	=> 'Patient Registration',
						   'patient_appointments'	=> 'patient_Appointments',
						   'profile'	=> 'profile',
						   'eligibility'			=> 'Eligibility',
						   'questionnaire_template'	=> 'Questionnaire Template',
						   'questionnaire'			=> 'Questionnaire',
						   'billing'				=> 'Billing',
						   'referral'				=> 'Referral',
						   'ledger'					=> 'Ledger',
						   'ar_management'			=> 'AR Management',
						   'problem_list'			=> 'Workbench',
						   'task_list'				=> 'Task List',
						   'correspondence'			=> 'Correspondence',
						   'patient_reports'		=> 'Patient Reports',
						   'correspondence'			=> 'Correspondence',
							'superbills' 			=> 'Superbills',
							'user' 			 		=> 'User',
							'note' 					=> 'Note',
							'wallet_history'		=> 'Wallet History',
							'return_check'			=> 'Return Check',
							'appointments'			=> 'Appointment',
							'budgetplan'			=> 'Budget Plan',
							'clinical_notes'		=> 'Clinical Notes',
							'medical_history'		=> 'Medical History',
							
							'claim_report' 			=> 'outstanding Claim Report',
							'charge_report' 		=> 'Charge Report',
							'payment_report' 		=> 'Payment Report',
							'adjustment_report' 	=> 'Adjustment Report',
							'refund_report' 		=> 'Refund Report',
							'appointment_report' 	=> 'Appointment Report',
							'year_end_financial' 	=> 'Year End Financial Report',
							
							
							'submitted' 			=> 'Submitted',
							'hold' 					=> 'hold',
							'rejections' 			=> 'Rejections',
							'ready_to_submit'		=> 'Ready to Submit',
							
						   ],null, ['class' => 'form-control select2']) !!}
						{!! $errors->first('type', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>
              

                <div class="form-group margin-b-20">
                   {!! Form::label('Title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label star']) !!} 
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-10 @if($errors->first('title')) error @endif">
                       {!! Form::text('title',null,['class'=>'form-control','maxlength'=>'51','autocomplete'=>'off']) !!}
						{!! $errors->first('title', '<p> :message</p>')  !!}
                    </div>
                     <div class="col-sm-1"></div>
                </div>              
               <!----->
                <div class="form-group">                                                                     
                    <div class="col-lg-12 col-sm-12 @if($errors->first('content')) error @endif">
                        {!! Form::textarea('content',null,['id'=>'editor1','name'=>'content','class'=>'form-control']) !!}
                        {!! $errors->first('content', '<p> :message</p>')  !!}
                    </div>                     
                </div>
                <!----->
                <div class="form-group">
                    {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4  control-label']) !!}                                       
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 @if($errors->first('provider_id')) error @endif">  
                       {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                       {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
						{!! $errors->first('status', '<p> :message</p>')  !!}          
                    </div>
                    <div class="col-sm-3"> </div>
                </div>
                <!----->
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
               {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics']) !!}
                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                @if(strpos($currnet_page, 'edit') !== false)
                    <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure to delete the entry?"
					href="{{ url('staticpage/delete/'.$staticpages->id) }}">Delete</a>
                <a href="javascript:void(0)" data-url="{{ url('staticpage/'.$staticpages->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
				@else
				<a href="javascript:void(0)" data-url="{{ url('staticpage')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @endif     
               </div>                
            </div><!-- /.box-body -->        
    </div><!-- /.box -->    
</div><!--/.col (left) -->

@push('view.scripts')
    <script type="text/javascript">
        $(document).ready(function () {
			CKEDITOR.instances.editor1.on('change', function () {
				CKEDITOR.instances['editor1'].updateElement();
				$('#js-bootstrap-validator').bootstrapValidator();
				$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'content');
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
                        type: {
                            message: ' ',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/staticpage.validation.name") }}'
                                }
                            }
                        },
                        title: {
                            message: ' ',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/staticpage.validation.title") }}'
                                },
								callback: {
									message: '',
									callback: function (value, validator) {
										if(value.length > 50){
											return {
												valid: false,
												message: '{{ trans("common.validation.city_limit") }}'
											};
										}
										return true;	
									}
								}
                            }
                        },
						content: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("common.validation.content") }}'
                                },
								callback: {
									message: '',
									callback: function (value, validator) {
										var value = CKEDITOR.instances['editor1'].getData();
										value = $($.parseHTML(value)).text();
										if(value.length > 0){
											var get_val = value.trim();
											if(get_val.length == 0){
												return {
													valid: false,
													message: '{{ trans("common.validation.not_only_space") }}'
												};
											}
										}
										return true;
									}
								}
                            }
                        },
                        status: {
                            message: ' ',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/staticpage.validation.status") }}'
                                }
                            }
                        }
                    }
                });               
               
        });
    </script>
@endpush