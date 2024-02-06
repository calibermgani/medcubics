<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Fee Schedule</title>
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
            $feeschedules = $result['feeschedules'];
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="6" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="6" style="text-align:center;">Fee Schedule List</td>
            </tr>
            <tr>
                <td valign="middle" colspan="6" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">File Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Year</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Percentage</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Created By</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Uploaded On</th>
                </tr>
            </thead>    
            <tbody>
                @if(count($feeschedules) > 0)
                @foreach($feeschedules as $feeschedule)                
                <?php
                    if(@$feeschedule->fee_schedule->file_name!=''){
                        $feeschedule_arr = explode(".",@$feeschedule->fee_schedule->file_name);
                        $file_name       = $feeschedule_arr[0];
                    }else{
                        $file_name = '';
                    }
                ?>
                <tr>
                    <td>{{ @$file_name }}</td>
                    <td class="text-left">{{ @$feeschedule->fee_schedule->choose_year }}</td>
                    <td>{{ (isset($feeschedule->insurance_info->short_name) && !empty($feeschedule->insurance_info->short_name) ? $feeschedule->insurance_info->short_name : 'Default') }}</td>
                    <td class="text-left">@if(@$feeschedule->fee_schedule->percentage != '') {{ @$feeschedule->fee_schedule->percentage }} @else - @endif</td>
                    <td>@if(@$feeschedule->fee_schedule->created_by != ''){{ App\Http\Helpers\Helpers::shortname(@$feeschedule->fee_schedule->created_by) }}@endif</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$feeschedule->fee_schedule->created_at, 'date') }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="6">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>