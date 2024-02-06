<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Refund Analysis</title>
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
        $column = $result['column'];
        $total_refund = $result['total_refund'];
        $refund_type = $result['refund_type'];
        $refund_result = $result['refund_result'];
        $get_refund_datas = $result['get_refund_datas'];
        $start_date = $result['start_date'];
        $end_date = $result['end_date'];
        $wallet = $result['wallet'];
        $unposted = $result['unposted'];
        $createdBy = $result['createdBy'];
        $practice_id = $result['practice_id'];
        $user_names = $result['user_names'];
        $export = $result['export'];
        $search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails();
        if($refund_type == 'insurance') {
            $colspan = 12;
        } elseif($refund_type == 'patient' && empty($unposted) && empty($wallet)) {
            $colspan = 4;
        } elseif(!is_null($unposted) && !empty($unposted) || !is_null($wallet) && !empty($wallet)) {
            $colspan = 5;
        }
        ?>
        <table>
            <tr>
                <td colspan="{{$colspan}}" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="{{$colspan}}" style="text-align:center;">Refund Analysis - Detailed</td>
            </tr>
            <tr>
                <td colspan="{{$colspan}}" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="{{$colspan}}" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!} : </span>{{ @$val[0] }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if($refund_type == 'insurance' && empty($unposted))
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Billing</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Check Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Check No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Refund Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">User</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($get_refund_datas) && $get_refund_datas != '')
                @foreach(@$get_refund_datas as $refund_value)
                <?php
                    $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->claim->patient->last_name,@$refund_value->claim->patient->first_name,@$refund_value->claim->patient->middle_name);
                    $insurance_name = App\Models\Insurance::where('id', @$refund_value->insurance_id)->value("insurance_name");
                ?>
                <tr>
                    <td style="text-align: left;">{{ @$refund_value->claim->claim_number }}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$refund_value->claim->date_of_service, 'dob') }}</td>
                    <td style="text-align: left;">{{ @$refund_value->claim->patient->account_no }}</td>
                    <td>{{ @$patient_name }}</td>
                    <td>@if(isset($refund_value->claim->rendering_provider->id) && $refund_value->claim->rendering_provider->id !=''){{ @$refund_value->claim->rendering_provider->short_name }} - {{ @$refund_value->claim->rendering_provider->provider_name }} @else -Nil- @endif</td>
                    <td>@if(isset($refund_value->claim->billing_provider->id) && $refund_value->claim->billing_provider->id !=''){{ @$refund_value->claim->billing_provider->short_name }} - {{ @$refund_value->claim->billing_provider->provider_name }} @else -Nil- @endif</td>
                    <td>@if(isset($refund_value->claim->facility_detail->id) && $refund_value->claim->facility_detail->id !=''){{ @$refund_value->claim->facility_detail->short_name }} - {{ @$refund_value->claim->facility_detail->facility_name }} @else -Nil- @endif</td>
                    <td>{{ @$insurance_name}}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$refund_value->latest_payment_check->check_details->check_date, 'dob') }}</td>
                    <td class="text-left" style="text-align:left;">{{ @ucwords($refund_value->latest_payment_check->check_details->check_no) }}</td>
                    <td class="<?php echo(abs(@$refund_value->total_paid))<0?'med-red':'' ?>" style="text-align: right;@if(abs(@$refund_value->total_paid) <0) color:#ff0000; @endif"  data-format="#,##0.00">{!! abs(@$refund_value->total_paid) !!}</td>
                    <td>@if(isset($refund_value->created_by) && $refund_value->created_by !=''){{ @$refund_value->user->short_name }} - {{ @ucwords(@$refund_value->user->name) }} @else - @endif</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="3" style="color:#00877f;font-weight: bold;font-weight: 600;font-size:13.5px;"><h3 class="med-green">Summary</h3></td>
            </tr>
            <thead>
                <tr>
                    <th style="font-size:12px;font-weight: bold;font-weight: 600;border-bottom:1px solid black;">Title</th>
                    <th style="border-bottom:1px solid black;"></th>
                    <th style="text-align:right;font-size:12px;font-weight: bold;font-weight: 600;border-bottom:1px solid black;">Value($)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" style="font-size:9px;font-weight: bold;font-weight: 600;">Total Insurance Refunds</td>
                    <td class="<?php echo(@$total_refund->insurance)<0?'med-red':'' ?>" style="font-size:9px;text-align:right; @if(@$total_refund->insurance <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$total_refund->insurance !!}</td>
                </tr>                
            </tbody>
        </table>
            @elseif($refund_type == 'patient' && empty($unposted) && empty($wallet))        
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Refund Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">User</th>
                </tr>
            </thead>
            <tbody>
                @foreach($get_refund_datas as $refund_value)
                <?php
                    $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->claim_patient_det->last_name,@$refund_value->claim_patient_det->first_name,@$refund_value->claim_patient_det->middle_name);
                    $wallet_refund = @$refund_value->claim_patient_det->pmt_info[0]->refund_amt;
                    @$refund_amt = abs(@$refund_value->total_paid);
                ?>
                <tr>
                    <td>{{ @$patient_name }}</td>
                    <td>{{ @$refund_value->claim_patient_det->account_no }}</td>
                    <td class="<?php echo(@$refund_amt)<0?'med-red':'' ?>" style="text-align:right;<?php echo(@$refund_amt)<0?'color:#ff0000;':'' ?>" data-format="#,##0.00">{{ number_format(@$refund_amt) }}</td>
                    <td>@if(isset($refund_value->created_by) && $refund_value->created_by !=''){{ @$refund_value->user->short_name }} - {{ @ucwords(@$refund_value->user->name) }} @else - @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="3" style="color:#00877f;font-weight: bold;font-weight: 600;font-size:13.5px;"><h3 style="font-size:20px;">Summary</h3></td>
            </tr>
            <thead>
                <tr>
                    <th style="font-size:12px;font-weight: bold;font-weight: 600;border-bottom:1px solid black;">Title</th>
                    <th style="border-bottom:1px solid black;"></th>
                    <th style="text-align:right;font-size:12px;font-weight: bold;font-weight: 600;border-bottom:1px solid black;">Value($)</th>
                </tr>
            </thead>
            <tbody>
                <tr> 
                    <td colspan="2" style="font-size:9px;font-weight: bold;font-weight: 600;">Total Patients Refunds</td>
                    <td class="<?php echo(@$total_refund->patient)<0?'med-red':'' ?>" style="text-align:right; @if(@$total_refund->patient < 0) color:#ff000; @endif" data-format="#,##0.00">{!! @$total_refund->patient  !!}</td>
                </tr>
            </tbody>
        </table>
            @elseif(!is_null($unposted) && !empty($unposted) || !is_null($wallet) && !empty($wallet))
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Check Date</th>   
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Check No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Refund Amt($)</th> 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">User</th> 
                </tr>
            </thead>
            <tbody>
                @foreach($get_refund_datas as $refund_value)
                <?php 
                    $refund_amt = $refund_value->pmt_amt; 
                    $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->patient->last_name,@$refund_value->patient->first_name,@$refund_value->patient->middle_name);
                ?>
                <tr>
                    <td>{{ @$patient_name }}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$refund_value->check_details->check_date) }}</td>
                    <td>{{ @$refund_value->check_details->check_no }}</td>                        
                    <td class="<?php echo(@$refund_amt)<0?'med-red':'' ?>" style="text-align:right; @if(@$refund_amt < 0) color:#ff000; @endif" data-format="#,##0.00">{!! @$refund_amt !!}</td>
                    <td>@if(isset($refund_value->created_by) && $refund_value->created_by !=''){{ @$refund_value->created_user->short_name }} - {{ @ucwords(@$refund_value->created_user->name) }} @else - @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="3" style="color:#00877f;font-weight: bold;font-weight: 600;font-size:13.5px;"><h3 class="med-green" >Summary</h3></td>
            </tr>
            <thead>
                <tr>
                    <th style="font-size:12px;font-weight: bold;font-weight: 600;border-bottom:1px solid black;">Title</th>
                    <th style="border-bottom:1px solid black;"></th>
                    <th style="text-align:right;font-size:12px;font-weight: bold;font-weight: 600;border-bottom:1px solid black;">Value($)</th>
                </tr>
            </thead>
            <tbody>
                @if($refund_type == 'insurance')
                <tr> 
                    <td colspan="2" style="font-size:9px;font-weight: bold;font-weight: 600;">Total Insurance Refunds</td>
                    <td class="<?php echo(@$total_refund->insurance)<0?'med-red':'' ?>" style="text-align:right; @if(@$total_refund->insurance < 0) color:#ff000; @endif"  data-format="#,##0.00">{!! @$total_refund->insurance !!}</td>
                </tr>
                @else
                <tr> 
                    <td colspan="2" style="font-size:9px;font-weight: bold;font-weight: 600;">Total Patients Refunds</td>
                    <td class="<?php echo(@$total_refund->patient)<0?'med-red':'' ?>" style="text-align:right; @if(@$total_refund->patient < 0) color:#ff000; @endif"  data-format="#,##0.00">{!! @$total_refund->patient !!}</td>
                </tr>
                @endif
                <tr> 
                    <td colspan="2" style="font-size:9px;font-weight: bold;font-weight: 600;">Total Refunds</td>                            
                    <td class="<?php echo(@$total_refund->total)<0?'med-red':'' ?>" style="text-align:right; @if(@$total_refund->total < 0) color:#ff000; @endif"  data-format="#,##0.00">{!! @$total_refund->total !!}</td>
                </tr>
            </tbody>
        </table>
        @endif   
        <table>
            <tr>
                <td colspan="{{$colspan}}" style="">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>