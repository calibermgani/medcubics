<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->		

    <div class="box-header-view">
        <i class="fa fa-user" data-name="info"></i> <h3 class="box-title">Payer Analysis</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
        </div>
    </div>


    <div class="box-body  bg-white"><!-- Box Body Starts -->        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25">Payer Analysis</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><span class="med-green">Transaction Date</span> : {!! $start_date !!}  to {!! $end_date !!}</div>                    
                </div>
              
            </div>
        </div>
        
        <div class="table-responsive col-lg-12">
            <table class="table table-striped table-bordered" id="sort_list_noorder">
                <thead>
                    <tr>

                        <th>Payer</th>
                        <th>Billed($)</th>
                        <th>Paid($)</th>
                        <th>Adj($)</th>
                        <th>Balance($)</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($claim_details) > 0)
                    @php
						$total_billed = 0;
						$total_paids  = 0;
						$total_adjusted = 0;
						$total_balanced = 0;
					@endphp
                    @foreach($claim_details as $list)
                    @php
						$total_billed = $total_billed + $list->Billed;
						if($list->insurance_details != NULL)
							$total_paids = $total_paids + $list->insurancepaid;
						else
							$total_paids = $total_paids + $list->patientpaid;
						$total_adjusted = $total_adjusted + $list->adjusted;
						$total_balanced = $total_balanced + $list->balanced;
					@endphp
                    <tr style="cursor:default;">
                        @if($list->insurance_details != NUll)
                        <td>{!! $list->insurance_details->insurance_name !!}</td>
                        <td class="text-right">{!! $list->Billed !!}</td>
                        <td class="text-right">{!! $list->insurancepaid !!}</td>
                        <td class="text-right">{!! $list->adjusted !!}</td>
                        <td class="text-right">{!! $list->balanced !!}</td>
                        @else
                        <td>Patient</td>
                        <td class="text-right">{!! $list->Billed !!}</td>
                        <td class="text-right">{!! $list->patientpaid !!}</td>
                        <td class="text-right">{!! $list->adjusted !!}</td>
                        <td class="text-right">{!! $list->balanced !!}</td>
                        @endif   	
                    </tr>
                    @endforeach
                </tbody>
            </table>    
        </div>
         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-10 no-padding dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding margin-t-m-10">{!! $pagination->pagination_prt !!}</div>
        </div>


        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border-top: 1px solid #f0f0f0;">
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
                            <td class="med-green">Total Billed</td>                                            
                            <td class='med-green font600 text-right' >{!! App\Http\Helpers\Helpers::priceFormat($total_billed) !!}</td>						
                        </tr>

                        <tr> 
                            <td class="med-green">Total Paid</td>                                            
                            <td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat($total_paids) !!}</td>                                            
                        </tr>

                        <tr> 
                            <td class="med-green">Total Adjustments</td>                                            
                            <td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat($total_adjusted) !!}</td>                                            
                        </tr>

                        <tr> 
                            <td class="med-green">Total Balance</td>                                            
                            <td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat($total_balanced) !!}</td>                                            
                        </tr>

                    </tbody>
                </table>   
            </div>
        </div>
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5>No Records Found !!</h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->
