@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/ar/list') }}">AR Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Aging Analysis - Detailed</span></small>

        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            <li class="dropdown messages-menu hide js_claim_export">
<!--<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
                @include('layouts.practice_module_stream_export', ['url' => 'reports/aginganalysisdetails/export/'])
                 <input type="hidden" name="report_controller_name" value="FinancialController" />
                <input type="hidden" name="report_controller_func" value="agingDetailsReportExport" />
                <input type="hidden" name="report_name" value="Aging Analysis - Detailed" />
            </li>
            <li><a href="{{ url('reports/ar/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/claim_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow ">

            <div class="box-body yes-border form-horizontal border-green border-radius-4">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform', 'url'=>'reports/financials/search/aginganalysisdetails','data-url'=>'reports/financials/search/aginganalysisdetails']) !!}

                <?php
                    $rendering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'); 
                    $billing_provider   = App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
                ?>
                @include('layouts.search_fields', ['search_fields'=>$search_fields])  
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part ">
                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding">
                            <input type="hidden" id="pagination_prt" value="string"/>
                            <input class="btn generate-btn js_filter_search_submit pull-left" value="Generate Report" type="submit">
                        </div>
                    </div>
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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_exit_part text-center hide">
    <input class="btn btn-medcubics-small" id="js_exit_part_report" value="Exit" type="button">
</div>
@stop
@push('view.scripts')
{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}
{!! HTML::script('js/xlsx.core.min.js') !!}
<script lang="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
<script>   
	var wto = '';
	var url = $('#js-bootstrap-searchvalidator_edts').attr("action");
	$(document).ready(function(){
		getMoreFieldData();
		setTimeout(function(){ 
		$('input[name="created_at"]').val('<?php echo Request::get('created_at'); ?>'); }, 50);
		$("#aging_insurance_id").hide();
		$("#facility_id").hide();
		$("#billing_provider_id").hide();
		$("#rendering_provider_id").hide();
		if($('#aging_group_by :selected').val() == 'insurance'){   
			$("#aging_"+$('#aging_group_by :selected').val()+"_id").show();
			$("#aging_"+$('#aging_group_by :selected').val()+"_id .select2-container").removeClass('hide');
		}
		else{   
			$("#"+$('#aging_group_by :selected').val()+"_id").removeClass('hide').show();
			$("#"+$('#aging_group_by :selected').val()+"_id .select2-container").removeClass('hide');
		}
	});
		
	/* function for get data for fields Start */
	function getData(){
		clearTimeout(wto);
		var data_arr = '';
		wto = setTimeout(function() {  
			 $('select.auto-generate:visible').each(function(){
				 data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';           
			 });                                                    
			 $('input.auto-generate:visible').each(function(){
				data_arr += $(this).attr('name')+'='+$(this).val()+'&';
			 });
			
			final_data = data_arr+"_token="+$('input[name=_token]').val(); 
			getAjaxResponse(url, final_data);
		}, 100);
	}
	/* function for get data for fields End */

	/* Onchange code for field Start */
	$(document).on('click','.js_filter_search_submit',function(){
		getData();
	});
	/* Onchange code for field End */ 

	/* Onchange code for more field Start */
	$(document).on('change','select.more_generate',function(){ 
		getMoreFieldData();
	});


	$("#aging_group_by.js_select_basis_change").on("click",function(){
		$("#facility_id").hide();
		$("#billing_provider_id").hide();
		$("#rendering_provider_id").hide();
		$("#aging_insurance_id").hide();
		if($(this).val()=='insurance'){
			$("#aging_"+$(this).val()+"_id").show();
			$("#aging_"+$(this).val()+"_id .select2-container").removeClass('hide');
		}
		else{
			$("#"+$(this).val()+"_id").removeClass('hide').show();
			$("#"+$(this).val()+"_id .select2-container").removeClass('hide');
		}
	})

    
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
                    value:  "FinancialController"
                });
                data_arr.push({
                    name : "function_name", 
                    value:  "agingDetailsReportExport"
                });
                data_arr.push({
                    name : "export", 
                    value:  "xlsx"
                });
             var baseurl = '{{url('/')}}';
            var url = baseurl+"/reports/streamcsv/export/aging-analysis-detailed-js";    
            processingImageShow(".box-view","show");
            $.ajax({
                type: 'POST',
                url: url,
                data:  data_arr,
                success: function(response) {
                    var myObject = JSON.parse( response );
                    var data = myObject.original;
    
    var user = "<?php $user = Auth::user()->name; echo $user; ?>";
    var practice_name = "<?php $heading_name = App\Models\Practice::getPracticeDetails(); echo $heading_name['practice_name']; ?>"

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = mm + '/' + dd + '/' + yyyy;

        var ws_data = [
        [practice_name],
        ['Aging Analysis - Detailed'],
        ['User : '+user+' | '+today+' '],
        [],
        ['Acc No', 'Patient Name', 'Claim No', 'DOS', 'Responsibility', 'Policy ID', 'Billing', 'Rendering', 'Facility', 'First Submission Date', 'Last Submission Date', 'Charges ($)', ...(data.value.show_flag == "All" || data.value.show_flag == "Unbilled") ?[ "Unbilled ($)"] : [], ...(data.value.show_flag == "All" || data.value.show_flag == "0-30") ? ["0-30"] : [], ...(data.value.show_flag == "All" || data.value.show_flag == "31-60") ? ["31-60"] : [], ...(data.value.show_flag == "All" || data.value.show_flag == "61-90") ? ["61-90"] : [], ...(data.value.show_flag == "All" || data.value.show_flag == "91-120") ? ["91-120"] : [], ...(data.value.show_flag == "All" || data.value.show_flag == "121-150") ? ["121-150"] : [], ...(data.value.show_flag == "All" || data.value.show_flag == "150-above") ? ["150-above"] : [], 'Pat AR ($)','Ins AR ($)', 'Tot AR ($)', 'AR Days', 'Claim Status']
        ];                        


        var search_lable = data.value.search_lable;
        var summaries = data.value.summaries;
        var show_flag = data.value.show_flag;
        var aging_report_list = data.value.aging_report_list;


        var temptasasasas_id = 0; var cnt = 0; var label = search_lable+'_id';
        $.each(aging_report_list, function(index, result) { 
            if( search_lable == 'billing_provider' || search_lable == 'rendering_provider' || search_lable == 'facility' ) {
                cnt++;
            // alert(temptasasasas_id);
                // alert(result[label]);
                if(temptasasasas_id != 0 && temptasasasas_id != result[label]) {
                    var wa_data_array = [];

                    wa_data_array.push("Totals");
                    wa_data_array.push("");
                    wa_data_array.push("");
                    wa_data_array.push("");
                    wa_data_array.push("");
                    wa_data_array.push("");
                    wa_data_array.push("");
                    wa_data_array.push("");
                    wa_data_array.push("");
                    wa_data_array.push("");
                    wa_data_array.push("");
                    var id = result.billing_provider_id;

                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["total_charge"]));

                    if(show_flag == "All" || show_flag == "Unbilled") {
                        wa_data_array.push(parseInt(summaries[temptasasasas_id]["unbilled"]));
                    }
                    if(show_flag == "All" || show_flag == "0-30") {
                        wa_data_array.push(parseInt(summaries[temptasasasas_id]["days30"]));
                    }
                    if(show_flag == "All" || show_flag == "31-60") {
                        wa_data_array.push(parseInt(summaries[temptasasasas_id]["days60"]));
                    }
                    if(show_flag == "All" || show_flag == "61-90") {
                        wa_data_array.push(parseInt(summaries[temptasasasas_id]["days90"]));
                    }
                    if(show_flag == "All" || show_flag == "91-120") {
                        wa_data_array.push(parseInt(summaries[temptasasasas_id]["days120"]));
                    }
                    if(show_flag == "All" || show_flag == "121-150") {
                        wa_data_array.push(parseInt(summaries[temptasasasas_id]["days150"]));
                    }
                    if(show_flag == "All" || show_flag == "150-above") {
                        wa_data_array.push(parseInt(summaries[temptasasasas_id]["daysabove"]));
                    }
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["total_pat"]));
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["total_ins"]));
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["total"]));
                    wa_data_array.push("");
                    wa_data_array.push("");

                    temptasasasas_id = 0;
                    ws_data.push(wa_data_array);
                }
            
            if(search_lable == 'rendering_provider'){
                provider_name = 'Rendering Provider - '+result.rendering_name;
            }
            if(search_lable == 'facility'){
                provider_name = 'Facility - '+result.facility_name;
            }
            if(search_lable == 'billing_provider'){
                provider_name = 'Billing Provider - '+result.billing_name;
            }
            
            if( temptasasasas_id == 0 && temptasasasas_id != result[label]) {
                var wa_data_array = [];

                wa_data_array.push(provider_name);
                if(show_flag == "All" || show_flag == "Unbilled") {
                    wa_data_array.push("");
                }
                if(show_flag == "All" || show_flag == "0-30") {
                    wa_data_array.push("");
                }
                if(show_flag == "All" || show_flag == "31-60") {
                    wa_data_array.push("");
                }
                if(show_flag == "All" || show_flag == "61-90") {
                    wa_data_array.push("");
                }
                if(show_flag == "All" || show_flag == "91-120") {
                    wa_data_array.push("");
                }
                if(show_flag == "All" || show_flag == "121-150") {
                    wa_data_array.push("");
                }
                if(show_flag == "All" || show_flag == "150-above") {
                    wa_data_array.push("");
                }
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");

                ws_data.push(wa_data_array);
                // alert(result[label]);
                temptasasasas_id = result[label];
                // alert(temptasasasas_id);
            }

            }
            var wa_data_array = [];

            wa_data_array.push(result.account_no);
            wa_data_array.push(result.patient_name);
            wa_data_array.push(result.claim_number);
            wa_data_array.push(result.dos);
            wa_data_array.push(result.responsibility_name);
            wa_data_array.push(result.policy_id);
            wa_data_array.push(result.billing_name);
            wa_data_array.push(result.rendering_name);
            wa_data_array.push(result.facility_name);
            wa_data_array.push((result.submited_date != "" && result.submited_date != null) ? result.submited_date : "-Nil-");
            wa_data_array.push((result.last_submited_date != "" && result.last_submited_date != null) ? result.last_submited_date : "-Nil-");
            wa_data_array.push(parseInt(result.total_charge));
            if(show_flag == "All" || show_flag == "Unbilled") {
                wa_data_array.push(parseInt(result.unbilled));
            }
            if(show_flag == "All" || show_flag == "0-30") {
                wa_data_array.push(parseInt(result.days30));
            }
            if(show_flag == "All" || show_flag == "31-60") {
                wa_data_array.push(parseInt(result.days60));
            }
            if(show_flag == "All" || show_flag == "61-90") {
                wa_data_array.push(parseInt(result.days90));
            }
            if(show_flag == "All" || show_flag == "91-120") {
                wa_data_array.push(parseInt(result.days120));
            }
            if(show_flag == "All" || show_flag == "121-150") {
                wa_data_array.push(parseInt(result.days150));
            }
            if(show_flag == "All" || show_flag == "150-above") {
                wa_data_array.push(parseInt(result.daysabove));
            }
            wa_data_array.push(parseInt(result.pat_bal));
            wa_data_array.push(parseInt(result.ins_bal));
            wa_data_array.push(parseInt(result.total_bal));
            wa_data_array.push((result.ar_days!=0 && result.ar_days != null) ? result.ar_days : '0');
            wa_data_array.push(result.status);

            ws_data.push(wa_data_array);

            if( (search_lable == 'billing_provider' || search_lable == 'rendering_provider' || search_lable == 'facility')) {   
                // alert(temptasasasas_id);
            if (cnt == aging_report_list.length) {
                var wa_data_array = [];

                wa_data_array.push("Totals");
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");
                wa_data_array.push("");

                id = result.billing_provider_id;
                
                wa_data_array.push(parseInt(summaries[temptasasasas_id]["total_charge"]));

                if(show_flag == "All" || show_flag == "Unbilled") {
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["unbilled"]));
                }
                if(show_flag == "All" || show_flag == "0-30") {
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["days30"]));
                }
                if(show_flag == "All" || show_flag == "31-60") {
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["days60"]));
                }
                if(show_flag == "All" || show_flag == "61-90") {
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["days90"]));
                }
                if(show_flag == "All" || show_flag == "91-120") {
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["days120"]));
                }
                if(show_flag == "All" || show_flag == "121-150") {
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["days150"]));
                }
                if(show_flag == "All" || show_flag == "150-above") {
                    wa_data_array.push(parseInt(summaries[temptasasasas_id]["daysabove"]));
                }
                wa_data_array.push(parseInt(summaries[temptasasasas_id]["total_pat"]));
                wa_data_array.push(parseInt(summaries[temptasasasas_id]["total_ins"]));
                wa_data_array.push(parseInt(summaries[temptasasasas_id]["total"]));
                wa_data_array.push("");
                wa_data_array.push("");
                ws_data.push(wa_data_array);
            }
            }

        });

        ws_data.push([""]);
        ws_data.push(["Copyright \u00A9 "+yyyy+" Medcubics. All rights reserved."]);
        
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
            { s: {r:0, c:0}, e: {r:0, c:22} },{ s: {r:1, c:0}, e: {r:1, c:22} }, { s: {r:2, c:0}, e: {r:2, c:22} }
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
                    if(cell.v == null) continue;
                    var cell_ref = XLSX.utils.encode_cell({c:C,r:R});

                    

                    
                    // if(C == 0){
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
                    if(cell.v == "Totals") {
                        cell.s={
                            font:{
                                bold:true,
                                color: { rgb: "000000"}
                            },
                            alignment: {
                                horizontal: "center"
                            }
                        }                      
                    }
                    var str1 = "Rendering Provider";
                    var str2 = "Billing Provider";
                    var str3 = "Facility -";

                    var val = ""+cell.v+""; 
                    if(val.includes(str1) || val.includes(str2) || val.includes(str3)) {
                        cell.s={
                            font:{
                                sz : "9",
                                bold: true,
                                color: { rgb: "00877f"}
                            },
                            alignment: {
                                horizontal: "left"
                            }
                        }  
                        merge.push({ s: {r:R, c:C}, e: {r:R, c:C + 20} });                    
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

        var ws_name = "Aging_Analysis_Detailed";
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
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'Aging_Analysis_Detailed.xlsx');
        processingImageShow(".box-view","hide");
            },
                error: function (jqXhr, textStatus, errorThrown) {
                    processingImageShow(".box-view","hide");
                    js_sidebar_notification('error', "Currently unable to proccess excel");
                }
            });
        });

</script>
@endpush  