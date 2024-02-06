<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->		

    <div class="box-header-view">
        <i class="livicon" data-name="info"></i> <h3 class="box-title">End of the Day Total Report</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
        </div>
    </div>

	<div class="box-body  bg-white"><!-- Box Body Starts -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-list-bg no-padding margin-b-10">
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 margin-b-6 no-padding">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 font600 med-green">Trans</div>
					<div class="col-lg-8 col-md-8 col-sm-6 col-xs-6">{!! $start_date !!}  to {!! $end_date !!}</div>
				</div>
			</div>
		</div>
		<div class="table-responsive col-lg-12">
			<table class="table table-striped table-bordered" id="list_noorder">
				<thead>
					<tr>

						<th>Date</th>
						<th>Charges</th>
						<th>Payments</th>
						<th>Adjustments</th>
						<th>Patient Payments</th>
						<th>Insurance Payments</th>  		
					</tr>
				</thead>
				<tbody>
				@if(count($filter_result) > 0)  
				@php
					$total_adj = 0;
					$patient_total = 0;
					$insurance_total = 0;
				@endphp
					@foreach($filter_result as $list)
					
					<tr style="cursor:default;">
						<td>{!! date('m/d/Y',strtotime($list->created_at)) !!}</td>
						<td class="text-right">{!!  $list->claim->total_charge !!}</td>
						<td>{!! $list->payment_type !!}</td>
						<td class="text-right">{!! $list->total_adjusted !!}</td>
						<td class="text-right">{!! $list->patient_paid_amt !!}</td>
						<td class="text-right">{!! $list->insurance_paid_amt !!}</td>
							@php
							$total_adj = $total_adj + $list->total_adjusted;
							$patient_total = $patient_total + $list->patient_paid_amt;
							$insurance_total = $insurance_total + $list->insurance_paid_amt;
							@endphp
					</tr>
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
