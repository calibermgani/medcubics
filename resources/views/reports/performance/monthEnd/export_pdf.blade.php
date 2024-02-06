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
                text-align:left !important;
                font-size:10px !important;
                font-weight: 600 !important;
                border-bottom: 1px solid #ccc;
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 13px !important;}
            .table-summary tbody tr{line-height: 22px !important;} 
            .table-summary tbody tr td{font-size:11px !important;} 
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right !important;}
            .text-left{text-align: left !important;}
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
        <div class="header">            
			<?php 
				@$FacilityWiseOutstanding = $result['FacilityWiseOutstanding'];
				@$insuranceClaimsByFacility = $result['insuranceClaimsByFacility'];
				@$facilityStatus = $result['facilityStatus'];
				@$days = $result['days'];
				@$resultDays = $result['resultDays'];
				@$createdBy = $result['createdBy'];
				@$practice_id = $result['practice_id'];
				@$searchBy = $result['searchBy'];
				@$heading_name = App\Models\Practice::getPracticeName($practice_id); 
			?>
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center" >{{$heading_name}} - <i>Month End Performance Summary Report</i></h3> </td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 1; ?>
                            @if(isset($searchBy) && !empty($searchBy))
                            @foreach($searchBy as $header_name => $header_val)
                            <span>
                                {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i< count((array)$searchBy)) | @endif 
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important"><span>User :</span> <span class="med-orange">@if(isset($createdBy)){{ $createdBy }}@endif</span></th>
                </tr>
            </table>
        </div>
        
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;float:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px; width: 98%">         
            <div>   
                <table class="popup-table-border  table-separate table m-b-m-1"><tr><td colspan="16" class="med-orange font600" style="font-size: 16px;"> Outstanding AR - By Location</td></tr></table>
                <table class="popup-table-border  table-separate table m-b-m-1" style="width: 98%">
                    <thead>
                        <tr>
                            <th class="text-center" style="border-right: 1px solid #fff ">Facility</th>
                            <th class="text-center" colspan="2" style="border-right: 1px solid #fff">Unbilled</th>
                            <th class="text-center" colspan="2" style="border-right: 1px solid #fff">0-30</th>
                            <th class="text-center" colspan="2" style="border-right: 1px solid #fff">31-60</th>
                            <th class="text-center" colspan="2" style="border-right: 1px solid #fff">61-90</th>
                            <th class="text-center" colspan="2" style="border-right: 1px solid #fff">91-120</th>
                            <th class="text-center" colspan="2" style="border-right: 1px solid #fff">121-150</th>
                            <th class="text-center" colspan="2" style="border-right: 1px solid #fff">>150</th>
                            <th class="text-center" style=" ">Totals</th>
                        </tr>
                        <tr>
                            <td class="font600 bg-white line-height-26 text-center" style="border-right: 1px solid #CDF7FC"><span class="med-green"></span></td>
                            <td class="font600 text-center  line-height-26" style="border-right: 1px solid #a4ede9;background: #dbfaf8;">
                                <span class="med-green"> Claims</span>
                            </td>
                            <td class="font600 text-center line-height-26" style="border-right: 1px solid #a4ede9;background: #dbfaf8;">
                                <span class="med-green"> Value($)</span>
                            </td>
                            <td class="font600 text-center  line-height-26" style="border-right: 1px solid #a4ede9;background: #dbfaf8;">
                                <span class="med-green"> Claims</span>
                            </td>
                            <td class="font600 text-center line-height-26" style="border-right: 1px solid #a4ede9;background: #dbfaf8;">
                                <span class="med-green"> Value($)</span>
                            </td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
                            <td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
                            <td class="font600 bg-white text-center"><span class="med-green"> </span></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $claim_counts_unbilled = $claim_counts_0_30 = $claim_counts_31_60 = $claim_counts_61_90 = $claim_counts_91_120 = $claim_counts_121_150 = $claim_counts_150 = $tot_ar_unbilled = $tot_ar_0_30 = $tot_ar_31_60 = $tot_ar_61_90 = $tot_ar_91_120 = $tot_ar_121_150 = $tot_ar_150  = 0;
                        ?>
                        @if(!empty($FacilityWiseOutstanding))
                            @foreach($FacilityWiseOutstanding as $key=>$value)
                            <tr>
                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC">{{$key}}</td>
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
                                <td class="text-center bg-white">{{$claim_count_unbilled}}</td>
                                <td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_unbilled) !!}</td>
                                <td class="text-center bg-white">{{$claim_count_0_30}}</td>
                                <td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_0_30) !!}</td>
                                <td class="text-center bg-white">{{$claim_count_31_60}}</td>
                                <td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_31_60) !!}</td>
                                <td class="text-center bg-white">{{$claim_count_61_90}}</td>
                                <td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_61_90) !!}</td>
                                <td class="text-center bg-white">{{$claim_count_91_120}}</td>
                                <td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_91_120) !!}</td>
                                <td class="text-center bg-white">{{$claim_count_121_150}}</td>
                                <td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_121_150) !!}</td>
                                <td class="text-center bg-white">{{$claim_count_150}}</td>
                                <td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_150) !!}</td>
                                <td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!!  \App\Http\Helpers\Helpers::priceFormat($total_ar_unbilled+$total_ar_0_30+$total_ar_31_60+$total_ar_61_90+$total_ar_91_120+$total_ar_121_150+$total_ar_150) !!}</td>
                            </tr>
                            @endforeach       
                            <tr>
                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC">Totals</td>
                                <td class="font600 text-center bg-white">{{$claim_counts_unbilled}}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_unbilled) !!}</td>
                                <td class="font600 text-center bg-white">{{$claim_counts_0_30}}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_0_30) !!}</td>
                                <td class="font600 text-center bg-white">{{$claim_counts_31_60}}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_31_60) !!}</td>
                                <td class="font600 text-center bg-white">{{$claim_counts_61_90}}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_61_90) !!}</td>
                                <td class="font600 text-center bg-white">{{$claim_counts_91_120}}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_91_120) !!}</td>
                                <td class="font600 text-center bg-white">{{$claim_counts_121_150}}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_121_150) !!}</td>
                                <td class="font600 text-center bg-white">{{$claim_counts_150}}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_150) !!}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat( $tot_ar_unbilled+$tot_ar_0_30+$tot_ar_31_60+$tot_ar_61_90+$tot_ar_91_120+$tot_ar_121_150+$tot_ar_150) !!}</td>                                                
                            </tr>
                        @endif
                    </tbody>
                </table>
                <table class="popup-table-border  table-separate table m-b-m-1">
                    <tr>
                        <td class="med-orange font600" colspan="16" style="font-size: 16px;">Insurance Claims - Paid By Location</td>
                    </tr>
                </table>
                @if(!empty($insuranceClaimsByFacility))
                    @foreach($insuranceClaimsByFacility as $key => $value)
                    <table class="popup-table-border  table-separate table m-b-m-1">
                        <tr>
                            <td class="med-green" colspan="16" style="font-size: 14px;">{{$key}}</td>
                        </tr>
                    </table>
                        <table class="popup-table-border  table-separate table m-b-m-1" style="width: 98%">
                            <tr>
                                <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">Payer</th>
                                <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims Billed</th>
                                <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims Paid</th>
                                <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Difference </th>                                            
                                <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Outstanding </th>                                           
                                <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">%</th>
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
                                    <td class="" style="line-height: 24px;">{{$v['insurance_name']}}</td>
                                    <td class="text-center">{{$v['claim_count']}}</td>
                                    <td class="text-center">{{$v['paid']}}</td>
                                    <td class="text-center">{{$v['claim_count']-$v['paid']}}</td>
                                    <td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($v['total_ar']) !!}</td>
                                    <td class="text-right">{{ round(($v['total_ar']/$total_ar_bal)*100,2)}}%</td>
                                </tr>                               
							@endforeach
                            <tr>
                                <td class="font600">Totals</td>
                                <td class="font600 text-center">{{$billed}}</td>
                                <td class="font600 text-center"> {{$total_paid}} </td>
                                <td class="font600 text-center">{{$billed-$total_paid}}  </td>
                                <td class="font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($ar) !!}  </td>
                                <td class="font600 med-orange text-right">{{$tot_ar_percentage}}%</td> 
                            </tr>
                        </table>
                    @endforeach
                @endif
                <table class="popup-table-border  table-separate table m-b-m-1">
                    <tr>
                        <td class="med-orange font600" colspan="16" style="font-size: 16px;">Location Status Summary</td>
                    </tr>
                </table>
                <table class="popup-table-border  table-separate table m-b-m-1 margin-t-10" style="width: 98%">
                    <tr>
                        <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Facility</th>
                        <th class="font600 text-center" colspan="4" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;border-right: 1px solid #a4ede9;border-left: 1px solid #a4ede9">Totals</th>
                        <th class="font600 text-center" colspan="2" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Avg Collections per</th>                                        
                    </tr>
                    <tr>
                        <td class=""></td>
                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Visits/Claims</span></td>
                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Days Worked</span></td>
                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Charges</span></td>
                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Payment</span></td>
                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Patient/Appt</span></td>
                        <td class="font600 text-center  line-height-26"style="background: #dbfaf8;"><span class="med-green"> Day</span></td>
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
                            <td class="" style="line-height: 24px">{{$value['facility_name']}}</td>
                            <td class="text-center">{{$value['claim_count']}}</td>
                            <td class="text-center">{{array_sum($resultDays)}}</td>
                            <td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($value['total_charge']) !!}</td>
                            <td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($value['payments']) !!}</td>
                            <td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($patient_appt) !!}</td> 
                            <td class="text-right">${!! ($per_day!=0)?round($per_day,2):'0.00' !!}</td> 
                        </tr>
                        @endforeach                                    
                        <tr>
                            <td class="font600">Totals</td>
                            <td class="text-center font600">{{$status_tot_claims}}</td>
                            <td class="text-center font600">{{$tot_days_worked}}</td>
                            <td class="text-right font600">${!! \App\Http\Helpers\Helpers::priceFormat($total_charges) !!}</td>
                            <td class="text-right font600">${!! \App\Http\Helpers\Helpers::priceFormat($tot_payments) !!}</td>
                            <td class="text-right font600">${!! \App\Http\Helpers\Helpers::priceFormat($tot_avg_pmt) !!}</td> 
                            <td class="text-right font600">${!! ($tot_avg_pmt_per_day!=0)?round($tot_avg_pmt_per_day,2):'0.00' !!}</td> 
                        </tr>
                    @endif
                </table>
            </div>      
        </div>
    </body>
</html>