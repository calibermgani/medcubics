<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Month End Performance Summary</title>
        <style>
            table tbody tr  td{
                font-size: 9px ;
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
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600 !important;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>	
    <body>
        <?php 
			@$FacilityWiseOutstanding = $result['FacilityWiseOutstanding'];
			@$insuranceClaimsByFacility = $result['insuranceClaimsByFacility'];
			@$facilityStatus = $result['facilityStatus'];
			@$days = $result['days'];
			@$resultDays = $result['resultDays'];
			@$createdBy = $result['createdBy'];
			@$practice_id = $result['practice_id'];
			@$searchBy = $result['searchBy'];
			@$heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="16" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="16" style="text-align:center;">Month End Performance Summary Report</td>
            </tr>
            <tr>
                <td colspan="16" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="16" style="text-align:center;">
                    <?php $i = 1; ?>
                    @if(isset($searchBy) && !empty($searchBy))
                    @foreach($searchBy as $header_name => $header_val)
                    <span>
                        {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i< count((array)$searchBy)) | @endif 
                    <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table><?php /*
        <table><tr><td colspan="16" class="font600" style=" ;font-weight:600;font-size:13px;"> Outstanding AR - By Location</td></tr></table>
        <table>
            <thead>
                <tr>
                    <th style="text-align: center; ;font-weight:600;border-bottom:1px solid black;font-size:12px;width:24px;">Facility</th>
                    <th colspan="2"  style="text-align: center; ;font-weight:600;border-bottom:1px solid black;font-size:12px;">Unbilled</th>
                    <th colspan="2" style="text-align: center; ;font-weight:600;border-bottom:1px solid black;font-size:12px;">0-30</th>
                    <th colspan="2" style="text-align: center; ;font-weight:600;border-bottom:1px solid black;font-size:12px;">31-60</th>
                    <th colspan="2" style="text-align: center; ;font-weight:600;border-bottom:1px solid black;font-size:12px;">61-90</th>
                    <th colspan="2" style="text-align: center; ;font-weight:600;border-bottom:1px solid black;font-size:12px;">91-120</th>
                    <th colspan="2" style="text-align: center; ;font-weight:600;border-bottom:1px solid black;font-size:12px;">121-150</th>
                    <th colspan="2" style="text-align: center; ;font-weight:600;border-bottom:1px solid black;font-size:12px;">>150</th>
                    <th style="text-align: center; ;font-weight:600;border-bottom:1px solid black;font-size:12px;">Totals</th>
                </tr>
                <tr>
                    <td class="font600 text-center" ></td>
                    <td class="font600 text-center " style="text-align:center;font-weight:600;width:14px;">Claims</td>
                    <td class="font600 text-center" style="border-top:1px solid black;text-align:center;font-weight:600;width:14px;font-size:11px;">Value($)</td>
                    <td class="font600 text-center "  style="text-align:center;font-weight:600;width:14px;">Claims</td>
                    <td class="font600 text-center" style="border-top:1px solid black;text-align:center;font-weight:600;width:14px;font-size:11px;">Value($)</td>
                    <td class="font600 text-center" style="text-align:center;font-weight:600;width:14px;">Claims</td>
                    <td class="font600 text-center" style="border-top:1px solid black;text-align:center;font-weight:600;width:14px;font-size:11px;">Value($)</td>
                    <td class="font600 text-center" style="text-align:center;font-weight:600;width:14px;">Claims</td>
                    <td class="font600 text-center" style="border-top:1px solid black;text-align:center;font-weight:600;width:14px;font-size:11px;">Value($)</td>
                    <td class="font600 text-center" style="text-align:center;font-weight:600;width:14px;">Claims</td>
                    <td class="font600 text-center" style="border-top:1px solid black;text-align:center;font-weight:600;width:14px;font-size:11px;">Value($)</td>
                    <td class="font600 text-center" style="text-align:center;font-weight:600;width:14px;">Claims</td>
                    <td class="font600 text-center" style="border-top:1px solid black;text-align:center;font-weight:600;width:14px;font-size:11px;">Value($)</td>
                    <td class="font600 text-center" style="text-align:center;font-weight:600;width:14px;">Claims</td>
                    <td class="font600 text-center" style="border-top:1px solid black;text-align:center;font-weight:600;width:14px;font-size:11px;">Value($)</td>
                    <td class="font600 text-center"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $claim_counts_unbilled = $claim_counts_0_30 = $claim_counts_31_60 = $claim_counts_61_90 = $claim_counts_91_120 = $claim_counts_121_150 = $claim_counts_150 = $tot_ar_unbilled = $tot_ar_0_30 = $tot_ar_31_60 = $tot_ar_61_90 = $tot_ar_91_120 = $tot_ar_121_150 = $tot_ar_150  = 0;
                ?>
                @if(!empty($FacilityWiseOutstanding))
                    @foreach($FacilityWiseOutstanding as $key=>$value)
                    <tr>
                        <td class="font600" style="font-weight:600;font-size:9px;" >{{$key}}</td>
                        <?php
                        $claim_count_unbilled = isset($value['Unbilled']['claim_count'])?$value['Unbilled']['claim_count']:0;
                        $claim_count_0_30 = isset($value['0-30']['claim_count'])?$value['0-30']['claim_count']:0;
                        $claim_count_31_60 = isset($value['31-60']['claim_count'])?$value['31-60']['claim_count']:0;
                        $claim_count_61_90 = isset($value['61-90']['claim_count'])?$value['61-90']['claim_count']:0;
                        $claim_count_91_120 = isset($value['91-120']['claim_count'])?$value['91-120']['claim_count']:0;
                        $claim_count_121_150 = isset($value['121-150']['claim_count'])?$value['121-150']['claim_count']:0;
                        $claim_count_150 = isset($value['>150']['claim_count'])?$value['>150']['claim_count']:0;
                        $total_ar_unbilled = isset($value['Unbilled']['total_ar'])?$value['Unbilled']['total_ar']:0.00;
                        $total_ar_0_30 = isset($value['0-30']['total_ar'])?$value['0-30']['total_ar']:0.00;
                        $total_ar_31_60 = isset($value['31-60']['total_ar'])?$value['31-60']['total_ar']:0.00;
                        $total_ar_61_90 = isset($value['61-90']['total_ar'])?$value['61-90']['total_ar']:0.00;
                        $total_ar_91_120 = isset($value['91-120']['total_ar'])?$value['91-120']['total_ar']:0.00;
                        $total_ar_121_150 = isset($value['121-150']['total_ar'])?$value['121-150']['total_ar']:0.00;
                        $total_ar_150 = isset($value['>150']['total_ar'])?$value['>150']['total_ar']:0.00;
                        $claim_counts_unbilled += $claim_count_unbilled;
                        $claim_counts_0_30 += $claim_count_0_30;
                        $claim_counts_31_60 += $claim_count_31_60;
                        $claim_counts_61_90 += $claim_count_61_90;
                        $claim_counts_91_120 += $claim_count_91_120;
                        $claim_counts_121_150 += $claim_count_121_150;
                        $claim_counts_150 += $claim_count_150;
                        $tot_ar_unbilled += $total_ar_unbilled;
                        $tot_ar_0_30 += $total_ar_0_30;
                        $tot_ar_31_60 += $total_ar_31_60;
                        $tot_ar_61_90 += $total_ar_61_90;
                        $tot_ar_91_120 += $total_ar_91_120;
                        $tot_ar_121_150 += $total_ar_121_150;
                        $tot_ar_150 += $total_ar_150;
                        ?>
                        <td class="text-center" style="text-align:center;font-size:9px;">{{$claim_count_unbilled}}</td>
                        <td class="text-right"  style="text-align:right;font-size:9px;@if($total_ar_unbilled <0) color:#ff0000; @endif"  data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_unbilled) !!}</td>
                        <td class="text-center" style="text-align:center;font-size:9px;">{{$claim_count_0_30}}</td>
                        <td class="text-right"  style="text-align:right;font-size:9px;@if($total_ar_0_30 <0) color:#ff0000; @endif"  data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_0_30) !!}</td>
                        <td class="text-center" style="text-align:center;font-size:9px;">{{$claim_count_31_60}}</td>
                        <td class="text-right"  style="text-align:right;font-size:9px;@if($total_ar_31_60 <0) color:#ff0000; @endif"  data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_31_60) !!}</td>
                        <td class="text-center" style="text-align:center;font-size:9px;">{{$claim_count_61_90}}</td>
                        <td class="text-right"  style="text-align:right;font-size:9px;@if($total_ar_61_90 <0) color:#ff0000; @endif"  data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_61_90) !!}</td>
                        <td class="text-center" style="text-align:center;font-size:9px;">{{$claim_count_91_120}}</td>
                        <td class="text-right"  style="text-align:right;font-size:9px;@if($total_ar_91_120 <0) color:#ff0000; @endif"  data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_91_120) !!}</td>
                        <td class="text-center" style="text-align:center;font-size:9px;">{{$claim_count_121_150}}</td>
                        <td class="text-right"  style="text-align:right;font-size:9px;@if($total_ar_121_150 <0) color:#ff0000; @endif"  data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_121_150) !!}</td>
                        <td class="text-center" style="text-align:center;font-size:9px;">{{$claim_count_150}}</td>
                        <td class="text-right"  style="text-align:right;font-size:9px;@if($total_ar_150 <0) color:#ff0000; @endif"  data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_150) !!}</td>
                        <td class="text-right"  style="text-align:right;font-size:9px;@if($total_ar_unbilled+$total_ar_0_30+$total_ar_31_60+$total_ar_61_90+$total_ar_91_120+$total_ar_121_150+$total_ar_150 <0) color:#ff0000; @endif"  data-format="#,##0.00">{!!  \App\Http\Helpers\Helpers::priceFormat($total_ar_unbilled+$total_ar_0_30+$total_ar_31_60+$total_ar_61_90+$total_ar_91_120+$total_ar_121_150+$total_ar_150) !!}</td>
                    </tr>
                    @endforeach       
                    <tr>
                        <td class="font600" style="font-weight:600;font-size:9px;">Totals</td>
                        <td class="font600 text-center" style="text-align:center;font-size:9px;font-weight:600;">{{$claim_counts_unbilled}}</td>
                        <td class="font600 text-right"  style="text-align:right;font-size:9px;font-weight:600;@if($tot_ar_unbilled <0) color:#ff0000; @endif" data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_unbilled) !!}</td>
                        <td class="font600 text-center" style="text-align:center;font-size:9px;font-weight:600;">{{$claim_counts_0_30}}</td>
                        <td class="font600 text-right"  style="text-align:right;font-size:9px;font-weight:600;@if($tot_ar_0_30 <0) color:#ff0000; @endif" data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_0_30) !!}</td>
                        <td class="font600 text-center" style="text-align:center;font-size:9px;font-weight:600;">{{$claim_counts_31_60}}</td>
                        <td class="font600 text-right"  style="text-align:right;font-size:9px;font-weight:600;@if($tot_ar_31_60 <0) color:#ff0000; @endif" data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_31_60) !!}</td>
                        <td class="font600 text-center" style="text-align:center;font-size:9px;font-weight:600;">{{$claim_counts_61_90}}</td>
                        <td class="font600 text-right"  style="text-align:right;font-size:9px;font-weight:600;@if($tot_ar_61_90 <0) color:#ff0000; @endif" data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_61_90) !!}</td>
                        <td class="font600 text-center" style="text-align:center;font-size:9px;font-weight:600;">{{$claim_counts_91_120}}</td>
                        <td class="font600 text-right"  style="text-align:right;font-size:9px;font-weight:600;@if($tot_ar_91_120 <0) color:#ff0000; @endif" data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_91_120) !!}</td>
                        <td class="font600 text-center" style="text-align:center;font-size:9px;font-weight:600;">{{$claim_counts_121_150}}</td>
                        <td class="font600 text-right"  style="text-align:right;font-size:9px;font-weight:600;@if($tot_ar_121_150 <0) color:#ff0000; @endif" data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_121_150) !!}</td>
                        <td class="font600 text-center" style="text-align:center;font-size:9px;font-weight:600;">{{$claim_counts_150}}</td>
                        <td class="font600 text-right"  style="text-align:right;font-size:9px;font-weight:600;@if($tot_ar_150 <0) color:#ff0000; @endif" data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_150) !!}</td>
                        <td class="font600 text-right"  style="text-align:right;font-size:9px;font-weight:600;@if( $tot_ar_unbilled+$tot_ar_0_30+$tot_ar_31_60+$tot_ar_61_90+$tot_ar_91_120+$tot_ar_121_150+$tot_ar_150 <0) color:#ff0000; @endif" data-format="#,##0.00">{!! \App\Http\Helpers\Helpers::priceFormat( $tot_ar_unbilled+$tot_ar_0_30+$tot_ar_31_60+$tot_ar_61_90+$tot_ar_91_120+$tot_ar_121_150+$tot_ar_150) !!}</td>                                                
                    </tr>
                @endif
            </tbody>
        </table>*/ ?>
        <table class="">
            <tr>
                <td class="font600" colspan="16" style="font-size: 13px;font-weight:600;">Insurance Claims - Paid By Location</td>
            </tr>
        </table>
        @if(!empty($insuranceClaimsByFacility))
            @foreach($insuranceClaimsByFacility as $key => $value)
            <table class="">
                <tr>
                    <td colspan="16" class="font600" style="font-size:12px;font-weight:600;">{{$key}}</td>
                </tr>
            </table>
                <table class="">
                    <tr>
                        <th class="" style="font-size: 12px ;border-bottom:1px solid #000000 ;font-weight:600;">Payer</th>
                        <th class=" text-center" style="font-size: 12px ;border-bottom:1px solid #000000 ;font-weight:600;"># of Claims Billed</th>
                        <th class=" text-center" style="font-size: 12px ;border-bottom:1px solid #000000 ;font-weight:600;"># of Claims Paid</th>
                        <th class=" text-center" style="font-size: 12px ;border-bottom:1px solid #000000 ;font-weight:600;">Difference </th>                                            
                        <th class=" text-center" style="font-size: 12px ;border-bottom:1px solid #000000 ;font-weight:600;text-align:right;">Outstanding </th>                                           
                        <th class=" text-center" style="font-size: 12px ;border-bottom:1px solid #000000 ;font-weight:600;text-align:right;">%</th>
                    </tr>
                    <?php $billed = $total_paid = $ar = $tot_ar_percentage = 0;?>
					@foreach($value as $k => $v)
                        <?php
                            $total_paid += $v['paid'];
                            $billed += $v['claim_count'];
                            $ar += $v['total_ar'];
                            $total_ar_bal = (array_sum(array_column($value,'total_ar'))!=0)?array_sum(array_column($value,'total_ar')):1;
                            $tot_ar_percentage += (($v['total_ar'] / $total_ar_bal) * 100);
                        ?>
                        <tr>
                            <td style="font-size: 9px ;">{{$v['insurance_full_name']}}</td>
                            <td class="text-center" style="font-size: 9px ;text-align:center;">{{$v['claim_count']}}</td>
                            <td class="text-center" style="font-size: 9px ;text-align:center;">{{$v['paid']}}</td>
                            <td class="text-center" style="font-size: 9px ;text-align:center;">{{$v['claim_count']-$v['paid']}}</td>
                            <td class="text-right" style="font-size: 9px ;text-align:right;@if($v['total_ar'] <0) color:#ff0000; @endif">${!! \App\Http\Helpers\Helpers::priceFormat($v['total_ar']) !!}</td>
                            <td class="text-right" style="font-size: 9px ;text-align:right;@if(round(($v['total_ar']/$total_ar_bal)*100,2) <0) color:#ff0000; @endif">{{ round(($v['total_ar']/$total_ar_bal)*100,2)}}%</td>
                        </tr>                               
					@endforeach
                    <tr>
                        <td class="font600" style="font-size: 9px ;font-weight:600;">Totals</td>
                        <td class="font600 text-center" style="font-size: 9px ;font-weight:600;text-align:center;">{{$billed}}</td>
                        <td class="font600 text-center" style="font-size: 9px ;font-weight:600;text-align:center;"> {{$total_paid}} </td>
                        <td class="font600 text-center" style="font-size: 9px ;font-weight:600;text-align:center;">{{$billed-$total_paid}}  </td>
                        <td class="font600 text-right" style="font-size: 9px ;font-weight:600;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($ar) !!}  </td>
                        <td class="font600 text-right" style="font-size: 9px ;font-weight:600;text-align:right;">{{$tot_ar_percentage}}%</td>
                    </tr>
                </table>
            @endforeach
        @endif
        <table class="">
            <tr>
                <td class="font600" colspan="16" style="font-size: 13px;font-weight:600;">Location Status Summary</td>
            </tr>
        </table>
        <table class="">
            <tr>
                <th class=" text-center" style="font-size: 12px;text-align:center;font-weight:600;">Facility</th>
                <th class=" text-center" colspan="4" style="font-size: 12px;text-align:center;font-weight:600;">Totals</th>
                <th class=" text-center" colspan="2" style="font-size: 12px;text-align:center;font-weight:600;">Avg Collections per</th>
            </tr>
            <tr>
                <td class="" style="border-top:1px solid #000000;"></td>
                <td class="font600 text-center" style="text-align:center;font-size: 10px;border-top:1px solid #000000;font-weight:600;">Visits/Claims</td>
                <td class="font600 text-center" style="text-align:center;font-size: 10px;border-top:1px solid #000000;font-weight:600;">Days Worked</td>
                <td class="font600 text-center" style="text-align:center;font-size: 10px;border-top:1px solid #000000;font-weight:600;">Charges</td>
                <td class="font600 text-center" style="text-align:center;font-size: 10px;border-top:1px solid #000000;font-weight:600;">Payment</td>
                <td class="font600 text-center" style="text-align:center;font-size: 10px;border-top:1px solid #000000;font-weight:600;">Patient/Appt</td>
                <td class="font600 text-center" style="text-align:center;font-size: 10px;border-top:1px solid #000000;font-weight:600;">Day</td>
            </tr>
            <?php $status_tot_claims = $tot_days_worked = $total_charges = $tot_payments = $tot_avg_pmt = $tot_avg_pmt_per_day = 0;?>
            @if(!empty($facilityStatus))
                @foreach($facilityStatus as $key => $value)
                <?php
                    if($value['Sunday'] !=0)
                        unset($resultDays['Sunday']);
                    if($value['Monday'] !=0)
                        unset($resultDays['Monday']);
                    if($value['Tuesday'] !=0)
                        unset($resultDays['Tuesday']);
                    if($value['Wednesday'] !=0)
                        unset($resultDays['Wednesday']);
                    if($value['Thursday'] !=0)
                        unset($resultDays['Thursday']);
                    if($value['Friday'] !=0)
                        unset($resultDays['Friday']);
                    if($value['Saturday'] !=0)
                        unset($resultDays['Saturday']);
                    $tot_days_worked += array_sum($resultDays);
                    $status_tot_claims += $value['claim_count'];
                    $total_charges += $value['total_charge'];
                    $tot_payments += $value['payments'];
                    $patient_appt = ($value['claim_count']!=0)?($value['payments']/$value['claim_count']):$value['payments'];
                    $per_day = ($days!=0)?$value['payments']/$days:$value['payments'];
                    $tot_avg_pmt += $patient_appt;
                    $tot_avg_pmt_per_day += $per_day;
                ?>
                <tr>
                    <td style="font-size: 9px;">{{$value['facility_name']}}</td>
                    <td class="text-center" style="font-size: 9px;text-align:center;">{{$value['claim_count']}}</td>
                    <td class="text-center" style="font-size: 9px;text-align:center;">{{array_sum($resultDays)}}</td>
                    <td class="text-right" style="font-size: 9px;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($value['total_charge']) !!}</td>
                    <td class="text-right" style="font-size: 9px;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($value['payments']) !!}</td>
                    <td class="text-right" style="font-size: 9px;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($patient_appt) !!}</td> 
                    <td class="text-right" style="font-size: 9px;text-align:right;">${!! ($per_day!=0)?round($per_day,2):'0.00' !!}</td> 
                </tr>
                @endforeach                                    
                <tr>
                    <td class="font600" style="font-size: 9px;font-weight:600;">Totals</td>
                    <td class="text-center font600" style="font-size: 9px;font-weight:600;text-align:center;">{{$status_tot_claims}}</td>
                    <td class="text-center font600" style="font-size: 9px;font-weight:600;text-align:center;">{{$tot_days_worked}}</td>
                    <td class="text-right font600" style="font-size: 9px;font-weight:600;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($total_charges) !!}</td>
                    <td class="text-right font600" style="font-size: 9px;font-weight:600;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($tot_payments) !!}</td>
                    <td class="text-right font600" style="font-size: 9px;font-weight:600;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($tot_avg_pmt) !!}</td> 
                    <td class="text-right font600"style="font-size: 9px;font-weight:600;text-align:right;">${!! ($tot_avg_pmt_per_day!=0)?round($tot_avg_pmt_per_day,2):'0.00' !!}</td> 
                </tr>
            @endif
        </table>
        <td colspan="16">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>