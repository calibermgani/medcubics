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
                padding-top:30px;
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right !important;}
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
            $get_refund_datas = $result['get_refund_datas'];
            $total_refund = $result['total_refund'];
            $unposted = $result['unposted'];
            $wallet = $result['wallet'];
            $refund_result = $result['refund_result'];
            $user_names = $result['user_names'];
            $refund_type = $result['refund_type'];
            $search_by = $result['search_by'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
            foreach ($get_refund_datas as $key => $value) {
                    $abb_rendering[] = @$value->claim->rendering_provider->short_name." - ".@$value->claim->rendering_provider->provider_name;
                    $abb_billing[] = @$value->claim->billing_provider->short_name." - ".@$value->claim->billing_provider->provider_name;
                    $abb_facility[] = @$value->claim->facility_detail->short_name." - ".@$value->claim->facility_detail->facility_name;
                }
                $abb_rendering = array_unique($abb_rendering);
                foreach (array_keys($abb_rendering, ' - ') as $key) {
                    unset($abb_rendering[$key]);
                }
                $abb_billing = array_unique($abb_billing);
                foreach (array_keys($abb_billing, ' - ') as $key) {
                    unset($abb_billing[$key]);
                }
                $abb_facility = array_unique($abb_facility);
                foreach (array_keys($abb_facility, ' - ') as $key) {
                    unset($abb_facility[$key]);
                }
                $rendering_imp = implode(':', $abb_rendering);
                $billing_imp = implode(':', $abb_billing);
                $facility_imp = implode(':', $abb_facility);
         ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Refund Analysis - Detailed</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"></p></td>
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
                    <th colspan="6" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="6" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>              
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>

        <div style="">
            @if($refund_type == 'insurance')
            <div>
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;  ">
                    <thead>
                        <tr>
                            <th>Claim No</th>
                            <th>DOS</th>
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>Rendering</th>
                            <th>Billing</th>
                            <th>Facility</th>
                            <th>Insurance</th>
                            <th>Check Date</th>
                            <th>Check No</th>
                            <th>Refund Amt($)</th> 
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($get_refund_datas))
                        @foreach(@$get_refund_datas as $refund_value)
                        <?php  $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->claim->patient->last_name,@$refund_value->claim->patient->first_name,@$refund_value->claim->patient->middle_name); ?>
                        <tr>
                            <td>{{ @$refund_value->claim->claim_number }}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$refund_value->claim->date_of_service, 'dob') }}</td>
                            <td>{{ @$refund_value->claim->patient->account_no }}</td>
                            <td>{{ @$patient_name }}</td>
                            <td>{{ @$refund_value->claim->rendering_provider->short_name}}</td>
                            <td>{{ @$refund_value->claim->billing_provider->short_name}}</td>
                            <td>{{ @$refund_value->claim->facility_detail->short_name}}</td>
                            <?php $insurance_name = @$refund_value->payment_info->insurancedetail->short_name; ?>
                            <td>{{ @$insurance_name}}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$refund_value->latest_payment_check->check_details->check_date, 'dob') }}</td>
                            <td class="text-left">{{ @ucwords($refund_value->latest_payment_check->check_details->check_no) }}</td>
                            <td class="text-right" data-format="0.00" style="padding-right:5px;">{!! App\Http\Helpers\Helpers::priceFormat(abs(@$refund_value->total_paid)) !!}</td>
                            <td>{{ @ucwords($refund_value->user->short_name) }}</td>
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
                            <th>Title</th>
                            <th class="text-right">Value($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr> 
                            <td>Total Insurance Refunds</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->insurance) !!}</td>
                        </tr>
                        <tr> 
                            <td>Total Patients Refunds</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->patient)  !!}</td>
                        </tr>
                        <tr> 
                            <td>Total Refunds</td>                            
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->total) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @elseif($refund_type == 'patient' && empty($unposted) && empty($wallet))
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;  ">
                    <thead>
                        <tr>
                            <th>Patient Name</th>	
                            <th>Acc No</th>
                            <th>Refund Amt($)</th> 
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($get_refund_datas as $refund_value)
                        <?php $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->claim_patient_det->last_name,@$refund_value->claim_patient_det->first_name,@$refund_value->claim_patient_det->middle_name); ?>
                        <tr>
                            <td>{{ @$patient_name }}</td>
                            <?php $wallet_refund = @$refund_value->claim_patient_det->pmt_info[0]->refund_amt; ?>
                            <td>{{ @$refund_value->claim_patient_det->account_no }}</td>
                            <?php @$refund_amt = abs(@$refund_value->total_paid); ?>
                            <td class="text-right" data-format="0.00" style="padding-right:5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$refund_amt) !!}</td>
                            <td> {{ @ucwords($refund_value->user->short_name) }}  </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="page-break-inside: avoid;" >
                <h3 class="med-orange" style="margin-left:10px">Summary</h3>
                <table class="table-summary" style="width: 40%; border:1px solid #ccc; margin-left: 10px;margin-top:10px;">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th class="text-right">Value($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr> 
                            <td>Total Insurance Refunds</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->insurance) !!}</td>
                        </tr>
                        <tr> 
                            <td>Total Patients Refunds</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->patient)  !!}</td>
                        </tr>
                        <tr> 
                            <td>Total Refunds</td>                            
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->total) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @elseif(!is_null($unposted) && !empty($unposted) || !is_null($wallet) && !empty($wallet))
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;  ">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Check Date</th>   
                            <th>Check No</th>
                            <th>Refund Amt($)</th> 
                            <th>User</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($get_refund_datas as $refund_value)
                        <?php $refund_amt = $refund_value->pmt_amt;
                     $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->patient->last_name,@$refund_value->patient->first_name,@$refund_value->patient->middle_name);?>
                        <tr>
                            <td>{{ @$patient_name }}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$refund_value->check_details->check_date) }}</td>                       
                            <td>{{ @$refund_value->check_details->check_no }}</td>
                            <td class="text-right" data-format="0.00" style="padding-right:5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$refund_amt) !!}</td>
                            <td>{{ @ucwords($refund_value->created_user->short_name) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="page-break-inside: avoid;" >
                <h3 class="med-orange" style="margin-left:10px">Summary</h3>
                <table class="table-summary" style="width: 40%; border:1px solid #ccc; margin-left: 10px;margin-top:10px;">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th class="text-right">Value($)</th>   
                        </tr>
                    </thead>
                    <tbody>
                        <tr> 
                            <td>Total Insurance Refunds</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->insurance) !!}</td>
                        </tr>
                        <tr> 
                            <td>Total Patients Refunds</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->patient)  !!}</td>
                        </tr>
                        <tr> 
                            <td>Total Refunds</td>                            
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->total) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        <ul style="line-height:20px;">
            <li>{{$rendering_imp}}</li>
            <li>{{$billing_imp}}</li>
            <li>{{$facility_imp}}</li>
        </ul>
    </body>
</html>