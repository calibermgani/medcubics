<?php
//dd($ageingday);
 $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->		

    <div class="box-header-view">
        <i class="fa fa-newspaper-o" data-name="info"></i> <h3 class="box-title">Patient Aging Analysis</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
        </div>
    </div>

	<div class="box-body  bg-white"><!-- Box Body Starts -->
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25">Patient Aging Analysis</h3>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
			   
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><span class="med-green">Transaction Date</span> : {!! @$start_date !!}   to {!! @$end_date !!}</div>                    
				</div>
			  
			</div>
		</div>
		
		
		@php $req=$ageingday   @endphp
		<div class="table-responsive col-lg-12">
			<table class="table table-striped table-bordered" id="list_noorder">
				<thead>
					<tr>					
						<th>Acc No</th>
						<th>Patient Name</th>
						<th>0-30($)</th>
						<th>31-60($)</th>
						<th>61-90($)</th>
						<th>91-120($)</th>
						<th>121-150($)</th>
						<th>>150($)</th>
						<th>Total Pat Bal($)</th>
					</tr>
				</thead>
				<tbody>					
			   
					@if($req == 'all')						
					@foreach($patient_data as $key=>$patient_data)							
					 <tr style="cursor:default;">
					 
						<td>{{@$key}}</td>
					
						<?php
							$pname=App\Models\Patients\Patient::where('account_no', $key)->select('last_name','middle_name','first_name')->first();
							$name=App\Http\Helpers\Helpers::getNameformat(@$pname->last_name,@$pname->first_name,@$pname->middle_name);
						?>
						<td>{!!@$name!!}</td>
						@if(@$patient_data->{'0-30'} != '')
						<td>{!!@$patient_data->{'0-30'}!!}</td>
						@else
						<td>0.00</td>
						@endif
						@if(@$patient_data->{'31-60'} != '')
						<td>{!!@$patient_data->{'31-60'}!!}</td>
						@else
						<td>0.00</td>
						@endif
						@if(@$patient_data->{'61-90'} != '')
						<td>{!!@$patient_data->{'61-90'}!!}</td>
						@else
						<td>0.00</td>
						@endif
						@if(@$patient_data->{'91-120'} != '')
						<td>{!!@$patient_data->{'91-120'}!!}</td>
						@else
						<td>0.00</td>
						@endif
						@if(@$patient_data->{'121-150'} != '')
						<td>{!!@$patient_data->{'121-150'}!!}</td>
						@else
						<td>0.00</td>
						@endif
						@if(@$patient_data->{'150-above'} != '')
						<td>{!!@$patient_data->{'150-above'}!!}</td>
						@else
						<td>0.00</td>
						@endif
						<?php  
						$a=array(@$patient_data->{'0-30'},@$patient_data->{'31-60'},@$patient_data->{'61-90'},@$patient_data->{'91-120'},@$patient_data->{'121-150'},@$patient_data->{'150-above'});
						@$sum_value=array_sum($a);
						?>
						<td>{!!	App\Http\Helpers\Helpers::priceFormat(@$sum_value)!!}</td>
						
					</tr>
					@endforeach
					@else
						@foreach($ar_filter_result as $list)
					
					<tr style="cursor:default;">
					 
						<td>{{@$list->account_no}}</td>
						<td>{{@$list->full_name}}</td>					
					
						@if($req == '0-30')
							<td>{!! @$list->balance_amt !!}</td>
						@else
							<td>0.00</td>
						@endif
						@if($req == '31-60')
							<td>{!! @$list->balance_amt !!}</td>
						@else
							<td>0.00</td>
						@endif
						@if($req == '61-90')
							<td>{!! @$list->balance_amt !!}</td>
						@else
							<td>0.00</td>
						@endif
						@if($req == '91-120')
							<td>{!! @$list->balance_amt !!}</td>
						@else
							<td>0.00</td>
						@endif
						@if($req == '121-150')
							<td>{!! @$list->balance_amt !!}</td>
						@else
							<td>0.00</td>
						@endif
						@if($req == '150-above')
							<td>{!! @$list->balance_amt !!}</td>
						@else
							<td>0.00</td>
						@endif
						<td>{!! App\Http\Helpers\Helpers::priceFormat(@$list->balance_amt) !!}</td>
					</tr>
					@endforeach
						
					@endif
				</tbody>
			</table>    
		</div>
		
		{{--   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p class="no-bottom"><label class="med-green font600"> Practice : </label>&nbsp; {!! @$heading_name !!}</p>
		</div>--}}
	   
		
	</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->