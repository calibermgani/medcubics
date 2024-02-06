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
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Appointment Analysis Report</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                     @if($i > 0){{' | '}}@endif
                           <span class="med-green">{!! $key !!}: </span>{{ @$val[0] }}                           
                          <?php $i++; ?>
                     @endforeach </div>                   
                </div>                
            </div>
        </div>


     @if(count($appt_result) > 0)  
<div class="box-body no-padding">  
    <div class="table-responsive mobile-lg-scroll mobile-md-scroll col-lg-12 no-padding">
            <table class="table table-striped table-bordered table-separate mobile-lg-width" id="sort_list_noorder_report">   
                <thead>
                    <tr>
                        <th>Appt Date</th>
                        <th>Appt Time</th>
                        <th>Appt Status</th>
                        <th>Canceled Reason</th>                                                        
                        <th>Acc No</th>
                        <th>Patient Name</th>
                        <th>Rendering</th>
                        <th>Facility</th>
                        <th>Reason for Visit</th>
                        <th>Responsibility</th>
                        <th>Eligibility</th>
                        <th>Co-Pay Amt($)</th>
                        <th>Mode of Pmt</th>
                        <th>Paid Date</th>
                        <th>Future Appt</th>                                                        
                        <th>Pat Bal($)</th>                                                        
                        <th>User</th>                                                        
                        <th>Created Date</th>                                                        
                    </tr>
                </thead>
                <tbody>
                   <?php   $total_amt_charge = 0;$total_amt_payment=0;?>
                    @foreach($appt_result as  $result)
                    <?php //From Stored procedure
                        if(isset($result->account_no) && $result->account_no != ''){
                    ?>
                    <tr style="cursor:default;">
                        <td>{!! !empty($result->scheduled_on)? @$result->scheduled_on : '-Nil-' !!}</td>
                        <td>{!! !empty($result->appointment_time)? @$result->appointment_time : '-Nil-' !!}</td>
                        <td>{!! !empty($result->status)? @$result->status : '-Nil-' !!}</td>
                        <td>{!! !empty($result->cancel_delete_reason)? @$result->cancel_delete_reason : '-Nil-' !!}</td>
                        <td>{!! !empty($result->account_no)? @$result->account_no : '-Nil-' !!}</td>
                        <td>{!! !empty($result->patient_name)? @$result->patient_name : '-Nil-' !!}</td>
                        <td>{!! !empty($result->rendering_short_name)? @$result->rendering_short_name : '-Nil-' !!}</td>
                        <td>{!! !empty($result->facility_short_name)? @$result->facility_short_name : '-Nil-' !!}</td>
                        <td>{!! !empty($result->reason)? @$result->reason : '-Nil-' !!}</td>
                        <td>{!! !empty($result->responsibility)? @$result->responsibility : '-Nil-' !!}</td>
                        <td>{!! !empty($result->eligibility)? @$result->eligibility : '-Nil-' !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->copay) !!}</td>
                        <td>{!! !empty($result->copay_option)? $result->copay_option : '-Nil-' !!}</td>
                        <td>{!! !empty($result->copay_date)? @$result->copay_date : '-Nil-' !!}</td>
                        <td>{!! !empty($result->next_appt)? @$result->next_appt : '-Nil-' !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->total_pat_due) !!}</td>
                        <td>
                            @if($result->created_by != 0 && isset($user_names[@$result->created_by]) )
                                {!! !empty($result->created_by)? $user_names[@$result->created_by] : '-Nil-' !!}
                            @endif
                        </td>
                        <td>{!! !empty($result->created_at)? @$result->created_at : '-Nil-' !!}</td>
                   </tr>
                    <?php
                        } else {
                    ?>
                    <?php                                                 
                       //   $insurance_payment[] = $dates->insurance_payment;
                        //  $patient_payment[] = $dates->patient_payment; 
                    $last_name = $result->patient->last_name;
                    $first_name = $result->patient->first_name;
                    $middle_name = $result->patient->middle_name;
                    $patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                    if($result->patient->eligibility_verification == 'None'){
                      $eligibility = 'Unverified';
                    }else if($result->patient->eligibility_verification == 'Active'){
                      $eligibility = 'Eligible';
                    }else if($result->patient->eligibility_verification == 'Inactive'){
                      $eligibility = 'Ineligible';
                    }else{
                        $eligibility = 'Error';
                    } 
                    if($result->patient->is_self_pay == 'No'){
                        @$responsibility = $result->patient->patient_insurance[0]->insurance_details->short_name;
                    } else{
                        $responsibility = 'Self Pay';
                    }
                    if(@$result->copay_date != '0000-00-00'){
                        $result->copay_date = App\Http\Helpers\Helpers::dateFormat(@$result->copay_date);
                    }else{
                         $result->copay_date = '-Nil-';
                    } 
                    if($result->copay_option != ''){
                        $result->copay_option = @$result->copay_option;
                    }else{
                        $result->copay_option = '-Nil-';
                    }                                     
                    ?>
                    <tr style="cursor:default;">
                        <td>{{ !empty($result->scheduled_on)? App\Http\Helpers\Helpers::timezone(@$result->scheduled_on, 'm/d/y') : '-Nil-' }}</td>
                        <td>{!! !empty($result->appointment_time)? @$result->appointment_time : '-Nil-' !!}</td>
                        <td>{{ !empty($result->status)? @$result->status : '-Nil-' }}</td>
                        <td>{{ (!empty(@$result->cancel_delete_reason))? @$result->cancel_delete_reason:'-Nil-' }}</td>
                        <td>{{ !empty($result->patient->account_no)? @$result->patient->account_no : '-Nil-' }}</td>
                        <td>{!! !empty($patient_name)? @$patient_name : '-Nil-' !!}</td>
                        <td>{{ !empty($result->provider->short_name)? @$result->provider->short_name : '-Nil-' }}</td>
                        <td>{{ !empty($result->facility->short_name)? @$result->facility->short_name : '-Nil-' }}</td>
                        <td>{{ !empty($result->reasonforvisit->reason)? @$result->reasonforvisit->reason : '-Nil-' }}</td>
                        <td>{{ !empty($responsibility)? @$responsibility : '-Nil-' }}</td>
                        <td>{{ @$eligibility}}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->copay) !!}</td>
                        <td>{{ !empty($result->copay_option)? $result->copay_option : '-Nil-' }}</td>
                        <td>{!! !empty($result->copay_date)? @$result->copay_date : '-Nil-' !!}</td>
                        <td>{!! !empty($result->next_appt)?App\Http\Helpers\Helpers::dateFormat(@$result->next_appt):'-Nil-' !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->patient->patient_claim_fin[0]->total_pat_due) !!}</td>
                        <td>{{ !empty($result->created_user->short_name)? @$result->created_user->short_name : '-Nil-' }}</td>
                        <td>{{ !empty($result->created_at)? App\Http\Helpers\Helpers::timezone(@$result->created_at, 'm/d/y') : '-Nil-' }}</td>
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
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends -->
</div><!-- /.box Ends-->