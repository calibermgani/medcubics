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
                text-align:right !important;
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
            .text-left{text-align: left !important;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
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
        <div class="header">
            <?php 
				@$providers = $result['providers'];
				@$createdBy = $result['createdBy'];
				@$practice_id = $result['practice_id'];
				@$searchBy = $result['searchBy'];
				@$heading_name = App\Models\Practice::getPracticeName($practice_id);
			?>
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center" >{{$heading_name}} - <i>Provider Summary by Location</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"> </td>
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
                    <th colspan="3" style="border:none;text-align: left !important"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important"><span>User :</span> <span class="med-orange">@if(isset($createdBy)){{ $createdBy }}@endif</span></th>
                </tr>
            </table>
        </div>
        
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;float:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">         
            @if(!empty($providers)) 
                @foreach($providers as $key => $value) 
                    <table>
                        <tr>
                            <td colspan="6" class="font600 med-green"><h3>Rendering : {{explode('_',$key)[0]}}</h3></td>
                        </tr>
                    </table>
                    <table style="width: 98%">
                        <thead>
                        <tr style="border-bottom: 2px solid #00877f;">
                            <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">By Location</th>
                            <th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Charges</th>
                            <th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Payments</th>
                            <th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Adjustments </th>                                            
                            <th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Outstanding</th>                                           
                            <?php /*<th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Expected Payment</th>*/ ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $tot_charges = $tot_payments = $tot_adjustments = $tot_ar = $tot_expected = 0; ?>  
                        @foreach($value as $k=>$v)
                        <?php
                            $tot_charges += $v['total_charge'];
                            $tot_payments += $v['payments'];
                            $tot_adjustments += $v['adjustment'];
                            $tot_ar += $v['total_ar'];
                            $tot_expected += $v['expected'];
                        ?>
                        <tr>
                            <td class="" style="line-height: 24px">{{$v['facility_name']}}</td>
                            <td class="text-right">${{\App\Http\Helpers\Helpers::priceFormat($v['total_charge'])}}</td>
                            <td class="text-right"> ${!! \App\Http\Helpers\Helpers::priceFormat($v['payments']) !!}</td>
                            <td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($v['adjustment']) !!}</td>
                            <td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($v['total_ar']) !!}</td>
                            <?php /*<td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($v['expected']) !!}</td>*/ ?>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="font600">Totals</td>
                            <td class="font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_charges) !!}</td>
                            <td class="font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_payments) !!}</td>
                            <td class="font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_adjustments) !!}</td>
                            <td class="font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_ar) !!}</td>
                            <?php /*<td class="font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_expected) !!}</td>*/ ?>
                        </tr>
                        </tbody>
                    </table>
                @endforeach
            @endif
        </div>
    </body>
</html>