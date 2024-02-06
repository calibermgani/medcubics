<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important; padding: 10px;
            }
            .summary-table table tbody tr:nth-of-type(odd) td{
                border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;
            }
            th {
                text-align:center !important;
                font-size:10px !important;
                font-weight: 600 !important;
                border-top: 1px solid #ccc;
                border-bottom: 1px solid #ccc;                
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 13px !important;}
            .table-summary tbody tr{line-height: 22px !important;} 
            .table-summary tbody tr td{font-size:11px !important;} 
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;
                padding-top:40px;
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right; padding-right:5px;}
            .text-left{text-align: left;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .text-center{text-align: center !important;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
            .med-red {color: #ff0000 !important;}
        </style>
    </head>	
    <body>
        <?php 
            $appt_result = $result['appt_result'];
            $search_by = $result['search_by'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
            foreach ($appt_result as $key => $value) {
                    $abb_facility[] = @$value->facility->short_name." - ".@$value->facility->facility_name;
                    $abb_rendering[] = @$value->provider->short_name." - ".@$value->provider->provider_name;
                    $abb_user[] = @$value->created_user->short_name." - ".@$value->created_user->name;
                }
                $abb_facility = array_unique($abb_facility);
                $abb_rendering = array_unique($abb_rendering);
                $abb_user = array_unique($abb_user);
                foreach (array_keys($abb_facility, ' - ') as $key) {
                    unset($abb_facility[$key]);
                }
                foreach (array_keys($abb_rendering, ' - ') as $key) {
                    unset($abb_rendering[$key]);
                }
                foreach (array_keys($abb_user, ' - ') as $key) {
                    unset($abb_user[$key]);
                }
                $facility_imp = implode(':', $abb_facility);
                $rendering_imp = implode(':', $abb_rendering);
                $user_imp = implode(':', $abb_user);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Appointment Analysis Report</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 0; ?>
                            @foreach($search_by as $key=>$val)
                                @if($i > 0){{' | '}}@endif
                                <span>{!! $key !!} :  </span>{{ @$val }}
                                <?php $i++; ?>
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="5" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="5" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="">
            @if(!empty($appt_result))
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse;">
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
                    @foreach($appt_result as  $result)
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
                    if($result->copay != ''){
                        $co_pay = App\Http\Helpers\Helpers::priceFormat(@$result->copay);
                    }else{ 
                        $co_pay = '-Nil-'; 
                    }
                    if(@$result->copay_date != '0000-00-00'){
                        $result->copay_date = App\Http\Helpers\Helpers::dateFormat(@@$result->copay_date);
                    }else{
                         $result->copay_date = '-Nil-';
                    } 
                    if($result->copay_option != ''){
                        $result->copay_option = @$result->copay_option;
                    }else{
                        $result->copay_option = '-Nil-';
                    }                                
                    ?>
                    <tr>
                       
                        <td >{{ App\Http\Helpers\Helpers::timezone(@$result->scheduled_on, 'm/d/y') }}</td> 
                        <td >{!! @$result->appointment_time !!}</td> 
                        <td >{{ @$result->status}}</td> 
                        <td>{!! @$result->cancel_delete_reason !!}</td>
                        <td >{{ @$result->patient->account_no}}</td> 
                        <td >{!! @$patient_name !!}</td> 

                        <td >{{ @$result->provider->short_name}}</td> 
                        <td >{{ @$result->facility->short_name}}</td> 
                        <td >{{ @$result->reasonforvisit->reason}}</td> 
                        <td >{{ @$responsibility}}</td>  
                        <td >{{ @$eligibility}}</td> 
                        <td  class="text-right">{!! $co_pay !!}</td> 
                        <td >{{ $result->copay_option}}</td> 
                        <td >{!! $result->copay_date !!}</td>
                        <td >{!! App\Http\Helpers\Helpers::dateFormat(@$result->next_appt) !!}</td> 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->patient->patient_claim_fin[0]->total_pat_due) !!}</td>       
                        <td >{{ @$result->created_user->short_name}}</td> 
                       <td >{{ App\Http\Helpers\Helpers::timezone(@$result->created_at, 'm/d/y') }}</td> 
                   </tr>
                @endforeach
                 </tbody>   
                </table>		
            </div>
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif
            <ul style="line-height:20px;">
                <li>{{$facility_imp}}</li>
                <li>{{$rendering_imp}}</li>
                <li>{{$user_imp}}</li>
            </ul>
        </div>        
    </body>
</html>