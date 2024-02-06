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
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Insurance Over Payment</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">
                        </script> 
<?php $i=1; ?>
                        @if(isset($header) && !empty($header))
                        @foreach($header as $header_name => $header_val)
                            <span class="med-green">
                                <?php $hn = $header_name; ?>
                                {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i<count((array)$header)) | @endif </script> 
<?php $i++; ?> 
                        @endforeach
                        @endif
                    </div>                    
                </div>
              
            </div>
        </div>
        
        @if(count($overpayment) > 0)
        <div class="table-responsive col-lg-12">
            <table class="table table-striped table-bordered" id="sort_list_noorder">
                <thead>
                    <tr>
                        <th>Claim No</th>
                        <th>DOS</th>
                        <th>Acc No</th>
                        <th>Patient Name</th>
                        <th>Billing</th>
                        <th>Facility</th>
                        <th>Transaction Date</th>
                        <th>Charge Amt($)</th>
                        <th>Adjustments($)</th>
                        <th>Payments($)</th>
                        <th>AR Due($)</th>
                    </tr>
                </thead>
                <tbody>
                        @foreach($overpayment as $r)
                        <tr style="cursor:default;">
                            <td>{!! $r->claim_number !!}</td>
                            <td>{!! $r->dos !!}</td>
                            <td>{!! $r->account_no !!}</td>
                            <td>{!! $r->last_name.', '.$r->first_name.' '.$r->middle_name !!}</td>
                            <td>{!! $r->provider_short_name !!}</td>
                            <td>{!! $r->facility_short_name !!}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat($r->date, 'date') }}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->total_charge) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->adjustment) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->insurance_paid) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->ar_due) !!}</td>
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
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->