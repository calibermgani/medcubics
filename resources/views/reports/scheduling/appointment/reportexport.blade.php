<html>
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important;
            } 
            .print-table table  tbody tr{ line-height: 26px !important; }
            .print-table table  tbody tr:nth-of-type(even) {
                background: #f3fffe !important;   
            }
            table tbody tr:nth-of-type(even) td{border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;}
            th {
                text-align:left;
                font-size:13px;
                font-weight: 100 !important;
                border-radius: 0px !important;
            }
            tr, tr span, th, th span{line-height: 20px;}
            //@page { margin: 0px; margin-right: -20px;}
            @page { margin: 110px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:13px; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #00877f;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .c-paid, .Paid{color:#02b424;}
            .c-denied, .Denied{color:#d93800;}
            .m-ppaid, .c-ppaid{color:#2698F8}
            .ready-to-submit, .Ready{color:#5d87ff;}
            .Rejection{color: #f07d08;}
            .Hold{color:#110010;}
            .claim-paid{background: #defcda; color:#2baa1d !important;}
            .claim-denied{ color:#d93800 !important;}
            .claim-submitted{background: #caf4f3; color:#41a7a5 !important}
            .claim-ppaid{background: #dbe7fe; color:#2f5dba !important;}
            .Patient{color:#e626d6;}
            .Submitted{color:#009ec6;}
            .Pending{color:#313e50;}
            .text-center{text-align: center;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
        </style>
    </head>	
    <body>
        <div class="header">
            <table style="background:#f0f0f0; padding: 0px 0px; margin-top: -20px;">
                <tr style="background:#f0f0f0;">
                    <td colspan="10"><h3 class="text-left" style="margin-left:10px;" >Appointment Analysis</h3> </td>
                    <td colspan="3" class=""><p style="float:right; text-align: right;margin-right: 30px;"><span>Page No :</span> <span class="med-green pagenum"></span></p> </td>
                </tr>
            </table>
            <table style="border-spacing: 0px;width:97%; margin-left: 10px; margin-top: 15px; border-bottom: 1px dashed #f0f0f0; padding-bottom: 15px;">
                <tr>
                    <th style=""><span>Created:</span> <span class="med-green">{{ date("m/d/y") }} - </span><span class="med-orange">{{ date("H:i A") }}</span></th>
                    <th colspan="2" style=""><span>User :</span> <span class="med-green">{{ Auth::user()->name }}</span></th>
                    @if($header !='' && count($header)>0)
                    @foreach($header as $header_name => $header_val)
                    <th colspan="1" style=""><span>{{ @$header_name }} :</span>  <span class="med-green">{{ @$header_val }}</span></th>
                    @endforeach
                    @endif
                </tr>              
            </table>
        </div>
        @if($export == "pdf")
        <div colspan="10" class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
        <div style="padding-top:10px;">
            <div class="print-table" style="">
                <table class="table-bordered" style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 96%; margin-left: 10px;  ">
                    <thead>
                        <tr style="background: #00837C; color: #fff;">
                            <th style="border-right:1px solid #fff !important;">Patient Name</th>
                            <th style="border-right:1px solid #fff !important;">Acc No</th>
                            <th style="border-right:1px solid #fff !important;">Created Date</th>
                            <th style="border-right:1px solid #fff !important;">Appt. Date</th>
                            <th style="border-right:1px solid #fff !important;">Appt. Time</th>
                            @if(@$column->provider =='')
                            <th style="border-right:1px solid #fff !important;">Provider</th>
                            @endif	
                            @if(@$column->facility =='')
                            <th style="border-right:1px solid #fff !important;">Facility</th>
                            @endif
                            <th style="border-right:1px solid #fff !important;">Status</th>
                            @if(@$column->patient_type =="")
                            <th style="border-right:1px solid #fff !important;">Patient</th>
                            @endif	
                            <th style="border-right:1px solid #fff !important;">Co-pay Type</th>
                            <th style="border-right:1px solid #fff !important;">Co-pay Amt($)</th>
                            <th style="border-right:1px solid #fff !important;">Previous Appt</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php  
							 $count = 1;   
							 $last_visit = [];
						 @endphp  
                        @if(count($appointment)>0)
                        @foreach($appointment as $appointment) 
                        @php   
							$time_arr = explode("-",@$appointment->appointment_time); 
							$patient = $appointment->patient;
							$set_title = (@$patient->title)? @$patient->title.". ":'';
							$patient_name = 	$set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name); 
							$patient_type ="";
						@endphp 
                        @if(empty($patient->patient_insurance))
                        <tr>
                            <td>{{$patient_name}}</td>
                            <td>{{$patient->account_no}}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$appointment->created_at) }}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$appointment->scheduled_on) }}</td>
                            <td style="text-transform: lowercase;">{{ $time_arr[0]}}</td>
                            @if(@$column->provider =='')
                            <td>{{ @$appointment->provider->provider_name }}</td> 
                            @endif
                            @if(@$column->facility =='')							
                            <td>{{ @$appointment->facility->facility_name }}</td>                      
                            @endif
                            <td>{{ @$appointment->status }}</td> 
                            @if(@$appointment->claim == null )
                            @php  $patient_type = 'New';@endphp
                            @else
                            @php  $patient_type = 'Existing';@endphp
                            @endif	
                            @if(@$column->patient_type =="")
                            <td> {{ @$patient_type }} </td>	
                            @endif
                            <td>{{ @$appointment->copay_option }}</td>
                            <td style="text-align:right;"> {{ @$appointment->copay }} </td>
                            @php  $last_visit_date = App\Models\Scheduler\PatientAppointment::getLastappointmentDate(@$appointment->patient->id); @endphp
                            <td>{{ $last_visit_date }}</td>
                            <td> {{ @$appointment->user->short_name }}</td>					
                        </tr>
                        @endif
                        @php  $count++;   @endphp 
                        @endforeach  
                        @endif
                    </tbody>                           
                </table>							
            </div> 
        </div>
        @if($export == "xlsx" || $export == "csv")
        <div colspan="10" class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
    </body>
</html>