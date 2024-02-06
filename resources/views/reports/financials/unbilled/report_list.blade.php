<div class="box box-view no-shadow"><!--  Box Starts -->		

    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: <span class="med-green">@if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</span></h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body  bg-white"><!-- Box Body Starts -->      
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Unbilled Claims Analysis</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>
				
                    @foreach($search_by as $key=>$val)
                     @if($i > 0){{' | '}}@endif
                           <span class="med-green">{!! $key !!}: </span>{{ @$val }}                           
                          <?php $i++; ?>
                     @endforeach </div>                    
                </div>                
            </div>
        </div>

         @if(count($unbilled_claim_details) > 0)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
            <div class="box box-info no-shadow no-bottom no-border">
                <div class="box-body no-padding">
        <div class="table-responsive mobile-md-scroll">
            <table class="table table-striped table-bordered table-separate mobile-md-width">
               <thead>
                   <tr>
                       <th>Acc No</th>
                       <th>Patient Name</th>
                       <th>DOS</th>  
                       <th>Claim No</th>
                       <th>Payer</th>
                       <th>Facility</th>
                       <th>Rendering </th>
                       <th>Billing </th>  
                       <th>Created Date</th>  		
                       <th class="text-left">Days Since Created</th>  		
                       <th class="text-right">Charges($)</th>  		
                   </tr>
            </thead>
                <tbody>
			<?php $grand_total = 0;  ?>
               
					@foreach($unbilled_claim_details as $lists)
					
                <tr style="cursor:default;">
                    <?php 
                    if(isset($lists->account_no) && $lists->account_no != ''){
                    ?>
                    <td>{!! !empty($lists->account_no)? $lists->account_no : '-Nill-' !!}</td>
                    <td>{!! !empty($lists->patient_name)? $lists->patient_name : '-Nill-' !!}</td>
                    <td>{!! !empty($lists->dos)? $lists->dos : '-Nill-'  !!}</td>
                    <td>{!! !empty($lists->claim_number)? $lists->claim_number : '-Nill-' !!}</td>
                    <td>{!! !empty($lists->insurance_short_name)? $lists->insurance_short_name : '-Nill-' !!}</td>
                    <td>{!! !empty($lists->facility_short_name)? $lists->facility_short_name : '-Nill-' !!}</td>
                    <td>{!! !empty($lists->rendering_provider_short_name)? $lists->rendering_provider_short_name : '-Nill-' !!}</td>
                    <td>{!! !empty($lists->billing_provider_short_name)? $lists->billing_provider_short_name : '-Nill-' !!}</td>
                    <td>{{ !empty($lists->created_at)? $lists->created_at : '-Nill-' }}</td>
                    <td class="text-left">{!! !empty($lists->daysSinceCreatedCount)? $lists->daysSinceCreatedCount : '-Nill-' !!}</td>
                    <?php 
                        } else {
                    ?>
                    <td>{!! !empty($lists->patient->account_no)? $lists->patient->account_no : '-Nill-' !!}</td>
                    <?php $name = App\Http\Helpers\Helpers::getNameformat(@$lists->patient->last_name,@$lists->patient->first_name,@$lists->patient->middle_name);
					?>
                    <td>{!! !empty($name)? $name : '-Nill-' !!}</td>
                    <td>{!! !empty($lists->date_of_service)? App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$lists->date_of_service, '','-Nil-') : '-Nill-'  !!}</td>
                    <td>{!! !empty($lists->claim_number)? $lists->claim_number : '-Nill-' !!}</td>		
                    <?php $insurance_name = App\Http\Helpers\Helpers::getInsuranceName(@$lists->insurance_id)?>
                    <td>{!! !empty($insurance_name)? $insurance_name : '-Nill-' !!}</td>
                    <td>{!! !empty($lists->facility->short_name) ? @$lists->facility->short_name:"-Nil-" !!}</td>
                    <td>{!! !empty($lists->rendering_provider->short_name) ? @$lists->rendering_provider->short_name:"-Nil-" !!}</td>
                    <td>{!! !empty($lists->billing_provider->short_name) ? @$lists->billing_provider->short_name:"-Nil-" !!}</td>
                    <td>{{ !empty($lists->created_at)? App\Http\Helpers\Helpers::timezone(@$lists->created_at, 'm/d/y') : '-Nill-' }}</td>
                    <td class="text-left">{!! !empty($lists->created_at)? App\Http\Helpers\Helpers::daysSinceCreatedCount(date('Y-m-d',strtotime(@$lists->created_at))) : '-Nill-' !!}</td>
                    <?php } ?>
                    <td class="text-right">{!! !empty($lists->total_charge)? $lists->total_charge : '-Nill-' !!}</td>
					<?php
						$grand_total = $grand_total + $lists->total_charge; 
					?>
						
                </tr>
				@endforeach
            </tbody>
            </table>    
        </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
                        </div>
         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 p-l-4 hide">
			<label class="font600">Total Charges : </label>&nbsp;
			<span class="med-orange font600">${!! App\Http\Helpers\Helpers::priceFormat($total_charges,'no') !!}</span>&emsp;
		</div>
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->
