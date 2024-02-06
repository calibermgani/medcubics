<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important; padding: 10px;
            }
            .summary-table table tbody tr:nth-of-type(odd) td{
                border-bottom: 0px solid #d7f4f2; border-top: 0px solid #d7f4f2;line-height: 30px !important;
            }
            th {
                text-align:left !important;
                font-size:10px !important;
                font-weight: 600 !important;
                border-bottom: 0px solid #ccc;
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 12px !important;}
            .table-summary tbody tr{line-height: 22px !important;} 
            .table-summary table tbody tr td{font-size:13px !important;line-height: 22px;} 
            .table-summary table tbody tr td:first-child{border-right: 1px solid #ccc !important;} 
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
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
            .c-paid, .Paid{color:#02b424;}
            .c-denied, .Denied{color:#d93800;}
            .m-ppaid, .c-ppaid{color:#2698F8}
            .ready-to-submit, .Ready{color:#5d87ff;}
            .Rejection{color: #f07d08;}
            .Hold{color:#110010;}
            .claim-paid{background: #defcda; color:#2baa1d !important;}
            .claim-denied{ color:#d93800 !important;}
            .claim-submitted{background: #caf4f3; color:#41a7a5 !important}
            .claim-ppaid{background: #dbe7fe; color:#2f5dba !important;}
            .Patient{color:#e626d6;}
            .Submitted{color:#009ec6;}
            .Pending{color:#313e50;}
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
            $payments = $result['payments'];
            $header = $result['header'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center" >{{@$heading_name}} - <i>Payment Analysis – Detailed Report</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i=1; ?>
                                @if(isset($header) && $header !='')
                                @foreach($header as $header_name => $header_val)
                                <span>{{ @$header_name }}</span> :  {{ @$header_val }} @if ($i < count((array)$header)) | @endif
                                <?php $i++; ?>
                            @endforeach
                            @endif
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none;text-align: left !important"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y',@$practice_id) }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
            
            
        </div>
        <?php
            $total_ins = 0;
            $total_pat = 0;
            $claim_paid = [];
        ?>        
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-left:10px;padding-top:20px;">
            @if(!empty($payments))
            <?php $count = 0;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; $claim_bal_total = $total_claim = $total_cpt =  0;  ?>
             @foreach($payments as $payments_list)
             @if($payments_list->total_paid > 0 && $payments_list->pmt_method=='Patient' || $payments_list->pmt_method=='Insurance')
            <?php
                $patient = @$payments_list->claim_patient_det;
                //$payment = $payments_list->pmt_info->claims;
                $claim = $payments_list->claim;
                $check_details = $payments_list->pmt_info->check_details;
                $eft_details = $payments_list->pmt_info->eft_details;
                $creditCardDetails = $payments_list->pmt_info->credit_card_details;
                $set_title = (@$patient->title)? @$patient->title.". ":'';
                $patient_name =     $set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name); 
            ?>
            <div class="box-border" style="page-break-after: auto; page-break-inside: avoid;width:96%;padding-top:0px;margin-top: -10px; overflow: hidden;border-radius:0px;">
                <table class="" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td class="med-green" style="">Claim No</td>
                            <td class="med-orange" style="">{{ $claim->claim_number }}</td>
                            <td class="med-green" style="">Acc No</td>
                            <td style="">{{ @$patient->account_no }}</td>
                            <td class="med-green" style="">Patient Name</td>
                            <td>{{ $patient_name }}</td>
                            <td class="med-green" style="">Payer</td>
                            @if($payments_list->payer_insurance_id==0)
                            <td>Self</td>
                            @else             
                            <?php $insurance_name = App\Http\Helpers\Helpers::getInsuranceName($payments_list->payer_insurance_id);?>
                            <td>{{ @$insurance_name }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="med-green" style="">User</td>
                            <td>{{ @$payments_list->pmt_info->created_user->short_name }}</td>
                            <td class="med-green">Billing</td>
                            <td>{{ (isset($payments_list->claim->billing_provider->short_name))?@$payments_list->claim->billing_provider->short_name:'-Nil-' }}</td>
                            <td class="med-green">Rendering</td>
                            <td>{{ (isset($payments_list->claim->rendering_provider->short_name))?@$payments_list->claim->rendering_provider->short_name:'-Nil-' }}</td>
                            <td class="med-green">Facility</td>
                            <td>{{ (isset($payments_list->claim->facility_detail->short_name))?@$payments_list->claim->facility_detail->short_name:'-Nil-' }}</td>
                        </tr>
                        <tr>
                            <td class="med-green" style="">Payment Type</td>
                            <td style="">{{ @$payments_list->pmt_info->pmt_mode }}</td>
                            @if($payments_list->pmt_info->pmt_mode =='Check')
                                <td class="med-green" style="">Check Number</td>
                                <td style="">{{ @$check_details->check_no }}</td>
                                <td class="med-green" style="">Check Date</td>
                                @if(empty($check_details->check_date))
                                <td style="">-Nil-</td>
                                @else                               
                                <td style="">{{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }} </td>
                                @endif 
                            @elseif($payments_list->pmt_info->pmt_mode =='Money Order')
                                <td class="med-green" style="">MO Number</td>
                                <td style="">{{ @$check_details->check_no }}</td>
                                <td class="med-green" style="">MO Date</td>
                                @if(empty($check_details->check_date))
                                <td style="">-Nil-</td>
                                @else                               
                                <td style="">{{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }} </td>
                                @endif 
                            @elseif($payments_list->pmt_info->pmt_mode =='EFT')
                                <td class="med-green" style="">EFT No</td>
                                @if(empty($eft_details->eft_no))
                                <td style="">-Nil-</td>
                                @else
                                <td style="">{{ $eft_details->eft_no }}</td>
                                @endif
                                <td class="med-green" style="">EFT Date</td>
                                @if(empty($eft_details->eft_date))
                                <td style="">-Nil-</td>
                                @else                               
                                <td style="">{{ App\Http\Helpers\Helpers::dateFormat($eft_details->eft_date) }}  </td>
                                @endif 
                            @elseif($payments_list->pmt_info->pmt_mode =='Credit')
                                <td class="med-green" style="">Card Type</td>
                                @if(empty($creditCardDetails->card_type))
                                <td style="">-Nil-</td>
                                @else
                                <td style="">{{ @$creditCardDetails->card_type }} </td>
                                @endif
                                <td class="med-green" style="">Card Number</td>
                                @if(empty($creditCardDetails->card_last_4))
                                <td style="">-Nil-</td>
                                @else                               
                                <td style="">{{ @$creditCardDetails->card_last_4 }}   </td>
                                @endif 
                            @endif
                            <td class="med-green" style="">Transaction Date</td>
                            <td>
                                @if(@$payments_list->created_at != "0000-00-00" && $payments_list->created_at != "1970-01-01" )
                                <span class="bg-date" style="">{{ App\Http\Helpers\Helpers::timezone(@$payments_list->created_at, 'm/d/y') }}</span>
                                @endif
                            </td>
                            <td class="med-green" style="">Reference</td>
                            <td>@if(@$payments_list->pmt_info->reference =='') -Nil- @else {{ @$payments_list->pmt_info->reference }} @endif</td>
                        </tr>
                    </tbody>
                </table>

                <div>  
                <table style="overflow: hidden;border-spacing: 0px;margin-top:-10px; font-weight:normal;width: 100%; padding-left: 10px;">
                    <thead>
                        <tr style="background:#f1fcfb;color:#00877f;">
                            <th style="">DOS</th>
                            <th style="">CPT</th>
                            <th style="text-align: right !important;padding-right: 5px;">Billed Amt($)</th>                                                                 
                            <th style="text-align: right !important;padding-right: 5px;">Allowed Amt($)</th>                                                                 
                            <th style="text-align: right !important;padding-right: 5px;">W/O($)</th>
                            <th style="text-align: right !important;padding-right: 5px;">Ded($)</th>
                            <th style="text-align: right !important;padding-right: 5px;">Co-Pay($)</th>
                            <th style="text-align: right !important;padding-right: 5px;">Co-Ins($)</th>
                            <th style="text-align: right !important;padding-right: 5px;">Other Adjustment($)</th>
                            <th style="text-align: right !important;padding-right: 5px;">Paid($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Claim Row -->
                        @foreach($payments_list->cpt_fin as $trans_cpt_details)    
                        <tr>                              
                            <td>{{ date('m/d/Y',strtotime(@$claim->date_of_service)) }}</td>
                            <td>{{ @$trans_cpt_details->claimcpt->cpt_code }}</td> 
                            <td style="text-align: right;padding-right: 5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$claim->total_charge) !!}</td>
                            <td style="text-align: right;padding-right: 5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->allowed) !!}</td>
                            <td style="text-align: right;padding-right: 5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->writeoff) !!}</td>
                            <td style="text-align: right;padding-right: 5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->deduction) !!}</td>
                            <td style="text-align: right;padding-right: 5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->copay) !!}</td>
                            <td style="text-align: right;padding-right: 5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->coins) !!}</td>
                            <td style="text-align: right;padding-right: 5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->withheld) !!}</td>
                                <?php 
                                    $adj = @$trans_cpt_details->adjustment+@$trans_cpt_details->with_held; 
                                    $trans_amt = @$trans_cpt_details->co_pay+@$trans_cpt_details->co_ins+@$trans_cpt_details->deductable; 
                                    $claim_paid_total += @$trans_cpt_details->patient_paid+@$trans_cpt_details->insurance_paid; 
                                    $bal = @$payments_list->fin[0]->patient_due+@$payments_list->fin[0]->insurance_due; 
                                ?>      
                            <td style="text-align: right;padding-right: 5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->paid) !!}</td>
                            <?php
                                $total_amt_bal      += @$bal;
                                $claim_bal_total    += @$bal;
                                $count_cpt += 1;
                            ?>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
                </div>
            </div>
            {{--*/ $count++;   /*--}} 
            @endif
            @endforeach
            <div class="hide summary-table" style="page-break-after: auto; page-break-inside: avoid;">
                <h3 class="med-orange">Summary</h3>
                <table class="summary-table table-borderless" style="width:96%;margin-top:10px;border:1px solid #ccc;">
                    <tbody>
                        @if($header !='')
                        @foreach($header as $header_name => $header_val)
                        @if($header_name=='Transaction Date')
                        <tr class="med-green font600" style="border:none;">
                            <td style="">Transaction Date</td>
                            <td class="text-right" style="">{{ @$header_val }}</td>
                        </tr>
                        @endif
                        @if($header_name=='DOS Date')
                        <tr class="med-green font600">
                            <td>DOS</td>
                            <td class="text-right">{{ @$header_val }}</td>
                        </tr>
                        @endif
                        @endforeach
                        @endif          
                        @if($header->Payer!="Insurance Only")          
                        <tr class="med-green font600">
                            <td>Total Patient Payments</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->patPmt) !!}</td>
                        </tr>
                        @endif
                        @if($header->Payer=="Insurance Only")
                        <tr class="med-green font600">
                            <td>Total Insurance Payments</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->insPmt) !!}</td>
                        </tr>
                        @endif
                        
                         @if($header->Payer!="Patient Payments")
                        <tr class="med-green font600">
                            <td>Total Write-Off</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->wrtOff) !!}</td>
                        </tr>
                        <tr class="med-green font600">
                            <td>Total Other Adjustments</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->other) !!}</td>
                        </tr>
                        <tr class="med-green font600">
                            <td>Total Deductible</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->deduction) !!}</td>
                        </tr>
                        <tr class="med-green font600">
                            <td>Total Co-Pay</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->copay) !!}</td>
                        </tr>
                        <tr class="med-green font600">
                            <td>Total Co-Insurance</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->coins) !!}</td>
                        </tr>
                        <tr class="med-green font600">
                            <td>Total EFT Payments</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->eft) !!}</td>
                        </tr>
                        @endif
                        <tr class="med-green font600">
                            <td>Total Check Payments</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->check) !!}</td>
                        </tr>
                        @if($header->Payer!="Insurance Only")
                        <tr class="med-green font600">
                            <td>Total Cash Payments</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->cash) !!}</td>
                        </tr>
                        <tr class="med-green font600">
                            <td>Total MO Payments</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->mo) !!}</td>
                        </tr>
                        @endif
                        <tr class="med-green font600">
                            <td>Total CC Payments</td>
                            <td data-format="0.00" class="text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->cc) !!}</td>
                        </tr>
                        
                        </tbody>
                        </table>
                         
                       
                    
            </div>                    
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif  
        </div>
    </body>
</html>