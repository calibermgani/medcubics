<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Questionnaires Template</title>
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
                <td colspan="5" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="5" style="text-align:center;">Questionnaires Template List</td>
            </tr>
            <tr>
                <td valign="middle" colspan="5" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Title</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Created By</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Created On</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Updated By</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Updated On</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($questionnaries as $questionnaries)
                <tr>
                    <td>{{ $questionnaries->title }}</td>
                    <td>{{ App\Http\Helpers\Helpers::shortname($questionnaries->created_by) }}</td>
                    <td>
                        @if($questionnaries->created_at !='' && $questionnaries->created_at !='-0001-11-30 00:00:00' && $questionnaries->created_at !='0000-00-00 00:00:00')
                            {{ App\Http\Helpers\Helpers::dateFormat($questionnaries->created_at,'date') }}
                        @endif
                    </td>
                    <td>{{ App\Http\Helpers\Helpers::shortname($questionnaries->updated_by) }}</td>
                    <td>
                        @if($questionnaries->updated_at !='' && $questionnaries->updated_at !='-0001-11-30 00:00:00' && $questionnaries->updated_at !='0000-00-00 00:00:00')
                            {{ App\Http\Helpers\Helpers::timezone($questionnaries->updated_at, 'm/d/y') }}
                        @endif
                    </td>
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