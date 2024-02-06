@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php  if(!isset($get_default_timezone)){
           $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
}?> 
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Documents</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($patients->id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 

            <?php $uniquepatientid = $patients->id; $id = $patients->id; ?>	
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
@include ('patients/patients/Document/tabs')
@stop

@section('practice')


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden-print ">
    @if(Session::get('message')!== null) 
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>



<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
    <div class="col-lg-12 margin-t-m-10">
        <div class="box-body form-horizontal  bg-white">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">

                                      
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Inner width Starts -->                                                                         
                        @include("documents/documents/common_document", ['from' => 'patient_tab'])                                             
                    </div><!-- Inner width Ends -->    
                </div>
            </div>
        </div>
    </div>
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10" >

    <div class="box box-info no-shadow">
        <div class="box-header">
            <i class="fa fa-bars font14"></i><h3 class="box-title">List</h3>
            <div class="box-tools pull-right margin-t-2">


            </div>

        </div><!-- /.box-header -->

        <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4" style="position: absolute; z-index:9; left:0px; margin-top: 12px; margin-left: 100px;">                                       

            <!-- <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
               class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a> -->
            <div> @if(Auth::user()->practice_user_type =="practice_admin")
                <a data-type = "delete" class="font600 form-cursor js-document-action"><i class="fa font16 {{Config::get('cssconfigs.common.delete')}}"></i> Delete</a>
                <span class="margin-l-5 margin-r-5">|</span> 
                @endif
                <a class="js-document-action font600 form-cursor" data-type = "download"><i class="fa font16 {{Config::get('cssconfigs.common.download')}}"></i> Download</a> <span class="margin-l-5 margin-r-5">|</span> 
                <a class="js-tab-document font600 form-cursor"><i class="fa font16 {{Config::get('cssconfigs.common.view')}}"></i> View</a></div>


        </div> 
        <div class="box-body">
            {!! Form::hidden('pat_id',$uniquepatientid,['id'=>"patient_id"]) !!}    
            <div class="table-responsive js-append-data-document">                                       
                @include('patients/patients/Document/document_ajax_list')
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

<!-- Patient Note Alert Window Starts  -->
<div id="document_attachment" class="js_common_modal_popup modal fade">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Attachment</h4>
            </div>
            <div class="modal-body">
                <img style="width: 100%" class="js-document">
                <input type="hidden" id="redirect_url" value="">
                <ul class="nav nav-list line-height-26">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 modal-desc text-center"></div>
                    </div>
                </ul>                   
                <div class="modal-footer">
                    <button class="js_note_confirm btn btn-medcubics-small js_common_modal_popup_save close_popup" id="true" type="button">Close</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<!-- Show Problem list start-->
<div id="show_document_assigned_list" class="modal fade in js_model_show_document_assigned_list"></div><!-- /.modal-dialog -->
<!-- Show Problem list end-->

@stop   
@push('view.scripts1')  
<script type="text/javascript">
    $(document).ready(function(){
      $('#documents_wrapper').find('div.row:eq( 1 )').addClass('monitor-scroll');
    });
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
                    validators: {
                        notEmpty: {
                            message: title_lang_err_msg
                        },
						remote: {
							message: 'Title already taken in the selected category',
							url: api_site_url+'/documentTitle',
							data:{'title':$('input[name="title"]').val(),'_token':$('input[name="_token"]').val(),'category_id':function() { return $('#category').val(); }},
							type: 'POST'
						},
                        regexp: {
                            regexp: /^[a-zA-Z0-9 ]+$/,
                            message: alphanumericspace_lang_err_msg
                        }
                    }
                },
                category: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: category_lang_err_msg
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                $('form#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'jsclaimnumber');
								$('form#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'title');
                                return true;
                            }
                        }
                    }
                },
                'jsclaimnumber': {
                    message: '',
                    selector: '#jsclaimnumber',
                    validators: {
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                category_value = $('#category').val();
                                value = $('#jsclaimnumber').val();
                                if (value == null && category_value == 'claim_document') {
                                    return {
                                        valid: false,
                                        message: "Select claim number"
                                    }
                                } else {
                                    return true;
                                }
                            }
                        }
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
                                    var extension_Arr 	= ['pdf','jpeg','jpg','png','gif','doc','zip','xls','csv','docx','xlsx','txt'];
									var file_name 		= $('[name="filefield"]')[0].files[0].name;
									var temp			= file_name.split(".");
									if(extension_Arr.indexOf(temp[1]) == -1){
										return false;
									}else{
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
<?php if(isset($get_default_timezone)){?>
     var get_default_timezone = '<?php echo $get_default_timezone;?>';    
<?php }?>
</script>
@endpush