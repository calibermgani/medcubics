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
            .text-center{text-align: center;}
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
            $payers = $result['payers'];
            $tot_units = $result['tot_units'];
            $tot_charges = $result['tot_charges'];
            $charges = $result['charges'];
            $adjustments = $result['adjustments'];
            $total_adj = $result['total_adj'];
            $insurance = $result['insurance'];
            $total_pmt = $result['total_pmt'];
            $insurance_bal = $result['insurance_bal'];
            $unit_details = $result['unit_details'];
            $insurance_total = $result['insurance_total'];
            $search_by = $result['search_by'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Payer Summary</i></h3></td>
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
                    <th colspan="2" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="2" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>              
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if(isset($payers) && !empty($payers)) 
            <div>  
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th class="text-left">Ins Name</th>                              
                            <th class="text-left">Ins Type</th>
                            <th class="text-left">Units</th>
                            <th class="text-right">Charges($)</th>
                            <th class="text-right">Adjustments($)</th>
                            <th class="text-right">Payments($)</th>   
                            <th class="text-right">Ins Balance($)</th>                    
                        </tr>                       
                    </thead>   
                    <tbody>
                        @foreach($payers as  $list)
                        <?php
							$insurance_name = $list->insurance_name;
                            $insurance_id = $list->insurance_id;
							$insurance_category = @$list->insurance_category;
							$units = isset($unit_details->$insurance_id) ? $unit_details->$insurance_id : 0;
							$total_charge = isset($charges->$insurance_id) ? $charges->$insurance_id : 0;
							$adjustment = isset($adjustments->$insurance_id) ? $adjustments->$insurance_id : 0;
							$pmt = isset($insurance->$insurance_id) ? $insurance->$insurance_id : 0;
							$ins_bal = isset($insurance_bal->$insurance_id) ? $insurance_bal->$insurance_id : 0;
                        ?>
                        <tr>                        
                            <td class="text-left">{{$insurance_name}}</td>           
                            <td class="text-left">{{ $insurance_category }}</td>
                            <td class="text-left">{!! $units!!}</td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat($total_charge) !!}</td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat($adjustment) !!} </td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat($pmt) !!}</td>
                            <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat($ins_bal) !!}</td>                                   
                        </tr>
                        @endforeach                       
                    </tbody>   
                </table>        
            </div>
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif
        </div>
    </body>
</html>