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
                text-align:left;
                font-size:13px;
                font-weight: 100 !important;
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 12px !important;}
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .med-red {
                color: red !important;
            }
            .text-right{text-align: right;}
            .text-left{text-align: left;}
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
        </style>
    </head>
    <body>
        <?php 
            $adjustment = $result['adjustment'];
            $instype = $result['instype'];
            $tot_adjs = $result['tot_adjs'];
            $search_by = $result['search_by'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Adjustment Analysis - Detailed</i></h3></td>
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
            <table class="new-border" style="width:97%; margin-left: 5px; margin-top: -5px;margin-bottom: -5px;">
                <tr style="font-weight:600;">
                    <th colspan="3" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;float:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-left:15px;padding-top:0px;">
            @if(!empty(@$adjustment))
                @foreach(@$adjustment as $adjustment)
                    <?php 
                        $patient_name =     @$adjustment->title.' '.App\Http\Helpers\Helpers::getNameformat(@$adjustment->last_name,@$adjustment->first_name,@$adjustment->middle_name);
                    ?>
                    @if(!empty($adjustment->cpt))
                    <div class="box-border" style="page-break-after: auto; page-break-inside: avoid;width:95%;padding-top:0px; overflow: hidden;border-radius:0px;">
                        <table style="width: 100%">
                            <tbody>
                                <tr>
                                    <td class="med-green" style="border:0px solid red !important;">Claim No: </td>
                                    <td class="med-orange">{{$adjustment->claim_number}}</td>
                                    <td>Patient Name</td>
                                    <td>{{ $patient_name }}</td>
                                </tr>
                                <tr>
                                    <td class="med-green">Acc No</td>
                                    <td>{{ $adjustment->account_no }}</td>
                                    <td class="med-green">Responsibility</td>
                                    <td>
                                        @if($adjustment->self_pay =='Yes')
                                            Patient
                                        @else
                                        <?php $ins = App\Http\Helpers\Helpers::getInsuranceName(@$adjustment->insurance_id); ?>
                                        {{$ins}}
                                        @endif
                                    </td>
                                    <td class="med-green">Billing </td>
                                    <td>{{ $adjustment->billing_name  }}</td>
                                </tr>
                                <tr>
                                    <td class="med-green">Rendering</td>
                                    <td>{{ $adjustment->rendering_name}}</td>
                                    <td class="med-green">Facility</td>
                                    <td>{{str_limit($adjustment->facility_name)}}</td>
                                    <td class="med-green">Tot Adj($)</td>
                                    <td>{!! App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten(json_decode(json_encode($adjustment->tot_adj), true)))) !!}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="width:100%;border: none !important;border-spacing: 0px; margin-top:-15px;">
                            <thead>
                                <tr style="background: #f1fcfb; color: #00877f;">
                                <th style="background: #d9f3f0; color: #00877f;">DOS </th>                                       
                                <th style="background: #d9f3f0; color: #00877f;">CPT</th>
                                <th style="background: #d9f3f0; color: #00877f;">Payer</th>
                                <th style="background: #d9f3f0; color: #00877f;">Adj Date</th>
                                <th style="background: #d9f3f0; color: #00877f;">Adj Reason</th>
                                <th style="background: #d9f3f0; color: #00877f;">CPT Adj($)</th>
                                <th style="background: #d9f3f0; color: #00877f;">Reference</th>
                                <th style="background: #d9f3f0; color: #00877f;">User</th>
                                </tr>
                            </thead>
                            @foreach($adjustment->cpt as $cpt)
                                <tbody>
                                    <tr>                              
                                        <td valign="top">{{ App\Http\Helpers\Helpers::dateFormat($cpt->dos_from,'dob') }} </td>
                                        <td valign="top">{{ @$cpt->cpt_code }}</td>
                                        <td>
                                            <table> 
                                                <tbody>
                                                    @if(!empty($cpt->payer))
                                                        @foreach(array_flatten(json_decode(json_encode($cpt->payer),true))  as $key=>$adj)
                                                            <tr><td>{{$adj}}</td></tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <table> 
                                                <tbody>
                                                    @if(!empty($cpt->adj_date))
                                                        @foreach(array_flatten(json_decode(json_encode($cpt->adj_date),true)) as $key=>$adj)
                                                            <tr><td>{{$adj}}</td></tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <table> 
                                                <tbody>
                                                    @if(!empty($cpt->adj_reason))
                                                        @foreach(array_flatten(json_decode(json_encode($cpt->adj_reason),true)) as $key=>$adj)
                                                            <tr><td>{{$adj}}</td></tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </td>
                                        <td> 
                                            <table>
                                                <tbody>
                                                    @if(!empty($cpt->adj_amt))
                                                        @foreach(array_flatten(json_decode(json_encode($cpt->adj_amt),true)) as $key=>$adj)
                                                            <tr><td class="@if($adj<0) med-red @endif">{{$adj}}</td></tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <table> 
                                                <tbody>
                                                    @if(!empty($cpt->reference))
                                                        @foreach(array_flatten(json_decode(json_encode($cpt->reference),true)) as $key=>$adj)
                                                            <tr><td>{{$adj}}</td></tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </td>
                                        <td valign="top">{{App\Http\Helpers\Helpers::user_names($adjustment->created_by)}}</td>
                                    </tr>
                                </tbody>
                            @endforeach
                        </table>
                        </div>
                    @endif
                    @endforeach 
                    <div class="summary-table" style="page-break-after: auto; page-break-inside: avoid;">
                        <h4 class="med-orange" style="margin-bottom: 0px;">Summary</h4>
                        <table style="width:45%;;border:1px solid #ccc;font-size:11px !important">
                            <tbody>
                                @if(@$instype == "all")
                                <tr> 
                                    <td class="">Total Insurance Adjustments</td>                                            
                                    <td class='font600 text-right'>                                   
                                        ${!!App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Insurance))) !!}</td>
                                </tr>
                                <tr> 
                                    <td class="">Total Patient Adjustments</td>                                            
                                    <td class='font600 text-right'>${!!App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Patient)))!!}</td>
                                </tr>
                                <tr> 
                                    <td class="font600">Total Adjustments</td> 
                                    <td class='font600 text-right' >${!! App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Insurance))+array_sum(array_flatten((array)@$tot_adjs->Patient))) !!}</td>
                                </tr>
                                @endif
                                @if(@$instype == "insurance")
                                <tr> 
                                    <td class="">Total Insurance Adjustments</td>                                            
                                    <td class='font600 text-right'>${!!App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Insurance))) !!}</td>
                                </tr>
                                @endif
                                @if(@$instype == "self")
                                <tr> 
                                    <td class="">Total Patient Adjustments</td>                                            
                                    <td class='font600 text-right'>${!!App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Patient)))!!}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>   
                    </div>
            @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
            @endif	
        </div>
    </body>
</html>