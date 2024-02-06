<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Adjustment Reason</title>
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
                <td colspan="8" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">Adjustment Reason</td>
            </tr>
            <tr>
                <td valign="center" colspan="8" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->short_name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">S.No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Adjustment Type</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Adjustment ShortName</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Adjustment Reason</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Status</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Created By</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Updated By</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Updated On</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($adjustmentreason as $keys=> $adjustment)
                <tr>
                    <td>{{$keys+1}}</td>
                    <td>{{ @$adjustment->adjustment_type }}</td>
                    <td>{{ @$adjustment->adjustment_shortname }}</td>
                    <td>{{ @$adjustment->adjustment_reason }}</td>
                    <td>{{ @$adjustment->status }}</td>
                    <td>{{ App\Http\Helpers\Helpers::shortname($adjustment->created_by) }}</td>
                    <td>{{ App\Http\Helpers\Helpers::shortname($adjustment->updated_by) }}</td>
                    <td>
                        @if($adjustment->updated_by !='' && $adjustment->updated_by !='0')
                            {{ App\Http\Helpers\Helpers::dateFormat($adjustment->updated_at, 'date') }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <td colspan="8">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>