<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important; padding: 10px;
            }
            .summary-table table tbody tr:nth-of-type(odd) td{
                border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;
            }
            th {
                text-align:left !important;
                font-size:10px !important;
                font-weight: 600 !important;
                border-top: 1px solid #ccc;
                border-bottom: 1px solid #ccc;                
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 13px !important;}
            .table-summary tbody tr{line-height: 22px !important;} 
            .table-summary tbody tr td{font-size:11px !important;} 
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right; padding-right:5px;}
            .text-left{text-align: left;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .text-center{text-align: center !important;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
            .med-red {color: #ff0000 !important;}
        </style>
    </head>	
    <body>
        <?php 
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}}</h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>Appointment List</i></p></td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="5" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="4" style="border:none;text-align: right !important">
                        <span>User :</span>
                        <span class="med-orange">
                            {{ Auth::user()->short_name }}
                        </span>
                    </th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if(!empty($patient_app))
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>DOB</th>
                            <th>Facility</th>
                            <th>Rendering Provider</th>
                            <th>Appt Date</th>
                            <th>Appt Time</th>
                            <th>Reason for Visit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>                        
                        @foreach($patient_app as $patient_app)
                        <?php
                            $patient = @$patient_app->patient;
                            $patient_name = App\Http\Helpers\Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
                            $dob = App\Http\Helpers\Helpers::dateFormat(@$patient->dob, 'dob');
                            $provider = @$patient_app->provider;
                            $facility = @$patient_app->facility;
                            $time_arr = explode("-", @$patient_app->appointment_time);
                            $appt_date = App\Http\Helpers\Helpers::timezone(@$patient_app->scheduled_on, 'm/d/y');
                            $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_app->patient_id);
                            $pro_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$provider->id, 'encode');
                            $fac_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$facility->id, 'encode');
                        ?>
                        <tr>
                            <td class="text-left">{{ @$patient->account_no }}</td>
                            <td class="text-left">{{@$patient_name}}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$patient_app->patient->dob,'dob')}}</td>
                            <td>{{ @$patient_app->facility->short_name }}</td>
                            <td>{{ @$patient_app->provider->short_name }}</td>
                            <td>@if(@$patient_app->scheduled_on != "0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat(@$patient_app->scheduled_on,'date') }} @endif</td>
                            <td>{{ @$time_arr[0] }}</td>
                            <td>{{ @$patient_app->reasonforvisit->reason }}</td>
                            <td class="text-left">{{ @$patient_app->status}}</td>
                       </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif
        </div>
    </body>
</html>