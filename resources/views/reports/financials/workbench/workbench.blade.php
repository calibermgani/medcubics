@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/ar/list') }}">AR Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>AR Workbench Report</span></small>
        </h1>
        <ol class="breadcrumb">
            <!-- <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li> -->
            
			
			
            <li class="dropdown messages-menu hide js_claim_export">
                @include('layouts.practice_module_stream_export', ['url' => 'reports/ar/workbench/export/'])
                <input type="hidden" name="report_controller_name" value="FinancialController" />
                <input type="hidden" name="report_controller_func" value="workbenchSearchExport" />
                <input type="hidden" name="report_name" value="AR Work Bench Report" />
            </li>
            <li><a href="{{ url('reports/ar/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/workbench_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop  


@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow ">
            <div class="box-body yes-border border-green border-radius-4">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform', 'url'=>'reports/ar/workbench', 'data-url'=>'reports/ar/workbench']) !!}

                @php 
					$rendering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'); 
					$billing_provider 	= App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
					$reffering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Referring'); 
				@endphp 
			
                 @include('layouts.search_fields', ['search_fields'=>$search_fields])                

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">                     

                            

                       <div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding">
                            <input class="btn generate-btn js_filter_search_submit pull-left" value="Generate Report" type="submit">
                        </div>
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
	  <?php 
	if(Request::get('cpt_type') != ''){   
	if(Request::get('cpt_type') == 'custom_type' ){?>
	    $("#custom_type_from").show();
	    $('#custom_type_to').show();
	<?php }else{ ?>
	    $("#custom_type_from").hide();
	    $('#custom_type_to').hide();
	<?php }?>
	<?php if(Request::get('cpt_type') == 'cpt_code' ){?>
	    $("#cpt_code_id").show();
	    <?php }else{ ?>
	    $("#cpt_code_id").hide();
	    <?php }?> 
	    <?php if(Request::get('cpt_type') == 'All' ){?>
	    $("#custom_type_from").hide();
	    $('#custom_type_to').hide();
	    $("#cpt_code_id").hide();
	    <?php }?>

	    <?php }else{?>  
	         $("#custom_type_from").hide();
	    $('#custom_type_to').hide();
	     $("#cpt_code_id").hide();
	    <?php }?> 
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

	$("#cpt_type .js_select_basis_change").on("click",function(){
		if($(this).val() == 'custom_type'){
			$("#custom_type_from").show();
			$("#custom_type_to").show();
			$("#cpt_code_id").hide();
		}else if($(this).val() == 'All'){
			$("#custom_type_from").hide();
			$("#custom_type_to").hide();
			$("#cpt_code_id").hide();
		}else if($(this).val() == 'cpt_code'){
			$("#cpt_code_id").show();
			$("#custom_type_from").hide();
			$("#custom_type_to").hide();
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
                    value:  "FinancialController"
                });
                data_arr.push({
                    name : "function_name", 
                    value:  "workbenchSearchExport"
                });
                data_arr.push({
                    name : "export", 
                    value:  "xlsx"
                });
                data_arr.push({
                    name : "practice_id", 
                    value:  $("input[name='practice_id']").val()
                });
                // console.log(data_arr);
             var baseurl = '{{url('/')}}';
            var url = baseurl+"/reports/streamcsv/export/ar-workbench-report-js";    
            processingImageShow(".box-view","show");
            $.ajax({
                type: 'POST',
                url: url,
                data:  data_arr,
                success: function(response) {
                    var myObject = JSON.parse( response );
                    var data = myObject.original.value;

                    var user = "<?php $user = Auth::user()->name; echo $user; ?>";
                    var practice_name = "<?php $heading_name = App\Models\Practice::getPracticeDetails(); echo $heading_name['practice_name']; ?>"
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        today = mm + '/' + dd + '/' + yyyy;

        count = Object.keys(data.search_by).length - 1;
        var i = 0;
        var search_by = '';
        $.each(data.search_by, function(index, value) {
            var pipe = (count == i) ? "" : " | ";
            search_by += index +" : "+ value + pipe;
        i++;
        });

        var ws_data = [
        [practice_name],
        ['AR Workbench Report'],
        ['User : '+user+' | '+today+' '],
        [search_by],
        [],
        ['Claim No', 'DOS', 'Patient Name', 'Rendering', 'Billing', 'Facility','Responsibility', 'Category','Charge Amt($)', 'Paid($)','Adj($)', 'Pat AR($)', 'Ins AR($)', 'AR Due($)', 'Claim Age', 'Claim Status', 'Workbench Status', 'Followup Date', 'Assigned To'],
        ];

        var workbench_list = data.workbench_list;
        $.each(workbench_list, function(index, result) {
            if((typeof(result.claim_number) != "undefined" && result.claim_number !== null) ) {
                var wa_data_array = [];

                wa_data_array.push(result.claim_number);
                wa_data_array.push(result.dos);
                wa_data_array.push(result.patient_name);
                wa_data_array.push(result.rendering_provider_name);
                wa_data_array.push(result.billing_provider_name);
                wa_data_array.push(result.facility_name);
                wa_data_array.push(result.insurance_name);
                wa_data_array.push(result.insurance_category);
                wa_data_array.push(parseInt(result.total_charge));
                wa_data_array.push(parseInt(result.tot_paid));
                wa_data_array.push(parseInt(result.tot_adj));
                wa_data_array.push(parseInt(result.pat_due));
                wa_data_array.push(parseInt(result.ins_due));
                wa_data_array.push(parseInt(result.ar_due));
                wa_data_array.push(result.claim_age_days);
                wa_data_array.push(result.claim_status);
                wa_data_array.push(result.workbench_status);
                if(today == result.fllowup_date) {
                    wa_data_array.push(result.fllowup_date);
                } else if (today >= result.fllowup_date){
                    wa_data_array.push(result.fllowup_date);
                } else {
                    wa_data_array.push(result.fllowup_date);
                }
                wa_data_array.push(result.assign_user_id);
            }

            ws_data.push(wa_data_array);
        });

        ws_data.push([""]);
        ws_data.push(["Copyright \u00A9 "+yyyy+" Medcubics. All rights reserved."]);

        function Workbook() {
            if(!(this instanceof Workbook)) return new Workbook();
            this.SheetNames = [];
            this.Sheets = {};
        }
        // var wb = XLSX.utils.book_new();
        var wb = new Workbook();

        var merge = [
            { s: {r:0, c:0}, e: {r:0, c:18} },{ s: {r:1, c:0}, e: {r:1, c:18} }, { s: {r:2, c:0}, e: {r:2, c:18} }, { s: {r:3, c:0}, e: {r:3, c:18} }
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
                    if(C == 0 && R == 3) {
                        cell.s= {
                            alignment: {
                                horizontal: "center"
                            }                            
                        }
                    }
                    if(C <= 23 && R == 5) {
                        cell.s = {
                            font: {
                                bold:true
                            },
                            alignment: {
                                horizontal: "center",
                                vertical: "center"
                            }                             
                        }
                    }
                    if(R >= 6 ) {
                        cell.s = {
                            font: {
                                sz: "9"
                            }
                        }                        
                    }
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
            var wscols = [
                {wch: 12},
                {wch: 10},
                {wch: 10},
                {wch: 10},
                {wch: 10},
                {wch: 10},
                {wch: 10},
                {wch: 10},
                {wch: 10},
                {wch: 10},
                {wch: 15}// "pixels"
            ];
            ws['!cols'] = wscols;
            return ws;
        }

        var ws = sheet_from_array_of_arrays(ws_data);

        var ws_name = "AR_Workbench_Report";
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
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'AR_Workbench_Report.xlsx');
        processingImageShow(".box-view","hide");
            },
                error: function (jqXhr, textStatus, errorThrown) {
                    processingImageShow(".box-view","hide");
                    js_sidebar_notification('error', "Currently unable to proccess excel");
                    console.log(errorThrown);
                }
            });
        });
</script>
@endpush  
