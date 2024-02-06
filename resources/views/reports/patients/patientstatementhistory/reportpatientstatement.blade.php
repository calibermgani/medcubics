<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body"><!-- Box Body Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange">Statement History - Detailed</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-0 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">
                        <?php $i = 0; ?>
                        @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span class="med-green">{!! $key !!} : </span>{{ @$val[0] }}                           
                        <?php $i++; ?>
                        @endforeach </div>                     
                </div>
            </div>
        </div>
        @if(count($patient_statementhistory_filter) > 0)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
            <div class="box box-info no-shadow no-bottom no-border">
                <div class="box-body no-padding">
                    <div class="table-responsive mobile-md-scroll">
                        <div class="ajax_table_list hide"></div>
                        <div class="data_table_list" id="js_ajax_part">
                            <table id="sort_list_noorder" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Acc No</th>
                                        <th>Last Name</th>
                                        <th>First Name</th>
                                        <th>DOS</th>
                                        <th>Patient Balance($)</th>
                                        <th># of Statements Sent</th>
                                        <th>Last Statement Date</th>        
                                        <th>Last Payment Amount($)</th>        
                                        <th>Last Payment Date</th>         
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient_statementhistory_filter as $list)     
                                    <?php
                                    $pat_last_pmt = App\Http\Helpers\Helpers::getPatientLastPaymentAmount($list->patient_id, 'Patient');
                                    $patPmtDate = isset($pat_last_pmt['created_at']) ? $pat_last_pmt['created_at'] : @$list->latest_payment_date;
                                    $patPmtAmt = isset($pat_last_pmt['total_paid']) ? $pat_last_pmt['total_paid'] : $list->latest_payment_amt;
                                    ?>									
                                    <tr style="cursor:default;">
                                        <?php
                                        if (isset($list->account_no) && $list->account_no != '') {
                                            ?>
                                            <td>{!! @$list->account_no !!}</td>
                                            <td>{!! @$list->last_name !!}</td>
                                            <td>{!! @$list->first_name !!}</td>
                                            <?php
                                        } else {
                                            ?>
                                            <td>{!! @$list->patient_detail->account_no !!}</td>
                                            <td>{!! @$list->patient_detail->last_name !!}</td>
                                            <td>{!! @$list->patient_detail->first_name !!}</td>
                                        <?php } ?>
                                        <td style="word-break:break-all">{!! App\Http\Helpers\Helpers::getIdToDos($list->claim_id_collection) !!}</td>
                                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$list->balance) !!}</td> 
                                        <td>{!! @$list->statements !!}</td>
                                        <td> 
                                            {!! (App\Http\Helpers\Helpers::dateFormat(@$list->send_statement_date	) == '01/01/70') ? '-Nil-' : App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$list->send_statement_date	, '', '-Nil-', 'm/d/y') !!}
                                        </td>
                                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$patPmtAmt) !!}</td>
                                        <td>
                                            {!! (App\Http\Helpers\Helpers::dateFormat(@$patPmtDate) == '01/01/70') ? '-Nil-' : App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patPmtDate, '', '-Nil-', 'm/d/y') !!}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <input type="hidden" id="pagination_prt" value="string"/>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
        </div>
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->