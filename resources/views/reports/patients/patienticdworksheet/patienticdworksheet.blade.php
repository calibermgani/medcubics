@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/patients/list') }}">Patient Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>ICD Worksheet</span></small>
        </h1>
        <ol class="breadcrumb">
            
            <li class="">
                @include('layouts.practice_module_stream_export', ['url' => '/reports/filter_icdworksheet/export/'])
                <input type="hidden" name="report_controller_name" value="ReportController" />
                <input type="hidden" name="report_controller_func" value="patientIcdWorksheetExport" />
                <input type="hidden" name="report_name" value="ICD Worksheet" />
            </li>
            <li><a href="{{ url('reports/patients/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>			
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
                <!-- SEARCH FIELDS FILE -->
                {!! Form::open(['url'=>'reports/search/patienticdworksheet','onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform','data-url' =>'reports/search/patienticdworksheet' ]) !!}
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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_exit_part text-center hide">
    <input id="js_exit_part_report" class="btn btn-medcubics-small" value="Exit" type="button">
</div>
<div class="ajax_table_list hide"></div>
@stop
@push('view.scripts') 

{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}

<script type="text/javascript">
    //Search data result

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
                 console.log($(this).select2('val'));
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
</script>
@endpush
<!-- Server script end -->