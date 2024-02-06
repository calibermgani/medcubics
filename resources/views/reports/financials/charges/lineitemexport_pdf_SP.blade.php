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
                font-weight: 600 !important;
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 12px !important;}
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
        </style>
    </head>
    <body>
        <div class="header">
            <?php $heading_name = App\Models\Practice::getPracticeName($practice_id); ?>
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">                    
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}}</h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>Charge Analysis - Detailed</i></p></td>                    
                </tr>                
            </table>

            <table class="new-border" style="width:97%; margin-left: 5px; margin-top: -5px;margin-bottom: -5px;">
                <tr style="font-weight:600;">
                    <th colspan="4" style="border:none"><span><b>Created Date :</b></span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y  H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important"><span>User :</span> <span class="med-orange">@if(isset($createdBy)){{ $createdBy }}@endif</span></th>
                    @if(isset($header) && !empty($header))
                    @if($status_option == "all")
                    @foreach($header as $header_name => $header_val)
                    @if((@$header_name == "Transaction Date" || @$header_name == "Date Of Service" ))
                    <th style="color:#00877f"><span><?php $hn = $header_name; ?>{{ @$header_name }}</span> <span>: {{str_replace('-','/', @$header_val)}}</span></th>
                    @endif
                    @endforeach
                    @endif
                    <?php
                    $date_cal = json_decode(json_encode($header), true);
                    $trans = str_replace('-', '/', @$date_cal['Transaction Date']);
                    $dos = str_replace('-', '/', @$date_cal['Date Of Service']);
                    ?>
                    @if($status_option != "all")
                    @foreach($header as $header_name => $header_val)
                    @if(@$header_name == "groupBy" && $charge_date_opt="transaction_date" || @$header_name == "groupBy" && $charge_date_opt="dos_date")
                    <th style="text-align:center;"><span>{{ucfirst( @$header_name) }}</span> : {{ ucfirst(@$header_val) }}-{{ @$trans }} {{ @$dos }}</th>
                    @endif
                    @endforeach
                    @endif
                    @endif
                </tr>              
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-left:15px;padding-top:0px;">
            @if(count($claims)>0)
            <?php $count = 0;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; 
                        $claim_bal_total = $total_claim = $total_cpt =  0; $claim_units_total = 0;  $claim_cpt_total = 0; ?>
                @foreach($claims as $claims_list)
                    <?php
                    $set_title = (@$claims_list->title)? @$claims_list->title.". ":'';
                    $patient_name = $set_title.$claims_list->last_name .', '. $claims_list->first_name .' '. $claims_list->middle_name;
                    ?>
            <div class="box-border" style="page-break-after: auto; page-break-inside: avoid;width:95%;padding-top:0px; overflow: hidden;border-radius:0px;">
                <table style="width:100%;">
                    <tbody>
                        <tr>
                            <td class="med-green" style="border:0px solid red !important;">Claim No</td>
                            <td class="med-orange">{{ $claims_list->claim_number }}</td>
                            <td class="med-green">Acc No</td>
                            <td>{{ @$claims_list->account_no }}</td>
                            <td class="med-green">Patient Name</td>
                            <td>{!! $patient_name  !!}</td>
                        </tr>
                        <tr>
                            <td class="med-green">Billing</td>
                            <td>{{ @$claims_list->billProvider_short_name }}</td>
                            <td class="med-green">Rendering</td>
                            <td>{{ @$claims_list->rendProvider_short_name }}</td>
                            <td class="med-green">Facility</td>
                            <td>{{ @$claims_list->facility_short_name }}</td>
                        </tr>
                        <tr>
                            <td class="med-green">Responsibility</td>
                            <td>@if($claims_list->self_pay=="Yes")
                                Self
                                @else
                                {{ @$claims_list->insurance_short_name }}
                                @endif</td>
                            <td class="med-green">User</td>
                            <td>
                                @if($claims_list->created_by != 0 && isset($user_names[@$claims_list->created_by]) )
                                    {!! $user_names[@$claims_list->created_by] !!}
                                @endif
                            </td>
                            <td class="med-green">Entry Date</td>
                            <td>
                                @if(@$claims_list->entry_date != "0000-00-00" && $claims_list->entry_date != "1970-01-01" )
                                    <span class="">{{ App\Http\Helpers\Helpers::timezone(@$claims_list->entry_date, 'm/d/y') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="med-green">POS</td>
                            <td> {{ @$claims_list->code}} - {{@$claims_list->pos }}</td>
                            <td class="med-green">Status</td>
                            <td>{{ @$claims_list->status }}</td>
                            <td class="med-green">Insurance Type</td>
                            <td>
                                @if(isset($claims_list->type_name)) 
                                    {{ @$claims_list->type_name }}
                                @endif
                            </td>
                        </tr>
                        @if(@$claims_list->claim_reference !='')
                        <tr>
                            <td class="med-green">Reference</td>
                            <td colspan="4">{{ @$claims_list->claim_reference }}</td>                            
                        </tr>
                        @endif
                    </tbody>
                </table>
                <table style="width:100%;border: none !important;border-spacing: 0px; margin-top:-15px;">
                    <thead>
                        <tr style="background: #f1fcfb; color: #00877f;">
                            <th>DOS</th>
                            <th>CPT</th>
                            @if(in_array('include_cpt_description',$include_cpt_option))
                            <th>CPT Description</th>
                            @endif
                            @if(in_array('include_modifiers',$include_cpt_option))
                            <th>Modifiers</th>
                            @endif
                            @if(in_array('include_icd',$include_cpt_option))
                            <th class="text-left" style="background: #d9f3f0; color: #00877f;" colspan="12">ICD-10</th>
                            @endif
                            <th class="text-left">Units</th>
                            <th class="text-right">Charges($)</th>
                            <th class="text-right">Paid($)</th>
                            <!--<th class="text-right">Total Bal($)</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Claim Row -->
                        <?php
                            $dos = $cpt = $cpt_description = $modifier1 = $modifier2 = $modifier3 = $modifier4 = $icd_10 = $units = $charges = $paid = $total_bal = '';

                            if(isset($claims_list->claim_dos_list) && $claims_list->claim_dos_list != '') {
                                $claim_line_item = explode("^^", $claims_list->claim_dos_list);
                                foreach($claim_line_item as $claim_line_item_val){
                                    if($claim_line_item_val != ''){
                                        $line_item_list = explode("$$", $claim_line_item_val);
                                        $claim_cpt = $line_item_list[0];
                                        if(($line_item_list[0]) != ''){
                                            $dos       = $line_item_list[1];
                                            $cpt       = $line_item_list[2];
                                            $cpt_description = $line_item_list[3];
                                            $modifier1 = $line_item_list[4];
                                            $modifier2 = $line_item_list[5];
                                            $modifier3 = $line_item_list[6];
                                            $modifier4 = $line_item_list[7];
                                            $icd_10    = $line_item_list[8];
                                            $units     = $line_item_list[9];
                                            $charges   = $line_item_list[10];
                                            $paid      = $line_item_list[11];
                                            $total_bal = $line_item_list[12];                                                
                                        }
                                    }
                            ?>
                        <tr>                              
                            <td>{{ $dos }}</td>
                            <td>{{ $cpt }}</td>
                            @if(in_array('include_cpt_description',$include_cpt_option))                                            
                            <td>{{ $cpt_description }}</td>
                            @endif
                            @if(in_array('include_modifiers',$include_cpt_option))
                            <?php
                            $modifier_arr = array();
                            if ($modifier1 != '')
                                array_push($modifier_arr, $modifier1);
                            if ($modifier2 != '')
                                array_push($modifier_arr, $modifier2);
                            if ($modifier3 != '')
                                array_push($modifier_arr, $modifier3);
                            if ($modifier4 != '')
                                array_push($modifier_arr, $modifier4);
                            if (count($modifier_arr) > 0) {
                                $modifier_val = implode($modifier_arr, ',');
                            } else {
                                $modifier_val = '-Nil-';
                            }
                            ?>
                            <td>{{@$modifier_val}}</td>
                            @endif
                            <?php $exp = explode(',', $icd_10); ?>

                            @if(in_array('include_icd',$include_cpt_option))
                            @for($i=0; $i<12;$i++)                                               
                                <td> {{ @$exp[$i] }}</td>  
                            @endfor
                            @endif
                            <td class="text-left">{!! $units !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$charges) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$paid) !!}</td>
                            <!--<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$total_bal) !!}</td>-->
                        </tr>
                        <?php 
                        $claim_billed_total += @$charges;
                        $claim_paid_total += $paid;
                        $claim_bal_total += $total_bal;
                        if(is_numeric($units))
                            $claim_units_total += $units;
                        $claim_cpt_total += count($claim_cpt); } } ?>
                        <!-- Claim Total Row -->
                        <tr>                              
                            <td class="text-right"></td>
                            <td class="text-right"></td>
                            @if(in_array('include_cpt_description',$include_cpt_option))
                            <td></td>											
                            @endif
                            @if(in_array('include_modifiers',$include_cpt_option))
                            <td class="text-right"></td>
                            @endif
                            @if(in_array('include_icd',$include_cpt_option))
                            <td colspan="12"></td>
                            @endif
                            <td style="border-radius: 20px 0px 0px 20px" class="text-right"><label class="med-green font600 no-bottom">Total</label></td>
                            <td style="" class="text-right font600">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_billed_total) !!}
                                @php  $claim_billed_total = 0; @endphp</td>
                            <td style="" class="text-right font600">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_paid_total) !!}
                                @php  $claim_paid_total = 0; @endphp</td>
                           <!-- <td style="" class="text-right font600">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_bal_total) !!}
                                @php  $claim_bal_total = 0; @endphp</td>-->
                        </tr>
                    </tbody>
                </table>
            </div>
            @php  $count++;   @endphp
            @endforeach
            <div class="summary-table" style="page-break-after: auto; page-break-inside: avoid;">
                <h4 class="med-orange" style="margin-bottom: 0px;">Summary</h4>
                <table style="width:45%;;border:1px solid #ccc;font-size:11px !important">
                    <thead>
                        <tr style="line-height:16px;"> 
                            <th></th>
                            <th class="text-left font600"><span style="font-weight:600;color:#00877f;">Counts</span></th>
                            <th class="text-right font600"><span style="font-weight:600;color: #00877f;">Value($)</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border:none;line-height: 20px;"> 
                            <td class='med-green font600' style="font-size:13px" >Total Patients</td>
                            <td class="text-left">{{$tot_summary->total_patient}}</td>
                            <td class="text-right">{{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                        </tr>
                        <tr style="line-height:20px;">
                            <td class='med-green font600'>Total CPT</td>
                            <td class="text-left">{{$tot_summary->total_cpt}}</td>
                            <td class="text-right">{{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                        </tr>
                        <tr style="line-height:20px;">
                            <td class='med-green font600'>Total Units</td>
                            <td class="text-left">{{$tot_summary->total_unit}}</td>
                            <td class="text-right">{{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                        </tr>
                        <tr style="line-height:20px;">
                            <td class='med-green font600'>Total Charges</td>
                            <td class="text-left">{{$tot_summary->total_claim}}</td>
                            <td class="text-right">{{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
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