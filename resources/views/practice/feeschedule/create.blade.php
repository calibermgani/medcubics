@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Fee Schedule <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('feeschedule') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/fees_schedule')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
{!! Form::open(['url'=>['feeschedule'],'id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}    
<?php /* <input type="hidden" name="_token" value="{{ csrf_token() }}" > */ ?>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.feeschedules") }}' />
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" ><!-- col-12 starts -->
    <div class="box no-shadow"><!-- Box Starts -->
        <div class="box-block-header with-border">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Add Details</h3>
        </div><!-- /.box-header -->             
        <div class="box-body form-horizontal margin-t-10 margin-l-10"><!-- Box Body Starts -->
            
             @if($fav_count != 0)
            <div class="form-group">
                {!! Form::label('Download Sample Favorite CPT/HCPCS', 'Download Sample Favorite CPT/HCPCS', ['class'=>'col-lg-3 col-md-5 col-sm-5 col-xs-12 control-label']) !!}                               				
                <div class="col-sm-2 p-l-0"><a href="{{ url('feeschedule_file/sample') }}" id="sample_format" class="med-orange font600 download-btn">Download</a></div>								
            </div>
            @endif
            
            
            <div class="form-group">
                {!! Form::label('Upload File', 'Upload File', ['class'=>'col-lg-3 col-md-5 col-sm-5 col-xs-12 control-label star']) !!}
                <!--<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('conversion_factor')) error @endif">
                        {!! Form::file('upload_file',['accept'=>'application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) !!}
                        {!! $errors->first('upload_file', '<p class="med-red font12"> :message</p>')  !!}
                </div>-->

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-10 no-padding @if($errors->first('conversion_factor')) error @endif">
                    <span class="fileContainer" style="padding:1px 20px;margin-left: 0px;">
                        <input class="col-lg-2 col-md-2 col-sm-3 form-control" name="upload_file" id="feeScheduleDoc" type="file" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">	 Upload </span>
                    <span class="">{!! $errors->first('upload_file',  '<p> :message</p>')  !!}</span>
                    &emsp;<span class="js-display-error"></span>
                </div>				
            </div>

            

            <div class="form-group">
                {!! Form::label('Percentage', 'Percentage', ['class'=>'col-lg-3 col-md-5 col-sm-3 col-xs-12 control-label']) !!} 
                <div style="padding-left: 0px;" class="col-lg-2 col-md-4 col-sm-6 col-xs-10 @if($errors->first('percentage')) error @endif">
                    {!! Form::text('percentage',null,['autocomplete'=>'off' ,'id'=>'percentage' ,'class'=>'form-control','maxlength'=>'3','onKeyPress'=>'if (document.layers) var c = event.which; else if (document.all)var c = event.keyCode;else var c = event.charCode;var s = String.fromCharCode(c); if(c!=0) return /[0-9]/.test(s);else return test(s);']) !!}
                    {!! $errors->first('percentage', '<p> :message</p>')  !!}
                </div>
                <!--div class="col-sm-2">
                        <a href="{{ url('feeschedule_file/cptcode') }}" id="sample_format" class="med-orange font600">Get CPT code</a>
                </div-->
            </div>

            <?php $year_range = array_combine(range(date("Y")+0, date("Y")-4), range(date("Y")+0, date("Y")-4)); ?>

            <div class="form-group">
                {!! Form::label('year_lbl', 'Year', ['class'=>'col-lg-3 col-md-5 col-sm-5 col-xs-12 control-label star']) !!} 
                <div style="padding-left: 0px;" class="col-lg-2 col-md-4 col-sm-6 col-xs-10">
                    {!! Form::select('choose_year', array('' => '-- Select --') + (array)@$year_range,null,['class'=>'select2 form-control','id'=>'choose_year']) !!}
                    {!! $errors->first('choose_year', '<p> :message</p>')  !!}
                </div>
            </div>
            <?php $insurance = App\Http\Helpers\Helpers::getInsuranceNameLists(); ?>
            <div class="form-group">
                {!! Form::label('year_lbl', 'Payer', ['class'=>'col-lg-3 col-md-5 col-sm-5 col-xs-12 control-label star']) !!} 
                <div style="padding-left: 0px;" class="col-lg-2 col-md-4 col-sm-6 col-xs-10">
                    {!! Form::select('insurance_id', array('' => '-- Select --', '0' => 'Default') + (array)@$insurance,null,['class'=>'select2 form-control','id'=>'insurance_id']) !!}
                    {!! $errors->first('insurance_id', '<p> :message</p>')  !!}
                </div>
            </div>

            <div class="form-group hide">
                {!! Form::label('ConversionFactor', 'Conversion Factor', ['class'=>'col-lg-3 col-md-5 col-sm-5 col-xs-12 control-label']) !!} 
                <div style="padding-left: 0px;" class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('conversion_factor')) error @endif">
                    {!! Form::radio('conversion_factor', 'decimal',true,['class'=>'','id'=>'c-decimal']) !!} {!! Form::label('c-decimal', 'Decimal',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('conversion_factor', 'round_off',null,['class'=>'','id'=>'c-roundoff']) !!} {!! Form::label('c-roundoff', 'Round Off',['class'=>'med-darkgray font600 form-cursor']) !!}
                    {!! $errors->first('conversion_factor', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
            
           
            
        </div><!-- /.box-body -->
        <?php @$feeschedules->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$feeschedules->id,'encode'); ?>
        <div class="box-footer">
            <div class="col-lg-12 col-md-12 col-sm-10 col-xs-12 text-center">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics']) !!}
                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                @if(strpos($currnet_page, 'edit') !== false)
                @if($checkpermission->check_url_permission('feeschedule/{feeschedule_id}/delete') == 1)
                <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete this fee schedule?" href="{{ url('feeschedule/'.$feeschedules->id.'/delete') }}">Delete</a>
                @endif	                    
                <a href="javascript:void(0)" data-url="{{ url('feeschedule/'.$feeschedules->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @else
                <a href="javascript:void(0)" data-url="{{ url('feeschedule')}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                @endif
            </div>
        </div><!-- /.box-footer -->
    </div><!-- Box Ends -->
</div><!--/.col-12 ends -->


{!! Form::close() !!}
@stop   

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#percentage').attr('autocomplete', 'off');
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
                        upload_file: {
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/feeschedule.validation.file") }}'
                                },
                                file: {
                                    extension: 'xls,xlsx,XLS,XLSX',
                                    type: 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    maxSize: 5 * 1024 * 1024, // 5 MB
                                    message: '{{ trans("practice/practicemaster/feeschedule.validation.file_size") }}'
                                },
                                callback: {
                                    callback: function (value, validator) {
                                        $fields = validator.getFieldElements('upload_file');
                                        var get_para_text = $fields.closest('div').find('p.med-red').html();
                                        if (get_para_text != undefined) {
                                            $fields.closest('div').find('p.med-red').addClass('hide');
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        percentage: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[0-9]{0,3}$/,
                                    message: '{{ trans("practice/practicemaster/feeschedule.validation.percentage") }}'
                                },
                                callback: {
                                    callback: function (value, validator) {
                                        if (value < 0) {
                                            return {
                                                valid: false,
                                                message: '{{ trans("practice/practicemaster/feeschedule.validation.percentagemax") }}'
                                            };
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        choose_year: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: 'Select year'
                                }
                            }
                        },
                        insurance_id: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: 'Select payer'
                                }
                            }
                        },
                    }
                }).on('success.form.bv', function (e) {
            e.preventDefault();
            var url = api_site_url + '/feeschedule';
            var formData = new FormData(this);
            var postData = $(this).serializeArray();
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: url,
                processData: false,
                contentType: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                },
                data: formData,
                // {'upload_file' : upload_file, 'percentage' : percentage, 'choose_year' : choose_year, 'insurance_id' : insurance_id, 'conversion_factor' : conversion_factor},            
                success: function (result) { 
                    if ($.trim(result) == "uploadError") { 
                        var mesg = "You are trying to override the Existing records <br> Are you sure want to replace it?";
                        $("#session_model .med-green").html(mesg);
                        $('.btn').prop('disabled', false);
                        $("#session_model")
                                .modal({keyboard: false, backdrop: 'static'}).on('click', '.js_session_confirm', function (e) {
                            var conformation = $(this).attr('id');
                            if (conformation == "true") {
                                var success = "success";
                                formData.append("updateSuccess", success);
                                $.ajax({
                                    type: "POST",
                                    enctype: 'multipart/form-data',
                                    url: url,
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    headers: {
                                        'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                                    },
                                    data: formData,
                                    success: function (result) {
                                        js_alert_popup("Your records have been successfully updated");
                                        $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').html('');
                                        $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').append('<a href="' + api_site_url + '/feeschedule" class="btn btn-medcubics-small">OK</a>');
                                    }
                                });
                            }
                            else {
                            }
                        });
                    }
                    else if ($.trim(result) == "success") {
                        js_alert_popup("Your records have been successfully created");
                        $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').html('');
                        $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').append('<a href="' + api_site_url + '/feeschedule" class="btn btn-medcubics-small">OK</a>');
                    }
                    else {
                        //var error = result.message;
                        if ($('#temp-block').length==0) {
                            $('.col-lg-2.col-md-2.col-sm-6.col-xs-10.no-padding').append('<small class="help-block" id="temp-block" style="color:red;">Please, upload valid file. Refer sample file</small>');
                        }
                        // $("input[data-bv-validator='file']").html('').css('display', 'block');
                    }
                }
            });
        });
    });

    // $('#js-bootstrap-validator').submit(function(e) {
    //     e.preventDefault();
    //     var url = api_site_url+'/feeschedule';
    //     var formData = new FormData(this);
    //     var postData = $(this).serializeArray();
    
    //     $.ajax({
    //         type: "POST",
    //         enctype: 'multipart/form-data',
    //         url: url,
    //         processData: false,
    //         contentType: false,
    //         cache: false,
    //         headers: {
    // 			'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
    // 		},            
    //         data: formData,
    //         // {'upload_file' : upload_file, 'percentage' : percentage, 'choose_year' : choose_year, 'insurance_id' : insurance_id, 'conversion_factor' : conversion_factor},            
    //         success: function(result){
    //             if(result == "uploadError") {
    //                 var mesg = "Your trying to overwrite the existing records <br> Are you sure?";
    //                 $("#session_model .med-green").html(mesg);
    //                 $("#session_model")
    //                 .modal({ show: 'false', keyboard: false }).on('click', '.js_session_confirm', function (e) {
    //         		  var conformation = $(this).attr('id');
    //                     if (conformation == "true") {
    //                         var success = "success";
    //                         formData.append("updateSuccess", success);
    
    //                         $.ajax({
    //                             type: "POST",
    //                             enctype: 'multipart/form-data',
    //                             url: url,
    //                             processData: false,
    //                             contentType: false,
    //                             cache: false,
    //                             headers: {
    //                                 'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
    //                             },            
    //                             data: formData,
    //                             success: function(result) {
    
    //                                 js_alert_popup("Your records have been successfully updated");
    //                                 $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').html('');
    //                                 $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').append('<a href="' + api_site_url + '/feeschedule" class="btn btn-medcubics-small">OK</a>');
    //                             }
    //                         });
    //                     }
    //                     else {

    //                     }
    //                 });
    //             } 
    //             else if(result == "success") {
    
    //                 js_alert_popup("Your records have been successfully created");
    //                 $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').html('');
    //                 $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').append('<a href="' + api_site_url + '/feeschedule" class="btn btn-medcubics-small">OK</a>');
    //             }
    //             else {
    //                 console.log(result);
    //             }
    //         }
    // 	});
    // });
</script>
@endpush