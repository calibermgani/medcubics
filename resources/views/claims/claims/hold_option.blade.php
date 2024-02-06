<div class="modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">Hold Claims</h4>
        </div>
        <div class="modal-body" >
            {!! Form::open(['name'=>'myform','id'=>'js-bootstrap-validator','class'=>'popupmedcubicsform']) !!}
            {!! Form::hidden('hold_claim_ids',null,['id'=>'hold_claim_ids']) !!} 
            <small class="help-block hide" id="js-error-msg"></small>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
                <div class="box box-info no-shadow no-border no-bottom">
                    <div class="box-body form-horizontal">                  
                        <div class="form-group">                             
                            {!! Form::label('Reason', 'Reason', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label-popup']) !!}
                            <div class="col-lg-7 col-md-7 col-sm-7">
                                {!! Form::select('hold_reason_id',[''=>'-- Select --']+(array)$hold_options+['add_new'=>'Add New'],null,['class'=>'select2 form-control input-sm-modal-billing','id'=>'hold_reason_id']) !!}  
                                <small class="help-block hide" id="js-error-hold_reason_id"></small>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>
                        <div class="form-group hide" id="js_new_hold_reason">                             
                            {!! Form::label('New Reason', 'New Reason', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label-popup']) !!}
                            <div class="col-lg-7 col-md-7 col-sm-7">
                                {!! Form::text('hold_reason',null,['id'=>'hold_reason','class'=>'form-control input-sm-header-billing']) !!}
                                <small class="help-block hide" id="js-error-hold_reason"></small>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>       
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit('Hold', ['class'=>'btn btn-medcubics-small js-submit-btn']) !!}
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small js_recentform','data-dismiss'=>'modal']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->