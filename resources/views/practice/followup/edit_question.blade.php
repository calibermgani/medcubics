<?php 
	$filed_type = array('date'=>'Date','text'=>'Text');
	$filed_validation = array('number'=>'Number Only','text'=>'Text Only','both'=>'Both');
	$date_type = array('single_date'=>'Single Date','double_date'=>'Double Date');
?>
<div class="modal-md">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
			<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">
				Edit Claim Status & Question
			</h4>
		</div>
      <div class="modal-body">
		{!! Form::open(['url'=>'followup/edit/question','id'=>'js-bootstrap-validator-edit','name'=>'medcubicsform','class'=>'medcubicsform popupmedcubicsform']) !!}
		<input type="hidden" name="id" value="{{ $question[0]->id }}" />
           <!-- Modal Body -->
        <div class="modal-body form-horizontal">
            <div class="form-group">
                {!! Form::label('category', 'Claim Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('category', array('' => '-- Select --') + (array)$category,  $question[0]->category_id,['class'=>'form-control select2','id'=>'followup_category']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group">
                {!! Form::label('question', 'Question', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::textarea('question',$question[0]->question,['class'=>'form-control','name'=>'question']) !!}
                    {!! $errors->first('question', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group">
                {!! Form::label('hint', 'Hint', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('hint',$question[0]->hint,['class'=>'form-control','name'=>'hint','maxlength'=>25,'autocomplete'=>'off']) !!}
                    {!! $errors->first('question', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			<div class="form-group">
                {!! Form::label('field_type', 'Field Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('field_type', array('' => '-- Select --') + (array)$filed_type,  $question[0]->field_type,['class'=>'form-control select2','id'=>'edit_field_type']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>

			<div class="form-group hide" id="edit_date_type">
                {!! Form::label('date_type', 'Date Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('date_type', array('' => '-- Select --') + (array)$date_type,  $question[0]->date_type,['class'=>'form-control select2','id'=>'edit_date_type']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group hide" id="edit_field_validation">
                {!! Form::label('field_validation', 'Field Validation', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   {!! Form::select('field_validation', array('' => '-- Select --') + (array)$filed_validation,  $question[0]->field_validation,['class'=>'form-control select2','id'=>'edit_field_validation']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
			
			<div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
					<?php 
						$active = null; $inactive = null; 
						if($question[0]->status == 'Active') {
							$active = true;
						} else {
							$inactive = true;
						}
					?>					
										
                    {!! Form::radio('status', 'Active',$active,['class'=>'','id'=>'c1-active']) !!} {!! Form::label('c1-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',$inactive,['class'=>'','id'=>'c1-inactive']) !!} {!! Form::label('c1-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
                    {!! $errors->first('status', '<p> :message</p>')  !!}
                </div>                       
            </div>
		</div>
		<div id="footer_part" class="modal-footer">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js-submit-btn','id'=>'frmsubmit']) !!}
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small close_popup']) !!}
            </div>
        </div>
		{!! Form::close() !!}
      </div>     
    </div>
  </div>