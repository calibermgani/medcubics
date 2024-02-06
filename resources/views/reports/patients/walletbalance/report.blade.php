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
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange">Wallet Balance</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">
               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-0 no-padding">
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
        
        @if(count($patient) > 0)
        <div class="table-responsive col-lg-12">
            <table class="table table-striped table-bordered" id="sort_list_noorder">
                <thead>
                    <tr>
                        <th>Acc No</th>
                        <th>Patient Name</th>
                        <th>DOB</th>
                        <th>Statements</th>
                        <th>Statement Sent</th>
                        <th>Last Statement Date</th>
                        <th>Wallet Balance($)</th>
                        <th>Pat AR($)</th>
                    </tr>
                </thead>
                <tbody>
                        @foreach($patient as $r)
                        <?php $patient_id = base64_encode($r->id);?>
                        <tr style="cursor:default;">
                            <td>{!! $r->account_no !!}</td>
                            <td><a href="{{url('patients/'.$patient_id.'/ledger')}}" target="_blank">{!! $r->last_name.', '.$r->first_name.' '.$r->middle_name !!}</a></td>
                            <td>{!! $r->dob !!}</td>
                            <td>{!! $r->statements !!}</td>
                            <td>{!! $r->statements_sent !!}</td>
                            <td>{!! $r->last_statement !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->wallet_balance) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->patient_balance) !!}</td>
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
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->
