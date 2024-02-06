@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-line-chart font16"></i> Appointment List </small> 
        </h1>
        <ol class="breadcrumb">          
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @if(count(@$app_list) > 0 )
            <li class="dropdown messages-menu">
                @include('layouts.practice_module_export', ['url' => 'api/schedulerlistreports'])
            </li>
            @endif
            <li><a href="#js-help-modal" data-url="{{url('help/appointmentlist')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>

			<!--li><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
			<li><a href="#js-help-modal" data-url="" class="js-help" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li-->
        </ol>
    </section>
</div>
@stop
@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">

        <div class="box box-view no-shadow">        
            <div class="box-body yes-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
              
            <!--    <h3 class="san-heading p-l-2 margin-t-m-10 margin-b-25" style="font-size:28px !important;">Appointment Filter</h3> -->
                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part margin-t-10">

                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group">
                                {!! Form::label('Patient', 'Patient', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-9">
                                   {!! Form::select('patient_id', array(''=>'Patient')+(array)$patients,  NULL,['class'=>'form-control select2 js_filter_search']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Provider', 'Provider', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                   {!! Form::select('provider_id', array(''=>'Provider')+(array)$provider,  @$time_arr,['class'=>'form-control select2 js_filter_search']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('facility_id', array(''=>'Facility')+(array)$facility,  @fac_id,['class'=>'form-control select2 js_filter_search']) !!}
                                </div>                        
                            </div>                            
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group">
                                {!! Form::label('Status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    <select class="form-control select2 js_filter_search" name="status">
                                        <option value="">Status</option>
                                        <option value="Scheduled">Scheduled</option>
                                        <option value="Complete">Complete</option>
                                        <option value="Rescheduled">Rescheduled</option>
                                        <option value="Canceled">Canceled</option>
                                        <option value="No Show">No Show</option>
                                    </select>
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Date', 'Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    <select class="form-control select2 js_filter_search">
                                        <option value="">Date</option>
                                        <option>Today</option>
                                        <option>This Week</option>
                                        <option>This Month</option>
                                        <option>Prev Week</option>
                                        <option>Prev Month</option>
                                    </select>
                                </div>                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">          
        <p class="pull-right no-bottom">
            <span class="med-green font600">View :</span> 
            <a href="#" class="js_view_port active_list" id="table"  data-placement="bottom"  data-toggle="tooltip" data-original-title="Table" style="margin-left:10px;"><i class="fa fa-align-justify font14"></i> </a> 
            <a href="#" class="js_view_port" id="list"  data-placement="bottom"  data-toggle="tooltip" data-original-title="List" style="margin-left:10px;"><i class="fa fa-th-list font14"></i> </a> 
            <a href="#" class="js_view_port" id="grid"  data-placement="bottom"  data-toggle="tooltip" data-original-title="Grid" style="margin-left:10px;"> <i class="fa fa-th font14"></i> </a></a>
        </p>
    </div>
</div>
<div id="js_loading_image" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center med-green margin-t-10 font13 hide">
    <i class="fa fa-spinner fa-spin med-green"></i> Processing
    {!! Form::open(['method'=>'POST','class'=>'all_values','name'=>'all_values']) !!}
    <div class="all_values_input"></div>
    {!! Form::close() !!}
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_add_detail">
    @include ('scheduler/listing/listview')
</div>
<div id="eligibility_content_popup" class="modal fade in">
    @include ('layouts/eligibility_modal_popup')
</div>
<!--End-->
@stop

@push('view.scripts')
<script>
    /*** Tooltip for mouseover starts ***/
    var tooltipTimeout;
    $(document).on({
        mouseenter: function () {
            var id = $(this).data('id');
            var type = $(this).data('type');
            var uniqueid = $(this).data('uniqueid');
            tooltip1Timeout = setTimeout(function () {
                showTooltip(id, uniqueid, type)
            }, 700);
        },
        mouseleave: function () {
            hideTooltip();
        }
    }, '.js_patient_hover');

    function showTooltip(id, uniqueid = '', type = '') {
        var tooltip1 = $("<div id='tooltip1' class='tooltip1'>" + $('.js-tooltip' + type + '_' + uniqueid + id).html() + "</div>");
        tooltip1.appendTo($("#someelem" + type + uniqueid + id));
    }

    function hideTooltip() {
        clearTimeout(tooltip1Timeout);
        $("#tooltip1").fadeOut().remove();
    }
    /*** Tooltip for mouseover ends ***/
</script>
@endpush