<?php $heading_name = App\Models\Practice::getPracticeName();  ?>
<div class="box box-view no-shadow"><!--  Box Starts -->        

    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>

    <div class="box-body  bg-white"><!-- Box Body Starts -->      
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange">Payer Summary</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-10 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-0 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?> 
                        @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span class="med-green">{!! $key !!} : </span>{{ @$val }}                           
                        <?php $i++; ?>
                        @endforeach 
					</div>                   
                </div>                
            </div>
        </div>
        @if(isset($payers) && !empty($payers))  
        <?php 
        //$total_pmt = $tot_units = $tot_charges = $total_adj = $patient_total = $insurance_total = $total = $count = $payments = 0;
        ?>
        <div class="table-responsive col-lg-12">
                <table class="table table-striped table-bordered dataTable" id="sort_list_noorder_report">  
                    <thead>
                        <tr>
                            <th>Ins Name</th>
                            <th>Ins Type</th>
                            <th>Units</th>
                            <th>Charges($)</th>
                            <th>Adjustments($)</th>
                            <th>Payments($)</th>   
                            <th>Ins Balance($)</th>                    
                        </tr>
                    </thead>
                    <tbody>
						@foreach($payers as  $list)
						<?php
							$insurance_name = $list->insurance_name;
							$insurance_id = $list->insurance_id;
							$insurance_category = @$list->insurance_category;
							$units = isset($unit_details->$insurance_id)?$unit_details->$insurance_id:0;
							$total_charge = isset($charges->$insurance_id)?$charges->$insurance_id:0;
							$adjustment = isset($adjustments->$insurance_id)?$adjustments->$insurance_id:0;
							$pmt = isset($insurance->$insurance_id)?$insurance->$insurance_id:0;
							$ins_bal = isset($insurance_bal->$insurance_id)?$insurance_bal->$insurance_id:0;
						?>
						<tr style="cursor:default;">                       
							<td class="text-left">{{ $insurance_name }}</td>           
							<td class="text-left">{{ $insurance_category }}</td>
							<td class="text-left">{!! $units!!}</td>
							<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($total_charge) !!}</td>
							<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($adjustment) !!} </td>
							<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($pmt) !!}</td>
							<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($ins_bal) !!}</td>                 
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
							<td>Total Units</td>                                            
							<td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat(@$tot_units,'no') !!}</td>
						</tr>
						<tr> 
							<td>Total Charges</td>                                            
							<td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat(@$tot_charges,'no') !!}</td>
						</tr>
						<tr> 
							<td>Total Payments</td>                                            
							<td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat(@$total_pmt,'no') !!}</td>
						</tr>
						<tr> 
							<td>Total Adjustments (With held included)</td>                                            
							<td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat(@$total_adj,'no') !!}</td>
						</tr> 
						<tr> 
							<td>Total Insurance Balance</td>                                            
							<td class='med-green font600 text-right'>{!! App\Http\Helpers\Helpers::priceFormat(@$insurance_total,'no') !!}</td>
						</tr>
					</tbody>
				</table>   
			</div>
		</div>
        
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->