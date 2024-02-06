<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Appointment Analysis Report</title>
        <style>
            table tbody tr  td{
                font-size: 9px !important;
                border: none !important;
            }
            table tbody tr th {
                text-align:center !important;
                font-size:10px !important;                
                color:#000 !important;
                border:none !important;
                border-radius: 0px !important;
            }
            table thead tr th{border-bottom: 5px solid #000 !important;font-size:10px !important}
            .text-right{text-align: right;}
            .text-left{text-align: left;}
            .text-center{text-align: center;}
            .med-green{color: #00877f;}
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head> 
    <body> 
        <?php 
        $appt_result = $result['appt_result'];
        $user_names = $result['user_names'];
        $createdBy = $result['createdBy'];
        $practice_id = $result['practice_id'];
        $search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>
                <td colspan="17" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="17" style="text-align:center;">Appointment Analysis Report</td>
            </tr>
            <tr>
                <td colspan="17" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="17" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span>{!! $key !!} :  </span>{{ @$val[0] }}
                        <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count($appt_result) > 0)
        <table>
            <thead>
                  <tr>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:10px;">Appt Date</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Appt Time</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Appt Status</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Canceled Reason</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:10px;">Acc No</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:24px;">Rendering</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:10px;">Facility</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:22px;">Reason for Visit</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:19px;">Responsibility</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Eligibility</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:18px;">Co-Pay Amt($)</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Mode of Pmt</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:12px;">Paid Date</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Future Appt</th>                                                        
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Pat Bal($)</th>                                                        
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:10px;">User</th>                                                        
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Created Date</th>                                                         
                    </tr>             
            </thead>   
            <tbody> 
               <?php   $total_amt_charge = 0;$total_amt_payment=0; ?>
                    @foreach($appt_result as  $result)
                    <?php //From Stored procedure
                        if(isset($result->account_no) && $result->account_no != ''){
                    ?>
                    <tr>
                        <td>{!! @$result->scheduled_on !!}</td>
                        <td>{!! @$result->appointment_time !!}</td>
                        <td>{!! @$result->status !!}</td>
                        <td>{!! @$result->cancel_delete_reason !!}</td>
                        <td>{!! @$result->account_no !!}</td>
                        <td>{!! @$result->patient_name !!}</td>
                        <td>{!! @$result->rendering_short_name !!} - {!! @$result->rendering_name !!}</td>
                        <td>{!! @$result->facility_short_name !!} - {!! @$result->facility_name !!}</td>
                        <td>{!! @$result->reason !!}</td>
                        <td>{!! @$result->responsibility !!} - {!! @$result->insurance_name !!}</td>
                        <td>{!! @$result->eligibility !!}</td>
                        <td style="<?php echo($result->copay)<0?'color:#ff0000;':'' ?> text-align:right;"  data-format="#,##0.00">{!! @$result->copay !!}</td>
                        <td>{!! $result->copay_option !!}</td>
                        <td>{!! @$result->copay_date !!}</td>
                        <td>{!! @$result->next_appt !!}</td>
                        <td style="<?php echo($result->total_pat_due)<0?'color:#ff0000;':'' ?> text-align:right;"  data-format="#,##0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->total_pat_due) !!}</td>
                        <td>
                            @if($result->created_by != 0 )
                                {!! \App\Http\Helpers\Helpers::user_names($result->created_by) !!} - {!! \App\Http\Helpers\Helpers::getUserFullName($result->created_by) !!}
                            @endif
                        </td>
                        <td>{!! @$result->created_at !!}</td>
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
                        @$responsibility = $result->patient->patient_insurance[0]->insurance_details->insurance_name;
                    } else{
                        $responsibility = 'Self Pay';
                    } 
                    if($result->copay != ''){
                        $co_pay = App\Http\Helpers\Helpers::priceFormat(@$result->copay);
                    }else{ 
                        $co_pay = '0.00'; 
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
                        <td>{{ App\Http\Helpers\Helpers::timezone(@$result->scheduled_on, 'm/d/y') }}</td> 
                        <td>{!! @$result->appointment_time !!}</td> 
                        <td>{{ @$result->status}}</td> 
                        <td>{!! (!empty(@$result->cancel_delete_reason))? @$result->cancel_delete_reason:'-Nil-' !!}</td>
                        <td>{{ @$result->patient->account_no}}</td> 
                        <td>{!! @$patient_name !!}</td>
                        <td>{{ @$result->provider->short_name}} - {{ @$result->provider->provider_name}}</td> 
                        <td>{{ @$result->facility->short_name}} - {{ @$result->facility->facility_name}}</td> 
                        <td>{{ @$result->reasonforvisit->reason}}</td> 
                        <td>{{ @$responsibility}}</td> 
                        <td>{{ @$eligibility}}</td> 
                        <td style="<?php echo($co_pay)<0?'color:#ff0000;':'' ?> text-align:right;" data-format="#,##0.00">{!! $co_pay !!}</td> 
                        <td>{{ $result->copay_option }}</td> 
                        <td>{!! $result->copay_date !!}</td>
                        <td>{!! isset($result->next_appt)?App\Http\Helpers\Helpers::dateFormat(@$result->next_appt):'-Nil-' !!}</td>
                        <td style="<?php echo(@$result->patient->patient_claim_fin[0]->total_pat_due)<0?'color:#ff0000;':'' ?> text-align:right;"  data-format="#,##0.00">{!! (!empty(@$result->patient->patient_claim_fin[0]->total_pat_due))? @$result->patient->patient_claim_fin[0]->total_pat_due: '0.00' !!}</td>       
                        <td>{{ @$result->created_user->short_name}} - {{ @$result->created_user->name}}</td> 
                        <td>{{ App\Http\Helpers\Helpers::timezone(@$result->created_at, 'm/d/y') }}</td> 
                   </tr>
                   <?php } ?>
                @endforeach
            </tbody>   
        </table>
        @endif
        <td colspan="17">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>