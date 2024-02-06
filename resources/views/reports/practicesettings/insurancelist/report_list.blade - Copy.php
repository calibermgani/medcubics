<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="livicon" data-name="info"></i> <h3 class="box-title">Insurance List</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
        </div>
    </div>
	<div class="box-body  bg-white"><!-- Box Body Starts -->    
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
			@if (!empty($start_date))
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-list-bg no-padding margin-b-10"> 
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 margin-b-6 no-padding">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 font600 med-green">Trans</div>
					<div class="col-lg-8 col-md-8 col-sm-6 col-xs-6">{!! $start_date !!}  to {!! $end_date !!}</div>
				</div>
			</div>
			@endif
		</div>
		<div class="table-responsive col-lg-12">
			<table class="table table-striped table-bordered" id="list_noorder">
				<thead>
				
					<tr>
						<th>Insurance Name</th>
						<th>Insurance Type</th>
						<th>Units</th>
						<th>charges</th>
						<th>Adjustments</th>
						<th>Payments</th>  	
						<th>Pat Balance</th> 
						<th>Ins Balance</th>					
						<th>Total Balance</th> 					
					</tr>
				</thead>
				<tbody>
				@if(count($filter_insuran_result) > 0) 			
				@php 
					$total_adj = 0;
					$patient_total = 0;
					$insurance_total = 0;
					$count = 0;				
				@endphp
					@foreach($filter_insuran_result as $list)
					{{-- @if($filter_insuran_result[$count]->insurance_details != NULL) --}}
					<tr style="cursor:default;">
						<td class="text-left">
						@if($filter_insuran_result[$count]->insurance_details != NULL)
						{!! $filter_insuran_result[$count]->insurance_details->insurance_name !!}
					@endif
					</td>			
						<td class="text-left">					
						@if($filter_insuran_result[$count]->insurance_details != NULL)
						{!! $filter_insuran_result[$count]->insurance_details->insurancetype->type_name !!}
					@endif
						
						</td>
						<td>{!! $list->claim_unit_details->unit !!}</td>
						<td class="text-right">{!! $list->claim_unit_details->charge !!}</td>
						<td class="text-right">{!! ($list->claim_unit_details->total_adjusted)-($list->$claim_unit_details->patient_adjusted)  !!}</td>
						<td class="text-right">{!! $list->claim_unit_details-> paid_amt !!}</td>
						<td class="text-right">{!! $list-> claim_unit_details->patient_balance !!}</td>
						<td class="text-right">{!! $list-> claim_unit_details->insurance_balance !!}</td>					
						< <td class="text-right">{!! $list->claim_unit_details->balance !!}</td>			
					</tr>
					@php   $count = $count + 1;  @endphp
						{{-- @endif --}}
					@endforeach					
				</tbody>
			</table>    
		</div>    
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p class="no-bottom"><label class="med-green font600"> Practice : </label>&nbsp; {!! $heading_name !!}</p>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-list-bg">
			
			
		  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<p><span class="med-green font600">Total Adjustments :</span> <span class="med-orange font600">{!! App\Http\Helpers\Helpers::priceFormat($total_adj,'no') !!}</span></p>
			</div>
			
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<p><span class="med-green font600">Patient Total :</span> <span class="med-orange font600">{!! App\Http\Helpers\Helpers::priceFormat($patient_total,'no') !!}</span></p>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<p><span class="med-green font600">Insurance Total :</span> <span class="med-orange font600">{!! App\Http\Helpers\Helpers::priceFormat($insurance_total,'no') !!}</span></p>
			</div> 
				
		</div>
		@else
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5>No Records Found !!</h5></div>
		@endif
	</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->