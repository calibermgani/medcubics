<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Provider Managed Care</title>
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
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="7" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="7" style="text-align:center;">Provider Managed Care</td>
            </tr>
            <tr>
                <td valign="middle" colspan="7" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Provider ID</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Credential</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Entity Type</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Effective Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Termination Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Fee Schedule</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($managecare as $managecare)
                <tr>
                    <td>@if($managecare->insurance){{ $managecare->insurance->insurance_name }}@endif</td>
                    <td class="text-left" style='text-align: left;'>{{ $managecare->provider_id }}</td>
                    <td>{{ $managecare->enrollment }}</td>
                    <td>{{ $managecare->entitytype }}</td>
                    <td>{{ ($managecare->effectivedate != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($managecare->effectivedate,'date') : '' }}</td>
                    <td>{{ ($managecare->terminationdate != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($managecare->terminationdate,'date') : '' }}</td>
                    <td>@if($managecare->feeschedule == '') - Nil - @else {{ $managecare->feeschedule }}@endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="7">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>