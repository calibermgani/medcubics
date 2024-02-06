
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.help_page") }}' />

<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
<div class="col-md-12" >
    
    <div class="box box-info no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
        </div><!-- /.box-header -->
        <!-- form start -->
		<div class="box-body  form-horizontal">
			<div class="form-group @if($errors->first('type')) error @endif">
				{!! Form::label('Module Name', 'Module Name', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}  
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-10 @if($errors->first('facilities_id')) error @endif">
					{!! Form::select('type', [
						''         			=> '-- Select --',
						'customer' 			=> 'Customer',
						'insurance'			=> 'Insurance',
						'insurance_types'	=> 'Insurance Types',
						'modifiers' 		=> 'Modifiers',
						'codes'     		=> 'Codes',
						'cpt'				=> 'CPT',
						'icd'				=> 'ICD',
						'speciality'		=> 'Speciality',
						'taxanomy'			=> 'Taxonomy',
						'pos'				=> 'Pos',
						'id_qualifier'		=> 'Id qualifier',
						'provider_degree'	=> 'Provider degree',
						'role'				=> 'Role',
						'admin_user' 		=> 'Admin User',
						'user_activity' 	=> 'User Activity',

						'practice' 			=> 'Practice',
						'facility' 			=> 'Facility',
						'provider' 			=> 'Provider',
						'edi'				=> 'EDI',
						'employer' 			=> 'Employer',
						'codes' 			=> 'Codes',
						'templates' 		=> 'Templates',
						'fee_schedule' 		=> 'Fee Schedule',   
						'help'				=> 'Help',
						'scheduler'			=> 'Scheduler',
						'registration'		=> 'Registration',
						'superbills'		=> 'Superbills',
						'hold_option'		=> 'Hold Option',
						'charges'			=> 'Charges',
						'payments'			=> 'Payments',
						'claims'			=> 'Claims',
						'documents'			=> 'Documents',
						'reports'			=> 'Reports',
						'messages'			=> 'Messages',

						'patients'				=>  'Patients',
						'patient_registration'	=> 'Patient Registration',
						'patient_appointments'	=> 'patient_Appointments',
						'eligibility'			=> 'Eligibility',
						'e-superbills'			=> 'E-Superbills',
						'billing'				=> 'Billing',
						'referral'				=> 'Referral',
						'ledger'				=> 'Ledger',
						'problem_list'			=> 'Workbench',
						'task_list'				=> 'Task List',
						'correspondence'		=> 'Correspondence',
						'patient_reports'		=> 'Patient Reports',
						'correspondence' 		=> 'Correspondence',
						'superbills' 			=> 'Superbills',
						'user' 					=> 'User',
						'note' 					=> 'Note',
						'manageticket' 			=> 'ManageTicket',
						'disclaimer'			=> 'Disclaimer',	
						'privacy_policy'		=> 'Privacy Policy',
						'terms_and_conditions'	=> 'Terms And Conditions',
						
					   ],null, ['class' => 'form-control select2']) !!}
					{!! $errors->first('type', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1 col-xs-2"></div>
			</div>
			<div class="form-group">
			   {!! Form::label('Title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-10 @if($errors->first('title')) error @endif">
				   {!! Form::text('title',null,['class'=>'form-control']) !!}
					{!! $errors->first('title', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>   
			<div class="form-group">
				{!! Form::label('Description', 'Description', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}												 
				<div class="col-lg-12 col-sm-12 @if($errors->first('content')) error @endif">
					{!! Form::textarea('content',null,['id'=>'editor1','name'=>'content','class'=>'form-control']) !!}
					{!! $errors->first('content', '<p> :message</p>')  !!}
				</div>
				 
			</div>
			<div class="form-group">
				{!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4  control-label']) !!}                                       
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 @if($errors->first('provider_id')) error @endif">  
				   {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'h_active']) !!} {!! Form::label('h_active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                                   {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'h_inactive']) !!} {!! Form::label('h_inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
					{!! $errors->first('status', '<p> :message</p>')  !!}          
				</div>
				<div class="col-sm-3"> </div>
			</div>
		</div><!-- /.box-body -->
		<div class="box-footer">
		   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		   {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
			<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
			@if(strpos($currnet_page, 'edit') !== false)
				@if(strpos($staticpages->type, 'static_page') === false)
				<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure you want to delete?"
				href="{{ url('admin/staticpage/delete/'.$staticpages->id) }}">Delete</a>
				@endif
				<a href="javascript:void(0)" data-url="{{ url('admin/staticpage/'.$staticpages->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@else
			<a href="javascript:void(0)" data-url="{{ url('admin/staticpage')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			@endif     
		   </div>
	   </div><!-- /.box-footer -->
    </div><!-- /.box -->
</div><!--/.col (left) -->
</div><!--Background color for Inner Content Starts -->
</div>


@push('view.scripts')
	{!! HTML::script('js/ckeditor/ckeditor.js') !!}
    <script type="text/javascript">
		CKEDITOR.replace('editor1', {skin: 'kama'});
		CKEDITOR.config.filebrowserBrowseUrl = '/browse.php';
		CKEDITOR.config.filebrowserUploadUrl = '/upload.php';
			
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
                                    message: '{{ trans("admin/staticpage.validation.modulename") }}'
                                }
                            }
                        },
                        title: {
                            message: ' ',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("common.validation.title") }}'
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
                                    message: '{{ trans("admin/staticpage.validation.status") }}'
                                }
                            }
                        }
                    }
                });
        });
    </script>
@endpush