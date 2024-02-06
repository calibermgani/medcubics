<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
	<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.templates") }}' />
    <div class="box box-info no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Content</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->

        <div class="box-body  form-horizontal">

            <div class="js_template_pair_access">
				<div id="tpairs" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<?php $current_page = Route::getFacadeRoot()->current()->uri(); ?>
					@foreach($templatepairs as $key => $value)
					<span class="template-links" id="templatetags" data-value="{{ $value }}">{{$key}}</span>
					@endforeach
				</div>
				<div  class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<p><span class="med-orange font600">Alert :</span> <span class="med-gray-dark">{{ trans("practice/practicemaster/template.validation.alert_msg") }}</span></p>
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
			@if(strpos($current_page, 'edit') !== false)
            <input type="hidden" id="page" value="edit" />
            @if($checkpermission->check_url_permission('templates/delete/{id}') == 1)
				@if($patient_correspondence == 0)
					<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure to delete the entry?" href="{{ url('templates/delete/'.$templates->id) }}">Delete</a>
				@endif
			@endif	
            <a href="javascript:void(0)" data-url="{{ url('templates/'.$templates->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            @else
            <input type="hidden" id="page" value="add" />
            <a href="javascript:void(0)" data-url="{{ url('templates')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
            @endif
        </div>

    </div><!-- /.box -->
</div><!--/.col (left) -->

@push('view.scripts')  
<script type="text/javascript">
    $(document).ready(function () {
		$(document).on("change",'select[name="template_type_id"]',function () {
			var template_type = $('select[name="template_type_id"]').find('option:selected').text().trim().toLowerCase();
			if(template_type =="benefit verification" || template_type =="benefit verifications") {
				$(".js_template_pair_access").addClass("hide");
				var textbox_text = $("#editor1").val();
				var split_text = textbox_text.match(/\##VAR-(.*?)\##/g);
				$.each(split_text, function (key, val) {
					textbox_text = textbox_text.replace(val, "");
				});
				CKEDITOR.instances['editor1'].setData(textbox_text);
			}
			else
				$(".js_template_pair_access").removeClass("hide");
		});
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
                            }/*,
                            regexp: {
                                regexp: /^[A-Za-z ]+$/,
                                message: '{{ trans("common.validation.alphaspace") }}'
                            }*/
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
                    }
                }
            });
    });
</script>
@endpush