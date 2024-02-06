@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
	<input type="hidden" name="csrf_toten_id" value="{{ csrf_token()}}" />
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Notes </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($patients->id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 
            <?php $uniquepatientid = $patients->id; 
					$patientEncId = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($uniquepatientid, 'encode');
			?>
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

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-15">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        @if(Session::get('message')!== null) 
        <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
        @endif
    </div>

    <p class="pull-right margin-b-10 margin-t-10">
        @if($checkpermission->check_url_permission('patients/{id}/notes/create') == 1)

        <?php $patient_id = @$patients->enc_id; ?>
        <a class="js-notes font600 font14 margin-r-10" href="#" accesskey="a"  data-toggle = 'modal' data-target="#create_notes" data-url="patients/{{$patient_id}}/notes/create" tabindex="-1"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Note
        </a>

        @endif
    </p>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
        <div class="box box-info no-shadow">
            <div class="box-header">
                <i class="fa fa-bars font14"></i><h3 class="box-title">List</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->            
            <div class="box-body"><!-- Box Body Starts -->  
                <?php //dd($notes);?>   
                @if(count($notes) > 0)
                @foreach($notes as $note)
                <?php 
					//$note->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($note->id,'encode');
					$noteid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($note->id,'encode');
					$updated_date = Date("Y-m-d", strtotime($note->updated_at)); 
				?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text notes pat-notes" style="border-bottom: 1px solid #f0f0f0; margin-bottom: 5px;"><!-- Inner width Starts -->                     
                    <div class="">
                        <div class="msg-time-chat1">

                            <div class="">
                                <div class="" >
                                    <p class="attribution" style="margin-bottom: 2px;">
                                        <?php $note_type= ucwords(str_replace('_', " ", @$note->patient_notes_type));  ?>                                        
                                        <a class="align-break font600 js-notes" tabindex="-1">@if($note_type == 'Claim Notes') CN @elseif($note_type == 'Claim Denial Notes') CN @elseif ($note_type == 'Patient Notes') PN @elseif ($note_type == 'Alert Notes') AN  @elseif ($note_type == 'Statement Notes') SN @endif 

                                            @if(!empty($note->claims))
                                            :<span class="med-green"> {{ $note->claims->claim_number }}</span>
                                            @endif
                                            - <span class="med-orange">{{ App\Http\Helpers\Helpers::dateFormat(@$note->created_at,'datetime')}}</span>
                                        </a>
                                        <?php                                        
											if (@$note->created_by == Auth::user()->id || Auth::user()->practice_user_type == "practice_admin" || Auth::user()->role_id == 1) { // 1 should be replaced with super admin type
												$edit_url = "#create_notes";
												$delete_class = "js-delete-confirm";
												$status_class = "js-status-change";
												$delete_url = url('patients/' . @$patientEncId . '/notes/delete/' . @$noteid);
												$color_class = '';
												//cur-block
											} else {
												$edit_url = "";
												$delete_class = "";
												$status_class = "";
												$delete_url = "#";
												$color_class = 'med-gray';
											}
                                        ?>
                                        <!-- Edit And Delete notes added in patient notes modules -->
										<!-- Revision  1 : MR-2731 : 23 Aug 2019 : selva -->
										
										
                                        <span class='notesdate'>                                           
                                            {{@$note->user->short_name}}                                            
											<span class="med-gray-dark"> |</span>
											 <a class="@if($status_class == ''){{'cur-block'}}@else{{$status_class}}@endif" data-status="{{$note->status}}" data-note="{{$noteid}}" href="javascript:void(0)" tabindex="-1">{{$note->status}}</a>
											@if(@$note->patient_notes_type !='claim_denial_notes')
                                            @if($checkpermission->check_url_permission('patients/{id}/notes/{id}/edit') == 1)
                                            <span class="med-gray-dark"> |</span>
                                             <a class="js-notes @if($edit_url == ''){{'cur-block'}}@endif" href="#"  data-toggle = 'modal' data-target="{{$edit_url}}" data-url="patients/{{@$patient_id}}/notes/{{@$noteid}}/edit" tabindex="-1"><i class= "fa fa-edit font16 {{$color_class}}" style="margin-right: 2px; margin-left: 3px;" title="@if(!empty($edit_url)){{'Edit'}}@endif"></i></a>
                                            @endif
                                            @endif
                                            @if($checkpermission->check_url_permission('patients/{id}/notes/{id}/delete') == 1)
                                                <span class="med-gray-dark"> |</span> 
                                                <a class="{{$delete_class}} @if($edit_url == ''){{'cur-block'}}@endif @if(@$note->patient_notes_type=='claim_denial_notes' && (@$note->deleted_at != 'null' && !empty(@$note->deleted_at))) {{'disabled'}} @endif"  data-text="Are you sure would you like to delete?" href="{{$delete_url}}"><i class= "fa fa-trash font16 {{$color_class}}" style="margin-right: 2px; margin-left: 3px;" title="@if(!empty($edit_url)){{'Delete'}}@endif"></i></a>
                                            @endif 
                                        </span>
                                    </p>  
                                    <?php //echo $updated_date ;?>

                                    @if(@$note->patient_notes_type=='claim_denial_notes')
                                    @if(@$note->deleted_at == 'null' || empty(@$note->deleted_at))
                                    <?php $denial_notes_arr = App\Models\Patients\Patient::getARDenialNotes(@$note->content); ?>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
                                            <p class="no-bottom margin-t-5"><span class="med-green font600">Denial Date :</span> <span class="">&nbsp; {{App\Http\Helpers\Helpers::dateFormat(@$denial_notes_arr['denial_date'],'date')}}</span> </p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
                                            <p class="no-bottom margin-t-5"><span class="med-green font600">Billed To :</span> <span class="">&nbsp;{{@$denial_notes_arr['denial_insurance']}}</span> </p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
                                            <p class="no-bottom margin-t-5"><span class="med-green font600">Check No :</span> <span class="">&nbsp;{{@$denial_notes_arr['check_no']}}</span> </p>
                                        </div>
                                        @if($denial_notes_arr['reference'] != '')
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
                                            <p class="no-bottom margin-t-5"><span class="med-green font600">Reference :</span> <span class="">&nbsp;{{@$denial_notes_arr['reference']}}</span> </p>
                                        </div>
                                        @endif
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">					
                                            @foreach($denial_notes_arr['denial_code_result'] as $denial_code_result_key=>$denial_code_result_val)
                                            <p class="margin-b-5">{{@$denial_code_result_val}}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
                                            <p class="no-bottom margin-t-5"><span class="med-green font600">Denial Note Deleted</span>  </p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
                                            <p class="no-bottom margin-t-5"><span class="med-green font600">Deleted Date :</span> <span class="">&nbsp;{{App\Http\Helpers\Helpers::dateFormat(@$note->deleted_at,'datetime')}}</span> </p>
                                        </div>
                                         <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
                                            <p class="no-bottom margin-t-5"><span class="med-green font600">User :</span> <span class="">&nbsp;{{@$note->user->short_name}}</span> </p>
                                        </div>
                                    </div>
                                    @endif  
                                    @else 


                                    <p class="align-break" style="background: transparent;">
                                        @if(!empty($note->claims))
                                        <span class="font600 med-darkgray">DOS : {{ App\Http\Helpers\Helpers::dateFormat(@$note->claims->date_of_service) }} - </span>
                                        @endif
                                        {{ $note->content }}
                                        @if($updated_date != "1970-01-01" && $updated_date != "-0001-11-30")
                                        <i> Last Modified - <span class="med-orange">{{ App\Http\Helpers\Helpers::dateFormat(@$note->updated_at,"datetime")}}</span></i>
                                        @endif
                                    </p>
                                    @endif
                                </div>                                    
                            </div>
                        </div>
                    </div>

                </div><!-- Inner width Ends -->                
                @endforeach

                @else
                <div class="alert med-gray-dark text-center font14">No Records Found </div>
                @endif
            </div> <!-- Box Body Ends -->    

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

<?php /* ?>

<div id="create_notes" class="js_common_modal_popup modal fade">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title margin-l-5">Notes</h4>
            </div>
            <div class="modal-body">
                <div class="box-body no-bottom p-b-0"><!--Background color for Inner Content Starts 
                    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.notes") }}' />

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >

                            <div class="box box-info no-shadow no-bottom">
                                
                                <div class="box-body form-horizontal">
                                    <div class="form-group hide">
                                        {!! Form::label('Title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label star']) !!} 
                                        <div class="col-lg-9 col-md-9 col-sm-12 @if($errors->first('title')) error @endif">
                                            {!! Form::text('title',null,['class'=>'form-control','maxlength'=>'100']) !!}
                                            {!! $errors->first('title', '<p> :message</p>')  !!}
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        {!! Form::label('patient_notes_type', 'Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label star']) !!} 
                                        <div class="col-lg-5 col-md-5 col-sm-12 @if($errors->first('patient_notes_type')) error @endif">
                                            {!! Form::select('patient_notes_type',[''=>'-- Select --','alert_notes' => 'Alert Notes','patient_notes' => 'Patient Notes','claim_notes'=>'Claim Notes','statement_notes'=>'Statement Notes'],null,['class'=>'select2 form-control js_patient_notes_type']) !!}
                                            {!! $errors->first('patient_notes_type', '<p> :message</p>')  !!}
                                        </div>
                                    </div>  
                                    <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>

                                    <div class="form-group @if(strpos($currnet_page, 'edit') !== false && $notes->patient_notes_type == 'claim_notes') show @else hide @endif js_claim_note">
                                        {!! Form::label('Claim Number', 'Claim Number', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label star']) !!}
                                        <div class="col-lg-5 col-md-5 col-sm-12 @if($errors->first('codecategory_id')) error @endif">
                                            {!! Form::select('claim_id', array('' => '-- Select Claim Number --') + (array)$claims_id, null,['class'=>'form-control select2']) !!}
                                            {!! $errors->first('claim_id', '<p> :message</p>')  !!}
                                        </div>
                                        <div class="col-sm-1 col-xs-2"></div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('content', 'Content', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label star']) !!} 
                                        <div class="col-lg-9 col-md-9 col-sm-12 @if($errors->first('content')) error @endif">
                                            {!! Form::textarea('content',null,['class'=>'form-control','style'=>'height:170px;']) !!}
                                            {!! $errors->first('content', '<p> :message</p>')  !!}
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                                     {!! Form::submit('Save', ['class'=>'btn btn-medcubics']) !!}
                                        @if(strpos($currnet_page, 'patients') !== false)
                                        <a href="javascript:void(0)" data-url="{{ url('patients/'. $patients->id.'/notes') }}">
                                            {!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}
                                        </a>
                                        @else
                                        {!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
</div>
                </div>Background color for Inner Content Ends	                                
            </div>
        </div> 
    </div>
</div>  
<?php */ ?>
@stop    

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {

        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
              
                patient_notes_type: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/patients/patients_notes.validation.type") }}'
                        }
                    }
                },
                content: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("common.validation.content") }}'
                        }
                    }
                },
                claim_id: {
                    enabled: false,
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/patients/patients_notes.validation.claim_id") }}'
                        }
                    }
                },
            }
        });
    });
</script>
@endpush