@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <?php 
            if(!isset($get_default_timezone)){
                $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
            }
        ?>
        <h1>
            <small class="toolbar-heading"><i class="fa fa-pencil font14"></i> Charges <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Charge</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a accesskey="b" href="javascript:void(0)" data-url="{{ url('charges') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/charges')}}" class="js-help" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice-info')
@include ('patients/billing/model-inc')
<div class="js-replace-patient-info">
    @include ('charges/charges/tabs1') 
</div>
@stop
    @section('practice')    
	<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.charges_create") }}' />		
     <div class="js-replace-section">
        <div class="js-disable">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20">
        <div class="box-body-block"><!--Background color for Inner Content Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border no-padding margin-t-10 border-green"><!-- General Details Full width Starts -->
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 tab-r-b-1 no-padding border-green"><!-- Only general details content starts -->
                <div class="box no-border  no-shadow"><!-- Box Starts -->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  tab-r-b-1 border-green"><!--  1st Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="font600 bg-white padding-0-4">General Details</span>
                        </div>
                            <div class="box-body form-horizontal margin-t-5"><!-- Box Body Starts -->
                                <div class="form-group form-group-billing">
                                    {!! Form::label('Rendering Provider', 'Rendering Provider', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green font600 star']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup @if($errors->first('rendering_provider_id')) error @endif">
                                        {!! Form::select('rendering_provider_id', array('' => '-- Select --')+(array)$rendering_providers, @$charge_session_value->rendering_provider_id,['class'=>'select2 form-control']) !!}  
                                        {!! $errors->first('rendering_provider_id', '<p> :message</p>') !!}
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                        <!-- Popup Starts -->
                                        @if(!empty($charge_session_value->rendering_provider_id))
                                        <div class="dropdown user user-menu">
                                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span class="hidden-xs">
                                                    <i class="fa fa-comments med-green form-icon-billing"></i>
                                                </span>
                                            </a>                                            
                                        </div>
                                        @endif
                                        <!-- Popup Ends -->
                                    </div>
                                </div>

                                <div class="form-group form-group-billing">
                                    {!! Form::label('Referring Provider', 'Referring Provider', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green font600']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup @if($errors->first('referring_provider_id')) error @endif">
                                        {!! Form::text('referring_provider_id',null,['maxlength'=>'25','class'=>'form-control bg-white input-sm-header-billing']) !!}
                                        {!! $errors->first('referring_provider_id', '<p> :message</p>') !!}
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                        <!-- Popup Starts -->
                                        <i class="fa fa-plus-circle med-green form-icon-billing" style="margin-left: 6px;"></i>
                                        <!-- Popup Ends -->
                                    </div>
                                </div>

                                <div class="form-group form-group-billing">
                                    {!! Form::label('Billing Provider', 'Billing Provider', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green font600 star']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup @if($errors->first('billing_provider_id')) error @endif">
                                        {!! Form::select('billing_provider_id', array('' => '-- Select --')+(array)$billing_providers,@$charge_session_value->billing_provider_id,['class'=>'select2 form-control']) !!}
                                        {!! $errors->first('billing_provider_id', '<p> :message</p>') !!}
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">

                                        @if(!empty($charge_session_value->billing_provider_id))
                                        <div class="dropdown user user-menu">
                                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span class="hidden-xs">
                                                    <i class="fa fa-comments med-green form-icon-billing"></i>
                                                </span>
                                            </a>                                            
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group form-group-billing">
                                    {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600 star']) !!}
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup @if($errors->first('facility_id')) error @endif">  
                                        {!! Form::select('facility_id', array(''=>'-- Select --')+(array)$facilities, @$charge_session_value->facility_id,['class'=>'select2 form-control','id'=>'facility_id']) !!}   
                                        {!! $errors->first('facility_id', '<p> :message</p>') !!}
                                    </div>      
                                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                        @if(!empty($charge_session_value->facility_id))
                                        <div class="dropdown user user-menu">
                                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span class="hidden-xs">
                                                    <i class="fa fa-comments med-green form-icon-billing"></i>
                                                </span>
                                            </a>                                           
                                        </div>
                                        @endif
                                        <!-- Popup Ends -->
                                    </div>                            
                                </div>

                                <div class="form-group form-group-billing">
                                    {!! Form::label('Billed To', 'Billed To', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600 star']) !!}
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">  
                                        {!! Form::select('insurance_id', array(''=>'-- Select --'), null,['class'=>'select2 form-control','id'=>'insurance_id']) !!}                                   
                                    </div>                                     
                                </div>
                                <div class="form-group form-group-billing">
                                    {!! Form::label('authorization', 'Auth No', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">  
                                        {!! Form::text('authorization',null,['autocomplete'=>'nope' ,'maxlength'=>'25','class'=>'form-control input-sm-header-billing bg-white','readonly'=>'readonly']) !!}
                                    </div>                                       
                                </div>

                            </div><!-- /.box-body Ends-->
                        </div><!--  1st Content Ends -->
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" ><!--  2nd Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green no-padding margin-t-m-10">&emsp; </div>
                        <div class="box-body form-horizontal margin-t-5 js-address-class" id="js-address-primary-address">
                            <div class="form-group form-group-billing">                             
                                {!! Form::label('Admission', 'Admission', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing  med-green font600']) !!}                           
                                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-5 ">
                                    <i class="fa fa-calendar-o form-icon-billing"></i>  
                                    {!! Form::text('admit_date',(isset($claims->admit_date) && $claims->admit_date != '1970-01-01')?@date('m/d/Y',strtotime($claims->admit_date)):'',['class'=>'form-control bg-white input-sm-header-billing call-datepicker dm-date p-r-0','placeholder'=>"From"]) !!}
                                </div>        

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5 ">
                                    {!! Form::text('discharge_date',(isset($claims->discharge_date) && $claims->discharge_date != '1970-01-01')?@date('m/d/Y',strtotime($claims->discharge_date)):'',['class'=>'form-control bg-white input-sm-header-billing call-datepicker dm-date p-r-0','placeholder'=>"To"]) !!}   
                                </div>
                            </div>
                            <div class="form-group form-group-billing">
                                {!! Form::label('DOI', 'DOI',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 p-r-0']) !!} 
                                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-4">
                                    <i class="fa fa-calendar-o form-icon-billing"></i> 
                                    {!! Form::text('doi',(@$claims->doi && $claims->doi !='0000-00-00 00:00:00')?date('m/d/Y', strtotime($claims->doi)):'',['class'=>'form-control bg-white dm-date input-sm-header-billing', 'id' => 'date_of_injury']) !!}
                                </div>
                                {!! Form::label('pos', 'POS',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label-billing med-green font600 p-l-0']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">  
									@if(!empty($charge_session_value->pos_code))
										{!! Form::text('pos_name',  @$charge_session_value->pos_code, ['class'=>'form-control bg-white input-sm-header-billing', 'id' => 'pos_name' ,'readonly' => 'readonly','tabindex'=>'-1']) !!}
									@else
										{!! Form::text('pos_name',  @$claims->pos_name, ['class'=>'form-control bg-white input-sm-header-billing', 'id' => 'pos_name' ,'readonly' => 'readonly','tabindex'=>'-1']) !!}
									@endif
                                    {!! Form::hidden('pos_code', @$claims->pos_code, ['id' => 'pos_code']) !!}
                                </div>    
                            </div>  
                            
                            <div class="form-group form-group-billing">
                                {!! Form::label('Copay', 'Co-Pay',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-4 select2-white-popup">
                                    {!! Form::select('copay',['' => '--','Check' => 'Check','Cash' => 'Cash','Credit' => 'CC'],null,['class'=>'form-control select2']) !!}
                                </div>
                                {!! Form::label('copay_amt', 'Amt',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label-billing med-green font600 p-l-0']) !!} 
                                <div class="col-lg-4 col-md-3 col-sm-3 col-xs-5">                                                                        
                                    {!! Form::text('copay_amt',(@$claims->copay_amt != 0? @$claims->copay_amt:''),['class'=>'form-control bg-white input-sm-header-billing', 'maxlength' => '10']) !!}                    
                                </div>    
                            </div>   

                            <div class="form-group form-group-billing">
                                {!! Form::label('Employer', 'Check No',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">
                                    {!! Form::text('employer_detail', @$claims->employer_details->employer_name,['class'=>'form-control bg-white input-sm-header-billing']) !!}
                                </div>                                
                            </div>
                             <div class="form-group form-group-billing">
                                {!! Form::label('mode', 'Date',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">
                                    <i class="fa fa-calendar-o form-icon-billing"></i> 
                                    {!! Form::text('copay_detail',@$claims->copay_detail,['class'=>'form-control bg-white input-sm-header-billing', 'autocomplete'=>'off']) !!}
                                </div>                          
                            </div>
                            <div class="form-group form-group-billing">
                                {!! Form::label('mode', 'Reference',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">
                                    {!! Form::text('copay_detail',@$charge_session_value->reference,['class'=>'form-control bg-white input-sm-header-billing','maxlength'=>'20']) !!}
                                </div>                          
                            </div>                                                                                               
                            </div><!-- /.box-body -->
                        </div><!--  2nd Content Ends -->
                    </div><!--  Box Ends -->
                </div><!-- Only general details Content Ends -->

                <div class="col-lg-2 col-md-2 col-sm-5 col-xs-12 "><!-- ICD Details Starts here -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                    <span class="font600 bg-white padding-0-4">Diagnosis - ICD 10</span>
                </div>
                <div class="box-body form-horizontal margin-t-5">
                    <div class="form-group form-group-billing">                            
                        {!! Form::label('icd1', '1',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd1',@$icd[1],['class'=>'form-control bg-white input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                            <span id="icd1" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[1])}}@endif</span>
                        </div>                                                     
                    </div>

                    <div class="form-group form-group-billing">                            
                        {!! Form::label('icd2', '2',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd2',@$icd[2],['class'=>'form-control bg-white input-sm-header-billing js-icd','data-val'=>"2"]) !!}
                            <span id="icd2" class="icd-hover">@if(!empty($icd[2])){{App\Models\Icd::getIcdDescription($icd[2])}}@endif</span>
                        </div>
                    </div>

                    <div class="form-group form-group-billing">                            
                        {!! Form::label('icd3', '3',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd3',@$icd[3],['class'=>'form-control bg-white input-sm-header-billing js-icd','data-val'=>"3"]) !!}
                            <span id="icd3" class="icd-hover">@if(!empty($icd[3])){{App\Models\Icd::getIcdDescription($icd[3])}}@endif</span>
                        </div>                                                                                         
                    </div>

                    <div class="form-group form-group-billing">                            
                        {!! Form::label('icd4', '4',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd4',@$icd[4],['class'=>'form-control bg-white input-sm-header-billing  js-icd','data-val'=>"4"]) !!}
                            <span id="icd4" class="icd-hover">@if(!empty($icd[4])){{App\Models\Icd::getIcdDescription($icd[4])}}@endif</span>
                        </div>                                            
                    </div>

                    <div class="form-group form-group-billing">                            
                        {!! Form::label('icd5', '5',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd5',@$icd[5],['class'=>'form-control bg-white input-sm-header-billing js-icd','data-val'=>"5"]) !!}
                            <span id="icd5" class="icd-hover">@if(!empty($icd[5])){{App\Models\Icd::getIcdDescription($icd[5])}}@endif</span>
                        </div>                                                                             
                    </div>

                    <div class="form-group form-group-billing margin-b-5">                            
                        {!! Form::label('icd6', '6',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd6',@$icd[6],['class'=>'form-control bg-white input-sm-header-billing js-icd','data-val'=>"6"]) !!}
                            <span id="icd6" class="icd-hover">@if(!empty($icd[6])){{App\Models\Icd::getIcdDescription($icd[6])}}@endif</span>
                        </div>                                          
                    </div>
                    <div class="js-display-err"></div>
                </div>
            </div>
                
            <div class="col-lg-2 col-md-2 col-sm-5 col-xs-12"><!-- ICD Details Starts here -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                    <span class="font600">&emsp;</span>
                </div>
                <div class="box-body form-horizontal margin-t-5">
                    <div class="form-group form-group-billing">                                                    
                        {!! Form::label('icd7', '7',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd7',@$icd[7],['class'=>'form-control bg-white input-sm-header-billing js-icd', 'data-val'=>"7"]) !!}                    
                            <span id="icd7" class="icd-hover">@if(!empty($icd[7])){{App\Models\Icd::getIcdDescription($icd[7])}}@endif</span>
                        </div>                       
                    </div>

                    <div class="form-group form-group-billing">                                                   
                        {!! Form::label('icd8', '8',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd8',@$icd[8],['class'=>'form-control bg-white input-sm-header-billing js-icd','data-val'=>"8"]) !!}
                            <span id="icd8" class="icd-hover">@if(!empty($icd[8])){{App\Models\Icd::getIcdDescription($icd[8])}}@endif</span>
                        </div>                             
                    </div>

                    <div class="form-group form-group-billing">                                                   
                        {!! Form::label('icd9', '9',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd9',@$icd[9],['class'=>'form-control bg-white input-sm-header-billing js-icd','data-val'=>"9"]) !!}
                            <span id="icd9" class="icd-hover">@if(!empty($icd[9])){{App\Models\Icd::getIcdDescription($icd[9])}}@endif</span>
                        </div>                                  
                    </div>

                    <div class="form-group form-group-billing">                                                                         
                        {!! Form::label('icd10', '10',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd10',@$icd[10],['class'=>'form-control bg-white input-sm-header-billing js-icd','data-val'=>"10"]) !!}
                            <span id="icd10" class="icd-hover">@if(!empty($icd[10])){{App\Models\Icd::getIcdDescription($icd[10])}}@endif</span>
                        </div>                              
                    </div>

                    <div class="form-group form-group-billing">                                                   
                        {!! Form::label('icd11', '11',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd11',@$icd[11],['class'=>'form-control bg-white input-sm-header-billing js-icd','data-val'=>"11"]) !!}
                            <span id="icd11" class="icd-hover">@if(!empty($icd[11])){{App\Models\Icd::getIcdDescription($icd[11])}}@endif</span>
                        </div>                                           
                    </div>

                    <div class="form-group form-group-billing margin-b-5">                                                   
                        {!! Form::label('icd12', '12',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            {!! Form::text('icd12',@$icd[12],['class'=>'form-control bg-white input-sm-header-billing js-icd','data-val'=>"12"]) !!}
                            <span id="icd12" class="icd-hover">@if(!empty($icd[12])){{App\Models\Icd::getIcdDescription($icd[12])}}@endif</span>
                        </div>
                    </div>    

                </div>
            </div>    

            </div><!-- General Details Full width Ends -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
				<div class="col-lg-6 col-md-7 col-sm-12 col-xs-12 pull-right no-padding">
					<div class="form-group margin-t-8">                            
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding p-r-0">
							<div class="col-lg-2 col-md-3 col-sm-2 col-xs-6 med-green font600 no-padding p-l-0">Anesthesia</div>
							<div class="col-lg-3 col-md-2 col-sm-3 col-xs-5 bootstrap-timepicker">
								<i class="fa fa-clock-o form-icon-billing"></i> 
								{!! Form::text('anesthesia_start',@$claims->anesthesia_start,['class'=>'form-control input-sm-modal-billing timepicker1 dm-time','placeholder'=>'Start Time', 'id' => 'anesthesia_start']) !!}
							</div>
							<div class="col-lg-3 col-md-2 col-sm-2 col-xs-10 bootstrap-timepicker">
								<i class="fa fa-clock-o form-icon-billing"></i> 
								{!! Form::text('anesthesia_stop',@$claims->anesthesia_stop,['class'=>'form-control input-sm-modal-billing timepicker1 dm-time','placeholder'=>'Stop Time','id' => 'anesthesia_stop']) !!}                    
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 ">
								{!! Form::text('anesthesia_minute',@$claims->anesthesia_minute,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Min', 'readonly' => 'readonly']) !!}
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 ">
								{!! Form::text('anesthesia_unit',@$claims->anesthesia_unit,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Units', 'readonly' => 'readonly']) !!}
							</div>
						</div>
					</div>
				</div>
			</div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8">                            
                <ul class="billing" style="list-style-type:none; padding:0px; line-height:26px; border: 1px solid #85e2e6; border-radius:4px;" id="">
                    <li class="billing-grid">
                        <table class="table-billing-view" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="text-align: center; width: 3%;">&emsp;</th>
                                    <th style="text-align: center; width: 6%;">From</th>                                                
                                    <th style="text-align: center;width: 6%">To</th>                                
                                    <th style="text-align: center; width: 8%">CPT</th>
                                    <th style="text-align: center; width: 4%">M 1</th>
                                    <th style="text-align: center; width: 4%">M 2</th>
                                    <th style="text-align: center; width: 4%">M 3</th>    
                                    <th style="text-align: center; width: 4%">M 4</th>  
                                    <th style="text-align: center; width: 18%">ICD Pointers</th>
                                    <th style="text-align: center; width: 5%">Units</th>
                                    <th style="text-align: center; width: 6%">Charges</th>
                                </tr>
                            </thead>
                        </table>                                     
                    </li>
                    @for($i=0;$i<=6;$i++)
                    <li class="billing-grid">
                        <table class="table-billing-view superbill-claim">
                            <tbody>
                                <tr>
                                    <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                    <td style="text-align: center; width: 6%;">
										<input type="text" @if($i < 1 )value='{{ @$charge_session_value->dos_from }}' @endif class="bg-white billing-noborder">
									</td>
                                    <td style="text-align: center; width: 6%;">
										<input type="text" @if($i < 1 )value='{{ @$charge_session_value->dos_to }}' @endif class="bg-white billing-noborder">
									</td> 
                                    <td style="text-align: center; width: 8%;"> <input type="text" class="bg-white billing-noborder"></td>  
                                    <td style="text-align: center; width: 4%" class="bg-white billing-noborder">{!! Form::text('preferred_communication',null,['class'=>'form-control bg-white billing-noborder textboxrow']) !!}</td>
                                    <td style="text-align: center; width: 4%" class="bg-white billing-noborder">{!! Form::text('preferred_communication',null,['class'=>'form-control bg-white billing-noborder textboxrow']) !!}</td>
                                    <td style="text-align: center; width: 4%" class="bg-white billing-noborder">{!! Form::text('preferred_communication',null,['class'=>'form-control bg-white billing-noborder textboxrow']) !!}</td>
                                    <td style="text-align: center; width: 4%" class="bg-white billing-noborder">{!! Form::text('preferred_communication',null,['class'=>'form-control bg-white billing-noborder textboxrow']) !!}</td>
                                    <td style="text-align: center; width: 18%" class="bg-white billing-noborder">{!! Form::text('pointers',null,['class'=>'bg-white billing-noborder']) !!}</td>
                                    <td style="text-align: center; width: 5%"><input type="text" class="bg-white billing-noborder"></td>
                                    <td style="text-align: center; width: 6%"><input type="text" class="bg-white billing-noborder"></td>                              
                                </tr>
                            </tbody>
                        </table>                                     
                    </li>
                    @endfor
                    
                </ul>
                <div class="pull-right" style="margin-top: -5px; margin-bottom: 5px;"> 
					<span class=" med-green font600" >Total Charges : </span>
					<span class="med-orange font600 margin-l-20">0.00 </span>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding charge-notes-bg">

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">
                    {!! Form::text('doi',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Notes']) !!}
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">&emsp;</div>
                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-5">
                    <input type="checkbox" class="flat-red"> Hold
                </div>

                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 billing-select2 no-padding">
                    {!! Form::select('employer_id',array(''=>'--','Cash'=>'Cash','Check'=>'Check','Credit Card'=>'Credit','Others'=>'Others'),null,['class'=>'form-control select2 input-sm-modal-billing']) !!}
                </div>
            </div>

            <div class="payment-links">
                <ul class="nav nav-pills pull-left margin-t-10">
                    <li><a href="#js-model-popup" data-toggle="modal" data-target="#js-model-popup" class="claimdetail font600"> <i class="fa fa-file-text-o"></i> Claim Details</a></li>                   
                </ul>             
            </div>

            <div class="box-footer space20" id = "js-charge-cancel">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    {!! Form::submit('Save', ['class'=>'btn btn-medcubics']) !!}
                   <a href="javascript:void(0)" data-url="{{ url('charges')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                 
                </div>
            </div><!-- /.box-footer -->

        </div><!--Background color for Inner Content Ends -->
    </div>
   
    
    </div><!-- /.modal-dialog -->

    <!--End-->
    </div>

    <div id="Patient_list" class="modal fade in">
        <div class="modal-md-650">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-url = "" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Patient Lists</h4>
                </div>
                <div class="modal-body">
                 
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
	</div><!-- Modal Light Box Ends -->  
	
	<div class="hide" id="showmenu-bar">
		<span id="showmenu" class="cur-pointer alertnotes-icon"><i class="fa fa-bell med-orange"></i></span>
		<div class="snackbar-alert success menu">
			<h5 class="med-orange margin-b-5 margin-l-15 margin-t-6"><span>Alert Notes</span> <span class="pull-right cur-pointer" ><i class="fa fa-times" id="showmenu1"></i></span></h5>            
			<p id="alert-notes-msg"></p>
		</div>
	</div>
      
@include ('patients/problemlist/commonproblemlist') 
@stop

@push('view.script')
<script type="text/javascript">
$('input[type="text"]').attr('autocomplete','off');
<?php if(isset($get_default_timezone)){?>
    var get_default_timezone = '<?php echo $get_default_timezone; ?>';
<?php }?>

</script>
@endpush