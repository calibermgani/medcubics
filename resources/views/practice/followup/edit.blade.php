<div class="modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
			<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">
				Add Claim Status & Question
			</h4>
		</div>
      <div class="modal-body">
		{!! Form::open(['url'=>'followup/edit/category','id'=>'js-bootstrap-validator-editcategory','name'=>'medcubicsform','class'=>'medcubicsform popupmedcubicsform']) !!}
           <!-- Modal Body -->
		<input type="hidden" name="id" value="{{ $category->id}}" />
        <div class="modal-body form-horizontal">
            <div class="form-group">
				{!! Form::label('categoty', 'Category', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('category',$category->name,['maxlength'=>'20','class'=>'form-control','name'=>'category','id'=>'edi_category','autocomplete'=>'off']) !!}
                    {!! $errors->first('category', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1"></div>
            </div> 

			<div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
				<?php $active = null; $inactive = null; ?>
					@if($category->status == 'Active')
						<?php $active = true;   ?>
					@else
						<?php $inactive = true; ?>
					@endif
                    {!! Form::radio('status', 'Active',$active,['class'=>'','id'=>'c3-active']) !!} {!! Form::label('c3-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('status', 'Inactive',$inactive,['class'=>'','id'=>'c3-inactive']) !!} {!! Form::label('c3-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
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