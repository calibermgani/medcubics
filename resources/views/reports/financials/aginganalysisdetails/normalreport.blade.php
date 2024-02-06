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
            <h3 class="text-center reports-heading p-l-2 margin-b-25 med-orange">Aging Analysis - Detailed</h3>
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

  <?php //if(($search_lable != 'rendering_provider')  && ($search_lable != 'facility')){
    ?>
    @if(count($aging_report_list) > 0)  
      <div class="box-body no-padding">  
        <div class="table-responsive mobile-lg-scroll mobile-md-scroll col-lg-12 no-padding">
            <table class="table table-striped table-bordered table-separate">  
                <thead>
                    <tr>
                        <th>Acc No</th>
                        <th>Patient Name</th>
                        <th>Claim No</th>
                        <th>DOS</th> 
                        <th>Responsibility</th> 
                        <th>Category</th> 
                        <th>Insurance Type</th> 
                        <th>Policy ID</th> 
                        <th>Billing</th> 
                        <th>Rendering</th> 
                        <th>Facility</th> 
                        <th>First Submission Date</th> 
                        <th>Last Submission Date</th> 
                        <th>Charges ($)</th>
                        @if($show_flag == "All" || $show_flag == "Unbilled")
                        <th>Unbilled ($)</th>
                        @endif
                        @if($show_flag == "All" || $show_flag == "0-30")
                        <th>0-30 ($)</th>
                        @endif
                        @if($show_flag == "All" || $show_flag == "31-60")
                        <th>31-60 ($)</th>
                         @endif
                        @if($show_flag == "All" || $show_flag == "61-90")
                        <th>61-90 ($)</th>
                          @endif
                        @if($show_flag == "All" || $show_flag == "91-120")
                        <th>91-120 ($)</th>
                          @endif
                        @if($show_flag == "All" || $show_flag == "121-150")
                        <th>121-150 ($)</th>
                          @endif
                        @if($show_flag == "All" || $show_flag == "150-above")
                        <th> >150 ($)</th>
						@endif  
						<th>Pat AR ($)</th>
						<th>Ins AR ($)</th>
						<th>Tot AR ($)</th>
						<th>AR Days</th>
						<th>Claim Status</th>
						<th>Claim Sub Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $temp_id = 0; $cnt = $pagination->from-1; $label = $search_lable.'_id'; ?>         
                  @foreach($aging_report_list as  $result)
                    @if( ($search_lable == 'billing_provider' || $search_lable == 'rendering_provider' || $search_lable == 'facility'))
                      @if ($pagination->to == $pagination->total)
                        <?php $cnt++;?>
                      @endif
                      @if($temp_id!=0 && $temp_id != $result->$label)
                        <tr>
                          <th>Totals</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <?php $id = $result->billing_provider_id;?>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_charge)!!}</th>
                          @if($show_flag == "All" || $show_flag == "Unbilled")                      
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->unbilled)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "0-30")   
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days30)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "31-60") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days60)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "61-90") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days90)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "91-120")  
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days120)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "121-150") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days150)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "150-above") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->daysabove)!!}</th>
                          @endif 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_pat) !!}</th>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_ins) !!}</th>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total) !!}</th>
                          <th class="text-right"></th>
                          <th class="text-right"></th>
						  <th class="text-right"></th>
                        </tr>
                        <?php $temp_id = 0;?>  
                      @endif
                      <?php
                      if($search_lable == 'rendering_provider'){
                        $provider_id = $result->$label;
                        $provider_name = 'Rendering Provider - '.App\Models\Provider::getProviderFullName(@$provider_id);
                      }
                      if($search_lable == 'facility'){
                        $provider_id = $result->$label;
                        $provider_name = 'Facility - '.App\Models\Facility::getFacilityName(@$provider_id);
                      }
                      if($search_lable == 'billing_provider'){
                         $provider_id = $result->$label;
                         $provider_name = 'Billing Provider - '.App\Models\Provider::getProviderFullName(@$provider_id);
                       }
                      ?>
                      @if( $temp_id==0 && $temp_id != $result->$label)
                      <tr style="border: none !important; cursor:default;">
                        <td colspan ="12" class="font600 med-green">{{$provider_name}}</td>
                        @if($show_flag == "All" || $show_flag == "Unbilled")                      
                        <td></td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "0-30")   
                        <td></td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "31-60") 
                        <td></td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "61-90") 
                        <td></td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "91-120")  
                        <td></td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "121-150") 
                        <td></td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "150-above") 
                        <td></td>
                        @endif  
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
						<td></td>
                      </tr>
                      <?php $temp_id = $result->$label;?>
                      @endif
                    @endif
                     <tr style="cursor:default;">
                       
                        <td >{{ $result->account_no}}</td> 
                        <td >{{ $result->patient_name}}</td> 
                        <td >{{ $result->claim_number}}</td> 
                        <td >{{ @$result->dos}}</td> 
                        <td >{{ @$result->responsibility}}</td> 
                        <td >{{ (isset($result->insurance_category) && !empty($result->insurance_category) ? $result->insurance_category : '-Nil-' )}}</td> 
                        <td >{{ (isset($result->type_name) && !empty($result->type_name) ? $result->type_name : '-Nil-' )}}</td>
                        <td >{{ @$result->policy_id}}</td>
                        <?php /* providers name from stored procedure result  */
                            if(isset($result->rendering_name) && $result->rendering_name != '' || isset($result->billing_name) && $result->billing_name != ''){
                        ?>
                        <td>{{ @$result->billing_short_name }}</td> 
                        <td>{{ @$result->rendering_short_name }}</td> 
                        <td>{{ @$result->facility_short_name }}</td>
                        <td>{!! !empty($result->submited_date)? $result->submited_date : '-Nill-' !!}</td>
                        <td>{!! !empty($result->last_submited_date)? $result->last_submited_date : '-Nill-' !!}</td>
                        <?php 
                            } else {
                        ?>
                        <td>{{ App\Models\Provider::getProviderShortName(@$result->billing_provider_id)}}</td> 
                        <td>{{ App\Models\Provider::getProviderShortName(@$result->rendering_provider_id)}}</td> 
                        <td>{{ App\Models\Facility::getFacilityShortName(@$result->facility_id)}}</td>
                        <td>{!! App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->submited_date,'','-Nil-') !!}</td>
                        <td>{!! App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->last_submited_date,'','-Nil-') !!}</td>
                        <?php } ?>                          
                         <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($result->total_charge)!!}</td>
                        @if($show_flag == "All" || $show_flag == "Unbilled")                       
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->unbilled) !!}</td>  
                        @endif  
                        @if($show_flag == "All" || $show_flag == "0-30")                    
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days30)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "31-60") 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days60)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "61-90") 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days90)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "91-120") 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days120)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "121-150") 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days150)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "150-above")      
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->daysabove)!!}</td> 
                        @endif 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($result->pat_bal)!!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($result->ins_bal)!!}</td>    
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($result->total_bal) !!}</td>    
                        <td class="@if($result->ar_days<0) med-red @endif">{{ ($result->ar_days!=0)?$result->ar_days:'0' }}</td>    
                        <td>{{ $result->status}}</td>
						<td> @if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){{ $result->sub_status_desc}}@endif</td>
                    </tr>
                    @if( ($search_lable == 'billing_provider' || $search_lable == 'rendering_provider' || $search_lable == 'facility'))
                      @if ($cnt == $pagination->total)
                        <tr>
                          <th>Totals</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <?php $id = $result->billing_provider_id;?>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_charge)!!}</th>
                          @if($show_flag == "All" || $show_flag == "Unbilled")                      
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->unbilled)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "0-30")   
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days30)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "31-60") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days60)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "61-90") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days90)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "91-120")  
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days120)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "121-150") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days150)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "150-above") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->daysabove)!!}</th>
                          @endif 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_pat) !!}</th>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_ins) !!}</th>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total) !!}</th>
                          <th class="text-right"></th>
                          <th class="text-right"></th>
						  <th class="text-right"></th>
                        </tr>
                      @endif
                    @endif
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
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
    @endif
      
    </div><!-- Box Body Ends --> 

</div><!-- /.box Ends-->
