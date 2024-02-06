<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.modifier1") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" ><!-- Col Starts -->
    <div class="box box-info no-shadow"><!-- Box General Information Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Modifier Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->

        <div class="box-body  form-horizontal margin-l-10"><!-- Box Body Starts -->
            <div class="form-group">                        

                {!! Form::label('modifierstype', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('modifiers_type_id')) error @endif">  
                    {!! Form::select('modifiers_type_id', array('' => '-- Select --') + (array)$modifierstype,  $modifiers_type_id,['class'=>'form-control select2']) !!}

                    {!! $errors->first('modifiers_type_id', '<p> :message</p>')  !!}
                </div> 
                <div class="col-sm-1"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('code', 'Modifier', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('code')) error @endif">
                    {!! Form::text('code',null,['maxlength'=>'2','class'=>'form-control','name'=>'code']) !!}
                    {!! $errors->first('code', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">
                {!! Form::label('name', 'Name', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-10 @if($errors->first('name')) error @endif">
                    {!! Form::text('name',null,['class'=>'form-control js-letters-caps-format','name'=>'name','maxlength'=>'255']) !!}
                    {!! $errors->first('name', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">
                {!! Form::label('description', 'Description', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-10 @if($errors->first('description')) error @endif">
                    {!! Form::textarea('description',null,['class'=>'form-control','name'=>'description']) !!}
                    {!! $errors->first('description', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">
                {!! Form::label('anesthesia_base_unit', 'Anesthesia Base Unit', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!} 
                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('anesthesia_base_unit')) error @endif">
                    {!! Form::text('anesthesia_base_unit',null,['class'=>'form-control','name'=>'anesthesia_base_unit', 'maxlength'=> 6]) !!}
                    {!! $errors->first('anesthesia_base_unit', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-6 control-label']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 @if($errors->first('status')) error @endif">
                    {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
                    {!! $errors->first('status', '<p> :message</p>')  !!}
                </div>                       
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics']) !!}
                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                @if(strpos($currnet_page, 'edit') !== false)
                @if($checkpermission->check_url_permission('modifierlevel1/delete/{id}') == 1 )
                <a class="btn btn-medcubics js-delete-confirm hide"data-text="Are you sure to delete the entry?" href="{{ url('modifierlevel1/delete/'.$modifiers->id) }}">Delete</a>
                @endif
                <a href="javascript:void(0)" data-url="{{url('modifierlevel1/'.$modifiers->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @else

                <a href="javascript:void(0)" data-url="{{url('modifierlevel1')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a> 
                @endif
            </div>
        </div> 
    </div><!-- Box General Information Ends -->
</div><!--/.col ends -->

@push('view.scripts')  
<script type="text/javascript">
    $('#code').attr('autocomplete','off');
    $('#name').attr('autocomplete','off');
    $('#anesthesia_base_unit').attr('autocomplete','off');
    $(document).ready(function () {
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
					code: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/modifier.validation.code") }}'
							},
							callback: {
								callback: function (value, validator) {
									var re = /^[a-zA-Z0-9]+$/i;
									if (value != '' && value == 0 || value.length == 1)
										return {
											valid: false,
											message: '{{ trans("practice/practicemaster/modifier.validation.code_regex") }}'
										};
									else if (value != '' && !re.test(value)) {
										return {
											valid: false,
											message: '{{ trans("common.validation.alphanumeric") }}'
										};
									}
									return true;
								}
							}
						}
					},
					name: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/modifier.validation.name") }}'
							}
						}
					},
					description: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("common.validation.description") }}'
							}

						}
					},
					modifiers_type_id: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/modifier.validation.modifiers_category") }}'
							}
						}
					},
					anesthesia_base_unit: {
						message: '',
						validators: {
							regexp: {
								regexp: /^[a-zA-Z0-9. ]+$/,
								message: '{{ trans("common.validation.alphanumericdot") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator) {
									var count = value.split(".").length - 1;
									if (count > 1) {
										return {
											valid: false,
											message: '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}'
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