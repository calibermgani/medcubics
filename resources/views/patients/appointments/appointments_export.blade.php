<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Patient - Appointments</title>
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
            @$patients = $result['patients'];
            @$patient_appointment = $result['patient_appointment'];
            @$rendering_provider = $result['rendering_provider'];
            @$reason = $result['reason'];
            @$facility = $result['facility'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>
                <td colspan="6" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:center;">Patient Appointments List</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Time</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Provider</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Reason for Visit</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Status</th>
                </tr>
            </thead>    
            <tbody>
                <?php $count = 1;   ?>  
                @if(count((array)@$patient_appointment) > 0 )
                @foreach($patient_appointment as $patient_appointment) 
                @if(@$patient_appointment->patient_id==$patients->id)
                <tr>
                    <td>{{App\Http\Helpers\Helpers::dateFormat($patient_appointment->scheduled_on,'date')}}</td>
                    <?php $s = @str_split(@$patient_appointment->appointment_time, 8); ?>
                    <td>{{ $s[0] }}</td>
                    <td>@if(isset($patient_appointment->provider->provider_name) && $patient_appointment->provider->provider_name!=''){{ @$patient_appointment->provider->short_name }} - {{ @$patient_appointment->provider->provider_name }} @else -Nil- @endif</td>
                    <td>@if(isset($patient_appointment->facility->facility_name) && $patient_appointment->facility->facility_name!=''){{ @$patient_appointment->facility->short_name }} - {{ @$patient_appointment->facility->facility_name }} @else -Nil- @endif</td>
                    <td>{{ @$patient_appointment->reasonforvisit->reason }}</td>
                    <td>{{@$patient_appointment->status}}</td>
                </tr>
                @endif
                @endforeach
                @endif
            </tbody>
        </table>
        <td colspan="6">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>