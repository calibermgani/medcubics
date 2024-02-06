@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/practicesettings/list') }}">Practice Indicators</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Payer Summary</span></small>
        </h1>
        <ol class="breadcrumb">
            
            <li class="dropdown messages-menu hide js_claim_export">
                @include('layouts.practice_module_stream_export', ['url' => 'reports/filter_insurance_result/export'])
                <input type="hidden" name="report_controller_name" value="InsurancelistController" /> 
                <input type="hidden" name="report_controller_func" value="insuranceListExport" />
                <input type="hidden" name="report_name" value="Payer Summary" />
            </li>
            <li><a href="{{ url('reports/practicesettings/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>			
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
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform', 'url'=>'reports/practicesettings/filter_insurance_result','data-url'=>'reports/practicesettings/filter_insurance_result']) !!}
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
<script>
    var wto = '';
    var url = $('#js-bootstrap-searchvalidator_edts').attr("action");
    $('#sort_list_noorder_report').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "searchHighlight"   : true,
        "ordering": true,
        "info": true,
        //"fixedHeader": true,
        "responsive": true,
        //"autoWidth": true,
        "order": [3, 'desc'],
        "columnDefs": [{ "orderable": false, "targets": 0 } ],
        "fnDrawCallback": function(settings) {
            $('#sort_list_noorder_report td').unhighlight();
            if ($('.dataTables_filter input').val() != "") {
                var selector_name = $("#sort_list_noorder_report tr td");
                var str = $('.dataTables_filter input').val();
                selector_name.highlight($.trim(str));
            }
			hideLoadingImage(); // Hide loader once content get loaded.
        }                   
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
  
</script>
@endpush  