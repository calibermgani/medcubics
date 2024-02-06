@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/ar/list') }}">AR Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Denial Trend Analysis</span></small>
        </h1>
        <ol class="breadcrumb">
            <!-- <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li> -->
            <li class="dropdown messages-menu hide js_claim_export">
                @include('layouts.practice_module_stream_export', ['url' => 'reports/ar/denialtrendanalysis/export/'])
                <input type="hidden" name="report_controller_name" value="FinancialController" />
                <input type="hidden" name="report_controller_func" value="denialAnalysisSearchExport" />
                <input type="hidden" name="report_name" value="Denial Trend Analysis" />
            </li>
            <li><a href="{{ url('reports/ar/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/denialtrendanalysis_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop  


@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow ">
            <div class="box-body yes-border border-green border-radius-4">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform', 'url'=>'reports/ar/denialtrendanalysis', 'data-url'=>'reports/ar/denialtrendanalysis']) !!}

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
	  //  $('select#adjustment_reason_id').attr('disabled',true);   
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
		//console.log($(this).val());
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
                    value:  "denialAnalysisSearchExport"
                });
                data_arr.push({
                    name : "export", 
                    value:  "xlsx"
                });
                // console.log(data_arr);
             var baseurl = '{{url('/')}}';
            var url = baseurl+"/reports/streamcsv/export/denial-trend-analysis-js";    
            processingImageShow(".box-view","show");
            $.ajax({
                type: 'POST',
                url: url,
                data:  data_arr,
                success: function(response) {
                    var myObject = JSON.parse( response );
                    // console.log(myObject);
                    var data = myObject.original;
                    console.log(data);
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

		var workbench_status = data.value.workbench_status;
		var denial_cpt_list = data.value.denial_cpt_list;

        var ws_data = [
        [practice_name],
        ['Denial Trend Analysis'],
        ['User : '+user+' | '+today+' '],
        [],
        ['Claim No', 'DOS', 'Acc No', 'Patient Name', 'Insurance', 'Category', 'Rendering', 'Facility', 'Denied CPT', 'Denied Date', 'Denial Reason Code', 'Denial Reason', 'Claim Age', ...(typeof(workbench_status) != "undefined" && workbench_status !== null && workbench_status == 'Include') ? ['Workbench Status'] : [], 'Charge Amt($)', 'Outstanding AR($)']
        ];
                        
		if(denial_cpt_list.length > 0) { 
			$.each(denial_cpt_list, function(index,result) {
				if(typeof(result.claim_number) != "undefined" && result.claim_number !== null && result.claim_number != ''){ 
					var wa_data_array = [];
                    var ar_due = result.total_ar_due; 	

					wa_data_array.push(result.claim_number);
					wa_data_array.push(result.dos);
					wa_data_array.push(result.account_no);
					wa_data_array.push(result.patient_name);
					wa_data_array.push(result.responsibility_full_name);
					wa_data_array.push(result.ins_category);
					wa_data_array.push(result.rendering_name);
					wa_data_array.push(result.facility_name);
					wa_data_array.push(result.cpt_code);
					wa_data_array.push(result.denial_date);
					wa_data_array.push((result.denial_code != '') ? result.denial_code.replace(/,+$/,'') : "-Nil-");

                    if(result.denial_code != '') {
                        var code = result.denial_code.replace(/,+$/,'');
                        var denial_code_ids = code.split(','); 
                        $.each(denial_code_ids, function(key, id) {
                            if(id.indexOf("CO") != -1) {
                                var exp = id.split('CO');
                                var denial_code_id = exp[1];
                            } else if(id.indexOf("PR") != -1) {
                                var exp = id.split('PR');
                                var denial_code_id = exp[1];
                            } else if(id.indexOf("OA") != -1) {
                                var exp = id.split('OA');
                                var denial_code_id = exp[1];
                            } else if(id.indexOf("PI") != -1) {
                                var exp = id.split('PI');
                                var denial_code_id = exp[1];
                            } else{
                                var denial_code_id = id;
                            }
                        });
                            wa_data_array.push("");
                    } else {
                        wa_data_array.push("-Nil-");
                    }
                    wa_data_array.push(""+result.claim_age_days+"");

                    if(typeof(workbench_status) != "undefined" && workbench_status !== null && workbench_status != ''){
                        if(typeof(result.last_workbench_status) != "undefined" && result.last_workbench_status !== null) {
                            wa_data_array.push(result.last_workbench_status);
                        } else {
                            wa_data_array.push("N/A");
                        }
                    }
                    wa_data_array.push(parseInt(result.charge));
                    wa_data_array.push(parseInt(result.total_ar_due));

                    ws_data.push(wa_data_array);
				} else {
                    
				}
			});
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
            { s: {r:0, c:0}, e: {r:0, c:15} },{ s: {r:1, c:0}, e: {r:1, c:15} }, { s: {r:2, c:0}, e: {r:2, c:15} }
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

        var ws_name = "Denial_Trend_Analysis";
        if(!ws['!merges']) ws['!merges'] = [];
        ws["!merges"] = merge;
        // wb.Sheets["Test Sheet"] = ws;
        
        wb.SheetNames.push(ws_name);
	    wb.Sheets[ws_name] = ws;
        
        
        var wbout = XLSX.write(wb, { bookType:'xlsx', bookSST:true, type: 'binary', compression:true});
        function s2ab(s) {
  
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
                
        }
        // stream.pipe(fs.createWriteStream(output_file_name));
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'Denial_Trend_Analysis.xlsx');
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