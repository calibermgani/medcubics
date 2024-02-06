@extends('admin')
<?php //dd($patients); ?>
@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.document_open')}} font14"></i> Documents <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li> <a href="javascript:void(0)" data-url="{{ url('patients/'.$patients->id.'/documents')}}" class="js_next_process"> <i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <?php $uniquepatientid = $patients->id; ?>	
            @include ('patients/layouts/patientstatement_icon')
            @include ('patients/layouts/swith_patien_icon')			
<!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->            
            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patients->id,'needdecode'=>'yes'])
@stop

@section('practice')
<div class="box-body"><!--Background color for Inner Content Starts -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >
        {!! Form::open(['url'=>'patients/document/add/'.$patients->id,'id'=>'js-bootstrap-validator','files'=>true,'method'=>'POST','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
        <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.documents") }}' />
        <div class="box no-shadow">
            <div class="box-block-header ">
                <i class="livicon" data-name="folders"></i> <h3 class="box-title">New Document</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->
            <div class="box-body  form-horizontal margin-l-10">                        
                <div class="form-group">
                    {!! Form::label('title', 'Title', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                    <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('title')) error @endif">
                        {!! Form::text('title',null,['class'=>'form-control','id'=>'tle']) !!} 
                        {!! $errors->first('title', '<p> :message</p>')  !!} 
                    </div>
                    <div class="col-sm-1"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('category', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                    <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('category')) error @endif">
                        {!! Form::select('category', array('' => '-- Select --') + (array)$category_list,null,['class'=>'select2 form-control','id'=>'category']) !!}
                        {!! $errors->first('category', '<p> :message</p>')  !!} 
                    </div>
                    <div class="col-sm-1"></div>
                </div> 
                <div class="form-group">
                    <?php $webcam = App\Http\Helpers\Helpers::getDocumentUpload('webcam'); ?>
                    <?php $scanner = App\Http\Helpers\Helpers::getDocumentUpload('scanner'); ?>
                    @if($webcam || $scanner)  
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                        {!! Form::label('attachment', 'Attachment', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-4 control-label star']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            {!! Form::radio('upload_type', 'browse',true,['class'=>'flat-red js-upload-type']) !!} Upload &emsp;
                            @if($webcam){!! Form::radio('upload_type', 'webcam',null, ['class'=>'flat-red js-upload-type']) !!} Picture &emsp;@endif
                            @if($scanner){!! Form::radio('upload_type', 'scanner',null,['class'=>'flat-red js-upload-type']) !!} Scanner @endif
                        </div>
                        <div class="col-sm-1"></div>				 
                    </div> 
                    @endif
                    <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-upload margin-t-10">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="dropdown pull-right">
                                <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-question-circle margin-t-3 med-green form-icon-billing pull-right"  data-placement="top" data-toggle="tooltip" data-original-title="Info"></i>
                                </a>
                                <div class="dropdown-menu1">
                                    <p class="font12 padding-4">pdf, jpeg, jpg, png, gif, doc, xls, csv, docx, xlsx, txt</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding">
                            <span class="fileContainer " style="padding:1px 16px;">
                                <input class="form-control form-cursor uploadFile" name="filefield" type="file" id="filefield1">Upload  </span>
                            {!! $errors->first('filefield',  '<p> :message</p>')  !!}
                            <div>&emsp;<p class="js-display-error" style="display: inline;"></p>
                                <span><i class="fa fa-times-circle cur-pointer removeFile margin-l-10 med-red" data-placement="bottom" data-toggle="tooltip" title="Remove" data-original-title="Tooltip on bottom" style="display:none;"></i></span>
                            </div>
                        </div>
                    </div>							 

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js-photo margin-t-10" style="display:none">
                        {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-4 control-label']) !!} 
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <span class="fileContainer js-webcam-class" style="padding:1px 20px;">
                                <input type="hidden" class="js_err_webcam" /> Webcam</span>
                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                            &emsp;<span class="js-display-error"></span>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="box-footer js-scanner" style="display:none"> 
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <button type="button" class="btn btn-medcubics" onclick="scan();">Scan</button>
                        </div>
                    </div>
                    <input type="hidden" name="upload_type" value="browse">
                    <input type="hidden" name="scanner_filename" id="scanner_filename">
                    <input type="hidden" name="scanner_image" id="scanner_image">
                    @if($errors->first('filefield'))
                    <div class="form-group">
                        {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-4 control-label']) !!}
                        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('filefield')) error @endif">
                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                        </div>                                                          
                        <div class="col-sm-1"></div>
                    </div>
                    @endif
                </div><!-- /.box-body -->
            </div><!-- /.box-body -->
            <div class="box-footer">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                    {!! Form::submit('Save', ['class'=>'btn btn-medcubics form-group']) !!}
                    <a href="javascript:void(0)" data-url="{{ url('patients/'.$patients->id.'/documents')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                </div>
            </div><!-- /.box-footer -->
        </div><!-- /.box -->
        <div style="display:none" id="js-show-webcam">
            <?php $document_type = "patients"; ?>  
            @if($document_type=='patients')
            @include ('layouts/webcam', ['type' => 'patient_document'])
            @endif
        </div>
        {!! Form::close() !!}
    </div><!--/.col (left) -->
</div><!--Background color for Inner Content Ends -->	
@stop 

@push('view.scripts1')  
<script type="text/javascript">
    $(document).ready(function () {
        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            excluded: [':disabled'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                title: {
                    message: '',
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: title_lang_err_msg
                        },
                        maxlength: {
                            maxlength : 120,
                            message: "Maxmimum Character Exceeded"
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 ]+$/,
                            message: alphanumericspace_lang_err_msg
                        },
                        callback: {
                            message: '',
                            callback: function(value, validator, $field) {
                                var count = $("#tle").val().length;
                                if(count >= 120){
                                    return {
                                        valid: false,
                                        message: "Maxmimum Character Exceeded"
                                    };
                                } else {
                                    return true;
                                }
                            }
                        }
                    }
                },
                category: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: category_lang_err_msg
                        },
                    }
                },
                js_err_webcam: {
                    message: '',
                    selector: '.js_err_webcam',
                    validators: {
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                var get_checked_val = $('input[name="upload_type"]:checked').val();
                                var err_msg = $('#error-cam').val();
                                if ((err_msg == '' || err_msg == null || err_msg == 1) && get_checked_val == "webcam") {
                                    if (value == '' || value == null)
                                        return false;
                                    else
                                        return true;
                                }
                                return true;
                            }
                        }
                    }
                },
                filefield: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: attachment_lang_err_msg
                        },
                        file: {
                            maxSize: filesize_max_defined_length * 32768,
                            message: attachment_length_lang_err_msg
                        },
                        callback: {
                            message: attachment_valid_lang_err_msg,
                            callback: function (value, validator) {
                                if ($('[name="filefield"]').val() != "") {
                                    var extension_Arr = ['pdf', 'jpeg', 'jpg', 'png', 'gif', 'doc', 'xls', 'csv', 'docx', 'xlsx', 'txt', 'PDF', 'JPEG', 'JPG', 'PNG', 'GIF', 'DOC', 'XLS', 'CSV', 'DOCX', 'XLSX', 'TXT'];
                                    var file_name = $('[name="filefield"]')[0].files[0].name;
                                    var temp = file_name.split(".");
                                    if (extension_Arr.indexOf(temp[1]) == -1) {
                                        return false;
                                    } else {
                                        return true;
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