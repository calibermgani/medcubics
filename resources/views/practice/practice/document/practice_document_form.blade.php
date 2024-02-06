<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
    @if($document_type=='practice')
		{!! Form::open(['url'=>'document/addDocument','id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
		<?php $document_type_id = ""; ?>  
		<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.practice_document") }}' />
    @elseif($document_type=='facility')
		{!! Form::open(['url'=>'facility/'.$facility->id.'/facilitydocument/addDocument','id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
		<?php $document_type_id = $facility->id; ?>
		<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.facility_document") }}' />
    @elseif($document_type=='provider')
		{!! Form::open(['url'=>'provider/'.$provider->id.'/providerdocuments','id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
		?php $document_type_id = $provider->id; ?>  
		<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.provider_document") }}' />
    @endif
    <div class="box box-info no-shadow">
        <div class="box-block-header">
            <i class="livicon" data-name="folders"></i> <h3 class="box-title">New Document</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->
        <div class="box-body  form-horizontal margin-l-10 margin-t-10">

            <div class="form-group">
                {!! Form::label('title', 'Title', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('title')) error @endif">
                    {!! Form::text('title',null,['class'=>'form-control','id'=>'tle','maxlength' => 120]) !!} 
                    {!! $errors->first('title', '<p> :message</p>')  !!} 
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group">
                {!! Form::label('category', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('category')) error @endif">
                    {!! Form::select('category', array('' => '-- Select --') + (array)$cate_type_list_arr,null,['class'=>'select2 form-control','id'=>'category']) !!}
                    {!! $errors->first('category', '<p> :message</p>')  !!} 
                </div>
                <div class="col-sm-1"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('assigned', 'Assigned To', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('category')) error @endif">
                    {!! Form::select('assigned', array('' => '-- Select --') + (array)$user_list,null,['class'=>'select2 form-control','id'=>'assigned']) !!}
                    {!! $errors->first('assigned', '<p> :message</p>')  !!} 
                </div>
            </div> 



            <div class="form-group">
                {!! Form::label('priority', 'Priority', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('priority')) error @endif">
                    {!! Form::select('priority', array('' => '-- Select --') + (array)$priority,null,['class'=>'select2 form-control','id'=>'priority']) !!}
                    {!! $errors->first('priority', '<p> :message</p>')  !!} 
                </div>
            </div> 

            <div class="form-group">
                {!! Form::label('followup', 'Followup Date', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6">
                    {!! Form::text('followup',null,['class'=>'form-control dm-date','id'=>'follow_up_date']) !!}
                </div>
            </div> 

            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('category')) error @endif">
                    {!! Form::select('status', array('' => '-- Select --') + (array)array('Assigned'=>'Assigned','Inprocess'=>'Inprocess','Pending'=>'Pending','Review'=>'Review','Completed'=>'Completed'),null,['class'=>'select2 form-control','id'=>'status']) !!}
                    {!! $errors->first('status', '<p> :message</p>')  !!} 
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('page', 'Pages', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6">
                    {!! Form::text('page',null,['class'=>'form-control js_numeric','autocomplete'=>'off','maxlength'=> 7]) !!}
                </div>
                <div class="col-sm-1"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('notes', 'Notes', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6">
                    {!! Form::textarea('notes',null,['class'=>'form-control']) !!} 
                </div>
            </div> 

            <!-- Adding for upload type field for temp -->
            <input type="hidden" name="upload_type" value="browse" />
            <!-- Adding for upload type field for temp -->
            <div class="form-group">
                <?php $webcam = App\Http\Helpers\Helpers::getDocumentUpload('webcam'); ?>
                <?php $scanner = App\Http\Helpers\Helpers::getDocumentUpload('scanner'); ?>
                @if($webcam || $scanner)  
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    {!! Form::label('upload_type', 'Attachments', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label star']) !!} 
                    <div class="col-md-6 col-sm-4">
                        {!! Form::radio('upload_type', 'browse',true,['class' => 'flat-red js-upload-type','id'=>'c-upload']) !!} {!! Form::label('c-upload', 'Upload',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                        @if($webcam){!! Form::radio('upload_type', 'webcam',null, ['class' => 'flat-red js-upload-type','id'=>'c-picture']) !!} {!! Form::label('c-picture', 'Picture',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;@endif
                        @if($scanner){!! Form::radio('upload_type', 'scanner',null,['class' => 'flat-red js-upload-type','id'=>'c-scanner']) !!} {!! Form::label('c-scanner', 'Scanner',['class'=>'med-darkgray font600 form-cursor']) !!} @endif
                    </div>
                    <div class="col-sm-1"></div>
                </div> 
                @endif
                <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-upload margin-t-10">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <div class="dropdown pull-right">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-question-circle margin-t-3 med-green form-icon-billing pull-right"  data-placement="top" data-toggle="tooltip" data-original-title="Info"></i>
                            </a>
                            <div class="dropdown-menu1">
                                <p class="font12 padding-4">pdf, jpeg, jpg, png, gif, doc, xls, csv, docx, xlsx, txt</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding @if($errors->first('filefield')) error @endif">
                        <span class="fileContainer " style="padding:1px 16px;">
                            <input class="form-control form-cursor uploadFile" name="filefield[]" type="file" multiple="true" id="filefield1">Upload  </span>
                        {!! $errors->first('filefield',  '<p> :message</p>')  !!}
                        <div>&emsp;<p class="js-display-error" style="display: inline;"></p>
                            <span><i class="fa fa-times-circle cur-pointer removeFile margin-l-10 med-red" data-placement="bottom" data-toggle="tooltip" title="Remove" data-original-title="Tooltip on bottom" style="display:none;"></i></span>
                        </div>
                    </div>
                </div>

                 
                <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-photo margin-t-10" style="display:none">
                    {!! Form::label('', '', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                    <div class="col-lg-3 col-md-6 col-sm-6 no-padding">
                        <span class="fileContainer js-webcam-class" style="padding:1px 20px 1px 11px;">
                            <input type="hidden" class="js_err_webcam col-lg-2 col-md-2 col-sm-3 form-control" /> Webcam</span>
                        {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                        &emsp;<span class="js-display-error"></span>
                    </div>
                    <div class="col-sm-1"></div>
                </div>

            </div>
            <div class="box-footer js-scanner" style="display:none"> 
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <button type="button" class="btn btn-medcubics" onclick="scan();">Scan</button>
                </div>
            </div>

            <input type="hidden" name="scanner_filename" id="scanner_filename">
            <input type="hidden" name="scanner_image" id="scanner_image">                      


        </div><!-- /.box-body -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            {!! Form::submit('Save', ['class'=>'btn btn-medcubics form-group']) !!}
            <a  href="javascript:void(0)" 
                @if($document_type=='practice')
                data-url="{{ url('document')}}"
                @elseif($document_type=='facility')
                data-url="{{url('facility/'.$facility->id.'/facilitydocument')}}" 
                @elseif($document_type=='provider')
                data-url="{{ url('provider/'.$provider->id.'/providerdocuments')}}" 
                @endif
                >{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
        </div>

    </div><!-- /.box -->
    <div style="display:none" id="js-show-webcam">
        @if($document_type=='practice')
        @include ('layouts/webcam', ['type' => 'practice']) 
        @elseif($document_type=='facility')
        @include ('layouts/webcam', ['type' => 'facility']) 
        @elseif($document_type=='provider')
        @include ('layouts/webcam', ['type' => 'provider']) 
        @endif
    </div>
    {!! Form::close() !!}
</div><!--/.col (left) -->