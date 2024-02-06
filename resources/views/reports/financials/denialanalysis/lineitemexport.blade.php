<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Denial Trend Analysis</title>
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
			@$denial_cpt_list = $result['denial_cpt_list'];
			@$workbench_status = $result['workbench_status'];
			@$createdBy = $result['createdBy'];
			@$practice_id = $result['practice_id'];
			@$search_by = $result['search_by'];
			$heading_name = App\Models\Practice::getPracticeDetails(); 
		?>
        <table>
            <tr>                   
                <td colspan="16" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="16" style="text-align:center;">Denial Trend Analysis</td>
            </tr>
            <tr>
                <td valign="center" colspan="16" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td valign="center" colspan="16" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!} : </span>{{ @$val[0] }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count($denial_cpt_list) > 0) 
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Category</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Denied CPT</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Denied Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Denial Reason Code</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Denial Reason</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim Age</th>
                    @if(isset($workbench_status) && $workbench_status == 'Include')
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Workbench Status</th>
                    @endif
					<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim Sub Status</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charge Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Outstanding AR($)</th>                                                    
                </tr>            
            </thead>   
            <tbody>              
                @foreach($denial_cpt_list as  $result)
                <?php //sp result
                    if(isset($result->claim_number) && $result->claim_number != ''){
                ?>
                <tr>
                    <td valign="center">{{ @$result->claim_number }}</td>
                    <td valign="center">{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->dos,'','-Nil-') }}</td>
                    <td valign="center">{{ @$result->account_no }}</td>
                    <td valign="center">{{ @$result->patient_name }}</td>
                    <td valign="center">{{ @$result->responsibility_full_name }}</td>
                    <td valign="center">{{ @$result->ins_category }}</td>
                    <td valign="center">{{ @$result->rendering_short_name }} - {{ @$result->rendering_name }}</td>
                    <td valign="center">{{ @$result->facility_short_name }} - {{ @$result->facility_name }}</td>
                    <td valign="center" style="text-align:left">{{ @$result->cpt_code }}</td>
                    <td valign="center">{{ @$result->denial_date }}</td>
                    <td valign="center">
                        @if(@$result->denial_code != '')
							<?php
								$denial_code = array_unique(array_map('trim', explode(',', $result->denial_code)));
								$denial_code = rtrim(implode(',',$denial_code), ',');
							?>	
                            {{ $denial_code }}
                        @else
                            -Nil-
                        @endif
                    </td>
                    <td valign="center" style="text-align:left;width:100px;">
                        @if(@$result->denial_code != '' && @$result->denial_code != '-Nil-')
                            <?php
                              
                            $denial_code_ids = array_unique(explode(',',rtrim(@$result->denial_code,',')));
                            foreach($denial_code_ids as $key=>$id){
                                if(strstr($id,'CO')){
                                    $exp = explode('CO', $id);
                                    $denial_code_id = $exp[1];
                                }
                                elseif(strstr($id,'PR')){
                                    $exp = explode('PR', $id);
                                    $denial_code_id = $exp[1];
                                }elseif(strstr($id,'OA')){
                                    $exp = explode('OA', $id);
                                    $denial_code_id = $exp[1];
                                }elseif(strstr($id,'PI')){
                                    $exp = explode('PI', $id);
                                    $denial_code_id = $exp[1];
                                }else{
                                    $denial_code_id = $id;
                                }
                                echo \DB::table('codes')->where('transactioncode_id',$denial_code_id)->value('description');
                                if($key!=count($denial_code_ids)-1)
                                    echo '<br>';
                            }
                           
                            ?>
                        @else
                            -Nil-
                        @endif
                    </td>
                    <td style="text-align:left">{{ @$result->claim_age_days }}</td>
                    @if(isset($workbench_status) && $workbench_status == 'Include')
                    <td>
                        @if(isset($result->last_workbench_status))
                            {{ $result->last_workbench_status }}
                        @else
                            N/A
                        @endif
                    </td>
                    @endif
					<td class="text-left">@if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){{ $result->sub_status_desc}}@endif</td>
                    <td style="text-align:right;@if(@$result->charge <0) color:#ff0000; @endif" data-format="#,##0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td>
                    <td style="text-align:right;@if(@$result->total_ar_due <0) color:#ff0000; @endif" data-format="#,##0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->total_ar_due) !!}</td>
                </tr>
                <?php
                    } else {                     
                    $last_name = @$result->claim->patient->last_name;
                    $first_name = @$result->claim->patient->first_name;
                    $middle_name = @$result->claim->patient->middle_name;
                    $patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);		
                    $ar_due = @$result->total_ar_due; 		
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
					$responsibility = App\Http\Helpers\Helpers::getInsuranceFullName(@$result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id);
					$ins_category = @$result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->ins_category;	
                    //$last_txn_id = $result->last_txn_id;	
                    $cpt_info_id = $result->claim_cpt_info_id;	
                ?>
                <tr>
                    <td valign="center">{{ @$result->claim->claim_number }}</td>
                    <td valign="center">{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->claimcpt->dos_from,'','-Nil-') }}</td>
                    <td valign="center">{{ @$result->claim->patient->account_no }}</td>
                    <td valign="center">{{ $patient_name }}</td>
                    <td valign="center">{{ $responsibility }}</td>
                    <td valign="center">{{ $ins_category }}</td>
                    <td valign="center">{{ @$result->claim->rend_providers->provider_short }} - {{ @$result->claim->rend_providers->provider_name }}</td>
                    <td valign="center">{{ @$result->claim->facility->facility_short }} - {{ @$result->claim->facility->facility_name }}</td>
                    <td valign="center" style="text-align:left">{{ @$result->claimcpt->cpt_code }}</td>
                    <td valign="center">{{ $denial_date }}</td>
                    <td valign="center" style="text-align:left">
                        @if(@$result->lastcptdenialdesc->claimcpt_txn->denial_code != '')
							<?php
								$denial_code = $result->lastcptdenialdesc->claimcpt_txn->denial_code;
								$denial_code = array_unique(array_map('trim', explode(',', $denial_code)));
								$denial_code = rtrim(implode(',',$denial_code), ',');
							?>	
                            {{ $denial_code }}                            
                        @else 
                            -Nil-	
                        @endif
                    </td>
                    <td style="text-align:left;width:100px;">
                        @if(@$result->lastcptdenialdesc->claimcpt_txn->denial_code != '' && @$result->lastcptdenialdesc->claimcpt_txn->denial_code != '-Nil-')
                            <?php
                            $denial_code_ids = array_unique(explode(',',rtrim(@$result->lastcptdenialdesc->claimcpt_txn->denial_code,',')));
                            foreach($denial_code_ids as $key=>$id){
                                if(strstr($id,'CO')){
                                    $exp = explode('CO', $id);
                                    $denial_code_id = $exp[1];
                                }elseif(strstr($id,'PR')){
                                    $exp = explode('PR', $id);
                                    $denial_code_id = $exp[1];
                                }elseif(strstr($id,'OA')){
                                    $exp = explode('OA', $id);
                                    $denial_code_id = $exp[1];
                                }elseif(strstr($id,'PI')){
                                    $exp = explode('PI', $id);
                                    $denial_code_id = $exp[1];
                                }else{
                                    $denial_code_id = $id;
                                }
                                echo \DB::table('codes')->where('transactioncode_id',$denial_code_id)->value('description');
                                if($key!=count($denial_code_ids)-1)
                                    echo '<br>';
                            }
                            ?>
                        @else 
                            -Nil- 
                        @endif
                    </td>
                    <td style="text-align:left">{{ @$result->claim->claim_age_days }}</td>
                    @if(isset($workbench_status) && $workbench_status == 'Include')
                    <td>
                        @if(isset($result->last_workbench))
                            {{ $result->last_workbench->status }}
                        @else 
                            N/A
                        @endif
                    </td>
                    @endif
					<td class="text-left">@if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){{ $result->sub_status_desc}}@endif</td>
                    <td style="text-align:right;@if(@$result->claimcpt->charge <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$result->claimcpt->charge !!}</td>
                    <td style="text-align:right;@if(@$ar_due <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$ar_due !!}</td>
                </tr>
                <?php } ?>
                @endforeach
            </tbody>   
        </table>
        @endif
        <td colspan="16">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>