<!--1st row-->
<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
            <div class="box box-info no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

             <div class="box-body  form-horizontal space20">
                    <div class="form-group">
                          {!! Form::label('change_indicator', 'Change Indicator', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label']) !!}
                          <div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('change_indicator')) error @endif ">
                          {!! Form::text('change_indicator',null,['maxlength'=>'7','class'=>'form-control']) !!}
                          {!! $errors->first('change_indicator', '<p> :message</p>')  !!}
                          </div>
                          <div class="col-sm-1"></div>
                    </div>
                    <div class="form-group">
                              {!! Form::label('short_description', 'Short Description', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label']) !!}
                              <div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('short_desc')) error @endif ">
                                 {!! Form::text('short_desc',null,['maxlength'=>'28','class'=>'form-control']) !!}
                                 {!! $errors->first('short_desc', '<p> :message</p>')  !!}
                              </div>
                              <div class="col-sm-1"></div>
                    </div>
                    <div class="form-group">
                              {!! Form::label('long_description', 'Long Description', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label']) !!}
                              <div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('long_desc')) error @endif ">
                                 {!! Form::textarea('long_desc',null,['maxlength'=>'163','class'=>'form-control']) !!}
                                 {!! $errors->first('long_desc', '<p> :message</p>')  !!}
                              </div>
                              <div class="col-sm-1"></div>
                    </div>
            </div>

        <div class="box-footer">
                            <div class="col-lg-12 col-md-12 col-sm-6">
                            {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
                                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                                @if(strpos($currnet_page, 'edit') !== false  && $checkpermission->check_adminurl_permission('admin/icd09/{icd09_id}/delete') == 1)
                                <a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure would you like to delete this ICD-9?" href="{{ url('admin/icd09/'.$icd->id.'/delete') }}">Delete</a></center>
                                @endif
                                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics','onclick' => 'history.back(-1)']) !!}
                            </div>
         </div>
 </div>

@push('view.scripts')

<script type="text/javascript">
$(document).ready(function() {
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
            short_desc:{
                message:'Short description field is invalid',
                validators:{
                    notEmpty:{
                        message: 'Enter short description'
                    },
                    regexp:{
                        regexp: /^[a-zA-Z0-9\s\.\-\,]{0,48}$/,
                        message: 'Short description should not exceed 48 characters!'
                    }

                }
            },
            medium_desc:{
                message:'Medium description field is invalid',
                validators:{
                    notEmpty:{
                        message: 'Enter medium description'
                    },
                    regexp:{
                        regexp: /^[a-zA-Z0-9\s\.\-\,]{0,60}$/,
                        message: 'Medium description should not exceed 60 characters!'
                    }
                }
            },

            long_desc:{
                message:'Long description field is invalid',
                validators:{
                    notEmpty:{
                        message: 'Enter long description'
                    },

                }
            },

          code:{
                message:'code field is invalid',
                validators:{
                    notEmpty:{
                        message: 'Enter code'
                    },
                    regexp:{
                        regexp: /^[a-zA-Z0-9\s\.\-\,]{0,7}$/,
                        message: 'Enter valid code!'
                    }
                }
            },
/*
          change_indicator:{
                message:'change indicator field is invalid',
                validators:{
                    notEmpty:{
                        message: 'Enter change indicator'
                    },
                    regexp:{
                        regexp: /^[a-zA-Z0-9\s\.\-\,]{0,7}$/,
                        message: 'Enter valid change indicator!'
                    }
                }
            },
*/
          code_status:{
                message:'code status field is invalid',
                validators:{
                    notEmpty:{
                        message: 'Enter code status'
                    },
                    regexp:{
                        regexp: /^[a-zA-Z0-9\s\.\-\,]{0,7}$/,
                        message: 'Enter valid code status!'
                    }
                }
            }
        }
    });
});
</script>
@endpush
