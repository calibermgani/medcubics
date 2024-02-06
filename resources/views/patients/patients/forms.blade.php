<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-13">
    <div class="med-tab">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="js-tab-heading ajax-loading-demo @if($tab == 'demo') active @endif" id="js-tab-heading-demo"><a accesskey="g" id="" @if(Request::segment(2) != 'create') href="{{  url('patients/'.@$id.'/edit') }}"class="js_arrow" @endif><i class="fa fa-info-circle i-font-tabs"></i> Demo<span class="text-underline">g</span>raphic</a></li>
				
                @if($checkpermission->check_url_permission('patients/{id}/edit/{tab?}/{more?}') == 1)
                    <li class="js-tab-heading @if($tab == 'insurance') active @endif" id="js-tab-heading-insurance">
                        <a href="javascript:void(0);" tabindex="-1" accesskey="i" id="insurance" @if(Request::segment(2) == 'create') class="inactivelink" @else  class="js_arrow" @endif><i class="fa fa-institution i-font-tabs"></i> <span class="text-underline">I</span>nsurance</a>
                    </li>
                @endif
				
                @if(@$selectbox != '')
                    <li class="js-tab-heading @if($tab == 'contact') active @endif" id="js-tab-heading-contact">
                        <a href="javascript:void(0);" tabindex="-1" accesskey="c" id="contact" @if(Request::segment(2) == 'create') class="inactivelink" @else  class="js_arrow" @endif><i class="fa fa-book i-font-tabs"></i> <span class="text-underline">C</span>ontacts</a>
                    </li>
                @endif

                <li class="js-tab-heading @if($tab == 'authorization') active @endif" id="js-tab-heading-authorization">
                    <a href="javascript:void(0);" tabindex="-1" accesskey="z" id="authorization" @if(Request::segment(2) == 'create') class="inactivelink" @else  class="js_arrow" @endif><i class="fa fa-shield i-font-tabs"></i> Authori<span class="text-underline">z</span>ation</a>
                </li>

                @if($checkpermission->check_url_permission('patients/{id}/archiveinsurance') == 1)
                <li><a accesskey="e" @if(Request::segment(2) == 'create') class="inactivelink" href="javascript:void(0);" tabindex="-1" @else href="{{ url('patients/'.@$id.'/archiveinsurance') }}" tabindex="-1" @endif><i class="fa fa-institution i-font-tabs"></i> Insurance Archive</a></li>
                @endif
            </ul>

            <div class="tab-content patient-tab-bg">                 
                @if(Request::segment(2) == 'create')
                    <div class="tab-pane active" id="demo-info">
                        <!-- Form 1 -->
                        {!! Form::open(['url'=>'patients/store', 'id' => 'js-bootstrap-validator','class' => 'patients-info-form medcubicsform','name'=>'patients-form', 'files' => true]) !!}
                            {!! Form::hidden('next_tab',null,['class'=>'form-control','id'=>'next_tab']) !!}
                            @include ('patients/patients/personal-info',['submitBtn'=>'Save']) 
                        {!! Form::close() !!}              
                    </div><!-- /.tab-pane --> 
                @else
                    {!! Form::hidden('encode_patient_id',$id,['class'=>'form-control','id'=>'encode_patient_id']) !!}
                    <div class="tab-pane test-demo @if($tab == 'demo') active @endif" id="demo-info">
                        <!-- Form 1 -->                                                            
                        {!! Form::model($patients, array('url'=>'patients/store/'.$id,'id' => 'js-bootstrap-validator','class' => 'patients-info-form medcubicsform','name'=>'patients-form','files' => true)) !!}
                            {!! Form::hidden('next_tab',null,['class'=>'form-control','id'=>'next_tab']) !!}
                            @include ('patients/patients/personal-info',['submitBtn'=>'Save']) 
                        {!! Form::close() !!}            
                    </div><!-- /.tab-pane -->

                    <div class="tab-pane @if($tab == 'insurance') active @endif" id="insurance-info">
                        @if($tab == 'insurance')
                            @include ('patients/patients/insurance-info',['submitBtn'=>'Save'])
                        @endif
                    </div>

                    <!--Contact -->
                    @if(@$selectbox != '')
                        <div class="tab-pane @if($tab == 'contact') active @endif" id="contact-info">
                            @if($tab == 'contact')
                                @include ('patients/patients/contact-info',['submitBtn'=>'Save'])
                            @endif
                        </div>
                    @endif
                    <!-- /.box-body -->

                    <!--- Authorization -->
                    <div class="tab-pane @if($tab == 'authorization') active @endif" id="authorization-info">
                        @if($tab == 'authorization')
                            @include ('patients/patients/authorization-info',['submitBtn'=>'Save']) 
                        @endif
                    </div>
                @endif
            </div><!-- /.tab-content -->
        </div><!-- /.nav-tabs-custom -->
    </div>
</div><!-- /.col -->

<!-- Modal Light Box Address starts -->  
<div id="form-address-modal" class="modal fade in">
    @include ('practice/layouts/usps_form_modal')
</div><!-- Modal Light Box Ends -->

{!! Form::hidden('current_delete_type',null,['id'=>'current_delete_type']) !!}
{!! Form::hidden('current_div_id',null,['id'=>'current_div_id']) !!}
{!! Form::hidden('current_delete_typeid',null,['id'=>'current_delete_typeid']) !!}

<div id="delete-form-modal" class="modal fade in">
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h4 class="modal-title">Alert</h4></div>
            <div class="modal-body text-center med-green font600"></div>
            <div class="modal-footer">
                <button class="confirm btn btn-medcubics-small js-patient-delete-yes" type="button" data-dismiss="modal">Yes</button>
                <button class="cancel btn btn-medcubics-small" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@push('view.scripts1')
{!! HTML::script('js/address_check.js') !!}
<script type="text/javascript">
	function closeTheCalendar(){
		var status = $('#ui-datepicker-div').css("display");
		if( status= "block"){
			 $('#ui-datepicker-div').hide();
		}
		var timePickerStatus = $('.bootstrap-timepicker-widget').css("display");
		if(timePickerStatus ="block"){
			$('.bootstrap-timepicker-widget').removeClass('open');
		}
	}
	
	$(document).mapKey('Alt+r', function (e) {
		if (!$("body").hasClass("modal-open")) {
			$("#select2-drop-mask").not('#js-insurance').click();
			$('#js-insurance').select2('open');
			closeTheCalendar();
			return false;
		}
	});
	
    $('#js-bootstrap-validator').find('input:visible').each(function () {
        $(this).attr("autocomplete", "nope");
    });
    // $(".js-address-check").trigger("blur");
</script>
@endpush
