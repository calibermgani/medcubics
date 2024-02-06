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
            .text-right{text-align: right !important;padding-right:5px;}
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
            $cpts = $result['cpts'];
            $patient = $result['patient'];
            $cpt_summary = $result['cpt_summary'];
            $summary_det = $result['summary_det'];
            $cptDesc = $result['cptDesc'];
            $search_by = $result['search_by'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>CPT/HCPCS Summary</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
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
                    <th colspan="4" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>              
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if(!empty($cpts))
            <div> 
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th>CPT Code</th>
                            <th>Description</th>
                            <th>Units</th>
                            <th class="text-center">Charges($)</th>
                            <th class="text-center">Pat Paid($)</th>
                            <th class="text-center">Ins Paid($)</th>
                            <th class="text-center">Pat Adj($)</th>
                            <th class="text-center">Ins Adj($)</th>
                            <th class="text-center">AR Due($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cpts as $list)
                        <?php							
                            $cpt_code = @$list->cpt_code;
                            $total_charge = isset($list->total_charge) ? $list->total_charge : 0;
                            $desc = isset($cptDesc->$cpt_code) ? $cptDesc->$cpt_code : '';
                            $patient_adj = isset($list->pat_adj)?$list->pat_adj:0;
                            $insurance_adj = isset($list->ins_adj)?$list->ins_adj:0;
                            $adjustment = isset($list->tot_adj)?$list->tot_adj:0;
                            $pat_pmt = isset($patient->$cpt_code)?$patient->$cpt_code:0;
                            $ins_pmt = isset($insurance->$cpt_code)?$insurance->$cpt_code:0;
                            $pat_bal = isset($list->patient_bal)?$list->patient_bal:0;
                            $ins_bal = isset($list->insurance_bal)?$list->insurance_bal:0;
                        ?>
                        <tr>
                            <td style="text-align: left;">{{ @$cpt_code }}</td>
                            <td style="text-align: left;">{{ @$desc }}</td>
                            <td>{{ @$list->unit }}</td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat($total_charge) !!}</td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat(@$list->patient_paid) !!}</td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat(@$list->insurance_paid) !!}</td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat($patient_adj) !!} </td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat($insurance_adj) !!} </td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat(@$list->total_ar_due) !!}</td>
                        </tr>
                        @endforeach 
                    </tbody>
                </table>							
            </div>
            <div style="page-break-inside: avoid;">
                <h3 class="med-orange" style="margin-left:10px">Summary</h3>
                <table class="table-summary" style="width: 40%; border:1px solid #ccc; margin-left: 10px;margin-top:10px;">
                    <thead>
                        <tr>
                            <th class="font600">Title</th>
                            <th class="text-right font600">Value($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $wallet = isset($patient->wallet) ? $patient->wallet : 0;
                            if ($wallet < 0)
                                $wallet = 0;
                        ?>
                        <tr>
                            <td>Wallet Balance</td>
                            <td class='text-right'>${{App\Http\Helpers\Helpers::priceFormat($wallet)}}</td>
                        </tr>
                        <tr>
                            <td>Total Units</td>
                            <td class='text-right'>{{ $summary_det['units'] }}</td>						
                        </tr>
                        <tr>
                            <td>Total Charges</td>
                            <td class='text-right'>${!! App\Http\Helpers\Helpers::priceFormat($summary_det['charges']) !!}</td>
                        </tr>
                        <tr>
                            <td>Total Adjustments</td>
                            <td class='text-right'>${!! App\Http\Helpers\Helpers::priceFormat($summary_det['adj']) !!}</td>
                        </tr>                                       
                        <tr>
                            <td>Total Payments</td>
                            <td class='text-right'>${!! App\Http\Helpers\Helpers::priceFormat($summary_det['pmt']) !!}</td>
                        </tr>
                        <tr>
                            <td>Total Balance</td>
                            <td class='text-right'>{!! App\Http\Helpers\Helpers::priceFormat($summary_det['bal'],'yes') !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </body>
</html>