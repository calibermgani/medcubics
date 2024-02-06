<?php try{ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Patient - Itemized Bill</title>
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
        @$patient = $result['patient'];
        @$wallet = $result['wallet'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$header = $result['header'];
        @$heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="9" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="9" style="text-align:center;">Patient - Itemized Bill</td>
            </tr>
            <tr>
                <td colspan="9" style="text-align:center;">User :@if(Auth::check() && isset(Auth::user()->name)) {{ Auth::user()->name }} @endif | Created :{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</td>
            </tr>
            <tr>
                <td colspan="9" style="text-align:center;">
                    <?php $i = 1; ?>
                    @if(isset($header) && !empty($header))
                    @foreach($header as $header_name => $header_val)
                    <?php $hn = $header_name; ?>
                    {{ @$header_name }} : {{str_replace('-','/', @$header_val)}}@if($i < count((array)$header)) | @endif
                    <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>
        <?php
        $total_charges = $total_insurance_payments = $total_patient_payments = $total_insurance_adjustments = $total_patient_adjustments = $total_insurance_refunds = $total_patient_refunds = $total_wallet_balance = $total_insurance_over_payment = $outstanding_ar = $paid = $writeoff = $withheld = 0;
        ?>
        @if(!empty($patient))
            @foreach($patient as $r)
                <table>
                    <tbody>
                    <?php
                    $ins_overpayment = App\Models\Payments\ClaimInfoV1::InsuranceOverPayment($r['claim']['id']);
                    $insurance_refund =  App\Models\Payments\ClaimInfoV1::getRefund($r['claim']['id'], 'insurance_paid_amt');
                    $patient_refund = @App\Models\Payments\ClaimInfoV1::getRefund($r['claim']['id'], 'patient_paid_amt');
                    $patient_id = $r['claim']['patient_id'];
                    ?>
                    <tr>
                        <td></td>
                        <td>Acc No</td>
                        <td>{{ $r['claim']['account_no'] }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Patient Name</td>
                        <td>
                            <?php
                            $patient_name = App\Http\Helpers\Helpers::getNameformat(@$r['claim']['last_name'], @$r['claim']['first_name'], @$r['claim']['middle_name']);
                            $age = App\Http\Helpers\Helpers::dob_age($r['claim']['dob']);
                            echo $patient_name." (".$r['claim']['dob'],", ".$age." - ".$r['claim']['gender'].")";
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>SSN</td>
                        <td>{{ !empty($r['claim']['ssn'])?$r['claim']['ssn']:'Nil'  }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Address</td>
                        <td>{{ $r['claim']['address1'] }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Status</td>
                        <td>{{ $r['claim']['status'] }}</td>
                        <td>Billed To</td>
                        <td>
                            @if(!empty($r['claim']['insurance_id']))
                                {{ \App\Http\Helpers\Helpers::getInsuranceName($r['claim']['insurance_id']) }} - {{ \App\Http\Helpers\Helpers::getInsuranceFullName($r['claim']['insurance_id']) }}
                            @else
                                Patient
                            @endif
                        </td>
                        <td>Total Charge</td>
                        <td data-format='0.00'>{{$r['claim']['total_charge']}}</td>
                       <?php $total_charges += $r['claim']['total_charge'];?>
                    </tr>
                    <tr>
                        <td></td>
                        <td>DOS</td>
                        <td>{{App\Http\Helpers\Helpers::dateFormat($r['claim']['date_of_service'],'dob')}}</td>
                        <td>Rendering Provider</td>
                        <td>{{ @$r['claim']['rendering_short_name']}} - {{ @$r['claim']['rendering_name'] }}</td>
                        <td>Ins Overpayment</td>
                        <td data-format='0.00' style="@if($ins_overpayment<0) color:#ff0000; @endif">{{$ins_overpayment}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Claim No</td>
                        <td>{{ $r['claim']['claim_number'] }}</td>
                        <td>Billing Provider</td>
                        <td>{{ @$r['claim']['billing_short_name']}} - {{ @$r['claim']['billing_name'] }}</td>
                        <td>Insurance Refund</td>
                        <td data-format='0.00' style="@if($insurance_refund<0) color:#ff0000; @endif">{{$insurance_refund}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Facility</td>
                        <td>{{ @$r['claim']['facility_short_name']}} - {{ @$r['claim']['facility_name'] }}</td>
                        <td>Patient Refund</td>
                        <td data-format='0.00' style="@if($patient_refund<0) color:#ff0000; @endif">{{$patient_refund}}</td>
                    </tr>
                    
                    @foreach($r['CPT'] as $key=>$cpt)
                        <tr>
                            <td></td>
                            <td>CPT</td>
                            <td colspan="3">
                                <?php 
                                    $code = \DB::table('claim_cpt_info_v1')->select('cpt_code','charge')->where('id',$key)->first(); 
                                    $icd = \DB::table('icd_10')->selectRaw('group_concat(icd_code) as icd')->whereRaw("id in (".$r['claim']['icd_codes'].")")->first(); 
                                    echo $code->cpt_code." (".$icd->icd.")"; 
                                    ?>
                            </td>
                            <td>CPT Amount</td>
                            <td data-format='0.00'>{{$code->charge}}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="background: #00837C; color:#fff;">Trans Date</td>
                            <td style="background: #00837C; color:#fff;">Responsibility</td>
                            <td style="background: #00837C; color:#fff;">Description</td>
                            <td style="background: #00837C; color:#fff;">Payment Type</td>
                            <td style="background: #00837C; color:#fff;text-align: right;">Payments</td>
                            <td style="background: #00837C; color:#fff;text-align: right;">Adj</td>
                            <td style="background: #00837C; color:#fff;text-align: right;">Pat Bal</td>
                            <td style="background: #00837C; color:#fff;text-align: right;">Ins Bal</td>
                        </tr>
                        @foreach($cpt as $c)
                        <tr>
                            <td></td>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($c['claim_cpt_created_at'])}}</td>
                            <td>
                            @if($c['claim_cpt_responsibility']==0)
                                Patient
                            @else
                                {{ \App\Http\Helpers\Helpers::getInsuranceName($c['claim_cpt_responsibility']) }}
                                @if(isset($c['respCat']) && $c['respCat'] != '')
                                    <span class="{{@$c['resp_bg_class']}}">{{ substr(@$c['respCat'], 0, 1) }}</span>
                                @endif
                            @endif
                            </td>
                            <td>{!! nl2br($c['desc']) !!}</td>
                            <td>{{$c['pmt_type']}}</td>
                            <td class="text-right" style="@if($c['pmts']<0) color:#ff0000; @endif" data-format='0.00'>{{isset($c['pmts'])?$c['pmts']:'0.00'}}</td>
                            <td class="text-right" style="@if($c['adj']<0) color:#ff0000; @endif" data-format='0.00'>
                                        {{$c['adj']}}
                            </td>
                            <td class="text-right" style="@if($c['claim_cpt_pat_bal']<0) color:#ff0000; @endif" data-format='0.00'>{{$c['claim_cpt_pat_bal']}}</td>
                            <td class="text-right" style="@if($c['claim_cpt_ins_bal']<0) color:#ff0000; @endif" data-format='0.00'>{{$c['claim_cpt_ins_bal']}}</td>
                        </tr>
                        <?php
                        if($c['claim_cpt_transaction_type']=='Patient Payment' || $c['value_2'] == 'Patient')
                            $total_patient_payments += $c['pmts'];

                        if($c['claim_cpt_transaction_type']=='Insurance Payment')
                            $total_insurance_payments += $c['pmts'];

                        if($c['claim_cpt_transaction_type'] == 'Insurance Adjustment' || $c['claim_cpt_transaction_type']=='Insurance Payment'){
                            $total_insurance_adjustments += $c['adj'];
                        }

                        if($c['claim_cpt_transaction_type']=='Patient Adjustment' || $c['claim_cpt_transaction_type']=='Patient Payment'){
                            $total_patient_adjustments += $c['adj'];
                        }

                        if($c['claim_cpt_transaction_type']=='Credit Balance')
                            $total_wallet_balance += $c['pmts'];

                        ?>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>   
                <?php
                $total_insurance_over_payment += $ins_overpayment;
                $total_insurance_refunds += $insurance_refund;
                $total_patient_refunds += $patient_refund;
                ?>
            @endforeach 
            <?php
            $ar = \DB::table('pmt_claim_tx_v1')->selectRaw('sum(total_paid+total_writeoff+total_withheld) as tot_ar')->where('patient_id',$patient_id)->first(); 
            $insurance_adj = \DB::table('pmt_claim_tx_v1')->selectRaw('sum(total_writeoff+total_withheld) as ins_adj')->where('pmt_method','Insurance')->where('patient_id',$patient_id)->first(); 
            $patient_adj = \DB::table('pmt_claim_tx_v1')->selectRaw('sum(total_writeoff+total_withheld) as patient_adj')->where('pmt_method','Patient')->where('patient_id',$patient_id)->first(); 
            $insurance = \DB::table('pmt_claim_tx_v1')->selectRaw('sum(total_paid) as insurance')->where('pmt_method','Insurance')->where('patient_id',$patient_id)->first(); 
            $patient = \DB::table('pmt_claim_tx_v1')->selectRaw('sum(total_paid) as patient')->where('pmt_method','Patient')->where('patient_id',$patient_id)->first(); 
            $outstanding_ar = $total_charges - $ar->tot_ar;
            ?>
            <tr>
                <td colspan="7"></td>
                <td>Total Charges</td><td data-format='0.00' class="text-right" style="@if($total_charges<0) color:#ff0000; @endif">{{$total_charges}}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td>Total Insurance Payments</td><td data-format='0.00' class="text-right" style="@if($insurance->insurance<0) color:#ff0000; @endif">{{ !empty($insurance->insurance)?$insurance->insurance:'0.00'}}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td>Total Patient Payments</td><td data-format='0.00' class="text-right" style="@if($patient->patient<0) color:#ff0000; @endif">{{ !empty($patient->patient)?$patient->patient:'0.00' }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td>Total Insurance Adjustments</td><td data-format='0.00' class="text-right" style="@if($insurance_adj->ins_adj<0) color:#ff0000; @endif">{{ !empty($insurance_adj->ins_adj)?$insurance_adj->ins_adj:'0.00'}}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td>Total Patient Adjustments</td><td data-format='0.00' class="text-right" style="@if($patient_adj->patient_adj<0) color:#ff0000; @endif">{{ !empty($patient_adj->patient_adj)?$patient_adj->patient_adj:'0.00'}}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td>Total Insurance Refunds</td><td data-format='0.00' class="text-right" style="@if($total_insurance_refunds<0) color:#ff0000; @endif">{{ !empty($total_insurance_refunds)?$total_insurance_refunds:'0.00' }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td>Total Patient Refunds</td><td data-format='0.00' class="text-right" style="@if($total_patient_refunds<0) color:#ff0000; @endif">{{ !empty($total_patient_refunds)?$total_patient_refunds:'0.00' }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td>Total Wallet Balance</td><td data-format='0.00' class="text-right" style="@if($total_wallet_balance<0) color:#ff0000; @endif">{{ !empty($wallet)?$wallet:'0.00' }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td>Total Insurance Over-payment</td><td data-format='0.00' class="text-right" style="@if($total_insurance_over_payment<0) color:#ff0000; @endif">{{ !empty($total_insurance_over_payment)?$total_insurance_over_payment:'0.00' }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td>Outstanding AR</td><td data-format='0.00' class="text-right"  style="@if( ($outstanding_ar)<0)color:#ff0000; @endif">{{$outstanding_ar}}</td>
            </tr>
        @endif       
        <div colspan="8">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</div>
    </body>
</html>
<?php 
} catch(Exception $e){ \Log::info("Exception Msg".$e->getMessage()); } 
?>