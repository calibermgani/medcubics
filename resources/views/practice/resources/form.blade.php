<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-md-12" >
            <div class="box box-info no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="box-body  form-horizontal">
                    <div class="form-group">
                       {!! Form::label('resource_name', 'Resource Name', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('resource_name')) error @endif">
                           {!! Form::text('resource_name',null,['class'=>'form-control']) !!}
                            {!! $errors->first('resource_name', '<p> :message</p>')  !!}
                        </div>
                         <div class="col-sm-1 col-xs-2"></div>
                    </div> 
                           
                    <div class="form-group">
                       {!! Form::label('resource_location_id', 'Resource Facility', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('resource_location_id')) error @endif">
                           {!! Form::select('resource_location_id', array(''=>'-- Select --')+(array)$facilities,  $facility_id,['class'=>'select2 form-control','id'=>'resource_location_id']) !!}  
                           {!! $errors->first('resource_location_id', '<p> :message</p>')  !!}
                        </div>
                         <div class="col-sm-1 col-xs-2"></div>
                    </div> 
                

                
                    <div class="form-group">
                       {!! Form::label('resource_code', 'Resource Code', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('resource_code')) error @endif">
                           {!! Form::text('resource_code',null,['maxlength'=>'4','class'=>'form-control']) !!}
                            {!! $errors->first('resource_code', '<p> :message</p>')  !!}
                        </div>
                         <div class="col-sm-1 col-xs-2"></div>
                    </div>
                

                
                    <div class="form-group">
                       {!! Form::label('phone_number', 'Phone Number', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('phone_number')) error @endif">
                           {!! Form::text('phone_number',null,['class'=>'form-controldm-phone']) !!}
                            {!! $errors->first('phone_number', '<p> :message</p>')  !!}
                        </div>
                         <div class="col-sm-1 col-xs-2"></div>
                    </div>
                

                
                    <div class="form-group">
                       {!! Form::label('default_provider_id', 'Default Provider', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('default_provider_id')) error @endif">
                        {!! Form::select('default_provider_id', array(''=>'-- Select --')+(array)$providers,  $provider_id,['class'=>'select2 form-control','id'=>'default_provider_id']) !!}  
                        {!! $errors->first('default_provider_id', '<p> :message</p>')  !!}
                        </div>
                         <div class="col-sm-1 col-xs-2"></div>
                    </div> 
                </div>

                <div class="box-footer">
                     <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12">
                   {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
                    <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                    @if(strpos($currnet_page, 'edit') !== false)
                        <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete this resources?"
                        href="{{ url('resources/delete/'.$resources->id) }}">Delete</a>
						<a href="{{ url('resources/'.$resources->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
					@else
						<a href="{{ url('resources')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
                    @endif   
                     
                    </div>
                </div>
            </div><!-- /.box -->
        </div>
    </div>
</div>

@push('view.scripts')
    <script type="text/javascript">
        $(document).ready(function () {

            $('[name="phone_number"]').on('change',function(){
                $('#js-bootstrap-validator')
                .data('bootstrapValidator')
                .updateStatus('phone_number', 'NOT_VALIDATED')
                .validateField('phone_number');
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

                    fields: {

                        resource_name:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Enter employer name!'
                                        }
                                    }
                                },

                        resource_location_id:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Select resource facility!'
                                        }
                                    }
                                },

                        resource_code:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Enter resource code!'
                                        }
                                    }
                                },
                        
                        default_provider_id:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Select default provider!'
                                        }
                                    }
                                },

                        phone_number: {
                            message: '',
                            validators: {
                                callback: {
                                    message: 'Enter valid phone number',
                                    callback: function (value, validator) {
                                        if (value.search("\\(\[0-9]{3}\\\)\\s[0-9]{3}\-\[0-9]{4}") == -1)
                                            return false;
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