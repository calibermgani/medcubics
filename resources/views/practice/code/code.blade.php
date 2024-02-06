@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Remittance Codes </span></small>
        </h1>
        <ol class="breadcrumb">
			
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			
		<!--	@if(count($codes)>0)
				li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/codereports/'])
				</li
			@endif	-->
			
            <li><a href="javascript:void(0);l" data-url="{{url('help/codes')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
    
@stop
    
@section('practice')
	<div class="col-lg-12">
		@if(Session::get('message')!== null) 
		<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
		@endif
	</div> 
	
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
        <div class="box box-info no-shadow">
            <div class="box-header margin-b-10">
                <i class="fa fa-bars font14"></i><h3 class="box-title"> Remittance Codes List</h3>
                <div class="box-tools pull-right margin-t-2">
					@if(count($codes)>0)
						@if($checkpermission->check_url_permission('code/create'))
							<a href="{{ url('code/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Remittance Code</a>
						@endif	
					@else
						<a href="" class="selFnCode med-red font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> Import Remittance Code</a>
					@endif		
                </div>
            </div><!-- /.box-header -->
			<div class="box-body table-responsive" id="js_table_search_listing">
				@include('practice/code/codes-list')
			</div>
        </div><!-- /.box -->
    </div>  
<!--End-->



<div id="ruleEngine" class="modal fade">
	<div class="modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Set Rule Engine</h4>
			</div>
			<div class="modal-body">
				<span id="document_add_form_part">
					{!! Form::open(['url' => '','name'=>'rule_engine','onsubmit'=>"event.preventDefault();",'id'=>'rule_engine','files'=>true,'class'=>'popupmedcubicsform']) !!}
					<!-- Modal Body -->
					<input type="hidden" name="code_id" class="js_code_id" value="" />
					<div class="modal-body form-horizontal">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="form-group">
									{!! Form::label('reason-code', 'Reason Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label star']) !!}  
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
										{!! Form::label('reason_code',null, [ 'id'=>'code', 'class'=>'form-control']) !!}
									</div>
									<!-- <div class="col-sm-1"></div> -->
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="form-group">
									{!! Form::label('reason_type', 'Reason Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label star']) !!}  
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
										{!! Form::select('reason_type', array('' => '-- Select --','Billing' => 'BIlling','Coding' => 'Coding'),null,['class'=>'select2 form-control']) !!} 
									</div>
									<div class="col-sm-1"></div>
								</div>
							</div>
						</div>
						<div class="col-md-6" style="width: 48%; margin: 5px; border: 2px solid #cacaca; border-radius: 5px; ">
							<center><strong style="text-align:center; "> CO </strong></center>
							<div class="form-group">
								{!! Form::label('co_claim_status', 'Action', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('co_claim_status', array('Adjustment' => 'Adjustment','Denied' => 'Denied','Info' => 'Info','Patient' => 'Patient'),null,['class'=>'select2 form-control js-action-change','data-type'=>'co']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
							<div class="form-group">
								{!! Form::label('co_next_resp', 'Responsibility', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('co_next_resp', array('Next' => 'Next','Same' => 'Same','Patient' => 'Patient'),null,['class'=>'select2 form-control']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
							<div class="form-group">
								{!! Form::label('co_priority', 'Priority', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('co_priority', array('Low' => 'Low','Medium' => 'Medium','High' => 'High'),null,['class'=>'select2 form-control']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
						</div>
						<div class="col-md-6" style="width: 48%; margin: 5px; border: 2px solid #cacaca; border-radius: 5px; ">
							<center><strong style="text-align:center; "> PR</strong></center>
							<div class="form-group">
								{!! Form::label('pr_claim_status', 'Action', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('pr_claim_status', array('Adjustment' => 'Adjustment','Denied' => 'Denied','Info' => 'Info','Patient' => 'Patient'),null,['class'=>'select2 form-control js-action-change','data-type'=>'pr']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
							<div class="form-group">
								{!! Form::label('pr_next_resp', 'Responsibility', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('pr_next_resp', array('Next' => 'Next','Same' => 'Same','Patient' => 'Patient'),null,['class'=>'select2 form-control']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
							<div class="form-group">
								{!! Form::label('pr_priority', 'Priority', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('pr_priority', array('Low' => 'Low','Medium' => 'Medium','High' => 'High'),null,['class'=>'select2 form-control']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
						</div>
						<div class="col-md-6" style="width: 48%; margin: 5px; border: 2px solid #cacaca; border-radius: 5px; ">
							<center><strong style="text-align:center; "> OA</strong></center>
							<div class="form-group">
								{!! Form::label('oa_claim_status', 'Action', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('oa_claim_status', array('Adjustment' => 'Adjustment','Denied' => 'Denied','Info' => 'Info','Patient' => 'Patient'),null,['class'=>'select2 form-control js-action-change','data-type'=>'oa']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
							<div class="form-group">
								{!! Form::label('oa_next_resp', 'Responsibility', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('oa_next_resp', array('Next' => 'Next','Same' => 'Same','Patient' => 'Patient'),null,['class'=>'select2 form-control']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
							<div class="form-group">
								{!! Form::label('oa_priority', 'Priority', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('oa_priority', array('Low' => 'Low','Medium' => 'Medium','High' => 'High'),null,['class'=>'select2 form-control']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
						</div>
						<div class="col-md-6" style="width: 48%; margin: 5px; border: 2px solid #cacaca; border-radius: 5px; ">
							<center><strong style="text-align:center; "> PI</strong></center>
							<div class="form-group">
								{!! Form::label('pi_claim_status', 'Action', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('pi_claim_status', array('Adjustment' => 'Adjustment','Denied' => 'Denied','Info' => 'Info','Patient' => 'Patient'),null,['class'=>'select2 form-control js-action-change','data-type'=>'pi']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
							<div class="form-group">
								{!! Form::label('pi_next_resp', 'Responsibility', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('pi_next_resp', array('Next' => 'Next','Same' => 'Same','Patient' => 'Patient'),null,['class'=>'select2 form-control']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
							<div class="form-group">
								{!! Form::label('pi_priority', 'Priority', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									{!! Form::select('pi_priority', array('Low' => 'Low','Medium' => 'Medium','High' => 'High'),null,['class'=>'select2 form-control']) !!} 
								</div>
								<div class="col-sm-1"></div>
							</div>
						</div>
					<div id="footer_part" class="modal-footer">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							{!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js-submit-btn js-ruleEngine']) !!}
							{!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small close_popup']) !!}
						</div>
					</div>
				</div>
					{!! Form::close() !!}
				</span>
			</div>
		
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->	


@stop
   
@push('view.scripts')
<script type="text/javascript">
	var api_site_url = '{{url("/")}}';
    $(document).ready(function () {
		$(document).on('click', '.selFnCode', function (e) {
            
			$.ajax({
				type: 'GET',
				url: api_site_url + '/getmastercode',
				success: function (result) {
					js_alert_popup(result.message);
					 window.location = api_site_url + '/code'; 
				}
			});
			e.preventDefault();
		});	
	});
</script>	

<script>

$(document).on('click', '.js-prevent-redirect', function (e) { 
    e.stopImmediatePropagation();
});

$(document).on('click','.js_code_id',function(){
		$('input[name="code_id"]').val($(this).attr('data-code-id'));
		$('#code').text($(this).attr('data-reason-code'));
		$('select[name="reason_type"]').val($(this).attr('data-reason-type')).select2().trigger('change');
		if($(this).attr('data-co-claim-status') != ''){
			$('select[name="co_claim_status"]').val($(this).attr('data-co-claim-status')).select2().trigger('change');
			$('select[name="co_next_resp"]').val($(this).attr('data-co-next-resp')).select2().trigger('change');
			$('select[name="co_priority"]').val($(this).attr('data-co-priority')).select2().trigger('change');

			$('select[name="pr_claim_status"]').val($(this).attr('data-pr-claim-status')).select2().trigger('change');
			$('select[name="pr_next_resp"]').val($(this).attr('data-pr-next-resp')).select2().trigger('change');
			$('select[name="pr_priority"]').val($(this).attr('data-pr-priority')).select2().trigger('change');

			$('select[name="oa_claim_status"]').val($(this).attr('data-oa-claim-status')).select2().trigger('change');
			$('select[name="oa_next_resp"]').val($(this).attr('data-oa-next-resp')).select2().trigger('change');
			$('select[name="oa_priority"]').val($(this).attr('data-oa-priority')).select2().trigger('change');

			$('select[name="pi_claim_status"]').val($(this).attr('data-pi-claim-status')).select2().trigger('change');
			$('select[name="pi_next_resp"]').val($(this).attr('data-pi-next-resp')).select2().trigger('change');
			$('select[name="pi_priority"]').val($(this).attr('data-pi-priority')).select2().trigger('change');
		}else{
			$('select[name="co_claim_status"]').val('Adjustment').select2().trigger('change');
			$('select[name="co_next_resp"]').val('Next').select2().trigger('change');
			$('select[name="co_priority"]').val('Low').select2().trigger('change');

			$('select[name="pr_claim_status"]').val('Adjustment').select2().trigger('change');
			$('select[name="pr_next_resp"]').val('Next').select2().trigger('change');
			$('select[name="pr_priority"]').val('Low').select2().trigger('change');

			$('select[name="oa_claim_status"]').val('Adjustment').select2().trigger('change');
			$('select[name="oa_next_resp"]').val('Next').select2().trigger('change');
			$('select[name="oa_priority"]').val('Low').select2().trigger('change');

			$('select[name="pi_claim_status"]').val('Adjustment').select2().trigger('change');
			$('select[name="pi_next_resp"]').val('Next').select2().trigger('change');
			$('select[name="pi_priority"]').val('Low').select2().trigger('change');
		}
	});
 $('#rule_engine')                                              
    .bootstrapValidator({
        message: 'This value is not valid',
        excluded: ':disabled',
        feedbackIcons: {
            valid: '',
            invalid: '',
            validating: ''
        },
        fields: {
			reason_type: {
				message: '',
				validators: {
					notEmpty:{
                        message: 'Select reason type'
                    }
				}
			}, 
			claim_status:{
				message:'',
				validators:{
					notEmpty:{
                        message: 'Select claim status'
                    }
				}
			},
			next_resp:{
				message:'',
				validators:{
					notEmpty:{
                        message: 'Select next respobility'
                    }
				}
			},
			priority: {
				message: '',
				validators: {
					notEmpty:{
                        message: 'Select responsibility'
                    }
				}
			}
        }
    }).on('success.form.bv', function(e) { 
    	var co_claim_status = $('select[name="co_claim_status"]').val();
    	var co_next_resp = $('select[name="co_next_resp"]').val();
    	var co_priority = $('select[name="co_priority"]').val();
    	var co = {code_type:"CO", claim_status : co_claim_status, next_resp : co_next_resp,priority : co_priority};

    	var pr_claim_status = $('select[name="pr_claim_status"]').val();
    	var pr_next_resp = $('select[name="pr_next_resp"]').val();
    	var pr_priority = $('select[name="pr_priority"]').val();
    	var pr = {code_type:"PR", claim_status : pr_claim_status, next_resp : pr_next_resp,priority : pr_priority};

    	var oa_claim_status = $('select[name="oa_claim_status"]').val();
    	var oa_next_resp = $('select[name="oa_next_resp"]').val();
    	var oa_priority = $('select[name="oa_priority"]').val();
    	var oa = {code_type:"OA", claim_status : oa_claim_status, next_resp : oa_next_resp,priority : oa_priority};

    	var pi_claim_status = $('select[name="pi_claim_status"]').val();
    	var pi_next_resp = $('select[name="pi_next_resp"]').val();
    	var pi_priority = $('select[name="pi_priority"]').val();
    	var pi = {code_type:"PI", claim_status : pi_claim_status, next_resp : pi_next_resp,priority : pi_priority};

    	var code_type = [co,pr,oa,pi]

		$.ajax({
			type: 'POST',
			url: api_site_url + '/codes/setRuleEngine',
			data: {'_token':'<?php echo csrf_token(); ?>','reason_type':$('select[name="reason_type"]').val(),'code_type':code_type,'code_id':$('input[name="code_id"]').val()},
			success: function (data) {  
				$('#ruleEngine').modal('hide');
				js_sidebar_notification("success", "Rule engine added successfully");
				setTimeout(function(){ location.reload(); }, 100);
				
			}
			
		});

	});
$(document).on('change','.js-action-change',function(){
	console.log($(this).attr('data-type'));
	console.log($(this).val());
	var name = $(this).attr('data-type')+ "_next_resp";
	if($(this).val() == 'Patient'){
		$('select[name='+name+']').val('Patient').trigger('change');
		$('#'+name).select2("readonly", true);
	}else{
		$('#'+name).select2("readonly", false);
	}
});
</script>
@endpush