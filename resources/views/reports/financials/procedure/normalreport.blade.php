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
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Procedure Collection Report - Insurance Only</h3>
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


     @if(count($cptreport_list) > 0)  
<div class="box-body ">  
    <div class="table-responsive  col-lg-12">
            <table class="table table-striped table-bordered table-separate" id="sort_list_noorder_report">  
                <thead>
                    <tr>
                        <th>CPT</th>
                        <th>DOS</th>
                        <th>Acc No</th>
                        <th>Claim No</th>
                        <th>Patient Name</th>
                        <th>Rendering</th>
                        <th>Ins Type</th>
                        <th>Insurance</th>
                        <th>Charge Date</th>
                        <th class="text-right">Charge Amount($)</th>
                        <th>Payment Date</th>
                        <th>Allowed Amount($)</th>
                        <th class="text-right">Payment Amount($)</th>                                                        
                    </tr>
                </thead>
                <tbody>
                   <?php   $total_amt_charge = 0;$total_amt_payment=0; ?>     
                    @foreach($cptreport_list as  $result)
                    <?php                                                 
                       //   $insurance_payment[] = $dates->insurance_payment;
                        //  $patient_payment[] = $dates->patient_payment; 
                        @$last_name = $result->patient_details->last_name;
                        @$first_name = $result->patient_details->first_name;
                        @$middle_name = $result->patient_details->middle_name;
                        @$patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                    ?>
                     <tr style="cursor:default;">
                        <?php /* SP  */
                            if(isset($result->account_no) && $result->account_no != ''){
                        ?>
                        <td>{!! !empty($result->cpt_code)? @$result->cpt_code : '-Nill-' !!}</td> 
                        <td>{!! !empty($result->date_of_service)? @$result->date_of_service : '-Nill-' !!}</td>
                        <td>{!! !empty($result->account_no)? @$result->account_no : '-Nill-' !!}</td>
                        <td>{!! !empty($result->claim_number)? @$result->claim_number : '-Nill-' !!}</td>
                        <td>{!! !empty($result->patient_name)? @$result->patient_name : '-Nill-'  !!}</td>
                        <td>{!! !empty($result->rendering_short_name)? @$result->rendering_short_name : '-Nill-' !!}</td>
                        <td>{!! !empty($result->type_name)? @$result->type_name : '-Nill-' !!}</td>
                        <td>{!! !empty($result->insurance_short_name)? @$result->insurance_short_name : '-Nill-' !!}</td>
                        <td class="text-center">{!! !empty($result->charge_date)? @$result->charge_date : '-Nill-' !!}</td> 
                        <td class="text-right">{!! !empty($result->charge)? App\Http\Helpers\Helpers::priceFormat(@$result->charge) : '-Nill-' !!}</td> 
                        <td class="text-center">{!! !empty($result->payment_date)? @$result->payment_date : '-Nill-' !!}</td> 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->allowed) !!}</td> 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->Payment_amount) !!}</td>
                        <?php 
                            } else {
                        ?>
                        <td>{{ !empty($result->cpt_code)? @$result->cpt_code : '-Nill-' }}</td> 
                        <td>{!! !empty($result->date_of_service)? date('m/d/Y',strtotime(@$result->date_of_service)) : '-Nill-' !!}</td>
                        <td>{{ !empty($result->patient_details->account_no)? @$result->patient_details->account_no : '-Nill-' }}</td> 
                        <td>{{ !empty($result->claim_details->claim_number)? @$result->claim_details->claim_number : '-Nill-' }}</td> 
                        <td>{!! !empty($patient_name)? @$patient_name : '-Nill-' !!}</td>
                        <td>{{ !empty($result->claim_details->rend_providers->short_name)? @$result->claim_details->rend_providers->short_name :  '-Nill-' }}</td>
                        <td>{{ !empty($result->type_name)? @$result->type_name : '-Nill-'}}</td>
                        <td>{{ !empty($result->payer_insurance_id)? App\Models\Insurance::getInsuranceshortName(@$result->payer_insurance_id) : '-Nill-'}}</td>
                        <td class="text-center">{{ !empty($result->charge_date)? App\Http\Helpers\Helpers::dateFormat(@$result->charge_date, 'date') : '-Nill-' }}</td> 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td> 
                        <td class="text-center">{{ App\Http\Helpers\Helpers::dateFormat(@$result->payment_date, 'date') }}</td> 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->allowed) !!}</td> 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->Payment_amount) !!}</td>
                        <?php } ?>
                        <?php 
                            $total_amt_payment += @$result->Payment_amount;
                            $total_amt_charge += @$result->charge;
                        ?>
                   </tr>
                    @endforeach
                    <tr>
                        <th class=" bg-white border-radius-4"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="text-right bg-white"></th>
                        <th class ="med-green font600 text-right bg-white">Totals</th>
                        <th class ="text-right bg-white font600 med-orange border-radius-4">${!!App\Http\Helpers\Helpers::priceFormat($total_amt_payment)!!}</th>
                    </tr>
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