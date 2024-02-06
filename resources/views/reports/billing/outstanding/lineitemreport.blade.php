<div class="box box-view no-shadow"><!--  Box Starts -->
	<div class="box-header-view">

            <i class="livicon" data-name="info"></i> <h3 class="box-title">Outstanding Claims Report</h3>
            <div class="pull-right">
                <h3 class="box-title med-orange">{{ date("m/d/Y") }}</h3>
            </div>
        </div>
	
	
		@if($header !='' && count($header)>0)
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-list-bg no-padding margin-b-10">
			@foreach($header as $header_name => $header_val)
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 margin-b-6 no-padding">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 font600 med-green">{{ $header_name }}</div>
					<div class="col-lg-8 col-md-8 col-sm-6 col-xs-6">{{ $header_val }}</div>
				</div>
			@endforeach
			</div>
		</div>
		@endif
            
		@if(count($claims)>0)
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
			<div class="box box-info no-shadow no-bottom no-border">
				<div class="box-body">
					<div class="table-responsive  margin-t-10">
					 @php $count = 1;   @endphp  
					@foreach($claims as $claims_list)  
							@php
								$patient = $claims_list->patient;
								$set_title = (@$patient->title)? @$patient->title.". ":'';
								$patient_name = 	$set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name); 
							@endphp 
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border yes-border margin-t-10">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
							<span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600">{{ $claims_list->claim_number }}</span>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
								<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Patient Name</label>
								{{ $patient_name }}
							</div>
							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
								<label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Acc No</label>
								{{ @$claims_list->patient->account_no }}
							</div>
							@if(@$column->insurance =='')
							   @if(@$claims_list->insurance_details->insurance_name != '')		
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Insurance</label>
									@if(@$column->insurance =='')
										 {{ @$claims_list->insurance_details->insurance_name }}
									 @endif 
								</div>
								@endif
							@endif
							@if(@$column->rendering =='')
							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
								<label for="rendering" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Rendering</label>
								{{ @$claims_list->rendering_provider->provider_name }}
							</div>
							@endif
							@if(@$column->billing =='')
							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
								<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Billing</label>
								{{ @$claims_list->billing_provider->provider_name }}
							</div>
							@endif
							
							@if(@$column->facility =='')
							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
								<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Facility</label>
								{{ @$claims_list->facility_detail->facility_name }}
							</div>
							@endif 
						</div> 
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<table class="popup-table-wo-border table table-responsive">                    
								<thead>
									<tr>
										<th>DOS From</th>
										<th>DOS To</th>
										<th>CPT</th>
										<th class="text-right">Billed</th>                                                                 
										@if(@$column->bal_patient =='')<th class="text-right">Ins Paid</th>@endif            
										<th class="text-right">Pat Paid</th>                                                                  
										<th class="text-right">Adj</th>                                                                   
										<th class="text-right">Total Bal</th>
									</tr>
								</thead>                
								<tbody> 
									@foreach($claims_list->cpttransactiondetails as $cpt_details) 
									@if($cpt_details->cpt_code !="Patient")
									<tr>                              
										<td>{{ date('m/d/Y',strtotime($cpt_details->dos_from)) }}</td>                           
										<td>{{ date('m/d/Y',strtotime($cpt_details->dos_to)) }}</td>                           
										<td>{{ $cpt_details->cpt_code }}</td> 
										<td class="text-right">{{App\Http\Helpers\Helpers::priceFormat(@$cpt_details->charge)}}</td>
										@if(@$column->bal_patient =='')<td class="text-right">{{App\Http\Helpers\Helpers::priceFormat(@$cpt_details->insurance_paid)}}</td>@endif
										<td class="text-right">{{App\Http\Helpers\Helpers::priceFormat(@$cpt_details->patient_paid)}}</td>
										<td class="text-right">{{App\Http\Helpers\Helpers::getCalculatedAdjustment(@$cpt_details->adjustment, @$cpt_details->with_held)}}</td>
										<td class="text-right">{{App\Http\Helpers\Helpers::priceFormat(@$cpt_details->balance)}}</td>
									</tr>
									@endif
									@endforeach                  
								</tbody>
							</table>
						</div>
					</div>
					@php $count++;   @endphp 
					@endforeach  
					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
						<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
							Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
						</div>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
					</div>
					
				</div><!-- /.box-body -->
			</div><!-- /.box -->
			</div><!-- /.box -->
		</div>
		@else
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5>No Records Found !!</h5></div>
		@endif
	</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 text-center">
	<input class="btn btn-medcubics-small" id="js_exit_part" value="Exit" type="button">
</div>