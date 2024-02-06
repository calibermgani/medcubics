<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.setquestionaire") }}' />
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!--  Left side Content Starts -->   

    <div class="box no-shadow"><!-- General Information Box Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title"> General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    {!! Form::label('facility', 'Facility',['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}                                                                                             
                    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10 @if($errors->first('facility_id')) error @endif">
                        {!! Form::select('facility_id', array(''=>'-- Select --')+(array)$facility,  NULL,['class'=>'form-control select2','id'=>'facility_id']) !!}
                        <p class="js-sel-provider-type-dis hide no-bottom font12"></p>
                        {!! $errors->first('facility_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <div class="form-group">
                    {!! Form::label('provider', 'Provider', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
                    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10 @if($errors->first('provider_id')) error @endif">
                        {!! Form::select('provider_id', array(''=>'-- Select --')+(array)$provider,  NULL,['class'=>'form-control select2','id'=>'provider_id']) !!}
                        <p class="js-sel-provider-type-dis hide no-bottom font12"></p>
                        {!! $errors->first('provider_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <div class="form-group">
                    {!! Form::label('questionnaires', 'Questionnaires',['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
                    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10 @if($errors->first('template_id')) error @endif">
                        {!! Form::select('template_id', array(''=>'-- Select --')+(array)$questionnaires,  NULL,['class'=>'form-control select2','id'=>'template_id']) !!}
                        <p class="js-sel-provider-type-dis hide no-bottom font12"></p>
                        {!! $errors->first('template_id', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

            </div>
            
            <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
                {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
                @if(strpos($currnet_page, 'edit') !== false)
                @if($checkpermission->check_url_permission('questionnaires/{id}/delete') == 1)
                <a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure would you like to delete?" href="{{ url('questionnaires/'.$id.'/delete') }}">Delete</a>
                @endif
                <a href="javascript:void(0)" data-url="{{ url('questionnaires/'.$id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @else
                <a href="javascript:void(0)" data-url="{{ url('questionnaires') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @endif
            </div>
        </div><!--  Left side Content Ends -->
        
    </div>
</div><!--Background color for Inner Content Starts -->

{!! Form::close() !!}
@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.js-set-questionaire').bootstrapValidator({
            message: 'This value is not valid',
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                facility_id: {
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/questionnaries.validation.facility_id") }}'
                        },
                    }
                },
                provider_id: {
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/questionnaries.validation.provider_id") }}'
                        },
                    }
                },
                template_id: {
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/questionnaries.validation.template_id") }}'
                        },
                    }
                }
            }
        });
    });
</script>
@endpush