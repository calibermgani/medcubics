<div class="box-body margin-t-10 no-padding mobile-scroll"><!-- Box Body Starts -->
    <div class="box box-view no-shadow yes-border mob-150 med-border-color"><!--  Box Starts -->
        <div class="box-header med-bg-green no-padding margin-b-10 b-r-4-4-0">
            <div class="col-lg-02 col-md-02 col-sm-2 col-xs-2">
                <h3 class="box-title med-white padding-6-15">Date</h3>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <h3 class="box-title med-white padding-6-15">Time</h3>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                <h3 class="box-title med-white padding-6-0">Provider</h3>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <h3 class="box-title med-white padding-6-0">Facility</h3>
            </div>
            <div class="col-lg-2 col-md-2 hidden-sm hidden-xs">
                <h3 class="box-title med-white padding-6-0">Reason for Visit</h3>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                <h3 class="box-title med-white padding-6-0">Status</h3>
            </div>

        </div><!-- /.box-header -->

        <div class="box-body p-b-0 m-b-m-8 padding-10-4-4-4">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding"><!-- Appointment Listing Starts  -->  
                <?php $count = 1;   ?>  
                @if(count(@$patient_appointment) > 0 )
                @foreach($patient_appointment as $patient_appointment) 
                @if(@$patient_appointment->patient_id==$patients->id)
                <div class="box box-view no-shadow collapsed-box yes-border no-border-radius" style="border: 1px solid #9fe4df; margin-top: -21px;"><!--  Box Starts -->
                    <div class="box-header-view-white">
                        <div class="col-lg-02 col-md-02 col-sm-2 col-xs-2">
                            <span class="box-title font13">{{App\Http\Helpers\Helpers::dateFormat($patient_appointment->scheduled_on,'date')}}</span>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <?php $s = @str_split(@$patient_appointment->appointment_time, 8); ?>
                            <span class="box-title font13 med-text">{{ $s[0] }}</span>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                            <span class="box-title font13 med-text"> 
                                <div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
                                    <a id="someelem{{hash('sha256','p_'.@$patient_appointment->provider->id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$patient_appointment->provider->id.$count)}}" href="javascript:void(0);"> 
                                        {{ @$patient_appointment->provider->short_name }} </a>
                                    <?php 
										$provider = @$patient_appointment->provider; 
										$provider->id = 'p_'.@$patient_appointment->provider->id.$count;
									?>  
                                    @include ('layouts/provider_hover')
                                </div>
                                <!--{{ str_limit(@$patient_appointment->provider->provider_name,20,'...')  }}--> </span>                                    
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <span class="box-title font13 med-text">
                                <div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
                                    <a id="someelem{{hash('sha256','f_'.@$patient_appointment->facility->id.$count)}}" class="someelem" data-id="{{hash('sha256','f_'.@$patient_appointment->facility->id.$count)}}" href="javascript:void(0);"> 
                                        {{ str_limit(@$patient_appointment->facility->facility_name,30,'...') }}</a>

                                    <?php 
										$facility = @$patient_appointment->facility; 
										@$facility->id = 'f_'.@$patient_appointment->facility->id.$count;	
									?> 
                                    @include ('layouts/facility_hover')
                                </div>	
                                <!--{{ str_limit(@$patient_appointment->facility->facility_name,30,'...') }}--></span>
                        </div>
                        <div class="col-lg-2 col-md-2 hidden-sm hidden-xs">
                            <span class="box-title font13 med-text"> {{str_limit(@$patient_appointment->reasonforvisit->reason,20,'..') }}</span>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                            <span class="box-title font13 med-text"><span class="{{@$patient_appointment->status}} "> {{@$patient_appointment->status}}</span></span>
                            @if(!empty($patients_insurance['Secondary'])){{ str_limit($patients_insurance['Secondary'],35,'...')}} - <span class="med-orange">$145</span>@endif</h5>
                        </div>

                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body p-b-0">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-t-5 appointments">
                            <table class="table-responsive table table-borderless">                    
                                <tbody>
                                    <tr>
                                        <td class="med-green">Provider</td>
                                        <td style="border-right: 1px solid #9fe4df;">{{@$patient_appointment->provider->provider_name  }} {{@$patient_appointment->provider->degrees->degree_name  }}</td>
                                    </tr>
                                    <tr>
                                        <td class="med-green">Facility</td>
                                        <td style="border-right: 1px solid #9fe4df;">{{@$patient_appointment->facility->facility_name}}</td>                                                
                                    </tr>
                                    <tr>
                                        <td class="med-green">Non Billable Visit</td>
                                        <td style="border-right: 1px solid #9fe4df;"><span class="patient-status-bg-form @if(@$patient_appointment->non_billable_visit == 'Yes')label-success @else label-danger @endif">{{@$patient_appointment->non_billable_visit}}</span></td>                                                
                                    </tr>
                                    <tr>
                                        <td class="med-green">Co-pay Option </td>
                                        <td style="border-right: 1px solid #9fe4df;">{{@$patient_appointment->copay_option}}</td>                                                
                                    </tr>
                                    <tr>
                                        <td class="med-green">Co-pay Amount</td>
                                        <td style="border-right: 1px solid #9fe4df;"><span class="font600">{{@$patient_appointment->copay}}</span></td>                                                
                                    </tr>
                                    <tr>
                                        <td class="med-green">Co-pay details </td>
                                        <td style="border-right: 1px solid #9fe4df;"><span class="font600 {{@$patient_appointment->status}}">{{($patient_appointment->copay_details != '') ? $patient_appointment->copay_details : "-"}}</span></td>                                               
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12  padding-t-5 appointments">
                            <table class="table-responsive table table-borderless">                    
                                <tbody>
                                    <tr>
                                        <td class="med-green">Date</td>
                                        <td>{{App\Http\Helpers\Helpers::dateFormat($patient_appointment->scheduled_on,'date')}}</td>
                                    </tr>
                                    <tr>
                                        <td class="med-green">Time</td>
                                        <td>{{@$patient_appointment->appointment_time}}</td>
                                    </tr>
                                    <tr>
                                        <td class="med-green">Check In Time</td>
                                        <td>{{@$patient_appointment->checkin_time}}</td>
                                    </tr>
                                    <tr>
                                        <td class="med-green">Check Out Time</td>
                                        <td>{{@$patient_appointment->checkout_time}}</td>
                                    </tr>
                                    <tr>
                                        <td class="med-green">Appointment Status</td>
                                        <td style="border-right: 1px solid #9fe4df;"><span class="font600 {{@$patient_appointment->status}}">{{@$patient_appointment->status}}</span></td>                                               
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p class="margin-b-6"><span class="med-green font600">Reason for Visit : </span>
                                {{@$patient_appointment->reasonforvisit->reason }}</p>                                        
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            @if(@$patient_appointment->notes !='')
                            <p><span class="med-orange"><b>Notes :</b> </span>
                                {{@$patient_appointment->notes}}</p> 
                            @endif
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box Ends-->
                <?php $count++;   ?> 
                @endif
                @endforeach
                @else
                <p class="text-center med-gray-dark margin-t-m-13"> No Records Found </p>
                @endif
            </div><!-- Appointment Listing Ends -->
        </div><!-- Box Body Ends -->    
    </div><!-- Box Ends -->
</div><!-- /.box-body Ends --> 