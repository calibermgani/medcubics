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
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">End of the Day Totals</h3>
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
        <?php /* && array_sum(array_flatten(json_decode(json_encode($result),true)))!=0*/?>
@if(count((array)$result)>0)
    <div class="table-responsive col-lg-12">
            <table class="table table-striped table-bordered" id="sort_list_noorder">
                <thead>
                    <tr>
                      <th style="border-bottom: 1px solid #fff !important;" rowspan="2">Date-Day</th>                        
                        <th style="border-bottom: 1px solid #fff !important;" rowspan="2">Charges($)</th>
                        <th style="border-bottom: 1px solid #fff !important;" rowspan="2">Claims</th>
                        <th style="border-bottom: 1px solid #fff !important;" rowspan="2">Writeoff($)</th>
                        <th style="border-bottom: 1px solid #fff !important;" colspan="2">Adjustments($)</th>
                        <th style="border-bottom: 1px solid #fff !important;" colspan="2">Refund($)</th>
                        <th style="border-bottom: 1px solid #fff !important;" colspan="2">Payments($)</th>
                        <th style="border-bottom: 1px solid #fff !important;" rowspan="2">Total Payments($)</th>
                    </tr>
                    <tr>                        
                        <th style="border-bottom: 1px solid #fff !important;border-left: 1px solid #fff !important;">Insurance</th>
                        <th style="border-bottom: 1px solid #fff !important;">Patient</th>
                        <th style="border-bottom: 1px solid #fff !important;">Insurance</th>
                        <th style="border-bottom: 1px solid #fff !important;">Patient</th>
                        <th style="border-bottom: 1px solid #fff !important;">Insurance</th>
                        <th style="border-bottom: 1px solid #fff !important;">Patient</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($result))  
                    
                    <?php
        				$total_adj = 0;
        				$patient_total = 0;
        				$insurance_total = 0;
			         ?>                   
                    @foreach($result as  $key=>$dates)
                    <?php                        
                          $insurance_payment[] = isset($dates->insurance_payment) ? $dates->insurance_payment : 0;
                          $writeoff_total[] = isset($dates->writeoff_total) ? $dates->writeoff_total : 0;
                          $patient_payment[] = isset($dates->patient_payment) ? $dates->patient_payment : 0;                       
                          $patient_adjustment[] = isset($dates->patient_adjustment) ? $dates->patient_adjustment : 0;                       
                          $insurance_adjustment[] = isset($dates->insurance_adjustment) ? $dates->insurance_adjustment : 0;
                          $insurance_refund[] = isset($dates->insurance_refund) ? $dates->insurance_refund : 0;
                          $patient_refund[] = isset($dates->patient_refund) ? $dates->patient_refund : 0;
                    ?>
                     <tr style="cursor:default;">
                         <td><span class="med-green font600">{{$key.'-'.date('D', strtotime($key))}}</span></td>
                        <td class="text-right">{{ App\Http\Helpers\Helpers::priceFormat(@$dates->total_charge, 'no') }} </td>
                        <td class="text-right">@if(@$dates->claims_count != ''){{ @$dates->claims_count }}@else 0 @endif </td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->writeoff_total,'no') !!} </td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->insurance_adjustment,'no')  !!}  </td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->patient_adjustment,'no') !!}  </td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->insurance_refund,'no') !!}</td>
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->patient_refund,'no') !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->insurance_payment,'no') !!} </td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->patient_payment,'no') !!} </td>     
						<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$dates->total_payment,'no') !!} 	</td>     
                   </tr>
                    @endforeach
                  @endif
                </tbody>
            </table>    
        </div>
   
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <input type="hidden" id="pagination_prt" value="string"/>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
        </div>
      
        
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->