@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/collections/list') }}" >Collection Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Adjustment Analysis - Detailed</span></small>
        </h1>
        <ol class="breadcrumb">
            <!-- <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li> -->
            
            
                @include('layouts.practice_module_stream_export', ['url' => 'reports/adjustment/export/'])
                <input type="hidden" name="report_controller_name" value="ReportController" />
                <input type="hidden" name="report_controller_func" value="adjustmentSearchexport" />
                <input type="hidden" name="report_name" value="Adjustment Analysis Detailed" />
            <li><a href="{{ url('reports/collections/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
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
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform', 'url'=>'reports/search/adjustment', 'data-url'=>'reports/search/adjustment']) !!}

                <?php
                    $rendering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'); 
                    $billing_provider   = App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
                    $reffering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Referring'); 
                ?> 
            
                 @include('layouts.search_fields', ['search_fields'=>$search_fields])                

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <!--<div class="form-group">
                                {!! Form::label('Transaction Date', 'Transaction Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('date_option',['enter_date' => 'Choose Date','daily' => 'Today','current_month'=>'Current Month','previous_month'=>'Previous Month','current_year'=>'Current Year','previous_year'=>'Previous Year'],@$report_data['date_option'],['class'=>'select2 form-control js_change_date_option','tabindex'=>'1']) !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('From', 'From Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', @$report_data['from_date'],['class'=>'search_start_date form-control datepicker dm-date','tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>  
                            
                            <div class="form-group">
                                {!! Form::label('Adjustment Type', 'Adjustment Type', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('insurance_charge', ['all'=>'All','insurance' => 'Insurance','patient' => 'Patient'],@$report_data['insurance_charge'],['class'=>'select2 form-control js_select_basis_change_adjusment js_select_change_adj','tabindex'=>'4', 'id' => "js_ins_adj_typ"]) !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('Insurance', 'Insurance', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('insurance', [''=>'All']+(array)@$insurance,@$report_data['insurance'],['class'=>'select2 form-control','tabindex'=>'6', 'id' => "js-insurance-adj"]) !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('Billing', 'Billing', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">                               
                                    {!! Form::select('billing_provider_id',['all'=>'All']+(array)$billing_provider,@$report_data['billing_provider_id'],['class'=>'select2 form-control js_individual_select','id'=>"js_provider",'tabindex'=>'8']) !!}
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
                                {!! Form::label('To', 'To Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('to_date', @$report_data['to_date'],['class'=>'search_end_date form-control datepicker dm-date','tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('Adjustment Reason', 'Adjustment Reason', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('adjustment_reason_id',['Patient'=>'All']+(array)@$adj_reason_patient,null,['class'=>'select2 form-control js_patient_aging js_all_hide_col','disabled']) !!}
                                    {!! Form::select('adjustment_reason_id',['Insurance'=>'All']+(array)@$adj_reason_ins,null,['class'=>'select2 form-control js_insurance_aging js_all_hide_col hide','disabled']) !!}
                                    {!! Form::select('adjustment_reason_id',['all'=>'All'],null,['class'=>'select2 form-control js_all_aging js_all_hide_col hide','disabled','tabindex'=>'5']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('facility_id',['all'=>'All']+(array)@$facilities, @$report_data['facility_id'],['class'=>'select2 form-control js_individual_select','id'=>"js_facility",'tabindex'=>'7']) !!}
                                </div>                        
                            </div>  
                            
                            <div class="form-group">
                                {!! Form::label('Rendering', 'Rendering', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('rendering_provider_id',['all'=>'All']+(array)$rendering_provider,@$report_data['rendering_provider_id'],['class'=>'select2 form-control js_individual_select','id'=>"js_provider",'tabindex'=>'9']) !!}
                                </div>                        
                            </div>-->

                            

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
    /*$(document).on('click','.js_search_export_pdf',function(){
        var baseurl = '{{url('/')}}';
        var url = baseurl+"/reports/export_pdf/adjustment-analysis-detailed"
        var data_arr = [];
        form = $('form').serializeArray();
        form_data = "<form id='export_pdf' target='_blank' method='POST' action='"+url+"'>";
        
        $('select.auto-generate:visible').each(function(){
            data_arr.push({
                name : $(this).attr('name'),
                value: ($(this).select2('val'))
            });
        });
        $('input.auto-generate:visible').each(function(){
            data_arr.push({
                name : $(this).attr('name'),
                value: ($(this).val())
            });
        });
            data_arr.push({
                name : "controller_name", 
                value:  "ReportController"
            });
            data_arr.push({
                name : "function_name", 
                value:  "paymentsearchexport"
            });
            data_arr.push({
                name : "report_name", 
                value:  "Payment-Analysis-Detailed-Report"
            });
        $.each(data_arr,function(index,value){
            if($.isArray(value.value)) {
                if(value.value.length > 0) {
                    var avoid ="[]"
                    form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
                }
            } else {
                if(value.value.length > 0) {
                    form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
                }
            }
        });
        form_data  += "<input type='hidden' name='exports' value='pdf'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
        form_data += "</form>";
        $("#export_pdf_div").html(form_data);
        $("#export_pdf").submit();
        $("#export_pdf").empty();
    });*/
   var wto = '';
	var url = $('#js-bootstrap-searchvalidator_edts').attr("action");
	$(document).ready(function(){
		getMoreFieldData();
		$("#insurance_id").hide();
		$('#select_date_of_service').parent().parent().hide();
		$('#adjustment_reason_id').find('option').remove();
		$('select#adjustment_reason_id').attr('disabled',true);   
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
	
	$("#insurance_charge.js_select_basis_change").on("click",function(){
		/*$("#facility_id").hide();
		$("#billing_provider_id").hide();
		$("#rendering_provider_id").hide();
		$("#aging_insurance_id").hide();*/
		if($(this).val() == 'insurance'){
            $("select#adjustment_reason_id").val('');
              $('select#adjustment_reason_id').trigger('change'); 
			$("#insurance_id").show();
			$('select#adjustment_reason_id').attr('disabled',false);
		   $('select#adjustment_reason_id').append('<option value="0">CO253</option><option value="CO45">CO45</option><?php foreach($adj_reason_ins as $key=>$val){ echo '<option value="'.$key.'">'.trim($val).'</option>'; }?>');
			$("select#adjustment_reason_id option[value='Patient']").remove(); 
		  
			<?php foreach($adj_reason_patient as $key=>$val){ ?> 
			$("select#adjustment_reason_id option[value='<?php echo $key;?>']").remove();
			<?php }?>
		}else if($(this).val() == 'self'){
            $("select#adjustment_reason_id").val('');
              $('select#adjustment_reason_id').trigger('change'); 
			$("#insurance_id").hide();
			$('select#adjustment_reason_id').attr('disabled',false);
			$('select#adjustment_reason_id').append('<?php foreach($adj_reason_patient as $key=>$val){ echo '<option value="'.$key.'">'.trim($val).'</option>'; }?>'); 
			$("select#adjustment_reason_id option[value='Insurance']").remove(); 
			$("select#adjustment_reason_id option[value='0']").remove(); 
			$("select#adjustment_reason_id option[value='CO45']").remove(); 
		  
			<?php foreach($adj_reason_ins as $key=>$val){ ?> 
			$("select#adjustment_reason_id option[value='<?php echo $key;?>']").remove();
			<?php }?>
		}else if($(this).val() == 'all'){
		      $("select#adjustment_reason_id").val('');
              $('select#adjustment_reason_id').trigger('change'); 
			$("select#adjustment_reason_id option[value='Insurance']").remove();
			$("select#adjustment_reason_id option[value='0']").remove();
			$("select#adjustment_reason_id option[value='CO45']").remove();     
			<?php foreach($adj_reason_ins as $key=>$val){ ?> 
			$("select#adjustment_reason_id option[value='<?php echo $key;?>']").remove();
			<?php }?> 

			$("select#adjustment_reason_id option[value='Patient']").remove();     
			<?php foreach($adj_reason_patient as $key=>$val){ ?> 
			$("select#adjustment_reason_id option[value='<?php echo $key;?>']").remove();
			<?php }?>
			$('select#adjustment_reason_id').attr('disabled',true);
			 $("#insurance_id").hide();
		}
    });
    $(document).on('click','.js_search_export_raw', function(){ 
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
                    value:  "adjustmentSearchexport"
                });
                data_arr.push({
                    name : "export", 
                    value:  "xlsx"
                });
                data_arr.push({
                    name : "type", 
                    value:  "js-raw"
                });
                // console.log(data_arr);
             var baseurl = '{{url('/')}}';
            var url = baseurl+"/reports/streamcsv/export/adjustment-analysis-detailed-js";    
            processingImageShow(".box-view","show");
            $.ajax({
                type: 'POST',
                url: url,
                data:  data_arr,
                success: function(response) {
                    var myObject = JSON.parse( response );
                    var data = myObject.original;
                    // console.log(data);
                    // $("#export_csv_div").html(myObject.original.html);
                    // console.log(data);

                    var user = "<?php $user = Auth::user()->name; echo $user; ?>";
    // console.log(user);

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = mm + '/' + dd + '/' + yyyy;

        var ws_data = [
        ['Test Practice'],
        ['Adjustment Analysis - Detailed'],
        ['User : '+user+' | '+today+' '],
        [],
        ['Claim No', 'Patient Name', 'Acc No', 'Responsibility', 'Billing', 'Rendering', 'Facility', 'Payer', 'Adj Date', 'DOS', 'CPT', 'Adj Reason', 'CPT Adj($)', 'Tot Adj($)', 'Reference', 'User']
        ];

        $.each( data.value, function( i, data_list ) {
            if(jQuery.isArray(data_list)) {
                $.each(data_list, function(index, value) {
                    ws_data.push(value);
                });
            }
        });
// console.log(ws_data);
        function Workbook() {
            if(!(this instanceof Workbook)) return new Workbook();
            this.SheetNames = [];
            this.Sheets = {};
        }

        var wb = new Workbook();

        var merge = [
            { s: {r:0, c:0}, e: {r:0, c:15} },{ s: {r:1, c:0}, e: {r:1, c:15} }, { s: {r:2, c:0}, e: {r:2, c:15} }
            ];


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

                        if(typeof cell.v === 'number') {
                            
                            if(cell_ref.search("Q") !== -1) {
                                cell.v = "";
                            }
                            else if(cell_ref.search("N") !== -1) {
                                cell.t = 'n';
                                if(cell.v < 0 ) { 
                                    cell.s = {numFmt : "0.00", font : {sz : "9", color: { rgb: "FF0000"} }, alignment : { horizontal: "center", vertical : "center" } } 
                                }
                                else { 
                                    cell.s = {numFmt : "0.00", font : {sz : "9" }, alignment : { horizontal: "center", vertical : "center" } } 
                                }
                                if(data[R].slice(-1)[0] !== "") {
                                    if(data[R].slice(-1)[0] == 1) {

                                    }else {
                                        var colmrg = data[R].slice(-1)[0] - 1;
                                        merge.push({ s: {r:R, c:C}, e: {r:R + colmrg, c:C} });
                                    }
                                }else {
                                }
                                
                            } else {                                
                                cell.t = 'n';
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
            // var wscols = [
            //     {wch:6},
            //     {wch:7},
            //     {wch:10},
            //     {wch:20}
            // ];
            // ws['!cols'] = wscols;
            return ws;
        }
        
        ws_data.push([""]);
        ws_data.push(["Summary"]);
        if(data.instype == "insurance") {
            ws_data.push(["Total Insurance Adjustments", data.sum_tot_adjs_ins]);
        }
        if(data.instype == "self") {
            ws_data.push(["Total Patient Adjustments", data.sum_tot_adjs_pat]);
        }
        if(data.instype == "all") {
            ws_data.push(["Total Insurance Adjustments", data.sum_tot_adjs_ins]);
            ws_data.push(["Total Patient Adjustments", data.sum_tot_adjs_pat]);
            ws_data.push(["Total Adjustments", data.sum_tot_adjs]);
        }
        ws_data.push([""]);
        ws_data.push(["Copyright \u00A9 "+yyyy+" Medcubics. All rights reserved."]);

        var ws = sheet_from_array_of_arrays(ws_data);

        var ws_name = "Adjustment_Analysis_Detailed";
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
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'Adjustment_Analysis_Detailed.xlsx');
        processingImageShow(".box-view","hide");
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    js_sidebar_notification('error', "Currently unable to proccess excel");
                    console.log(errorThrown);
                }
                });
    });     

    
</script>   
@endpush