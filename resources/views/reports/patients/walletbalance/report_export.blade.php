<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Wallet Balance</title>
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
        @$patient = $result['patient'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$header = $result['header'];
        @$heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="8" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">Wallet Balance</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;"><span>User :</span><span>@if(Auth::check() && isset(Auth::user()->name)) {{ Auth::user()->name }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">
                    <?php $i = 1; ?>
                    @if(isset($header) && !empty($header))
                    @foreach($header as $header_name => $header_val)
                    <span>
                    <?php $hn = $header_name; ?>
                    {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i < count((array)$header)) | @endif
                    <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOB</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Statements</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Statement Sent</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last Statement Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Wallet Balance($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat AR($)</th>
                </tr>
            </thead>    
            <tbody>
                @if(count((array)$patient)>0)
                    @foreach($patient as $r)
                        <tr>
                            <td>{!! $r->account_no !!}</td>
                            <td>{!! $r->last_name.', '.$r->first_name.' '.$r->middle_name !!}</td>
                            <td>{!! $r->dob !!}</td>
                            <td>{!! $r->statements !!}</td>
                            <td class="text-left" style="text-align:left;">{!! $r->statements_sent !!}</td>
                            <td>{!! $r->last_statement !!}</td>
                            <td class="text-right <?php echo($r->wallet_balance)<0?'med-red':'' ?>" style="text-align:right;<?php echo($r->wallet_balance)<0?'color:#ff0000;':'' ?>" data-format="#,##0.00">{!! $r->wallet_balance !!}</td>
                            <td class="text-right <?php echo($r->patient_balance)<0?'med-red':'' ?>" style="text-align:right;<?php echo($r->patient_balance)<0?'color:#ff0000;':'' ?>" data-format="#,##0.00">{!! $r->patient_balance !!}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>   
        </table>
        <div colspan="8"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
    </body>
</html>