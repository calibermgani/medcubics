{!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-validators','name'=>'medcubicsform','class'=>'medcubicsform js_problem_show']) !!}   
	<?php
	$priority = ['High' => 'High','Moderate' => 'Moderate','Low' => 'Low'];
	$status = ['Assigned' => 'Assigned','Inprocess' => 'Inprocess','Pending'=> 'Pending','Review'=>'Review','Completed' => 'Completed'];
	
	?>
<div class="modal-dialog js_show_problem">
    <div class="modal-content">
        <div class="modal-header">
			<button type="button" class="close js_documentlist_update" aria-label="Close" id="close-button"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title "> History </h4>
        </div>
		<div id="edit_success_alert_part" class="col-lg-12 hide" style="float: none;">
        <p class="alert alert-success" id="edit_success_msg">added successfully</p>
    </div>
				
		<div class="modal-body no-padding">
            <div class="box box-view no-shadow no-border"><!--  Box Starts -->
				<input type="hidden" class="js_document_id" value=""/>
                <div class="box-body chat ar-notes form-horizontal js_problem_scroll">
				@include ('patients/patients/Document/documentfollowuplist')
				</div>
				<?php 
				$assigned_user_id = @$assigned_document[0]->assigned_user_id;
				$created_id = @$assigned_document[0]->created_by;
				$admin_role_id = Auth::user()->role_id;
				if($assigned_user_id == Auth::user()->id || $created_id == Auth::user()->id || $admin_role_id == 1){
				?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                    <p class="padding-4 med-orange bg-aqua font600">Create New </p>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5" ><!-- Left side Content Starts -->                            
                    <div class="form-horizontal"><!-- Box Starts -->
                        <div class="form-group-billing">                                                     
                            <div class="col-lg-12 col-md-12 col-sm-8 col-xs-10"> 
                                {!! Form::textarea('notes',null,['class'=>'form-control input-sm-modal-billing problem_desc','placeholder'=>'Notes']) !!}
                            </div>
                        </div> 

                        <div class="form-group-billing">					         
                            <div class="col-lg-3 col-md-3 col-sm-5 col-xs-6 select2-white-popup">  
                                {!! Form::select('assign_user_id', [''=>'-- Assign To --']+(array)@$practice,null,['class'=>'form-control select2 input-sm-modal-billing js_users']) !!}
                            </div>                                    

                            <div class="col-lg-3 col-md-3 col-sm-2 col-xs-4">
                                <span id="followup_date_icon"><i class="fa fa-calendar-o form-icon-billing"></i></span>
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
					<button class="btn btn-medcubics-small js_documentlist_update" id="close-popup-here" type="button">Cancel</button>
                </div>
				<?php } ?>
				<div id="js_edit_problem_list_loading" class="modal-footer m-b-m-15 text-centre hide">
					<i class="fa fa-spinner fa-spin med-green text-centre"></i> Processing
				</div>
            </div><!-- /.box-body -->                                
        </div><!-- /.box Ends Contact Details-->
    </div>
</div><!-- /.modal-content -->

{!! Form::close() !!}
<script type="text/javascript">
    $("select.select2.form-control").select2();
</script>