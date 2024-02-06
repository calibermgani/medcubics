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
            $charges_list = $result['charges_list'];
            $total_arr = $result['total_arr'];
            $search_by = $result['search_by'];
            $practice_id = $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{@$heading_name}} - <i>Charge Category Report</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 0; ?>
                            @foreach($search_by as $key=>$val)
                            @if($i > 0){{' | '}}@endif
                            <span>{!! $key !!} : </span>{{ @$val[0] }}                           
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
        <div style="padding-top:10px;">
           
            <div> 
                @if(!empty($charges_list))  
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th>CPT/HCPCS Category</th>
                            <th>CPT/HCPCS</th>
                            <th>Description</th>
                            <th>Rendering</th>
                            <th>Units</th>                                                                            
                            <th>Charge Amt($)</th>
                            <th>Payments($)</th>
                            <th>Work RVU($)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
						$temp = 0;
						$inc = 0;
						$total_arr = json_decode(json_encode($total_arr), true);
                    ?>
                    @foreach($charges_list as  $result)
                    <?php
						$inc++;                        
						$provider_id = $result->provider_id;
						$provider_name = 'Rendering Provider - '.App\Models\Provider::getProviderFullName(@$provider_id);
                    ?>
                    <?php if ($temp != $provider_id) { ?>
                        <tr>
                            <td>{{$provider_name}}</td> 
                        </tr>
                    <?php } ?>
                    <tr>
                       
                     
                         <td >{!! @$result->procedure_category !!}</td> 
                         <td >{{ @$result->cpt_code}}</td> 
                         <td >{{ @$result->description}}</td> 
                         <td >{{ @$result->provider_short_name}}</td> 
                         <td >{!! @$result->units !!}</td>                          
                         <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge)!!}</td> 
                         <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->payment)!!}</td> 
                         <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->work_rvu)!!}</td>               
                   </tr>
            <?php if ($inc == @$total_arr[$provider_id]['last_rec'] + 1) { ?>     
                <tr>
                      <td class="font600">Totals</td>
                      <td class="font600"></td>
                      <td class="font600"></td>
                      <td class="font600"></td>
                      <td class="text-left font600">{!! @$total_arr[$provider_id]['units'] !!}</td>
                      <td class="text-right font600" data-format='0.00'>${!! $total_arr[$provider_id]['charge'] !!}</td>
                      <td class="text-right font600" data-format='0.00'>${!! $total_arr[$provider_id]['payment'] !!}</td>
                      <td class="text-right font600" data-format='0.00'>${!! $total_arr[$provider_id]['work_rvu'] !!}</td>                    
                  </tr>
            <?php } ?>
            <?php $temp = $provider_id; ?>
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