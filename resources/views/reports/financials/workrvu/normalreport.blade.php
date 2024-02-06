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
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Work RVU Report</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                     @if($i > 0){{' | '}}@endif
                           <span class="med-green">{!! $key !!}: </span>{{ @$val[0] }}                           
                          <?php $i++; ?>
                     @endforeach </div>                   
                </div>                
            </div>
        </div>


     @if(count($workrvu_list) > 0)  
<div class="box-body no-padding">  
    <div class="table-responsive mobile-md-scroll col-lg-12">
            <table class="table table-striped table-bordered table-separate" id="sort_list_noorder">  
                <thead>
                    <tr>
                        <th>Transaction Date</th>
                        <th>DOS</th>
                        <th>Acc No</th>                        
                        <th>Patient Name</th>
                        <th>CPT/HCPCS</th>
                        <th>Description</th>
                        <th>Rendering</th>
                        <th>Facility</th>
                        <th>Units</th>
                        <th>Units Charge($)</th>
                        <th>Total Charge($)</th>
                        <th>Work RVU($)</th>                                                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($workrvu_list as  $result)
                    <?php                                                 
                        @$last_name = $result->patient_details->last_name;
                        @$first_name = $result->patient_details->first_name;
                        @$middle_name = $result->patient_details->middle_name;
                        @$patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                        @$total_amt_charge = ($result->units > 0) ? (@$result->charge / @$result->units) : $result->charge;
                    ?>
                    <tr style="cursor:default;">
                        <?php 
                            if(isset($result->account_no) && $result->account_no != ''){
                        ?>
                        <td>{!! !empty($result->transaction_date)? $result->transaction_date : '-Nill-' !!}</td>
                        <td>{!! !empty($result->date_of_service)? $result->date_of_service : '-Nill-' !!}</td>
                        <td>{!! !empty($result->account_no)? $result->account_no : '-Nill-' !!}</td>
                        <td>{!! !empty($result->patient_name)? $result->patient_name : '-Nill-' !!}</td>
                        <td>{!! !empty($result->cpt_code)? $result->cpt_code : '-Nill-' !!}</td>
                        <td>{!! !empty($result->medium_description)? $result->medium_description : '-Nill-' !!}</td> 
                        <td>{!! !empty($result->rendering_short_name)? $result->rendering_short_name : '-Nill-' !!}</td> 
                        <td>{!! !empty($result->facility_short_name)? $result->facility_short_name : '-Nill-' !!}</td>
                        <td>{!! !empty($result->units)? $result->units : '-Nill-' !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->total_amt_charge) !!}</td> 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->work_rvu) !!}</td>
                        <?php 
                            } else {
                        ?>
                        <td>{{ !empty($result->transaction_date)? App\Http\Helpers\Helpers::timezone(@$result->transaction_date, 'm/d/y') : '-Nill-' }}</td>
                        <td>{!! !empty($result->date_of_service)? date('m/d/Y',strtotime(@$result->date_of_service)) :'-Nill-' !!}</td>                        
                        <td>{{ !empty($result->patient_details->account_no)? @$result->patient_details->account_no : '-Nill-' }}</td>
                        <td>{!! !empty($patient_name)? $patient_name : '-Nill-' !!}</td>
                        <td>{{ !empty($result->cpt_code)? $result->cpt_code : '-Nill-'}}</td>
                        <td>{{ !empty($result->medium_description)? $result->medium_description : '-Nill-' }}</td> 
                        <td>{{ !empty($result->claim_details->rend_providers->short_name)? $result->claim_details->rend_providers->short_name : '-Nill-' }}</td> 
                        <td>{{ !empty($result->claim_details->facility->short_name)? $result->claim_details->facility->short_name : '-Nill-' }}</td>
                        <td>{{ !empty($result->units)? $result->units : '-Nill-' }}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$total_amt_charge) !!}</td> 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->work_rvu) !!}</td>
                        <?php } ?>
                    </tr>
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