<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.taxanomy") }}' />

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
            <div class="box box-info no-shadow">
                    <div class="box-block-header margin-b-10">
                        <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                    </div><!-- /.box-header -->
                <!-- form start -->
                    <div class="box-body form-horizontal margin-l-10">
                       <div class="form-group">
                           {!! Form::label('specialty', 'Specialty', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10 @if($errors->first('speciality')) error @endif">
                               {!! Form::select('speciality_id', array(''=>'-- Select --')+(array)$specialities,  $speciality_id,['class'=>'select2 form-control','id'=>'speciality_id']) !!}
                               {!! $errors->first('speciality_id', '<p> :message</p>')  !!}
                            </div>
                        </div>

                        <div class="form-group">
                           {!! Form::label('code', 'Code', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10 @if($errors->first('code')) error @endif">
                               {!! Form::text('code',null,['maxlength'=>'10','class'=>'form-control']) !!}
                                {!! $errors->first('code', '<p> :message</p>')  !!}
                            </div>
                        </div>
						
                        <div class="form-group">
                            {!! Form::label('description', 'Description', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10 @if($errors->first('resource_name')) error @endif">
                                {!! Form::textarea('description',null,['class'=>'form-control']) !!}
                                {!! $errors->first('description', '<p> :message</p>')  !!}
                            </div>
                        </div>
                    </div>
                
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-t-10">
                        {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
                        @php $currnet_page = Route::getFacadeRoot()->current()->uri(); @endphp
                        @if(strpos($currnet_page, 'edit') !== false)
							@if($checkpermission->check_adminurl_permission('admin/taxanomy/delete/{id}') == 1)
								<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?"
								href="{{ url('admin/taxanomy/delete/'.$taxanomy->id) }}">Delete</a>
							@endif

                            <a href="javascript:void(0)" data-url="{{ url('admin/taxanomy/'.$taxanomy->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                                @else
                            <a href="javascript:void(0)" data-url="{{ url('admin/taxanomy')}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                       @endif
                  </div>
            </div><!-- /.box -->
        </div>

@push('view.scripts')
    <script type="text/javascript">
        $(document).ready(function() {
			$('[name="speciality_id"]').on('change',function() {
				$('#js-bootstrap-validator').data('bootstrapValidator').updateStatus('speciality_id', 'NOT_VALIDATED').validateField('speciality_id');
			});
            $('#js-bootstrap-validator')
            .bootstrapValidator({
				message: '',
				excluded: ':disabled',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					description:{
						message:'',
						validators:{
							notEmpty:{
								message: '{{ trans("common.validation.description") }}'
							}
						}
					},
					speciality_id:{
						message:'',
						validators:{
							notEmpty:{
								message: '{{ trans("admin/taxanomy.validation.speciality") }}'
							}
						}
					},
					code:{
						message:'',
						validators:{
							callback: {
								message: '',
								callback: function (value, validator) {
									var regex = new RegExp(/^[A-Za-z0-9]+$/);
									if(value == ""){
										return {
											valid: false,
											message: '{{ trans("admin/pos.validation.code") }}'
										};
									}
									if(!regex.test(value)) {
										return {
											valid: false,
											message: '{{ trans("common.validation.alphanumeric") }}'
										};
									}
									
									if(value.length < taxonomy_code_max_defined_length){
										return {
											valid: false,
											message: '{{ trans("admin/taxanomy.validation.code_regex") }}'
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
    </script>
@endpush
