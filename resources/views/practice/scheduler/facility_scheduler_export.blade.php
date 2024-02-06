<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Facility Scheduler</title>
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
            $facility_scheduler = $result['facility_scheduler'];
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="7" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="7" style="text-align:center;">Facility Scheduler</td>
            </tr>
            <tr>
                <td valign="middle" colspan="7" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Short Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Specialty</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">POS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">City</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">State</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Scheduled</th>
                </tr>
            </thead>    
            <tbody>
                @if(count($facility_scheduler) > 0)
                @foreach($facility_scheduler as $facility)
                <?php
                    $facility_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'encode');
                    $scheduled_count = App\Models\ProviderScheduler::getScheduledCountByProviderId($facility_id,'Facility');
                    $scheduled = (isset($scheduled_count)&&($scheduled_count>0)) ? 'Yes' : 'No';
                ?>
                <tr>
                    <td>{{ @$facility->short_name }}</td>
                    <td>{{ @$facility->facility_name }}</td>
                    <td>{{ @$facility->speciality_details->speciality }}</td>    
                    <td>{{ @$facility->pos_details->code }}</td>        
                    <td>{{ @$facility->facility_address->city }}</td> 
                    <td>{{ @$facility->facility_address->state }}</td>            
                    <td>{{ $scheduled }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="7">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>