<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->        

    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>

    <div class="box-body  bg-white"><!-- Box Body Starts -->      
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange">AR Workbench Report</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                     @if($i > 0){{' | '}}@endif
                           <span class="med-green">{!! $key !!} : </span>{{ @$val[0] }}                           
                          <?php $i++; ?>
                     @endforeach </div>                   
                </div>                
            </div>
        </div>

		@if(count($workbench_list) > 0)  
		<div class="box-body no-padding">  
                    <div class="table-responsive mobile-md-scroll col-lg-12 no-padding">
                        <table class="table table-striped table-bordered table-separate" id="sort_list_noorder_report">  
                            <thead>
                                <tr>
                                    <th>Claim No</th>
                                    <th>DOS</th>							
                                    <th>Patient Name</th>							
                                    <th>Rendering</th>                        							
                                    <th>Billing</th>
                                    <th>Facility</th>							
                                    <th>Responsibility</th>							
                                    <th>Category</th>							
                                    <th>Charge Amt($)</th>
                                    <th>Paid($)</th>
                                    <th>Adj($)</th>
                                    <th>Pat AR($)</th>
                                    <th>Ins AR($)</th>
                                    <th>AR Due($)</th>
                                    <th>Claim Age</th>
                                    <th>Claim Status</th>
									<th>Sub Status</th>
                                    <th>Workbench Status</th>
                                    <th>Followup Date</th>
                                    <th>Assigned To</th>                                                     
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workbench_list as  $result)
                                <?php //from stored procedure
                                    if(isset($result->claim_number) && $result->claim_number != ''){
                                ?>
                                <tr style="cursor:default;">
                                    <td>{{ @$result->claim_number}}</td>
                                    <td>{{ @$result->dos }}</td>
                                    <td>{{ @$result->patient_name }}</td>
                                    <td>{{ @$result->rendering_provider_short_name }}</td>
                                    <td>{{ @$result->billing_provider_short_name }}</td>
                                    <td>{{ @$result->facility_short_name }}</td>
                                    <td>{{ @$result->responsibility }}</td>
                                    <td>{{ @$result->insurance_category }}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->total_charge) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->tot_paid) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->tot_adj) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->pat_due) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->ins_due) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->ar_due) !!}</td>
                                    <td>{{ @$result->claim_age_days }}</td>
                                    <td>{{ @$result->claim_status }}</td>
									<td>@if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){{ $result->sub_status_desc}}@endif</td>
                                    <td>{{ @$result->workbench_status }}</td>
                                    <td>
                                        @if(date("m/d/y") == $result->fllowup_date)
                                                <span class="med-orange">{{@$result->fllowup_date}}</span>
                                        @elseif(date("m/d/y") >= $result->fllowup_date)
                                                <span class="med-red">{{@$result->fllowup_date}}</span>
                                        @else
                                                <span class="med-gray">{{@$result->fllowup_date}}</span>
                                        @endif
                                    </td>
                                    <td>{{ App\Http\Helpers\Helpers::shortname($result->assign_user_id) }}</td>							
                                </tr>
                                <?php 
                                    } else {
                                ?>
                                <?php
                                    $last_name = @$result->patient->last_name;
                                    $first_name = @$result->patient->first_name;
                                    $middle_name = @$result->patient->middle_name;
                                    $patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                                    $fin_details = @$result->claim->pmt_claim_fin_data;
                                    $pat_due = ($result->claim->insurance_id == 0)?@$fin_details->total_charge-(@$fin_details->patient_paid + @$fin_details->patient_adj+ @$fin_details->insurance_paid+ @$fin_details->insurance_adj+ @$fin_details->withheld):0;
                                    $ins_due = ($result->claim->insurance_id != 0) ? @$fin_details->total_charge-(@$fin_details->patient_paid+ @$fin_details->patient_adj+ @$fin_details->insurance_paid+ @$fin_details->insurance_adj+ @$fin_details->withheld):0;
                                    $tot_adj = @$fin_details->patient_adj + @$fin_details->insurance_adj+ @$fin_details->withheld;
                                    $tot_paid = @$fin_details->patient_paid + @$fin_details->insurance_paid;
                                    $ar_due = @$fin_details->total_charge-(@$fin_details->patient_paid+@$fin_details->patient_adj + @$fin_details->insurance_paid+ @$fin_details->insurance_adj+ @$fin_details->withheld);
                                    $fllowup_date = date("m/d/y", strtotime(@$result->fllowup_date));
                                    $fllowup_date = date("m/d/y", strtotime(@$result->fllowup_date));
                                    $responsibility = 'Patient';
                                    $ins_category = 'Patient';
                                    if($result->claim->insurance_details){
                                        $responsibility = App\Http\Helpers\Helpers::getInsuranceName(@$result->claim->insurance_details->id);
                                        $ins_category= @$result->insurance_category;	 
                                    }
                                ?>
                                <tr style="cursor:default;">
                                    <td>{{@$result->claim->claim_number}}</td>
                                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$result->claim->date_of_service,'claimdate') }}</td>
                                    <td>{{ $patient_name }}</td>							
                                    <td>
                                        @if(@$result->claim->rendering_provider->short_name !='')
                                        {{ str_limit(@$result->claim->rendering_provider->short_name,25,'...') }}
                                        @else
                                        -Nil-
                                        @endif
                                    </td>
                                    <td>
                                        @if(@$result->claim->billing_provider->short_name != '')
                                        {{ str_limit(@$result->claim->billing_provider->short_name,25,'...') }}
                                        @else
                                        -Nil-
                                        @endif
                                    </td>
                                    <td>
                                        @if(@$result->claim->facility_detail->short_name != '')
                                        {{ str_limit(@$result->claim->facility_detail->short_name,25,'...') }} 
                                        @else
                                        -Nil-
                                        @endif
                                    </td>
                                    <td>{{ $responsibility }}</td>							
                                    <td>{{ $ins_category }}</td>							
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->claim->total_charge) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$tot_paid) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$tot_adj) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$pat_due) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$ins_due) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$ar_due) !!}</td>
                                    <td>{{ @$result->claim->claim_age_days }}</td>
                                    <td>{{ @$result->claim->status }}</td>
									<td>@if(isset($result->sub_status_desc) && $result->sub_status_desc != null){{ $result->sub_status_desc}}@else-Nil-@endif</td>
                                    <td>{{ @$result->status }}</td>
                                    <td>
                                        @if(date("m/d/y") == $fllowup_date)
                                            <span class="med-orange">{{$fllowup_date}}</span>
                                        @elseif(date("m/d/y") >= $fllowup_date)
                                            <span class="med-red">{{$fllowup_date}}</span>
                                        @else
                                            <span class="med-gray">{{$fllowup_date}}</span>
                                        @endif
                                    </td>
                                    <td>{{ App\Http\Helpers\Helpers::shortname($result->assign_user_id) }}</td>							
                                </tr>
                                <?php } ?>
                                @endforeach                
				   
                            </tbody>
                        </table>    
			 
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                                    Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
                    </div>
		</div>	
		@else
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
		@endif
    
    </div><!-- Box Body Ends --> 

</div><!-- /.box Ends-->