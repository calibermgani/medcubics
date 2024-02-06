<div class="box box-view no-shadow"><!--  Box Starts -->
	<div class="box-header-view">
		<h2 class="text-center" style="font-size:20px;">Outstanding Claims Report
		<span class="pull-right" style="font-size:13px;padding:10px;">
			<!--button class="btn btn-box-tool" data-widget="collapse"-->Date : {{ date("m/d/Y") }}<!--i class="fa fa-minus"></i></button--></h2>
		</div>
	</div>
	<div class="box-body p-b-40"><!-- Box Body Starts -->
		@if($header !='' && count($header)>0)
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-list-bg">
			@foreach($header as $header_name => $header_val)
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 margin-b-6 no-padding">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 font600 med-green">{{ $header_name }}</div>
					<div class="col-lg-8 col-md-8 col-sm-6 col-xs-6">{{ $header_val }}</div>
				</div>
			@endforeach
			</div>
		</div>
		@endif
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="box box-info no-shadow no-border">
				<div class="box-body">
					<div class="table-responsive">
					<table id="list_noorder" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Acc No</th>
								<th>Patient Name</th>
								<th>Claim No</th>
								<th>DOS</th>
								@if(@$column->insurance =='')<th>Insurance</th>@endif
								@if(@$column->billing =='')<th>Billing</th>@endif 
								@if(@$column->rendering =='')<th>Rendering</th>@endif
								@if(@$column->facility =='')<th>Facility</th>@endif 
								<th>Billed</th>
								@if(@$column->bal_patient =='')<th>Ins Paid</th>@endif
								<th>Pat Paid</th>
								<th>Adj</th>
								<th>Total Bal</th>
							</tr>
						</thead>                
						<tbody>
							 @php  $count = 1;   @endphp  
							@foreach($claims as $claims_list)                       
								@php 
									$patient = $claims_list->patient;
									$set_title = (@$patient->title)? @$patient->title.". ":'';
									$patient_name = 	$set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name); 
								@endphp 
								<tr style="cursor:default;">                              
								<td>{{ @$claims_list->patient->account_no }}</td>                           
								<td>{{ $patient_name }}</td>                           
								<td>{{@$claims_list->claim_number}}</td>
								<td>{{date('m/d/Y',strtotime($claims_list->date_of_service))}}</td>
								@if(@$column->insurance =='')
									@if(empty($claims_list->insurance_details))
										<td>Self</td>  
									@else                               
										<td>{{str_limit(@$claims_list->insurance_details->insurance_name,15,' ...')}}</td>
									@endif  
								 @endif 
								@if(@$column->billing =='')
								<td> {{str_limit(@$claims_list->billing_provider->short_name,15,' ...')}}</td>
								@endif
								@if(@$column->rendering =='')
								<td>{{str_limit(@$claims_list->rendering_provider->short_name,15,' ...')}}</td>
								@endif
								
								@if(@$column->facility =='')
								<td>{{str_limit(@$claims_list->facility_detail->short_name,15,' ...')}}</td> 
								@endif
								<td>{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->total_charge)}}</td>
								@if(@$column->bal_patient =='')
								<td>{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->insurance_paid)}}</td>
								@endif
								<td>{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->patient_paid)}}</td>
								<td>{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->total_adjusted)}}</td>
								<td>{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->balance_amt)}}</td>
							</tr>
							@php  $count++;   @endphp 
							@endforeach                  
						</tbody>
					</table>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
			</div><!-- /.box -->
		</div>
	</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->