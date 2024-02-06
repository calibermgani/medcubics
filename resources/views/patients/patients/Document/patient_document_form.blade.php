<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
		{!! Form::open(['url'=>'document/addDocument','id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
            <div class="box box-info no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="folders"></i> <h3 class="box-title">New Document</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="box-body  form-horizontal">
                        
                    <div class="form-group">
                         {!! Form::label('title', 'Title', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('title')) error @endif">
                            {!! Form::text('title',null,['class'=>'form-control','maxlength' => 20]) !!} 
                            {!! $errors->first('title', '<p> :message</p>')  !!} 
                        </div>
                        <div class="col-sm-1"></div>
                    </div> 
					
					
                    <div class="form-group">
                         {!! Form::label('category', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('category')) error @endif">
                            {!! Form::select('category', $cate_type_list_arr,null,['class'=>'select2 form-control','id'=>'category']) !!}
                            {!! $errors->first('category', '<p> :message</p>')  !!} 
                        </div>
                        <div class="col-sm-1"></div>
                    </div> 

                    <!--div class="form-group">
                         {!! Form::label('description', 'Description', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('description')) error @endif">
                            {!! Form::textarea('description',null,['class'=>'form-control']) !!} 
                            {!! $errors->first('description', '<p> :message</p>')  !!} 
                        </div>
                        <div class="col-sm-1"></div>
                    </div--> 
					
					<div class="form-group">
                        {!! Form::label('upload_type', 'Choose type to upload document', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-md-6 col-sm-4">
                             {!! Form::radio('upload_type', 'webcam',null, ['class' => 'js-upload-type']) !!} Capture photo &emsp; {!! Form::radio('upload_type', 'browse',true,['class' => 'js-upload-type']) !!} upload from PC
                         </div>                                                          
                        <div class="col-sm-1"></div>
                    </div> 
                                                 
                    <div class="form-group js-upload">
                          {!! Form::label('filefield', 'Attachment', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('description')) error @endif">
                            {!! Form::file('filefield',null,['class'=>'form-control']) !!} 
                            {!! $errors->first('filefield', '<p style="color:#a94442;"> :message</p>')  !!} 
                        </div>
                        <div class="col-sm-1"></div>
                    </div>  
					<div class="box-footer js-photo" style="display:none">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          Click to capture photo<input type="button" value="Takephot" class="btn btn-medcubics js-webcam-class">                        
                        </div>
                   </div>
				   
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                       {!! Form::submit('Save', ['class'=>'btn btn-medcubics form-group']) !!}
                       <a href="javascript:void(0)" data-url="{{ url('document')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                    </div>
                </div><!-- /.box-footer -->
            </div><!-- /.box -->
			<div style="display:none" id="js-show-webcam">
			@if($document_type=='patients')
			@include ('layouts/webcam', ['type' => 'patients']) 
			@endif
			</div>
		{!! Form::close() !!}
        </div><!--/.col (left) -->
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->        