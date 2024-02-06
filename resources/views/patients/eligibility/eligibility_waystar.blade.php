@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Eligibility </span></small>
        </h1>
        <?php 	$uniquepatientid = @$patient_id;
				$patientid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id,'decode');
				?>	
        <ol class="breadcrumb">
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($patient_id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>	

            @include ('patients/layouts/swith_patien_icon')  

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/eligibility')}}"  class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patientid,'needdecode'=>'no'])
@include ('patients/eligibility/tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 "><!-- Inner Content for full width Starts -->
    <div class="box box-info no-shadow bg-transparent">
         @if(Session::get('message')!== null) 
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">           
            <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>            
        </div>
         @endif
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h4 class="med-green"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> List</h4>
        </div>
        <div class="box-body">
            <div class="accordion">
                 <table id="benefit_verification" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Created On</th>
                            <th>Category</th>
                            <th>DOS</th>                                        
                            <th>Provider</th>   
                            <th>Facility</th>
                            <th>Insurance</th>  
                            <th>Policy ID</th>  
                            <th>Template</th>   
                            <th>User</th>                                           
                            <th>Document</th>
                        </tr>
                    </thead>
				
                    @foreach($eligibility as $benefit)
                    <?php                                                                         
                        $benefit_encid  = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$benefit->id ,'encode'); 
                        $category = "EDI";					
                        $provider_id = "";
                        $facility = "";
                       
                    ?>    
                    <tr style="cursor:default;">
                        <td>{{ App\Http\Helpers\Helpers::timezone(@$benefit->created_at,'m/d/y') }}</td>   
                        <td>{{$category}}</td>
                        <td>
                            @if(@$benefit->created_at !="0000-00-00") {{ App\Http\Helpers\Helpers::dateFormat(@$benefit->created_at,'dob') }} @endif
                        </td>        
                             
                        <td>
                            @if(!empty($provider_id))
                            <a id="someelem{{hash('sha256',@$provider_id)}}" class="someelem" data-id="{{hash('sha256',@provider_id)}}" href="javascript:void(0);"> {{ @$provider->short_name }}</a> 
                            @include ('layouts/provider_hover')
                            @else
								RPH
                            @endif
                        </td>
                        <td>
                            <span class="js-display-detail"> </span>
                            @if(!empty($facility_id)) 
                            @include('layouts.facilitypop', array('data' => @$facility, 'from' =>'facility'))
                            <a id="someelem{{hash('sha256',@$facility_id)}}" class="someelem" data-id="{{hash('sha256',@$facility_id)}}" href="javascript:void(0);"> {{@$facility->short_name }}</a>
                            @include ('layouts/facility_hover')
                            @else
                            -
                            @endif
                        </td>
                        <td>{{str_limit(@$benefit->insurance_details->insurance_name,25,'...') }}</td>
                        <td>
							<?php echo (isset($benefit->insurance_details->eligibility_payerid) && !empty($benefit->insurance_details->eligibility_payerid)) ? $benefit->insurance_details->eligibility_payerid : 'Nil'; ?>
                        </td>
                        <td>-</td> 
                        <td>{{ App\Http\Helpers\Helpers::shortname($benefit->created_by) }}</td>
                        <td class="td-c-5 text-center js-prevent-show">
							<a title="Eligibility Details" data-id="{{ @$benefit->id }}" class="js_get_eligiblity_details_waystar_history js-claimlink"  data-patientid="{{ @$benefit->patient_id }}" data-toggle="modal" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.common.user')}} text-green font10"></i></a>
                        </td>  
                    </tr>
                    @endforeach 
					
                </table>  
            </div>           
        </div><!-- /.box-body --> 
    </div><!-- Box Ends -->
    <!-- SIVA -->
</div>
    <div id="add_new_edi_verification" class="modal fade in" data-keyboard="false"></div>
    <div class="js-edi_verification hide">
        <div class="modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Eligibility Verification</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['onsubmit'=>"event.preventDefault();" ,'name'=>'edi_form','id'=>'js-bootstrap-validator','class'=>'']) !!}
                    <div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
                        <div class="box-body form-horizontal no-padding">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-class" id="js-address-general-address">
                                {!! Form::token() !!}
                                <div class="form-group margin-b-10">
                                    {!! Form::label('Insurance name', 'Insurance name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('insurance_name')) error @endif">
                                        {!! Form::select('insid', array(''=>'-- Select --')+(array)@$patient_insurances,null,['class'=>'select_2 form-control edi_insurance_id']) !!}                                       
                                        {!!Form::hidden('policyid')!!}
                                        {!!Form::hidden('insurance_id')!!}
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->   
                    </div><!-- /.box Ends Contact Details-->
                    <div id="insurance-info-footer" class="modal-footer">
                        <input  class="btn btn-medcubics-small js-patient-eligibility_check" type="submit" value="Save" data-page="eligibility" patientid ="{{$patient_id}}">
                        <button class="btn btn-medcubics-small" data-dismiss="modal" type="button">Cancel</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>	
    <!-- SIVA -->
	<div id="eligibility_content_popup" class="modal fade in">
    @include ('layouts/eligibility_modal_popup')
</div>
@stop