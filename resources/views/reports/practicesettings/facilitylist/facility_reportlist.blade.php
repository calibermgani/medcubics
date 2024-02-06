<?php @$heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->		
    
  <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
      <div class="box-body  bg-white"><!-- Box Body Starts -->      
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange">Facility Summary</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">
					<?php $i = 0; $search_by = isset($search_by) ? $search_by : []; ?>
                    @foreach($search_by as $key=>$val)
						@if($i > 0){{' | '}}@endif
                        <span class="med-green">{!! $key !!}: </span>{{ $val }}                           
                        <?php $i++; ?>
                    @endforeach </div>                   
                </div>                
            </div>
        </div>
        <?php $req = @$practice_opt; ?>
        @if($req == "provider_list")
        <div class="table-responsive col-lg-12 margin-t-20">
            <table class="table table-striped table-bordered" id="sort_list_noorder">
                <thead>
                    <tr>
                        <th>Facility Name</th>
                        <th>POS</th>              
                        <th>Created On</th>
                        <th>User</th> 					
                    </tr>
                </thead>
                <tbody>			
                    @if(count((array)$facilities) > 0)  
                    <?php
						$total_adj = $patient_total = $insurance_total = $count = 0;
			        ?>
                    @foreach($facilities as $list)				
                    <tr style="cursor:default;">                   
                        <td class="text-left">{!! @$list->facility_name !!}</td>
                        <td class="text-left">{!! @$list->pos_details->code !!}-{!! @$list->pos_details->pos !!}</td>                 
                        <td class="text-left">{{ App\Http\Helpers\Helpers::timezone(@$list->created_at, 'm/d/y') }}</td>
                        <td class="text-left">{!! @$list->facility_user_details->short_name !!}</td>						
                    </tr>
                    @endforeach				
                </tbody>
             @endif
            </table>    
        </div>
       
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
        </div>
        @else
            @if(!empty($facilities))         
                <?php
                    $total_adj = $patient_total = $insurance_total = $total = $count = $payments = $total_payments = $tot_avg = $cnts = 0;
                    $wallet = isset($wallet)?$wallet:0;
                ?>	
                @if(!empty($charges) || !empty($adjustments) || !empty($patient) || !empty($insurance) || !empty($patient_bal) || !empty($insurance_bal) || !empty($unit_details))	
                <div class="table-responsive col-lg-12">
                    <table class="table table-striped table-bordered" id="sort_list_noorder">
                        <thead>
                            <tr>
                                <th>Facility Name</th>
                                <th>POS</th>
                                <th>Units</th>
                                <th>Charges($)</th>
                                <th>Adj($)</th>
                                <th>Pmts($)</th>    
                                <th>Avg pmts/Pat($)</th>    
                                <th>Pat Balance($)</th> 
                                <th>Ins Balance($)</th>
                                <th>Total Balance($)</th>                   
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facilities as $key => $list)
                            <?php
                                $practice_timezone = App\Http\Helpers\Helpers::getPracticeTimeZone();
                                $exp = explode("to",$search_by->{'Transaction Date'});
                                $start_date = date("Y-m-d",strtotime(trim($exp[0])));
                                $end_date = date("Y-m-d",strtotime(trim($exp[1])));
                                $total_bal = @$list->patient_bal+@$list->insurance_bal;
                                $payments = @$list->patient+@$list->insurance;
                                if(!empty(App\Models\Pos::select('id')->where('code',$list->code)->get()->toArray())){
                                    $pos_id = App\Models\Pos::select('id')->where('code',$list->code)->get()->toArray()[0]['id'];
                                    $count = DB::select("select count(claim.facility_id) as cnt from (select facility_id, pos_id from claim_info_v1 where facility_id = ".$list->facility_id." and pos_id = ".$pos_id." and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' and deleted_at is null group by patient_id) as claim");
                                    if(!empty($count)){
                                        $patient_cnt = ($count[0]->cnt!=0)?$count[0]->cnt:1;
                                    }else{
                                        $patient_cnt = 1;
                                    }
                                        $tot_avg_pmt = ((@$list->patient+@$list->insurance)!=0 && $patient_cnt != 0)?round((@$list->patient+@$list->insurance)/$patient_cnt):0.00;
                                }
                            ?>              
                            <tr style="cursor:default;">
                                <td class="text-left">{!! @$list->facility_name !!}</td>
                                <td>{!! @$list->code !!}-{!! @$list->pos !!}</td>
                                <td>{!! (@$list->unit_details!='')?$list->unit_details:0 !!}</td>                      
                                <td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat(@$list->charges) !!}</td>
                                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$list->adjustments) !!}</td>
                                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($payments) !!}</td>
                                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($tot_avg_pmt) !!}</td>
                                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$list->patient_bal) !!}</td>
                                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$list->insurance_bal) !!}</td>
                                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$total_bal) !!}</td>   
                            </tr>
                                <?php $tot_avg += $tot_avg_pmt; ?>
                            @endforeach
                            <?php
                                if(!empty($header)){
                                    $counts = DB::select("select count(claim.facility_id) as cnt from (select facility_id, pos_id from claim_info_v1 where facility_id in (".implode(',', $header).") and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' and deleted_at is null group by patient_id) as claim");
                                } else {
                                    $counts = DB::select("select count(claim.facility_id) as cnt from (select facility_id, pos_id from claim_info_v1 where DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' and deleted_at is null group by patient_id) as claim");
                                }
                                if(!empty($counts)){
                                    $cnts = $counts[0]->cnt;
                                    $tot_avg = ($cnts != 0)?$tot_avg/$cnts:$tot_avg;
                                }
                                //$tot_avg = (($patient+$insurance)!=0 && $cnts != 0)?round(($patient+$insurance)/$cnts):0.00;
                            ?>
                            @if(!empty($facilities))
                            @if(@$pagination->to==@$pagination->total)
                            <tr>
                                <td class="med-orange font600">Totals</td>
                                <td></td>
                                <td class="text-left med-green font600">{{@$unit_details}}</td>
                                <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat(@$charges) !!}</td>
                                <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat(@$adjustments) !!}</td>
                                <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($patient+$insurance) !!}</td>
                                <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($tot_avg) !!}</td>
                                <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($patient_bal) !!}</td>
                                <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($insurance_bal) !!}</td>
                                <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($patient_bal+$insurance_bal) !!}</td>
                            </tr>
                            @endif
                            @endif
                        </tbody>
                    </table>    
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                        Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
                </div>
				<?php
					$wallet = ($wallet<0) ? 0 : $wallet;
				?>
				<div style="margin-top: 15px;">
					<table>
						<td class="font600">Wallet Balance : </td>
						<td class='text-right med-orange font600'>${{App\Http\Helpers\Helpers::priceFormat($wallet)}}</td>
					</table>
				</div>
				
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding hide" style="border-top: 1px solid #f0f0f0;">
                    <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-10">
                        <div class="box-header-view-white no-border-radius pr-t-5 margin-b-5">
                            <i class="fa fa-bars"></i><strong class="med-orange font13"> Summary</strong>                     
                        </div><!-- /.box-header -->
                        <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4" style="border: 1px solid #00877f;">	
                            <thead>
                                <tr>
                                    <th class="med-bg-green">Title</th>                                           
                                    <th class="text-right">Value($)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr> 
                                    <td>Total Facility count</td>                                            
                                    <td class='med-green font600 text-right' >{{count((array)$facilities)}}</td>                      
                                </tr>
                                <tr> 
                                    <td>Total Wallet Balance</td>                                            
                                    <td class='med-green font600 text-right' >{!! App\Http\Helpers\Helpers::priceFormat($wallet) !!}</td>
                                </tr>
                                <tr> 
                                    <td>Total Patient Balance</td>                                            
                                    <td class='med-green font600 text-right' >{!! App\Http\Helpers\Helpers::priceFormat(@$patient_total,'no') !!}</td>                      
                                </tr>
                                <tr> 
                                    <td>Total Insurance Balance</td>                                            
                                    <td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat(@$insurance_total,'no') !!}</td>
                                </tr>
                                <tr> 
                                    <td>Total Adjustments (With held included)</td>                                            
                                    <td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat(@$total_adj,'no') !!}</td>
                                </tr> 
                                <tr> 
                                    <td>Total Balance</td>                                            
                                    <td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat(@$total,'no') !!}</td>
                                </tr>  
                            </tbody>
                        </table>   
                    </div>
                </div>
                @else
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
                @endif
            @else
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
            @endif
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->