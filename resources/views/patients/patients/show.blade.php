@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    {!! Form::hidden('patient_id_show',@$patients->id,['class'=>'form-control','id'=>'patient_id_show']) !!}
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="users-add"></i>Patients </small>
        </h1>
        <ol class="breadcrumb">
            @include ('patients/layouts/swith_patien_icon')
            <li><a href="{{ url('patients') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>  
            <?php /*                     
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            */ ?>
            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop
@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$id,'needdecode'=>'yes'])
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-13"><!-- Main Col Starts -->
    <div class="med-tab"><!-- Med-tab starts -->
        <div class="nav-tabs-custom" ><!-- nav-tabs- customs starts -->
            <ul class="nav nav-tabs">
                <li id="personal-info_{{@$id}}" class="active js-curr-tab"><a href="#personal-info" data-toggle="tab"><i class="fa fa-info-circle i-font-tabs"></i> Demographic</a></li>
                <li id="insurance-info_{{@$id}}" class="js-curr-tab"><a href="#insurance-info" data-toggle="tab"><i class="fa fa-institution i-font-tabs"></i> Insurance</a></li>
                @if(@$selectbox != '')<li id="contact-info_{{@$id}}" class="js-curr-tab"><a href="#contact-info" data-toggle="tab"><i class="fa fa-book i-font-tabs"></i> Contacts</a></li>@endif
                <li class="js-curr-tab" id="authorization-info_{{@$id}}"><a href="#authorization" data-toggle="tab"><i class="fa fa-shield i-font-tabs"></i> Authorization</a></li>
               
            </ul>
            <div class="tab-content patient-tab-bg">
                <div class="active tab-pane" id="personal-info"><!-- Demographics Tab Starts -->
                    <div class="box-body m-b-m-10"><!-- Demographics Box Body Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-m-10 margin-t-m-5"><!-- Alert Box Starts -->
                            @if(@$patients->alert_notes)<div class="alert">
                                <span class="med-orange"><b>Alert ! :</b> </span> 
                                <i class="med-green">{{ @$patients->alert_notes }}</i>
                            </div>@endif
                        </div><!-- Alert Box Ends -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10"><!-- Buttons Col Starts -->
                            <p class="font14"><a href="{{url('patients/'.@$id.'/edit')}}" class="font600 pull-right padding-r-5"><i class="fa fa-edit"></i> Edit</a></p>
                        </div><!-- Buttons Col Ends -->

                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--Demographics Left side Content Starts -->
                            <div class="box box-view no-shadow"><!--  Box Personal Information Starts -->
                                <div class="box-header-view">
                                    <i class="fa fa-info-circle"></i><h3 class="box-title">Personal Information</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body p-b-46"><!-- Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="table-responsive">
                                            <table class="table-responsive table-striped-view table">                    
                                                <tbody>                                            
                                                    <tr>
                                                        <td>Title</td>
                                                        <td>{{ @$patients->title }}</td>
                                                        <td></td> 
                                                    </tr>

                                                    <tr>
                                                        <td>Address Line 1</td>
                                                        <td>{{ @$patients->address1}}</td>
                                                        <td></td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Address Line 2</td>
                                                        <td>{{ @$patients->address2}}</td>
                                                        <td></td> 
                                                    </tr>
                                                    <tr>
                                                        <td>City</td>
                                                        <td>{{ @$patients->city}} @if( @$patients->state !='') - <span class="bg-state">{{ @$patients->state}}</span> @endif</td>
                                                        <td></td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Zip Code</td>
                                                        <td>{{ @$patients->zip5 }} @if(@$patients->zip4 !='')- {{@$patients->zip4}} @endif</td>
                                                        <td>
                                                            @include('practice/layouts/usps_show_form_modal_input',['div_name' => 'js-address-general-address', 'af_type' => 'patients', 'af_type_id' => $patients->id, 'af_sub_type' => 'personal_info_address'])
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Gender</td>
                                                        <td>{{ @$patients->gender }}</td>
                                                        <td></td> 
                                                    </tr>
                                                    <tr>
                                                        <td>SSN</td>
                                                        <td>{{ @$patients->ssn }}<a id="document_add_modal_link_ssn" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients/'.@$id.'/ssn')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="fa fa-paperclip margin-l-10  @if( @$patients->ssn !='') icon-green-form @else icon-green @endif" ></i></a></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>DOB</td>
                                                        <td><span class="bg-date">@if(@$patients->dob != "0000-00-00" && @$patients->dob != "1901-01-01" && @$patients->dob != ""){{ App\Http\Helpers\Helpers::dateFormat(@$patients->dob,'dob') }} {{ App\Http\Helpers\Helpers::dob_age(@$patients->dob) }}  @endif </td>
                                                        <td></td> 
                                                    </tr>

                                                    <tr>
                                                    <td>Gua. Name</td>
                                                    <td><?php $guarantor_name = App\Http\Helpers\Helpers::getNameformat("$patients->guarantor_last_name","$patients->guarantor_first_name","$patients->guarantor_middle_name"); ?> 
                                                    {{ $guarantor_name }}</td>
                                                    <td>
                                                        <?php $gua_name = ''; ?>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-b-0 p-l-0">
                                                            &emsp; @if(@$guarantor_name != '')<a class="mm"><i class="fa fa-twitch icon-green cur-pointer margin-t-0"></i></a>@endif
                                                            <div class="on-hover-content mo-right" style="display:none;">
                                                                @foreach($patients->contact_details as $contactinfo)
                                                                    @if(@$gua_name != '')
                                                                        <br>
                                                                    @endif

                                                                    @if($contactinfo->category == 'Guarantor')
                                                                        <?php $gua_name = App\Http\Helpers\Helpers::getNameformat("$contactinfo->guarantor_last_name","$contactinfo->guarantor_first_name","$contactinfo->guarantor_middle_name"); ?> 
                                                                        <p class="no-bottom med-green">{{ @$gua_name }}</p>
                                                                        <?php
                                                                            $address = '';
                                                                            if(@$contactinfo->guarantor_address1 != '')
                                                                                $address .= @$contactinfo->guarantor_address1;

                                                                            if(@$contactinfo->guarantor_address2 != '')
                                                                            {
                                                                                if($address != '')
                                                                                    $address .= ', ';
                                                                                $address .= @$contactinfo->guarantor_address2;
                                                                            }

                                                                            if(@$contactinfo->guarantor_city != '')
                                                                            {
                                                                                if($address != '')
                                                                                    $address .= ', ';
                                                                                $address .= @$contactinfo->guarantor_city;
                                                                            }

                                                                            if(@$contactinfo->guarantor_state != '')
                                                                            {
                                                                                if($address != '')
                                                                                    $address .= ', ';
                                                                                $address .= @$contactinfo->guarantor_state;
                                                                            }

                                                                            if(@$contactinfo->guarantor_zip5 != '')
                                                                            {
                                                                                if($address != '')
                                                                                    $address .= ', ';
                                                                                $address .= @$contactinfo->guarantor_zip5;
                                                                            }

                                                                            if(@$contactinfo->guarantor_zip4 != '')
                                                                            {
                                                                                if($address != '')
                                                                                {
                                                                                    if($contactinfo->guarantor_zip5 != '')
                                                                                        $address .= '-';
                                                                                    else
                                                                                        $address .= ', ';
                                                                                }
                                                                                $address .= @$contactinfo->guarantor_zip4;
                                                                            }
                                                                        ?>
                                                                        @if($address != '')
                                                                            <p class="no-bottom">{{$address}}</p>
                                                                        @endif

                                                                        @if(@$contactinfo->guarantor_home_phone)
                                                                            <p class="no-bottom">
                                                                                <b>Home Phone: </b>{{ @$contactinfo->guarantor_home_phone }}
                                                                            </p>
                                                                        @endif

                                                                        @if(@$contactinfo->guarantor_cell_phone)
                                                                            <p class="no-bottom">
                                                                                <b>Cell Phone: </b>{{ @$contactinfo->guarantor_cell_phone }}
                                                                            </p>
                                                                        @endif
																		
                                                                        @if(@$contactinfo->guarantor_email)
                                                                            <p class="no-bottom">
                                                                                <b>Email: </b>{{ @$contactinfo->guarantor_email }}
                                                                            </p> 
                                                                        @endif
																		
                                                                        @if(@$contactinfo->guarantor_relationship)
                                                                            <p class="no-bottom">
                                                                                <b>Relationship: {{ @$contactinfo->guarantor_relationship }}
                                                                            </p>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                    <tr>
                                                        <td>Gua. Relationship</td>
                                                        <td>{{ @$patients->guarantor_relationship }}</td> 
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Employment Status</td>
                                                        <td>{{ @$patients->employment_status}}</td>
                                                        <td></td>
                                                    </tr>

                                                    @if(@$registration->student_status ==1)
                                                    <tr>
                                                        <td>Student Status</td>
                                                        <td>{{ @$patients->student_status }}</td>
                                                        <td></td>
                                                    </tr>
                                                    @endif

                                                    <tr>
                                                        <td>Home Phone</td>
                                                        <td>{{ @$patients->phone}}</td>
                                                        <td></td>
                                                    </tr>

                                                    <tr>
                                                        <td>Work Phone</td>
                                                        <td>{{ @$patients->work_phone}} @if(@$patients->work_phone_ext != '') <span class=" bg-ext ">{{ @$patients->work_phone_ext}} </span> @endif </td>
                                                        <td></td>
                                                    </tr>

                                                    <tr>
                                                        <td>Cell Phone</td>
                                                        <td>{{ @$patients->mobile}}</td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>



                                </div><!-- /.box-body -->
                            </div><!--  Box Personal Information Ends -->
                        </div><!-- Demographics Left side Content Ends -->



                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--Demographics Right side Content Starts -->
                            <div class="box box-view no-shadow"><!--  Box General Information Starts -->
                                <div class="box-header-view">
                                    <i class="fa fa-info-circle"></i><h3 class="box-title">General Information</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body p-b-12"><!-- Box Body Starts -->

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="table-responsive">
                                            <table class="table-responsive table-striped-view table">                    
                                                <tbody>
                                                    @if(@$registration->ethnicity ==1)<tr>
                                                        <td>Ethnicity</td>
                                                        <td>@if(@$patients->ethnicity_details){{ @$patients->ethnicity_details->name }}@endif</td> 
                                                        <td></td>
                                                    </tr>@endif

                                                    @if(@$registration->race ==1)<tr>
                                                        <td>Race</td>
                                                        <td>{{ $patients->race }}</td>
                                                        <td></td>
                                                    </tr>@endif
                                                    @if(@$registration->driving_license ==1)
                                                    <tr>
                                                        <td>Driving License</td>
                                                        <td>{{ @$patients->driver_license }} <a id="document_add_modal_link_driver_license" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients/'.@$id.'/Patient_Documents_Driving_License')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="fa fa-paperclip margin-l-10 @if(@$patients->driver_license !='')icon-green-form @else icon-green @endif"></i></a></td>
                                                        <td></td>
                                                    </tr>
                                                    @endif

                                                    @if(@$registration->email_id ==1)
                                                    <tr>
                                                        <td>Email</td>
                                                        <td><a href="mailto:{{@$patients->email}}">{{ @$patients->email}}</a></td>
                                                        <td></td>
                                                    </tr>
                                                    @endif

                                                    @if(@$registration->preferred_language ==1)<tr>
                                                        <td>Pref. Language</td>
                                                        <td>@if(@$patients->language_details){{ @$patients->language_details->language }}@endif</td> 
                                                        <td></td> 
                                                    </tr> @endif  

                                                    @if(@$registration->marital_status ==1)<tr>
                                                        <td>Marital Status</td>
                                                        <td>{{ @$patients->marital_status }}</td>
                                                        <td></td> 
                                                    </tr>@endif                                                

                                                    @if(@$registration->primary_care_provider ==1)<tr>
                                                        <td>PCP</td>
                                                        <td>{{ @$patients->provider_details->provider_name }} {{ @$patients->provider_details->degrees->degree_name}}</td>  
                                                        <td></td>
                                                    </tr>@endif

                                                    @if(@$registration->primary_facility ==1)<tr>
                                                        <td>Primary Facility</td>
                                                        <td>@if(@$patients->facility_details){{ @$patients->facility_details->facility_name }}@endif</td>  
                                                        <td></td>
                                                    </tr>@endif

                                                    @if(@$registration->send_email_notification ==1)<tr>
                                                        <td>Email Reminders</td>
                                                        <td><span class="patient-status-bg-form @if(@$patients->email_notification) @if(@$patients->email_notification == 'Yes') label-success @else label-danger @endif @endif">{{ @$patients->email_notification }}</span></td>
                                                        <td></td>
                                                    </tr>@endif

                                                    @if(@$registration->auto_phone_call_reminder ==1)<tr>
                                                        <td>Phone Reminders</td>
                                                        <td><span class="patient-status-bg-form @if(@$patients->phone_reminder) @if(@$patients->phone_reminder == 'Yes') label-success @else label-danger @endif @endif" >{{ @$patients->phone_reminder }}</span></td>
                                                        <td></td>
                                                    </tr>@endif
                                                    @if(@$registration->preferred_communication ==1)<tr>
                                                        <td>Pref. Communication</td>
                                                        <td>{{ @$patients->preferred_communication }}</td>
                                                        <td></td>
                                                    </tr>@endif
                                                    <tr>
                                                        <td>Statements</td>
                                                        <td><span class="patient-status-bg-form @if(@$patients->statements) @if(@$patients->statements == 'Yes') label-success @else label-danger @endif @endif">{{ @$patients->statements }}</span></td>  
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Statements Sent</td>
                                                        <td>{{ @$patients->statements_sent }}</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bill Cycle</td>
                                                        <td>{{ @$patients->bill_cycle }}</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Deceased Date</td>
                                                        <td>@if($patients->deceased_date != '0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$patients->deceased_date,'date') }}@endif</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Chart No</td>
                                                        <td>{{ @$patients->medical_chart_no }}</td>  
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div><!-- /.box-body -->
                            </div><!--  Box General Information Ends -->
                        </div><!-- Demographics Right side Content Ends -->

                    </div><!-- Demographics box-body Ends -->
                </div><!-- Demographics Tab Ends -->


                <div class="tab-pane m-b-m-15 " id="insurance-info"> <!-- Insurance Tab-pane Starts -->
                    <div class="box no-border no-shadow "><!-- Insurance Box Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10">
                            @if($patients->patient_insurance && @$patients->is_self_pay!='Yes')
                            <a href="{{url('patients/'.$id.'/edit/insurance/more')}}" class="font600 pull-right p-l-10 b-l-1 font14"><i class="fa fa-plus"></i> New Insurance</a>
                            @endif
                            <a href="{{url('patients/'.$id.'/edit/insurance')}}" class="font600 pull-right margin-r-10 font14">@if($patients->patient_insurance)<i class="fa fa-edit"></i> Edit @else <i class="fa fa-plus-circle"></i>  New Insurance @endif</a>
                        </div>

                        @if(@$patients->is_self_pay=='Yes')
                            <p class="font16 text-center font600 med-orange">Self Pay</p>
                        @else
                        <div class="box-body patient-tab-bg margin-t-20"><!-- Insurance Box Body Starts -->
                            <div class="box no-shadow med-border-color margin-t-13"><!--  Box Starts -->
                                <div class="box-header no-padding margin-b-10 med-bg-green">
                                    <div class="col-lg-3 col-md-4 col-sm-7 col-xs-7">
                                        <h3 class="box-title med-white padding-4-15">Name</h3>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-5 col-xs-5">
                                        <h3 class="box-title med-white padding-4-15">Policy ID</h3>
                                    </div>
                                    <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                        <h3 class="box-title med-white padding-4-0">Effective Date</h3>
                                    </div>
                                    <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                        <h3 class="box-title med-white padding-4-0">Termination Date</h3>
                                    </div>
                                    <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                        <h3 class="box-title med-white padding-4-0">Benefit Verification</h3>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body" style="padding: 10px 2px 2px;">
                                    <?php
										$count_id = 1;
										$count_rev_order = count(@$patients->patient_insurance) - 1;
                                    ?>
                                    @if(!$patients->patient_insurance)
                                    <h5 class="margin-t-m-13 margin-l-10">No Records Found !!!</h5>
                                    @else

                                    @foreach($patients->patient_insurance as $insurance)
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-8 m-b-m-8">
                                        <div class="box box-view no-shadow collapsed-box"><!--  Box Starts -->
                                            <div class="box-header-view">
                                                <div class="col-lg-3 col-md-4 col-sm-7 col-xs-7">
                                                    <h3 class="box-title">
                                                        @if(@$insurance->category =='Primary')<span class="" style="background: #F9EFD3; padding: 2px 6px; color:#D98400">P</span> 
                                                        @elseif(@$insurance->category =='Secondary')<span style="background: #F2F9C8; padding: 2px 6px; color:#798C01">S</span> 
                                                        @elseif(@$insurance->category =='Tertiary')<span style="background: #D2EDF9; padding: 2px 6px; color:#0572A1">T</span>
                                                        @elseif(@$insurance->category =='Others')<span style="background: #F6E3FC; padding: 2px 4px; color:#720294">O</span>
                                                        @elseif(@$insurance->category =='Workerscomp')<span style="background: #F6E3FC; padding: 2px 4px; color:#720294">W</span>
                                                        @elseif(@$insurance->category =='Liability')<span style="background: #F6E3FC; padding: 2px 4px; color:#720294">L</span>
                                                        @endif {{ @$insurance->insurance_details->insurance_name }}
                                                    </h3>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-5 col-xs-5 ">
                                                    <h3 class="box-title font-gray margin-l-10">{{ @$insurance->policy_id }}</h3>
                                                </div>
                                                <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                                    <h3 class="box-title">
                                                        @if(@$insurance->effective_date != '0000-00-00')
                                                        <span class="">{{ App\Http\Helpers\Helpers::dateFormat($insurance->effective_date,'date') }} </span>
                                                        @endif
                                                    </h3>
                                                </div>
                                                <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                                    <h3 class="box-title"> 
                                                        @if(@$insurance->termination_date != '0000-00-00')
                                                        <span class=" margin-l-10">{{ App\Http\Helpers\Helpers::dateFormat(@$insurance->termination_date,'date') }} </span>
                                                        @endif
                                                    </h3>
                                                </div>

                                                <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                                    <h3 class="box-title"> 
                                                        <span class="js_insgray{{ @$insurance->id }}" @if(isset($insurance->eligibility_verification) && ($insurance->eligibility_verification == 'Active' || $insurance->eligibility_verification == 'Inactive')) style="display:none;" @endif >	
                                                              <a title="Check Eligibility" data-unid="{{ @$insurance->id }}"  data-patientid="{{ @$insurance->patient_id }}" data-category="{{ @$insurance->category }}" class="js-patient-eligibility_check" href="javascript:void(0);"><i class="fa fa-user text-gray font10"></i></a> 
                                                        </span>
                                                        <i class="fa fa-spinning fa-spin patientinsloadingimg{{ @$insurance->id }} font11" style="display:none;"></i>
                                                        <span class="js_insgreen{{ @$insurance->id }}" @if(isset($insurance->eligibility_verification) && ($insurance->eligibility_verification == 'None' || $insurance->eligibility_verification == 'Inactive' || $insurance->eligibility_verification == 'Error')) style="display:none;" @endif >	
                                                              <a title="Eligibility Details" class="js_get_eligiblity_details" data-unid="{{ @$insurance->id }}" data-patientid="{{ @$insurance->patient_id }}" data-category="{{ @$insurance->category }}" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-green font10"></i></a> 
                                                        </span>

                                                        <span class="js_insred{{ @$insurance->id }}" @if(isset($insurance->eligibility_verification) && ($insurance->eligibility_verification == 'None' || $insurance->eligibility_verification == 'Active' || $insurance->eligibility_verification == 'Error')) style="display:none;" @endif >	
                                                              <a title="Eligibility Details" class="js_get_eligiblity_details" data-unid="{{ @$insurance->id }}" data-patientid="{{ @$insurance->patient_id }}" data-category="{{ @$insurance->category }}" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-red font10"></i></a> 
                                                        </span>
                                                    </h3>
                                                </div>

                                                <div class="box-tools pull-right">
                                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div><!-- /.box-header -->
                                            <div class="box-body m-b-m-15">
                                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 right-border">
                                                    <div class="table-responsive">
                                                        <table class="table-responsive table-striped-view table">                    
                                                            <tbody>
                                                                <tr>
                                                                    <td>Name</td>
                                                                    <td>{{ @$insurance->insurance_details->insurance_name }}</td>
                                                                    <td></td>
                                                                </tr>

                                                                <tr>
                                                                    <td>Address</td>
                                                                    <td>{{ @$insurance->insurance_details->address_1.' '.@$insurance->insurance_details->address_2 }}</td> 
                                                                    <td></td>
                                                                </tr> 

                                                                <tr>
                                                                    <td>City</td>
                                                                    <td>{{ @$insurance->insurance_details->city }} @if( @$insurance->insurance_details->state !='') - <span class="bg-state">{{ @$insurance->insurance_details->state}}</span> @endif
                                                                    </td> 
                                                                    <td></td>
                                                                </tr>  

                                                                <tr>
                                                                    <td>Zip Code</td>
                                                                    <td>{{ @$insurance->insurance_details->zipcode5 }} @if( @$insurance->insurance_details->zipcode4 !='') - {{ @$insurance->insurance_details->zipcode4 }} @endif</td>
                                                                    <td>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>Claims Status Ph</td>
                                                                    <td>{{ @$insurance->insurance_details->claim_ph.' '.@$insurance->insurance_details->claim_ext }}</td>
                                                                    <td><a data-toggle="modal" href="#additional-contact{{@$insurance->id}}">View More Contacts</a></td> 
                                                                </tr>

                                                                <tr>
                                                                    <td>Eligibility Ph</td>
                                                                    <td>{{ @$insurance->insurance_details->eligibility_ph.' '.@$insurance->insurance_details->eligibility_ext }}</td>
                                                                    <td></td> 
                                                                </tr>
                                                                <tr>
                                                                    <td>Policy ID</td>
                                                                    <td><span @if(@$insurance->policy_id != "")class ="bg-number" @endif/>{{ @$insurance->policy_id }}  <a id="document_add_modal_link_policy_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients::insurance::'.@$id.'/'.@$insurance->document_save_id.'/Patient_Documents_Insurance_Card_Copy')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}  margin-l-10 @if(@$insurance->policy_id = '') margin-t-m-8 @endif"></i></a></td>  
                                                                    <td></td>
                                                                </tr>

                                                                <tr>
                                                                    <td>Group ID</td>
                                                                    <td><span @if(@$insurance->group_id != "")class ="bg-number" @endif/>{{ @$insurance->group_id }}</td>  
                                                                    <td></td>
                                                                </tr>

                                                                <tr>
                                                                    <td>Group Name</td>
                                                                    <td>{{ @$insurance->group_name }}</td>  
                                                                    <td></td>
                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                    <div class="table-responsive">
                                                        <table class="table-responsive table-striped-view table">                    
                                                            <tbody>

                                                                <tr>
                                                                    <td>Insured</td>
                                                                    <td>
                                                                        <?php $insured_name = App\Http\Helpers\Helpers::getNameformat("$insurance->last_name","$insurance->first_name","$insurance->middle_name"); ?> 
                                                                        {{ $insured_name }}
                                                                    </td>  
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Relationship</td>
                                                                    <td>{{ @$insurance->relationship }}</td>  
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Insured SSN</td>
                                                                    <td><span @if(@$insurance->insured_ssn != "")class ="bg-number" @endif/>{{ @$insurance->insured_ssn }} <a id="document_add_modal_link_insured_ssn" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients::insurance::'.@$id.'/'.@$insurance->document_save_id.'/insured_ssn')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} margin-l-10  @if(@$insurance->insured_ssn = '')margin-t-m-8 @endif"></i></a></td>  
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Insured D.O.B</td>
                                                                    <td>@if(@$insurance->insured_dob != "0000-00-00")<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat(@$insurance->insured_dob,'dob') }}</span>@endif
                                                                    </td>  
                                                                    <td></td>
                                                                </tr>

                                                                <tr>
                                                                    <td>Address Line 1</td>
                                                                    <td>{{ @$insurance->insured_address1}}</td>
                                                                    <td></td> 
                                                                </tr>
                                                                <tr>
                                                                    <td>Address Line 2</td>
                                                                    <td>{{ @$insurance->insured_address2}}</td>
                                                                    <td></td> 
                                                                </tr>
                                                                <tr>
                                                                    <td>City</td>
                                                                    <td>{{ @$insurance->insured_city}} @if( @$insurance->insured_state !='') - <span class="bg-state">{{ @$insurance->insured_state}}</span> @endif</td>
                                                                    <td></td> 
                                                                </tr>
                                                                <tr>
                                                                    <td>Zip Code</td>
                                                                    <td>{{ @$patients->zip5 }} @if(@$patients->zip4 !='') - {{ @$patients->zip4 }} @endif</td>
                                                                    <td>
                                                                        @include('practice/layouts/usps_show_form_modal_input',['div_name' => 'js-address-general-address', 'af_type' => 'patients', 'af_type_id' => $patients->id, 'af_sub_type' => 'personal_info_address'])
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>Effective Date</td>
                                                                    <td>
                                                                        @if(@$insurance->effective_date != "0000-00-00")
                                                                        <span class="bg-green-date">{{ App\Http\Helpers\Helpers::dateFormat(@$insurance->effective_date,'date') }}</span>
                                                                        @endif
                                                                    </td>  
                                                                    <td></td>
                                                                </tr>   

                                                                <tr>
                                                                    <td>Termination Date</td>
                                                                    <td>
                                                                        @if(@$insurance->termination_date != "0000-00-00")
                                                                        <span class="bg-red-date"> {{ App\Http\Helpers\Helpers::dateFormat(@$insurance->termination_date,'date') }}</span>
                                                                        @endif
                                                                    </td>  
                                                                    <td></td>
                                                                </tr>    
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                @if(@$insurance->insurance_notes!='') 
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <p><span class="med-orange"><b>Notes :-</b> </span>
                                                        {{ @$insurance->insurance_notes }}</p>
                                                </div> 
                                                @endif
                                            </div><!-- /.box-body -->  
                                        </div><!-- /.box Ends-->
                                    </div>
                                    <?php
										$count_id ++;
										$count_rev_order --;
                                    ?>

                                    <!-- Start view more contact information modal box -->
                                    <div id="additional-contact{{@$insurance->id}}" class="modal fade in">
                                        <div class="modal-md-650">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title">{{ @$insurance->insurance_details->insurance_name }} - Additional Contacts</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                                                        <div class="box-body no-padding m-b-m-15">
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space10">
                                                                <div class="table-responsive">
                                                                    <table class="table-responsive table-striped-view table" style="border:1px solid #E4FAFD;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>Eligibility Ph</td>
                                                                                <td>{{ @$insurance->insurance_details->eligibility_ph2 }} @if(@$insurance->insurance_details->eligibility_ext2 !='') <span class="ph-ext-bg">{{ @$insurance->insurance_details->eligibility_ext2 }}</span>@endif</td>
                                                                                <td></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Enrollment Ph</td>
                                                                                <td>{{ @$insurance->insurance_details->enrollment_ph }} @if(@$insurance->insurance_details->enrollment_ext !='')<span class="ph-ext-bg">{{ @$insurance->insurance_details->enrollment_ext }}</span>@endif</td>
                                                                                <td></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Prior Auth Ph</td>
                                                                                <td>{{ @$insurance->insurance_details->prior_ph }} @if(@$insurance->insurance_details->prior_ext !='')<span class="ph-ext-bg">{{ @$insurance->insurance_details->prior_ext }}</span>@endif</td>
                                                                                <td></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Appeals Ph</td>
                                                                                <td>{{ @$insurance->insurance_details->phone1 }} </td>
                                                                                <td></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Adjustor</td>
                                                                                <td>{{ @$insurance->adjustor_ph }}</td>
                                                                                <td></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space10">
                                                                <div class="table-responsive">
                                                                    <table class="table-responsive table-striped-view table" style="border:1px solid #E4FAFD;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>Eligibility Fax</td>
                                                                                <td>{{ @$insurance->insurance_details->eligibility_fax }}  </td>
                                                                                <td></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Enrollment Fax</td>
                                                                                <td>{{ @$insurance->insurance_details->enrollment_fax }} </td>
                                                                                <td></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Prior Auth Fax</td>
                                                                                <td>{{ @$insurance->insurance_details->prior_fax }}</td>
                                                                                <td></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Appeals Fax</td>
                                                                                <td>{{ @$insurance->insurance_details->fax }}</td>
                                                                                <td></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Adjustor Fax</td>
                                                                                <td>{{ @$insurance->adjustor_fax }}</td>
                                                                                <td></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div><!-- /.box-body -->
                                                    </div>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div>
                                    </div>
                                    <!-- End view more contact information modal box -->

                                    @endforeach
                                    @endif
                                </div>    
                            </div><!-- Box Ends -->
                        </div><!-- Insurance Box Body Ends -->
                        @endif


                        <!-- Insurance Archive Div part Starts -->
                        @if(@$patients->patient_insurance_archive)

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide">
                            <span class="pull-left" style="font-weight: 600; color: rgb(114, 2, 148);">Patient Insurance Archive</span>
                        </div>

                        <div class="box-body hide"><!-- Insurance Archive Box Body Starts -->
                            <div class="box box-view no-shadow med-border-color margin-t-20"><!--  Box Starts -->
                                <div class="box-header no-padding margin-b-10 med-bg-green" style="border-radius: 4px 4px 0px 0px;">
                                    <div class="col-lg-3 col-md-4 col-sm-7 col-xs-7">
                                        <h3 class="box-title med-white" style="padding: 4px 15px;">Name</h3>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-5 col-xs-5">
                                        <h3 class="box-title med-white" style="padding: 4px 15px;">Policy ID</h3>
                                    </div>
                                    <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                        <h3 class="box-title med-white" style="padding: 4px 0px;">From Date</h3>
                                    </div>
                                    <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                        <h3 class="box-title med-white" style=" padding: 4px 0px;">To Date</h3>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body" style="padding: 10px 2px 2px;">
                                    @foreach($patients->patient_insurance_archive as $insurance_archive)
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-8 m-b-m-8">
                                        <div class="box box-view no-shadow collapsed-box"><!--  Box Starts -->
                                            <div class="box-header-view">
                                                <div class="col-lg-3 col-md-4 col-sm-7 col-xs-7">
                                                    <h3 class="box-title">
                                                        @if(@$insurance_archive->category =='Primary')<span class="" style="background: #F9EFD3; padding: 2px 6px; color:#D98400">P</span> 
                                                        @elseif(@$insurance_archive->category =='Secondary')<span style="background: #F2F9C8; padding: 2px 6px; color:#798C01">S</span> 
                                                        @elseif(@$insurance_archive->category =='Tertiary')<span style="background: #D2EDF9; padding: 2px 6px; color:#0572A1">T</span>
                                                        @elseif(@$insurance_archive->category =='Others')<span style="background: #F6E3FC; padding: 2px 4px; color:#720294">O</span>
                                                        @elseif(@$insurance_archive->category =='Workerscomp')<span style="background: #F6E3FC; padding: 2px 4px; color:#720294">W</span>
                                                        @elseif(@$insurance_archive->category =='Liability')<span style="background: #F6E3FC; padding: 2px 4px; color:#720294">L</span>
                                                        @endif {{ @$insurance_archive->insurance_details->short_name }}
                                                    </h3>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-5 col-xs-5 ">
                                                    <h3 class="box-title font-gray margin-l-10">{{ @$insurance_archive->policy_id }}</h3>
                                                </div>
                                                <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                                    <h3 class="box-title">
                                                        <span class="patient-status-bg label-success">{{ App\Http\Helpers\Helpers::dateFormat($insurance_archive->from,'date') }} </span>
                                                    </h3>
                                                </div>
                                                <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                                    <h3 class="box-title"> 
                                                        <span class="patient-status-bg label-warning margin-l-10">{{ App\Http\Helpers\Helpers::dateFormat(@$insurance_archive->to,'date') }} </span>
                                                    </h3>
                                                </div>

                                                <div class="box-tools pull-right">
                                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div><!-- /.box-header -->
                                            <div class="box-body m-b-m-15">
                                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                    <div class="table-responsive">
                                                        <table class="table-responsive table-striped-view table">                    
                                                            <tbody>
                                                                <tr>
                                                                    <td>Name</td>
                                                                    <td>{{ @$insurance_archive->insurance_details->insurance_name }}</td>
                                                                    <td></td>
                                                                </tr>

                                                                <tr>
                                                                    <td>Address</td>
                                                                    <td>{{ @$insurance_archive->insurance_details->address_1.' '.@$insurance_archive->insurance_details->address_2 }}</td> 
                                                                    <td></td>
                                                                </tr> 

                                                                <tr>
                                                                    <td>City</td>
                                                                    <td>{{ @$insurance_archive->insurance_details->city }} @if( @$insurance_archive->insurance_details->state !='') - <span class="bg-state">{{ @$insurance_archive->insurance_details->state}}</span> @endif
                                                                    </td> 
                                                                    <td></td>
                                                                </tr>  

                                                                <tr>
                                                                    <td>Zip Code</td>
                                                                    <td>{{ @$insurance_archive->insurance_details->zipcode5 }} @if( @$insurance_archive->insurance_details->zipcode4 !='') - {{ @$insurance_archive->insurance_details->zipcode4 }} @endif</td>
                                                                    <td>
                                                                    </td>
                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                    <div class="table-responsive">
                                                        <table class="table-responsive table-striped-view table">                    
                                                            <tbody>
                                                                <tr>
                                                                    <td>Insured</td>
                                                                    <td><?php $insured_name = App\Http\Helpers\Helpers::getNameformat("$insurance_archive->last_name","$insurance_archive->first_name","$insurance_archive->middle_name"); ?> 
                                                                        {{ $insured_name }}</td>  
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Relationship</td>
                                                                    <td>{{ @$insurance_archive->relationship }}</td>  
                                                                    <td></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div><!-- /.box-body -->  
                                        </div><!-- /.box Ends-->
                                    </div>
                                    @endforeach
                                </div>    
                            </div><!-- Box Ends -->
                        </div><!-- Insurance Archive Box Body Ends -->
                        @endif
                        <!-- Insurance Archive Div part Ends -->

                    </div> <!-- Insurance Box Ends -->
                </div><!-- /.tab-pane Insurance Ends -->

                <div class="tab-pane m-b-m-15" id="contact-info"><!-- Contact Info Tab Starts  -->
                    <div class="box box-info no-border no-shadow"><!-- Contact Box Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10">
                            @if($patients->contact_details)
                            <a href="{{url('patients/'.$id.'/edit/contact/more')}}" class="pull-right font600 font14 p-l-10" style="border-left:1px solid #ccc;"><i class="fa fa-plus"></i> New Contacts </a>
                            @endif
                            <a href="{{url('patients/'.$id.'/edit/contact')}}" class="pull-right margin-r-5 font600 font14">@if($patients->contact_details) <i class="fa fa-edit"></i> Edit @else <i class="fa fa-plus"></i> Add Contacts @endif</a>
                        </div>
                        <div class="box-body patient-tab-bg margin-t-20"><!-- Contact Box Body Starts -->
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!--  Contact Col Starts -->
                                @if(!$patients->contact_details)
                                <h5 class="bg-white padding-10">No Records Found !!!</h5>
                                @else
                                <?php $contact_count = 0; ?>
                                @foreach($patients->contact_details as $contact)
                                @if($contact->category == "Guarantor")
                                <div class="margin-t-10"><!-- Guarantor Starts --> 
                                    <div class="box box-view no-shadow"><!--  Box Starts -->
                                        <div class="box-header-view">
                                            <i class="livicon" data-name="users-add"></i> <h3 class="box-title">{{ $contact->category }}</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div><!-- /.box-header -->
                                        <div class="box-body p-b-0"><!-- Guarantor Box Body Starts -->
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="table-responsive">
                                                    <table class="table-striped-view table right-border">
                                                        <tbody>
                                                            <tr>
                                                                <td>Name</td>
                                                                <td>
                                                                    <?php $contact_name = App\Http\Helpers\Helpers::getNameformat("$contact->guarantor_last_name","$contact->guarantor_first_name","$contact->guarantor_middle_name"); ?> 
                                                                    {{ $contact_name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Relationship</td>
                                                                <td>{{@$contact->guarantor_relationship}}</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Home Phone</td>										
                                                                <td>{{ @$contact->guarantor_home_phone }} @if(@$contact->guarantor_phone_ext !='')<span class="ph-ext-bg">{{ @$contact->guarantor_phone_ext }}</span>@endif</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Cell Phone</td>
                                                                <td>{{@$contact->guarantor_cell_phone}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>&emsp;</td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                                <table class="table-striped-view table">                  
                                                    <tbody>
                                                        <tr>
                                                            <td>Email</td>
                                                            <td>
                                                                @if($contact->guarantor_email)
                                                                <a href="mailto:{{@$contact->guarantor_email}}"> {{@$contact->guarantor_email}}</a>
                                                                @endif
                                                            </td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td>Address Line 1</td>
                                                            <td>{{@$contact->guarantor_address1}}</td> 
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address Line 2</td>
                                                            <td>{{@$contact->guarantor_address2}}</td> 
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>City</td>
                                                            <td>{{@$contact->guarantor_city}} @if( @$patients->guarantor_state !='') - <span class="bg-state">{{ @$patients->guarantor_state}}</span> @endif</td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td>Zip Code</td>
                                                            <td>{{@$contact->guarantor_zip5 }}  @if(@$contact->guarantor_zip4 !='')- {{@$contact->guarantor_zip4}}@endif</td> 
                                                            <td>
                                                                @include('practice/layouts/usps_show_form_modal_input',['div_name' => 'js-contact_'.$contact->id, 'af_type' => 'patients', 'af_type_id' => $contact->id, 'af_sub_type' => 'patient_contact_address'])   
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>

                                            </div>
                                        </div><!-- Guarantor Box Body Ends -->
                                    </div><!-- /.Guarantor box Ends-->
                                </div><!-- Guarantor Ends-->
                                @endif

                                @if($contact->category == "Emergency Contact")
                                <div><!--Emergency  Starts --> 
                                    <div class="box box-view no-shadow"><!-- Emergency Box Starts -->
                                        <div class="box-header-view">
                                            <i class="livicon" data-name="users-add"></i> <h3 class="box-title">{{$contact->category}}</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div><!-- /.box-header -->
                                        <div class="box-body p-b-0"><!-- Emergency Box Body Starts -->
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                                <table class="table-striped-view table right-border">
                                                    <tbody>
                                                        <tr>
                                                            <td>Name</td>
                                                            <td>{{@$contact->emergency_last_name.", ".@$contact->emergency_first_name." ".@$contact->emergency_middle_name}}</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Relationship</td>
                                                            <td>{{ @$contact->emergency_relationship }}</td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td>Home Phone</td>												
                                                            <td>{{ @$contact->emergency_home_phone }} @if(@$contact->emergency_phone_ext !='')<span class="ph-ext-bg">{{ @$contact->emergency_phone_ext }}</span>@endif</td>
                                                            <td></td> 
                                                        </tr>

                                                        <tr>
                                                            <td>Cell Phone</td>
                                                            <td>{{@$contact->emergency_cell_phone}}</td>  
                                                            <td></td>
                                                        </tr>     
                                                        
                                                        <tr>
                                                            <td>&emsp;</td>
                                                            <td>&emsp;</td>
                                                            <td></td>
                                                        </tr>  

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                                <table class="table-striped-view table">                    
                                                    <tbody>

                                                        <tr>
                                                            <td>Email</td>
                                                            <td>
                                                                @if($contact->emergency_email)
                                                                <a href="mailto:{{@$contact->emergency_email}}"> {{@$contact->emergency_email}}</a>
                                                                @endif
                                                            </td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td>Address Line 1</td>
                                                            <td>{{@$contact->emergency_address1}}</td> 
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address Line 2</td>
                                                            <td>{{@$contact->emergency_address2}}</td> 
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>City</td>
                                                            <td>{{@$contact->emergency_city}} @if( @$contact->emergency_state !='') - <span class="bg-state">{{ @$contact->emergency_state}}</span> @endif</td>
                                                            <td></td>
                                                        </tr>                                                        
                                                        <tr>
                                                            <td>Zip Code</td>
                                                            <td>{{@$contact->emergency_zip5 }} @if(@$contact->emergency_zip4 !='') - {{@$contact->emergency_zip4}} @endif</td> 
                                                            <td>
                                                                @include('practice/layouts/usps_show_form_modal_input',['div_name' => 'js-contact_'.$contact->id, 'af_type' => 'patients', 'af_type_id' => $contact->id, 'af_sub_type' => 'patient_contact_address']) 
                                                            </td>
                                                        </tr>


                                                    </tbody>
                                                </table>

                                            </div>
                                        </div><!-- Emergency box-body Ends -->
                                    </div><!-- Emergency box Ends-->
                                </div><!-- Emergency Ends-->
                                @endif

                                @if(@$contact->category == "Employer")
                                <div><!-- Employer Starts --> 
                                    <div class="box box-view no-shadow"><!-- Emergency Box Starts -->
                                        <div class="box-header-view">
                                            <i class="livicon" data-name="users-add"></i> <h3 class="box-title">Employer</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div><!-- /.box-header -->
                                        <div class="box-body p-b-0"><!-- Emergency Box-body Starts -->
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                                <table class="table-striped-view table right-border">
                                                    <tbody>
                                                        <tr>
                                                            <td>Employer Status </td>
                                                            <td>{{@$contact->employer_status }}</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Employer Name</td>
                                                            <td>{{@$contact->employer_name}}</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Work Phone</td>												
                                                            <td>{{ @$contact->employer_work_phone }} @if(@$contact->employer_phone_ext !='')<span class="ph-ext-bg">{{ @$contact->employer_phone_ext }}</span>@endif</td>
                                                            <td></td> 
                                                        </tr>
                                                         <tr>
                                                            <td>&emsp;</td>
                                                            <td>&emsp;</td>
                                                            <td></td>
                                                        </tr>  
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                                <table class="table-striped-view table">                    
                                                    <tbody>

                                                        <tr>
                                                            <td>Address Line 1</td>
                                                            <td>{{@$contact->employer_address1}}</td> 
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address Line 2</td>
                                                            <td>{{@$contact->employer_address2}}</td> 
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>City</td>
                                                            <td>{{@$contact->employer_city}} @if( @$contact->employer_state !='') - <span class="bg-state">{{ @$contact->employer_state}}</span> @endif</td>
                                                            <td></td>
                                                        </tr>                                                        
                                                        <tr>
                                                            <td>Zip Code</td>
                                                            <td>{{@$contact->employer_zip5}} @if($contact->employer_zip4 !='') - {{$contact->employer_zip4}} @endif</td> 
                                                            <td>
                                                                @include('practice/layouts/usps_show_form_modal_input',['div_name' => 'js-contact_'.$contact->id, 'af_type' => 'patients', 'af_type_id' => $contact->id, 'af_sub_type' => 'patient_contact_address'])
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div><!-- Emergency box-body Ends -->
                                    </div><!-- Emergency box Ends-->
                                </div><!-- Employer Ends-->
                                @endif

                                @if($contact->category == "Attorney")
                                <div><!-- Attorney Starts --> 
                                    <div class="box box-view no-shadow"><!-- Attorney Box Starts -->
                                        <div class="box-header-view">
                                            <i class="livicon" data-name="users-add"></i> <h3 class="box-title">Attorney</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div><!-- /.box-header -->
                                        <div class="box-body p-b-0"><!-- Attorney Box Body Starts -->
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                                <table class="table-striped-view table right-border">
                                                    <tbody>
                                                        <tr>
                                                            <td>Adjuster Name</td>
                                                            <td>{{@$contact->attorney_adjuster_name }}</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>DOI</td>
                                                            <td><span class="bg-date">@if(@$contact->attorney_doi != "0000-00-00") {{ App\Http\Helpers\Helpers::dateFormat($contact->attorney_doi,'date') }}@else   @endif</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Claim Number</td>
                                                            <td>{{@$contact->attorney_claim_num}}</td>
                                                            <td></td>
                                                        </tr>                                                        

                                                        <tr>
                                                            <td>Work Phone</td>												
                                                            <td>{{ @$contact->attorney_work_phone }} @if(@$contact->attorney_phone_ext !='')<span class="ph-ext-bg">{{ @$contact->attorney_phone_ext }}</span>@endif</td>
                                                            <td></td> 
                                                        </tr>
                                                        <tr>
                                                            <td>Fax</td>
                                                            <td>{{@$contact->attorney_fax}}</td>  
                                                            <td></td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 table-responsive">
                                                <table class="table-striped-view table">                    
                                                    <tbody>

                                                        <tr>
                                                            <td>Email</td>
                                                            <td>
                                                                @if($contact->attorney_email)
                                                                <a href="mailto:{{@$contact->attorney_email}}"> {{@$contact->attorney_email}}</a>
                                                                @endif
                                                            </td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td>Address Line 1</td>
                                                            <td>{{@$contact->attorney_address1}}</td> 
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address Line 2</td>
                                                            <td>{{@$contact->attorney_address2}}</td> 
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>City</td>
                                                            <td>{{@$contact->attorney_city}} @if( @$contact->attorney_state !='') - <span class="bg-state">{{ @$contact->attorney_state}}</span> @endif</td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td>Zip Code</td>
                                                            <td>{{@$contact->attorney_zip5 }} @if(@$contact->attorney_zip4 !='') - {{ @$contact->attorney_zip4}} @endif</td> 
                                                            <td>
                                                                @include('practice/layouts/usps_show_form_modal_input',['div_name' => 'js-contact_'.$contact->id, 'af_type' => 'patients', 'af_type_id' => $contact->id, 'af_sub_type' => 'patient_contact_address'])
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div><!-- Attorney box-body Ends -->
                                    </div><!-- Attorney box Ends-->
                                </div><!-- Attorney Ends-->
                                @endif

                                <?php $contact_count++; ?>                  
                                @endforeach
                                @endif                  
                            </div><!-- Contact Col Ends -->
                        </div><!-- Contact Box Body Ends -->
                    </div><!-- Contact Bos Ends -->						
                </div><!--Contact tab pane ends -->

                <div class="tab-pane m-b-m-15" id="authorization"><!-- Authorization Tab pane Starts -->
                    <div class="box no-border no-shadow"><!-- Authorization Box Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10">
                            @if($patients->authorization_details)
                            <a href="{{url('patients/'.$id.'/edit/authorization/more')}}" class="font600 pull-right font14" style="border-left:1px solid #ccc; padding-left: 10px;"><i class="fa fa-plus"></i> Add Authorization</a>
                            @endif
                            <a href="{{url('patients/'.$id.'/edit/authorization')}}" class="font600 pull-right margin-r-10 font14">@if($patients->authorization_details) <i class="fa fa-edit"></i> Edit @else<i class="fa fa-plus"></i> Add Authorization @endif</a>
                        </div>
                        <div class="box-body patient-tab-bg margin-t-20"><!-- Authorization Box Body Starts -->

                            <?php
								$auth_count_rev_order = count(@$patients->authorization_details) - 1;
                            ?>

                            @if(!$patients->authorization_details)
                            <h5 class="bg-white padding-10 margin-t-10">No Records Found !!!</h5>
                            @else
                            @foreach($patients->authorization_details as $authorization)
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10"><!--  Authorization Col Content Starts -->
                                <div class="box box-view no-shadow"><!--  Box Starts -->
                                    <div class="box-header-view">
                                        <i class="livicon" data-name="users-add"></i> <h3 class="box-title">Authorization</h3>
                                        <div class="box-tools pull-right">
                                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div><!-- /.box-header -->
                                    <div class="box-body p-b-0"><!-- Box Body Starts -->
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <div class="table-responsive">
                                                <table class="table-responsive table-striped-view table right-border">
                                                    <tbody>
                                                        <tr>
                                                            <td>Auth No</td>
                                                            <td>{{$authorization->authorization_no}} <a id="document_add_modal_link_authorization_number" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients::Authorization::'.@$id.'/'.@$authorization->document_save_id.'/Authorization_Documents_Pre_Authorization_Letter')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="margin-l-10 {{Config::get('siteconfigs.document_upload_modal_icon')}} @if($authorization->authorization_no='') margin-t-m-8 @endif"></i></a></td>
                                                            <td>
                                                                
                                                            </td>
                                                        </tr>                                                        

                                                        @if(@$registration->requested_date ==1)
                                                        <tr>
                                                            <td>Requested Date</td>
                                                            <td>@if((@$authorization->requested_date) != "0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat(@$authorization->requested_date,'date') }} @endif </td>
                                                            <td></td>
                                                        </tr>
                                                        @endif
                                                        @if(@$registration->contact_person ==1)
                                                        <tr>
                                                            <td>Contact Person</td>
                                                            <td>{{@$authorization->authorization_contact_person}}</td>
                                                            <td></td>
                                                        </tr>
                                                        @endif

                                                        @if(@$registration->alert_on_appointment ==1)
                                                        <tr>
                                                            <td>Alert On Appointment</td>
                                                            <td><span class="patient-status-bg-form @if($authorization->alert_appointment =='Yes') label-success @else label-danger @endif">{{ @$authorization->alert_appointment }}</span></td>  
                                                            <td></td>
                                                        </tr>
                                                        @endif
                                                        @if(@$registration->allowed_visit ==1)
                                                        <tr>
                                                            <td>Allowed Visits</td>
                                                            <td>{{ @$authorization->allowed_visit }}</td>  
                                                            <td></td>
                                                        </tr>
                                                        @endif                                                        
                                                        <tr>
                                                            <td>&emsp;</td>
                                                            <td></td>  
                                                            <td></td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <div class="table-responsive">
                                                <table class="table-responsive table-striped-view table">
                                                    <tbody>
                                                        <tr>
                                                            <td>Insurance</td>
                                                            <td>{{ @$authorization->insurance_details->insurance_name }}</td>  
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>POS</td>
                                                            <td>{{ @$authorization->pos_detail->pos }}</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Start Date</td>
                                                            <td>@if((@$authorization->start_date) != "0000-00-00")<span class="bg-green-date">{{ App\Http\Helpers\Helpers::dateFormat(@$authorization->start_date,'date') }} </span>@endif</td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td>End Date</td>												
                                                            <td>@if((@$authorization->end_date) != "0000-00-00")<span class="bg-red-date">{{ App\Http\Helpers\Helpers::dateFormat(@$authorization->end_date,'date') }}  </span> @endif</td>
                                                            <td></td>
                                                        </tr>
                                                        @if(@$registration->work_phone ==1)
                                                        <tr>
                                                            <td>Work Phone</td>
                                                            <td>{{ @$authorization->authorization_phone }} @if(@$authorization->authorization_phone_ext !='') <span class="ph-ext-bg">{{ @$authorization->authorization_phone_ext }}</span> @endif</td>   												 
                                                            <td></td>
                                                        </tr>
                                                        @endif

                                                        @if(@$registration->alert_on_billing ==1)
                                                        <tr>
                                                            <td>Alert On Billing</td>
                                                            <td><span class="patient-status-bg-form @if($authorization->alert_billing == 'Yes') label-success @else label-danger @endif">{{ @$authorization->alert_billing }}</span></td>  
                                                            <td></td>
                                                        </tr>
                                                        @endif
                                                        @if(@$registration->total_allowed_amount ==1)
                                                        <tr>
                                                            <td>Allowed Amount</td>
                                                            <td>${{ @$authorization->allowed_amt }}</td> 
                                                            <td></td>
                                                        </tr>
                                                        @endif
                                                        @if(@$registration->amount_used ==1)
                                                        <tr>
                                                            <td>Used Amount</td>                                                
                                                            <td>${{ @$authorization->amt_used }}</td>
                                                            <td></td> 
                                                        </tr>
                                                        @endif
                                                        @if(@$registration->amount_remaining ==1)
                                                        <tr>
                                                            <td>Remaining Amount</td>
                                                            <td>${{ @$authorization->amt_remaining }}</td>  
                                                            <td></td>
                                                        </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @if($authorization->authorization_notes != '')
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <p>
                                                <span class="med-orange"><b>Notes :</b> </span>
                                                {{$authorization->authorization_notes}}
                                            </p>
                                        </div>
                                        @endif
                                    </div><!-- /.box-body --> 
                                </div><!-- /.box Ends-->
                            </div><!-- Authorization Col Ends -->
                            <?php $auth_count_rev_order --; ?>
                            @endforeach
                            @endif
                        </div><!-- Authorization Box Body Ends -->
                    </div><!-- Authorization Box Ends -->
                </div><!-- Authorization tab-pane Ends --> 

            </div><!-- /.tab-content Ends -->
        </div><!-- /.nav tab custom Ends -->
    </div><!-- /med-tab ends  -->
</div><!-- Main Col Ends -->


<div id="form-address-modal" class="modal fade in">
    @include ('practice/layouts/usps_form_modal')
</div>


<div id="eligibility_content_popup" class="modal fade in">
    @include ('layouts/eligibility_modal_popup')
</div>

@stop 