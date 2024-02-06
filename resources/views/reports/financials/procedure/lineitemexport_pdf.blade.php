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
            .text-right{text-align: right !important; padding-right:5px;}
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
            $cptreport_list = $result['cptreport_list'];
            $search_by = $result['search_by'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
            foreach ($cptreport_list as $key => $value) {
                $abb_rendering[] = @$value->claim_details->rend_providers->short_name." - ".@$value->claim_details->rend_providers->provider_name;        
            }
            $abb_rendering = array_unique($abb_rendering);
            foreach (array_keys($abb_rendering, ' - ') as $key) {
                unset($abb_rendering[$key]);        
            }
            $rendering_imp = implode(':', $abb_rendering);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Procedure Collection Report - Insurance Only</i></h3></td>
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
                    <th colspan="5" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y',$practice_id) }}</span></th>
                    <th colspan="5" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="">
            @if(!empty($cptreport_list))
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="border-left: 1px solid #ccc;padding-left: 5px;">CPT</th>
                            <th>DOS</th>
                            <th>Acc No</th>
                            <th>Claim No</th>
                            <th style="width:80px;">Patient Name</th>
                            <th style="width:60px;padding-left: 5px;">Rendering</th>
                            <th>Ins Type</th>
                            <th>Insurance</th>
                            <th>Charge Date</th>
                            <th class="text-right">Charge Amount($)</th>
                            <th style="padding-left: 5px;">Payment Date</th>
                            <th class="text-right">Allowed Amount</th>
                            <th class="text-right" style="border-right: 1px solid #ccc;">Payment Amount($)</th>                                                        
                        </tr> 
                    </thead>
                    <tbody>
                        <?php $total_amt_charge = 0;$total_amt_payment=0; ?>
                        @foreach($cptreport_list as  $result)
                        <?php
                        //   $insurance_payment[] = $dates->insurance_payment;
                        //  $patient_payment[] = $dates->patient_payment; 
                        @$last_name = $result->patient_details->last_name;
                        @$first_name = $result->patient_details->first_name;
                        @$middle_name = $result->patient_details->middle_name;
                        @$patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                        ?>
                        <tr>                       
                            <td style="border-left: 1px solid #ccc;padding-left: 5px;">{{ @$result->cpt_code}}</td> 
                            <td>{!! date('m/d/Y',strtotime(@$result->date_of_service)) !!}</td>
                            <td>{{ @$result->patient_details->account_no}}</td> 
                            <td>{{ @$result->claim_details->claim_number}}</td> 
                            <td>{!! @$patient_name !!}</td>                          
                            <td style="padding-left: 5px;">{{ @$result->claim_details->rend_providers->short_name}}</td>
                            <td >{{ @$result->type_name}}</td>
                            <td>{{ App\Models\Insurance::getInsuranceshortName(@$result->payer_insurance_id)}}</td> 
                            <td>{{ App\Http\Helpers\Helpers::timezone(@$result->charge_date, 'm/d/y') }}</td> 
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td> 
                            <td style="padding-left: 5px;">{{ App\Http\Helpers\Helpers::timezone(@$result->payment_date, 'm/d/y') }}</td> 
                            <td style="border-right: 1px solid #ccc;" class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->allowed) !!}</td> 
                            <td style="border-right: 1px solid #ccc;" class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->Payment_amount) !!}</td> 
                            <?php 
                        $total_amt_payment += @$result->Payment_amount;
                        $total_amt_charge += @$result->charge;
                        ?>
                        </tr>
                        @endforeach
                        <tr>
                            <th style="border-left: 1px solid #ccc;padding-left: 5px;">Totals ($)</th>                              
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-right" style="border-right: 1px solid #ccc;">${!!App\Http\Helpers\Helpers::priceFormat($total_amt_payment)!!}</th>
                        </tr>
                    </tbody>   
                </table>		
            </div>
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif
        </div>
        <ul style="line-height:20px;">
            <li>{{$rendering_imp}}</li>
        </ul>
    </body>
</html>