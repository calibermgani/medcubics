<div class="modal-header">
    <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?php 
        if(!isset($get_default_timezone)){
           $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
        }      
    ?>
    <h4 class="modal-title">
        @if($document_list_count>0 || $document_category=='alldocument')
        Documents
        @else
        New Document
        @endif
    </h4>
</div>
<div class="modal-body">

    <div style="display:none;" id="delete_doc_success-alert">
        <p class="alert alert-success">Document deleted successfully</p>
    </div>

    <div style="display:none;" id="delete_doc_error-alert">
        <p class="alert alert-error">Already assigned documents cannot be deleted.</p>
    </div>

    @if($document_list_count>0 || $document_category=='alldocument')

    <span @if($document_list_count==0 && $document_category!='alldocument') style="display:none" @endif id="document_list_form_part">	
           <div style="display:none;" id="add_doc_success-alert" class="col-lg-12">
            <p class="alert alert-success" id="success-alert">Document added successfully</p>
        </div>

        @if($document_category!='alldocument')
        <div class="col-lg-12 box-body-block pull-right p-t-0">
            <div class="box-body no-padding">
                <a id="add_more" class="pull-right m-r-m-10 font600 form-cursor"><i class="fa fa-plus"></i> Add More</a>
            </div>
        </div>
        @endif
        <div class='box-body-block no-padding'>
            <div class='box-body no-padding'>
                <table id='documents' class='table table-bordered table-striped table-collapse' data-category="{{ @$document_category }}">
                    <thead>
                        <tr>
                            <th>Created On</th> 
                            <th>User</th>           
                            <th>Title</th>           
                            <th>Category</th>
                            <th>Assigned To</th>
                            <th>Follow up Date</th>
                            <th>Status</th>
                            <th>Pages</th>
                            <th></th>
                            <th class="td-c-8"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count(@$documents_list)>0)
                        @foreach($documents_list as $list)	
                        <tr  class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list['id'] }}" data-url="{{url('patients/'.@$list['type_id'].'/document-assigned/'.@$list['id'].'/show')}}">
                            <td>{{ App\Http\Helpers\Helpers::timezone($list['created_at'],'m/d/y')}}</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($list['created_by']) }}</td>
                            <td><span data-toggle="tooltip" title="{{ ucfirst($list['title']) }}">{{ ucfirst(substr($list['title'], 0, 20)) }}</span></td>
                            <td>{{ $list['document_categories']['category_value'] }}</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($list['document_followup']['assigned_user_id']) }}</td>
                            <td>
                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list['document_followup']['followup_date'],'date'); ?>
                                @if(date("m/d/y") == $fllowup_date)
                                <span class="med-orange">{{$fllowup_date}}</span>
                                @elseif(date("m/d/y") >= $fllowup_date)
                                <span class="med-red">{{$fllowup_date}}</span>
                                @else
                                <span class="med-gray">{{$fllowup_date}}</span>
                                @endif
                            </td>
                            <td><span class="font600 {{ @$list['document_followup']['status'] }}" >{{ @$list['document_followup']['status'] }}</span></td>	
                            <td>{{ @$list['page'] }}</td>
                            <td>
                                <span class="{{@$list->document_followup->priority}}">
                                    @if(@$list['document_followup']['priority'] == 'High')
                                    <span class="hide">{{@$list['document_followup']['priority'] }}</span><i class="fa fa-arrow-up high-color" data-toggle="tooltip" data-original-title="High Priority" aria-hidden="true"></i>
                                    @elseif(@$list['document_followup']['priority'] == 'Low')
                                    <span class="hide">{{@$list['document_followup']['priority'] }}</span><i class="fa fa-arrow-down low-color" data-toggle="tooltip" data-original-title="Low Priority" aria-hidden="true"></i>
                                    @elseif(@$list['document_followup']['priority'] == 'Moderate')
                                    <span class="hide">{{@$list['document_followup']['priority'] }}</span><i class="fa fa-arrows-h moderate-color" data-toggle="tooltip" data-original-title="Moderate Priority" aria-hidden="true"></i>
                                    @endif							
                                </span>
                            </td>
                            <td>

                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list['type_id'],'encode').'/'.$list['document_type'].'/'.$list['filename']) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span>

                                <a onClick="window.open('{{ url('api/documentmodal/get/'.$document_type_id.'/'.$document_type.'/'.$list['filename']) }}', '_blank')"><i class="fa fa-eye form-cursor js-prevent-action margin-l-5 margin-r-5" data-placement="bottom" data-toggle="tooltip" title="View" data-original-title="View"></i></a>

                                <a class="js-popupdocument-delete remove-zindex" data-id="{{@$list['id']}}" href="javascript:void(0);"><i data-placement="bottom"  data-toggle="tooltip" title="Delete" class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="3"><p class="med-gray no-bottom text-center margin-t-10">No records found</p></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </span>

    @endif

    <span @if($document_list_count>0 || $document_category=='alldocument') style="display:none" @endif id="document_add_form_part">
		<?php
			if ($document_category == 'driving_license') {
				$document_category = 'Patient_Documents_Driving_License';
			}
		?>
		{!! Form::open(['name'=>'documentaddmodalform','onsubmit'=>"event.preventDefault();",'id'=>'documentaddmodalform','files'=>true,'class'=>'popupmedcubicsform']) !!}
		{!! Form::hidden('document_type',$document_type,['class'=>'form-control input-sm','id'=>'document_type']) !!}
		{!! Form::hidden('document_sub_type',$document_sub_type,['class'=>'form-control input-sm','id'=>'document_sub_type']) !!}
		{!! Form::hidden('document_type_id',$document_type_id,['class'=>'form-control input-sm','id'=>'document_type_id']) !!}	
		{!! Form::hidden('main_type_id',$main_type_id,['class'=>'form-control input-sm','id'=>'main_type_id']) !!}	
		{!! Form::hidden('document_category',$document_category,['class'=>'form-control input-sm','id'=>'document_category']) !!}
		{!! Form::hidden('upload_type','browse',['class'=>'form-control input-sm','id'=>'upload_type']) !!}	
        <!-- Modal Body -->
        <div class="modal-body form-horizontal">
            <div class="form-group">
                {!! Form::label('title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('title',null,['class'=>'form-control','autocomplete'=>'off','maxlength'=>120]) !!} 
                </div>
                <div class="col-sm-1"></div>
            </div> 
            <div class="form-group">
                {!! Form::label('category', 'Category', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!}  
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    <!--  Disabled also Document.js one line added -->
                    {!! Form::select('category', (array)$cate_type_list_arr,$document_category,['class'=>'select2 form-control','id'=>'category','disabled']) !!} 
                </div>
                <div class="col-sm-1"></div>
            </div>
            <?php if ($document_category == 'Authorization_Documents_Pre_Authorization_Letter') { ?>
                <div class="form-group">
                    <?php $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(Request::segment(2), 'decode'); ?>
                    {!! Form::label('Claim Number', 'Claim No', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                        {!! Form::select('claim_number',  array('' => '-- Select --') +(array)$claim_number,null,['class'=>'select2 form-control', 'id'=>'jsclaimnumber']) !!}                                        
                    </div>                                   
                </div>
            <?php } ?>
            <div class="form-group">
                {!! Form::label('assigned', 'Assigned To', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('assigned', array('' => '-- Select --') + (array)$user_list,null,['class'=>'select2 form-control','id'=>'assigned']) !!}
					@if(isset($errors) && $errors->first('assigned'))
						{!! $errors->first('assigned', '<p> :message</p>')  !!} 
					@endif
                </div>                                   
            </div> 

            <div class="form-group">
                {!! Form::label('priority', 'Priority', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('priority', array('' => '-- Select --') + (array)$priority,null,['class'=>'select2 form-control','id'=>'priority']) !!}
					@if(isset($errors) && $errors->first('priority'))
						{!! $errors->first('priority', '<p> :message</p>')  !!} 
					@endif
                </div>                                   
            </div>


            <div class="form-group">
                {!! Form::label('followup', 'Followup Date', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 p-r-0 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('followup',null,['class'=>'form-control dm-date','id'=>'follow_up_date', 'autocomplete'=>'off']) !!}
                </div>                                   
            </div> 

            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('status', array('' => '-- Select --') + (array)array('Assigned'=>'Assigned','Inprocess'=>'Inprocess','Pending'=>'Pending','Review'=>'Review','Completed'=>'Completed'),null,['class'=>'select2 form-control','id'=>'status']) !!}
					@if(isset($errors) && $errors->first('status'))
						{!! $errors->first('status', '<p> :message</p>')  !!} 
					@endif
                </div>                                   
            </div> 

            <div class="form-group">
                {!! Form::label('page', 'Pages', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('page',null,['class'=>'form-control js_numeric','autocomplete'=>'off','maxlength'=> 7]) !!} 
                </div>
                <div class="col-sm-1"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('notes', 'Notes', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::textarea('notes',null,['class'=>'form-control']) !!} 
                </div>                                   
            </div>


            <div class="form-group">
                <?php 
					$webcam = App\Http\Helpers\Helpers::getDocumentUpload('webcam'); 
					$scanner = App\Http\Helpers\Helpers::getDocumentUpload('scanner'); 
				?>
                @if($webcam || $scanner)  
                <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding">
                    {!! Form::label('attachment', 'Attachment', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                        {!! Form::radio('upload_type', 'browse',true,['class'=>'flat-red js-upload-type']) !!} Upload &emsp;
                        @if($webcam){!! Form::radio('upload_type', 'webcam',null, ['class'=>'flat-red js-upload-type']) !!} Picture &emsp;@endif
                        @if($scanner){!! Form::radio('upload_type', 'scanner',null,['class'=>'flat-red js-upload-type']) !!} Scanner @endif
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
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding">
                        <span class="fileContainer " style="padding:1px 16px;">
                            <input class="form-control form-cursor uploadFile" name="filefield[]" type="file" id="filefield1" multiple="ture">Upload  </span>
						@if(isset($errors) && $errors->first('filefield'))	
                        {!! $errors->first('filefield',  '<p> :message</p>')  !!}
						@endif
                        <div>&emsp;<p class="js-display-error" style="display: inline;"></p>
                            <span><i class="fa fa-times-circle cur-pointer removeFile margin-l-10 med-red" data-placement="bottom" data-toggle="tooltip" title="Remove" data-original-title="Tooltip on bottom" style="display:none;"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-photo margin-t-10" style="display:none">
                    {!! Form::label('', '', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding">
                        <span class="fileContainer js-webcam-class" style="padding:1px 20px;">
                            <input type="hidden" class="js_err_webcam" /> Webcam</span>
							@if(isset($errors) && $errors->first('filefield'))
							{!! $errors->first('filefield',  '<p> :message</p>')  !!} 
							@endif
                        &emsp;<span class="js-display-error"></span>
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <div class="box-footer js-scanner" style="display:none"> 
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                        <button type="button" class="btn btn-medcubics" onclick="scan();">Scan</button>
                    </div>
                </div>

                <input type="hidden" name="scanner_filename" id="scanner_filename">
                <input type="hidden" name="scanner_image" id="scanner_image">
                @if(isset($errors) && $errors->first('filefield'))
                <div class="form-group">
                    {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
                    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('filefield')) error @endif">
                       {!! $errors->first('filefield',  '<p> :message</p>')  !!}
                    </div>                                                          
                    <div class="col-sm-1"></div>
                </div>
                @endif
            </div><!-- /.box-body -->
            <div class="form-group" id="js-show-webcam" style="display:none">
                {!! Form::label('', '', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    <table class="main">
                        <tr>
                            <td valign="top">
                                <div class="border">
                                    Live Webcam<br>                           
                                </div>
                                <br/><input type="button" class="snap" value="SNAP IT" id="js-snap">
                            </td>			     
                            <td width="50"><div id="webcam"></div></td>
                            <td valign="top">
                                <div id="upload_results" class="border">
                                    Snapshot<br>
                                    <img src="{{ URL::to('/') }}/img/web_logo.jpg" style="width:50%;"/>
                                </div>
                            </td>
                        </tr>
                        <?php $api_url = url('/api/getwebcamimage/' . $document_type); ?>
                        {!! Form::hidden('apiurl',$api_url,['id' => 'apiurl']) !!}
                        {!! Form::hidden('webcam_image',null,['id' => 'webcam_image']) !!}
                        {!! Form::hidden('webcam_filename',null, ['id' => 'filename_image']) !!} 
                        {!! Form::hidden('error-cam',null,['id' => 'error-cam']) !!}
                    </table>
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group add_doc_error-alert" class="hide" style="display:none">
                {!! Form::label('', '', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 error">
                    <p class="js_document_err_msg"></p>
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="box-footer js-scanner" style="display:none"> 
                <button type="button" class="btn btn-medcubics">Scan</button>
            </div>

            <input type="hidden" name="scanner_filename" id="scanner_filename">
            <input type="hidden" name="scanner_image" id="scanner_image">  
        </div><!-- /.box-body -->


        <!-- Modal Footer -->

        <div class="modal-footer spin_image hide">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green">
                <i class="fa fa-spinner fa-spin med-green font20"></i> Processing
            </div>
        </div>
        <div id="footer_part" class="modal-footer">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js-submit-btn','id'=>'frmsubmit']) !!}
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small close_popup']) !!}
                {!! Form::button('Reset', ['class'=>'btn btn-medcubics-small js_popup_form_reset']) !!}

            </div>
        </div>
        {!! Form::close() !!}
    </span>
</div>
<!-- Show Problem list start-->
<div id="show_document_assigned_list" class="modal fade in js_model_show_document_assigned_list"></div><!-- /.modal-dialog -->
<!-- Show Problem list end-->
<script type="text/javascript">
// Only numeric allow to enter
$(document).on('keypress keyup blur','.js_numeric',function(event){
	$(this).val($(this).val().replace(/[^\d].+/, ""));
	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});	

<?php if(isset($get_default_timezone)){?>
     var get_default_timezone = '<?php echo $get_default_timezone;?>';    
<?php }?>
</script>