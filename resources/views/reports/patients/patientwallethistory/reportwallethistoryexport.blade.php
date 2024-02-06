<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Wallet History</title>
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
        @$patient_wallethistory_filter = $result['patient_wallethistory_filter'];
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
                <td colspan="8" style="text-align:center;">Wallet History - Detailed</td>
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
        @if(count((array)$patient_wallethistory_filter) > 0)
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payment ID</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Mode of Pmt</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payment Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total Payment($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Posted($)</th>  		
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">UnPosted($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patient_wallethistory_filter as $list)
                <?php
                    $patient_name = App\Http\Helpers\Helpers::getNameformat(@$list->patient->last_name, @$list->patient->first_name, @$list->patient->middle_name);
                    $bal_amt = @$list->pmt_amt - @$list->amt_used;
                ?>
                <tr>
                    <?php /* patients details from SP  */
                    if(isset($list->account_no) && $list->account_no != ''){
                    ?>
                    <td>{!! @$list->account_no !!}</td>
                    <td>{!! @$list->patient_name  !!}</td>
                    <?php 
                        } else {
                    ?>
                    <td>{!! @$list->patient->account_no !!}</td>
                    <td>{!! $patient_name !!}</td>
                    <?php } ?>
                    <td style="text-align:left;width:30px;">{!! @$list->pmt_no !!}</td>
                    <td>{!! @$list->pmt_mode !!}</td>
                    <td>{{ App\Http\Helpers\Helpers::timezone(@$list->created_at, 'm/d/y') }}</td>
                    <td class="text-right <?php echo(@$list->pmt_amt)<0?'med-red':'' ?>" style="text-align:right;<?php echo(@$list->pmt_amt)<0?'color:#ff0000;':'' ?>"  data-format='#,##0.00'>{!! @$list->pmt_amt !!}</td>
                    <td class="text-right <?php echo(@$list->amt_used)<0?'med-red':'' ?>" style="text-align:right;<?php echo(@$list->amt_used)<0?'color:#ff0000;':'' ?>"  data-format='#,##0.00'>{!! @$list->amt_used !!}</td>
                    <td class="text-right <?php echo(@$bal_amt)<0?'med-red':'' ?>" style="text-align:right;<?php echo(@$bal_amt)<0?'color:#ff0000;':'' ?>"  data-format='#,##0.00'>{!! @$bal_amt !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        <div colspan="8"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
    </body>
</html>