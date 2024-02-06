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
                padding-top:30px;
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
            $data = $result['result'];
            $search_by  = $result['search_by']; 
            //$practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName();
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>End of the Day Totals</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 0; ?>
                            @foreach($search_by as $key=>$val)
                            @if($i > 0){{' | '}}@endif
                            <span>{!! $key !!} : </span>{{ @$val }}                           
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
            @if(!empty($data))
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="border-left: 1px solid #ccc;border-right: 1px solid #ccc;">Date-Day</th>
                            <th rowspan="2" style="border-right: 1px solid #ccc;">Charges($)</th>
                            <th rowspan="2" style="border-right: 1px solid #ccc;">Claims</th>
                            <th rowspan="2" style="border-right: 1px solid #ccc;">Write-off($)</th>
                            <th colspan="2" class="text-center" style="border-right: 1px solid #ccc;">Adjustments($)</th>
                            <th colspan="2" class="text-center" style="border-right: 1px solid #ccc;">Refund($)</th>
                            <th colspan="2" class="text-center" style="border-right: 1px solid #ccc;">Payments($)</th>
                            <th rowspan="2" style="border-right: 1px solid #ccc;">Total Payments($)</th>
                        </tr>
                        <tr>                            
                            <th class="text-center" style="border-right: 1px solid #ccc;">Insurance</th>
                            <th class="text-center" style="border-right: 1px solid #ccc;">Patient</th>
                            <th class="text-center" style="border-right: 1px solid #ccc;">Insurance</th>
                            <th class="text-center" style="border-right: 1px solid #ccc;">Patient</th>
                            <th class="text-center" style="border-right: 1px solid #ccc;">Insurance</th>
                            <th class="text-center" style="border-right: 1px solid #ccc;">Patient</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($data))
                        <?php 
                        $total_adj = 0;
                        $patient_total = 0;
                        $insurance_total = 0;

                     ?>                   
                        @foreach($data as  $key=>$dates)
                        <?php
                        $insurance_payment[] = isset($dates->insurance_payment) ? $dates->insurance_payment : 0;
                        $writeoff_total[] = isset($dates->writeoff_total) ? $dates->writeoff_total : 0;
                        $patient_payment[] = isset($dates->patient_payment) ? $dates->patient_payment : 0;
                        $patient_adjustment[] = isset($dates->patient_adjustment) ? $dates->patient_adjustment : 0;
                        $insurance_adjustment[] = isset($dates->insurance_adjustment) ? $dates->insurance_adjustment : 0
                        ?>
                        <tr>						
                            <td style="padding-left:5px;">{{$key.'-'.date('D', strtotime($key))}}</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->total_charge, 'no') !!} </td>
                            <td class="text-right" data-format="0.00">@if(@$dates->claims_count != ''){!! @$dates->claims_count !!} @else 0 @endif </td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->writeoff_total) !!} </td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->insurance_adjustment,'no')  !!}  </td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->patient_adjustment,'no') !!}  </td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->insurance_refund,'no') !!}</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->patient_refund,'no') !!}</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->insurance_payment,'no') !!} </td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->patient_payment,'no') !!} </td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->total_payment,'no') !!} </td> 	
                        </tr>
                        @endforeach
                        @endif
                    </tbody>   
                </table>		
            </div>
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif
        </div>
    </body>
</html>