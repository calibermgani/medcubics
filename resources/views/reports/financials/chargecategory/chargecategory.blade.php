@extends('admin')
@section('toolbar')

<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/financials/list') }}">Billing Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Charge Category Report</span></small>
        </h1>
        <ol class="breadcrumb">
            <!-- <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li> -->
            
            
               @include('layouts.practice_module_stream_export', ['url' => 'reports/financials/chargecategoryexport/'])
                <input type="hidden" name="report_controller_name" value="FinancialController" />
                <input type="hidden" name="report_controller_func" value="chargecategorysearchExport" />
                <input type="hidden" name="report_name" value="Charge Category Report" />
         <li><a href="{{ url('reports/financials/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/adjustment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop  


@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow ">
            <div class="box-body yes-border border-green border-radius-4">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform', 'url'=>'reports/financials/chargecategoryreport', 'data-url'=>'reports/financials/chargecategoryreport']) !!}

                <?php
                    $rendering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'); 
                    $billing_provider   = App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
                    $reffering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Referring'); 
                ?> 
            
                 @include('layouts.search_fields', ['search_fields'=>$search_fields])                

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">                     

                            

                       <div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding">
                            <input type="hidden" id="pagination_prt" value="string"/>
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
$('#js-bootstrap-searchvalidator_edts').find('input:visible').each(function () {
        $(this).attr("autocomplete", "nope");
    });
    var wto = '';
    var url = $('#js-bootstrap-searchvalidator_edts').attr("action");
    $(document).ready(function(){
        getMoreFieldData();  
        $("#cpt_type .js_select_basis_change").trigger("click");
        //$("#choose_date .js_select_basis_change").trigger("click");
        $('#select_date_of_service').parent().parent().hide();
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

    $("#cpt_type .js_select_basis_change").on("click",function(){
        /*$("#facility_id").hide();
        $("#billing_provider_id").hide();
        $("#rendering_provider_id").hide();
        $("#aging_insurance_id").hide();*/
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
                    value:  "chargecategorysearchExport"
                });
                data_arr.push({
                    name : "export", 
                    value:  "xlsx"
                });
                // console.log(data_arr);
            var baseurl = '{{url('/')}}';
            var url = baseurl+"/reports/streamcsv/export/Charge_Category_Report_js";    
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
                    // console.log(user);

                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = today.getFullYear();
                    today = mm + '/' + dd + '/' + yyyy;

                    var ws_data = [
                    [practice_name],
                    ['Charge Category Report'],
                    ['User : '+user+' | '+today+' '],
                    [],
                    ['CPT/HCPCS Category','CPT/HCPCS','Description','Rendering','Units','Charge Amt($)','Payments($)','Work RVU($)']
                    ];                        

                    var charges_list = data.value.charges_list;
                    console.log(charges_list);
                    
                    function Workbook() {
                        if(!(this instanceof Workbook)) return new Workbook();
                        this.SheetNames = [];
                        this.Sheets = {};
                    }
                    // var wb = XLSX.utils.book_new();
                    var wb = new Workbook();

                    var merge = [
                        { s: {r:0, c:0}, e: {r:0, c:7} },{ s: {r:1, c:0}, e: {r:1, c:7} }, { s: {r:2, c:0}, e: {r:2, c:7} }
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
                                
                                else cell.t = 's';
                                
                                ws[cell_ref] = cell;
                            }
                        }
                        if(range.s.c < 10000000) ws['!ref'] = XLSX.utils.encode_range(range);
                        return ws;
                    }

                    var ws = sheet_from_array_of_arrays(ws_data);

                    var ws_name = "Charge_Category_Report";
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
                    saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'Charge_Category_Report.xlsx');
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
