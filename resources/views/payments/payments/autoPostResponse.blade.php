@foreach($postedClaim as $list) 
<div class="box box-view no-shadow yes-border no-bottom no-padding"><!--  Box Starts -->
    <div class="box-body form-horizontal no-padding">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10" style="padding-bottom: 8px;">
            <input type="hidden" name="post_insurance_name" value="{{ $list['insurance_company'] }}" />
            <div class="col-lg-12 no-padding">
                <div class="col-lg-4 p-l-0"><span class="font600 med-orange">Check No :</span> <span class="font600">{{ $list['check_no'] }}</span></div>
                <div class="col-lg-4"><span  class="font600 med-orange">Check Amount :</span> <span class="font600">{!! App\Http\Helpers\Helpers::priceFormat($list['check_amount']) !!}</span></div>
                <div class="col-lg-4"><span   class="font600 med-orange">Check Date :</span> <span class="font600">{!! App\Http\Helpers\Helpers::dateFormat($list['check_date']) !!}</span></div>
            </div>
        </div>
    </div>
</div>

<div class="modal-body no-padding">

    <div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
        <div class="box-body form-horizontal no-padding pat-ins-search-scroll">


            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  margin-t-0"><!--  Left side Content Starts -->
                <div class="box box-view no-shadow yes-border"><!--  Box Starts -->
                    <div class="box-header-view">
					@if($pageType == 'Era Status Popup')
                        <i class="livicon" data-name="users-add"></i> <h3 class="box-title">Ready For Posting</h3>
					@else
						<i class="livicon" data-name="users-add"></i> <h3 class="box-title">Posted Claims</h3>
					@endif
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool js-collapse" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.minus')}}"></i></button>
                        </div>
                    </div><!-- /.box-header -->                                 
                    <div class="box-body js-collpased no-padding form-horizontal">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">


                            <?php $count = 1001; ?>
                            @if(!empty($list['posted']))
								@foreach($list['posted'] as $key => $Plist)  
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border yes-border margin-t-10">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
									<?php 
											if(isset($Plist['claim_id']) && empty($Plist['claim_id']))
												$chargeUrl = '#'; 
											else
												$chargeUrl = '/charges/'.@$Plist['claim_id'].'/charge_edit'; 
												
											if(isset($Plist['patient_id']) && empty($Plist['patient_id']))
												$ledgerUrl = '#';
											else
												$ledgerUrl = 'patients/'.@$Plist['patient_id'].'/ledger';
									?>
										<span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600">
										@if($chargeUrl != '#')
											<a href="{{ url($chargeUrl) }}" target="_blank">Claim No: {{ @$Plist['claim_no'] }}</a>
										@else
											Claim No: {{ @$Plist['claim_no'] }}
										@endif
										</span>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
											<label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Acc No</label>
											@if($ledgerUrl != '#')
												<a href="{{ url($ledgerUrl) }}" target="_blank">{{ @$Plist['patient_acct'] }}</a>
											@else
												{{ @$Plist['patient_acct'] }}
											@endif
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
											<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Patient Name</label>
											@if($ledgerUrl != '#')
												<a href="{{ url($ledgerUrl) }}" target="_blank">{{ @$Plist['patient_name'] }}</a>
											@else
												{{ @$Plist['patient_name'] }}
											@endif
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
											<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Patient Policy ID</label>
											{{ @$Plist['pat_policyId'] }}
										</div>
										<!--

										-->
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive hide">
											<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Billing</label>
											Fisher, Daniel P
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive hide">
											<label for="rendering" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Rendering</label>
											Fisher, Daniel P
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive hide">
											<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Facility</label>
											Daniel P Fisher Psy D
										</div>                                                       
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive hide">
											<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Responsibility</label>
											Self
											<!--                                                                        Blue Cross Blue Shield IL
											-->
										</div>                                
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive hide">
											<label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">User</label>
											MED
										</div>                                
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive hide">
											<label for="entrydate_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Entry date</label>
											<span class="bg-date">	08/10/18 </span>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
											<label for="entrydate_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Resp</label>
											{{ @$Plist['resp'] }}
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
											<label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Status</label>
											{{ @$Plist['status'] }}
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive hide">
											<label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Insurance Type</label>
											Bcbs
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive hide">
											<label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Reference</label>	
										</div>
									</div> 
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<table class="popup-table-wo-border table table-responsive" style="margin-bottom: 5px; margin-top: 5px;">                    
											<thead>
												<!-- Claim Header -->
												<tr>
													<th style="background: #d9f3f0; color: #00877f;">DOS </th>
													<th style="background: #d9f3f0; color: #00877f;">CPT</th>
													<th style="background: #d9f3f0; color: #00877f;">Modifiers</th>
													<th class="text-right" style="background: #d9f3f0; color: #00877f;">Units</th>
													<th class="text-right" style="background: #d9f3f0; color: #00877f;">Charges</th>
													<th class="text-right" style="background: #d9f3f0; color: #00877f;">Allowed</th>
													<th class="text-right" style="background: #d9f3f0; color: #00877f;">Co-Ins</th>
													<th class="text-right" style="background: #d9f3f0; color: #00877f;">Co-Pay</th>
													<th class="text-right" style="background: #d9f3f0; color: #00877f;">Deductible</th>
													<th class="text-right" style="background: #d9f3f0; color: #00877f;">Adj</th>
													<th class="text-right" style="background: #d9f3f0; color: #00877f;">Paid</th>
												</tr>
											</thead>                
											<tbody>
												<?php $totalBilled = $totalAllowed = $totalCo_Ins = $totalCo_Pay = $totalDebuctable = $totalAdj = $totalPaid = 0;  ?>
												@if(isset($Plist['cpt']))
												@foreach($Plist['cpt'] as $subkey => $subvalue)
												<tr>                              
													<td>{{ date('m/d/y',strtotime($Plist['dos_from'][$subkey])) }}</td>     
													<td>{{ $subvalue }}</td> 
													<td class="text-left">-</td>
													<td class="text-right">1</td>
													<td class="text-right"><?php $totalBilled = $totalBilled + $Plist['cpt_billed_amt'][$subkey];  ?>  {!! App\Http\Helpers\Helpers::priceFormat($Plist['cpt_billed_amt'][$subkey]) !!}</td>
													<td class="text-right"><?php $totalAllowed = $totalAllowed + $Plist['cpt_allowed_amt'][$subkey]; ?>{!! App\Http\Helpers\Helpers::priceFormat($Plist['cpt_allowed_amt'][$subkey]) !!} </td>
													<td class="text-right"><?php $totalCo_Ins = $totalCo_Ins + $Plist['co_ins'][$subkey]; ?>{!! App\Http\Helpers\Helpers::priceFormat($Plist['co_ins'][$subkey]) !!} </td>
													<td class="text-right"><?php $totalCo_Pay = $totalCo_Pay + $Plist['co_pay'][$subkey]; ?>{!! App\Http\Helpers\Helpers::priceFormat($Plist['co_pay'][$subkey]) !!} </td>
													<td class="text-right"><?php $totalDebuctable = $totalDebuctable + $Plist['deductable'][$subkey]; ?>{!! App\Http\Helpers\Helpers::priceFormat($Plist['deductable'][$subkey]) !!} </td>
													<td class="text-right"><?php $totalAdj = $totalAdj + array_sum($Plist['adj_reson_amount'][$subkey]); ?>{!! App\Http\Helpers\Helpers::priceFormat(array_sum($Plist['adj_reson_amount'][$subkey])) !!} </td>
													<td class="text-right"><?php $totalPaid = $totalPaid + $Plist['paid_amt'][$subkey];  ?> {!! App\Http\Helpers\Helpers::priceFormat($Plist['paid_amt'][$subkey]) !!}</td>
												</tr>
												@endforeach
                                            @endif
                                            <!-- Claim Total Row -->
                                            <tr>                              
                                                <td class="text-right"></td>     
                                                <td class="text-right"></td> 
                                                <td class="text-right"></td> 
                                                <td style="background: #f5fffe;border-radius: 20px 0px 0px 20px" class="text-right"><label for="total" class="med-green font600 no-bottom">Total</label></td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalBilled) !!}
                                                </td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalAllowed)!!}
                                                </td>							
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalCo_Ins) !!} </td>

                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalCo_Pay) !!} </td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalDebuctable) !!} </td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalAdj) !!} </td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalPaid) !!} </td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php $count++; ?>
                            @endforeach

                            @else
                            <p>No Posted Claim Found.</p>
                            @endif
                        </div>


                    </div><!-- /.box Ends-->
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-m-15 margin-t-m-10"><!--  Left side Content Starts -->
                <div class="box box-view no-shadow yes-border"><!--  Box Starts -->
                    <div class="box-header-view">
					@if($pageType == 'Era Status Popup')
                        <i class="livicon" data-name="users-add" ></i> <h3 class="box-title">Error Claims</h3>
					@else
						<i class="livicon" data-name="users-add"></i> <h3 class="box-title">UnPosted Claims</h3>
					@endif
                        
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool js-collapse" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.minus')}}"></i></button>
                        </div>
                    </div><!-- /.box-header -->                                 
                    <div class="box-body js-collpased no-padding form-horizontal margin-t-10">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                            <?php $count = 5500; ?>
                            @if(!empty($list['unposted']))
                            @foreach($list['unposted'] as $key => $Ulist)  
							<?php 	if(isset($Ulist['claim_id']) && empty($Ulist['claim_id']))
										$chargeUrl = '#'; 
									else
										$chargeUrl = '/charges/'.@$Ulist['claim_id'].'/charge_edit'; 
										
									if(isset($Ulist['patient_id']) && empty($Ulist['patient_id']))
										$ledgerUrl = '#';
									else
										$ledgerUrl = 'patients/'.@$Ulist['patient_id'].'/ledger';
							?>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border yes-border margin-t-10">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                    <span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600">
									@if($chargeUrl != '#')
										<a href="{{ url($chargeUrl) }}" target="_blank">Claim No: {{ @$Ulist['claim_no'] }}</a>
									@else
										Claim No: {{ @$Ulist['claim_no'] }}
									@endif
									</span>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                        <label for="act no" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Acc No</label>
										@if($ledgerUrl != '#')
											<a href="{{ url($ledgerUrl) }}" target="_blank">{{ @$Ulist['patient_acct'] }}</a>
										@else
											{{ @$Ulist['patient_acct'] }}
										@endif
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                        <label for="name" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Patient Name</label>
                                        @if($ledgerUrl != '#')
											<a href="{{ url($ledgerUrl) }}" target="_blank">{{ @$Ulist['patient_name'] }}</a>
										@else
											{{ @$Ulist['patient_name'] }}
										@endif
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                        <label for="name" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Patient Policy ID</label>
                                        {{ @$Ulist['pat_policyId'] }}
                                    </div>
                                    <!--

                                    -->
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive hide">
                                        <label for="name" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Billing</label>
                                        Fisher, Daniel P
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive hide">
                                        <label for="rendering" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Rendering</label>
                                        Fisher, Daniel P
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive hide">
                                        <label for="name" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Facility</label>
                                        Daniel P Fisher Psy D
                                    </div>                                                       
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive hide">
                                        <label for="name" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Responsibility</label>
                                        Self
                                        <!--                                                                        Blue Cross Blue Shield IL
                                        -->
                                    </div>                                
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive hide">
                                        <label for="user_lbl" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">User</label>
                                        MED
                                    </div>                                
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive hide">
                                        <label for="entrydate_lbl" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Entry date</label>
                                        <span class="bg-date">	08/10/18 </span>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                        <label for="entrydate_lbl" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Resp</label>
                                        {{ @$Ulist['resp'] }}
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                        <label for="user_lbl" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Status</label>
                                        {{ @$Ulist['status'] }}
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive hide">
                                        <label for="user_lbl" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Insurance Type</label>
                                        Bcbs
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive hide">
                                        <label for="user_lbl" class="col-lg-5 col-md-5 col-sm-6 col-xs-6 med-green font600">Reference</label>	
                                    </div>
                                </div> 
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <table class="popup-table-wo-border table table-responsive" style="margin-bottom: 5px; margin-top: 5px;">                    
                                        <thead>
                                            <!-- Claim Header -->
                                            <tr>
                                                <th style="background: #d9f3f0; color: #00877f;">DOS </th>
                                                <th style="background: #d9f3f0; color: #00877f;">CPT</th>
                                                <th style="background: #d9f3f0; color: #00877f;">Modifiers</th>
                                                <th class="text-right" style="background: #d9f3f0; color: #00877f;">Units</th>
                                                <th class="text-right" style="background: #d9f3f0; color: #00877f;">Charges</th>
                                                <th class="text-right" style="background: #d9f3f0; color: #00877f;">Allowed</th>
                                                <th class="text-right" style="background: #d9f3f0; color: #00877f;">Co-Ins</th>
                                                <th class="text-right" style="background: #d9f3f0; color: #00877f;">Co-Pay</th>
                                                <th class="text-right" style="background: #d9f3f0; color: #00877f;">Deductible</th>
                                                <th class="text-right" style="background: #d9f3f0; color: #00877f;">Adj</th>
                                                <th class="text-right" style="background: #d9f3f0; color: #00877f;">Paid</th>
                                            </tr>
                                        </thead>                
                                        <tbody>
                                            <?php $totalBilled = $totalAllowed = $totalCo_Ins = $totalCo_Pay = $totalDebuctable = $totalAdj = $totalPaid = 0;  ?>
                                            @if(isset($Ulist['cpt']))
                                            @foreach($Ulist['cpt'] as $subkey => $subvalue)
                                            <tr>                              
                                                <td>{{ date('m/d/y',strtotime($Ulist['dos_from'][$subkey])) }}</td>  
                                                <td>{{ $subvalue }}</td> 
                                                <td class="text-left">-</td>
                                                <td class="text-right">1</td>
                                                <td class="text-right"><?php $totalBilled = $totalBilled + $Ulist['cpt_billed_amt'][$subkey];  ?>  {!! App\Http\Helpers\Helpers::priceFormat($Ulist['cpt_billed_amt'][$subkey]) !!}</td>
                                                <td class="text-right"><?php $totalAllowed = $totalAllowed + $Ulist['cpt_allowed_amt'][$subkey]; ?>{!! App\Http\Helpers\Helpers::priceFormat($Ulist['cpt_allowed_amt'][$subkey]) !!} </td>
                                                <td class="text-right"><?php $totalCo_Ins = $totalCo_Ins + $Ulist['co_ins'][$subkey]; ?>{!! App\Http\Helpers\Helpers::priceFormat($Ulist['co_ins'][$subkey]) !!} </td>
                                                <td class="text-right"><?php $totalCo_Pay = $totalCo_Pay + $Ulist['co_pay'][$subkey]; ?>{!! App\Http\Helpers\Helpers::priceFormat($Ulist['co_pay'][$subkey]) !!} </td>
                                                <td class="text-right"><?php $totalDebuctable = $totalDebuctable + $Ulist['deductable'][$subkey]; ?>{!! App\Http\Helpers\Helpers::priceFormat($Ulist['deductable'][$subkey]) !!} </td>
                                                <td class="text-right"><?php $totalAdj = $totalAdj + array_sum($Ulist['adj_reson_amount'][$subkey]); ?>{!! App\Http\Helpers\Helpers::priceFormat(array_sum($Ulist['adj_reson_amount'][$subkey])) !!} </td>
                                                <td class="text-right"><?php $totalPaid = $totalPaid + $Ulist['paid_amt'][$subkey];  ?> {!! App\Http\Helpers\Helpers::priceFormat($Ulist['paid_amt'][$subkey]) !!}</td>											

                                            </tr>
                                            @endforeach
                                            @endif
                                            <!-- Claim Total Row -->
                                            <tr>                              
                                                <td class="text-right"></td>     
                                                <td class="text-right"></td> 
                                                <td class="text-right"></td> 
                                                <td style="background: #f5fffe;border-radius: 20px 0px 0px 20px" class="text-right"><label for="total" class="med-green font600 no-bottom">Total</label></td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalBilled) !!}
                                                </td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalAllowed)!!}
                                                </td>							
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalCo_Ins) !!} </td>

                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalCo_Pay) !!} </td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalDebuctable) !!} </td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalAdj) !!} </td>
                                                <td style="background: #f5fffe" class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($totalPaid) !!} </td>

                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                            @foreach($Ulist['error'][0] as $errorlist)
                            <span class="med-orange"><i>* {{ $errorlist }}</i> </span> </br>
                            @endforeach
                            <?php $count++; ?>
                            @endforeach
                            @else
                            No Unposted Claim Found..
                            @endif


                        </div>


                    </div><!-- /.box Ends-->
                </div>
            </div>
        </div>
    </div>
</div>

@endforeach