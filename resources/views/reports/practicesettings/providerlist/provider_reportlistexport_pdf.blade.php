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
            .text-right{text-align: right !important;padding-right: 5px;}
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
            .med-red {color: #ff0000 !important;}
        </style>
    </head>
    <body>
        <?php 
            $providers = $result['providers'];
            $charges = $result['charges'];
            $patient = $result['patient'];
            $insurance = $result['insurance'];
            $patient_bal = $result['patient_bal'];
            $insurance_bal = $result['insurance_bal'];
            $units = $result['units'];
            $header = $result['header'];
            $writeoff = $result['writeoff'];
            $pat_adj = $result['pat_adj'];
            $ins_adj = $result['ins_adj'];
            $header = $result['header'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
            foreach ($providers as $key => $value) {
                $abb_billing[] = @$value->short_name." - ".@$value->provider_name;        
            }
            $abb_billing = array_unique($abb_billing);
            foreach (array_keys($abb_billing, ' - ') as $key) {
                unset($abb_billing[$key]);        
            }
            $billing_imp = implode(':', $abb_billing);
            $req = @$practiceopt;
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Provider @if($req == "provider_list") List @else Summary @endif</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 1; ?>
                            @if(isset($header) && $header !='')
                            @foreach($header as $header_name => $header_val)
                            <span>{{ @$header_name }}</span> : {{ @$header_val }} @if($i<count((array)$header)) | @endif 
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="2" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="2" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>              
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if($req == "provider_list")
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th>Provider Name</th>
                            <th>Type</th>
                            <th>Created On</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($filter_group_list))
                        <?php
							@$total_adj = 0;
							@$patient_total = 0;
							@$insurance_total = 0;
						?>
                        @foreach($filter_group_list as $list)
                        <tr>                   
                            <td>{!! @$list->provider_name !!}</td>
                            <td>{!! @$list->provider_types->name !!}</td>
                            <td>{!! date('m/d/Y',strtotime(@$list->created_at)) !!}</td>
                            <td>{!! @$list->provider_user_details->short_name !!}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @else
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th>@if($header->{'Provider Type'}=="Billing")Billing @else Rendering @endif</th>
                            <th class="text-left">Units</th>
                            <th class="text-right">Charges($)</th>
                            <th class="text-right">W/O($)</th>
                            <th class="text-right">Pat Adj($)</th>
                            <th class="text-right">Ins Adj($)</th>
                            <th class="text-right">Total Adj($)</th>
                            <th class="text-right">Pat Pmts($)</th>     
                            <th class="text-right">Ins Pmts($)</th>     
                            <th class="text-right">Total Pmts($)</th>     
                            <th class="text-right">Pat Balance($)</th> 
                            <th class="text-right">Ins Balance($)</th>
                            <th class="text-right">Total Balance($)</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($providers))
                        @foreach($providers as $list)
                        <tr> 
							<td>{!! @$list->short_name." - ".$list->provider_name !!}</td>
							<?php 
								$name = $list->provider_name;
								$prID = $list->id;
								$charge = isset($charges->$prID) ? $charges->$prID : 0;
								$unit = isset($units->$prID) ? $units->$prID : 0;
								$wo = isset($writeoff->$prID) ? $writeoff->$prID : 0;
								$patient_adj = isset($pat_adj->$prID) ? $pat_adj->$prID : 0;
								$insurance_adj = isset($ins_adj->$prID) ? $ins_adj->$prID : 0;
								$pat_pmt = isset($patient->$prID) ? $patient->$prID : 0;
								$ins_pmt = isset($insurance->$prID) ? $insurance->$prID : 0;
								$pat_bal = isset($patient_bal->$prID) ? $patient_bal->$prID : 0;
								$ins_bal = isset($insurance_bal->$prID) ? $insurance_bal->$prID : 0;
							?>                           
                            <td class="text-left">{!! $unit !!}</td>
                            <td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat($charge) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($wo) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($patient_adj) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($insurance_adj) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($wo+$patient_adj+$insurance_adj) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pat_pmt) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($ins_pmt) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pat_pmt+$ins_pmt) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pat_bal) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($ins_bal) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pat_bal+$ins_bal) !!}</td>   
                        </tr>
                        @endforeach
                        @endif  
                    </tbody>
                </table>
            </div>
            <div style="page-break-inside: avoid;" >
                <h3 class="med-orange" style="margin-left:10px">Summary</h3>
                <table class="table-summary" style="width: 40%; border:1px solid #ccc; margin-left: 10px;margin-top:10px;">
                    <thead>
                        <tr>
                            <th></th>
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
                            <td class='text-right'>{!! array_sum((array)$units) !!}</td>                      
                        </tr>
                        <tr> 
                            <td>Total Charges</td>                                            
                            <td class='text-right'>${!! App\Http\Helpers\Helpers::priceFormat(array_sum((array)$charges)) !!}</td>                      
                        </tr>
                        <tr> 
                            <td>Total Adjustments ( Writeoff included )</td>
                            <td class='text-right'>${!! App\Http\Helpers\Helpers::priceFormat(array_sum((array)$writeoff)+array_sum((array)$pat_adj)+array_sum((array)$ins_adj)) !!}</td>
                        </tr> 
                        <tr> 
                            <td>Total Payments</td>                                            
                            <td class='text-right'>${!! App\Http\Helpers\Helpers::priceFormat(array_sum((array)$patient)+array_sum((array)$insurance)) !!}</td>
                        </tr>
                        <tr> 
                            <td>Total Balance</td>                                            
                            <td class='text-right'>${!! App\Http\Helpers\Helpers::priceFormat(array_sum((array)$patient_bal)+array_sum((array)$insurance_bal)) !!}</td>
                        </tr>  
                    </tbody>
                </table>
            </div>
            @endif
            <ul style="line-height:20px;">
                <li>{{$billing_imp}}</li>
            </ul>
        </div>
    </body>
</html>