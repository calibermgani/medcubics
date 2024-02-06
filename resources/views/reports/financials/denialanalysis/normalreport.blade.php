<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->        

    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>

    <div class="box-body  bg-white"><!-- Box Body Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2  margin-b-25 med-orange">Denial Trend Analysis</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>					 
                    @foreach($search_by as $key=>$val)
						@if($i > 0){{' | '}}@endif
                        <span class="med-green">{!! $key !!} : </span>{{ @$val[0] }}
                        <?php $i++; ?>
                    @endforeach </div>
                </div>
            </div>
        </div>

        @if(count((array)$denial_cpt_list) > 0)  
        <div class="box-body no-padding">
            <div class="table-responsive  mobile-md-scroll col-lg-12 no-padding">
                <table class="table table-striped table-bordered table-separate" id="sort_list_noorder_report">
                    <thead>
                        <tr>
                            <th>Claim No</th>
                            <th>DOS</th>
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>Insurance</th>
                            <th>Category</th>
                            <th>Rendering</th>
                            <th>Facility</th>
                            <th>Denied CPT</th>
                            <th>Denied Date</th>
                            <th>Denial Reason Code</th>
                            <th>Claim Age</th>
                            @if(isset($workbench_status) && $workbench_status == 'Include')
                                <th>Workbench Status</th>
                            @endif
							<th>Claim Sub Status</th>
                            <th class="text-right">Charge Amt($)</th>
                            <th class="text-right">Outstanding AR($)</th>							
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($denial_cpt_list as  $result)
                        <?php 
                            if(isset($result->claim_number) && $result->claim_number != ''){
                        ?>
                        <tr style="cursor:default;">
                            <td>{{ @$result->claim_number }}</td>
                            <td>{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->dos,'','-Nil-') }}</td>
                            <td>{{ @$result->account_no }}</td>
                            <td>{{ @$result->patient_name }}</td>
                            <td>{{ @$result->responsibility }}</td>
                            <td>{{ @$result->ins_category }}</td>
                            <td>{{ @$result->rendering_short_name }}</td>
                            <td>{{ @$result->facility_short_name }}</td>
                            <td>{{ @$result->cpt_code }}</td>
                            <td>{{ @$result->denial_date }}</td>
                            <td>
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
                            <td>{{ @$result->claim_age_days }}</td>
                            @if(isset($workbench_status) && $workbench_status == 'Include')
                            <td>
                                @if(isset($result->last_workbench_status))
                                    {{ $result->last_workbench_status }}
                                @else
                                    N/A
                                @endif
                            </td>
                            @endif
							<td>@if(isset($result->sub_status_desc) && $result->sub_status_desc != null){{ $result->sub_status_desc}} @else -Nil- @endif</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->total_ar_due) !!}</td>
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
							$responsibility = App\Http\Helpers\Helpers::getInsuranceName(@$result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id);
							$ins_category = @$result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->ins_category;
                            //$last_txn_id = $result->last_txn_id;
                            $cpt_info_id = $result->claim_cpt_info_id;
                        ?>
                        <tr style="cursor:default;">
                            <td>{{ @$result->claim->claim_number }}</td>
                            <td>{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->claimcpt->dos_from,'','-Nil-') }}</td>
                            <td>{{ @$result->claim->patient->account_no }}</td>
                            <td>{{ $patient_name }}</td>
                            <td>{{ $responsibility }}</td>
                            <td>{{ $ins_category }}</td>
                            <td>{{ @$result->claim->rend_providers->provider_short }}</td>
                            <td>{{ @$result->claim->facility->facility_short }}</td>
                            <td>{{ @$result->claimcpt->cpt_code }}</td>
                            <td>{{ $denial_date }}</td>
                            <td>
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
                            <td>{{ @$result->claim->claim_age_days }}</td>
                            @if(isset($workbench_status) && $workbench_status == 'Include')
                            <td>
                                @if(isset($result->last_workbench))
                                    {{ $result->last_workbench->status }}
                                @else
                                    N/A
                                @endif
                            </td>
                            @endif
							<td>@if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){{ $result->sub_status_desc}}@endif</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->claimcpt->charge) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$ar_due) !!}</td>
                        </tr>
                        <?php } ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                    Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
            </div>
        </div>
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends -->
</div><!-- /.box Ends-->