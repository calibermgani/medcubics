@extends('admin')
@section('toolbar')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/financials/list') }}">Billing Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Charge Analysis - Detailed</span></small>
        </h1>
        <ol class="breadcrumb">
            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
                @include('layouts.practice_module_stream_export', ['url' => 'reports/charges/export/'])
				<input type="hidden" name="report_controller_name" value="ReportController" />
				<input type="hidden" name="report_controller_func" value="chargesearchexport" />
				<input type="hidden" name="report_name" value="Charge Analysis Detailed" />
            <li><a href="{{ url('reports/financials/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/charge_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow no-bottom">        
            <div class="box-body yes-border border-radius-4 border-green">
                <!-- SEARCH FIELDS FILE -->
                {!! Form::open(['data-url'=>'reports/search/charges','url'=>'reports/search/charges','onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform']) !!}
                @include('layouts.search_fields', ['search_fields'=>$search_fields])
                <div class="col-lg-11 col-md-11 col-sm-12 col-xs-8 no-padding margin-l-10">                                            
                    <input class="btn generate-btn js_filter_search_submit pull-left m-r-m-3" value="Generate Report" type="submit">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<div class="js_spin_image hide">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center">
        <i class="fa fa-spinner fa-spin med-green font20"></i> Processing
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_exit_part text-center hide">
    <input class="btn btn-medcubics-small" id="js_exit_part" value="Exit" type="button">
</div>
<div id="export_pdf_div"></div>

@stop
@push('view.scripts') 

{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}
{!! HTML::script('js/xlsx.core.min.js') !!}
<script lang="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
<script type="text/javascript">
    var api_site_url = '{{url('/')}}';   
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/reports/search/charges"; 
	var wto = '';
	var url = $('#js-bootstrap-searchvalidator').attr("action");

	//--------------------------------- FORM SUBMIT ----------------------

	$(".js_filter_search_submit").on("click",function(){
		getAjaxResponse(listing_page_ajax_url, $("#js-bootstrap-searchvalidator").serialize());
	});
	
	$(document).ready(function(){
		getMoreFieldData();
		$(".selholdBlk").prop("disabled", true);
		$(".selClaimStatus").trigger("change");
		$('#select_date_of_service').parent().parent().hide();
		$('[id^=insurance_id]').hide();
		<?php 
	    if(!empty($searchUserData))
	        foreach(json_decode($searchUserData->search_fields_data,true) as $res){
	            if($res['label_name']=='insurance_charge' && $res['value']=='insurance'){?>
	                    $("[id^=insurance_id]").show();
	                    <?php
	            }
	        }
	    ?>
	});



	/* Onchange code for more field Start */
	$(document).on('change','select.more_generate',function(){ 
		getMoreFieldData();
	});
	/* Onchange code for more field End */ 


	$("#choose_date.js_select_basis_change").on("click",function(){
		if($(this).val()=='transaction_date'){
			$('#select_transaction_date').parent().parent().show();
			$('#select_date_of_service').parent().parent().hide();
		} else if($(this).val()=='DOS'){
			$('#select_date_of_service').parent().parent().show();
            $('#select_transaction_date').parent().parent().hide();
		} else{
			$('#select_transaction_date').parent().parent().show();
			$('#select_date_of_service').parent().parent().show();
		}
	});
	$("#insurance_charge.js_select_basis_change").on("click",function(){
		//$("#insurance_id").hide();
		if($(this).val()=='insurance'){
		   $('[id^=insurance_id]').show();   
		}
		else{
		   $('[id^=insurance_id]').hide();  
		}
	});
    
    $(document).on('click','.js_search_export_raw', function(){ 
        // if(typeof require !== 'undefined') XLSX = require('xlsx');

        var data_arr = [];
            $('select.auto-generate:visible').each(function(){
                //  data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';
                if($(this).select2('val').length > 0) {
                    var avoid ="[]"
                    data_arr.push({
                        name : $(this).attr('name').replace(avoid, ''), 
                        value:  $(this).select2('val')
                    });
                }
             });       
             $('input.auto-generate:visible').each(function(){
                // data_arr += $(this).attr('name')+'='+$(this).val()+'&';
                if($(this).val().length > 0) { 
                    var avoid ="[]"
                    data_arr.push({
                        name : $(this).attr('name'), 
                        value:  $(this).val()
                    });
                }
             });
             data_arr.push({
                    name : "_token", 
                    value:  $('input[name=_token]').val()
                });
                data_arr.push({
                    name : "controller_name", 
                    value:  "ReportController"
                });
                data_arr.push({
                    name : "function_name", 
                    value:  "chargesearchexport"
                });
                data_arr.push({
                    name : "export", 
                    value:  "xlsx"
                });
                // console.log(data_arr);
             var baseurl = '{{url('/')}}';
            var url = baseurl+"/reports/streamcsv/export/charge-analysis-detailed-js";    
            processingImageShow(".box-view","show");
            $.ajax({
                type: 'POST',
                url: url,
                data:  data_arr,
                success: function(response) {
                    var myObject = JSON.parse( response );
                    // console.log(myObject);
                    var data = myObject.original.value;
                    // console.log(data);
                    // $("#export_csv_div").html(myObject.original.html);
                    // console.log(data);

    var user = "<?php $user = Auth::user()->name; echo $user; ?>";
    var practice_name = "<?php $heading_name = App\Models\Practice::getPracticeDetails(); echo $heading_name['practice_name']; ?>"
    // console.log(user);

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = mm + '/' + dd + '/' + yyyy;

 
        // wb.Props = {
        //         Title: "SheetJS Tutorial",
        //         Subject: "Test",
        //         Author: "Red Stapler",
        //         CreatedDate: new Date(2017,12,19),
        //     };
        
        // wb.SheetNames.push("Test Sheet");
        // console.log(wb.Sheets);
    //     wb.Sheets.push(
    //         Sheet1: {
    //   "!ref":"A1:C1",
    //   A1: { t:"n", v:10000 },                    // <-- General format
    //   B1: { t:"n", v:10000, z: "0%" },           // <-- Builtin format
    //   C1: { t:"n", v:10000, z: "\"T\"\ #0.00" }  // <-- Custom format
    // }
        // )
        var claims = data.claims;
        var user_full_names = data.user_full_names;
        var include_cpt_option = data.include_cpt_option;
        var tot_summary = data.tot_summary;

        var ws_data = [
        [practice_name],
        ['Charge Analysis - Detailed'],
        ['User : '+user+' | '+today+' '],
        [],
        ['DOS', 'Claim No', 'Acc No', 'Patient', 'Billing', 'Rendering', 'Facility', 'POS', 'Responsibility', 'Insurance Type', 'CPT', ...( jQuery.inArray('include_cpt_description', include_cpt_option)  !== -1) ? ['CPT Description'] : [], ...(jQuery.inArray('include_modifiers', include_cpt_option) !== -1) ? ['Modifiers'] : [], ...(jQuery.inArray('include_icd', include_cpt_option) !== -1) ? ['ICD-10'] : [],'Units', 'Charges($)', 'Paid($)', 'Total Balance($)', 'Status', 'Hold Reason','Entry Date', 'Reference', 'First Submission', 'Last Submission','User']
        ];
        // var form_data = $("#js-bootstrap-searchvalidator").serialize();
        // console.log(form_data);

        // if( jQuery.inArray('include_cpt_description', include_cpt_option)  !== -1)
        //     { ws_data[4].splice(11, 0, 'CPT Description');}
        // if(jQuery.inArray('include_modifiers', include_cpt_option) !== -1)
        //     { ws_data[4].splice(12, 0, 'Modifiers');}
        // if(jQuery.inArray('include_icd', include_cpt_option) !== -1)
        //    {  ws_data[4].splice(13, 0, 'ICD-10');}         
        //    ws_data.join();
        var colspan = 20;
        if (jQuery.inArray('include_icd', include_cpt_option) !== -1) {
            colspan += 1; }
        if (jQuery.inArray('include_cpt_description', include_cpt_option) !== -1) {
            colspan += 1; }
        if (jQuery.inArray('include_modifiers', include_cpt_option) !== -1) {
            colspan += 1; }

        if(claims.length > 0) {
            var count = 0;  var total_amt_bal = 0; var count_cpt = 0; var claim_billed_total = 0; var claim_paid_total = 0; 
            var claim_bal_total = 0; var total_claim = 0; var total_cpt =  0; var claim_units_total = 0;  var claim_cpt_total = 0;

            $.each( claims, function( i, claims_list ) {
                var set_title = (claims_list.title)? claims_list.title+". ":'';
                var patient_name = set_title+claims_list.last_name +', '+ claims_list.first_name +' '+ claims_list.middle_name;

                dos = cpt = cpt_description = modifier1 = modifier2 = modifier3 = modifier4 = icd_10 = '';
                charges = paid = total_bal = 0;
                var units = 0;

                if( claims_list.claim_dos_list !== '' && claims_list.claim_dos_list !== null && claims_list.claim_dos_list !== undefined) {
                                var claim_line_item = claims_list.claim_dos_list.split('^^');      //explode("^^", $claims_list->claim_dos_list);
                                $.each( claim_line_item, function( i, claim_line_item_val ) {
                                // foreach($claim_line_item as $claim_line_item_val){
                                    if(claim_line_item_val != ''){
                                        var line_item_list =  claim_line_item_val.split('$$');          //explode("$$", $claim_line_item_val);
                                        var claim_cpt = line_item_list[0];
                                        if((line_item_list[0]) != ''){
                                            var dos       = line_item_list[1];
                                            var cpt       = line_item_list[2];
                                            var cpt_description = line_item_list[3];
                                            var modifier1 = line_item_list[4];
                                            var modifier2 = line_item_list[5];
                                            var modifier3 = line_item_list[6];
                                            var modifier4 = line_item_list[7];
                                            var icd_10    = line_item_list[8];
                                            var units     = line_item_list[9]; 
                                            var charges   = (line_item_list[10] !== undefined) ? line_item_list[10] : 0.00;
                                            var paid      = (line_item_list[11] !== undefined) ? line_item_list[11] : 0.00;
                                            var total_bal = (line_item_list[12] !== undefined) ? line_item_list[12] : 0.00;                                                
                                        } 
                                    }

                                    var wa_data_array = [];
                                    var avoid = '""';

                                    wa_data_array.push(dos);
                                    wa_data_array.push(claims_list.claim_number);
                                    wa_data_array.push(claims_list.account_no);
                                    wa_data_array.push(patient_name);
                                    wa_data_array.push(claims_list.billProvider_name);
                                    wa_data_array.push(claims_list.rendProvider_name);
                                    wa_data_array.push(claims_list.facility_name);
                                    if(claims_list.code !== null && claims_list.code != "" && claims_list.pos !== null && claims_list.pos != "") {
                                    wa_data_array.push(claims_list.code+ "-" +claims_list.pos);
                                    } else {
                                        wa_data_array.push("-Nil-");
                                    }
                                    if(claims_list.self_pay=="Yes") {
                                        wa_data_array.push('Self');
                                    } else {
                                        wa_data_array.push(claims_list.insurance_name);
                                    }
                                    if(claims_list.type_name) 
                                    { wa_data_array.push(claims_list.type_name); } else { wa_data_array.push(""); }
                                    wa_data_array.push(cpt);
                                    if(jQuery.inArray('include_cpt_description',include_cpt_option) !== -1)                                            
                                    { wa_data_array.push(cpt_description); } 
                                    if(jQuery.inArray('include_modifiers',include_cpt_option) !== -1) {
                                    var modifier_arr = [];
                                    if (modifier1 != '')
                                        modifier_arr.push(modifier1);
                                    if (modifier2 != '')
                                        modifier_arr.push(modifier2);
                                    if (modifier3 != '')
                                        modifier_arr.push(modifier3);
                                    if (modifier4 != '')
                                        modifier_arr.push(modifier4);
                                    if (modifier_arr.length > 0) {
                                        var modifier_val = modifier_arr.join(',');
                                    } else {
                                        var modifier_val = '-Nil-';
                                    }
                                    wa_data_array.push(modifier_val); }

                                    if(jQuery.inArray('include_icd',include_cpt_option) !== -1){
                                        wa_data_array.push(icd_10);
                                    }
                                    wa_data_array.push(units);
                                    wa_data_array.push(parseInt(charges));
                                    // console.log(parseInt(charges));
                                    wa_data_array.push(parseInt(paid));
                                    wa_data_array.push(parseInt(total_bal));
                                    wa_data_array.push(claims_list.status);
                                    wa_data_array.push((claims_list.option_reason != '') ? claims_list.option_reason : '-Nil-');
                                    if(claims_list.entry_date != "0000-00-00" && claims_list.entry_date != "1970-01-01" )
                                    { wa_data_array.push(claims_list.entry_date); }
                                    wa_data_array.push(claims_list.claim_reference);
                                    wa_data_array.push(claims_list.submited_date);
                                    wa_data_array.push(claims_list.last_submited_date);
                                    if(claims_list.created_by != 0 && user_full_names[claims_list.created_by] !== null && user_full_names[claims_list.created_by] !== undefined ) {
                                    wa_data_array.push(user_full_names[claims_list.created_by]);
                                    } else {
                                        wa_data_array.push("");
                                    }

                                    ws_data.push(wa_data_array);

                                }); 
                            } else {
                                        var dos       = "-Nil-";
                                        var cpt       = "-Nil-";
                                        var cpt_description = "-Nil-";
                                        var modifier1 = "-Nil-";
                                        var modifier2 = "-Nil-";
                                        var modifier3 = "-Nil-";
                                        var modifier4 = "-Nil-";
                                        var icd_10    = "-Nil-";
                                        var units     = "-Nil-" ;
                                        var charges   = 0.00;
                                        var paid      = 0.00;
                                        var total_bal = 0.00;

                                    var wa_data_array = [];
                                    var avoid = '""';

                                    wa_data_array.push(dos);
                                    wa_data_array.push(claims_list.claim_number);
                                    wa_data_array.push(claims_list.account_no);
                                    wa_data_array.push(patient_name);
                                    wa_data_array.push(claims_list.billProvider_name);
                                    wa_data_array.push(claims_list.rendProvider_name);
                                    wa_data_array.push(claims_list.facility_name);
                                    wa_data_array.push(claims_list.code+ "-" +claims_list.pos);
                                    if(claims_list.self_pay=="Yes") {
                                        wa_data_array.push('Self');
                                    } else {
                                        wa_data_array.push(claims_list.insurance_name);
                                    }
                                    if(claims_list.type_name) 
                                    { wa_data_array.push(claims_list.type_name); } else { wa_data_array.push(""); }
                                    wa_data_array.push(cpt);
                                    if(jQuery.inArray('include_cpt_description',include_cpt_option) !== -1)                                            
                                    { wa_data_array.push(cpt_description); } 
                                    if(jQuery.inArray('include_modifiers',include_cpt_option) !== -1) {
                                    var modifier_arr = [];
                                    if (modifier1 != '')
                                        modifier_arr.push(modifier1);
                                    if (modifier2 != '')
                                        modifier_arr.push(modifier2);
                                    if (modifier3 != '')
                                        modifier_arr.push(modifier3);
                                    if (modifier4 != '')
                                        modifier_arr.push(modifier4);
                                    if (modifier_arr.length > 0) {
                                        var modifier_val = modifier_arr.join(',');
                                    } else {
                                        var modifier_val = '-Nil-';
                                    }
                                    wa_data_array.push(modifier_val); }

                                    if(jQuery.inArray('include_icd',include_cpt_option) !== -1){
                                        wa_data_array.push(icd_10);
                                    }
                                    wa_data_array.push(units);
                                    wa_data_array.push(parseInt(charges));
                                    // console.log(parseInt(charges));
                                    wa_data_array.push(parseInt(paid));
                                    wa_data_array.push(parseInt(total_bal));
                                    wa_data_array.push(claims_list.status);
                                    wa_data_array.push((claims_list.option_reason != '')? claims_list.option_reason : '-Nil-');
                                    if(claims_list.entry_date != "0000-00-00" && claims_list.entry_date != "1970-01-01" )
                                    { wa_data_array.push(claims_list.entry_date); }
                                    wa_data_array.push(claims_list.claim_reference);
                                    wa_data_array.push(claims_list.submited_date);
                                    wa_data_array.push(claims_list.last_submited_date);
                                    if(claims_list.created_by != 0 && user_full_names[claims_list.created_by] !== null && user_full_names[claims_list.created_by] !== undefined ) {
                                    wa_data_array.push(user_full_names[claims_list.created_by]);
                                    } else {
                                        wa_data_array.push("");
                                    }

                                    ws_data.push(wa_data_array);
                            } 
            });
            ws_data.push([""]);
            ws_data.push(["Summary"]);
            ws_data.push(["","Counts", "Value($)"]);
            ws_data.push(["Total Patients", ''+tot_summary.total_patient, tot_summary.total_charge]);
            ws_data.push(["Total CPT", ''+tot_summary.total_cpt, tot_summary.total_charge]);
            ws_data.push(["Total Units", ''+tot_summary.total_unit, tot_summary.total_charge]);
            ws_data.push(["Total Charges", ''+tot_summary.total_claim, tot_summary.total_charge]);
            ws_data.push([""]);
            ws_data.push(["Copyright \u00A9 "+yyyy+" Medcubics. All rights reserved."]);

         }                        


        // var elt = document.getElementById('export_table');
        // var wb = XLSX.utils.table_to_book(elt, {sheet:"Sheet JS"});
        function Workbook() {
            if(!(this instanceof Workbook)) return new Workbook();
            this.SheetNames = [];
            this.Sheets = {};
        }
        // var wb = XLSX.utils.book_new();
        var wb = new Workbook();

        var merge = [
            { s: {r:0, c:0}, e: {r:0, c:colspan} },{ s: {r:1, c:0}, e: {r:1, c:colspan} }, { s: {r:2, c:0}, e: {r:2, c:colspan} }
            ];
        // var ws = XLSX.utils.aoa_to_sheet(ws_data);
        // var range = XLSX.utils.decode_range(ws['!ref']);
        function datenum(v, date1904) {
            if(date1904) v+=1462;
            var epoch = Date.parse(v);
            return (epoch - new Date(Date.UTC(1899, 11, 30))) / (24 * 60 * 60 * 1000);
        }

        function sheet_from_array_of_arrays(data, opts) {
            var ws = {};
            var range = {s: {c:10000000, r:10000000}, e: {c:0, r:0 }};
            for(var R = 0; R != data.length; ++R) {
                for(var C = 0; C != data[R].length; ++C) {
                    if(range.s.r > R) range.s.r = R;
                    if(range.s.c > C) range.s.c = C;
                    if(range.e.r < R) range.e.r = R;
                    if(range.e.c < C) range.e.c = C;
                    var cell = {v: data[R][C] };
                    // console.log(data[R][C]);
                    if(cell.v == null) continue;
                    var cell_ref = XLSX.utils.encode_cell({c:C,r:R});

                    

                    
                    // if(C == 0){
                    //     console.log(C);
                    //     cell.s={
                    //         font:{
                    //             bold:true
                    //         }
                    //     }
                    // }
                    // if(R == 0){
                    //     cell.s={
                    //         fill:{
                    //             fgColor:{ rgb: "FFFFAA00" }
                    //         }
                    //     }
                    // }
                    // cell.s = {
                    //     font: {
                    //         sz: "11"
                    //     }
                    // }
                    if(C == 0 && R == 0) {
                        cell.s={
                            font:{
                                bold:true,
                                color: { rgb: "00877f"}
                            },
                            alignment: {
                                horizontal: "center"
                            }
                        }
                    }
                    if(C == 0 && R == 1) {
                        cell.s= {
                            alignment: {
                                horizontal: "center"
                            }                            
                        }
                    }
                    if(C == 0 && R == 2) {
                        cell.s= {
                            alignment: {
                                horizontal: "center"
                            }                            
                        }
                    }
                    if(C <= 23 && R == 4) {
                        cell.s = {
                            font: {
                                bold:true
                            },
                            alignment: {
                                horizontal: "center"
                            }                             
                        }
                    }
                    if(R >= 5 ) {
                        cell.s = {
                            font: {
                                sz: "9"
                            }
                        }                        
                    }
                    // if(C == 12) {
                    //     cell.s= {
                    //         numFmt : "0.00",
                    //         alignment: {
                    //             horizontal: "right"
                    //         }                             
                    //     }                        
                    // }
                    if(cell.v == "Summary") {
                        cell.s={
                            font:{
                                bold:true,
                                color: { rgb: "00877f"}
                            },
                            alignment: {
                                horizontal: "center"
                            }
                        }                      
                    }
                    if(typeof cell.v === 'number') {cell.t = 'n';
                        if(cell.v < 0 ) { cell.s = {numFmt : "0.00", font : {sz : "9", color: { rgb: "FF0000"} } } }
                        else { cell.s = {numFmt : "0.00", font : {sz : "9" } } }
                         }
                    else if(typeof cell.v === 'boolean') cell.t = 'b';
                    // else if(cell.v instanceof Date) {
                    //     cell.t = 'n'; cell.z = XLSX.SSF._table[14];
                    //     cell.v = datenum(cell.v);
                    // }
                    else cell.t = 's';
                    
                    ws[cell_ref] = cell;
                }
            }
            if(range.s.c < 10000000) ws['!ref'] = XLSX.utils.encode_range(range);
            return ws;
        }

        var ws = sheet_from_array_of_arrays(ws_data);

        var ws_name = "Charge_Analysis_Detailed";
        if(!ws['!merges']) ws['!merges'] = [];
        ws["!merges"] = merge;
        // wb.Sheets["Test Sheet"] = ws;
        
        wb.SheetNames.push(ws_name);
	    wb.Sheets[ws_name] = ws;
        
        
        var wbout = XLSX.write(wb, { bookType:'xlsx', bookSST:true, type: 'binary'});
        function s2ab(s) {
  
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
                
        }
        // stream.pipe(fs.createWriteStream(output_file_name));
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'Charge_Analysis_Detailed.xlsx');
        processingImageShow(".box-view","hide");
            },
                error: function (jqXhr, textStatus, errorThrown) {
                    processingImageShow(".box-view","hide");
                    js_sidebar_notification('error', "Currently unable to proccess excel");
                    console.log(errorThrown);
                }
            });
        });

// $('.js_search_export_pdf').click(function(){
//     var baseurl = '{{url('/')}}';
//     var url = baseurl+"/reports/export_pdf/charge_analysis";
//     var data_arr = [];
//     form = $('form').serializeArray();
//     form_data = "<form id='export_pdf' target='_blank' method='POST' action='"+url+"'>";
    
//     $('select.auto-generate:visible').each(function(){
//         data_arr.push({
//             name : $(this).attr('name'),
//             value: ($(this).select2('val'))
//         });
//     });
//     $('input.auto-generate:visible').each(function(){
//         data_arr.push({
//             name : $(this).attr('name'),
//             value: ($(this).val())
//         });
//     });
//     data_arr.push({
//         name : "controller_name", 
//         value:  "ReportController"
//     });
//     data_arr.push({
//         name : "function_name", 
//         value:  "chargesearchexport"
//     });
//     data_arr.push({
//         name : "report_name", 
//         value:  "Charge-Analysis-Detailed"
//     });
//     $.each(data_arr,function(index,value){
//         if($.isArray(value.value)) {
//             if(value.value.length > 0) {
//                 var avoid ="[]"
//                 form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
//             }
//         } else {
//             if(value.value.length > 0) {
//                 form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
//             }
//         }
//     });
//     form_data  += "<input type='hidden' name='exports' value='pdf'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
//     form_data += "</form>";
//     $("#export_pdf_div").html(form_data);
//     $("#export_pdf").submit();
//     $("#export_pdf").empty();
// });


    // $('.js_search_export_pdf').click(function(){
    //     $('#showmenu-bar').removeClass('hide');
    //     var append = $('#append_report_list').html('<div class="col-md-9" id="alert-notes-msg">'+report_name+'.xlsx</div><div class="col-md-3 no-padding"><span class="progress col-md-12 no-padding" style="float: right;padding: 2px 30px; font-size:12px; border-radius : 20px;"><p class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></p></span></div>');
    //     var url = api_site_url+"/reports/export_pdf/"+report_name.replace(/_/g, '-').toLowerCase();
    //     var data_arr = [];
    //     form = $('form').serializeArray();
    //     form_data = "<form id='export_pdf'>";
        
    //     $('select.auto-generate:visible').each(function(){
    //         data_arr.push({
    //             name : $(this).attr('name'),
    //             value: ($(this).select2('val'))
    //         });
    //     });
    //     $('input.auto-generate:visible').each(function(){
    //         data_arr.push({
    //                     name : $(this).attr('name'), 
    //                     value:  $(this).val()
    //                 });
    //              });
    //              data_arr.push({
    //                     name : "controller_name", 
    //                     value:  controller_name
    //                 });
    //                 data_arr.push({
    //                     name : "function_name", 
    //                     value:  function_name
    //                 });
    //                 data_arr.push({
    //                     name : "report_name", 
    //                     value:  report_name
    //                 });
    //                 data_arr.push({
    //                     name : "practice_id", 
    //                     value:  $("input[name='practice_id']").val()
    //                 });
    //     $.each(data_arr,function(index,value){
    //         if($.isArray(value.value)) {
    //             if(value.value.length > 0) {
    //                 var avoid ="[]"
    //                 form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
    //             }
    //         } else {
    //             if(value.value != "") {
    //                 form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
    //             }
    //         }
    //     });
    //     form_data  += "<input type='hidden' name='export' value='pdf'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
    //     form_data += "</form>";
    //     $.ajaxSetup({
    //           headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //           }
    //         });
    //     $.ajax({
    //         url: url,
    //         type: 'POST',
    //         data : $(form_data).serialize(),
    //         success: function(data) {
    //             generate_report();
    //         }
    //     });
    // });

</script>
@endpush
<!-- Server script end -->