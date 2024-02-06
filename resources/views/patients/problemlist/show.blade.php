{!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-validators','name'=>'medcubicsform','class'=>'medcubicsform js_problem_show']) !!}   
<?php
	$priority = ['High' => 'High','Moderate' => 'Moderate','Low' => 'Low'];
	$status = ['Assigned' => 'Assigned','Inprocess' => 'Inprocess','Completed' => 'Completed'];
?>
<div class="modal-dialog js_show_problem">
    <div class="modal-content">
        <div class="modal-header">
			<button type="button" class="close js_problemlist_update" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title "> Claim No : {{@$claims_number}}</h4>
        </div>
		<!--div id="edit_success_alert_part" class="col-lg-12 hide" style="float: none;">
        <p class="alert alert-success" id="edit_success_msg">added successfully</p>
    </div-->
				
		<div class="modal-body no-padding" >
            <div class="box box-view no-shadow no-border"><!--  Box Starts -->
				<input type="hidden" class="js_claim" value="{{@$id}}"/>
				<input type="hidden" class="js_claim_no" value="{{@$claims_number}}"/>
                <input type="hidden" class="js_patient_id" value="{{$patient_id}}"/>
                <div class="box-body chat ar-notes form-horizontal js_problem_scroll">
				@include ('patients/problemlist/problemdetaillist')
				
				</div>
				@if(@$problemlist[0]->assign_user_id == Auth::user ()->id || @$problemlist[0]->created_by->id == Auth::user ()->id)
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                    <p class="padding-4 med-orange bg-aqua font600">Create New </p>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5" ><!-- Left side Content Starts -->                            
                    <div class="form-horizontal"><!-- Box Starts -->
                        <div class="form-group-billing">                                                     
                            <div class="col-lg-12 col-md-12 col-sm-8 col-xs-10"> 
                                {!! Form::textarea('description',null,['class'=>'form-control input-sm-modal-billing problem_desc','placeholder'=>'Description']) !!}
                            </div>
                        </div> 

                        <div class="form-group-billing">					         
                            <div class="col-lg-3 col-md-3 col-sm-5 col-xs-6 select2-white-popup">  
                                {!! Form::select('assign_user_id', [''=>'-- Assign To --']+(array)@$practice,null,['class'=>'form-control select2 input-sm-modal-billing js_users']) !!}
                            </div>                                    

                            <div class="col-lg-3 col-md-3 col-sm-2 col-xs-4"> 
                                <i class="fa fa-calendar-o form-icon-billing"></i> 
                                {!! Form::text('fllowup_date',null,['autocomplete'=>'off', 'id'=>'followup_date','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control input-sm-header-billing form-cursor dm-date followup_date datepicker']) !!}
								
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-2 col-xs-4 select2-white-popup"> 
                                {!! Form::select('priority', [''=>'-- Priority --']+$priority,null,['class'=>'select2 form-control js_priority']) !!}
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-2 col-xs-6 select2-white-popup">  
                                {!! Form::select('status', [''=>'-- Status --']+$status,null,['class'=>'select2 form-control js_status']) !!}
                            </div>
                        </div>   
                    </div>                                                        
                </div>
                <div id="js_edit_problem_list" class="modal-footer m-b-m-15">
                    {!! Form::submit("Submit", ['class'=>'btn btn-medcubics-small form-group js_problem_submit']) !!}
					<button class="btn btn-medcubics-small js_problemlist_update" data-dismiss="modal" type="button">Cancel</button>
                </div>
				<div id="js_edit_problem_list_loading" class="modal-footer m-b-m-15 text-centre hide">
					<i class="fa fa-spinner fa-spin med-green text-centre"></i> Processing
				</div>
			@endif
            </div><!-- /.box-body -->                                
        </div><!-- /.box Ends Contact Details-->
    </div>
</div><!-- /.modal-content -->

{!! Form::close() !!}