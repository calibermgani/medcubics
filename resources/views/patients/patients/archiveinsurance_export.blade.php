<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Patient - Insurance Archive</title>
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
            $archiveinsurance = $result['archiveinsurance'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>                   
                <td colspan="5" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:center;">Patient Insurance Archive</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->short_name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Category</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insured</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Policy ID</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">From / To</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($archiveinsurance as $archiveinsurance_val)
                <?php 
                    $ins_arc_from = App\Http\Helpers\Helpers::dateFormat(@$archiveinsurance_val->active_from,'date');
                    $ins_arc_to   = App\Http\Helpers\Helpers::dateFormat(@$archiveinsurance_val->active_to,'date');  
                ?>
                <tr>
                    <td>{{ @$archiveinsurance_val->insurance_details->insurance_name }}</td>        
                    <td>{{ @$archiveinsurance_val->category }}</td>
                    <td>{{ @$archiveinsurance_val->relationship }}</td>
                    <td style="text-align:left;">{{ @$archiveinsurance_val->policy_id }}</td>
                    <td>@if(@$archiveinsurance_val->active_from !='0000-00-00 00:00:00')
                        [ {{ @$ins_arc_from }} To {{ @$ins_arc_to }} ] @else -  @endif </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="5">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>