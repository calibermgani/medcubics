<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Statement Status</title>
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
        @$patient_statementstatus_filter = $result['patient_statementstatus_filter'];
        @$start_date = $result['start_date'];
        @$end_date = $result['end_date'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="12" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;">Statement Status - Detailed</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;"><span>User :</span><span>@if(Auth::check() && isset(Auth::user()->name)) {{ Auth::user()->name }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span>{!! $key !!} :  </span>{{ @$val[0] }}
                        <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count($patient_statementstatus_filter) > 0)
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOB</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">SSN</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Statements</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;"># of Statements sent</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Hold Reason</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Hold Release Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Statement Category</th>					
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Wallet Balance($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat Balance($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance Balance($)</th>                    
                </tr>
            </thead>
            <tbody>
                @foreach($patient_statementstatus_filter as $list)
                <?php
                $patientName = App\Http\Helpers\Helpers::getNameformat(@$list->last_name, @$list->first_name, @$list->middle_name);
                $stmt_category = isset($list->stmt_category_info->category) ? $list->stmt_category_info->category : "N/A";
                $hold_reason = isset($list->stmt_holdreason_info->hold_reason) ? $list->stmt_holdreason_info->hold_reason : "N/A";
                $hold_release_date = isset($list->hold_release_date) ? $list->hold_release_date : "N/A";
                $wallet_bal = App\Models\Payments\PMTWalletV1::getPatientWalletData($list->id);
                $patPmt = App\Models\Patients\Patient::paymentclaimsum($list->id);
                $insurance_due = isset($patPmt['tins_due']) ? $patPmt['tins_due'] : 0;
                $patient_due = isset($patPmt['tpat_due']) ? $patPmt['tpat_due'] : 0;
                ?>
                <tr>
                    <td>{!! @$list->account_no !!}</td>
                    <td>{!! $patientName !!}</td>
                    <td>{!! (App\Http\Helpers\Helpers::dateFormat(@$list->dob) == '01/01/70') ? '-Nil-' : App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$list->dob, '', '-Nil-', 'm/d/Y') !!}</td>
                    <td style="text-align:left;width:15px;">@if(@$list->ssn != '') {!! @$list->ssn !!} @else -Nil- @endif</td>
                    <td>{!! @$list->statements !!}</td>
                    <td class="text-left" style="text-align:left;">{!! @$list->statements_sent !!}</td>
                    <?php 
                    if(isset($list->holdReason) && $list->holdReason != ''){
                    ?>
                    <td>{!! @$list->holdReason !!}</td>
                    <?php 
                        } else {
                    ?>
                    <td>{!! @$hold_reason !!}</td>
                    <?php } ?>
                    <td>{!! (App\Http\Helpers\Helpers::dateFormat(@$hold_release_date) == '01/01/70') ? '-Nil-' : App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$hold_release_date, '', '-Nil-', 'm/d/y') !!}
                    </td>
                    <?php 
                    if(isset($list->category) && $list->category != ''){
                    ?>
                    <td>{!! @$list->category !!}</td>
                    <?php 
                        } else {
                    ?>
                    <td>{!! @$stmt_category !!}</td>
                    <?php } ?>
                    <td  style="<?php echo(@$wallet_bal)<0?'color:#ff0000;':'' ?>"  data-format='#,##0.00'><?php echo(isset($wallet_bal))?$wallet_bal : '0' ?></td>
                    <td style="<?php echo(@$patient_due)<0?'color:#ff0000;':'' ?>"  data-format='#,##0.00'>{!! @$patient_due !!}</td>
                    <td  style="<?php echo(@$insurance_due)<0?'color:#ff0000;':'' ?>"  data-format='#,##0.00'>{!! @$insurance_due !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        <td colspan="12">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>