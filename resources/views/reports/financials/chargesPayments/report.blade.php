<style type="text/css">
    td{
        padding: 0 5px;
    }
</style>
<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body bg-white border-radius-4"><!-- Box Body Starts -->
        @if($header !='' && count($header)>0)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange" >
                <div class="margin-b-15">Charges & Payments Summary</div>
            </h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding text-center">
                    </script> 
					<?php $i=1; ?>
						@foreach($header as $header_name => $header_val)
							<span class="med-green">
								<?php $hn = $header_name; ?>
								{{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i<count((array)$header)) | @endif </script> 
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
        @if((isset($charges) && count($charges)>0) || (isset($payments) && count($payments)>0) || (isset($pmt_adj) && count($pmt_adj)>0))
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="box box-info no-shadow no-border no-bottom">
                <div class="box-body">
                    <?php $patient_total_payment = $insurance_total_payment = $total_billed = $patient_total_adj = $insurance_total_adj = 0; ?>
                    @if(isset($billingprov) && !empty($billingprov))
                    <table class="table table-striped table-bordered table-separate">
                        <thead>
                            <tr>
                                <th>Billing</th>
                                <th class="text-right">Total Charges($)</th>
                                <th class="text-right">Patient Adjustments($)</th>
                                <th class="text-right">Insurance Adjustments($)</th>
                                <th class="text-right">Total Adjustments($)</th>
                                <th class="text-right">Patient Payments($)</th>
                                <th class="text-right">Insurance Payments($)</th>
                                <th class="text-right">Total Payments($)</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($billingprov as $val)
                        <?php
							$provider_name = str_replace(',','',$val->provider_name);
							$key = str_replace(' ','_',($provider_name));
                            $billed = isset($charges[$key])?$charges[$key]:0;
                            $pat_adj = isset($pmt_adj[$key]['Patient'])?$pmt_adj[$key]['Patient']:0;
							
							if($payerType == 'insurance')
								$pat_adj = 0;
							
                            $ins_adj = isset($pmt_adj[$key]['Insurance'])?$pmt_adj[$key]['Insurance']:0;
							
							if($payerType == 'self')
								$ins_adj = 0;
							
                            $pat_pmt = isset($payments[$key]['Patient'])?$payments[$key]['Patient']:0;
							if($payerType == 'insurance')
								$pat_pmt = 0;
                            $ins_pmt = isset($payments[$key]['Insurance'])?$payments[$key]['Insurance']:0;
							if($payerType == 'self')
								$ins_pmt = 0;
                            $tot_adj = $pat_adj+$ins_adj;
                            $tot_pmt = $pat_pmt+$ins_pmt;
                            $total_billed += $billed;
                            $patient_total_payment += $pat_pmt;
                            $insurance_total_payment += $ins_pmt;
                            $patient_total_adj += $pat_adj;
                            $insurance_total_adj += $ins_adj;
                        ?>
                        @if($billed || $pat_adj || $ins_adj || $pat_pmt || $ins_pmt!=0)
                        <tr>
                            <td>{{str_replace('_', ' ', $key)}}</td>
                            <td class="text-right">{{App\Http\Helpers\Helpers::priceFormat($billed)}}</td>
                            <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($pat_adj)!!}</td>
                            <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($ins_adj)!!}</td>
                            <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($tot_adj)!!}</td>
                            <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($pat_pmt)!!}</td>
                            <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($ins_pmt)!!}</td>
                            <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($tot_pmt)!!}</td>
                        </tr>
                        @endif
                    @endforeach
                        
                        <tr >
                            <td class="font600 med-orange">Totals($)</td>
                            <td class="text-right med-green font600">{{App\Http\Helpers\Helpers::priceFormat(array_sum((array)$charges))}}</td>
                            <td class="text-right med-green font600">{!!App\Http\Helpers\Helpers::priceFormat($patient_total_adj)!!}</td>
                            <td class="text-right med-green font600">{!!App\Http\Helpers\Helpers::priceFormat($insurance_total_adj)!!}</td>
                            <td class="text-right med-green font600">{!!App\Http\Helpers\Helpers::priceFormat($patient_total_adj+$insurance_total_adj)!!}</td>
                            <td class="text-right med-green font600">{!!App\Http\Helpers\Helpers::priceFormat($patient_total_payment)!!}</td>
                            <td class="text-right med-green font600">{!!App\Http\Helpers\Helpers::priceFormat($insurance_total_payment)!!}</td>
                            <td class="text-right med-green font600">{!!App\Http\Helpers\Helpers::priceFormat($patient_total_payment+$insurance_total_payment)!!}</td>
                        </tr>
                    </tbody>
                    </table>
                    @endif
                    <?php
                        $wallet = isset($payments['wallet'])?$payments['wallet']:0;
                        if($wallet<0)
                            $wallet = 0;
					?>
                    @if($total_billed || $patient_total_payment || $insurance_total_payment || $patient_total_adj || $insurance_total_adj!=0)
                    <div style="margin-top: 15px;">
                        <table>
                            <td class="font600">Wallet Balance : </td>
                            <td class='text-right med-orange font600'>${{App\Http\Helpers\Helpers::priceFormat($wallet)}}</td>
                        </table>
                    </div>
                    @endif
                    @if($total_billed || $patient_total_payment || $insurance_total_payment || $patient_total_adj || $insurance_total_adj!=0)
                    <div class="table-responsive hide">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 no-padding">
                                <div class="box-header-view-white no-border-radius pr-t-5 margin-b-5">
                                    <i class="fa fa-bars"></i><strong class="med-orange font13">Summary </strong>                     
                                </div><!-- /.box-header -->
                                <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4" style="border: 1px solid #00877f;">   
                                    <thead>
                                        <th></th>
                                        <th class="text-right">Value</th>
                                    </thead> 
                                    <tbody>
                                            <tr>
                                                <td>Total Charges</td>
                                                <td class='text-right med-green font600'>${{App\Http\Helpers\Helpers::priceFormat($total_billed)}}</td>
                                            </tr>
                                            <?php
												$wallet = isset($payments['wallet'])?$payments['wallet']:0;
												if($wallet<0)
													$wallet = 0;
												$tot_adj = $patient_total_adj+$insurance_total_adj;
												$tot_pmt = $patient_total_payment+$insurance_total_payment+$wallet;
                                            ?>
                                            <tr>
                                                <td>Wallet Balance</td>
                                                <td class='text-right med-green font600'>${{App\Http\Helpers\Helpers::priceFormat($wallet)}}</td>
                                            </tr>
                                            <tr> 
                                                <td>Patient Adjustments</td>
                                                <td class="text-right med-green font600">${!!App\Http\Helpers\Helpers::priceFormat($patient_total_adj)!!}</td>
                                            </tr>
                                            <tr>
                                                <td>Insurance Adjustments</td>
                                                <td class="text-right med-green font600">${!!App\Http\Helpers\Helpers::priceFormat($insurance_total_adj)!!}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 15px">Total Adjustments</td>
                                                <td class="text-right med-green font600">${!!App\Http\Helpers\Helpers::priceFormat($tot_adj)!!}</td>
                                            </tr>
                                            <tr> 
                                                <td>Patient Payments</td>
                                                <td class="text-right med-green font600">${!!App\Http\Helpers\Helpers::priceFormat($patient_total_payment+$wallet)!!}</td>
                                            </tr>
                                            <tr>
                                                <td>Insurance Payments</td>
                                                <td class="text-right med-green font600">${!!App\Http\Helpers\Helpers::priceFormat($insurance_total_payment)!!}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 15px">Total Payments</td>
                                                <td class="text-right med-green font600">${!!App\Http\Helpers\Helpers::priceFormat($tot_pmt)!!}</td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    @else
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center hide"><h5 class="text-gray"><i>No Records Found</i></h5></div>
                    @endif
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->