<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Provider Summary by Location</title>
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
			@$providers = $result['providers'];
			@$createdBy = $result['createdBy'];
			@$practice_id = $result['practice_id'];
			@$searchBy = $result['searchBy'];
			@$heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="5" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:center;">Provider Summary by Location</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:center;">
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
        </table>
        @if(!empty($providers)) 
            @foreach($providers as $key => $value) 
                <table>
                    <tr>
                        <td colspan="5" class="font600" style="font-weight:600;"><h3 style="font-weight:600;">Rendering : {{explode('_',$key)[0]}}</h3></td>
                    </tr>
                </table>
                <table>
                    <thead>
                        <tr style="font-weight:600;">
                            <th style="text-align: left;font-weight:600;border-bottom:1px solid black;">By Location</th>
                            <th style="text-align: right;font-weight:600;border-bottom:1px solid black;">Total Charges</th>
                            <th style="text-align: right;font-weight:600;border-bottom:1px solid black;">Total Payments</th>
                            <th style="text-align: right;font-weight:600;border-bottom:1px solid black;">Total Adjustments </th>
                            <th style="text-align: right;font-weight:600;border-bottom:1px solid black;">Total Outstanding</th>
                            <?php /*<th class="text-right font600" style="">Expected Payment</th>*/ ?>
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
                            <td style="line-height: 24px;font-size:9px;">{{$v['facility_name']}}</td>
                            <td style="font-size:9px;text-align:right;">${{\App\Http\Helpers\Helpers::priceFormat($v['total_charge'])}}</td>
                            <td style="font-size:9px;text-align:right;"> ${!! \App\Http\Helpers\Helpers::priceFormat($v['payments']) !!}</td>
                            <td style="font-size:9px;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($v['adjustment']) !!}</td>
                            <td style="font-size:9px;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($v['total_ar']) !!}</td>
                            <?php /*<td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($v['expected']) !!}</td>*/ ?>
                        </tr>
                        @endforeach
                        <tr>
                            <td style="font-weight:600;font-size:9px;">Totals</td>
                            <td style="font-weight:600;font-size:9px;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($tot_charges) !!}</td>
                            <td style="font-weight:600;font-size:9px;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($tot_payments) !!}</td>
                            <td style="font-weight:600;font-size:9px;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($tot_adjustments) !!}</td>
                            <td style="font-weight:600;font-size:9px;text-align:right;">${!! \App\Http\Helpers\Helpers::priceFormat($tot_ar) !!}</td>
                            <?php /*<td class="font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_expected) !!}</td>*/ ?>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endif
        <td colspan="5">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>