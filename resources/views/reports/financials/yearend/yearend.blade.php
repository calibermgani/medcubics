@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/financials/list') }}">Billing Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Year End Financials</span></small>

        </h1>
        <ol class="breadcrumb">
        <!--     <a href="{{url('reports/export_pdf/year-end-financials')}}" target="_blank" class="js_search_export_pdf">
                <i class="fa fa-file-pdf-o" data-placement="top" data-toggle="tooltip" data-original-title="Generate PDF"></i>
            </a> -->
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            
                @include('layouts.practice_module_stream_export', ['url' => 'reports/financial/export/'])
                <input type="hidden" name="report_controller_name" value="ReportController" />
                <input type="hidden" name="report_controller_func" value="financialSearchExport" />
                <input type="hidden" name="report_name" value="Year End Financials" />
            <li><a href="{{ url('reports/financials/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>    
            <li><a href="#js-help-modal" data-url="{{url('help/year_end_financial')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop


@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"> 
        <div class="box box-view no-shadow">

            <div class="box-body yes-border border-green border-radius-4">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'year_end_report', 'name'=>'medcubicsform', 'url'=>'reports/search/financial']) !!}

				@include('layouts.search_fields')
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				<div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding margin-l-10">
					<input class="btn generate-btn js_filter_search_submit pull-left" tabindex="10" value="Generate Report" type="submit">
				</div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="box box-view no-shadow">
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Search</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body form-horizontal">
            {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform', 'url'=>'reports/search/financial']) !!}

            @php 
	            $facility_list	=  	App\Models\Facility::getAllfacilities();//Getting all facilities detail
	            $rendering_provider = App\Models\Provider::typeBasedProviderlist('Rendering'); 
	            $billing_provider 	= App\Models\Provider::typeBasedProviderlist('Billing');
                    
            @endphp 
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_search_part no-padding">
<!-- Facility type  --><!-- 
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">
    <label for="Facility" class="col-lg-3 col-md-4 col-sm-4 med-green">Facility</label>
    <div class="col-lg-3 col-md-8 col-sm-8 col-xs-8">
        {!! Form::select('facility_id',['all'=>'All']+(array)@$facility_list,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_facility"]) !!}
    </div>
</div>

<!-- Rendering provider  --><!-- 
<div class="col-lg-12 col-md-6 col-sm-6 no-padding margin-t-10">
    <label for="Rendering Provider" class="col-lg-3 col-md-4 col-sm-4 col-xs-4 med-green">Rendering Provider Name</label>
    <div class="col-lg-3 col-md-8 col-sm-8 col-xs-8">
        {!! Form::select('rendering_provider',['all'=>'All']+(array)$rendering_provider,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider"]) !!}
    </div>
</div>

<div class="col-lg-12 col-md-6 col-sm-6 no-padding margin-t-10">
    <label for="Rendering Provider" class="col-lg-3 col-md-4 col-sm-4 col-xs-4 med-green">Billing Provider Name</label>
    <div class="col-lg-3 col-md-8 col-sm-8 col-xs-8">
        {!! Form::select('billing_provider',['all'=>'All']+(array)$billing_provider,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider"]) !!}
    </div>
</div>


<!-- Year Scheduled  --><!-- 
<div class="col-lg-12 col-md-6 col-sm-6 no-padding margin-t-10 form-group">                                      
    {!! Form::label('Year Option', 'Year Option', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
        {!! Form::radio('charge_year_option', 'current_year',true,['class'=>'flat-red js_select_basis_change']) !!}&nbsp;Current Year&emsp;
        {!! Form::radio('charge_year_option', 'previous_year',null,['class'=>'flat-red js_select_basis_change']) !!}&nbsp;Previous Year&emsp;
        {!! Form::radio('charge_year_option','enter_year',null,['class'=>'flat-red js_select_basis_change','id'=>'js_select_financial_year']) !!}&nbsp;Select Year&emsp;
    </div>                    
</div>

<div class="col-lg-12 col-md-6 col-sm-6 no-padding margin-t-10 form-group js_all_hide_col js_select_financial_year hide">                                      
    {!! Form::label('', '', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
    <div class="col-lg-3 col-md-8 col-sm-8 col-xs-8">
        {!! Form::select('select_year',(array)$array_year,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_year_select"]) !!}
    </div>  
</div>  

</div>
<div class="col-lg-12 col-md-12 col-sm-12">
<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 no-padding">
    <input class="btn btn-flat btn-medcubics js_filter_search_submit" value="Search" type="submit">
</div>
</div>
</div>
{!! Form::close() !!}
</div>
</div>-->
<div class="js_spin_image hide">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center">
        <i class="fa fa-spinner fa-spin med-green font20"></i> Processing
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_claim_list_part hide no-padding"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_exit_part text-center hide">
    <input class="btn btn-medcubics-small" id="js_exit_part_report" value="Exit" type="button">
</div>
<div class="export_pdf_div"></div>

@endsection

@push('view.scripts')
{!! HTML::script('js/daterangepicker_dev.js') !!}
{!! HTML::script('js/xlsx.core.min.js') !!}
<script lang="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
<script>
	var wto = '';
	var url = $('#year_end_report').attr("action");
	$(document).ready(function(){
		getMoreFieldData();
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
			final_data = data_arr+"_token="+$('input[name=_token]').val();;
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
	/* Onchange code for more field End */
        
/* Export PDF Function*/


/* Export Excel Function*/

    $(document).on('click','.js_search_export_raw', function(){ 
        //console.log('test');
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
                    value:  "financialSearchExport"
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
            var url = baseurl+"/reports/streamcsv/export/year-end-financials-js";    
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


        var ws_data = [
        [practice_name],
        ['Year End Financials'],
        ['User : '+user+' | '+today+' '],
        [],
        ['Month', 'Claims', 'Charges($)', 'Adjustments($)', '', '','Refund($)', '', '', 'Payments($)', '', '','AR Bal($)'],
        ['', '', '', 'Patient', 'Insurance', 'Total', 'Patient', 'Insurance', 'Total', 'Patient', 'Insurance', 'Total', 'Patient', 'Insurance', 'Total']
        ];

        var claims = data.value.claims;

        var count = 1;
        var last_visit = [];
        var charges = total_adjustments = patient_payments = insurance_payments = patient_ar_due = insurance_ar_due = total_patient_adj = total_ins_adj = total_ref_patient = total_ref_ins = claims_count = 0;


            $.each(claims, function(key, claim_list) {
                var ins_adj = claim_list.insurance_adj;
                claims_count += claim_list.claims_visits;

                var wa_data_array = [];
                wa_data_array.push(key+" - "+claim_list.year_key);
                wa_data_array.push(""+claim_list.claims_visits+"");
                wa_data_array.push((claim_list.value != "")? 0 : parseInt(claim_list.value));
                wa_data_array.push(parseInt(claim_list.patient_adjusted));
                wa_data_array.push(parseInt(ins_adj));
                wa_data_array.push(claim_list.total_adjusted);
                wa_data_array.push(claim_list.patient_refund);
                wa_data_array.push(-(claim_list.ins_refund));
                wa_data_array.push((-(claim_list.ins_refund)) + (claim_list.patient_refund));
                wa_data_array.push(claim_list.patient_payment);
                wa_data_array.push(claim_list.insurance_payment);
                wa_data_array.push((claim_list.insurance_payment)+(claim_list.patient_payment));
                wa_data_array.push((typeof(claim_list.patient_due != "undefined" ) && claim_list.patient_due !== null)?claim_list.patient_due:0);
                wa_data_array.push((typeof(claim_list.insurance_due != "undefined" ) && claim_list.insurance_due !== null)?claim_list.insurance_due:0);
                wa_data_array.push(claim_list.insurance_due + claim_list.patient_due);

                ws_data.push(wa_data_array);

                count++;  
                charges += claim_list.value; 
                total_adjustments += claim_list.total_adjusted;
                total_patient_adj += parseInt(claim_list.patient_adjusted);
                total_ins_adj += parseInt(ins_adj);
                patient_payments += claim_list.patient_payment;
                total_ref_patient += claim_list.patient_refund;
                total_ref_ins += claim_list.ins_refund;
                insurance_payments += claim_list.insurance_payment;
                patient_ar_due += claim_list.patient_due;
                insurance_ar_due += claim_list.insurance_due;
            });

            var wa_data_array = [];
            wa_data_array.push("Totals");
            wa_data_array.push(""+claims_count+"");
            wa_data_array.push(charges);
            wa_data_array.push(total_patient_adj);
            wa_data_array.push(total_ins_adj);
            wa_data_array.push(total_adjustments);
            wa_data_array.push(total_ref_patient);
            wa_data_array.push(-(total_ref_ins));
            wa_data_array.push((-(total_ref_ins)) + ((total_ref_patient)));
            wa_data_array.push(patient_payments);
            wa_data_array.push(insurance_payments);
            wa_data_array.push(insurance_payments+patient_payments);
            wa_data_array.push(patient_ar_due);
            wa_data_array.push(insurance_ar_due);
            wa_data_array.push((insurance_ar_due+patient_ar_due));

            ws_data.push(wa_data_array);

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
            { s: {r:0, c:0}, e: {r:0, c:14} },{ s: {r:1, c:0}, e: {r:1, c:14} }, { s: {r:2, c:0}, e: {r:2, c:14} }, { s: {r:4, c:3}, e: {r:4, c:5} }, { s: {r:4, c:6}, e: {r:4, c:8} }, { s: {r:4, c:9}, e: {r:4, c:11} }, { s: {r:4, c:12}, e: {r:4, c:14} }, { s: {r:4, c:0}, e: {r:5, c:0} }, { s: {r:4, c:1}, e: {r:5, c:1} }, { s: {r:4, c:2}, e: {r:5, c:2} }
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
                                horizontal: "center",
                                vertical: "center"
                            }                             
                        }
                    }
                    if(C <= 14 && R == 5) {
                        cell.s = {
                            font: {
                                bold:true
                            },
                            alignment: {
                                horizontal: "center"
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
                    if(cell.v == "Totals") {
                        var row = R;
                    }
                    if(typeof cell.v === 'number') {
                        cell.t = 'n';
                        if(R == row) {
                            if(cell.v < 0 ) { 
                            cell.s = {numFmt : "0.00", font : {sz : "9", color: { rgb: "FF0000"}, bold:true } } 
                            }
                            else { 
                                cell.s = {numFmt : "0.00", font : {sz : "9", bold:true } } 
                            }                            
                        } else {
                            if(cell.v < 0 ) { 
                                cell.s = {numFmt : "0.00", font : {sz : "9", color: { rgb: "FF0000"} } } 
                            }
                            else { 
                                cell.s = {numFmt : "0.00", font : {sz : "9" } } 
                            }
                        }
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

        var ws_name = "Year_End_Financials";
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
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'Year_End_Financials.xlsx');
        processingImageShow(".box-view","hide");
            },
                error: function (jqXhr, textStatus, errorThrown) {
                    processingImageShow(".box-view","hide");
                    js_sidebar_notification('error', "Currently unable to proccess excel");
                    console.log(errorThrown);
                }
            });
        });

    // $(document).on('click','.js_search_export_pdf',function(){
    //     var baseurl = '{{url('/')}}';
    //     var url = baseurl+"/reports/export_pdf/year-end-financials";
    //     $.ajax({
    //         type: 'GET',
    //         url: url,
    //         success: function(data){
    //             console.log('success');
    //         }
    //     });
    // });

    // $(document).on('click','.js_search_export_pdf',function(){
    //     var baseurl = '{{url('/')}}';
    //     var url = baseurl+"/reports/export_pdf/year-end-financials";
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
    //         value:  "financialSearchExport"
    //     });
    //     data_arr.push({
    //         name : "report_name", 
    //         value:  "Year-End-Financials"
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
    //     alert('hi');
    //     $("#export_pdf").empty();
    // });
</script>
@endpush