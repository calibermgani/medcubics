<div class="box box-view no-shadow"><!--  Box Starts -->		
    <div class="box-header-view">
        <i class="fa fa-line-chart" data-name="info"></i> <h3 class="box-title">Provider Reimbursement Analysis</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
        </div>
    </div>
	<div class="box-body  bg-white"><!-- Box Body Starts -->    
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25">Provider Reimbursement Analysis</h3>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
				   
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><span class="med-green">Transaction Date</span> : {!! $start_date !!}  to {!! $end_date !!}</div>                    
					</div>
				  
				</div>
			</div>    
		<input type="hidden" name="rendering_provider" value="{!! $rendering_provider !!}">
		<input type="hidden" name="billing_provider" value="{!! $billing_provider !!}">
		<div class="table-responsive col-lg-12">	
			<?php //dd($data_value);?>
		@if(count($data_value) > 0)
			
			 @foreach($data_value as $key=>$provider)
			<?php
				$provider_name = $type = ''; 
				if($key !=0)    {
					$provider_data = explode('-',$provider_info->{$key}); 
					$provider_name = @$provider_data[0];				
					$type = @$provider_data[1];
				}
			?>
			 <p class="margin-b-5"><span class="med-green font600">{!! $provider_name !!}</span> - <span class="med-orange font600"> {!! $type !!} </span></p>
			<table class="table table-striped table-bordered table-separate l-green-b">
				<tr>
					<th class="light-th-bg td-c-50">Facility Name</th>
					<th class="text-right light-th-bg td-c-15">Charges($)</th>
					<th class="text-right light-th-bg td-c-15">Pmts($)</th> 
					<th class="text-right light-th-bg td-c-15">Adj($)</th>
					<th class="text-right light-th-bg">Balance($)</th>
				</tr>
				 @foreach($provider as $key=>$facility)
				<tr>	
				<?php $i= 0;?>			   
					<td>{!! @$facility[0]->facility_detail->facility_name !!}</td>
					<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$facility[$i]->total_charge) !!}</td>
					<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$facility[$i]->total_paid) !!}</td>
					<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$facility[$i]->total_adjusted) !!}</td>
					<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$facility[$i]->balance_amt) !!}</td>
				</tr>
				@endforeach				
			</table>
			@endforeach	
			@else
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5>No Records Found !!</h5></div>
		@endif
		</div>
	</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->