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
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange">CPT/HCPCS Summary</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-0 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>
                        @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span class="med-green">{!! $key !!} : </span>{{ @$val[0] }}                           
                        <?php $i++; ?>
                        @endforeach 
					</div>                   
                </div>                
            </div>
        </div>
        @if(count($cpts) > 0)
        <div class="table-responsive col-lg-12 margin-t-20">
            <table class="table table-striped table-bordered" id="sort_list_noorder">
                <thead>
                    <tr>
                        <th>CPT Code</th>
                        <th>Description</th>
                        <th>Units</th>                   
                        <th>Charges($)</th>
                        <th>Pat Paid($)</th>   
                        <th>Ins Paid($)</th> 
                        <th>Pat Adj($)</th>
                        <th>Ins Adj($)</th>                          
                        <?php /*	
                        <th>Pat Balance($)</th> 
                        <th>Ins Balance($)</th>                 
                        */ ?>
                        <th>AR Due($)</th>              
                    </tr>
                </thead>
                <tbody>         
                    @foreach($cpts as $list)
                    <tr style="cursor:default;">
                        <?php							
                            $cpt_code = @$list->cpt_code;
                            $total_charge = isset($list->total_charge) ? $list->total_charge : 0;
                            $desc = isset($cptDesc->$cpt_code) ? $cptDesc->$cpt_code : '';
                            $patient_adj = isset($list->pat_adj)?$list->pat_adj:0;
                            $insurance_adj = isset($list->ins_adj)?$list->ins_adj:0;
                            $adjustment = isset($list->tot_adj)?$list->tot_adj:0;
                            $pat_pmt = isset($patient->$cpt_code)?$patient->$cpt_code:0;
                            $ins_pmt = isset($insurance->$cpt_code)?$insurance->$cpt_code:0;
                            $pat_bal = isset($list->patient_bal)?$list->patient_bal:0;
                            $ins_bal = isset($list->insurance_bal)?$list->insurance_bal:0;
                        ?>
                        <td>{{ @$cpt_code }}</td>
                        <td>{{ @$list->description }}</td>
                        <td>{{ @$list->unit}}</td>
                        <td class="text-right">{{ $total_charge }}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$list->patient_paid) !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$list->insurance_paid) !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($patient_adj) !!} </td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($insurance_adj) !!} </td>
                        <?php /*
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pat_bal) !!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($ins_bal) !!}</td>
                        */ ?>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($list->total_ar_due) !!} </td>
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
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border-top: 1px solid #f0f0f0;">
            <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-10">
                <div class="box-header-view-white no-border-radius pr-t-5 margin-b-10">
                        <i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>                     
                    </div><!-- /.box-header -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10">
                    <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4 summary-box" style="">    
                        <tbody>
                        <?php
                            $wallet = isset($patient->wallet)?$patient->wallet:0;
                            if($wallet<0)
                                $wallet = 0;
                        ?>
                            <tr>
                                <td>Wallet Balance</td>
                                <td class='text-right font600'>${{App\Http\Helpers\Helpers::priceFormat($wallet)}}</td>
                            </tr>
                            <tr> 
                                <td>Total Units</td>                                            
                                <td class='font600 text-right' >{{ $summary_det['units'] }}</td>						
                            </tr>
                            <tr> 
                                <td>Total Charges</td>                                            
                                <td class='font600 text-right'>${!! App\Http\Helpers\Helpers::priceFormat($summary_det['charges']) !!}</td>
                            </tr>
                            <tr> 
                                <td>Total Adjustments</td>                                            
                                <td class='font600 text-right'>${!! App\Http\Helpers\Helpers::priceFormat($summary_det['adj']) !!}</td>
                            </tr>                                       
                            <tr> 
                                <td>Total Payments</td>                                            
                                <td class='font600 text-right'>${!! App\Http\Helpers\Helpers::priceFormat($summary_det['pmt']) !!}</td>
                            </tr>
                            <tr> 
                                <td class="font600">Total Balance</td>                                            
                                <td class='font600 med-orange text-right'>${!! App\Http\Helpers\Helpers::priceFormat($summary_det['bal']) !!}</td>
                            </tr>
                    </tbody>
                </table>   
                </div>
            </div>
        </div>
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->