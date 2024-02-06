@extends('admin')
@section('toolbar')

<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/financials/list') }}">Billing Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>End of the Day Totals</span></small>
        </h1>
        <ol class="breadcrumb">

            @include('layouts.practice_module_stream_export', ['url' => 'reports/financials/enddayexport/'])
                <input type="hidden" name="report_controller_name" value="FinancialController" />
                <input type="hidden" name="report_controller_func" value="endDayExport" />
                <input type="hidden" name="report_name" value="End of the Day Totals" />

            <li><a href="{{ url('reports/financials/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/adjustment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop


@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  id="js_ajax_part">



    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow ">

            <div class="box-body yes-border border-green border-radius-4">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform', 'url'=>'reports/financials/filter_result', 'data-url'=>'reports/financials/filter_result']) !!}
                <input type = "hidden" name = "practice_id" value = "{{ $practice_id }}" />
                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                      @include('layouts.search_fields')    
                       <!-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group">
                                {!! Form::label('Group By', 'Transaction Date', ['class'=>'col-lg-4 col-md-5 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('date_option',$groupby,null,['class'=>'select2 form-control js_change_date_option_edt','tabindex'=>'1']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('From', 'From Date', ['class'=>'col-lg-4 col-md-5 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', null,['class'=>'search_start_date form-control datepicker dm-date','tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                    {!! Form::hidden('hidden_from_date', null,['tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>  

                        </div>



                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group  margin-b-18 hidden-sm hidden-xs">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
                                    {!! Form::label('', '', ['class'=>'control-label']) !!}

                                </div>                                                        
                            </div> 

                            <div class="form-group">
                                {!! Form::label('To', 'To Date', ['class'=>'col-lg-4 col-md-5 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                    {!! Form::hidden('hidden_to_date', null,['tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>-->

                            <div class="col-lg-11 col-md-11 col-sm-9 col-xs-12 no-padding margin-l-10">
                                <input class="btn generate-btn js_filter_search_submit pull-left" tabindex="10" value="Generate Report" type="submit">
                            </div>
                        <!--</div>-->

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
    <input id="js_exit_part_report" class="btn btn-medcubics-small" value="Exit" type="button">
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
	});
    
	/* function for get data for fields Start */
	function getData(){
	    clearTimeout(wto);
	    var data_arr = '';
	    wto = setTimeout(function() {  
	         $('select.auto-generate:visible').each(function(){
	             data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';
	            //  console.log($(this).select2('val'));
	         });                                                    
	         $('input.auto-generate:visible').each(function(){
	            data_arr += $(this).attr('name')+'='+$(this).val()+'&';
	         });
        
	        final_data = data_arr+"_token="+$('input[name=_token]').val(); 
	        // console.log('form '+final_data);       
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
                    value:  "endDayExport"
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
            var url = baseurl+"/reports/streamcsv/export/end-of-the-day-totals-js";    
            processingImageShow(".box-view","show");
            $.ajax({
                type: 'POST',
                url: url,
                data:  data_arr,
                success: function(response) {
                    var myObject = JSON.parse( response );
                    // console.log(myObject);
                    var data = myObject.original;
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


        var ws_data = [
        [practice_name],
        ['End of the Day Totals'],
        ['User : '+user+' | '+today+' '],
        [],
        ['Date-Day', 'Charges($)', 'Claims', 'Write-off($)', 'Adjustments($)', '','Refund($)', '','Payments($)', '','Total Payments($)'],
        ['', '', '', '', 'Insurance', 'Patient', 'Insurance', 'Patient', 'Insurance', 'Patient', 'Total Payments($)']
        ];

        var endDayTotals = data.value.response;

        var total_adj = 0;
        var patient_total = 0;
        var insurance_total = 0;

        $.each(endDayTotals, function(index, dates) {
            var total_charge = (typeof(dates.total_charge) != "undefined" && dates.total_charge !== null) ? dates.total_charge : 0;
            var writeoff_total = (typeof(dates.writeoff_total) != "undefined" && dates.writeoff_total !== null) ? dates.writeoff_total : 0;
            var insurance_adjustment = (typeof(dates.insurance_adjustment) != "undefined" && dates.insurance_adjustment !== null) ? dates.insurance_adjustment : 0;
            var patient_adjustment = (typeof(dates.patient_adjustment) != "undefined" && dates.patient_adjustment !== null) ? dates.patient_adjustment : 0;                
            var insurance_refund = (typeof(dates.insurance_refund) != "undefined" && dates.insurance_refund !== null) ? dates.insurance_refund : 0;
            var patient_refund = (typeof(dates.patient_refund) != "undefined" && dates.patient_refund !== null) ? dates.patient_refund : 0;
            var insurance_payment = (typeof(dates.insurance_payment) != "undefined" && dates.insurance_payment !== null) ? dates.insurance_payment : 0;
            var patient_payment = (typeof(dates.patient_payment) != "undefined" && dates.patient_payment !== null) ? dates.patient_payment : 0;
            var total_payment = (typeof(dates.total_payment) != "undefined" && dates.total_payment !== null) ? dates.total_payment : 0;

            var wa_data_array = [];

            var weekday = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
            var a = new Date(index);
            wa_data_array.push(index+" - "+weekday[a.getDay()]);
            wa_data_array.push(parseInt(total_charge));
            wa_data_array.push(""+dates.claims_count+"");
            wa_data_array.push(parseInt(writeoff_total));
            wa_data_array.push(parseInt(insurance_adjustment));
            wa_data_array.push(parseInt(patient_adjustment));
            wa_data_array.push(parseInt(insurance_refund));
            wa_data_array.push(parseInt(patient_refund));
            wa_data_array.push(parseInt(insurance_payment));
            wa_data_array.push(parseInt(patient_payment));
            wa_data_array.push(parseInt(total_payment));

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
            { s: {r:0, c:0}, e: {r:0, c:10} },{ s: {r:1, c:0}, e: {r:1, c:10} }, { s: {r:2, c:0}, e: {r:2, c:10} }, { s: {r:4, c:4}, e: {r:4, c:5} }, { s: {r:4, c:6}, e: {r:4, c:7} }, { s: {r:4, c:8}, e: {r:4, c:9} }, { s: {r:4, c:0}, e: {r:5, c:0} }, { s: {r:4, c:1}, e: {r:5, c:1} }, { s: {r:4, c:2}, e: {r:5, c:2} }, { s: {r:4, c:3}, e: {r:5, c:3} }, { s: {r:4, c:10}, e: {r:5, c:10} }
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
                    if(C <= 10 && R == 5) {
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

        var ws_name = "End_of_the_Day_Totals";
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
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'End_of_the_Day_Totals.xlsx');
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
