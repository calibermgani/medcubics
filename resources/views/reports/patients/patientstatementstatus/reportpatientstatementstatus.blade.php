<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body"><!-- Box Body Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange">Statement Status - Detailed</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-0 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">
                       <?php $i = 0; ?>
                        @foreach($search_by as $key=>$val)
							@if($i > 0){{' | '}}@endif
							<span class="med-green">{!! $key !!} : </span>{{ @$val[0] }}                           
							<?php $i++; ?>
                        @endforeach </div>                     
                </div>
            </div>
        </div>
        @if(count($patient_statementstatus_filter) > 0)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
            <div class="box box-info no-shadow no-bottom no-border">
                <div class="box-body no-padding">
                    <div class="table-responsive mobile-lg-scroll mobile-md-scroll">
                        <div class="ajax_table_list hide"></div>
                        <div class="data_table_list" id="js_ajax_part">
                            <table id="sort_list_noorder" class="table table-bordered table-striped margin-l-5">
                                <thead>
                                    <tr>
                                        <th>Acc No</th>
                                        <th>Patient Name</th>
										<th>DOB</th>
										<th>SSN</th>
										<th>Statements</th>
										<th># of Statements Sent</th>
										<th>Hold Reason</th>
										<th>Hold Release Date</th>
										<th>Statement Category</th>
										<th class="text-right">Wallet Balance($)</th>
										<th class="text-right">Pat Balance($)</th>
										<th class="text-right">Insurance Balance($)</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient_statementstatus_filter as $list)     
									<?php
										$patientName = App\Http\Helpers\Helpers::getNameformat(@$list->last_name, @$list->first_name, @$list->middle_name);
										$stmt_category = isset($list->stmt_category_info->category) ?  $list->stmt_category_info->category : "N/A";
										$hold_reason = isset($list->stmt_holdreason_info->hold_reason) ?  $list->stmt_holdreason_info->hold_reason : "N/A";
										$hold_release_date = isset($list->hold_release_date) ?  $list->hold_release_date : "N/A";
										$wallet_bal = App\Models\Payments\PMTWalletV1::getPatientWalletData($list->id);
										$patPmt = App\Models\Patients\Patient::paymentclaimsum($list->id);
										$insurance_due = isset($patPmt['tins_due']) ? $patPmt['tins_due'] : 0;
										$patient_due = isset($patPmt['tpat_due']) ? $patPmt['tpat_due'] : 0; 
									?>	
                                    <tr style="cursor:default;">										
                                        <td>{!! !empty($list->account_no)? @$list->account_no : '-Nill-' !!}</td>
                                        <td>{!! !empty($patientName)? $patientName : '-Nill-' !!}</td>
                                        <td>{!! (App\Http\Helpers\Helpers::dateFormat(@$list->dob) == '01/01/70') ? '-Nil-' : App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$list->dob, '', '-Nil-', 'm/d/Y') !!}</td>
                                        <td class="text-left">@if(@$list->ssn != ''){!! @$list->ssn !!} @else -Nil- @endif</td>
                                        <td class="text-left">{!! !empty($list->statements)? @$list->statements : '-Nill-' !!}</td>
                                        <td>{!! !empty($list->statements_sent)? @$list->statements_sent : '-Nill-' !!}</td>
                                        <?php 
                                        if(isset($list->holdReason) && $list->holdReason != ''){
                                        ?>
                                        <td>{!! !empty($list->holdReason)? @$list->holdReason : '-Nill-' !!}</td>
                                        <?php 
                                            } else {
                                        ?>
                                        <td>{!! !empty($hold_reason)? @$hold_reason : '-Nill-' !!}</td>
                                        <?php } ?>
                                        <td>{!! (App\Http\Helpers\Helpers::dateFormat(@$hold_release_date) == '01/01/70') ? '-Nil-' : App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$hold_release_date, '', '-Nil-', 'm/d/y') !!}
                                        </td>
                                        <?php 
                                        if(isset($list->category) && $list->category != ''){
                                        ?>
                                        <td>{!! !empty($list->category)? @$list->category : '-Nill-' !!}</td>
                                        <?php 
                                            } else {
                                        ?>
                                        <td>{!! !empty($stmt_category)? @$stmt_category : '-Nill-' !!}</td>
                                        <?php } ?>
                                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$wallet_bal) !!}</td>
                                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$patient_due) !!}</td>
                                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$insurance_due) !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
        </div>
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->