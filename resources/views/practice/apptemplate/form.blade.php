<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.apptemplate") }}' />
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >

    <div class="box box-info no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->
        <div class="box-body  form-horizontal">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    {!! Form::label('name', 'Name', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label js-letters-caps-format']) !!} 
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-10 @if($errors->first('name')) error @endif">
                        {!! Form::text('name',null,['class'=>'form-control input-sm-modal-billing','name'=>'name','maxlength'=>'100','autocomplete'=>'off']) !!}
                        {!! $errors->first('name', '<p> :message</p>')  !!}
                    </div>                        
                </div>
                <input type="hidden" name="template_type_id" value="{{ $templates->template_type_id}}" />
                <div class="form-group">
                    {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-10 @if($errors->first('status')) error @endif">
                        {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                        {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
                        {!! $errors->first('status', '<p> :message</p>')  !!}
                    </div>                   
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group col-lg-12 col-md-12 col-sm-12 @if($errors->first('content')) error @endif">
                    {!! Form::textarea('content',null,['class'=>'form-control','name'=>'content','id'=>"editor1"]) !!}
                    {!! $errors->first('content', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
        </div><!-- Box Body Ends -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
            <input type="hidden" id="page" value="edit" />
            <a href="javascript:void(0)" data-url="{{ url('apptemplate/'.$templates->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>

        </div>

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
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				framework: 'bootstrap',
				fields: {
					name: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/template.validation.name") }}'
							},
							regexp: {
								regexp: /^[A-Za-z ]+$/,
								message: '{{ trans("common.validation.alphaspace") }}'
							}
						}
					},
					template_type_id: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/template.validation.category_id") }}'
							},
						}
					},
					content: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("common.validation.content") }}'
							}
						}
					}
				}
			});
    });


</script>
@endpush