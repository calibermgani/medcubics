@extends('admin')
@section('toolbar')
<div class="row toolbar-header " >
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="users-add"></i>Patients </small>
        </h1>
        <ol class="breadcrumb">
            <li><a accesskey="b" href="#" onclick="history.go(-1); return false;"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href=""><i class="fa {{Config::get('cssconfigs.common.edit')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')

<div class="col-md-12">
    <ul class="nav nav-tabs">
        <div class="table-container ">
            <div class="col-table-cell col-md-2 col-sm-3 col-xs-4"><li><a href="#personal-info" data-toggle="tab"><i class="livicon tabs" data-name="home" data-size="20"></i>Personal Details</a></li></div>
            <div class="col-table-cell col-md-2 col-sm-3 col-xs-4"><li><a href="#insurance-info" data-toggle="tab"><i class="livicon" data-name="user" data-size="20" ></i> Insurance</a></li></div>
            <div class="col-table-cell col-md-2 col-sm-3 col-xs-4"><li><a href="#contact-info" data-toggle="tab"><i class="livicon" data-name="user" data-size="20"></i> Contacts</a></li></div>
            <div class="col-table-cell col-md-2 col-sm-3 col-xs-4"><li><a href="#authorization" data-toggle="tab"><i class="livicon" data-name="user" data-size="20"></i><span> Auth No </span></a></li></div>
            <div class="col-table-cell col-md-2 col-sm-3 col-xs-4"><li><a href="#" data-toggle="tab"><i class="livicon" data-name="folders" data-size="20"></i> Documents</a></li></div>
            <div class="col-table-cell col-md-2 col-sm-3 col-xs-4"><li><a href="#" data-toggle="tab"><i class="livicon" data-name="pin-on" data-size="20"></i> Notes</a></li></div>
        </div>    
    </ul>
    <div class="nav-tabs-custom">    
        <div class="tab-content">               
            @if(Request::segment(2) == 'create')
               <div class="tab-pane active" id="personal-info">
                    <!-- Form 1 -->
                    {!! Form::open(['url'=>'patients', 'id' => 'patients-form-create']) !!}
                    {!! Form::hidden('patient_id',null,['class'=>'form-control','id'=>'patient_id']) !!}
						@include ('patients/patients/personal-info',['submitBtn'=>'Save']) 
                    {!! Form::close() !!}              
                </div><!-- /.tab-pane -->             
            @else
               <div class="tab-pane active" id="personal-info">
                    <!-- Form 1 -->
                    {!! Form::model($patients, array('url'=>'patients','id' => 'patients-form')) !!}
                    {!! Form::hidden('patient_id',$patients->id,['class'=>'form-control','id'=>'patient_id']) !!}
						@include ('patients/patients/personal-info',['submitBtn'=>'Save']) 
                    {!! Form::close() !!}              
                </div><!-- /.tab-pane -->
                  
                <div class="tab-pane active" id="insurance-info"><!-- Insurance -->
                    <div class="col-md-12">&emsp;</div> 
                    {!! Form::model($patients, array('id' => 'insurance-info-form', 'name' => 'insurance-info', 'class' => 'insurance')) !!}
                    	@include ('patients/patients/insurance-info',['submitBtn'=>'Save'])              
                    {!! Form::close() !!}
                </div><!-- /.tab-pane -->

                <!--Contact -->
                <div class="tab-pane" id="contact-info">
                    <div class="col-md-12">&emsp;</div> 
                    {!! Form::model($patients, ['id' => 'contact-info-form', 'class' => 'contact']) !!}
                        @include ('patients/patients/contact-info',['submitBtn'=>'Save']) 
                    {!! Form::close() !!}              
                </div><!-- /.box-body -->
                
                <!--\Contact -->

                <!--- Authorization -->
                <div class="tab-pane" id="authorization"><!-- Insurance -->
                <div class="col-md-12">&emsp;</div> 
                {!! Form::model($patients, array('id' => 'authorization')) !!}
					@include ('patients/patients/authorization-info',['submitBtn'=>'Save']) 
                {!! Form::close() !!}                        
                
                </div><!-- /.tab-pane -->
            @endif
            <div class="tab-pane" id="settings"></div><!-- /.tab-pane -->
        </div><!-- /.tab-content -->
    </div><!-- /.nav-tabs-custom -->
</div><!-- /.col -->
 
<!-- Modal Light Box starts -->
<div id="form-address-modal" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">USPS Address Information</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-list" id="modal_show_success_message" @if($address_flag['general']['is_address_match'] != 'Yes') class="hide" @endif>
                    <li class="nav-header">Address : <span id="modal_address">{{$address_flag['general']['address1']}}</span></li>
                    <li class="nav-header">City : <span id="modal_city">{{$address_flag['general']['city']}}</span></li>
                    <li class="nav-header">State : <span id="modal_state">{{$address_flag['general']['state']}}</span></li>
                    <li class="nav-header">Zip Code : <span id="modal_zip5">{{$address_flag['general']['zip5']}}-{{$address_flag['general']['zip4']}}</span></li>
                </ul>
                <p id="modal_show_error_message" @if($address_flag['general']['is_address_match'] != 'No') class="hide" @endif>{{$address_flag['general']['error_message']}}</p>							
            </div>
		</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->            
@stop