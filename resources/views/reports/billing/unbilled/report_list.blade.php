@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Billing Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Unbilled Claims Reports Analysis</span></small>
        </h1>
        <ol class="breadcrumb">
            
            <li><a href="{{ url('reports/billing/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			.
           <li class="dropdown messages-menu js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-share-square-o" data-placement="bottom" data-toggle="tooltip" data-original-title="Export"></i></a>
                <!---Export -->
            
				<ul class="dropdown-menu" style="margin-top: 3px; display: none;">
					<li>
						<ul class="menu" style="list-style-type:none; ">
							<li>
								<a href="{!! url('reports/billing/unbilledexport/xlsx') !!}" data-url="{!! url('reports/billing/unbilledexport') !!}" data-option="xlsx">
									<i class="fa fa-file-excel-o"></i> Excel
								</a>
							</li>
							<li>
								<a href="{!! url('reports/billing/unbilledexport/pdf') !!}" data-url="{!! url('reports/billing/unbilledexport') !!}" !!}" data-option="pdf">
									<i class="fa fa-file-pdf-o" data-placement="right" data-toggle="tooltip" data-original-title="pdf"></i> PDF
								</a>
							</li>
							<li>
								<a href="{!! url('reports/billing/unbilledexport/csv') !!}" data-url="{!! url('reports/billing/unbilledexport') !!}" data-option="csv">
									<i class="fa fa-file-code-o" data-placement="right" data-toggle="tooltip" data-original-title="csv"></i> CSV
								</a>
							</li>
						</ul>
					</li>
				</ul>            
			<!---Export -->      
		  </li>
            <li><a href="#js-help-modal" data-url="{{url('help/adjustment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="box box-view no-shadow"><!--  Box Starts -->		

			<div class="box-header-view">
				<i class="livicon" data-name="info"></i> <h3 class="box-title">Unbilled Claims Reports</h3>
				<div class="pull-right">
					<h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
				</div>
			</div>
			<div class="box-body  bg-white"><!-- Box Body Starts -->

				<div class="table-responsive col-lg-12">
					<table class="table table-striped table-bordered" id="list_noorder">
						<thead>
							<tr>

								<th>Claim#</th>
								<th>Patient Name</th>
								<th>Acc No</th>
								<th>Responsibility</th>
								<th>Rendering Provider</th>
								<th>Billing Provider</th>  		
								<th>DOS</th>  		
								<th>Transaction Date</th>  		
								<th>Fee</th>  		
							</tr>
						</thead>
						<tbody>
						@php $grand_total = 0;  @endphp
							@if(count($unbilled_claim_details) > 0)
								@foreach($unbilled_claim_details as $lists)
								
							<tr style="cursor:default;">
								<td>{!! @$lists->claim_number !!}</td>
								<td>{!! @$lists->patient->first_name !!}</td>
								<td>{!! @$lists->patient->account_no !!}</td>					
								<td>{!! @$lists->insurance_details->insurance_name !!}</td>
								<td>{!! @$lists->rendering_provider->provider_name !!}</td>					
								<td>{!! @$lists->billing_provider->provider_name !!}</td>
								<td>{!! date('m/d/Y',strtotime(@$lists->date_of_service)) !!}</td>
								<td>{!! date('m/d/Y',strtotime(@$lists->created_at)) !!}</td>
								<td class="text-right">{!! @$lists->total_charge !!}</td>
								@php
									$grand_total = $grand_total + $lists->total_charge; 
								@endphp									
							</tr>
							@endforeach
						</tbody>
						
					</table>
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 p-l-4">
					<label class="med-green font600">Grand Total : </label>&nbsp;
					<span class="med-orange font600">{!! App\Http\Helpers\Helpers::priceFormat($grand_total,'no') !!}</span>&emsp;
				</div>
				@else
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5>No Records Found !!</h5></div>
				@endif
			</div><!-- Box Body Ends --> 
		</div><!-- /.box Ends-->
    </div>
@endsection