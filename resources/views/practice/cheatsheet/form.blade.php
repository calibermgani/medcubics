<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-md-12" ><!-- Col Starts -->
            <div class="box box-info no-shadow"><!-- Box Starts -->
                    <div class="box-block-header with-border">
                        <i class="livicon" data-name="new-window"></i> <h3 class="box-title">General</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                    </div><!-- /.box-header -->
                <!-- form start -->
                    <div class="box-body  form-horizontal">
                        <div class="form-group">
                           {!! Form::label('resource_id', 'Resource Name', ['class'=>'col-sm-3 control-label']) !!} 
                            <div class="col-sm-3 @if($errors->first('resource_id')) error @endif">
                               {!! Form::select('resource_id', array(''=>'-- Select --')+(array)$resources,  $resource_id,['class'=>'select2 form-control','id'=>'resource_id']) !!}
                               {!! $errors->first('resource_id', '<p> :message</p>')  !!}
                            </div>
                             <div class="col-sm-1"></div>
                        </div> 
                    </div>             

                    <div class="box-body  form-horizontal">
                        <div class="form-group">
                           {!! Form::label('facility_id', 'Facility', ['class'=>'col-sm-3 control-label']) !!} 
                            <div class="col-sm-3 @if($errors->first('facility_id')) error @endif">
                               {!! Form::select('facility_id', array(''=>'-- Select --')+(array)$facilities,  $facility_id,['class'=>'select2 form-control','id'=>'facility_id']) !!}  
                               {!! $errors->first('facility_id', '<p> :message</p>')  !!}
                            </div>
                             <div class="col-sm-1"></div>
                        </div> 
                    </div>

                    <div class="box-body  form-horizontal">
                        <div class="form-group">
                           {!! Form::label('provider_id', 'Provider', ['class'=>'col-sm-3 control-label']) !!} 
                            <div class="col-sm-3 @if($errors->first('provider_id')) error @endif">
                            {!! Form::select('provider_id', array(''=>'-- Select --')+(array)$providers,  $provider_id,['class'=>'select2 form-control','id'=>'provider_id']) !!}  
                            {!! $errors->first('provider_id', '<p> :message</p>')  !!}
                            </div>
                             <div class="col-sm-1"></div>
                        </div> 
                    </div>

                    <div class="box-body  form-horizontal">
                        <div class="form-group">
                           {!! Form::label('visit_type_id', 'Visit Type', ['class'=>'col-sm-3 control-label']) !!} 
                            <div class="col-sm-3 @if($errors->first('visit_type_id')) error @endif">
                                {!! Form::select('visit_type_id', [
                                    '' => '-- Select --',
                                    'Visit Type One' => 'Visit Type One',
                                    'Visit Type Two' => 'Visit Type Two'],null,['class'=>'form-control select2']
                                    ) !!}
                                {!! $errors->first('visit_type_id', '<p> :message</p>')  !!}
                            </div>
                             <div class="col-sm-1"></div>
                        </div>
                    </div>

                    <div class="box-body  form-horizontal">
                        <div class="form-group">
                           {!! Form::label('cpt', 'CPT', ['class'=>'col-sm-3 control-label']) !!} 
                            <div class="col-sm-3 @if($errors->first('cpt')) error @endif">
                               {!! Form::text('cpt',null,['maxlength'=>'4','class'=>'form-control']) !!}
                                {!! $errors->first('cpt', '<p> :message</p>')  !!}
                            </div>
                             <div class="col-sm-1"></div>
                        </div>
                    </div>

                    <div class="box-body  form-horizontal">
                        <div class="form-group">
                           {!! Form::label('icd', 'ICD', ['class'=>'col-sm-3 control-label']) !!} 
                            <div class="col-sm-3 @if($errors->first('icd')) error @endif">
                               {!! Form::text('icd',null,['maxlength'=>'7','class'=>'form-control']) !!}
                                {!! $errors->first('icd', '<p> :message</p>')  !!}
                            </div>
                             <div class="col-sm-1"></div>
                        </div>
                    </div>

                     <div class="box-body  form-horizontal">
                        <div class="form-group">
                           {!! Form::label('claimstatus', 'Claim Status', ['class'=>'col-sm-3 control-label']) !!} 
                            <div class="col-sm-3 @if($errors->first('claimstatus')) error @endif">
                               {!! Form::text('claimstatus',null,['class'=>'form-control']) !!}
                                {!! $errors->first('claimstatus', '<p> :message</p>')  !!}
                            </div>
                             <div class="col-sm-1"></div>
                        </div>
                    </div>

                    <div class="box-body  form-horizontal">
                        <div class="form-group">
                           {!! Form::label('feeschedules', 'Feeschedules', ['class'=>'col-sm-3 control-label']) !!} 
                            <div class="col-sm-3 @if($errors->first('feeschedules')) error @endif">
                               {!! Form::text('feeschedules',null,['class'=>'form-control']) !!}
                                {!! $errors->first('feeschedules', '<p> :message</p>')  !!}
                            </div>
                             <div class="col-sm-1"></div>
                        </div>
                    </div>

                    <div class="box-footer">
                       {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
                        <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                        @if(strpos($currnet_page, 'edit') !== false)
                            <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete this cheatsheet?"
                            href="{{ url('cheatsheet/delete/'.$cheatsheet->id) }}">Delete</a>
                        @endif                            
                    </div>
            </div><!-- /.box -->
        </div><!-- Col Ends -->
    </div>
</div>

@push('view.scripts')
    <script type="text/javascript">
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
                        resource_id:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Select resource!'
                                        }
                                    }
                                },
                        facility_id:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Select facility!'
                                        }
                                    }
                                },
                        provider_id:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Select provider!'
                                        }
                                    }
                                },
                        visit_type_id:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Select visit type!'
                                        }
                                    }
                                },
                        cpt:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Procedure code is required field. Please enter the same.'
                                        }
                                    }
                                },
                        icd:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Diagnosis code is required field. Please enter the same.'
                                        },
                                        regexp:{
                                            regexp: /^[a-zA-Z0-9\s\.\-\,]{0,7}$/,
                                            message: 'Enter valid code!'
                                        }
                                    }
                                },

                        claimstatus:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Enter claimstatus!'
                                        }
                                    }
                                },
                        feeschedules:{
                                    message:'The Code is invalid',
                                    validators:{
                                        notEmpty:{
                                            message: 'Enter feeschedules!'
                                        }
                                    }
                                },
                        }
            });
    });
</script>
@endpush