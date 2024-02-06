@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> Patients <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
        </h1>
        <ol class="breadcrumb">
			<li><a href="javascript:void(0)" data-url="{{ url('patients') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			@include ('patients/layouts/swith_patien_icon')
			
            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
    <div id="session_model1" class="modal fade">
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Warning </h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-list line-height-26">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center font600">Your session has been expired!. click Yes to continue </div>
                    </div>
                </ul>                   
                <div class="modal-footer">
                    <button [focus]='true'class="js_session_confirm btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal">Continue</button>
                    <button class="js_session_confirm btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal">Ignore</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</div>
@stop
@section('practice')
    @include ('patients/patients/forms',['submitBtn'=>'Save']) 
@stop