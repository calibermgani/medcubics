@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Ledger </span></small>
        </h1>
        <ol class="breadcrumb">    
            <li><a href="javascript:void(0)" accesskey="b" data-url="{{url('patients')}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a  style="cursor:pointer;" accesskey="a" onClick="window.open('{{url('/patients/create')}}', '_blank')"> <i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="New Patient"></i></a></li>	
            @include ('patients/layouts/swith_patien_icon')	
            <li><a href="javascript:void(0);" accesskey="p" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>  
            <li><a href="#js-help-modal" data-url="{{url('help/ledger')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop


@section('practice')
<!-- Filters Starts -->  
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-15 no-print">          
    <p class="pull-right font13 font600">
        <span class="med-orange">Row Filters : </span>&emsp;
        <span class="med-green">
            {!! Form::checkbox('filter', 'coverage_detail', true, ['class'=>"form-horizontal js_row_filter",'id'=>'c-coverage']) !!}<label for="c-coverage" class="no-bottom font600 cur-pointer">Coverage</label> &emsp;
            {!! Form::checkbox('filter', 'document_detail', false, ['class'=>"form-horizontal js_row_filter",'id'=>'c-documents']) !!}<label for="c-documents" class="no-bottom font600 cur-pointer">Documents</label> &emsp;
            {!! Form::checkbox('filter','notes',  false, ['class'=>"form-horizontal js_row_filter",'id'=>'c-notes']) !!}<label for="c-notes" class="no-bottom font600 cur-pointer">Notes</label>
        </span>
    </p> 
</div>
<!-- Filters Ends -->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Outer Layout Starts -->
    <div class="box no-border no-shadow no-bottom"><!-- Outer Box tarts -->
        <div class="box-body table-responsive p-b-0"><!-- Outer Box Body Starts -->

            <div class="row no-padding  pr-l-2"><!-- Row Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Demo Financials Red Alerts  Starts -->
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 no-padding"><!-- Demographics Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0">                            
                            <div class="box box-view-border no-shadow sm-m-b-0" style="border-radius: 4px 0px 0px 0px;"><!-- Demo Box Starts -->
                                <div class="box-body box-profile m-b-m-12"><!-- Demo Box Body Starts -->
                                    <center>
										<?php
											$patient_name = App\Http\Helpers\Helpers::getNameformat("$patients->last_name","$patients->first_name","$patients->middle_name"); 
											$filename = $patients->avatar_name.'.'.$patients->avatar_ext;
											$img_details = [];
											$img_details['module_name']='patient';
											$img_details['file_name']=$filename;
											$img_details['practice_name']="";
											
											$img_details['class']='ledger-profile';
											$img_details['alt']='ledger-image';
											$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
										?>
										{!! $image_tag !!}
                                        <i class="fa fa-circle @if($patients->status == 'Active')med-green-o @else med-red @endif margin-l-m-15 hidden-print patient-status-ledger"  data-placement="bottom"  data-toggle="tooltip" data-original-title="{{ $patients->status }} Patient"></i>   <!-- Dont remove this inline style -->                                     
                                    </center> 

                                    <h3 class="profile-username text-center">@if(@$patients->title){{ @$patients->title }}. @endif{{ $patient_name }}</h3>
                                    <h5 class="med-orange text-center">@if($patients->dob != "0000-00-00" && $patients->dob != "" && $patients->dob != "1901-01-01")<i class="fa fa-birthday-cake no-print"></i> {{ App\Http\Helpers\Helpers::dateFormat($patients->dob,'dob').", "}}{{ App\Http\Helpers\Helpers::dob_age(@$patients->dob) }} - @endif  {{ $patients->gender }}</h5>
                                    <span id="age"></span>
                                    <hr class="margin-t-0 margin-b-4 hide">    
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-6">
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 p-l-0 ">
                                            <h6 class="med-green pr-l-5">Acc No</h6>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                            <span style="margin-top: 10px; margin-bottom: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;">
                                                {{ $patients->account_no }}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 p-l-0">
                                            <h6 class="med-green pr-l-5">SSN</h6>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 issuecolor">
                                            <span class="" style="margin-top: 10px; margin-bottom: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;">@if($patients->ssn != ""){{ $patients->ssn }} @else <span class="nil">- Nil - </span> @endif</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 p-l-0">
                                            <h6 class="med-green pr-l-5">Address </h6>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 issuecolor" style="margin-bottom: 7px;">
                                            <span class="" style="margin-top: 10px; margin-bottom: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;">@if($patients->address1 !=''){{ $patients->address1 }} , </br> @else <span class="nil" style="margin-top: 10px; margin-bottom: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;">- Nil -</span> </br> @endif</span>
                                            <span class="" style="margin-top: 10px; margin-bottom: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;">@if($patients->city !=''){{ $patients->city }}, {{ $patients->state }} @else &emsp; @endif</span> </br>

                                            <span class="" style="margin-top: 10px; margin-bottom: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;">{{ $patients->zip5 }}@if($patients->zip4 != "") - {{ $patients->zip4 }}@endif @if($patients->zip5 =='') &emsp; @endif  </br></span>
                                        </div>   
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 p-l-0 p-r-0">
                                            <h6 class="med-green pr-l-5">Home Ph</h6>
                                        </div>
                                        <?php $phone_class = (isset($patients->phone) && !empty($patients->phone)) ? "js-callmsg-clas cur-pointer" : "" ?>                                       
                                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 issuecolor" >
                                            <span class="{{$phone_class}} " id="js-callmsg-clas" data-phone= "{{@$patients->phone}}" data-user_id="{{$patients->id}}" data-user_type="patient" style="margin-top: 10px; margin-bottom: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;">@if(@$patients->phone != '') {{ @$patients->phone}} <span class=" hidden-print fa fa-phone-square margin-l-4 med-green" data-placement="bottom"  data-toggle="tooltip" data-original-title="Make a Call" style="color:#00877f !important;"></span> @else - Nil - @endif</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 p-l-0 p-r-0">
                                            <h6 class="med-green pr-l-5">Cell Ph</h6>
                                        </div>
                                        <?php $phone_class = (isset($patients->mobile) && !empty($patients->mobile)) ? "js-callmsg-clas cur-pointer" : "" ?>                                       
                                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 issuecolor" >
                                            <span class="{{$phone_class}} " id="js-callmsg-clas-cell" data-phone= "{{@$patients->mobile}}" data-user_id="{{$patients->id}}" data-user_type="patient" style="margin-top: 10px; margin-bottom: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;">@if(@$patients->mobile != '') {{ @$patients->mobile}} <span class="hidden-print fa fa-phone-square margin-l-4 med-green" data-placement="bottom"  data-toggle="tooltip" data-original-title="Make a Call" style="margin-top: 10px; margin-bottom: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;"></span> @else - Nil - @endif</span>
                                        </div>
                                    </div>
									<?php /*
                                    <!--   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-1 pr-b-20">
                                           <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 p-l-0">
                                               <h6 class="med-green pr-l-5">Work Ph</h6>
                                           </div>
                                           <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                               <h6>@if($patients->work_phone != ""){{ $patients->work_phone }} @else <span class="nil">- Nil - </span> @endif</h6>
                                           </div>  
                                       </div>-->
									*/ ?>
                                </div><!-- Demo box-body Ends -->
                            </div><!-- Demo box Ends -->
                        </div><!-- /.col -->
                    </div><!-- Demographics Ends -->

                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 no-padding pr-border"><!-- Financial Red Alert Dates Starts -->
                        <div class="col-lg-4 col-md-4 col-sm-12 col-sm-04 col-xs-12 no-padding "><!-- Financial Col Starts -->
                            <div class="box box-view-border no-shadow no-border-radius no-b-l no-b-r sm-m-b-0 no-b-b pr-no-border-r"><!-- Financial Box Starts -->
                                <div class="box-header-view-white m-b-m-10 no-border-radius pr-t-5" style="color: #697d94"><!-- Box Header Starts -->
                                    <span class="livicon" data-color="#f07d08" data-name="responsive-menu"></span>
                                    <span style="color: #697d94; font-size: 13px; font-weight: 700;"> Financials</span>                       
                                </div><!-- /.box-header Ends  -->
                                <div class="box-body table-responsive"><!-- Financial Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10 pr-l-5">

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Billed</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-5 col-xs-12 no-padding text-right" style="margin-top: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;" >
                                                <span class="pr-r-10 ">{!! App\Http\Helpers\Helpers::priceFormat(@$patients->financial_data->billed)!!}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Unbilled</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-5 col-xs-12 no-padding  text-right" style="font-weight: 500; font-family: 'Maven Pro', sans-serif;" >
                                                <span class="text-right pr-r-10 ">{!!App\Http\Helpers\Helpers::priceFormat(@$patients->financial_data->unbilled)!!}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Ins Payment</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-5 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-orange text-right pr-r-10"> {!! App\Http\Helpers\Helpers::priceFormat(@$patients->financial_data->ins_paid) !!}</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Pat Payment</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-5 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-orange text-right pr-r-10"> {!! App\Http\Helpers\Helpers::priceFormat(@$patients->financial_data->pat_paid) !!}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- Financial box-body Ends -->
                            </div><!-- Financial box Ends -->
                        </div><!-- Financial Col Ends -->

                        <div class="col-lg-4 col-md-4 col-sm-12 col-sm-04 col-xs-12 p-r-0 p-l-0 pr-t-m-20"><!-- Red Alerts Starts -->
                            <div class="box box-view-border no-shadow no-border-radius no-b-b no-b-l no-b-r sm-m-b-0 pr-no-border-r"><!-- Red Alert Box Starts -->
                                <div class="box-header-view-white m-b-m-10 no-border-radius pr-p-t-20" style="color: #697d94"><!-- Box Header Starts -->
                                    <span class="livicon" data-color="#f07d08" data-name="responsive-menu"></span> 
                                    <span class="" style="font-weight: 700; font-family: 'Maven Pro', sans-serif;" > Red Alerts</span>                       
                                </div><!-- /.box-header ends  -->
                                <div class="box-body table-responsive"><!-- Red Alert Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10 pr-l-5 ">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Statement</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding" style="margin-top: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;" >
                                                <span class="">@if(@$patients->redalert_data->statement!='')
                                                    <span>{{ @$patients->redalert_data->statement }}</span> @else <span class="nil">- Nil - </span> @endif</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Statement Sent</h6>
                                            </div>
                                            <!-- Popup message open from Patient_statement.js called here-->
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding " style=" font-weight: 500; font-family: 'Maven Pro', sans-serif;" >
                                                <span class="">@if(@$patients->redalert_data->statement_sent!='') {{ $patients->redalert_data->statement_sent }} @else 0 @endif <a id="js_get_statementhistory"  href="{{ url('statementhistory/'.$patients->id) }}"> @if(@$patients->redalert_data->statement_sent !='') <i class="fa fa-comments med-orange margin-l-10"></i> @endif </a> </span>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Last Statement</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding " style=" font-weight: 500; font-family: 'Maven Pro', sans-serif;" >
                                                <span class="">@if(@$patients->redalert_data->last_statement != '-' && @$patients->redalert_data->last_statement !='0000-00-00' &&  @$patients->redalert_data->last_statement != '')
                                                    <span class="bg-date">{{ App\Http\Helpers\Helpers::dateFormat($patients->redalert_data->last_statement ,'date') }}</span> @else <span class="nil">- Nil - </span> @endif</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding pr-b-4">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Aging Days</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding " style=" font-weight: 500; font-family: 'Maven Pro', sans-serif;" >
                                                <?php
                                                    $fdate = App\Models\Payments\ClaimInfoV1::selectRaw('MIN(DATE(date_of_service)) as date')->where('status','!=','Paid')->where('patient_id',App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patients->id,'decode'))->where('date_of_service','<>',"0000-00-00")->pluck('date')->first();
                                                    if(empty($fdate)) {
                                                        $count = 0;
                                                    } else {
                                                        $datetime1 = new DateTime($fdate);
                                                        $datetime2 = new DateTime(App\Http\Helpers\Helpers::timezone(date('Y-m-d H:i:s'),'Y-m-d'));
                                                        $interval = $datetime1->diff($datetime2);
                                                        $count = $interval->format('%a');//now do whatever you like with $days
                                                    }
                                                    $ar_days = ($count == 1 || $count == 0) ? ($count == 0 ? "0" : $count . " Day") : $count . " Days";
                                                ?>
                                                <span class="">{{ $ar_days }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- Red Alert box-body ends -->
                            </div><!-- Red Alert box ends -->
                        </div><!-- Red Alert Ends -->

                        <div class="col-lg-4 col-md-4 col-sm-12 col-sm-04 col-xs-12 p-l-0 "><!-- Date col starts -->
                            <div class="box box-view-border no-shadow no-b-l no-b-b pr-no-border-r p-b-12" style="border-radius:0px 4px 0px 0px;"><!-- Dates Box Starts -->
                                <div class="box-header-view-white m-b-m-10" style="border-radius: 0px 4px 0px 0px; color: #697d94">
                                    <span class="livicon" data-color="#f07d08" data-name="responsive-menu"></span> <span class="" style="font-weight: 700; font-family: 'Maven Pro', sans-serif;"> Dates</span>                                
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive"><!-- Date box body starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10 pr-l-5">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Acc Created</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding" style="margin-top: 10px; font-weight: 500; font-family: 'Maven Pro', sans-serif;" >
                                                <span><span class=" bg-date"> {{ App\Http\Helpers\Helpers::dateFormat($patients->created_at ,'date') }}</span></span>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Last Appt</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding " style=" font-weight: 500; font-family: 'Maven Pro', sans-serif;" >
                                                <span> @if(@$patients->financial_data->last_appt!='-')
                                                    <span class="bg-date">{{ App\Http\Helpers\Helpers::dateformat($patients->financial_data->last_appt ,'date') }}</span> @else <span class="nil">- Nil - </span> @endif</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Future Appt</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10" style=" font-weight: 500; font-family: 'Maven Pro', sans-serif;">
                                                <h6> @if(@$patients->financial_data->future_appt!='-')
                                                    <span class="bg-date">{{ App\Http\Helpers\Helpers::dateFormat($patients->financial_data->future_appt ,'date') }}</span> @else <span class="nil">- Nil - </span> @endif</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding pr-b-4">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Last Payment</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10" style=" font-weight: 500; font-family: 'Maven Pro', sans-serif;">
                                                <h6>@if(@$patients->financial_data->last_pay!='')
                                                    <span class="bg-date">{{ App\Http\Helpers\Helpers::dateFormat(@$patients->financial_data->last_pay,'date') }}</span> @else <span class="nil">- Nil - </span>@endif</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- Date box-body Ends -->
                            </div><!-- Date box Ends -->
                        </div><!-- Date Col Ends -->

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 margin-t-m-20 pr-t-b pr-width-100"><!-- Outstanding Col Starts -->                                                                             
                            <div class="box box-view-border no-shadow no-border-radius no-b-l no-b-t "><!-- Outstanding Box Starts -->
                                <div class="box-header-view-white no-border-radius pr-t-5" style="color: #697d94;">
                                    <span class="livicon" data-color="#f07d08" data-name="responsive-menu"></span><span class="" style="font-weight: 700; font-family: 'Maven Pro', sans-serif;"> Outstanding AR</span>                     
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive pr-r-5 p-t-2"><!-- Outstanding Box-body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">
                                        <table id="" class="table table-borderless pr-r-m-20" style="table-layout: fixed;">	
                                            <thead>
                                                <tr>
                                                    <th class="font600 med-green text-center" style="background: #96dcd8; border-right: 5px solid #fff">&emsp;</th>
                                                    <th class="font600 med-green text-right" style="background: #96dcd8; border-right: 5px solid #fff">0-30</th>
                                                    <th class="font600 med-green text-right" style="background: #96dcd8; border-right: 5px solid #fff">31-60</th>
                                                    <th class="font600 med-green text-right" style="background: #96dcd8; border-right: 5px solid #fff">61-90</th>
                                                    <th class="font600 med-green text-right" style="background: #96dcd8; border-right: 5px solid #fff">91-120</th>
                                                    <th class="font600 med-green text-right" style="background: #96dcd8; border-right: 5px solid #fff">>120</th>
                                                    <th class="font600 med-green text-right" style="background: #96dcd8;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody >
                                                @foreach($patients->outstanding_data as $key_name =>$row_name)
                                                <?php $get_total=$row_name[5]; ?>
                                                @if($key_name =="Percentage") 
                                                <?php array_pop($row_name); $total = array_sum($row_name); ?>
                                                @endif
                                                <tr> 
                                                    @if($key_name !="Percentage")
                                                    <td class=" med-green font600">{{ $key_name }}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[0]) !!}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[1]) !!}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[2]) !!}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[3]) !!}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[4]) !!}</td>
                                                    <td class="font600 text-right" @if($key_name =="Percentage") style="border-radius:0px 0px 4px 0px;" @endif>{!! App\Http\Helpers\Helpers::priceFormat($get_total) !!}</td>

                                                    @else
                                                    <td class="med-green font600 border-radius-4">{{ $key_name }}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[0]) !!}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[1]) !!}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[2]) !!}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[3]) !!}</td>
                                                    <td class='@if($key_name =="Percentage") med-green font600 @endif text-right' >{!! App\Http\Helpers\Helpers::priceFormat($row_name[4]) !!}</td>
                                                    <td class="med-green font600 text-right" @if($key_name =="Percentage") style="border-radius:0px 0px 4px 0px;" @endif>{!! $get_total !!}%</td>
                                                    @endif	
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>    
                                    </div>
                                </div><!-- Outstanding box-body Ends -->
                            </div><!-- Outstanding box Ends -->
                        </div><!-- Outstanding Col Ends -->    
                    </div><!-- Financial Red Alert Dates Ends -->
                </div><!-- Demo Financials Red Alerts  Ends Full 1st row -->
                <!-- Coverage detail start -->

                <div class="js_coverage_detail" >
                    @include ('patients/ledger/coveragedetail')
                </div>
                <!-- Coverage detail end -->

                <!-- Documents appointment vob autherisation detail start -->
                <div class="js_document_detail hide" >
                    @include ('patients/ledger/documentdetail')
                </div>
                <!-- Documents appointment vob autherisation detail end -->

                <!-- Notes detail start -->
                <div class="js_notes hide" >
                    @include ('patients/ledger/notes')
                </div>
                <!-- Notes detail end -->

                <!-- Claims detail start -->

                <div class="js_claims" >
                    @include ('patients/ledger/claims')
                </div>
                <!-- Claims detail end -->
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
    <style>
         
        .printcolorissue{
            color: #868695 !important;
            float: right;
            font-weight: 600;
            font-family: 'Maven Pro', sans-serif;
            padding-bottom: -5px;
            margin-top: 10px;
        }
        .m-b-m-12 {
            margin-bottom: 3px !important;
        }
        .col-lg-4 {
            width: 33.33333333% !important;
        }
    </style>    
</div>
@if(!empty(@$alert_notes))
<span id="showmenu" class="cur-pointer alertnotes-icon"><i class="fa fa-bell med-orange"></i></span>
<div class="snackbar-alert success menu">
    <h5 class="med-orange margin-b-5 margin-l-15 margin-t-6"><span>Alert Notes</span> <span class="pull-right cur-pointer" ><i class="fa fa-times" id="showmenu1"></i></span></h5>            
    <p>{!! $alert_notes !!}</p>
</div>
@endif

<!--End-->
@stop
@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
		$(document).on('ifToggled click change', '.js_row_filter', function (event) {
			var get_current_val = $(this).val();
			var obj = $(".js_" + get_current_val);
			if ($(this).is(':checked')) {
				obj.removeClass("hide");
			}
			else if ($(this).not(':checked')) {
				obj.addClass("hide");
			}
		});
		
		$(document).on('click', '.pagination li a', function (e) {
			e.preventDefault();
			var pagination = $(this).attr("href").split('page=');
			var get_site_url = pagination[0].split("ledger?");			
			var url = get_site_url[0].replace("ledger/ajax/pagination?", "") + 'ledger/ajax/pagination?page=' + pagination[1];
			var form_value = $('.js_ledger_claim_search').parents('form').serialize();
			claimAjaxResponse(url, form_value);
		});
		
		$(document).on('keyup ifToggled', '.js_ledger_claim_search', function (e) {
			var count = this.value.length;
			if (count > 2 || count == 0) {
				e.preventDefault();
				var form_url = $(this).parents('form').attr('action');
				var form_value = $(this).parents('form').serialize();
				claimAjaxResponse(form_url, form_value);
			}
		});
    });
    
	$(function () {
		$(window).load(function () {
			var get_current_val = $(".js_control_height").height() + 10;
			if (get_current_val > 260)
				get_current_val = 249;
			$(".js_control_height").parents("div.ledger-scroll").css("height", get_current_val);
			$(".js_control_height").parents("div.slimScrollDiv").css("height", get_current_val);
		});
	});
	
    function claimAjaxResponse(url, formData) {
		processingImageShow("#js_ajax_part", "show");
		$.ajax({
			type: 'POST',
			url: url,
			data: formData,
			success: function (response) {
				//$(".js_spin_image").addClass("hide");
				processingImageShow("#js_ajax_part", "hide");
				$(".js_claim_list_part").html(response);
				$.AdminLTE.boxWidget.activate();
				var selector_name = $(".js_claims");
				var str = $('.js_ledger_claim_search').val();
				selector_name.highlight($.trim(str));
			}
		});
	}
</script>
@endpush