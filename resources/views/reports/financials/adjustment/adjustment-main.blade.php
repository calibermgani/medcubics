@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Adjustment Analysis Report</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/facilityreports/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/adjustment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  id="js_ajax_part">
	<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 p-l-0">
		<div class="box box-view no-shadow ">

			<div class="box-body yes-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
				{!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform', 'url'=>'reports/search/adjustment', 'data-url'=>'reports/search/adjustment']) !!}

				<?php
					$rendering_provider = App\Models\Provider::typeBasedProviderlist('Rendering'); 
					$billing_provider 	= App\Models\Provider::typeBasedProviderlist('Billing'); 
					$reffering_provider = App\Models\Provider::typeBasedProviderlist('Referring'); 
				?> 
				<h3 class="san-heading p-l-2 margin-t-m-10 margin-b-20">Adjustment Analysis</h3>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
					<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
						<div class="form-group">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
								{!! Form::label('Transaction Date', 'Transaction Date', ['class'=>'control-label']) !!}
								{!! Form::select('date_option', ['enter_date' => 'Enter Date','daily' => 'Daily','current_month'=>'Current Month','previous_month'=>'Previous Month','current_year'=>'Current Year','previous_year'=>'Previous Year'],null,['class'=>'select2 form-control js_change_date_option']) !!}                           
							</div>                                                        
						</div> 

						<div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
							<div class="form-group">
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
									{!! Form::label('From', 'From', ['class'=>'control-label']) !!}
									{!! Form::text('from_date', null,['class'=>'search_start_date form-control datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
								</div>                                                        
							</div>

							<div class="form-group">
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
									{!! Form::label('To', 'To', ['class'=>'control-label']) !!}
									{!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
								</div>                                                        
							</div>
						</div>

						<div class="form-group">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
								{!! Form::label('Adjustment Type', 'Adjustment Type', ['class'=>'control-label']) !!}
								{!! Form::select('insurance_type', ['all'=>'All','insurance' => 'Insurance','patient' => 'Patient'],null,['class'=>'select2 form-control js_select_basis_change']) !!}
							</div>                                                        
						</div>

						<div class="form-group">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
								{!! Form::label('Adjustment Reason', 'Adjustment Reason', ['class'=>'control-label']) !!}
								{!! Form::select('adjustment_reason_id',['Patient'=>'All']+(array)@$adj_reason_patient,null,['class'=>'select2 form-control js_patient_aging js_all_hide_col','disabled']) !!}
								{!! Form::select('adjustment_reason_id',['Insurance'=>'All']+(array)@$adj_reason_ins,null,['class'=>'select2 form-control js_insurance_aging js_all_hide_col hide','disabled']) !!}
								{!! Form::select('adjustment_reason_id',['all'=>'All'],null,['class'=>'select2 form-control js_all_aging js_all_hide_col hide','disabled']) !!}
							</div>                                                        
						</div>
					</div>

					<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
						<div class="form-group">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
								{!! Form::label('Insurance', 'Insurance', ['class'=>'control-label']) !!}
								{!! Form::select('insurance', [''=>'All']+(array)@$insurance,null,['class'=>'select2 form-control  ']) !!}
							</div>                                                        
						</div> 

						<div class="form-group">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
								{!! Form::label('Billing', 'Billing', ['class'=>'control-label']) !!}
								{!! Form::select('billing_provider_id',['all'=>'All']+(array)$billing_provider,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider"]) !!}
							</div>                                                        
						</div>

						<div class="form-group">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
								{!! Form::label('Rendering', 'Rendering', ['class'=>'control-label']) !!}
								{!! Form::select('rendering_provider_id',['all'=>'All']+(array)$rendering_provider,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider"]) !!}
							</div>                                                        
						</div>

						<div class="form-group">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
								{!! Form::label('Facility', 'Facility', ['class'=>'control-label']) !!}
								{!! Form::select('facility_id',['all'=>'All']+(array)@$facilities,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_facility"]) !!}
							</div>                                                        
						</div>

						<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 no-padding">
							<input class="btn btn-medcubics-small js_filter_search_submit pull-right margin-r-10" value="Search" type="submit">
						</div>
					</div>

				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
    
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 form-horizontal no-padding">

		<div class="box box-view no-shadow">

			<div class="box-body yes-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
			  
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part report-menu">
					<ul class="">
						<li><a href=""><i class="fa fa-angle-double-right"></i> Unpaid Claims Report</a></li>
						<li><a href=""><i class="fa fa-angle-double-right"></i> ANSI Reports</a></li>
						<li class="report-active"><a href=""><i class="fa fa-angle-double-right"></i> Adjustment Analysis</a></li>
						<li><a href=""><i class="fa fa-angle-double-right"></i> Authorizations List</a></li>
						<li><a href=""><i class="fa fa-angle-double-right"></i> Claim Submission Analysis</a></li>
						<li><a href=""><i class="fa fa-angle-double-right"></i> Unbilled Reports</a></li>
						<li><a href=""><i class="fa fa-angle-double-right"></i> Deposit Totals</a></li>
						<li><a href=""><i class="fa fa-angle-double-right"></i> Diagnosis Code Production Report</a></li>
						<li><a href=""><i class="fa fa-angle-double-right"></i> End of the day totals</a></li>
						<li><a href=""><i class="fa fa-angle-double-right"></i> Held Charges</a></li>
					</ul>
				</div>
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
