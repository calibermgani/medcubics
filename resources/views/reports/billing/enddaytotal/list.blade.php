@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Billing Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>End of the Day Totals Analysis</span></small>
        </h1>
        <ol class="breadcrumb">
            
            <li><a href="{{ url('reports/billing/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			.
            <li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
			
			<ul class="dropdown-menu" style="margin-top: 3px; display: none;">
				<li>
					<ul class="menu" style="list-style-type:none; ">
						<li>
							<a href="{!! url('reports/billing/enddayexport/xlsx') !!}" data-url="{!! url('reports/billing/enddayexport') !!}" data-option="xlsx" class="set_date">
								<i class="fa fa-file-excel-o"></i> Excel
							</a>
						</li>
						<li>
							<a href="{!! url('reports/billing/enddayexport/pdf') !!}" data-url="{!! url('reports/billing/enddayexport') !!}" !!}" data-option="pdf" class="set_date">
								<i class="fa fa-file-pdf-o" data-placement="right" data-toggle="tooltip" data-original-title="pdf"></i> PDF
							</a>
						</li>
						<li>
							<a href="{!! url('reports/billing/enddayexport/csv') !!}" data-url="{!! url('reports/billing/enddayexport') !!}" data-option="csv" class="set_date">
								<i class="fa fa-file-code-o" data-placement="right" data-toggle="tooltip" data-original-title="csv"></i> CSV
							</a>
						</li>
					</ul>
				</li>
			</ul>

			
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/adjustment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  id="js_ajax_part">
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
        <div class="box box-view no-shadow ">

            <div class="box-body yes-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edt', 'name'=>'medcubicsform', 'url'=>'reports/billing/filter_result']) !!}

                <h3 class="san-heading p-l-2 margin-t-m-10 margin-b-25" style="font-size:28px !important;">End of the Day Totals Analysis</h3>

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group">
                                {!! Form::label('Group By', 'Group By', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('date_option',$groupby,null,['class'=>'select2 form-control js_change_date_option_edt','tabindex'=>'1']) !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('From', 'From Date', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', null,['class'=>'search_start_date form-control datepicker dm-date','tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
									{!! Form::hidden('hidden_from_date', null,['tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>  

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group  margin-b-18">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
                                    {!! Form::label('', '', ['class'=>'control-label']) !!}
                                </div>                                                        
                            </div> 

                            <div class="form-group">
                                {!! Form::label('To', 'To Date', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
									{!! Form::hidden('hidden_to_date', null,['tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>

                            <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <input class="btn btn-medcubics-small js_filter_search_submit pull-right" tabindex="10" value="Search" type="submit">
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