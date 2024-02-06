<?php try{ ?>
<!DOCTYPE html>
<html lang="en"> 
    <head>
        <title>Reports - Charges</title>
    </head>
    <body>
        <?php 
            @$claims = $result['claims'];
            @$header = $result['search_by'];
            @$column = $result['column'];
            @$include_cpt_option = $result['include_cpt_option'];
            @$sinpage_charge_amount = $result['sinpage_charge_amount'];
            @$sinpage_claim_arr = $result['sinpage_claim_arr'];
            @$sinpage_total_cpt = $result['sinpage_total_cpt'];
            @$status_option = $result['status_option'];
            @$ftdate = $result['ftdate'];
            @$charge_date_opt = $result['charge_date_opt'];
            @$tot_summary = $result['tot_summary'];
            @$user_names = $result['user_names'];
            $user_full_names = $result['user_full_names']; 
            @$createdBy = $result['createdBy']; 
            @$practice_id = $result['practice_id'];
            @$page = $result['page'];
            @$export = $result['export'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <?php
                $colspan = 24;
                if (in_array('include_icd', $include_cpt_option))
                    $colspan += 1;
                if (in_array('include_cpt_description', $include_cpt_option))
                    $colspan += 1;
                if (in_array('include_modifiers', $include_cpt_option))
                    $colspan += 1;
            ?>
                
                <tr>                   
                    <td colspan="{{$colspan}}" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>                    
                </tr>            
                <tr>                   
                    <td colspan="{{$colspan}}" style="text-align:center;">Charge Analysis - Detailed </td>                    
                </tr>
                <tr>
                    <td colspan="{{$colspan}}" style="text-align:center;">User : @if(isset($createdBy)) {{  $createdBy }} @endif | Created : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y  H:i:s"), 'm/d/y') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="{{$colspan}}" style="text-align:center;">
                        @if($header !='' && count((array)$header) > 0)
                        <?php $i = 1; ?>
                        @foreach($header as $header_name => $header_val)
                        <span>
                            <?php $hn = $header_name; ?>
                            {{ @$header_name }}
                        </span> : @if($header_name != 'Insurance'){{str_replace('-','/', @$header_val)}} @else {{@$header_val}} @endif
                        @if($i < count((array)$header)) | @endif 
                        <?php $i++; ?>
                        @endforeach
                        <?php
                            $date_cal = json_decode(json_encode($header), true);
                            $trans = str_replace('-', '/', @$date_cal['Transaction Date']);
                            $dos = str_replace('-', '/', @$date_cal['Date Of Service']);
                        ?>
                        @endif
                    </td>
                </tr>
            </table>
        <div>
            <div  style="page-break-after: auto; page-break-inside: avoid;">
                <table>
                    <thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
                        <tr>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOB</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Policy ID</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billing</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Rendering</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">POS</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Responsibility</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance Type</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">CPT</th>
                            @if(in_array('include_cpt_description',$include_cpt_option))
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">CPT Description</th>
                            @endif
                            @if(in_array('include_modifiers',$include_cpt_option))
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Modifiers</th>
                            @endif
                            @if(in_array('include_icd',$include_cpt_option))
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">ICD-10</th>
                            @endif
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Units</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charges($)</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Paid($)</th>
                            
                            <!--<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total Balance($)</th>-->
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Status</th>
							<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Sub Status</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Hold Reason</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Entry Date</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Reference</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">First Submission</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last Submission</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count((array)$claims)>0)
                            <?php 
								$count = 0;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; 
                                $claim_bal_total = $total_claim = $total_cpt =  0; $claim_units_total = 0;  $claim_cpt_total = 0; 
							?>
                        @foreach($claims as $claims_list)
                            <?php
								$set_title = (@$claims_list->title)? @$claims_list->title.". ":'';
								$patient_name = $set_title.$claims_list->last_name .', '. $claims_list->first_name .' '. $claims_list->middle_name;
							 
								$dos = $cpt = $cpt_description = $modifier1 = $modifier2 = $modifier3 = $modifier4 = $icd_10 = '';
								$units = $charges = $paid = $total_bal = 0;   
			
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
                                    <td style="text-align:left;">{{ @$claims_list->claim_number }}</td>
                                    <td style="text-align:left;">{{ @$claims_list->account_no }}</td>
                                    <td>{{ $patient_name }}</td>
                                    <td style="text-align:left;">{{ @$claims_list->dob }}</td>
                                    <td style="text-align:left;" data-format='@'>{{ @$claims_list->policy_id }}</td>
                                    <td>{{ @$claims_list->billProvider_short_name }} - {{ @$claims_list->billProvider_name }}</td>
                                    <td>{{ @$claims_list->rendProvider_short_name }} - {{ @$claims_list->rendProvider_name }}</td>
                                    <td>{{ @$claims_list->facility_short_name }} - {{ @$claims_list->facility_name }}</td>
                                    <td>
                                        @if(@$claims_list->code != "")
                                            {{ @$claims_list->code}} - {{@$claims_list->pos }}
                                        @else
                                            -Nil-
                                        @endif
                                    </td>
                                    <td>
                                        @if($claims_list->self_pay=="Yes")
                                            Self
                                        @else
                                            {{ @$claims_list->insurance_name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($claims_list->type_name)) 
                                            {{ @$claims_list->type_name }}
                                        @endif
                                    </td>
                                    <td style="text-align:left;">{{ @$cpt }}</td>
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
										if (count((array)$modifier_arr) > 0) {
											$modifier_val = implode($modifier_arr, ',');
										} else {
											$modifier_val = '-Nil-';
										}
                                    ?>
                                    <td style="text-align:left;">{{@$modifier_val}}</td>
                                    @endif
                                    <?php $exp = explode(',', $icd_10); ?>
                                    @if(in_array('include_icd',$include_cpt_option))
                                    <td style="text-align:left;">{{@$icd_10}}</td>
                                    @endif
                                    <td style="text-align:left;">{!! @$units !!}</td>
                                    <td style="text-align: right; <?php echo($charges)<0?'color:#ff0000;':'' ?> "   data-format="#,##0.00">
                                        {!! @$charges !!}
                                    </td>
                                    <td style="text-align: right; <?php echo($paid)<0?'color:#ff0000;':'' ?> "   data-format="#,##0.00">
                                        {!! @$paid !!}
                                    </td>
                                  
                                    <!--<td style="text-align: right; <?php echo($total_bal)<0?'color:#ff0000;':'' ?> "  data-format="#,##0.00"> 
                                        {!! @$total_bal !!}
                                    </td>--> 
									<td>{{ @$claims_list->status }}</td>
									<td>
										@if(isset($claims_list->sub_status_desc) && $claims_list->sub_status_desc !== null)
											{{ $claims_list->sub_status_desc }}
										@else
											N/A
										@endif
									</td>
                                    <td>{{ (@$claims_list->option_reason!='')?@$claims_list->option_reason:'-Nil-' }}</td>
                                    <td>
                                        @if(@$claims_list->entry_date != "0000-00-00" && $claims_list->entry_date != "1970-01-01" )
                                            <span class="bg-date">{{ @$claims_list->entry_date }}</span>
										@else
											-Nil-
                                        @endif
                                    </td>
                                    <td style="text-align:left;">{{ (@$claims_list->claim_reference!='')?@$claims_list->claim_reference:"-Nil-" }}</td>
                                    <td>{{ (@$claims_list->submited_date !='')?@$claims_list->submited_date:"-Nil-"  }}</td>
                                    <td>{{ (@$claims_list->last_submited_date !='')?@$claims_list->last_submited_date:"-Nil-"  }}</td>
                                    <td>
                                        @if($claims_list->created_by != 0 )
                                        {!! \App\Http\Helpers\Helpers::user_names($claims_list->created_by) !!} - {!! \App\Http\Helpers\Helpers::getUserFullName($claims_list->created_by) !!}
                                        @endif
                                    </td>
                                </tr>
                                <?php 
                                    $claim_billed_total += (is_numeric($charges) && !empty($charges)) ? $charges : 0;
                                    $claim_paid_total += (is_numeric($paid) && !empty($paid)) ? $paid : 0;
                                    $claim_bal_total += (is_numeric($total_bal) && !empty($total_bal)) ? $total_bal : 0;
                                    $claim_units_total += (is_numeric($units) && !empty($units)) ? $units : 0;
                                    $claim_cpt_total += count((array)$claim_cpt); 
                                } 
                            } 
                                    // $claim_billed_total = 0;
                                    // $claim_paid_total = 0;
                                    // $claim_bal_total = 0;
                                    // $count++;
                            else {
                                $dos       = "-Nil-";
                                $cpt       = "-Nil-";
                                $cpt_description = "-Nil-";
                                $modifier1 = "-Nil-";
                                $modifier2 = "-Nil-";
                                $modifier3 = "-Nil-";
                                $modifier4 = "-Nil-";
                                $icd_10    = "-Nil-";
                                $units     = "-Nil-"; 
                                $charges   = 0.00;
                                $paid      = 0.00;
                                $total_bal = 0.00;
                                ?>
                            <tr>
                                <td>{{ $dos }}</td>
                                <td style="text-align: left;">{{ @$claims_list->claim_number }}</td>
                                <td style="text-align: left;">{{ @$claims_list->account_no }}</td>
                                <td>{{ $patient_name }}</td>
                                <td style="text-align:left;">{{ @$claims_list->dob }}</td>
                                <td style="text-align:left;">{{ @$claims_list->policy_id }}</td>
                                <td>{{ @$claims_list->billProvider_short_name }} - {{ @$claims_list->billProvider_name }}</td>
                                <td>{{ @$claims_list->rendProvider_short_name }} - {{ @$claims_list->rendProvider_name }}</td>
                                <td>{{ @$claims_list->facility_short_name }} - {{ @$claims_list->facility_name }}</td>
                                <td>
                                    @if(@$claims_list->code != "")
                                        {{ @$claims_list->code}} - {{@$claims_list->pos }}
                                    @else
                                        -Nil-
                                    @endif
                                </td>
                                <td>
                                    @if($claims_list->self_pay=="Yes")
                                        Self
                                    @else
                                        {{ @$claims_list->insurance_name }}
                                    @endif
                                </td>
                                <td>
                                    @if(isset($claims_list->type_name))
                                        {{ @$claims_list->type_name }}
                                    @endif
                                </td>
                                <td style="text-align:left;">{{ @$cpt }}</td>
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
									if (count((array)$modifier_arr) > 0) {
										$modifier_val = implode($modifier_arr, ',');
									} else {
										$modifier_val = '-Nil-';
									}
                                ?>
                                <td style="text-align: left;">{{@$modifier_val}}</td>
                                @endif
                                <?php $exp = explode(',', $icd_10); ?>
                                @if(in_array('include_icd',$include_cpt_option))
                                <td style="text-align:left;">{{@$icd_10}}</td>
                                @endif
                                <td style="text-align:left;">{!! @$units !!}</td>
                                <td style="text-align:right; <?php echo($charges)<0?'color:#ff0000;':'' ?> "   data-format="#,##0.00">
                                    {!! @$charges !!}
                                </td>
                                <td style="text-align:right; <?php echo($paid)<0?'color:#ff0000;':'' ?> "   data-format="#,##0.00">
                                    {!! @$paid !!}
                                </td>
                               
                                <!--<td style="text-align:right; <?php echo($total_bal)<0?'color:#ff0000;':'' ?> "  data-format="#,##0.00"> 
                                    {!! @$total_bal !!}
                                </td>--> 
                                <td>{{ @$claims_list->status }}</td>
								<td>
									@if(isset($claims_list->sub_status_desc) && $claims_list->sub_status_desc !== null)
										{{ $claims_list->sub_status_desc }}
									@else
										N/A
									@endif
								</td>
                                <td>{{ (@$claims_list->option_reason!='')?@$claims_list->option_reason:'-Nil-' }}</td>
                                <td>
                                    @if(@$claims_list->entry_date != "0000-00-00" && $claims_list->entry_date != "1970-01-01" )
                                        <span class="bg-date">{{ @$claims_list->entry_date }}</span>
                                    @endif
                                </td>
                                <td style="text-align:left;">{{ (@$claims_list->claim_reference!='')?@$claims_list->claim_reference:"-Nil-" }}</td>
                                <td>{{ (@$claims_list->submited_date!='') ? @$claims_list->submited_date : "-Nil-"  }}</td>
                                <td>{{ (@$claims_list->last_submited_date !='') ? @$claims_list->last_submited_date:"-Nil-"  }}</td>                          
                                <td>
                                    @if($claims_list->created_by != 0 )
                                    {!! \App\Http\Helpers\Helpers::user_names($claims_list->created_by) !!} - {!! \App\Http\Helpers\Helpers::getUserFullName($claims_list->created_by) !!}
                                    @endif
                                </td>
                            </tr>
                            <?php } ?>
                            @endforeach
                    </tbody>
                </table>
            </div>
            <div>
               <tr> <td class="med-green" style="color: #00877f;font-weight:600;">Summary</td> </tr>
                <table>   
                    <thead>
                        <tr style="background:#fff; ">
                            <th colspan="2"></th>
                            <th style="text-align:center">Counts</th>
                            <th style="text-align:center">Value($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="font600"> 
                            <td colspan="2">Total Patients</td>
                            <td style="text-align:left;">{{@$tot_summary->total_patient}}</td>
                            <td style="text-align:right;" data-format="#,##0.00">{{@$tot_summary->total_charge}}</td>
                        </tr>
                        <tr class=" font600">
                            <td colspan="2">Total CPT</td>
                            <td style="text-align:left;">{{$tot_summary->total_cpt}}</td>
                            <td style="text-align:right;" data-format="#,##0.00">{{@$tot_summary->total_charge}}</td>
                        </tr>
                        <tr class="font600">
                            <td colspan="2">Total Units</td>
                            <td style="text-align:left;">{{$tot_summary->total_unit}}</td>
                            <td style="text-align:right;" data-format="#,##0.00">{{@$tot_summary->total_charge}}</td> 
                        </tr>
                        <tr class=" font600">
                            <td colspan="2">Total Charges</td>
                            <td style="text-align:left;">{{@$tot_summary->total_claim}}</td>
                            <td style="text-align:right;" data-format="#,##0.00">{{@$tot_summary->total_charge}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif	
        </div>

        @if($export == "xlsx" || $export == "csv")
        <td colspan="{{$colspan}}" style="margin-left:10px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
        @endif
    </body>
</html>
<?php 
} catch(Exception $e){ 
	\Log::info("Exception Msg".$e->getMessage()); 
} 
?>