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
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Refund Analysis - Detailed</h3>
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
        @if(isset($get_refund_datas) && !empty($get_refund_datas))
        @if($refund_type == 'insurance' && empty($unposted))
        <div class="table-responsive col-lg-12">
            <table class="table table-striped table-bordered" id="sort_list_noorder">
                <thead>
                    <tr>

                        <th>Claim No</th>
                        <th>DOS</th>
                        <th>Acc No</th>
                        <th>Patient Name</th>                    
                        <th>Rendering</th>
                        <th>Billing</th>
                        <th>Facility</th>
                        <th>Insurance</th>
                        <th>Check Date</th>
                        <th>Check No</th>					
                        <th>Refund Amt($)</th> 
                        <th>User</th>  		
                    </tr>
                </thead>
                <tbody>
                    @if(isset($get_refund_datas) && !empty($get_refund_datas))
                    @foreach(@$get_refund_datas as $refund_value)
                    <?php $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->claim->patient->last_name,@$refund_value->claim->patient->first_name,@$refund_value->claim->patient->middle_name); ?> 
                    <tr style="cursor:default;">
                        <td>{{ !empty($refund_value->claim->claim_number)? $refund_value->claim->claim_number : '-Nill-' }}</td>
                        <td>{{ !empty($refund_value->claim->date_of_service)? App\Http\Helpers\Helpers::dateFormat(@$refund_value->claim->date_of_service, 'dob'): '-Nill-' }}</td>
                        <td>{{ !empty($refund_value->claim->patient->account_no)? $refund_value->claim->patient->account_no : '-Nill-' }}</td>
                        <td>{{ !empty($patient_name)? $patient_name : '-Nill-' }}</td>                                                 
                        <td>{{ !empty($refund_value->claim->rendering_provider->short_name)? $refund_value->claim->rendering_provider->short_name : '-Nill-' }}</td>
                        <td>{{ !empty($refund_value->claim->billing_provider->short_name)? $refund_value->claim->billing_provider->short_name : '-Nill-' }}</td>
                        <td>{{ !empty($refund_value->claim->facility_detail->short_name)? $refund_value->claim->facility_detail->short_name : '-Nill-'}}</td>
                        <?php $insurance_name = @$refund_value->payment_info->insurancedetail->short_name; ?>
                        <td>{{ !empty($insurance_name)? $insurance_name : '-Nill-' }}</td>
                        <td>{{ !empty($refund_value->latest_payment_check->check_details->check_date)? App\Http\Helpers\Helpers::dateFormat(@$refund_value->latest_payment_check->check_details->check_date, 'dob') : '-Nill-' }}</td>
                        <td> {{ !empty($refund_value->latest_payment_check->check_details->check_no)? @ucwords($refund_value->latest_payment_check->check_details->check_no) : '-Nill-' }} </td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(abs(@$refund_value->total_paid)) !!}</td>
                        <td> {{ !empty($refund_value->user->short_name)? @ucwords($refund_value->user->short_name) : '-Nill-' }} </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        @if(!empty($refund_result))
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! @$pagination->pagination_prt !!}</div>
        </div>
        @endif
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10" style="border-top: 1px solid #f0f0f0;">
            <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-10">

                <div class="box-header-view-white no-border-radius pr-t-5 margin-b-10">
                    <i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>                     
                </div><!-- /.box-header -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10">
                    <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4 summary-box" style="">    
                        <tbody>
                            <tr> 
                                <td>Total Insurance Refunds</td>                                            
                                <td class='med-green font600 text-right' >${!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->insurance) !!}</td>
                            </tr>
                        </tbody>
                    </table>   
                </div>
            </div>
        </div>

        @elseif($refund_type == 'patient' && empty($unposted) && empty($wallet))        
        <div class="table-responsive col-lg-12">
            <table class="table table-striped table-bordered space " id="sort_list_noorder">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Acc No</th>
                        <th>Refund Amt($)</th> 
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($get_refund_datas as $refund_value)
                    <?php
                        $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->claim_patient_det->last_name,@$refund_value->claim_patient_det->first_name,@$refund_value->claim_patient_det->middle_name);
                        $wallet_refund = @$refund_value->claim_patient_det->pmt_info[0]->refund_amt;
                        @$refund_amt = abs(@$refund_value->total_paid);
                    ?>
                    <tr style="cursor:default;">
                        <td>{{ @$patient_name }}</td>
                        <td>{{ @$refund_value->claim_patient_det->account_no }}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$refund_amt) !!}</td>
                        <td> {{ @ucwords($refund_value->user->short_name) }} </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div> 
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! @$pagination->pagination_prt !!}</div>
        </div>   

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10" style="border-top: 1px solid #f0f0f0;">
            <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-10">

                <div class="box-header-view-white no-border-radius pr-t-5 margin-b-10">
                    <i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>                     
                </div><!-- /.box-header -->

                <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4 summary-box" style="">    
                    <tbody>
                        
                        <tr> 
                            <td>Total Patients Refunds</td>                                            
                            <td class='med-green font600 text-right'>${!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->patient)  !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>       
        @elseif(!is_null($unposted) && !empty($unposted) || !is_null($wallet) && !empty($wallet))
        <div class="table-responsive col-lg-12">
            <table class="table table-striped table-bordered space " id="sort_list_noorder">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Check Date</th>   
                        <th>Check No</th>
                        <th>Refund Amt($)</th> 
                        <th>User</th>       
                    </tr>
                </thead>
                <tbody>
                    @foreach($get_refund_datas as $refund_value)
                    <?php 
                        $refund_amt = $refund_value->pmt_amt;
                        $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->patient->last_name,@$refund_value->patient->first_name,@$refund_value->patient->middle_name);
                    ?>
                    <tr style="cursor:default;">
                        <td>{{ @$patient_name }}</td>
                        <td>{{ App\Http\Helpers\Helpers::dateFormat(@$refund_value->check_details->check_date) }}</td>
                        <td>{{ @$refund_value->check_details->check_no }}</td>                        
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$refund_amt) !!}</td>
                        <td> {{ @ucwords($refund_value->created_user->short_name) }} </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div> 
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
             <input type="hidden" id="pagination_prt" value="string"/>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! @$pagination->pagination_prt !!}</div>
        </div>   

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10" style="border-top: 1px solid #f0f0f0;">
            <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-10">

                <div class="box-header-view-white no-border-radius pr-t-5 margin-b-10">
                    <i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>                     
                </div><!-- /.box-header -->

                <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4 summary-box" style="">    
                    <tbody>
                        @if($refund_type == 'insurance')
                        <tr> 
                            <td>Total Insurance Refunds</td>                                            
                            <td class='med-green font600 text-right' >${!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->insurance) !!}</td>
                        </tr>
                        @else
                        <tr> 
                            <td>Total Patients Refunds</td>                                            
                            <td class='med-green font600 text-right'>${!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->patient)  !!}</td>
                        </tr>
                        @endif
                        <tr class="hide"> 
                            <td class="font600">Total Refunds</td>                                            
                            <td class='med-orange font600 text-right'>${!! App\Http\Helpers\Helpers::priceFormat(@$total_refund->total) !!}</td>
                        </tr>
                    </tbody>
                </table>     
            </div>
        </div>
        @endif  
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->
