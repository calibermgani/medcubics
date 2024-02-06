@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Billing Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Outstanding AR</span></small>

        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="{{ url('reports/billing/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/facilityreports/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/claim_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  id="js_ajax_part">
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
        <div class="box box-view no-shadow ">

            <div class="box-body yes-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform', 'url'=>'reports/search/claims']) !!}

                @php
					$rendering_provider = App\Models\Provider::typeBasedProviderlist('Rendering'); 
					$billing_provider 	= App\Models\Provider::typeBasedProviderlist('Billing'); 
					$reffering_provider = App\Models\Provider::typeBasedProviderlist('Referring'); 
				@endphp  


                <h3 class="san-heading p-l-2 margin-t-m-10 margin-b-25" style="font-size:28px !important;">Outstanding AR</h3>

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group">
                                {!! Form::label('Group By', 'Group By', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('original_billed_date', ['all'=>'-- All --','entry_date' => 'Original Billed Date','date_of_service' => 'Date of Service','paid_date'=>'Paid Date','created_at'=>'Claim Created Date','last_submited_date'=>'Last Submission'],null,['class'=>'select2 form-control js_group_by','tabindex'=>'1']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('From', 'From Date', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', null,['class'=>'search_start_date form-control input-sm-modal-billing datepicker dm-date','disabled','placeholder'=>Config::get('siteconfigs.default_date_format'),'tabindex'=>'2'])  !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Insurance Type', 'Insurance Type', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('insurance_group',['all'=>'All']+(array)@$insurance_type,"all",['class'=>'select2 form-control input-sm-header-billing js_insurance_type','tabindex'=>'4']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Billed To', 'Billed To', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('insurance_category', ['all'=>'All','primary' => 'Primary','secondary' => 'Secondary','tertiary'=>'Tertiary'],null,['class'=>'select2 form-control','tabindex'=>'6']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Billing', 'Billing', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('billing_provider_id',['all'=>'All']+(array)$billing_provider,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider",'tabindex'=>'8']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Balance', 'Balance', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('balance_option', ['all'=>'All','insurance' => 'Insurance Balance','patient' => 'Patient Balance','partially_pending'=>'Partially Pending'],null,['class'=>'select2 form-control','tabindex'=>'10']) !!}
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
                                    {!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','disabled','placeholder'=>Config::get('siteconfigs.default_date_format'),'tabindex'=>'3'])  !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Insurance', 'Insurance', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('insurance_id',['all'=>'All']+(array)@$insurance,"all",['class'=>'select2 form-control','tabindex'=>'5']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('facility_id',['all'=>'All']+(array)@$facilities,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_facility",'tabindex'=>'7']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Rendering', 'Rendering', ['class'=>'col-lg-4 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('rendering_provider_id',['all'=>'All']+(array)$rendering_provider,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider",'tabindex'=>'9']) !!}
                                </div>                        
                            </div>

                            <div class="form-group no-bottom">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <input type="hidden" name="list_type" value="normal" />
                                    {!! Form::checkbox('list_type','line_items',null,['class'=>'flat-red','id'=>'line-item','tabindex'=>'11']) !!} 
                                    <label for="line-item" class="med-orange font600">Show Line Items</label>
                                </div>
                            </div>

                            <div class="col-lg-11 col-md-12 col-sm-10 col-xs-8 no-padding">                                            
                                <input class="btn btn-medcubics-small js_filter_search_submit pull-right m-r-m-3" tabindex="12" value="Search" type="submit">                                           
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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_claim_list_part hide"></div>
</div>
@stop