@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/collections/list') }}" >Collection Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Patient and Insurance Payment</span></small>
        </h1>
        <ol class="breadcrumb">
            <!-- <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li> -->
            @include('layouts.practice_module_stream_export', ['url' => 'reports/collections/patient-insurance?'])
                <input type="hidden" name="report_controller_name" value="CollectionController" />
                <input type="hidden" name="report_controller_func" value="patientInsurancePaymentSearchexport" />
                <input type="hidden" name="report_name" value="Patient and Insurance Payment"/>
         	<li><a href="{{ url('reports/collections/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--<li><a href="#js-help-modal" data-url="{{url('help/patientInsurancePayment')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>-->
        </ol>
    </section>
</div>
@stop  

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="box box-view no-shadow ">
            <div class="box-body yes-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform', 'url'=>'reports/search/patientInsurancePayment', 'data-url'=>'reports/search/patientInsurancePayment']) !!}

                @php 
                    $billing_provider   = App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
                @endphp 
            
                @include('layouts.search_fields', ['search_fields'=>$search_fields])                

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                       <div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding">
                            <input class="btn generate-btn js_filter_search_submit pull-left" value="Generate Report" type="submit">
                        </div>
                        </div>
                        <input type="hidden" name="practice_id" value="{{$practice_id}}">
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
<div id="export_pdf_div"></div>

@stop
@push('view.scripts') 

{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}
{!! HTML::script('js/xlsx.core.min.js') !!}
<script lang="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
<script type="text/javascript">
	var wto = '';
	var url = $('#js-bootstrap-searchvalidator').attr("action");
    var api_site_url = '{{url("/")}}';
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/reports/search/patientInsurancePayment"; 

	//--------------------------------- FORM SUBMIT ----------------------
	$(".js_filter_search_submit").on("click",function(){
		getAjaxResponse(listing_page_ajax_url, $("#js-bootstrap-searchvalidator").serialize());
	});

	$(document).ready(function(){
		getMoreFieldData();
		$('[id^=insurance_id]').hide();
		$('#select_date_of_service').parent().parent().hide();
		<?php
		if (isset($searchUserData->search_fields_data))
			$searchUserDetails = json_decode(@$searchUserData->search_fields_data, true);
		
		if(!empty(Request::all())){
			$request = Request::all();
		}
		if(!empty($searchUserDetails))
			foreach($searchUserDetails as $result)
				if($result['label_name']=='payer' && $result['value']=='insurance' || !empty(Request::all()) && $request['payer']=='insurance'){
					?>
					$('[id^=insurance_id]').show();<?php
				}
				if(!empty(Request::all()) && $request['payer']!='insurance'){
					?>
					$('[id^=insurance_id]').hide();<?php
				}
		?>
	});

	/* Onchange code for more field Start */
	$(document).on('change','select.more_generate',function(){ 
		getMoreFieldData();
	});


// $('.js_search_export_pdf').click(function(){
//     var baseurl = '{{url('/')}}';
//     var url = baseurl+"/reports/export_pdf/patient-insurance";
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


	$(".js_select_basis_change").on("click",function(){
	//$("#insurance_id").hide();
		if($(this).val()=='insurance'){
		   $('[id^=insurance_id]').show();
			$("[id^=insurance_id] .select2-container").removeClass('hide');
		} else {
		   $('[id^=insurance_id]').hide();
		   $('[id^=insurance_id]').val('');
		}	
	});

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
                    value:  "CollectionController"
                });
                data_arr.push({
                    name : "function_name", 
                    value:  "patientInsurancePaymentSearchexport"
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
            var url = baseurl+"/reports/streamcsv/export/patient-insurance-payment-js";    
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

        count = Object.keys(data.header).length - 1;
        var i = 0;
        var header = '';
        $.each(data.header, function(index, value) {
            var pipe = (count == i) ? "" : " | ";
            header += index +" : "+ value + pipe;
        i++;
        });

        var ws_data = [
        [practice_name],
        ['Patient and Insurance Payment'],
        ['User : '+user+' | '+today+' '],
        [header],
        [],
        ['Transaction Date', 'Acc No', 'Patient Name', 'DOS', 'Claim No', 'Payer','Payment Type', 'Check/EFT/CC/MO No','Check/EFT/CC/MO Date', 'Paid($)','Reference', 'User'],
        ];

        var payment = data.payment.payment;
        var user_names = data.user_names;
        $.each(payment, function(index,r) {
            var wa_data_array = [];

            wa_data_array.push(r.transaction_date);
            wa_data_array.push(r.account_no);
            wa_data_array.push(r.patient_name);
            wa_data_array.push((r.dos=='')?'-Nil-':r.dos);
            wa_data_array.push((r.claim_number=='')?'-Nil-':r.claim_number);
            wa_data_array.push(r.payer_name);
            wa_data_array.push(r.pmt_mode);
            wa_data_array.push((r.pmt_mode_no=='')?'-Nil-':r.pmt_mode_no);
            wa_data_array.push(r.pmt_mode_date);
            if((typeof(r.payer) != "undefined" && r.payer !== null) && r.payer=="Patient") {
                wa_data_array.push(parseInt(r.pmt_amt));
            } else {
                wa_data_array.push(parseInt(r.total_paid));
            }
            wa_data_array.push((r.reference != '') ? r.reference:"-Nil-");
            if(r.created_by != 0 && (typeof(user_names[r.created_by]) != "undefined") ) {
                wa_data_array.push(user_names[r.created_by]);
            }

            ws_data.push(wa_data_array);

        });

		ws_data.push([""]);
        ws_data.push(["Summary"]);
        var payments = data.payment;
        if((typeof(payments['header']['Transaction Date']) != "undefined" && payments['header']['Transaction Date'] !== null)) {
            ws_data.push(["Transaction Date", payments['header']['Transaction Date']]);
        }
        if((typeof(payments['header']['Payer']) && payments['header']['Payer'] !== null) && payments['header']['Payer']=="Patient Payments" || payments['header']['Payer']=="All Payments") {
            ws_data.push(["Total Patient Payments", payments['patient_total']]);
        }
        if((typeof(payments['header']['Payer']) && payments['header']['Payer'] !== null) && payments['header']['Payer']=="Insurance Payments" || payments['header']['Payer']=="All Payments") {
            ws_data.push(["Total Insurance Payments", payments['insurance_total']]);
        }
        if((typeof(payments['header']['Payer']) && payments['header']['Payer'] !== null) && payments['header']['Payer']=="All Payments") {
            ws_data.push(["Total Payments", payments['patient_total']+payments['insurance_total']]);
        }
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
            { s: {r:0, c:0}, e: {r:0, c:11} },{ s: {r:1, c:0}, e: {r:1, c:11} }, { s: {r:2, c:0}, e: {r:2, c:11} }, { s: {r:3, c:0}, e: {r:3, c:11} }
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

        var ws_name = "Patient_and_Insurance_Payment";
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
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'Patient_and_Insurance_Payment.xlsx');
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
<!-- Server script end -->