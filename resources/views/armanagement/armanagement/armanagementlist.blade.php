@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-laptop"></i> AR Management <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>@if(Request::segment(2) != 'myfollowup') List @else Followup List @endif</span></small>
        </h1>
        <ol class="breadcrumb">
            @include('layouts.practice_module_stream_export', ['url' => 'api/arManagementListExport'])
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
@if(Request::segment(2) != 'myfollowup')
<?php $module     = "armanagement";     
        ?>
 @include ('armanagement/armanagement/tabs')
@endif
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->
   <div class="med-tab nav-tabs-custom  no-bottom js-dynamic-tab-menu">
        <ul class="nav nav-tabs">
			<!--<li class=""><a href="{{ url('armanagement/armanagement') }}" ><i class="fa fa-cog i-font-tabs"></i> Summary</a></li>-->
            <li class="active"><a href="{{ url('armanagement/armanagementlist') }}" id="ar_href" ><i class="fa fa-bars i-font-tabs"></i><span id="claimdetlink_main0" class="js_claimdetlink"> List</span></a></li>             
        </ul>
    </div>
    <!-- Tab Ends -->
    <div id="js_artable_listing">
        @include('armanagement/armanagement/claimslist')
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
                <h4 class="modal-title">Claim Notes</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                    {!! Form::open(['onsubmit'=>"event.preventDefault();",'class'=>'popupmedcubicsform']) !!}
                    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.armanagement.notes") }}' />
                    <div class="box-body form-horizontal">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group-billing app">                                
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10" id="js-claim-notes-all-div">                                    
                                    {!! Form::textarea('claim_notes_all_txt',null,['class'=>'form-control ar-notes-minheight js_claim_notes_all_txt','placeholder'=>'Type your Notes']) !!}
                                     <button id="start-record-btn" title="Start Recording" class="margin-t-5 record-button">Start <i class="fa fa-microphone text-success"></i></button>
                                    <button id="pause-record-btn" title="Pause Recording" class="margin-t-5 record-button">Stop <i class="fa fa-microphone-slash text-danger"></i></button>
                                    <!--<button id="save-note-btn" title="Save Note">Save Note</button>  --> 
                                    <p id="recording-instructions">Press the <strong>Start Recognition</strong> button and allow access.</p>
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

<!-- Modal PAyment details starts here -->
<div id="claim_status_notes_form" class="modal fade in">
    <div class="modal-md-650">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Change Status</h4>
            </div>
            <div class="modal-body no-padding" >
                {!! Form::open(['method'=>'POST','name'=>'claim_status_chage_form','id'=>'bootstrap-validator-claim-status-chage-form','class'=>'popupmedcubicsform js-avoid-savepopup']) !!}
                <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.armanagement.followup") }}' />
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
                                        {!! Form::text('followup_dos', null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'dm-date form-control input-sm-header-billing js_claim_status_popup_fields','autocomplete' => 'off']) !!}
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
                                    {!! Form::label('assigned', 'Insurance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 star','style'=>'padding-right: 0px; padding-left: 20px;']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">                                        
                                        {!! Form::select('insurance', [''=>'-- Select To--']+(array)@$patient_insurance,null,['class'=>'select2 form-control  js_indivual_insurance','id'=>'js_indivual_insurance_'.@$claim_detail_val->claim_number]) !!}
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
                                                        {!! Form::text($qlist->label_name.'~~'.$qsublist->question_label.'~~'.@$qsublist->hint, null,['class'=>'common_error_validate form-control input-sm-header-billing ','data-field-type'=>$qsublist->field_validation]) !!}
                                                            <?php }else{ ?>
                                                        {!! Form::text($qlist->label_name.'~~'.$qsublist->question_label.'~~'.@$qsublist->hint, null,[ 'data-field-type'=>$qsublist->field_validation, 'class'=> trim($qsublist->hint) == "Check no" ? "common_error_validate form-control input-sm-header-billing js-all-caps-letter-format" : "common_error_validate form-control input-sm-header-billing "]) !!}
                                                            <?php } ?>
                                                    </div>
                                                </div>
                                            @elseif($qsublist->field_type == 'number')
                                                <div class="form-group-billing">
                                                    {!! Form::label($qsublist->question.'_label', $count .".". $qsublist->question, ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green ']) !!}
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                        {!! Form::text($qlist->label_name.'~~'.$qsublist->question_label.'~~'.@$qsublist->hint, null,['class'=>'form-control input-sm-header-billing common_error_validate ','data-field-type'=>$qsublist->field_validation]) !!}
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Edit Charge</h4>
            </div>
            <div class="modal-body no-padding">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="js-model-popup-payment" class="modal fade in">
    <div class="modal-md-800">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Claim No : CHR401</h4>
            </div>
            <div class="modal-body no-padding" >
            </div>
        </div>
    </div>
</div>

<!-- Showing Followup history in the popup using ajax -->
<div id="show_followup_history" class="modal fade in">
	<div class="modal-md-650">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"> Claim No : CHR401</h4>
			</div>
			<div class="modal-body no-padding" >
			</div>
		</div>
	</div>
</div>

@include ('patients/billing/model-inc')
<!--End-->
@stop

<!-- Server script start -->
@push('view.scripts') 
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script type="text/javascript">
	var api_site_url = '{{url("/")}}'; 
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/armanagement/claimsList";
	
    /* Search function start */
	var column_length = $('#search_table_claims thead th').length;         

	function accessAll() {                      
		var selected_column = ['DOS','Claim No','Acc No','Patient','Provider','Facility','Billed To','Charge Amt','Paid','AR Due','Status'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
		claimSearch(allcolumns); /* Trigger datatable */
	}  


	var dataArr = {};	
	var wto = '';
	
	/* function for get data for fields Start */
	function getData(){
		clearTimeout(wto);
		var data_arr = {};
		wto = setTimeout(function() {  
			 $('select.auto-generate').each(function(){
				 data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
			 });																				// Getting all data in select fields 
			 $('input.auto-generate:visible').each(function(){
				data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
			 });																				// Getting all data in input fields
			 dataArr = {data:data_arr};
			 accessAll();																		// Calling data table server side scripting
		}, 100);
	}
	/* function for get data for fields End */

    function claimSearch(allcolumns) {
        $("#search_table_claims").DataTable({          
            "createdRow":   function ( row, data, index ) {
                                if(data[1] != undefined)
                                    data[1] = data[1].replace(/[\-,]/g, '');
                            },      
            "bDestroy"  :   true,
            "paging"    :   true,
			"searching"	: 	false,
            "info"      :   true,
			//"processing": true,
            //"aoColumns"   :   allcolumns,
            "columnDefs":   [ { orderable: false, targets: [0,13,14] } ],
            "autoWidth" :   false,
            "lengthChange"      : false,
            //"searchHighlight" : true,
            "searchDelay": 450,
            "serverSide": true, 
            "order": [[1,"desc"],[2,"desc"]],
            "ajax": $.fn.dataTable.pipeline({
                url: listing_page_ajax_url, 
				data:{'dataArr':dataArr},
                pages: 1, // number of pages to cache
                success: function(){
                        // Hide loader once content get loaded.
                }   
            }),
            "columns": [
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },                
				{"datas": "id", sDefaultContent: "" },
            ],  
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) { 
                $(".ajax_table_list").html(aData+"</tr>");
                var get_orig_html = $(".ajax_table_list tr").html();
                var get_attr = $(".ajax_table_list tr").attr("data-url");
                var get_class = $(".ajax_table_list tr").attr("class");
                $(nRow).addClass(get_class).attr('data-url', get_attr);
                $(nRow).closest('tr').html(get_orig_html);
                $(".ajax_table_list").html("");             
            },
            "fnDrawCallback": function(settings) {
                $('#js-select-all').prop('checked',false); // uncheck select all option while paginating
                hideLoadingImage(); // Hide loader once content get loaded.
				/* Armanagement dynamic checkbox selection  */
				/* Revision 1 : MR-2716 : 22 Aug 2019 : Selva */
				var ClaimIds = $('input[name="encodeClaim"]').val();
				var type = $('select[name="js-select-option"]').val();
				if(ClaimIds != 'undefined' && type == 'all'){
					 $('.js-select-all-sub-checkbox').prop('checked',true);
				}else if(type == 'none'){
					$('.js-select-all-sub-checkbox').prop('checked',false);
				}else if(type == 'page'){
					 $('select[name="js-select-option"]').val('none');
				}
            }
        });
    } 

    $(document).ready(function(){
        $(".selClaimStatus").trigger("change");
    });
		
	/* Charge analysis report hold fields start  */
	$(document).on("change", ".selClaimStatus", function(){
		var isHold = 0;
		$("select.selClaimStatus option:selected").each(function () {
			if($(this).text() == 'Hold')
			isHold = 1;
		});

		if(isHold) {    
			$(".selholdBlk").prop("disabled", false);
		} else {        
			$(".selholdBlk").select2('val', '').val("").prop("disabled", true); // Clear already selected hold reason and release date.
		}
	});
	/* Charge analysis report hold fields end  */
</script>   
<script>
$(document).ready(function() {
setTimeout(function(){   
     $(".js-submit-popupform-notes li.select2-search-choice div").attr("style",'text-overflow: clip');  
     $(".js-submit-popupform-notes li.select2-search-choice div").attr("style",'overflow:visible');
  }, 100);  
});

try {
  var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  var recognition = new SpeechRecognition();
}
catch(e) {
  console.error(e);
  $('.no-browser-support').show();
  $('.app').hide();
}


var noteTextarea = $('.js_claim_notes_all_txt');
var instructions = $('#recording-instructions');
//var notesList = $('ul#notes');

var noteContent = '';

// Get all notes from previous sessions and display them.
var notes = getAllNotes();
renderNotes(notes);



/*-----------------------------
      Voice Recognition 
------------------------------*/

// If false, the recording will stop after a few seconds of silence.
// When true, the silence period is longer (about 15 seconds),
// allowing us to keep recording even when the user pauses. 
recognition.continuous = true;

// This block is called every time the Speech APi captures a line. 
recognition.onresult = function(event) {

  // event is a SpeechRecognitionEvent object.
  // It holds all the lines we have captured so far. 
  // We only need the current one.
  var current = event.resultIndex;

  // Get a transcript of what was said.
  var transcript = event.results[current][0].transcript;

  // Add the current transcript to the contents of our Note.
  // There is a weird bug on mobile, where everything is repeated twice.
  // There is no official solution so far so we have to handle an edge case.
  var mobileRepeatBug = (current == 1 && transcript == event.results[0][0].transcript);

  if(!mobileRepeatBug) {
    noteContent += transcript;
    noteTextarea.val( noteContent.charAt(0).toUpperCase() + noteContent.slice(1));
    $("#js-bootstrap-validator").bootstrapValidator('revalidateField', 'content');
  }
};

recognition.onstart = function() { 
  instructions.text('Converting your voice to text.');
}

recognition.onspeechend = function() {
  instructions.text('You were quiet for a while so voice recognition turned itself off.');
}

recognition.onerror = function(event) {
  if(event.error == 'no-speech') {
    instructions.text('No voice detected. Try again.');  
  };
}


$('#pause-record-btn').hide();
/*-----------------------------
      App buttons and input 
------------------------------*/

$('#start-record-btn').on('click', function(e) {
  if (noteContent.length) {
    noteContent += ' ';
  }
  recognition.start();
  instructions.text('Voice recognition activated. Try speaking into the microphone.');
  $('#pause-record-btn').show();
  $(this).hide();
});


$('#pause-record-btn').on('click', function(e) {
  recognition.stop();
  instructions.text('Voice recognition paused.');
  $('#start-record-btn').show();
  $(this).hide();
});

// Sync the text inside the text area with the noteContent variable.
noteTextarea.on('input', function() {
  noteContent = $(this).val();
})

/*-----------------------------
      Speech Synthesis 
------------------------------*/

function readOutLoud(message) {
    var speech = new SpeechSynthesisUtterance();

  // Set the text and voice attributes.
    speech.text = message;
    speech.volume = 1;
    speech.rate = 1;
    speech.pitch = 1;
  
    window.speechSynthesis.speak(speech);
}



/*-----------------------------
      Helper Functions 
------------------------------*/

function renderNotes(notes) {
  var html = '';
  if(notes.length) {
    notes.forEach(function(note) {
      html+= `<li class="note">
        <p class="header">
          <span class="date">${note.date}</span>
          <a href="#" class="listen-note" title="Listen to Note">Listen to Note</a>
          <a href="#" class="delete-note" title="Delete">Delete</a>
        </p>
        <p class="content">${note.content}</p>
      </li>`;    
    });
  }
  else {
    html = '<li><p class="content">You don\'t have any notes yet.</p></li>';
  }
  //notesList.html(html);
}


function saveNote(dateTime, content) {
  localStorage.setItem('note-' + dateTime, content);
}


function getAllNotes() {
  var notes = [];
  var key;
  for (var i = 0; i < localStorage.length; i++) {
    key = localStorage.key(i);

    if(key.substring(0,5) == 'note-') {
      notes.push({
        date: key.replace('note-',''),
        content: localStorage.getItem(localStorage.key(i))
      });
    } 
  }
  return notes;
}


function deleteNote(dateTime) {
  localStorage.removeItem('note-' + dateTime); 
}


</script> 
@endpush
<!-- Server script end -->