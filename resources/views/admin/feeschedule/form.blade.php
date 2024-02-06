<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
            <div class="box box-info no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <!-- form start -->


                <div class="box-body  form-horizontal">

                    <div class="form-group">
                        {!! Form::label('FileName', 'File Name', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('file_name')) error @endif">
                            {!! Form::text('file_name',null,['class'=>'form-control js-letters-caps-format']) !!}
                            {!! $errors->first('file_name', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('FeesType', 'Fees Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('fees_type')) error @endif">
                            {!! Form::select('fees_type', [
                            '' => '-- Select --',
                            'Allowed Amount' => 'Allowed Amount',
                            'Billed Amount' => 'Billed Amount'],null,['class'=>'form-control select2']
                            ) !!}
                            {!! $errors->first('fees_type', '<p> :message</p>')  !!}
                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('Template', 'Template', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('template')) error @endif">
                            {!! Form::select('template', [
                            '' => '-- Select --',
                            'Medicare Fees' => 'Medicare Fees',
                            'Custom Fees' => 'Custom Fees'],null,['class'=>'form-control select2']
                            ) !!}
                            {!! $errors->first('template', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('ChooseYear', 'Choose Year', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('choose_year')) error @endif">
                            {!! Form::text('choose_year',null,['class'=>'form-control dm-year','maxlength'=>'4']) !!}
                            {!! $errors->first('choose_year', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('ConversionFactor', 'Conversion Factor', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('conversion_factor')) error @endif">
                            {!! Form::text('conversion_factor',null,['class'=>'form-control']) !!}
                            {!! $errors->first('conversion_factor', '<p> :message</p>')  !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Percentage', 'Percentage', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('percentage')) error @endif">
                            {!! Form::text('percentage',null,['class'=>'form-control']) !!}
                            {!! $errors->first('percentage', '<p> :message</p>')  !!}
                        </div>                        
                    </div>

                </div><!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
						<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
							@if(strpos($currnet_page, 'edit') !== false)
								@if($checkpermission->check_adminurl_permission('admin/feeschedule/{feeschedule_id}/delete') == 1)
									<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('admin/feeschedule/'.$feeschedules->id.'/delete') }}">Delete</a>
								@endif

								<a href="{{ url('admin/feeschedule/'.$feeschedules->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
							@endif
						   @if(strpos($currnet_page, 'edit') == false)
								<a href="{{ url('admin/feeschedule') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
							 @endif
                    </div>
                </div><!-- /.box-footer -->
            </div><!-- /.box -->
        </div><!--/.col (left) -->
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->


@push('view.scripts')
<script type="text/javascript">

    $(document).ready(function () {
        
        $('[name="choose_year"]').on('change',function() {
                $('#js-bootstrap-validator')
                .data('bootstrapValidator')
                .updateStatus('choose_year', 'NOT_VALIDATED')
                .validateField('choose_year');
        });
        
        $('#js-bootstrap-validator')
            .find('[name="fees_type"]')
            .select2()
            // Re-validate the color when it is changed
            .change(function (e) {
                $('#js-bootstrap-validator')
                        .data('bootstrapValidator')
                        .updateStatus('fees_type', 'NOT_VALIDATED')
                        .validateField('fees_type');
            })
            .end()
            .find('[name="template"]')
            .select2()
            // Re-validate the color when it is changed
            .change(function (e) {
                $('#js-bootstrap-validator')
                        .data('bootstrapValidator')
                        .updateStatus('template', 'NOT_VALIDATED')
                        .validateField('template');
            })
            .end()
            .bootstrapValidator({
                message: 'This value is not valid',
                excluded: ':disabled',
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    fees_type: {
                        message: 'Fees type field is invalid',
                        validators: {
                            notEmpty: {
                                message: 'Select fees type'
                            }
                        }
                    },
                    template: {
                        message: 'Template field is invalid',
                        validators: {
                            notEmpty: {
                                message: 'Select template'
                            }
                        }
                    },
                    file_name: {
                        message: 'File name is invalid',
                        validators: {
                            notEmpty: {
                                message: 'Enter file name'
                            }
                        }
                    },
                    choose_year: {
                        message: 'Choose year is invalid',
                        validators: {
                            notEmpty: {
                                message: 'Enter year'
                            },
                            regexp: {
                                regexp: /^[0-9]{4}$/,
                                message: 'Year field should be 4 digits'
                            }

                        }
                    },
                    conversion_factor: {
                        message: 'Conversion factor is invalid',
                        validators: {
                            notEmpty: {
                                message: 'Enter conversion factor'
                            }
                        }
					},
                    percentage: {
                        message: 'Percentage is invalid',
                        validators: {
                            notEmpty: {
                                message: 'Enter percentage'
                            }
                        }
                    },
                }
            });
    });
</script>
@endpush
