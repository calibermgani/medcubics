<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Appointment List</title>
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
            @$patient_app = $result['patient_app'];
            @$headers = $result['search_by'];
            @$title = $result['title'];
            @$createdBy = $result['createdBy'];
            @$practice_id = $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>
                <td colspan="9" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="9" style="text-align:center;">{{ @$title }}</td>
            </tr>
            <tr>
                <td colspan="9" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
     
        <table>
          <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">DOB</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Rendering Provider</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Appt Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Appt Time</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Reason for Visit</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Status</th>
                </tr>
            </thead>   
            <tbody>
                 @if(!empty($patient_app))  
                @foreach($patient_app as $patient_app)
                    <?php
                        $patient = @$patient_app->patient;
                        $patient_name = App\Http\Helpers\Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->center_name);
                        $dob = App\Http\Helpers\Helpers::dateFormat(@$patient->dob, 'dob');
                        $provider = @$patient_app->provider;
                        $facility = @$patient_app->facility;
                        $time_arr = explode("-", @$patient_app->appointment_time);
                        $appt_date =App\Http\Helpers\Helpers::timezone(@$patient_app->scheduled_on, 'm/d/y');
                        $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_app->patient_id);
                        $pro_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$provider->id, 'encode');
                        $fac_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$facility->id, 'encode');
                    ?>
                <tr>
                    <td style="text-align:left;">{{ @$patient->account_no }}</td>
                    <td style="text-align:left;">{{@$patient_name}}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$patient_app->patient->dob,'dob')}}</td>
                    <td>@if($patient_app->facility->facility_name !=''){{ @$patient_app->facility->short_name }} - {{ @$patient_app->facility->facility_name }} @else -Nil- @endif</td>
                    <td>@if($patient_app->provider->provider_name !=''){{ @$patient_app->provider->short_name }} - {{ @$patient_app->provider->provider_name }} @else -Nil- @endif</td>
                    <td>@if(@$patient_app->scheduled_on != "0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat(@$patient_app->scheduled_on,'date') }} @endif</td>
                    <td>{{ @$time_arr[0] }}</td>
                    <td>{{ @$patient_app->reasonforvisit->reason }}</td>
                    <td style="text-align:left;">{{ @$patient_app->status}}</td>
                </tr>
                @endforeach
                   @endif
            </tbody>   
        </table>
        <td colspan="9">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>