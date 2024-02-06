@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Financial Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Provider Reimbursement Analysis</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('reports/financials/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                <ul class="dropdown-menu" style="margin-top: 3px; display: none;">
                    <li>
                        <ul class="menu" style="list-style-type:none; ">
                            <li>
                                <a href="{!! url('reports/financials/provider-reimbursement-export/xlsx') !!}" data-url="{!! url('reports/financials/provider-reimbursement-export') !!}" data-option="xlsx" class="set_parameter">
                                    <i class="fa fa-file-excel-o"></i> Excel
                                </a>
                            </li>
                            <li>
                                <a href="{!! url('reports/financials/provider-reimbursement-export/pdf') !!}" data-url="{!! url('reports/financials/provider-reimbursement-export') !!}" !!}" data-option="pdf" class="set_parameter">
                                    <i class="fa fa-file-pdf-o" data-placement="right" data-toggle="tooltip" data-original-title="pdf"></i> PDF
                                </a>
                            </li>
                            <li>
                                <a href="{!! url('reports/financials/provider-reimbursement-export/csv') !!}" data-url="{!! url('reports/financials/provider-reimbursement-export') !!}" data-option="csv" class="set_parameter">
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
    <div class="col-lg-9 col-md-10 col-sm-12 col-xs-12">
        <div class="box box-view no-shadow ">

            <div class="box-body yes-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edt', 'name'=>'medcubicsform', 'url'=>'reports/financials/provider-reimbursement-filter']) !!}

                <h3 class="san-heading p-l-2 margin-t-m-10 margin-b-25" style="font-size:28px !important;">Provider Reimbursement Analysis</h3>

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">



                            <div class="form-group">
                                {!! Form::label('Transaction Date', 'Transaction Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('date_option', $groupby,null,['class'=>'select2 form-control js_change_date_option_edt','tabindex'=>'1']) !!}
                                </div> 

                            </div>

                            <div class="form-group">
                                {!! Form::label('From', 'From Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', null,['class'=>'search_start_date form-control datepicker dm-date','tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>
                                <input type="hidden" name="hidden_from_date" />
                            </div> 

                            <div class="form-group">
                                {!! Form::label('Rendering Provider', 'Rendering Provider', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('rendering_provider', (array)$rendering_list,null,['class'=>'select2 form-control js_select_basis_change','tabindex'=>'4']) !!}
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
                                {!! Form::label('To', 'To Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-9">
                                    {!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>   
                                <input type="hidden" name="hidden_to_date" />
                            </div>

                            <div class="form-group">
                                {!! Form::label('Billing Provider', 'Billing Provider', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-9">
                                    {!! Form::select('billing_provider', (array)$billing_list,null,['class'=>'select2 form-control js_select_basis_change','tabindex'=>'4']) !!}
                                </div>                        
                            </div>

                            <div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding">
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