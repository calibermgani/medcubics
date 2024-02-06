<div class="box box-view no-shadow"><!--  Box Starts -->

	<div class="box-header-view">
		<h2 style="font-size:20px;">Payment Analysis
		<span class="pull-right" style="font-size:13px;padding:10px;">
			<!--button class="btn btn-box-tool" data-widget="collapse"-->Date : {{ date("m/d/y") }}<!--i class="fa fa-minus"></i></button--></h2>
		</div>
	</div>
	<div class="box-body p-b-40"><!-- Box Body Starts -->
		@if($header !='' && count($header)>0)
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-list-bg no-padding margin-b-20">
			@foreach($header as $header_name => $header_val)
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 margin-b-6 no-padding">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 font600 med-green">{{ $header_name }}</div>
					<div class="col-lg-8 col-md-8 col-sm-6 col-xs-6">{{ $header_val }}</div>
				</div>
			@endforeach
			</div>
		</div>
		@endif
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
			<div class="box box-info no-shadow no-bottom no-border">
				<div class="box-body no-padding">
				@if(count($payments)>0)
					<div class="table-responsive">
					<table id="sort_list_noorder" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Acc No</th>
								<th>Patient Name</th>
								<th>Total Billed($)</th>
								<th>Patient Paid($)</th>
								<th>Ins Paid($)</th>
								<th>Patient Bal($)</th>
								<th>Ins Bal($)</th>
								<th>Total Adj($)</th>
								@if(@$column->pat_over_pay =='1')<th>wallet Bal($)</th>@endif
							</tr>
						</thead>                
						<tbody>
							 @php $count = 1;   @endphp  
							@foreach($payments as  $payments_list)                      
							@php
								$patient = $payments_list->patient;
								$patient_wallet_balance = json_decode(json_encode($patient_wallet_balance), true);
								$set_title = (@$patient->title)? @$patient->title.". ":'';
								$patient_name = 	$set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name);
							@endphp 
							<tr style="cursor:default;">                              
								<td>{{ @$patient->account_no }}</td>                           
								<td>{{ $patient_name }} <!--div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
										<a id="someelem{{$payments_list->id}}" class="someelem" data-id="{{$payments_list->id}}" href="javascript:void(0);"> {{ $patient_name }}</a> 
									</div-->
								</td>                           
								<td class="text-right">{!! @$payments_list->total_charge !!}</td>
								<td class="text-right">{!! @$payments_list->patient_paid !!}</td>
								<td class="text-right">{!! @$payments_list->insurance_paid !!}</td>
								<td class="text-right">{!! @$payments_list->patient_bal !!}</td>
								<td class="text-right">{!! @$payments_list->insurance_bal !!}</td>
								<td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->adj+@$payments_list->withheld ) !!}</td>
								@if(@$column->pat_over_pay =='1')<td class="text-right">{{App\Http\Helpers\Helpers::priceFormat(@$patient_wallet_balance[$patient->id])}}</td>@endif
							</tr>
							<?php /*
							<div class="on-hover-content js-tooltip_{{@$payments_list->id}}" style="display:none;">
								<span class="med-orange font600">{{ $patient_name }}</span> 
										<p class="no-bottom hover-color"><span class="font600">Acc No :</span> {{ @$patient->account_no }}
									<br>
									@if(@$patient->age !='')<span class="font600">Age :</span> {{ @$patient->age }} Yrs @endif
												<span class="font600">Gender :</span> {{ @$patient->gender }}<br>
									<span class="font600">Ins :</span> {{ App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$patient->id)}}<br>
									<span class="font600">Address :</span> {{ @$patient->address1 }}<br>
									 {{ @$patient->city}}, {{ @$patient->state}}, {{ @$patient->zip5}}-{{ @$patient->zip4}}<br>
									@if(@$patient->phone)<span class="font600">Home Phone :</span>{{@$patient->phone}} <br>@endif
									@if(@$patient->work_phone)<span class="font600">Work Phone :</span> {{@$patient->work_phone}}@endif
								</p>
							</div>
							*/ ?>
							@php $count++;   @endphp 
							@endforeach                  
						</tbody>
					</table>
				</div><!-- /.box-body -->
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
						Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
					</div>
					<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right tabs-border yes-border box-header-view margin-t-10">
					<label class="med-green font600" > Total Pmt</label>&nbsp;&nbsp;
					<span style="color:#737373;"> ${!! App\Http\Helpers\Helpers::priceFormat(@$total->total_paid) !!}</span>&emsp;
					<label class="med-green font600" > Total Pat.Pmt</label>&nbsp;
					<span style="color:#737373;"> ${!! App\Http\Helpers\Helpers::priceFormat(@$total->pat_pay) !!}</span>&emsp;
					<label class="med-green font600" > Total Ins.Pmt</label>&nbsp;
					<span style="color:#737373;"> ${!! App\Http\Helpers\Helpers::priceFormat(@$total->ins_pay) !!}</span>&emsp;
					<label class="med-green font600" > Total Adj</label>&nbsp;&nbsp;
					<span style="color:#737373;"> ${!! App\Http\Helpers\Helpers::priceFormat(@$total->adjusted) !!}</span>&emsp;
					<label class="med-green font600" > Total Transfers</label>&nbsp;&nbsp;
					<span style="color:#737373;"> ${!! App\Http\Helpers\Helpers::priceFormat(@$total->trans) !!}</span>&emsp;
				</div>
				@else
					<div class="thumbnail col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green" style="padding: 10px 15px;"><h5>No Records Found !!!</h5></div>
				@endif
			</div><!-- /.box -->
			</div><!-- /.box -->
		</div>
	</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->