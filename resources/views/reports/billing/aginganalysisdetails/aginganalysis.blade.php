@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Billing Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Aging Analysis</span></small>

        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="{{ url('reports/billing/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
			    @include('layouts.practice_module_export', ['url' => 'billing/aginganalysisdetailsreports/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/claim_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
        <div class="box box-view no-shadow ">

            <div class="box-body yes-border form-horizontal" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform', 'url'=>'reports/billing/search/aginganalysisdetails']) !!}

                @php
					$rendering_provider = App\Models\Provider::typeBasedProviderlist('Rendering'); 
					$billing_provider 	= App\Models\Provider::typeBasedProviderlist('Billing'); 
				@endphp 

                <h3 class="san-heading p-l-2 margin-t-m-10 margin-b-25" style="font-size:28px !important;">Aging Analysis</h3>



                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                        <div class="form-group">
                            {!! Form::label('Claim By', 'Claim By', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
                            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                {!! Form::select('claim_by', ['create_by'=>'Created By','submited_by' => 'Submited By'],null,['class'=>'select2 form-control']) !!}
                            </div>                        
                        </div>
						<div class="form-group">
                            {!! Form::label('Aging Days', 'Aging Days', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
                            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                {!! Form::select('aging_days', ['all'=>'All','0-30' => '0-30','31-60'=>'31-60','61-90'=>'61-90','91-120'=>'91-120','121-150'=>'121-150','150-above'=>'> 150'],null,['class'=>'select2 form-control']) !!}
                            </div>                        
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('Aging By', 'Aging By', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
                            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                {!! Form::select('aging_by', ['all'=>'All','patient' => 'Patient','rendering_provider' => 'Rendering Provider','billing_provider'=>'Billing Provider','facility'=>'Facility','insurance'=>'Insurance'],null,['class'=>'select2 form-control  js_select_basis_change']) !!}
                            </div>                        
                        </div>
                        
                        <div class="form-group hide">
                            {!! Form::label('', '', ['class'=>'col-lg-4 col-md-5 col-sm-6 col-xs-12 control-label']) !!}
                            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">
                                {!! Form::select('patient', ['all'=>'All','insurance' => 'Insurance','patient' => 'Patient'],null,['class'=>'select2 form-control  ']) !!}
                            </div>                        
                        </div>
                        
                        <div class="form-group js_all_hide_col js_rendering_provider_aging hide">
                            {!! Form::label('Rendering', 'Rendering', ['class'=>'col-lg-4 col-md-5 col-sm-6 col-xs-12 control-label']) !!}
                            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">
                                {!! Form::select('rendering_provider_id',['all'=>'All']+(array)$rendering_provider ,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider"]) !!}
                            </div>
                        </div>
                        <div class="form-group-billing js_all_hide_col js_billing_provider_aging hide">
                            {!! Form::label('Billing', 'Billing', ['class'=>'col-lg-4 col-md-5 col-sm-6 col-xs-12 control-label']) !!}
                            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">
                                {!! Form::select('billing_provider_id',['all'=>'All']+(array)$billing_provider,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider"]) !!}
                            </div>
                        </div>
                        <div class="form-group-billing margin-t-10 js_all_hide_col js_facility_aging hide">
                            {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-4 col-md-5 col-sm-6 col-xs-12 control-label']) !!}
                            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">
                                {!! Form::select('facility_id',['all'=>'All']+(array)@$facilities,"all",['class'=>'select2 form-control js_individual_select']) !!}
                            </div>
                        </div>
                        <div class="form-group-billing margin-t-10 js_all_hide_col js_insurance_aging hide">
                            {!! Form::label('Insurance', 'Insurance', ['class'=>'col-lg-4 col-md-5 col-sm-6 col-xs-12 control-label']) !!}
                            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">
                                {!! Form::select('insurance_id',['all'=>'All']+(array)@$insurance,"all",['class'=>'select2 form-control input-sm-header-billing']) !!}
                            </div>
                        </div>
                        <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12 no-padding">
                            <input class="btn btn-medcubics-small js_filter_search_submit pull-right" value="Search" type="submit">
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
    <input class="btn btn-medcubics-small" id="js_exit_part" value="Exit" type="button">
</div>
@stop