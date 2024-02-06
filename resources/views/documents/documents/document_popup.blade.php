<div class="modal-header">
    <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">New Document</h4>
</div>
<div class="modal-body">
    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.documents.document") }}' />
    <span id="document_add_form_part">
        {!! Form::open(['url' => '','name'=>'document_add_popupform','onsubmit'=>"event.preventDefault();",'id'=>'document_add_popupform','files'=>true,'class'=>'popupmedcubicsform']) !!}
        {!! Form::hidden('practice_id',$practice_id,['class'=>'form-control input-sm','id'=>'practice_id']) !!}
        {!! Form::hidden('upload_type','browse',['class'=>'form-control input-sm','id'=>'upload_type']) !!}	
        <!-- Modal Body -->
        <div class="modal-body form-horizontal">
            <div class="form-group">
                {!! Form::label('title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('title')) error @endif">
                    {!! Form::text('title',null,['class'=>'form-control','autocomplete'=>'off','maxlength'=>120,'id'=>'tle']) !!} 
                    {!! $errors->first('title', '<p> :message</p>')  !!} 
                </div>
                <div class="col-sm-1"></div>
            </div> 
            <div class="form-group">
                {!! Form::label('module', 'Module', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('document_type', array('' => '-- Select --','facility' => 'Facility','provider' => 'Provider','patient' => 'Patient','group' => 'Group'),null,['class'=>'select2 form-control js_select_module']) !!} 
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group js_select_type hide">
                {!! Form::label('select', 'Select', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label js_select_type_label','data-text'=>'select']) !!}  
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('type_id', array('' => '-- Select --'),null,['class'=>'select2 form-control js_select_type_id']) !!}
					{!! Form::text('type_id',null,['id'=>'patient_search','class'=>'form-control  input-sm-header-billing js_select_patient_id js_select_Patient js_select_type_id','autocomplete'=>'nope','placeholder'=>'LN, FN, Acc No, SSN']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group">
                {!! Form::label('category', 'Category', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!}  
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('category', array('' => '-- Select --'),null,['class'=>'select2 form-control js_select_category','id'=>'category_new']) !!} 
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group js-claim-data" style="display: none;">
                {!! Form::label('Claim Number', 'Claim No', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('claim_number',  array('' => '-- Select --')+(array)$claim_number,null,['class'=>'select2 form-control', 'id'=>'jsclaimnumber']) !!}
                </div>                                   
            </div>
            <div class="form-group show_payer_details hide">
                {!! Form::label('payer', 'Payer', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('payer',  array('' => '-- Select --') + (array)App\Http\Helpers\Helpers::getInsuranceNameLists(),null,['class'=>'select2 form-control payer-validation','id'=>'payer']) !!}                                        
                </div>                                   
            </div>
            <div class="form-group show_payer_details payer_appeal hide">
                {!! Form::label('checkno', 'Check No', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                   <!-- MR-2911 : Change cheque limit -->
                    {!! Form::text('checkno',null,['class'=>'form-control payer-validation','autocomplete'=>'off','maxlength'=>50,'id'=>'checkno']) !!} 
                </div>
                <div class="col-sm-1"></div>
            </div> 
            <div class="form-group show_payer_details payer_appeal hide">
                {!! Form::label('checkdate', 'Check Date', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('checkdate',null,['class'=>'form-control payer-validation dm-date','autocomplete'=>'off','id'=>'checkdate']) !!} 
                </div>
                <div class="col-sm-1"></div>
            </div> 
            <div class="form-group show_payer_details payer_appeal hide">
                {!! Form::label('checkamt', 'Check Amount', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('checkamt',null,['class'=>'form-control payer-validation','autocomplete'=>'off','id'=>'checkamt']) !!}
                </div>
                <div class="col-sm-1"></div>
            </div> 
            <div class="form-group">
                {!! Form::label('assigned', 'Assigned To', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('assigned', array('' => '-- Select --') + (array)$user_list,null,['class'=>'select2 form-control','id'=>'assigned']) !!}
                    {!! $errors->first('assigned', '<p> :message</p>')  !!} 
                </div>                                   
            </div> 

            <div class="form-group">
                {!! Form::label('priority', 'Priority', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('priority', array('' => '-- Select --') + (array)$priority,null,['class'=>'select2 form-control','id'=>'priority']) !!}
                    {!! $errors->first('priority', '<p> :message</p>')  !!} 
                </div>                                   
            </div> 

            <div class="form-group">
                {!! Form::label('followup', 'Followup Date', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 p-r-0 control-label star ']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('followup',null,['class'=>'form-control dm-date','id'=>'follow_up_date','autocomplete'=>'off']) !!}
                </div>                                   
            </div> 

            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::select('status', array('' => '-- Select --') + (array)array('Assigned'=>'Assigned','Inprocess'=>'Inprocess','Pending'=>'Pending','Review'=>'Review','Completed'=>'Completed'),null,['class'=>'select2 form-control','id'=>'status']) !!}
                    {!! $errors->first('status', '<p> :message</p>')  !!} 
                </div>                                   
            </div> 

            <div class="form-group">
                {!! Form::label('page', 'Pages', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                    {!! Form::text('page',null,['class'=>'form-control js_numeric', 'autocomplete'=>'off', 'maxlength'=> 7]) !!} 
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
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    {!! Form::label('attachment', 'Attachment', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                        {!! Form::radio('upload_type', 'browse',true,['class' => 'js-upload-type','id'=>'c-upload']) !!} {!! Form::label('c-upload', 'Upload',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                        @if($webcam){!! Form::radio('upload_type', 'webcam',null, ['class' => 'js-upload-type','id'=>'c-picture']) !!} {!! Form::label('c-picture', 'Picture',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;@endif
                        @if($scanner){!! Form::radio('upload_type', 'scanner',null,['class' => 'js-upload-type','id'=>'c-scanner']) !!} {!! Form::label('c-scanner', 'Scanner',['class'=>'med-darkgray font600 form-cursor']) !!} @endif
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
                        <span class="fileContainer" style="padding:1px 16px;"> 
                            {!! Form::file('filefield[]',['class'=>'form-control form-cursor uploadFile','id'=>'filefield1', 'multiple'=>'true']) !!}Upload 
						</span>
                        {!! $errors->first('filefield',  '<p> :message</p>') !!} 
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
                        {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                        &emsp;<span class="js-display-error"></span>
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-show-webcam margin-t-10" style="display:none">
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
                            {!! Form::hidden('webcam_image',null,['id' => 'webcam_image']) !!}
                            {!!Form::hidden('webcam_filename',null, ['id' => 'filename_image']) !!} 
                            {!!Form::hidden('error-cam',null,['id' => 'error-cam'])!!}
                        </table>
                    </div>
                    <div class="col-sm-1"></div>
                </div>
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
		{!! Form::hidden('patient_id',null,['id'=>'patient_id','class'=>'form-control input-sm-header-billing']) !!}
        <div id="footer_part" class="modal-footer">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js-submit-btn','id'=>'frmsubmit']) !!}
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small close_popup']) !!}
                {!! Form::button('Reset', ['class'=>'btn btn-medcubics-small js_popup_form_reset','onClick'=> "this.form.reset()"]) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </span>
</div>

<script>
    disableAutoFill('#js-bootstrap-validator');
    var tem_ssn = new Array();
   
   // Only numeric allow to enter
    $(document).on('keypress keyup blur','.js_numeric',function(event){
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    }); 
    
	var current_date = '{{date("Y-m-d")}}';
        <?php  
			$patient = 1;
			$patient_id = 1;
        if($patient !=""){
        ?>
        $(document).ready(function () {
            
			$(".modal-content #js-bootstrap-validator #patient_search").val("{{$patient}}");          
			patient_search_func();          
			$('#patient_search').autocomplete('search', $('#patient_search').val());     
      
			$('#patient_search').on('autocompleteopen', function(){                  
				console.log("demo");
				$('#patient_search').data('ui-autocomplete')._trigger('select', 'autocompleteselect', {item:{value:$(".ui-autocomplete > li:first").trigger("click")}});            
            });
			$( "#patient_search" ).on( "keyup keypress blur change", function() {
				 console.log("test");
				$('#patient_search').unbind("autocompleteopen");
			});  
			/* setTimeout(function(){    
				$("#patient_id").val("{{$patient_id}}");
			}, 1000);    */      
        });       
		<?php }?>
</script>