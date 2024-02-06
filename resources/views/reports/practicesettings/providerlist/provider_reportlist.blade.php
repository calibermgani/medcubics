<?php @$heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->
    <?php $req = @$practiceopt; ?>
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body  bg-white"><!-- Box Body Starts -->      
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange" >
                @if($req == "provider_list")
                <div class="margin-b-15">Provider List</div>
                @else
                <div class="margin-b-15 margin-t-10">Provider Summary</div>
                @endif
            </h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
                @php  $i=1; @endphp
                @if(isset($header) && !empty($header))
                @foreach($header as $header_name => $header_val)
                <span class="med-green">{{ @$header_name }}</span> : {{ @$header_val }} @if($i < count((array)$header)) | @endif 
				@php  $i++; @endphp                   
				@endforeach
				@endif
			</div>

		</div>
		@if($req == "provider_list")
		<div class="table-responsive col-lg-12 margin-t-20">
			<table class="table table-striped table-bordered table-separate" id="sort_list_noorder">
				<thead>
					<tr>
						<th>Provider Name</th>
						<th>Type</th>              
						<th>Created On</th>
						<th>User</th> 					
					</tr>
				</thead>
				<tbody>
					@if(count($filter_group_list) > 0)  
					<?php
						$total_adj = 0;
						$patient_total = 0;
						$insurance_total = 0;
					?>
					@foreach($filter_group_list as $list)
					<tr style="cursor:default;">                   
						<td class="text-left"> {!! @$list->provider_name !!}</td>
						<td class="text-left">{!! @$list->provider_types->name	 !!}</td>                 
						<td class="text-left">{!! date('m/d/y',strtotime(@$list->created_at)) !!}</td>
						<td class="text-left">{!! @$list->provider_user_details->short_name !!}</td>						
					</tr>
					@endforeach
				</tbody> 
				@endif
			</table>
		</div>      

		@else
		<div class="table-responsive col-lg-12 no-padding">
			<table class="table table-striped table-bordered table-separate" id="provider_list_noorder">
				<thead>
					<tr>
						<th>@if($header->{'Provider Type'}=="Billing")Billing @else Rendering @endif</th>
						<th class="text-left">Units</th>
						<th class="text-right">Charges($)</th>
						<th class="text-right">W/O($)</th>
						<th class="text-right">Pat Adj($)</th>
						<th class="text-right">Ins Adj($)</th>
						<th class="text-right">Total Adj($)</th>
						<th class="text-right">Pat Pmts($)</th>     
						<th class="text-right">Ins Pmts($)</th>     
						<th class="text-right">Total Pmts($)</th>     
						<th class="text-right">Pat Balance($)</th> 
						<th class="text-right">Ins Balance($)</th>
						<th class="text-right">Total Balance($)</th> 					
					</tr>
				</thead>
				<tbody>
					@if(!empty($providers) && count($providers)> 0)
					@foreach($providers as $list)
					<tr style="cursor:default;"> 
						<td>{!! @$list->short_name." - ".$list->provider_name !!}</td>
						<?php 
							$name = $list->provider_name;
							$prID = $list->id;
							$charge = isset($charges->$prID) ? $charges->$prID : 0;
							$unit = isset($units->$prID) ? $units->$prID : 0;
							$wo = isset($writeoff->$prID) ? $writeoff->$prID : 0;
							$patient_adj = isset($pat_adj->$prID) ? $pat_adj->$prID : 0;
							$insurance_adj = isset($ins_adj->$prID) ? $ins_adj->$prID : 0;
							$pat_pmt = isset($patient->$prID) ? $patient->$prID : 0;
							$ins_pmt = isset($insurance->$prID) ? $insurance->$prID : 0;
							$pat_bal = isset($patient_bal->$prID) ? $patient_bal->$prID : 0;
							$ins_bal = isset($insurance_bal->$prID) ? $insurance_bal->$prID : 0;
						?> 
						<td class="text-left">{!! $unit !!}</td>
						<td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat($charge) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($wo) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($patient_adj) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($insurance_adj) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($wo+$patient_adj+$insurance_adj) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pat_pmt) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($ins_pmt) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pat_pmt+$ins_pmt) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pat_bal) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($ins_bal) !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pat_bal+$ins_bal) !!}</td>   
					</tr>
					@endforeach
					@endif	
				</tbody>
			</table>    
		</div>   
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
				Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
			</div>
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
		</div>           
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border-top: 1px solid #f0f0f0;">
			<div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-10">
				<div class="box-header-view-white no-border-radius pr-t-5 margin-b-20">
					<i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>                     
				</div><!-- /.box-header -->        
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<table class="table table-separate table-borderless pr-r-m-20 table-separate border-radius-4">    
					<tbody>                                                
						<?php
							$wallet = isset($patient->wallet) ? $patient->wallet : 0;
							if ($wallet < 0)
								$wallet = 0;
						?>
						<tr>                            

							<td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
								<h3 class="text-center">${{App\Http\Helpers\Helpers::priceFormat($wallet)}}</h3>
								<h4 class="text-center"><i>Wallet Balance</i></h4>
							</td>
							<td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
								<h3 class="text-center">{!! array_sum((array)$units) !!}</h3>
								<h4 class="text-center"><i>Total Units</i></h4>
							</td>
							<td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
								<h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(array_sum((array)$charges)) !!}</h3>
								<h4 class="text-center"><i>Total Charges</i></h4>
							</td>
							<td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
								<h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(array_sum((array)$writeoff)+array_sum((array)$pat_adj)+array_sum((array)$ins_adj)) !!}</h3>
								<h4 class="text-center"><i>Total Adjustments</i><br><i style="font-size: 10px;">( Writeoff Included )</i></h4>
							</td>
							<td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
								<h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(array_sum((array)$patient)+array_sum((array)$insurance)) !!}</h3>
								<h4 class="text-center"><i>Total Payments</i></h4>
							</td>
							<td style="border-top:0px solid #fff !important">
								<h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(array_sum((array)$patient_bal)+array_sum((array)$insurance_bal)) !!}</h3>
								<h4 class="text-center"><i>Total Balance</i></h4>
							</td>


						</tr>
					</tbody>
				</table>
			</div>
		</div>
		@endif
		@if($req == "provider_list")
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
				Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
			</div>
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
		</div>
		@endif
	</div>
</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->