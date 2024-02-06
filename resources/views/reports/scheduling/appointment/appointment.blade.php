@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Appointment Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Appointment Analysis</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="{{ url('reports/appointments/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'appointment/'])
            </li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="box box-view no-shadow">
        <div class="box-header-view">
            <i class="fa fa-calendar" data-name="info"></i> <h3 class="box-title">Appointment Analysis</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body form-horizontal">
            {!! Form::open(['onsubmit'=>"event.preventDefault();",
            'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform', 
            'url'=>'reports/scheduling/search/appointment','data-url'=>'reports/scheduling/search/appointment']) !!}

            @php 
				$facility_list	=  	App\Models\Facility::allFacilityShortName('name');//Getting all facilities detail
				$provider_list	=  	App\Models\Provider::allProviderShortName('name');//Getting all provider detail
			@endphp
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js_search_part">
                <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 tab-r-b-1 border-green">
                    <div class="form-group-billing">
                        {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-8 select2-white-popup">
                            {!! Form::select('facility_id',['all'=>'All']+(array)@$facility_list,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_facility"]) !!}
                        </div>
                    </div>
                    <div class="form-group-billing">
                        {!! Form::label('Provider', 'Provider', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-8 select2-white-popup">
                            {!! Form::select('provider_id',['all'=>'All']+(array)$provider_list,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider"]) !!}
                        </div>
                    </div>
                    <div class="form-group-billing">
                        {!! Form::label('Non Billable Visit', 'Non Billable Visit', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                            {!! Form::select('non_billable_visit', ['yes' => 'Yes','no'=>'No'],null,['class'=>'select2 form-control']) !!}
                        </div>                        
                    </div>
                    <div class="form-group-billing">
                        {!! Form::label('Patient', 'Patient', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                            {!! Form::select('patient_option', [''=>'All','Yes' => 'New','No'=>'Existing'],null,['class'=>'select2 form-control']) !!}
                        </div>                        
                    </div>
                    <div class="form-group-billing">
                        {!! Form::label('Coverage Status', 'Coverage Status', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                            {!! Form::select('coverage_status', ['' => 'All','No' => 'Insured','Yes'=>'Self'],null,['class'=>'select2 form-control']) !!}
                        </div>                        
                    </div>                                                         
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">                                      
                        {!! Form::label('Date Option', 'Date Option', ['class'=>'col-lg-4 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="col-lg-8 col-md-7 col-sm-7 col-xs-10">
                            {!! Form::radio('charge_date_option','enter_date',true,['class'=>'flat-red']) !!} Created &emsp;
                            {!! Form::radio('charge_date_option', 'choose_date',null,['class'=>'flat-red']) !!} Appointment
                        </div>                    
                    </div>
                    <div class="form-group-billing">
                        {!! Form::label('Transaction Date', 'Transaction Date', ['class'=>'col-lg-4 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 select2-white-popup">
                            {!! Form::select('date_option', ['enter_date' => 'Choose Date','daily' => 'Today','current_month'=>'Current Month','previous_month'=>'Previous Month','current_year'=>'Current Year','prev_year'=>'Previous Year'],null,['class'=>'select2 form-control  js_change_date_option']) !!}								
                        </div>                        
                    </div>
                    <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                        <div class="form-group-billing">
                            {!! Form::label('', 'From', ['class'=>'col-lg-4 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 select2-white-popup">
                                <i class="fa fa-calendar-o form-icon-billing"></i> 
                                {!! Form::text('from_date', null,['class'=>'search_start_date form-control  datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                            </div>                        
                        </div>
                        <div class="form-group-billing">
                            {!! Form::label('', 'To', ['class'=>'col-lg-4 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 select2-white-popup">
                                <i class="fa fa-calendar-o form-icon-billing"></i> 
                                {!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                            </div>                        
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 tab-l-b-1 border-green">
                    {!! Form::label('', 'Status', ['class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 control-label margin-l-m-13 margin-b-10']) !!}  
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-bottom no-padding">
                        <div class="form-group">                                                                 
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 line-height-24">
                                {!! Form::checkbox('status_option[]', 'all',true,['id'=>'js_select_status','class'=>'flat-red js_menu js_status_option']) !!}&nbsp;All&emsp;<br>
                                {!! Form::checkbox('status_option[]', 'Scheduled',true,['class'=>'js_select_status flat-red js_submenu js_status_option']) !!}&nbsp;Scheduled&emsp;<br>
                                {!! Form::checkbox('status_option[]', 'Confirmed',true,['class'=>'js_select_status flat-red js_submenu js_status_option']) !!}&nbsp;Confirmed&emsp;<br>
                                {!! Form::checkbox('status_option[]', 'Not Confirmed',true,['class'=>'js_select_status flat-red js_submenu js_status_option']) !!}&nbsp;Not Confirmed&emsp; <br>
                                {!! Form::checkbox('status_option[]', 'Arrived',true,['class'=>'js_select_status flat-red js_submenu js_status_option']) !!}&nbsp;Arrived&emsp; <br>                        
                            </div>                    
                        </div>                                              
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-bottom no-padding">
                        <div class="form-group">                                                                 
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 line-height-24">
                                {!! Form::checkbox('status_option[]', 'In Session',true,['class'=>'js_select_status flat-red js_submenu js_status_option']) !!}&nbsp; In Session&emsp; <br>
                                {!! Form::checkbox('status_option[]', 'Complete',true,['class'=>'js_select_status flat-red js_submenu js_status_option']) !!}&nbsp; Complete&emsp; <br>
                                {!! Form::checkbox('status_option[]', 'Rescheduled',true,['class'=>'js_select_status flat-red js_submenu js_status_option']) !!}&nbsp; Rescheduled&emsp;<br>
                                {!! Form::checkbox('status_option[]', 'Cancelled',true,['class'=>'js_select_status flat-red js_submenu js_status_option']) !!}&nbsp; Cancelled&emsp; <br>
                                {!! Form::checkbox('status_option[]', 'No Show',true,['class'=>'js_select_status flat-red js_submenu js_status_option']) !!}&nbsp; No Show&emsp; <br>
                            </div>                    
                        </div>                                              
                    </div>
                </div>
                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-12">                   
                    <input class="btn btn-medcubics-small js_filter_search_submit pull-right" value="Search" type="submit">                  
                </div>
            </div>
            {!! Form::close() !!}
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
    <input id="js_exit_part" class="btn btn-medcubics-small" value="Exit" type="button">
</div>
@stop
@push('view.scripts1')
<script type="text/javascript">
$(document).on('change', '.js_change_date_option', function (e) {
    var current_val = $(this).val();
    if (current_val == "enter_date" || current_val == "" || typeof current_val == "undefined") {
        var str_date = '';
        var end_date = '';
    } else {
        var str_date = getStartDate(current_val);
        var end_date = getEndDate(current_val);
    }
    if ($(".search_start_date").length > 0) {
        $(".search_start_date").val(str_date);
        $(".search_end_date").val(end_date);
        $(".search_start_date,.search_end_date").trigger("keyup");
        if (current_val != "enter_date" && current_val != "" && typeof current_val != "undefined") {
            $(".search_start_date,.search_end_date").attr("disabled", 'disabled').removeClass("datepicker dm-date hasDatepicker");
        } else {
            $(".search_start_date,.search_end_date").removeAttr("disabled").addClass("datepicker dm-date hasDatepicker");
        }
        $('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_start_date'));
        $('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_end_date'));
    }
});

function getStartDate(date_option) {
    var d = new Date();
    switch (date_option) {
        case "daily":
            var strDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
            break;

        case "current_month":
            var date = new Date(d.getFullYear(), (d.getMonth()), 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        case "previous_month":
            var date = new Date(d.getFullYear(), (d.getMonth() - 1), 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        case "current_year":
            var date = new Date(d.getFullYear(), 0, 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        case "previous_year":
            var date = new Date(d.getFullYear() - 1, 0, 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        default:
            var strDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
    }
    return MakeDate(strDate);
}

function getEndDate(date_option) {
    var d = new Date();
    switch (date_option) {
        case "daily":
            var endDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
            break;

        case "current_month":
            var endDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
            break;

        case "previous_month":
            var date = new Date(d.getFullYear(), (d.getMonth()), 0);
            var endDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        case "current_year":
            var endDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
            break;

        case "previous_year":
            var date = new Date(d.getFullYear(), 0, 0);
            var endDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        default:
            var endDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
    }
    return MakeDate(endDate);
}

function MakeDate(date_value) {
    var date = date_value.split("/");
    date[0] = ((date[0]) < 10) ? "0" + date[0] : date[0];
    date[1] = ((date[1]) < 10) ? "0" + date[1] : date[1];
    var return_date = date.join("/");
    return return_date;
}
</script>
@endpush
