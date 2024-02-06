<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
        </div>
    </div>
    <div class="box-body bg-white border-radius-4"><!-- Box Body Starts -->
        {{-- @if($header !='' && count($header)>0) --}}
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px dashed #f0f0f0;">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange" >
                <div class="margin-b-15">Customer Report- Detailed</div>
            </h3>
            {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"> --}}
                {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding text-center"> --}}
                    <?php //$i=1; ?>
					{{-- @foreach($header as $header_name => $header_val) --}}
						{{-- <span class="med-green"> --}}
							<?php //$hn = $header_name; ?>
							{{-- {{ @$header_name }} --}}
                        {{-- </span> : {{str_replace('-','/', @$header_val)}} --}}
                        {{-- @if($i < count((array)$header) | @endif  --}}
                        <?php// $i++; ?>
					{{-- @endforeach --}}
                {{-- </div> --}}
                <?php
                // $date_cal = json_decode(json_encode($header), true);
                // $trans = str_replace('-', '/', @$date_cal['Transaction Date']);
                // $dos = str_replace('-', '/', @$date_cal['Date Of Service']);
                ?>
            {{-- </div> --}}
        </div>
        {{-- @endif --}}
        @if(count((array)$customers) >0)
        @foreach($customers as $customers_list)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="box box-info no-shadow no-border no-bottom">
                <div class="box-body">
                    <div class="table-responsive">
                        {{-- {{dd($customers)}} --}}
                        <?php //$count = 0;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; $claim_bal_total = $total_claim = $total_cpt =  0;   ?>
                        {{-- @foreach($customers as $customers_list) --}}
                            <?php
                             //$customers_list = $customers;
    							// $patient = $claims_list->patient;
    							// $set_title = (@$patient->title)? @$patient->title.". ":'';
    							// $patient_name = 	$set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name); 
    								
    						?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border yes-border margin-t-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600">Customer Details </span>
                                
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Customer Name:</label>
                                    {{ $customers_list->customer_name }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Description:</label>
                                    {{ $customers_list->customer_desc }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Customer Type: </label>
                                    {{ $customers_list->customer_type }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Contact Person:</label>
                                    {{ $customers_list->contact_person }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="rendering" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Designation:</label>
                                    {{ $customers_list->designation }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Gender:</label>
                                    {{ $customers_list->gender }}
                                </div>                                                       
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Address: </label>
                                    {{ $customers_list->addressline1 }} , {{ $customers_list->addressline2 }} , {{ $customers_list->city }} ,
                                    {{ $customers_list->state }}        
                                </div>                                
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Phone:</label>
                                    {{ $customers_list->phone }} ({{ $customers_list->phoneext }})
                                </div>                                
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="entrydate_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Mobile: </label>
                                    <span class="bg-date">	{{ $customers_list->mobile }} </span>
                                    
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="entrydate_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Fax:</label>
                                    <td>  {{ $customers_list->fax }} </td>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                    <label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Created By:</label>
                                    <?php 
                                    $customer_id = $customers_list->id;
                                    $created_by = App\Http\Helpers\Helpers::shortname($customer_id); ?> 
                                    {{ $created_by }}
                                </div>
                            </div> 
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <table class="popup-table-wo-border table table-responsive" style="margin-bottom: 5px; margin-top: 5px;">                    
                                    <thead>
                                        <!-- Claim Header -->
                                        <tr>
                                            <th  class="text-left" style="background: #d9f3f0; color: #00877f;">Practice Name (Short Name) </th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">No. of Rendering Providers</th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">No. of Billing Providers</th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">No. of Referring Providers</th>                                        
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">Total Charges Count</th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">Total Charges Amount</th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">Insurance Payment</th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">Patient Payment</th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">Total Payments</th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">Statements Sent</th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">Tickets Raised</th>
                                            <th class="text-left" style="background: #d9f3f0; color: #00877f;">Total Users</th>
                                        </tr>
                                    </thead>                
                                    <tbody> 
                                        <!-- Claim Row -->
                                        {{-- @if($customers_list->id == ) --}}
                                        @if(count((array)$metrics) > 0)
                                        @foreach($metrics as $metrics_list)
                                        @if(array_key_exists("5", $metrics_list))
                                        @if($customers_list->id == $metrics_list[5])	
                                        <tr>                              
                                            <td style="text-align:center;">{{ $metrics_list[0]->practice_name }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[0]->Rendering }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[0]->Billing }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[0]->Referring }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->total_charge_count }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->total_charge_sum }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->insurance_paid_sum }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->patient_paid_sum }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->insurance_paid_sum + $metrics_list[1]->patient_paid_sum }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[2]->statements_total }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[3]->ticket_count }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[4] }}</td>     
                                        </tr>
                                        @else
                                        @endif
                                        @else
                                        <tr>                              
                                            <td style="text-align:center;">{{ $metrics_list[0]->practice_name }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[0]->Rendering }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[0]->Billing }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[0]->Referring }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->total_charge_count }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->total_charge_sum }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->insurance_paid_sum }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->patient_paid_sum }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[1]->insurance_paid_sum + $metrics_list[1]->patient_paid_sum }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[2]->statements_total }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[3]->ticket_count }}</td>
                                            <td style="text-align:center;">{{ $metrics_list[4] }}</td>     
                                        </tr>
                                        @endif

                                        @endforeach
                                        @else
                                        <!-- Claim Total Row -->
                                        <tr>                              
                                            <td class="text-center" colspan="12">No Records Found!!</td>     
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- @endforeach --}}
                        {{-- $count++;  --}}
                        
                        {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-10 no-padding dataTables_info">
                                Showing  to  of entries
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding margin-t-m-10"></div>
                        </div> --}}
                        {{-- @if(@$pagination->last_page != 1)
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-list-bg hide">
                            <label for="name" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 font600">Charge Count : <span class="med-orange">{{ @count((array)$sinpage_claim_arr) }}</span></label>
                            <label for="name" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 font600">Charge Value : <span class="med-orange">${{@$sinpage_charge_amount}}</span></label>
                            <label for="name" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 font600">No. of CPT Billed : <span class="med-orange">{{@$sinpage_total_cpt}}</span></label>
                        </div>
                            @endif --}}
                        {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 no-padding">
                                <div class="box-header-view-white no-border-radius pr-t-5 margin-b-5">
                                    <i class="fa fa-bars"></i><strong class="med-orange font13"> Summary</strong>                     
                                </div><!-- /.box-header -->
                                <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4" style="border: 1px solid #00877f;">    
                                    <thead>
                                        <th></th>
                                        <th class="text-left">Counts</th>
                                        <th class="text-right">Value</th>
                                    </thead>
                                    <tbody>
                                            <tr> 
                                                <td class='med-green font600' >Total Patients</td>
                                                <td class="text-left">{{$tot_summary->total_patient}}</td>
                                                <td class="text-right">${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                                            </tr>
                                            <tr>
                                                <td class='med-green font600' >Total CPT</td>
                                                <td class="text-left">{{$tot_summary->total_cpt}}</td>
                                                <td class="text-right">${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                                            </tr>
                                            <tr>
                                                <td class='med-green font600' >Total Units</td>
                                                <td class="text-left">{{$tot_summary->total_unit}}</td>
                                                <td class="text-right">${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                                            </tr>
                                            <tr>
                                                <td class='med-green font600' >Total Charges</td>
                                                <td class="text-left">{{ @$tot_summary->total_claim }}</td>
                                                <td class="text-right">${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}}</td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> --}}
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>
        @endforeach
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5>No Records Found !!</h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->