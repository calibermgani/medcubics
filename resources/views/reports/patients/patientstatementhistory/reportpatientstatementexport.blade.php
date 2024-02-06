<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Statement History</title>
        <style>
            table tbody tr  td{
                font-size: 9px !important;
                border: none !important;
            }
            table tbody tr th {
                text-align:center !important;
                font-size:10px !important;                
                color:#000 !important;
                border:none !important;
                border-radius: 0px !important;
            }
            table thead tr th{border-bottom: 5px solid #000 !important;font-size:10px !important}
            .text-right{text-align: right;}
            .text-left{text-align: left;}
            .text-center{text-align: center;}
            .med-green{color: #00877f;}
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>	
    <body>
        <?php 
        @$patient_statementhistory_filter = $result['patient_statementhistory_filter'];
        @$start_date = $result['start_date'];
        @$end_date = $result['end_date'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="8" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">Statement History - Detailed</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;"><span>User :</span><span>@if(Auth::check() && isset(Auth::user()->name)) {{ Auth::user()->name }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span>{!! $key !!} :  </span>{{ @$val[0] }}
                        <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count($patient_statementhistory_filter) > 0)
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">First Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Balance($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;"># of Statements sent</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last Statement date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last payment amount($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last Payment date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patient_statementhistory_filter as $list)
                <?php
                $pat_last_pmt = App\Http\Helpers\Helpers::getPatientLastPaymentAmount($list->patient_id, 'Patient');
                $patPmtDate = isset($pat_last_pmt['created_at']) ? $pat_last_pmt['created_at'] : @$list->latest_payment_date;
                $patPmtAmt = isset($pat_last_pmt['total_paid']) ? $pat_last_pmt['total_paid'] : $list->latest_payment_amt;
                ?>
                <tr>
                    <?php
                    if(isset($list->account_no) && $list->account_no != '') {
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
                    <td class="text-right <?php echo(@$list->balance)<0?'med-red':'' ?>" style="<?php echo(@$list->balance)<0?'color:#ff0000;':'' ?>text-align:right;"  data-format='#,##0.00'>{!! @$list->balance !!}</td>
                    <td class="text-left" style="text-align:left;">{!! @$list->statements !!}</td>
                    <td>
                        {!! (App\Http\Helpers\Helpers::dateFormat(@$list->send_statement_date	) == '01/01/70') ? '-Nil-' : App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$list->send_statement_date	, '', '-Nil-', 'm/d/y') !!}
                    </td>
                    <td class="text-right <?php echo(@$patPmtAmt)<0?'med-red':'' ?>" style="<?php echo(@$patPmtAmt)<0?'color:#ff0000;':'' ?>text-align:right;"  data-format='#,##0.00'>{!! @$patPmtAmt !!}</td>
                    <td>
                        {!! (App\Http\Helpers\Helpers::dateFormat(@$patPmtDate) == '01/01/70') ? '-Nil-' : App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patPmtDate, '', '-Nil-', 'm/d/y') !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        <td colspan="8">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>