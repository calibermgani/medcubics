<div class="box box-view no-shadow">
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y  H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body bg-white border-radius-4"><!-- Box Body Starts -->
        @if(isset($header) && !empty($header))
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px dashed #f0f0f0;">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange" >
                <div class="margin-b-15">Charge Analysis - Detailed</div>
            </h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding text-center">
                    <?php $i=1; ?>
                    @foreach($header as $header_name => $header_val)
                    <span class="med-green">
                        <?php $hn = $header_name; ?>
                        {{ @$header_name }}
                    </span> : @if($header_name != 'Insurance'){{str_replace('-','/', @$header_val)}} @else {{@$header_val}} @endif
                    @if($i<count((array)$header)) | @endif 
                        <?php $i++; ?>
                    @endforeach
                </div>
                <?php
					$date_cal = json_decode(json_encode($header), true);
					$trans = str_replace('-', '/', @$date_cal['Transaction Date']);
					$dos = str_replace('-', '/', @$date_cal['Date Of Service']);
                ?>
            </div>
        </div>
        @endif
        @if(count($claims)>0)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="box box-info no-shadow no-border no-bottom">
                <div class="box-body">
                    <div class="table-responsive">
                        <?php 
							$count = 0;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; 
							$claim_bal_total = $total_claim = $total_cpt =  0; $claim_units_total = 0;  $claim_cpt_total = 0; ?>
                        @foreach($claims as $claims_list)
                            <?php
                            $set_title = (@$claims_list->title)? @$claims_list->title.". ":'';
                            $patient_name = $set_title.$claims_list->last_name .', '. $claims_list->first_name .' '. $claims_list->middle_name;
                            ?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border yes-border margin-t-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600">Claim No: {{ $claims_list->claim_number }}</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Acc No</label>
                                    {{ !empty($claims_list->account_no)? $claims_list->account_no : '-Nil-' }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Patient Name</label>
                                    {!! !empty($patient_name)? $patient_name : '-Nil-'  !!}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Billing</label>
                                    {{ !empty($claims_list->billProvider_short_name)? $claims_list->billProvider_short_name : '-Nil-' }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="rendering" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Rendering</label>
                                    {{ !empty($claims_list->rendProvider_short_name)? $claims_list->rendProvider_short_name : '-Nil-' }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Facility</label>
                                    {{ !empty($claims_list->facility_short_name)? $claims_list->facility_short_name : '-Nil-' }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Responsibility</label>
                                    @if($claims_list->self_pay=="Yes")
                                        Self
                                    @else
                                        {{ !empty($claims_list->insurance_short_name)? $claims_list->insurance_short_name : '-Nil-' }}
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">User</label>
                                    @if($claims_list->created_by != 0 && isset($user_names[@$claims_list->created_by]) && !empty($user_names[@$claims_list->created_by]))
                                        {!! $user_names[@$claims_list->created_by] !!}
                                    @else
                                        {{ '-Nil-' }}
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="entrydate_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Entry date</label>
                                    @if(@$claims_list->entry_date != "0000-00-00" && $claims_list->entry_date != "1970-01-01" && !empty($claims_list->entry_date))
                                    <span class="bg-date">{{ @$claims_list->entry_date }}</span>
                                    @else
                                    <span class="bg-date">{{ '-Nil-' }}</span>
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="entrydate_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">POS</label>
                                    <td>
                                        @if(@$claims_list->code != "")
                                            {{ @$claims_list->code}} - {{@$claims_list->pos }}
                                        @else
                                            -Nil-
                                        @endif
                                    </td>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Status</label>
                                    {{ !empty($claims_list->status)? $claims_list->status : '-Nil-' }}
                                </div>
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Sub Status</label>
                                    @if(isset($claims_list->sub_status_desc) && $claims_list->sub_status_desc !== null && !empty($claims_list->sub_status_desc))
										{{ $claims_list->sub_status_desc }}
									@else
										{{ '-Nil-' }}
									@endif
                                </div>
								
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Insurance Type</label>
                                    @if(isset($claims_list->type_name) && !empty($claims_list->type_name)) 
                                        {{ @$claims_list->type_name }}
                                    @else
                                        {{ '-Nil-' }}
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Reference</label>
                                    {{ !empty($claims_list->claim_reference)? $claims_list->claim_reference : '-Nil-' }}
                                </div>
                                @if(isset($claims_list->option_reason))
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                        <label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-orange font600">Hold Reason :</label>
                                        {{ !empty($claims_list->option_reason)? $claims_list->option_reason : '-Nil-' }}
                                    </div>
                                @endif
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Policy ID</label>
                                    {{ !empty($claims_list->policy_id)? $claims_list->policy_id : '-Nil-' }}
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <table class="popup-table-wo-border table table-responsive" style="margin-bottom: 5px; margin-top: 5px;">                    
                                    <thead>
                                        <!-- Claim Header -->
                                        <tr>
                                            <th style="background: #d9f3f0; color: #00877f;" class="text-left">DOS </th>
                                            <th style="background: #d9f3f0; color: #00877f;" class="text-left">CPT</th>
                                            @if(in_array('include_cpt_description',$include_cpt_option))
												<th style="background: #d9f3f0; color: #00877f;" class="text-left">CPT Description</th>
                                            @endif
                                            @if(in_array('include_modifiers',$include_cpt_option))
                                            <th style="background: #d9f3f0; color: #00877f;" class="text-left">Modifiers</th>
                                            @endif
                                            @if(in_array('include_icd',$include_cpt_option))
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;" colspan="12">ICD-10</th>
                                            @endif
                                            
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;" class="text-left">Units</th>
                                            <th class="text-right" style="background: #d9f3f0; color: #00877f;">Charges($)</th>
                                            <th class="text-right" style="background: #d9f3f0; color: #00877f;">Paid($)</th>
                                            
                                            <!--<th class="text-right" style="background: #d9f3f0; color: #00877f;">Total Bal($)</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $dos = $cpt = $cpt_description = $modifier1 = $modifier2 = $modifier3 = $modifier4 = $icd_10 = $units = $charges = $paid = $total_bal = '';

                                    if(isset($claims_list->claim_dos_list) && $claims_list->claim_dos_list != '') {
                                        $claim_line_item = explode("^^", $claims_list->claim_dos_list);
                                        foreach($claim_line_item as $claim_line_item_val){
                                            if($claim_line_item_val != ''){
                                                $line_item_list = explode("$$", $claim_line_item_val);
                                                $claim_cpt = $line_item_list[0];
                                                if(($line_item_list[0]) != ''){
                                                    $dos       = isset($line_item_list[1]) ? $line_item_list[1] : '-Nil-';
                                                    $cpt       = isset($line_item_list[2]) ? $line_item_list[2] : '';
                                                    $cpt_description = isset($line_item_list[3]) ? $line_item_list[3] : '';
                                                    $modifier1 = isset($line_item_list[4]) ? $line_item_list[4] : '';
                                                    $modifier2 = isset($line_item_list[5]) ? $line_item_list[5] : '';
                                                    $modifier3 = isset($line_item_list[6]) ? $line_item_list[6] : '';
                                                    $modifier4 = isset($line_item_list[7]) ? $line_item_list[7] : '';
                                                    $icd_10    = isset($line_item_list[8]) ? $line_item_list[8] : '';
                                                    $units     = isset($line_item_list[9]) ? $line_item_list[9] : '';
                                                    $charges   = isset($line_item_list[10]) ? $line_item_list[10] : '';
                                                    $paid      = isset($line_item_list[11]) ? $line_item_list[11] : '';
                                                    $total_bal = isset($line_item_list[12]) ? $line_item_list[12] : '';                                              
                                                }
                                            }
                                    ?>
                                        <tr>
                                            <td>{{ !empty($dos)? $dos : '-Nil-' }}</td>
                                            <td>{{ !empty($cpt)? $cpt : '-Nil-' }}</td>

                                            @if(in_array('include_cpt_description',$include_cpt_option))                                            
                                            <td>{{ !empty($cpt_description)? $cpt_description : '-Nil-' }}</td>
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
                                            <td>{{ !empty($modifier_val)? $modifier_val : '-Nil-' }}</td>
                                            @endif
                                            <?php $exp = explode(',', $icd_10); ?>

                                            @if(in_array('include_icd',$include_cpt_option))
                                            @for($i=0; $i<12;$i++)     
                                                @if(!empty($exp[$i]))                                          
                                                <td> {{ !empty($exp[$i])? $exp[$i] : '-Nil-' }}</td>  
                                                @else
                                                <td></td>
                                                @endif
                                            @endfor
                                            @endif

                                            <td class="text-left">{!! !empty($units)? $units : '-Nil-' !!}</td>
                                            <td class="text-right">{!! !empty($charges)? App\Http\Helpers\Helpers::priceFormat(@$charges) : '-Nil-' !!}</td>
                                            <td class="text-right">{!! !empty($paid)? App\Http\Helpers\Helpers::priceFormat(@$paid) : '-Nil-' !!}</td>
                                            <!--<td class="text-right">{!! !empty($total_bal)? App\Http\Helpers\Helpers::priceFormat(@$total_bal) : '-Nil-' !!}</td>-->
                                        </tr>
										<?php 
											$claim_billed_total += @$charges;
											$claim_paid_total += $paid;
											$claim_bal_total += $total_bal;
                                            if(is_numeric($units))
											    $claim_units_total += $units;
											$claim_cpt_total += count((array)$claim_cpt); } } 
										?>
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

                                            <td style="background: #f5fffe;border-radius: 20px 0px 0px 20px" class="text-right"><label for="total" class="med-green font600 no-bottom">Total</label></td>
                                            <td style="background: #f5fffe" class="text-right">{!! !empty($claim_billed_total)? App\Http\Helpers\Helpers::priceFormat(@$claim_billed_total) : '0.00' !!}</td>
                                            @php  $claim_billed_total = 0; @endphp
                                            <td style="background: #f5fffe" class="text-right">{!! !empty($claim_paid_total)? App\Http\Helpers\Helpers::priceFormat(@$claim_paid_total) : '0.00' !!}</td>
                                           
                                            @php  $claim_paid_total = 0; @endphp
                                           <!-- <td style="background: #f5fffe" class="text-right">{!! !empty($claim_bal_total)? App\Http\Helpers\Helpers::priceFormat(@$claim_bal_total) : '0.00' !!}</td>-->
                                            @php  $claim_bal_total = 0; @endphp
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @php  $count++;   @endphp
                        @endforeach
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-10 no-padding dataTables_info">
                                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding margin-t-m-10">{!! $pagination->pagination_prt !!}</div>
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 no-padding">
                                <div class="box-header-view-white no-border-radius pr-t-5 margin-b-5">
                                    <i class="fa fa-bars"></i><strong class="med-orange font13"> OLD Summary</strong>                     
                                </div><!-- /.box-header -->
                                <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4" style="border: 1px solid #00877f;">    
                                    <thead>
                                        <th></th>
                                        <th class="text-left">Counts</th>
                                        <th class="text-right">Value</th>
                                    </thead>
                                    <tbody>
                                         <tr> 
                                                <td class='med-green font600' >Total Patients</td>
                                                <td class="text-left">{{$tot_summary->total_patient}}</td>
                                                <td class="text-right">${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                                            </tr>
                                            <tr>
                                                <td class='med-green font600' >Total CPT</td>
                                                <td class="text-left">{{$tot_summary->total_cpt}}</td>
                                                <td class="text-right">${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                                            </tr>
                                            <tr>
                                                <td class='med-green font600' >Total Units</td>                                                
                                                <td class="text-left">{{$tot_summary->total_unit}}</td>
                                                <td class="text-right">${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                                            </tr>
                                            <tr>
                                                <td class='med-green font600' >Total Charges</td>
                                                <td class="text-left">{{ @$tot_summary->total_claim }}</td>
                                                <td class="text-right">${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                                            </tr>   
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5>No Records Found !!</h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->