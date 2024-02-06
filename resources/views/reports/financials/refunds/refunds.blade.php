@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/collections/list') }}" >Collection Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Refund Analysis - Detailed</span></small>

        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            
                @include('layouts.practice_module_stream_export', ['url' => 'reports/refunds/export'])
                <input type="hidden" name="report_controller_name" value="ReportController" />
                <input type="hidden" name="report_controller_func" value="refundsearchexport" />
                <input type="hidden" name="report_name" value="Refund Analysis - Detailed" />
            <li><a href="{{ url('reports/collections/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/refund_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop


@section('practice')


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow">
            <div class="box-body yes-border border-green border-radius-4">        
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform', 'url'=>'reports/search/refunds','data-url'=>'reports/search/refunds']) !!}

                <?php
					$rendering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'); 
					$billing_provider 	= App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
				?> 


                @include('layouts.search_fields', ['search_fields'=>$search_fields]) 
                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding ">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding ">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="col-lg-11 col-md-12 col-sm-11 col-xs-12 no-padding">   
                                <input type="hidden" id="pagination_prt" value="string"/>                                  
                                <input class="btn generate-btn js_filter_search_submit pull-left" tabindex="9" value="Generate Report" type="submit">                                           
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
<script>
    var wto = '';
    var url = $('#js-bootstrap-searchvalidator_edts').attr("action");
    $(document).ready(function () {
        getMoreFieldData();
        //  $("#unposted").hide();    
    });
    /* function for get data for fields Start */
    function getData() {
        clearTimeout(wto);
        var data_arr = '';
        wto = setTimeout(function () {
            $('select.auto-generate:visible').each(function () {
                data_arr += $(this).attr('name') + '=' + $(this).select2('val') + '&';
            });
            $('input.auto-generate:visible').each(function () {
                data_arr += $(this).attr('name') + '=' + $(this).val() + '&';
            });

            final_data = data_arr + "_token=" + $('input[name=_token]').val();
            getAjaxResponse(url, final_data);
        }, 100);
    }
    /* function for get data for fields End */

    /* Onchange code for field Start */
    $(document).on('click', '.js_filter_search_submit', function () {
        getData();
    });
    /* Onchange code for field End */

    /* Onchange code for more field Start */
    $(document).on('change', 'select.more_generate', function () {
        getMoreFieldData();
    });

    $("#refund_type.js_select_basis_change").on("click", function () {
//$("#include").hide();
        if ($(this).val() == 'insurance') {
            $("#insurance_id").show();
            $("select#include option[value='wallet']").remove();
        } else {
            ﻿
                    $("#insurance_id").hide();
            $('select#include').append('<option value="wallet">Wallet Refund</option>');
        }
        console.log($(this).val());
    });

/* Export PDF Function*/
// $('.js_search_export_pdf').click(function(){
//     var baseurl = '{{url('/')}}';
//     var url = baseurl+"/reports/export_pdf/refund_analysis";
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
//         value:  "refundsearchexport"
//     });
//     data_arr.push({
//         name : "report_name", 
//         value:  "Refund-Analysis-Detailed"
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

/*Ajax call for export pdf function*/
/*$('.js_search_export_pdf').click(function(){
    $('#showmenu-bar').removeClass('hide');
    var append = $('#append_report_list').html('<p id="alert-notes-msg">Refund_Analysis_Detailed.pdf<span class="progress col-md-4" style="float: right;"><span class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></span></span>');
    var baseurl = '{{url('/')}}';
    var url = baseurl+"/reports/export_pdf/refund_analysis"
    var data_arr = [];
    form = $('form').serializeArray();
    form_data = "<form id='export_pdf_form'>";
    
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
    $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
    $.ajax({
        url: url,
        type: 'POST',
        data : $(form_data).serialize(),
        success: function(data) {
            generate_report();
        }
    });
});*/
/*Ajax call end*/

/*Generate report start*/
/*function generate_report(){
    var baseurl = '{{url('/')}}';
    var url = baseurl+"/reports/export_pdf/generate_report/Refund_Analysis_Detailed";
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data){
            $.each(data, function(key, value){
                baseurl = '{{url('/')}}'
                var file_path = value.report_file_name;
                var status = '<span style="float: right;"><a style = "padding: 2px 30px; font-size:12px; border-radius : 20px;" target = "_blank" onclick="preview_report()" href="'+baseurl+'/'+file_path+'" class="btn btn-success btn-lg active" role="button" aria-pressed="true">Preview</a>';
               var append = $('#append_report_list').html('<p id="alert-notes-msg">'+value.report_name+'.pdf'+status);

            });
        }
    });
};

function preview_report(){
    $('#showmenu-bar').addClass('hide');
}*/
/*Generate report End*/

/* Export Excel Function*/
/*$('.js_search_export_csv').click(function(){
	var baseurl = '{{url('/')}}';
	var url = baseurl+"/reports/streamcsv/export/refund-analysis-detailed";
		 form = $('form').serializeArray();
         var data_arr = [];
            $('select.auto-generate:visible').each(function(){
	            //  data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';
                data_arr.push({
                    name : $(this).attr('name'), 
                    value:  $(this).select2('val')
                });
	         });       
	         $('input.auto-generate:visible').each(function(){
	            // data_arr += $(this).attr('name')+'='+$(this).val()+'&';
                data_arr.push({
                    name : $(this).attr('name'), 
                    value:  $(this).val()
                });
	         });
             data_arr.push({
                    name : "controller_name", 
                    value:  "ReportController"
                });
                data_arr.push({
                    name : "function_name", 
                    value:  "refundsearchexport"
                });
                data_arr.push({
                    name : "report_name", 
                    value:  "Refund-Analysis-Detailed"
                });
		form_data = "<form id='export_csv' method='POST' action='"+url+"'>";
        // console.log(data_arr);
		 $.each(data_arr,function(index,value){			 
             if($.isArray(value.value)) {
                 if(value.value.length > 0) {
					 var avoid = "[]"
                    form_data += "<input type='text' name='"+value.name.replace(avoid,'')+"' value='"+value.value+"'>";
                 }
             } else {
                form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
             }
		 });
         form_data  += "<input type='hidden' name='exports' value = 'xlsx'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
		 form_data += "</form>";
		//  console.log(form_data);
		 $("#export_csv_div").html(form_data);
		 $("#export_csv").submit();
		 $("#export_csv").empty();
	});		*/

</script>
@endpush   