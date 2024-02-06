<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-calendar" data-name="info"></i> <h3 class="box-title">Appointment Analysis</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">{{ date("m/d/Y") }}</h3>
        </div>
    </div>	
    <div class="box-body"><!-- Box Body Starts -->
        @if($header !='' && count($header)>0)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25">Appointment Analysis</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
                @foreach($header as $header_name => $header_val)
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><span class="med-green">{{ @$header_name }}</span> : {{ @$header_val }}</div>                    
                </div>
                @endforeach
            </div>
        </div>
        @endif
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
            <div class="box box-info no-shadow no-bottom no-border">
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table id="sort_list_noorder" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Acc No</th>
                                    <th>Created Date</th>
                                    <th>Appt Date</th>
                                    <th>Appt Time</th>
                                    @if(@$column->provider =='')
                                    <th>Provider</th>
                                    @endif	
                                    @if(@$column->facility =='')
                                    <th>Facility</th>
                                    @endif
                                    <th>Status</th>
                                    @if(@$column->patient_type =="")
                                    <th>Patient</th>
                                    @endif	
                                    <th>Co-Pay Type</th>
                                    <th>Co-Pay Amt($)</th>
                                    <th>Previous Appt</th>
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
                                <tr style="cursor:default;">
                                    <td>{{$patient_name}}</td>
                                    <td>{{$patient->account_no}}</td>
                                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$appointment->created_at) }}</td>
                                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$appointment->scheduled_on) }}</td>
                                    <td>{{ $time_arr[0]}}</td>
                                    @if(@$column->provider =='')
                                    <td>{{ @$appointment->provider->short_name }}</td> 
                                    @endif
                                    @if(@$column->facility =='')							
                                    <td>{{ @$appointment->facility->short_name }}</td>                           
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
                                    <td class="text-right"> {{ @$appointment->copay }} </td>
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
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
        </div>
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->