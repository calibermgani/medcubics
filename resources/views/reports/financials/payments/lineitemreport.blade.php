<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-money" data-name="info"></i> <h3 class="box-title">Payment Analysis – Detailed Report</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body"><!-- Box Body Starts -->
        <?php $i=1; ?>
        @if(isset($header) && $header !='')
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Payment Analysis – Detailed Report</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
                @foreach($header as $header_name => $header_val)
                <span class="med-green">{{ @$header_name }}</span> : {{ @$header_val }} @if($i < count((array)$header)) | @endif
                    <?php $i++; ?>
                @endforeach
            </div>
        </div>
        @endif
        @if(isset($payments) && $payments !='')
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
            <div class="box box-info no-shadow no-bottom no-border">
                <div class="box-body">
                    <div class="table-responsive">
					
                         <?php $count = 1;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; $claim_bal_total = 0;   ?>
                        @foreach($payments as $payments_list)
                        @if($payments_list->total_paid > 0 && $payments_list->pmt_method=='Patient' || $payments_list->pmt_method=='Insurance')
                        <?php
                            if ($header->Payer == "Insurance Only") {
                                $claim = @$payments_list;
                                $patient = @$payments_list;
                                $check_details = @$payments_list;
                                $eft_details = @$payments_list;
                                $creditCardDetails = @$payments_list;
                                $payment_info = $payments_list;
                                $set_title = (@$patient->title) ? @$patient->title . ". " : '';
                                $patient_name = $set_title . "" . App\Http\Helpers\Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
                            }else{
                                $patient = @$payments_list->claim_patient_det;
                                $claim = $payments_list->claim;
                                $check_details = $payments_list->pmt_info->check_details;
                                $eft_details = $payments_list->pmt_info->eft_details;
                                $creditCardDetails = $payments_list->pmt_info->credit_card_details;
                                $set_title = (@$patient->title)? @$patient->title.". ":'';
                                $patient_name = $set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name); 
                            }
						?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border yes-border margin-t-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600">Claim No: {{ $claim->claim_number }}</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Acc No</label>
                                    {{ @$patient->account_no }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Patient Name</label>
                                    {{ $patient_name }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Payer</label>
                                    @if($payments_list->payer_insurance_id==0)
                                    Self
                                    @else             
                                    <?php $insurance_name = App\Http\Helpers\Helpers::getInsuranceName($payments_list->payer_insurance_id);?>
                                    {{ @$insurance_name }}
                                    @endif 
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Billing </label>
                                    @if ($header->Payer == "Insurance Only")
                                    {{ isset($claim->billing_short_name)?$claim->billing_short_name:'-Nil-' }}
                                    @else
                                    {{ isset($claim->billing_provider->short_name)?$claim->billing_provider->short_name:'-Nil-' }}
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Rendering</label>
                                    @if ($header->Payer == "Insurance Only")
                                    {{ isset($claim->rendering_short_name)?$claim->rendering_short_name:'-Nil-' }}
                                    @else
                                    {{ isset($claim->rendering_provider->short_name)?$claim->rendering_provider->short_name:'-Nil-' }}
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Facility</label>
                                    @if ($header->Payer == "Insurance Only")
                                    {{ isset($claim->facility_short_name)?$claim->facility_short_name:'-Nil-' }}
                                    @else
                                    {{ isset($claim->facility_detail->short_name)?$claim->facility_detail->short_name:'-Nil-' }}
                                    @endif
                                </div>
								
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Payment Type</label>
                                    {{ @$payments_list->pmt_mode }}
                                </div>
                                @if($payments_list->pmt_mode =='Check')
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Check Number</label>
                                    {{ @$check_details->check_no }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Check Date</label>
                                    @if(empty($check_details->check_date))
                                    -Nil-
                                    @else                               
                                    {{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }} 
                                    @endif 
                                </div>
                                @elseif($payments_list->pmt_mode =='Money Order')
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">MO Number</label>
                                    @if(!empty(@$check_details->check_no) || @$check_details->check_no==0)
                                        <?php $exp=explode("MO-", @$check_details->check_no); echo $exp[1];?>
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">MO Date</label>
                                    @if(empty($check_details->check_date))
                                    -Nil-
                                    @else                               
                                    {{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }} 
                                    @endif 
                                </div>
                                @elseif($payments_list->pmt_mode =='EFT')
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">EFT No</label>
                                    @if(empty($eft_details->eft_no)  || @$eft_details->eft_no==0)
                                    -Nil-
                                    @else                               
                                    {{ $eft_details->eft_no }} 
                                    @endif 
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">EFT Date</label>
                                    @if(empty($eft_details->eft_date))
                                    -Nil-
                                    @else                               
                                    {{ App\Http\Helpers\Helpers::dateFormat($eft_details->eft_date) }} 
                                    @endif 

                                </div>
                                @elseif($payments_list->pmt_mode =='Credit')
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Card Type</label>
                                    @if(empty($creditCardDetails->card_type))
                                    -Nil-
                                    @else                               
                                    {{ @$creditCardDetails->card_type }} 
                                    @endif 
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Card Number</label>
                                    @if(@$creditCardDetails->card_last_4 == '')
                                    -Nil-
                                    @else                               
                                    {{ @$creditCardDetails->card_last_4 }} 
                                    @endif 
                                </div>
                                @endif
                                @if($payments_list->pmt_mode =='Cash')
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Responsibility</label>
                                    @if($payments_list->payer_insurance_id==0 || $payments_list->source=='charge')
                                    Self
                                    @else    
                                        <?php $insurance_name = App\Http\Helpers\Helpers::getInsuranceName($claim->insurance_id);?>
                                    {{ @$insurance_name }}
                                    @endif 
                                </div>
                                @endif 
                                @if(@$column->trans_date =='')
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Transaction Date</label>{{ App\Http\Helpers\Helpers::timezone($payments_list->created_at, 'm/d/y') }} 
                                </div>
                                @endif
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Reference </label>
                                    @if(@$payments_list->reference =='') -Nil- @else {{ @$payments_list->reference }} @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">User</label>{{ App\Http\Helpers\Helpers::shortname($payments_list->created_by) }}
                                </div>
                            </div> 
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <table class="popup-table-wo-border table table-responsive">                    
                                    <thead>
                                        <tr>
                                            <th class="light-th-bg">DOS</th>
                                            <th class="light-th-bg">CPT</th>
                                            <th class="light-th-bg">Billed Amt($)</th>                    
                                            <th class="light-th-bg">Allowed Amt($)</th>                      
                                            <th class="light-th-bg">W/O($)</th>
                                            <th class="light-th-bg">Ded($)</th>
                                            <th class="light-th-bg">Co-Pay($)</th>
                                            <th class="light-th-bg">Co-Ins($)</th>
                                            <th class="light-th-bg">Other Adjustment($)</th>
                                            <th class="light-th-bg">Paid($)</th>
                                            @if(@$column->ins_over_pay =='1')<th class="text-right">Ins. Overpayment($)</th> @endif
                                        </tr>
                                    </thead>                
                                    <tbody> 
                                        <?php 
											// @todo check and replace new pmt flow
											$trans_details = [];//App\Models\Payments\PaymentClaimCtpDetail::getClaimCptDetail(@$claim->id);
                                            $cpt = \DB::table('pmt_claim_cpt_tx_v1')->selectRaw('pmt_claim_cpt_tx_v1.allowed, pmt_claim_cpt_tx_v1.writeoff, pmt_claim_cpt_tx_v1.deduction, pmt_claim_cpt_tx_v1.copay, pmt_claim_cpt_tx_v1.coins, pmt_claim_cpt_tx_v1.withheld, pmt_claim_cpt_tx_v1.paid, claim_cpt_info_v1.cpt_code')->leftJoin('claim_cpt_info_v1','claim_cpt_info_v1.claim_id','pmt_claim_cpt_tx_v1.claim_id')->where('pmt_claim_cpt_tx_v1.pmt_claim_tx_id','=',$payments_list->id)->groupBy('pmt_claim_cpt_tx_v1.id')->get();
										?>
                                        @if(isset($cpt) && !empty($cpt))
                                        @foreach($cpt as $trans_cpt_details)
                                        <tr>                              
                                            <td>{{ date('m/d/Y',strtotime(@$claim->date_of_service)) }}</td>
                                            <td>{{ @$trans_cpt_details->cpt_code }}</td> 
                                            <td>{!! App\Http\Helpers\Helpers::priceFormat(@$claim->total_charge) !!}</td>
                                            <td>{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->allowed) !!}</td>
                                            <td>{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->writeoff) !!}</td>
                                            <td>{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->deduction) !!}</td>
                                            <td>{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->copay) !!}</td>
                                            <td>{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->coins) !!}</td>
                                            <td>{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->withheld) !!}</td>
											<?php	
												$adj = @$trans_cpt_details->adjustment+@$trans_cpt_details->with_held; 
												$trans_amt = @$trans_cpt_details->co_pay+@$trans_cpt_details->co_ins+@$trans_cpt_details->deductable; 
												$claim_paid_total += @$trans_cpt_details->patient_paid+@$trans_cpt_details->insurance_paid; 
                                                $bal = @$payments_list->fin[0]->patient_due+@$payments_list->fin[0]->insurance_due;
											?>	
                                            <td>{!! App\Http\Helpers\Helpers::priceFormat(@$trans_cpt_details->paid) !!}</td>
											<?php
												$total_amt_bal      += @$bal;
                                                $claim_bal_total    += @$bal;
                                                $count_cpt += 1;
												$overpay = ($bal > 0) ? "0.00" : $bal;
                                            ?>                                           
                                            @if(@$column->ins_over_pay =='1')<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$overpay) !!}</td>@endif
                                        </tr>
                                        @endforeach  
                                        @endif 
                                        <?php $claim_bal_total = 0; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php $count++;   ?> 
                        @endif
                        @endforeach                          

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
                        </div>

                          
                        @if($header->Payer!="Patient Payments – Detailed Transaction")
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"  style="border-top: 1px solid #f0f0f0;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                    <div class="box-header-view-white no-border-radius pr-t-5 margin-b-10">
                        <i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>                     
                    </div>
                    <!-- /.box-header -->
                    
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 margin-b-10">
                    <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4 summary-box" style="">    
                        <tbody>
                            @if(isset($header) && $header !='')
                            @foreach($header as $header_name => $header_val)
                            @if($header_name=='Transaction Date')
                            <tr>
                                <td>Transaction Date</td>
                                <td class="med-green font600 text-right">{{ @$header_val }}</td>
                            </tr>
                            @endif
                            @if($header_name=='DOS Date')
                            <tr>
                                <td>DOS</td>
                                <td class="med-green font600 text-right">{{ @$header_val }}</td>
                            </tr>
                            @endif
                            @endforeach
                            @endif
                            @if($header->Payer!="Insurance Only")
                            <tr>
                                <td>Total Patient Payments</td>
                                <td class="@if($dataArr->patPmt>0)med-green @endif font600 text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->patPmt) !!}</td>
                            </tr>
                            
                           
                            @endif
                            @if($header->Payer=="Insurance Only")
                            <tr>
                                <td>Total Insurance Payments</td>
                                <td class="@if($dataArr->insPmt>0)med-green @endif font600 text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->insPmt) !!}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    </div>
                    <table class="table table-separate table-borderless pr-r-m-20 table-separate border-radius-4">    
                        <tbody>                                                

                            <tr>                            
                                @if($header->Payer!="Patient Payments")
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->wrtOff) !!}</h3>
                                    <h4 class="text-center"><i>Write-Off</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->other) !!}</h3>
                                    <h4 class="text-center"><i>Other Adjustments</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->deduction) !!}</h3>
                                    <h4 class="text-center"><i>Deductible</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->copay) !!}</h3>
                                    <h4 class="text-center"><i>Co-Pay</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->coins) !!}</h3>
                                    <h4 class="text-center"><i>Co-Insurance</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->eft) !!}</h3>
                                    <h4 class="text-center"><i>EFT Payments</i></h4>
                                </td>
                                @endif

                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center dashboard-number">$ {!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->check) !!}</h3>
                                    <h4 class="text-center"><i>Check Payments</i></h4>
                                </td>

                                @if($header->Payer!="Insurance Only")
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center dashboard-number">$ {!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->cash) !!}</h3>
                                    <h4 class="text-center"><i>Cash Payments</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center dashboard-number">$ {!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->mo) !!}</h3>
                                    <h4 class="text-center"><i>MO Payments</i></h4>
                                </td>
                                @endif

                                <td style="border-right: 0px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center dashboard-number">$ {!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->cc) !!}</h3>
                                    <h4 class="text-center"><i>CC Payments</i></h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>  
            @endif                        
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->