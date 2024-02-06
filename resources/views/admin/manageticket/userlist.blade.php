<span id="document_add_form_part">
       {!! Form::open(['name'=>'ticketassign_form','onsubmit'=>"event.preventDefault();",'id'=>'ticketassign_form','files'=>true]) !!}
       
       <!-- Modal Body -->
       <div class="modal-body form-horizontal">
     
		<div class="form-group">
            {!! Form::label('User List', 'User List', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!}  
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                {!! Form::select('userlist_id', array('' => '-- Select --')+(array)$userlist,null,['class'=>'select2 form-control js_select_user_id']) !!} 
            </div>
            <div class="col-sm-1"></div>
        </div>
		{!! Form::hidden('ticket_id',$ticket_id) !!}	
    </div><!-- /.box-body -->
    
    <!-- Modal Footer -->
	
	<div class="modal-footer spin_image hide">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green">
			<i class="fa fa-spinner fa-spin med-green font20"></i> Processing
		</div>
    </div>
	<div id="footer_part" class="modal-footer">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        {!! Form::submit('Assign', ['class'=>'btn btn-medcubics-small js-submit-btn','id'=>'frmsubmit']) !!}
        {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small js_ticket_reset']) !!}
        {!! Form::button('Close', ['class'=>'btn btn-medcubics-small close_popup']) !!}
		</div>
    </div>
    {!! Form::close() !!}
</span>