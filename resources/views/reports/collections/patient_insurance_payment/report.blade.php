<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->		

    <div class="box-header-view">
        <i class="fa fa-user" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>

    <div class="box-body  bg-white"><!-- Box Body Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Patient and Insurance Payment</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">
                        <?php $i=1; ?>
                        @if(isset($header) && !empty($header))
                        @foreach($header as $header_name => $header_val)
                            <span class="med-green">
                                <?php $hn = $header_name; ?>
                                {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i<count((array)$header)) | @endif 
								<?php $i++; ?>
                        @endforeach
                        @endif
                    </div>                    
                </div>
              
            </div>
        </div>
        @if(count((array)$payment) > 0)<?php //dd($header);?>
        <div class="table-responsive col-lg-12">
            <table class="table table-striped table-bordered" id="sort_list_noorder">
                <thead>
                    <tr>
                        <th>Transaction Date</th>
                        <th>Acc No</th>
                        <th>Patient Name</th>
                        @if($header['Payer']!="Patient Payments")
                        <th>DOS</th>
                        <th>Claim No</th>
                        @endif
                        <th>Payer</th>
                        <th>Payment Type</th>
                        <th>Check/EFT/CC/MO No</th>
                        <th>Check/EFT/CC/MO Date</th>
                        <th>Paid($)</th>
                        <th>Reference</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                        @foreach($payment as $key => $r)
                        <tr style="cursor:default;">
                            <?php $title = !empty($r->title)?$r->title.'. ':''; ?>
                            <?php /* from stored procedure  */
                                if(isset($r->patient_name) && $r->patient_name != ''){
                            ?>
                            <td>{!! !empty($r->transaction_date)? App\Http\Helpers\Helpers::timezone($r->transaction_date) : '-Nill-' !!}</td>
                            <td>{!! !empty($r->account_no)? $r->account_no : '-Nill-' !!}</td>
                            <td>{!! !empty($r->patient_name)? $r->patient_name : '-Nill-' !!}</td>
                            <?php 
                                } else {
                            ?>
                            <td>{{ !empty($r->transaction_date)? App\Http\Helpers\Helpers::timezone($r->transaction_date, 'm/d/y') : '-Nill-' }}</td>
                            <td>{!! !empty($r->account_no)? $r->account_no : '-Nill-' !!}</td>
                            <?php $patientName = @$r->last_name.', '.@$r->first_name.' '.@$r->middle_name ?>
                            
                            <td>{!! (!empty($patientName) && $patientName != ',  ')? $title.@$r->last_name.', '.@$r->first_name.' '.@$r->middle_name : '-Nill-' !!}</td>
                            <?php } ?>
                            @if($header['Payer']!="Patient Payments")
                            <td>@if( (isset($r->payer) && $r->payer=="Patient") && $header['Payer']!="All Payments")-Nil- @else {!! ($r->dos=='')?'-Nil-':$r->dos !!} @endif</td>
                            <td>@if( (isset($r->payer) && $r->payer=="Patient") && $header['Payer']!="All Payments")-Nil- @else {!! ($r->claim_number=='')?'-Nil-':$r->claim_number !!} @endif</td>
                            @endif
                            <td>{!! !empty($r->payer)? $r->payer : '-Nill-' !!}</td>
                            <td>{!! !empty( $r->pmt_mode)? $r->pmt_mode : '-Nill-' !!}</td>
                            <td>{!! ($r->pmt_mode_no=='')?'-Nill-':$r->pmt_mode_no !!}</td>
                            <td>{!! ($r->pmt_mode_date=='')?'-Nill-':$r->pmt_mode_date !!}</td>
                            @if(isset($r->payer) && $r->payer=="Patient")
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->total_paid) !!}</td>
                            @else
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->total_paid) !!}</td>
                            @endif
                            <td>{!! (!empty($r->reference))?$r->reference:"-Nil-" !!}</td>
                            <td>
                                @if($r->created_by != 0 && isset($user_names[@$r->created_by]) )
                                    {!! $user_names[@$r->created_by] !!}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                </tbody>
            </table>    
        </div>
         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-10 no-padding dataTables_info">
                Showing {{@$pagination['from']}} to {{@$pagination['to']}} of {{@$pagination['total']}} entries
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding margin-t-m-10">{!! $pagination['pagination_prt'] !!}</div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border-top: 1px solid #f0f0f0;">
            <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 no-padding margin-t-5">
                <div class="box-header-view-white no-border-radius pr-t-5 margin-b-10">
                    <i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>                     
                </div><!-- /.box-header -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10">
                    <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4 summary-box" style="">    
                        <tbody>
                        @if(isset($header['Transaction Date']))
                        <tr>
                            <td>Transaction Date</td>
                            <td class="med-green font600 text-right">{{$header['Transaction Date']}}</td>
                        </tr>
                        @endif
                        @if(isset($header['Payer']) && $header['Payer']=="Patient Payments" || $header['Payer']=="All Payments")
                        <tr>
                            <td>Total Patient Payments</td>
                            <td class=" font600 text-right">${!! App\Http\Helpers\Helpers::priceFormat($patient_total) !!}</td>
                        </tr>
                        @endif
                        @if(isset($header['Payer']) && $header['Payer']=="Insurance Payments" || $header['Payer']=="All Payments")
                        <tr>
                            <td>Total Insurance Payments</td>
                            <td class=" font600 text-right">${!! App\Http\Helpers\Helpers::priceFormat($insurance_total) !!}</td>
                        </tr>
                        @endif
                        @if(isset($header['Payer']) && $header['Payer']=="All Payments")
                        <tr>
                            <td class="font600">Total Payments</td>
                            <td class=" font600 text-right">${!! App\Http\Helpers\Helpers::priceFormat($patient_total+$insurance_total) !!}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>  
                </div>
            </div>
        </div>
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->