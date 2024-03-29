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
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}}</h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>AR Denial List</i></p></td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="7" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="8" style="border:none;text-align: right !important"><span>User :</span> <span class="">{{ Auth::user()->short_name }}</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;float:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if(count($denial_cpt_list) > 0)
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Claim No</th>
                            <th>DOS</th>
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>Insurance</th>
                            <th>Category</th>
                            <th>Provider</th>
                            <th>Facility</th>								
                            <th>Denied CPT</th>
                            <th>Denied Date</th>
                            <th>Sub Status</th>
                            <?php /*
                            <th>Denial Reason Code</th>
                            */ ?>
                            <th>Claim Age</th>
                            @if(isset($workbench_status) && $workbench_status == 'Include')
                                    <th>Workbench Status</th>
                            @endif
                            <th>Charge Amt($)</th>
                            <th>Outstanding AR($)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($denial_cpt_list as  $result)
                    <?php
                        $last_name = @$result->claim->patient->last_name;
                        $first_name = @$result->claim->patient->first_name;
                        $middle_name = @$result->claim->patient->middle_name;
                        $patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);

                        $ar_due = @$result->total_ar_due;
                        $tot_charge = isset($result->total_charge) ? $result->total_charge : @$result->claimcpt->charge;
                        if(isset($result->lastcptdenialdesc->pmtinfo)) {
                                if($result->lastcptdenialdesc->pmtinfo->pmt_mode == 'EFT')
                                        $denial_date = @$result->lastcptdenialdesc->pmtinfo->eft_details->eft_date;
                                elseif($result->lastcptdenialdesc->pmtinfo->pmt_mode == 'Credit')
                                        $denial_date = @$result->lastcptdenialdesc->pmtinfo->credit_card_details->expiry_date ;
                                else 
                                        $denial_date = @$result->lastcptdenialdesc->pmtinfo->check_details->check_date ;
                        }
                        $denial_date = App\Http\Helpers\Helpers::dateFormat(@$denial_date);

                        $responsibility = 'Patient';
                        $ins_category = 'Patient';
                        /*
                        if($result->claim->insurance_details){
                                $responsibility = App\Http\Helpers\Helpers::getInsuranceName(@$result->claim->insurance_details->id);
                                $ins_category= @$result->claim->insurance_category;
                        }*/
                        $responsibility = App\Http\Helpers\Helpers::getInsuranceName(@$result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id);
                        $ins_category = @$result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->ins_category;
                        //$last_txn_id = $result->last_txn_id;
                        $cpt_info_id = $result->claim_cpt_info_id;
                        if(isset($result->cpt_codes)){		
                            $cpt_arr = array_unique(explode(",", $result->cpt_codes));
                            $cpt_codes = implode(",", $cpt_arr);
                        } else {
                            $cpt_codes = @$result->claimcpt->cpt_code;
                        }
                    ?>
                    <tr>
                        <td>{{ @$result->claim->claim_number }}</td>
                        <td>{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->claimcpt->dos_from,'','Nil') }}</td>
                        <td>{{ @$result->claim->patient->account_no }}</td>
                        <td>{{ $patient_name }}</td>
                        <td>{{ $responsibility }}</td>
                        <td>{{ $ins_category }}</td>
                        <td>{{ @$result->claim->rend_providers->provider_short }}</td>
                        <td>{{ @$result->claim->facility->facility_short }}</td>
                        <td class="text-left">{{ @$cpt_codes }}</td>
                        <td>{{ $denial_date }}</td>
                        <td>
                            @if(isset($result->claim->claim_sub_status->sub_status_desc) && $result->claim->claim_sub_status->sub_status_desc != '')
                                    {{ @$result->claim->claim_sub_status->sub_status_desc }}
                            @else
                                    -Nil-	
                            @endif	
                        </td>
                        <?php /*
                        <td>
                                @if(@$result->lastcptdenialdesc->claimcpt_txn->denial_code != '')
                                        {{ rtrim(@$result->lastcptdenialdesc->claimcpt_txn->denial_code,',') }}
                                @else
                                        Nil
                                @endif
                        </td>
                        */ ?>
                        <td class="text-left">{{ @$result->claim->claim_age_days }}</td>
                        @if(isset($workbench_status) && $workbench_status == 'Include')
                        <td>
                            @if(isset($result->last_workbench))
                                {{ $result->last_workbench->status }}
                            @else
                                N/A
                            @endif
                        </td>
                        @endif
                        <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$tot_charge) !!}</td>
                        <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$ar_due) !!}</td>
                    </tr>
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