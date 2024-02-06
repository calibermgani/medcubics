@extends('admin')
@section('toolbar')
<?php 
	$id = Route::getCurrentRoute()->parameter('id'); 
	$current_date = date('m/d/Y');  
	
?>
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> AR Management <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Listing </span></small>
        </h1>
        <ol class="breadcrumb">			
            <?php $uniquepatientid = $id; ?>				
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($uniquepatientid)}} accesskey="b" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>		
          		 
            @include ('patients/layouts/swith_patien_icon')
            <?php /* ?>
              <li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
              <?php */ ?>    
            <li><a href="#js-help-modal" data-url="{{url('help/ar_management')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$id,'needdecode'=>'yes'])
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">    
    <!-- Tab Starts  -->
    <?php $activetab = 'payments_list'; 
        	$routex = explode('.',Route::currentRouteName());
    ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom js-dynamic-tab-menu">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'financials') active @endif"><a href="{{ url('patients/'.$id.'/armanagement/arsummary') }}" ><i class="fa fa-navicon i-font-tabs"></i> Su<span class="text-underline">m</span>mary</a></li>           	                      	           
            <li class="@if($activetab == 'payments_list') active @endif"><a href="{{ url('patients/'.$id.'/armanagement/list') }}" id="ar_href"><span id="claimdetlink_main0" class="js_claimdetlink"><i class=" fa fa-navicon i-font-tabs"></i> List<span></a></li>   
            <li class="@if($activetab == 'workorder') active @endif hide"><a href="" ><i class="fa fa-navicon i-font-tabs"></i> Work Order</a></li>   
            
        </ul>
    </div>
    <!-- Tab Ends -->

    <div id="js_artable_listing">
        @include('patients/armanagement/claimslist')
    </div>
</div>
<input type="hidden" class="js_selected_claim_ids_arr" id="selected_claim_ids_arr" />
<input type="hidden" class="js_curr_claim_id" id="selected_curr_claim_id" />
<input type="hidden" class="js_ar_max_claim_seleted" id="js_ar_max_claim_seleted" value="{{Config::get('siteconfigs.ar_max_claim_seleted')}}" />
<input type="hidden" name="_token" value="{{ csrf_token() }}" />

<!-- Modal PAyment details starts here -->
<div id="claim_notes_all" class="modal fade in">
    <div class="modal-md-650">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Notes</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.notes") }}' />
                    {!! Form::open(['onsubmit'=>"event.preventDefault();",'class'=>'popupmedcubicsform']) !!}
                    <div class="box-body form-horizontal">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group-billing">                                
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10" id="js-claim-notes-all-div">                                    
                                    {!! Form::textarea('claim_notes_all_txt',null,['class'=>'form-control ar-notes-minheight js_claim_notes_all_txt','placeholder'=>'Type your Notes']) !!}
                                    <span id="claim_notes_all_err" class="hide"><small class="help-block">Please enter note</small></span>
                                </div>                                
                            </div>
                        </div>                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5">       
                            {!! Form::submit("Submit", ['class'=>'js_claim_notes_all_btn btn btn-medcubics-small margin-t-m-5 pull-right']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->

@include ('patients/problemlist/commonproblemlist') 

<!-- Modal PAyment details starts here  -->
<div id="claim_status_notes_form" class="modal fade in">
    <div class="modal-md-650">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Change Status</h4>
            </div>
            <div class="modal-body no-padding" >
                {!! Form::open(['method'=>'POST','name'=>'claim_status_chage_form','id'=>'bootstrap-validator-claim-status-chage-form','class'=>'popupmedcubicsform js-avoid-savepopup']) !!}
                <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.followup") }}' />

                <div id="js_status_notes_part2" class="box-body form-horizontal p-b-0 js-followup-insurance">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-5">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> General</span>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive margin-b-10">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">                                    
                                <div class="form-group">
                                    {!! Form::label('followup_rep_name_label', 'Rep Name', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2">     
                                        {!! Form::text('followup_rep_name', null,['class'=>'form-control input-sm-header-billing']) !!}
                                    </div>                                
                                </div>
                                <div class="form-group">
                                    {!! Form::label('followup_dos_label', 'Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2">
                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('followup_dos')"></i> 
                                        {!! Form::text('followup_dos', null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'dm-date form-control input-sm-header-billing js_claim_status_popup_fields']) !!}
                                    </div>                                  
                                </div>
								
								<div class="form-group">
                                    {!! Form::label('check_box_claim_note', 'Convert Note', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
									 {!! Form::checkbox('check_box_claim_note', true, true, ["class" => "flat_red",'style'=> 'position:initial !important;background: #ffffff !important;']) !!}
                                    </div>                                  
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('followup_phone_label', 'Phone', ['class'=>'col-lg-3 col-md-3 col-sm-2 col-xs-12 control-label-billing med-green font600','style'=>'padding-right: 0px; padding-left: 20px;']) !!} 
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10 billing-select2" style="padding-left: 0px; padding-right: 0px;">       
                                        {!! Form::text('followup_phone', null,['class'=>'dm-phone form-control input-sm-header-billing']) !!}
                                    </div>
                                    {!! Form::label('followup_phone_ext_label', 'Ext', ['class'=>'col-lg-2 col-md-2 col-sm-1 col-xs-12 control-label-billing med-green font600','style'=>'padding-right: 0px; padding-left: 15px;']) !!} 
                                    <div style="padding-left: 0px; padding-right: 10px;" class="col-lg-3 col-md-3 col-sm-3 col-xs-10 billing-select2">       
                                        {!! Form::text('followup_phone_ext', null,['class'=>'dm-phone-ext form-control input-sm-header-billing']) !!}
                                    </div>
                                </div>  
                                
                                <div class="form-group">
                                    {!! Form::label('assigned_to', 'Insurance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 star','style'=>'padding-right: 0px; padding-left: 20px;']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">       
                                        {!! Form::select('insurance', [''=>'-- Select To --']+(array)$patient_insurance,null,['class'=>'select2 form-control js_indivual_insurance','id'=>'js_indivual_insurance_'.@$claim_detail_val->claim_number]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive  no-padding margin-t-m-5">
                    <div id="js_status_notes_part1" class="box-body table-responsive  no-padding margin-t-m-5">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
                            <div class="form-group">
                                @foreach($category as $keys=>$list)
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                    {!! Form::radio('claim_status_radio', $list->label_name,null,['class'=>'','id'=>'f'.$keys]) !!}
                                    <label class="font600 med-darkgray form-cursor" for="f{{$keys}}"> {{ $list->name }}</label>
                                </div>
                                @endforeach
                                <div class="show_manual_error help-block" style="display:none;font-size: 85%;">Select Category</div>                                    
                            </div>
                            @foreach($question as $qlist)
                            <div class="{{ $qlist->label_name }} followup-box">
                                <div class="box-body form-horizontal no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> {{ $qlist->name }}</span>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            <?php $count = 1; ?>
                                            @foreach($qlist->question as $qsublist)
                                            @if($qsublist->field_type == 'date')
                                            @if($qsublist->date_type == 'double_date')
                                            <div class="form-group-billing {{ $qlist->name }}">
                                                {!! Form::label($qsublist->question.'_label', $count .".". $qsublist->question, ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green ']) !!}
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('claim_nis_effective_date_from')"></i>
                                                    {!! Form::text($qlist->label_name.'~~'.$qsublist->question_label.'~~'.@$qsublist->hint.'~~'.' from',null,['class'=>'dm-date form-control input-sm-header-billing js_claim_status_popup_future_date common_error_validate','placeholder'=>'From','data-field-type' =>($qsublist->field_type != "")?"":""]) !!}
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                                    {!! Form::text($qlist->label_name.'~~'.$qsublist->question_label.'~~'.@$qsublist->hint.'~~'.'end', null,['class'=>'dm-date form-control input-sm-header-billing js_claim_status_popup_future_date common_error_validate','placeholder'=>'To','data-field-type' =>($qsublist->field_type != "")?"":""]) !!}
                                                </div>
                                            </div>
                                            @elseif($qsublist->date_type == 'single_date')
                                            <div class="form-group-billing">
                                                {!! Form::label($qsublist->question.'_label', $count .".". $qsublist->question, ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green ']) !!}
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('claim_inprocess_callback_date')"></i>
                                                    {!! Form::text($qlist->label_name.'~~'.$qsublist->question_label.'~~'.@$qsublist->hint, null,['class'=>'dm-date form-control input-sm-header-billing js_claim_status_popup_future_date common_error_validate','data-field-type' =>($qsublist->field_type != "")?"":""]) !!}
                                                </div>
                                            </div>
                                            @endif
                                            @elseif($qsublist->field_type == 'text')
                                            <div class="form-group-billing">
                                                {!! Form::label($qsublist->question.'_label', $count .".". $qsublist->question, ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green ']) !!}
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <?php if($qsublist->field_validation == 'number'){ ?>
                                                    {!! Form::text($qlist->label_name.'~~'.$qsublist->question_label.'~~'.@$qsublist->hint, null,['class'=>'common_error_validate form-control input-sm-header-billing','data-field-type'=>$qsublist->field_validation]) !!}
                                                        <?php }else{ ?>
                                                    {!! Form::text($qlist->label_name.'~~'.$qsublist->question_label.'~~'.@$qsublist->hint, null,(trim($qsublist->hint) == "Check no")? ['class'=>'common_error_validate form-control input-sm-header-billing js-all-caps-letter-format'] :['class'=>'common_error_validate form-control input-sm-header-billing'] +['data-field-type'=>$qsublist->field_validation]) !!}
                                                        <?php } ?>
                                                </div>
                                            </div>
                                            @elseif($qsublist->field_type == 'number')
                                            <div class="form-group-billing">
                                                {!! Form::label($qsublist->question.'_label', $count .".". $qsublist->question, ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green ']) !!}
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    {!! Form::text($qlist->label_name.'~~'.$qsublist->question_label.'~~'.@$qsublist->hint, null,['class'=>'common_error_validate form-control input-sm-header-billing  ','data-field-type'=>$qsublist->field_validation]) !!}
                                                </div>
                                            </div>
                                            @endif
                                                <?php $count++; ?>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div><!-- Notes box-body Ends-->
                    <div id="js-claim-status_submitbtn-footer" class="box-header-view-white ar-bottom-border text-center">
                        {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js-claim-status_submit-btn','style'=>'padding:2px 16px;']) !!}
                        <button class="btn btn-medcubics-small" data-dismiss="modal" type="button">Cancel</button>
                    </div><!-- /.box-header -->
                    <div id="js_arnotes_edit_part" class="padding-0-4 form-group hide" style="padding-top: 10px;">
                        <textarea name="areditor1" id="areditor1" class="form-control">                            
                        </textarea>
						
                    </div>
                    <div id="js-claim-status_final-submitbtn-footer" class="hide box-header-view-white ar-bottom-border text-center">
                        <button id="js_claim_final_save_btn" class="btn btn-medcubics-small" type="button">Save</button>
                        <button id="js_claim_reedit_back_btn" class="btn btn-medcubics-small" type="button">Back</button>
                    </div><!-- /.box-header -->

                    <div id="js-claim-status_submitbtn-load-footer" class="hide box-header-view-white ar-bottom-border text-center">  
                        <i class="fa fa-spinner fa-spin"></i>
                    </div><!-- /.box-header -->

                </div><!-- /.box-body --> 
                {!! Form::close() !!}
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->

<div id="claim-charge-modal-popup" class="modal fade in">
    <div class="modal-md-800">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Edit Charge</h4>
            </div>
            <div class="modal-body no-padding"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="js-model-popup-payment" class="modal fade in">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Claim No : CHR401</h4>
            </div>
            <div class="modal-body no-padding" ></div>
        </div>
    </div>
</div>

@include ('patients/billing/model-inc')
<!--End-->
@stop