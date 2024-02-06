@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Patient Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Patient Aging Analysis</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('reports/patients/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			<li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'filter_ar/'])
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
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edt', 'name'=>'medcubicsform', 'url'=>'reports/patientarlist/filter_ar']) !!}

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group">
                                {!! Form::label('Group By', 'Transaction Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}								
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('date_option',$groupby,null,['class'=>'select2 form-control js_change_date_option_edt','tabindex'=>'1']) !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('From', 'From Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', null,['class'=>'search_start_date form-control datepicker dm-date','tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
									{!! Form::hidden('hidden_from_date', null,['tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>  
							

                        </div>



                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group-billing">
							{!! Form::label('Aging Days', 'Aging Days', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}                                                    
							<div class="col-lg-7 col-md-7 col-sm-6 col-xs-9 ">
                            {!! Form::select('aging_days', ['all'=>'All','0-30' => '0-30','31-60'=>'31-60','61-90'=>'61-90','91-120'=>'91-120','121-150'=>'121-150','150-above'=>'> 	150'],null,['class'=>'select2 form-control  ']) !!}
							</div> 
															
							</div>
                            <div class="form-group">
                                {!! Form::label('To', 'To Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-9">
                                    {!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
									{!! Form::hidden('hidden_to_date', null,['tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>
							

                            <div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding">
                                <input class="btn btn-medcubics-small js_filter_search_submit pull-right" tabindex="10" value="Generate Report" type="submit">
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
