<?php $heading_name = App\Models\Practice::getPracticeName(); ?>

<div class="box box-view no-shadow"><!--  Box Starts -->        

    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>

<div class="box-body  bg-white no-padding"><!-- Box Body Starts -->        
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3 class="text-center reports-heading p-l-2 margin-t-10 margin-b-25 med-orange">Year End Financials</h3>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-5 text-center">               
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




    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-info no-shadow no-border no-bottom margin-t-1">
            <div class="box-body">
                <div class="table-responsive mobile-lg-scroll">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            
                            <tr>
                                <th rowspan="2">Month</th>
                                <th rowspan="2" class="text-right" style="border-right:1px solid #fff !important;">Claims</th>
                                <th rowspan="2" class="text-right" style="border-right:1px solid #fff !important;">Charges($)</th>
                                <th colspan="3" class="text-center" style="border-width:0px 0px 1px 0px !important;border-color: #fff !important;">Adjustments($)</th>
                                <th colspan="3" class="text-center" style="border-width:0px 0px 1px 1px !important;border-color: #fff !important;border-bottom-color: #CDF7FC !important;">Refunds($)</th>
                                <th colspan="3" class="text-center" style="border-width:0px 0px 1px 1px !important;border-color: #fff !important;border-bottom-color: #CDF7FC !important;">Payments($)</th>
                                <th colspan="3" class="text-center" style="border-width:0px 0px 1px 1px !important;border-color: #fff !important;border-bottom-color: #CDF7FC !important;">AR Bal($)</th>   
                            </tr>
                            <tr class="p-r-0">
                                <th class="text-right">Patient</th>
                                <th class="text-right">Insurance</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Patient</th>
                                <th class="text-right">Insurance</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Patient</th>
                                <th class="text-right">Insurance</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Patient</th>
                                <th class="text-right">Insurance</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>                
                        <tbody>
                            <?php 
								 $count = 1;   
								 $last_visit = [];
							 ?>  
                            <?php  
								$charges = 0;
								$total_adjustments = 0;
								$patient_payments = 0;
								$insurance_payments = 0;
								$patient_ar_due = 0;
								$insurance_ar_due = 0;
								$total_patient_adj =0;
								$total_ins_adj = $total_ref_patient = $total_ref_ins =0;
                                $claims_count = 0;
								?> 
                            @if(!empty($claims))

                            @foreach($claims as $key=>$claim_list) 							
                            <?php
								$ins_adj =$claim_list->insurance_adj;
                                $claims_count += $claim_list->claims_visits;
							?>
                            <tr class ="text-right default-cursor">
                                <td class="text-left"> {{ $key }}-{{$claim_list->year_key}}</td>
                                <td class="text-left"> {!! $claim_list->claims_visits !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat($claim_list->value) !!} </td>                                
                                <td> {!! App\Http\Helpers\Helpers::priceFormat($claim_list->patient_adjusted) !!}</td>
                                <td>{!! App\Http\Helpers\Helpers::priceFormat(@$ins_adj) !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat(@$claim_list->total_adjusted) !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat(@$claim_list->patient_refund) !!} </td> 
                                <td> {!! App\Http\Helpers\Helpers::priceFormat(-($claim_list->ins_refund)) !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat((-($claim_list->ins_refund)) + (@$claim_list->patient_refund)) !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat($claim_list->patient_payment) !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat($claim_list->insurance_payment) !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat(($claim_list->insurance_payment)+($claim_list->patient_payment)) !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat($claim_list->patient_due) !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat($claim_list->insurance_due) !!} </td>
                                <td> {!! App\Http\Helpers\Helpers::priceFormat($claim_list->insurance_due + $claim_list->patient_due) !!} </td>

                            </tr>
                            <?php $count++;  
                                $charges += $claim_list->value;                                 
								$total_adjustments += $claim_list->total_adjusted;
								$total_patient_adj += $claim_list->patient_adjusted;
								$total_ins_adj += $ins_adj;
								$patient_payments += $claim_list->patient_payment;
								$total_ref_patient += $claim_list->patient_refund;
								$total_ref_ins += $claim_list->ins_refund;
								$insurance_payments += $claim_list->insurance_payment;
								$patient_ar_due += $claim_list->patient_due;
								$insurance_ar_due += $claim_list->insurance_due;								
							?> 
                              <?php ?>
                            @endforeach 
                          
                            @endif
                            <tr style="background:#00837C;color:#fff !important;">
                                <th>Total</th>
                                <th class="text-left">{!! $claims_count !!}</th>
                                <th class ="text-right">${!! App\Http\Helpers\Helpers::priceFormat($charges) !!}</th>
                                <th class ="text-right">${!! App\Http\Helpers\Helpers::priceFormat($total_patient_adj) !!}</th>
                                <th class ="text-right">${!! App\Http\Helpers\Helpers::priceFormat($total_ins_adj) !!}</th>
                                <th class ="text-right">${!! App\Http\Helpers\Helpers::priceFormat($total_adjustments) !!}</th>
                                <th class ="text-right">${!!App\Http\Helpers\Helpers::priceFormat($total_ref_patient)!!}</th>
                                <th class ="text-right">${!!App\Http\Helpers\Helpers::priceFormat(-($total_ref_ins))!!}</th>
                                <th class ="text-right">${!!App\Http\Helpers\Helpers::priceFormat((-($total_ref_ins)) + (($total_ref_patient)))!!}</th>
                                <th class ="text-right">${!!App\Http\Helpers\Helpers::priceFormat($patient_payments)!!}</th>
                                <th class ="text-right">${!!App\Http\Helpers\Helpers::priceFormat($insurance_payments)!!}</th>
                                <th class ="text-right">${!!App\Http\Helpers\Helpers::priceFormat($insurance_payments+$patient_payments) !!}</th>
                                <th class ="text-right">${!!App\Http\Helpers\Helpers::priceFormat($patient_ar_due)!!}</th>
                                <th class ="text-right">${!!App\Http\Helpers\Helpers::priceFormat($insurance_ar_due)!!}</th>
                                <th class ="text-right">${!!App\Http\Helpers\Helpers::priceFormat(($insurance_ar_due+$patient_ar_due))!!}</th>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.box -->
    </div>
</div><!-- /.box Ends-->
</div>
