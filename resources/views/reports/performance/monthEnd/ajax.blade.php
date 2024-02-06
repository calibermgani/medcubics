<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" id="js_ajax_part">
    <div class="box box-view no-shadow">
        <div class="box-header-view">
            <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
            <div class="pull-right">
                <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
            </div>
        </div>

        <div class="box-body bg-white border-radius-4">

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange" >
					<div class="margin-b-15">Month End Performance Summary Report</div>

				</h3>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">
						<?php $i=1; ?>
							@if(isset($searchBy) && !empty($searchBy))
							@foreach($searchBy as $header_name => $header_val)
								<span class="med-green">
									{{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i< count((array)$searchBy)) | @endif 
									<?php $i++; ?>
							@endforeach
							@endif
					 </div> 
				</div>
			</div>
				
			@if(!empty($FacilityWiseOutstanding)  || !empty($insuranceClaimsByFacility) || !empty($facilityStatus))    
			<?php /*<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0 result_data">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-5 margin-t-10 ">
					<h4 class="med-darkgray no-bottom margin-t-5"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Outstanding AR - By Location</h4>
				</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<div class="box-body table-responsive p-t-0 no-padding">
						<table class="popup-table-border  table-separate table m-b-m-1">
							<thead>
								<tr>
									<th class="text-center" style="border-right: 1px solid #fff ">Facility</th>
									<th class="text-center" colspan="2" style="border-right: 1px solid #fff">Unbilled</th>
									<th class="text-center" colspan="2" style="border-right: 1px solid #fff">0-30</th>
									<th class="text-center" colspan="2" style="border-right: 1px solid #fff">31-60</th>
									<th class="text-center" colspan="2" style="border-right: 1px solid #fff">61-90</th>
									<th class="text-center" colspan="2" style="border-right: 1px solid #fff">91-120</th>
									<th class="text-center" colspan="2" style="border-right: 1px solid #fff">121-150</th>
									<th class="text-center" colspan="2" style="border-right: 1px solid #fff">>150</th>
									<th class="text-center" style=" ">Totals</th>
								</tr>
								<tr>
									<td class="font600 bg-white line-height-26 text-center" style="border-right: 1px solid #CDF7FC"><span class="med-green"></span></td>
									<td class="font600 text-center  line-height-26" style="border-right: 1px solid #a4ede9;background: #dbfaf8;">
										<span class="med-green"> Claims</span>
									</td>
									<td class="font600 text-center line-height-26" style="border-right: 1px solid #a4ede9;background: #dbfaf8;">
										<span class="med-green"> Value($)</span>
									</td>
									<td class="font600 text-center  line-height-26" style="border-right: 1px solid #a4ede9;background: #dbfaf8;">
										<span class="med-green"> Claims</span>
									</td>
									<td class="font600 text-center line-height-26" style="border-right: 1px solid #a4ede9;background: #dbfaf8;">
										<span class="med-green"> Value($)</span>
									</td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span></td>
									<td class="font600 text-center" style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value($)</span></td>
									<td class="font600 bg-white text-center"><span class="med-green"> </span></td>
								</tr>
							</thead>
							<tbody>
								<?php
									$claim_counts_unbilled = $claim_counts_0_30 = $claim_counts_31_60 = $claim_counts_61_90 = $claim_counts_91_120 = $claim_counts_121_150 = $claim_counts_150 = $tot_ar_unbilled = $tot_ar_0_30 = $tot_ar_31_60 = $tot_ar_61_90 = $tot_ar_91_120 = $tot_ar_121_150 = $tot_ar_150 = 0;
								?>
								@if(!empty($FacilityWiseOutstanding))
								@foreach($FacilityWiseOutstanding as $key=>$value)
								<tr>
									<td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-green"> {{$key}}</span></td>
									<?php
										$claim_count_unbilled = isset($value['Unbilled']['claim_count']) ? $value['Unbilled']['claim_count'] : 0;
										$claim_count_0_30 = isset($value['0-30']['claim_count']) ? $value['0-30']['claim_count'] : 0;
										$claim_count_31_60 = isset($value['31-60']['claim_count']) ? $value['31-60']['claim_count'] : 0;
										$claim_count_61_90 = isset($value['61-90']['claim_count']) ? $value['61-90']['claim_count'] : 0;
										$claim_count_91_120 = isset($value['91-120']['claim_count']) ? $value['91-120']['claim_count'] : 0;
										$claim_count_121_150 = isset($value['121-150']['claim_count']) ? $value['121-150']['claim_count'] : 0;
										$claim_count_150 = isset($value['>150']['claim_count']) ? $value['>150']['claim_count'] : 0;
										$total_ar_unbilled = isset($value['Unbilled']['total_ar']) ? $value['Unbilled']['total_ar'] : 0.00;
										$total_ar_0_30 = isset($value['0-30']['total_ar']) ? $value['0-30']['total_ar'] : 0.00;
										$total_ar_31_60 = isset($value['31-60']['total_ar']) ? $value['31-60']['total_ar'] : 0.00;
										$total_ar_61_90 = isset($value['61-90']['total_ar']) ? $value['61-90']['total_ar'] : 0.00;
										$total_ar_91_120 = isset($value['91-120']['total_ar']) ? $value['91-120']['total_ar'] : 0.00;
										$total_ar_121_150 = isset($value['121-150']['total_ar']) ? $value['121-150']['total_ar'] : 0.00;
										$total_ar_150 = isset($value['>150']['total_ar']) ? $value['>150']['total_ar'] : 0.00;
										$claim_counts_unbilled += $claim_count_unbilled;
										$claim_counts_0_30 += $claim_count_0_30;
										$claim_counts_31_60 += $claim_count_31_60;
										$claim_counts_61_90 += $claim_count_61_90;
										$claim_counts_91_120 += $claim_count_91_120;
										$claim_counts_121_150 += $claim_count_121_150;
										$claim_counts_150 += $claim_count_150;
										$tot_ar_unbilled += $total_ar_unbilled;
										$tot_ar_0_30 += $total_ar_0_30;
										$tot_ar_31_60 += $total_ar_31_60;
										$tot_ar_61_90 += $total_ar_61_90;
										$tot_ar_91_120 += $total_ar_91_120;
										$tot_ar_121_150 += $total_ar_121_150;
										$tot_ar_150 += $total_ar_150;
									?>
									<td class="text-center bg-white">{{$claim_count_unbilled}}</td>
									<td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_unbilled) !!}</td>
									<td class="text-center bg-white">{{$claim_count_0_30}}</td>
									<td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_0_30) !!}</td>
									<td class="text-center bg-white">{{$claim_count_31_60}}</td>
									<td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_31_60) !!}</td>
									<td class="text-center bg-white">{{$claim_count_61_90}}</td>
									<td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_61_90) !!}</td>
									<td class="text-center bg-white">{{$claim_count_91_120}}</td>
									<td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_91_120) !!}</td>
									<td class="text-center bg-white">{{$claim_count_121_150}}</td>
									<td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_121_150) !!}</td>
									<td class="text-center bg-white">{{$claim_count_150}}</td>
									<td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($total_ar_150) !!}</td>
									<td class="text-right bg-white" style="border-right: 1px solid #CDF7FC">{!!  \App\Http\Helpers\Helpers::priceFormat($total_ar_unbilled+$total_ar_0_30+$total_ar_31_60+$total_ar_61_90+$total_ar_91_120+$total_ar_121_150+$total_ar_150) !!}</td>
								</tr>
								@endforeach       
								<tr>
									<td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-orange"> Totals</span></td>
									<td class="font600 text-center bg-white med-orange">{{$claim_counts_unbilled}}</td>
									<td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_unbilled) !!}</td>
									<td class="font600 text-center bg-white med-orange">{{$claim_counts_0_30}}</td>
									<td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_0_30) !!}</td>
									<td class="font600 text-center bg-white med-orange">{{$claim_counts_31_60}}</td>
									<td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_31_60) !!}</td>
									<td class="font600 text-center bg-white med-orange">{{$claim_counts_61_90}}</td>
									<td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_61_90) !!}</td>
									<td class="font600 text-center bg-white med-orange">{{$claim_counts_91_120}}</td>
									<td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_91_120) !!}</td>
									<td class="font600 text-center bg-white med-orange">{{$claim_counts_121_150}}</td>
									<td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_121_150) !!}</td>
									<td class="font600 text-center bg-white med-orange">{{$claim_counts_150}}</td>
									<td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat($tot_ar_150) !!}</td>
									<td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!! \App\Http\Helpers\Helpers::priceFormat( $tot_ar_unbilled+$tot_ar_0_30+$tot_ar_31_60+$tot_ar_61_90+$tot_ar_91_120+$tot_ar_121_150+$tot_ar_150) !!}</td>                                                
								</tr>
								@endif
							</tbody>
						</table>
					</div><!-- /.box-body -->
				</div>
			</div>*/ ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
				<div class="box no-shadow"><!-- Primary Location Box Starts -->
					<div class="box-block-header with-border">
						<i class="fa fa-navicon" data-name="mail"></i> <h3 class="box-title"> Insurance Claims - Paid By Location</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div><!-- /.box-header -->
					<div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
						<?php $i = 0; ?>
						@if(!empty($insuranceClaimsByFacility))
							@foreach($insuranceClaimsByFacility as $key => $value)
								<h4 @if($i!=0) style="margin-top: 20px" @endif>{{$key}}</h4>
								<?php $i++;?>
								<table class="popup-table-border  table-separate table m-b-m-1">
									<tr>
										<th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">Payer</th>
										<th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims Billed</th>
										<th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims Paid</th>
										<th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Difference </th>                                            
										<th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Outstanding </th>                                           
										<th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">%</th>
									</tr>
									<?php $billed = $total_paid = $ar = $tot_ar_percentage = 0;?>
									@foreach($value as $k => $v)
										<?php
											$total_paid += $v['paid'];
											$billed += $v['claim_count'];
											$ar += $v['total_ar'];
											$total_ar_bal = (array_sum(array_column($value,'total_ar'))!=0)?array_sum(array_column($value,'total_ar')):1;
											$tot_ar_percentage +=($v['total_ar']/$total_ar_bal)*100;
										?>
										<tr>
											<td class="" style="line-height: 24px;">{{$v['insurance_name']}}</td>
											<td class="text-center">{{$v['claim_count']}}</td>
											<td class="text-center">{{$v['paid']}}</td>
											<td class="text-center">{{$v['claim_count']-$v['paid']}}</td>
											<td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($v['total_ar']) !!}</td>
											<td class="text-right">{{ round(($v['total_ar']/$total_ar_bal)*100,2)}}%</td>   
										</tr>        						
									@endforeach
									<tr class="med-green">
										<td class="med-orange font600">Totals</td>
										<td class="font600 med-orange text-center">{{$billed}}</td>
										<td class="font600 med-orange text-center">	{{$total_paid}}	</td>
										<td class="font600 med-orange text-center">{{$billed-$total_paid}}	</td>
										<td class="font600 med-orange text-right">${!! \App\Http\Helpers\Helpers::priceFormat($ar) !!}	</td>
										<td class="font600 med-orange text-right">{{$tot_ar_percentage}}%</td>
									</tr>
								</table>
							@endforeach
						@else
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
						@endif
					</div><!-- /.box-body -->
				</div><!-- Primary Location box Ends-->
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
				<div class="box no-shadow"><!-- Primary Location Box Starts -->
					<div class="box-block-header with-border">
						<i class="fa fa-navicon" data-name="mail"></i> <h3 class="box-title"> Location Status Summary</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div><!-- /.box-header -->
					<div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0">

							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
								<div class="box-body table-responsive p-t-0 no-padding">
									@if(!empty($facilityStatus))
										<table class="popup-table-border  table-separate table m-b-m-1 margin-t-10">
											<tr>
												<th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Facility</th>
												<th class="font600 text-center" colspan="4" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;border-right: 1px solid #a4ede9;border-left: 1px solid #a4ede9">Totals</th>
												<th class="font600 text-center" colspan="2" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Avg Collections per</th>                                        
											</tr>
											<tr>
												<td class=""></td>
												<td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Visits/Claims</span></td>
												<td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Days Worked</span></td>
												<td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Charges</span></td>
												<td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Payment</span></td>
												<td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Patient/Appt</span></td>
												<td class="font600 text-center  line-height-26"style="background: #dbfaf8;"><span class="med-green"> Day</span></td>
											</tr>
											<?php $status_tot_claims = $tot_days_worked = $total_charges = $tot_payments = $tot_avg_pmt = $tot_avg_pmt_per_day = 0;?>
											@foreach($facilityStatus as $key => $value)
											<?php
												if($value['Sunday'] !=0)
													unset($resultDays['Sunday']);
												if($value['Monday'] !=0)
													unset($resultDays['Monday']);
												if($value['Tuesday'] !=0)
													unset($resultDays['Tuesday']);
												if($value['Wednesday'] !=0)
													unset($resultDays['Wednesday']);
												if($value['Thursday'] !=0)
													unset($resultDays['Thursday']);
												if($value['Friday'] !=0)
													unset($resultDays['Friday']);
												if($value['Saturday'] !=0)
													unset($resultDays['Saturday']);
												$tot_days_worked += array_sum($resultDays);
												$status_tot_claims += $value['claim_count'];
												$total_charges += $value['total_charge'];
												$tot_payments += $value['payments'];
												$patient_appt = ($value['claim_count']!=0)?($value['payments']/$value['claim_count']):$value['payments'];
												$per_day = ($days!=0)?$value['payments']/$days:$value['payments'];
												$tot_avg_pmt += $patient_appt;
												$tot_avg_pmt_per_day += $per_day;
											?>
											<tr>
												<td class="" style="line-height: 24px">{{$value['facility_name']}}</td>
												<td class="text-center">{{$value['claim_count']}}</td>
												<td class="text-center">{{array_sum($resultDays)}}</td>
												<td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($value['total_charge']) !!}</td>
												<td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($value['payments']) !!}</td>
												<td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($patient_appt) !!}</td> 
												<td class="text-right">${!! ($per_day!=0)?round($per_day,2):'0.00' !!}</td> 
											</tr>
											@endforeach                                    
											<tr>
												<td class="font600 med-orange">Totals</td>
												<td class="text-center font600 med-orange">{{$status_tot_claims}}</td>
												<td class="text-center font600 med-orange">{{$tot_days_worked}}</td>
												<td class="text-right font600 med-orange">${!! \App\Http\Helpers\Helpers::priceFormat($total_charges) !!}</td>
												<td class="text-right font600 med-orange">${!! \App\Http\Helpers\Helpers::priceFormat($tot_payments) !!}</td>
												<td class="text-right font600 med-orange">${!! \App\Http\Helpers\Helpers::priceFormat($tot_avg_pmt) !!}</td> 
												<td class="text-right font600 med-orange">${!! ($tot_avg_pmt_per_day!=0)?round($tot_avg_pmt_per_day,2):'0.00' !!}</td> 
											</tr>
										</table>
									@else
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
									@endif
								</div><!-- /.box-body -->
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- Primary Location box Ends-->
			</div>
		@else
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
		@endif
		</div>
	</div>
</div>