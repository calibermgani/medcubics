<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.cpt") }}' />
<input type="hidden" name="multiFeeScheduleCptID" value="{{ @$cpt->id }}" />
<?php 
    if(!isset($get_default_timezone)){
       $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
    }      
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
	<div class="box  no-shadow">
		<div class="box-block-header margin-b-10">
			<i class="livicon" data-name="doc-portrait"></i> <h3 class="box-title">Procedure Description</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		
		<div class="box-body  form-horizontal margin-l-10">
			<div class="form-group">                
				{!! Form::label('short_description', 'Short Description', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                            
				<div class="col-lg-5 col-md-5 col-sm-7 col-xs-10 @if($errors->first('short_description')) error @endif ">
					{!! Form::text('short_description',null,['class'=>'form-control','maxlength'=>28]) !!}  
					{!! $errors->first('short_description', '<p> :message</p>')  !!}                               
				</div>						                         
			</div>
			<div class="form-group">                
				{!! Form::label('long_description', 'Long Description', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                            
				<div class="col-lg-5 col-md-5 col-sm-7 col-xs-10 @if($errors->first('long_description')) error @endif ">
					{!! Form::textarea('long_description',null,['class'=>'form-control',]) !!}  
					{!! $errors->first('long_description', '<p> :message</p>')  !!}                               
				</div>						
			</div>
		</div>               
	</div>
</div>                        
<!--2nd Data-->
<?php $procedure_category =   App\Http\Helpers\Helpers::getProcedureCategory() ?>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
	<div class="box no-shadow">
		<div class="box-block-header margin-b-10">
			<i class="livicon" data-name="code"></i> <h3 class="box-title">Codes</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body  form-horizontal margin-l-10">
			<div class="form-group">
				{!! Form::label('Procedure Category', 'Procedure Category', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('pos_id')) error @endif ">
					{!! Form::select('procedure_category', array('' => '-- Select --') + (array)$procedure_category,  null,['class'=>'form-control select2']) !!}
					{!! $errors->first('procedure_category', '<p> :message</p>')  !!}  
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('type_of_service', 'Type of service', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
				<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('type_of_service')) error @endif ">
				{!! Form::text('type_of_service',null,['class'=>'form-control','maxlength'=>'50']) !!}
				{!! $errors->first('type_of_service', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">
				{!! Form::label('POS', 'POS', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('pos_id')) error @endif ">
					{!! Form::select('pos_id', array('' => '-- Select --') + (array)$pos,  null,['class'=>'form-control select2']) !!}
					{!! $errors->first('pos_id', '<p> :message</p>')  !!}  
				</div>
				<div class="col-sm-1"></div>
			</div> 
			<div class="form-group">
				{!! Form::label('applicable_sex', 'Applicable Sex', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="control-group col-lg-8 col-md-8 col-sm-8 col-xs-12">
					{!! Form::radio('applicable_sex', 'Male',null,['class'=>'','id'=>'c-male']) !!} {!! Form::label('c-male', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('applicable_sex', 'Female',null,['class'=>'','id'=>'c-female']) !!} {!! Form::label('c-female', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
					{!! Form::radio('applicable_sex', 'Others',null,['class'=>'','id'=>'c-others']) !!} {!! Form::label('c-others', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!} 
				</div>						
			</div>
			<div class="form-group">
				{!! Form::label('Referring provider', 'Referring Provider', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="control-group col-lg-8 col-md-8 col-sm-8 col-xs-12">
					{!! Form::radio('referring_provider', 'Yes',null,['class'=>'','id'=>'c-r-y']) !!} {!! Form::label('c-r-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('referring_provider', 'No',true,['class'=>'','id'=>'c-r-n']) !!} {!! Form::label('c-r-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
				</div>						
			</div>
			<div class="form-group">        
				{!! Form::label('age_limit', 'Age Limit', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-2 col-md-4 col-sm-7 col-xs-10 @if($errors->first('age_limit')) error @endif ">
					{!! Form::text('age_limit',null,['maxlength' => '3', 'class'=>'form-control rvu_number']) !!}
					{!! $errors->first('age_limit', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('modifier', 'Modifier', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
				<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('modifier')) error @endif ">
					 {!! Form::select('modifier_id[]',(array)$modifier,null,['class'=>'form-control select2','multiple','id'=>'modifierId']) !!}
					{!! $errors->first('modifier', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('revenue_code', 'Revenue Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('revenue_code')) error @endif ">
					{!! Form::text('revenue_code',null,['maxlength' => '5','class'=>'form-control']) !!}
					{!! $errors->first('revenue_code', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('drug_name', 'Drug Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('drug_name')) error @endif ">
					{!! Form::text('drug_name',null,['maxlength' => '250','class'=>'form-control js-letters-caps-format']) !!}
					{!! $errors->first('drug_name', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<?php $current_page  = Route::getFacadeRoot()->current()->uri(); ?>
			{!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
			<div class="form-group">        
				{!! Form::label('ndc_number', 'NDC Number', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('ndc_number')) error @endif ">
					{!! Form::text('ndc_number',null,['class'=>'form-control','minlength'=>10,'maxlength'=>11 , 'style'=>'width:90%;float:left;margin-right:2px;']) !!}
					{!! $errors->first('ndc_number', '<p> :message</p>')  !!}
					<i class="fa fa-edit" style="cursor: pointer;margin-top: 8px"></i>
				</div>
				<div class="col-sm-1">
				</div>
			</div>
			<div class="form-group">        
				{!! Form::label('min units', 'Min Units', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-2 col-md-4 col-sm-7 col-xs-10">
					{!! Form::text('min_units',null,['class'=>'form-control','maxlength'=>6]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('max units', 'Max Units', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-2 col-md-4 col-sm-7 col-xs-10">
					{!! Form::text('max_units',null,['class'=>'form-control', 'maxlength'=>6]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('anesthesia_unit', 'Anesthesia Base Unit', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-2 col-md-4 col-sm-7 col-xs-10 @if($errors->first('anesthesia_unit')) error @endif ">
					{!! Form::text('anesthesia_unit',null,['class'=>'form-control', 'maxlength'=>6]) !!}
					{!! $errors->first('anesthesia_unit', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('service_id_qualifier', 'Service ID Qualifier', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('service_id_qualifier')) error @endif ">
					{!! Form::select('service_id_qualifier', array('' => '-- Select --') + (array)$qualifier,null,['class'=>'form-control select2']) !!}
					{!! $errors->first('service_id_qualifier', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>                                                     
		</div>
	</div>
</div>
<?php $year_range = array_combine(range(date("Y")+0, date("Y")-4), range(date("Y")+0, date("Y")-4));  ?>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
	<div class="box no-shadow">
		<div class="box-block-header margin-b-10">
			<i class="livicon" data-name="credit-card"></i> <h3 class="box-title">Billing</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body  form-horizontal margin-l-10">
			<div class="form-group">      
				{!! Form::label('year', 'Year', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('billed_amount')) error @endif ">
					{!! Form::select('year', array('' => '-- Select --') + (array)@$year_range,null,['class'=>'form-control select2 js-multiFeeYear']) !!}
					{!! $errors->first('allowed_amount', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group"> 
				{!! Form::label('insurance', 'Insurance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}    
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('billed_amount')) error @endif ">
					{!! Form::select('insurance', array('' => '-- Select --','0'=>'Default'),null,['class'=>'form-control select2 js-multiFeeInsurance']) !!}
					{!! $errors->first('allowed_amount', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('allowed_amount', 'Allowed Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('allowed_amount')) error @endif ">
					{!! Form::text('allowed_amount',null,['id'=>'allowed_amount','class'=>'form-control js_amount_separation','autocomplete'=>'off']) !!}
					{!! $errors->first('allowed_amount', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('billed_amount', 'Billed Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('billed_amount')) error @endif ">
					{!! Form::text('billed_amount',null,['id'=>'billed_amount','class'=>'form-control js_amount_separation','autocomplete'=>'off']) !!}
					{!! $errors->first('billed_amount', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">
				{!! Form::label('required_clia_id', 'Required CLIA ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
				<div class="control-group col-lg-6 col-md-6 col-sm-6">
				{!! Form::radio('required_clia_id', 'Yes',null,['class'=>'js_required_clia_id','id'=>'c-clai-y']) !!} {!! Form::label('c-clai-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
				{!! Form::radio('required_clia_id', 'No',true,['class'=>'js_required_clia_id','id'=>'c-clai-n']) !!} {!! Form::label('c-clai-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
				</div>
				<div class="col-sm-1"></div>
			</div> 
			
			<div class='form-group js_required_clia_id_show hide'>        
				{!! Form::label('clia_id', 'CLIA ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('clia_id',null,['maxlength'=>'15','class'=>'form-control']) !!}
				</div>
				<div class="col-sm-1">
				</div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('workrvu', 'Work RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('work_rvu',null,['class'=>'form-control js_amount_separation','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group">        
				{!! Form::label('facility_practicervu', 'Facility practice RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('facility_practice_rvu',null,['class'=>'form-control js_amount_separation','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('nonfacility_practicervu', 'Non Facility practice RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('nonfacility_practice_rvu',null,['class'=>'form-control js_amount_separation','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('plirvu', 'PLI RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('pli_rvu',null,['class'=>'form-control js_amount_separation','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('total_facilityrvu', 'Total facility RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('total_facility_rvu',null,['class'=>'form-control js_amount_separation','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">        
				{!! Form::label('total_nonfacilityrvu', 'Total Nonfacility RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
				{!! Form::text('total_nonfacility_rvu',null,['class'=>'form-control js_amount_separation','maxlength'=>13]) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group hide">
				{!! Form::label('', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
				<div class="control-group col-lg-6 col-md-6 col-sm-6">
				{!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
				{!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>
			<div class="bottom-space-15 hidden-sm hidden-xs">&emsp;</div>
		</div>      
	</div>
</div>
<div id="cmsupdate" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Units</h4>
      </div>
      <div class="modal-body">
        <div class="col-sm-4 form-group"><label class="control-label">Unit Code</label></div>
        <div class="col-sm-8 form-group"><select name="unit_code" class="form-control select2" id="unit_code">
        	<option value="" @if(isset($cpt->unit_code) && $cpt->unit_code=='') selected @endif>-- Select --</option>
        	<option value="F2" @if(isset($cpt->unit_code) && $cpt->unit_code=='F2') selected @endif>F2</option>
        	<option value="GR" @if(isset($cpt->unit_code) && $cpt->unit_code=='GR') selected @endif>GR</option>
        	<option value="ME" @if(isset($cpt->unit_code) && $cpt->unit_code=='ME') selected @endif>ME</option>
        	<option value="ML" @if(isset($cpt->unit_code) && $cpt->unit_code=='ML') selected @endif>ML</option>
        	<option value="UN" @if(isset($cpt->unit_code) && $cpt->unit_code=='UN') selected @endif>UN</option>
        </select></div>
        <div class="col-sm-4 form-group"><label class="control-label">Unit Per CPT</label></div>
        <div class="col-sm-8 form-group"><input type="text" name="unit_cpt" id='unit_cpt' class="form-control" @if(isset($cpt->unit_cpt)) value="{{$cpt->unit_cpt}}" @endif /></div>
        <div class="col-sm-4 form-group"><label class="control-label">Unit Per NDC</label></div>
        <div class="col-sm-8 form-group"><input type="text" name="unit_ndc" class="form-control" @if(isset($cpt->unit_ndc)) value="{{$cpt->unit_ndc}}" @endif /></div>
        <div class="col-sm-4 form-group"><label class="control-label">Unit Value</label></div>
        <div class="col-sm-8 form-group"><input type="text" name="unit_value" id="unit_value" class="form-control" @if(isset($cpt->unit_value)) value="{{$cpt->unit_value}}" @endif /></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Save</button>
      </div>
    </div>

  </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
	{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
	@if(strpos($current_page, 'edit') !== false &&$checkpermission->check_url_permission('cpt/{cpt_id}/delete') == 1)
		<a class="btn btn-medcubics js-delete-confirm hide"data-text="Are you sure to delete the entry?" href="{{ url('cpt/'.@$cpt->id.'/delete') }}">Delete</a></center>
	@endif
	@if(strpos($current_page, 'edit') == false)
		 <a href="javascript:void(0)" data-url="{{url('cpt/')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	@endif
	@if(strpos($current_page, 'edit') !== false)
		 <a href="javascript:void(0)" data-url="{{url('cpt/'.@$cpt->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	@endif
</div>

@push('view.scripts')
<script type="text/javascript">
	$('input[type="text"]').attr('autocomplete','off');
	$(document).on( 'keypress', '#min_units, #max_units,#age_limit, .rvu_number', function (e) {
	//if the letter is not digit then display error and don't type anything
		if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			return false;
		}
	});
	$(document).on('change','#unit_code,#unit_cpt',function(){
		if($('#unit_cpt').val()!='' && $('#unit_code').val()!=''){
			if($('#cmsupdate .form-group').hasClass('has-error'))
				$("#cmsupdate .modal-footer .btn").prop('disabled',true);
			else
				$("#cmsupdate .modal-footer .btn").prop('disabled',false);
		}
	});
	const regex = /[^\d.]|\.(?=.*\.)/g;
	const subst=``;
	$('#unit_value').keyup(function(){
		const str=this.value;
		const result = str.replace(regex, subst);
		this.value=result;
	});
	$(document).on('change','#ndc_number',function(){
		<?php
		if(strpos($current_page, 'edit') !== false){
			if($cpt->unit_code=='' || $cpt->unit_cpt==''){
			?>
			if($(this).val().length>=10){
				$("#cmsupdate").modal();
			}
			<?php
			}
		}elseif(strpos($current_page, 'edit') == false){
			?>
			if($(this).val().length>=10){
				$("#cmsupdate").modal();
			}
			<?php
		}
		?>
	});
	$(document).on('click','.fa-edit',function(){
		$("#cmsupdate").modal();
	});

	$(document).on( 'keypress', '.js_amount_separation, #anesthesia_unit,input[name="min_units"],input[name="max_units"]', function (e) {
		if (e.which != 8 && e.which != 46 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			return false;
		}
	});
	$(document).on('ifToggled change','input[type="radio"][name="required_clia_id"]',function () {
		var chk = $(this).attr("value");
		
		//if(chk== 'Yes') {
			//var current_id = $(this).attr("id");
			//alert(current_id)
			/*if(chk == 'Yes')
				$(".js_required_clia_id_show").removeClass("hide");
			else
				$(".js_required_clia_id_show").addClass("hide");*/
		//}
	});	
	$(document).on( 'keyup', '[name="min_units"]', function () {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="max_units"]'));
	});	
	$(document).ready(function() {
		
		$(function() {
			var eventDates = {};
			eventDates[ new Date( '<?php echo $get_default_timezone; ?>' )] = new Date( '<?php echo $get_default_timezone; ?>' );
			$("#effectivedate").datepicker({
				changeMonth: true,
				changeYear: true,
				beforeShowDay: function(d) {
				setTimeout(function() {
				$(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
				 }, 10);
				var highlight = eventDates[d];
					if( highlight ) {
						 return [true, "ui-state-highlight", ''];
					} else {
					   
						 return [true, '', ''];
					}
				},
				onClose: function (selectedDate) {
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="effectivedate"]'));
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="terminationdate"]'));
				}
			});

			$("#terminationdate").datepicker({
				changeMonth: true,
				changeYear: true,
				beforeShowDay: function(d) {
				setTimeout(function() {
				$(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
				 }, 10);
				var highlight = eventDates[d];
					if( highlight ) {
						 return [true, "ui-state-highlight", ''];
					} else {
					   
						 return [true, '', ''];
					}
				},
				onClose: function (selectedDate) {
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="effectivedate"]'));
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="terminationdate"]'));
				}
			});
		});
		
		$('#js-bootstrap-validator')                                              
		.bootstrapValidator({
			message: 'This value is not valid',
			excluded: ':disabled',
			feedbackIcons: {
				valid: '',
				invalid: '',
				validating: ''
			},
			fields: {
			   /*  pos_id:{
					message:'pos field is invalid',
					validators:{
						notEmpty:{
							message: 'Select place of service!'
						}
					}
				},
				medicare_global_period:{
					message:'',
					validators:{
						notEmpty:{
							message: 'Enter medicare Global Period'
				},
						 integer: {
								message: 'medicare Global Period should be numeric',
								thousandsSeparator: '',
								decimalSeparator: '.'
							}                    
					}
						}, 
				medicare_allowable: {
					message:'',
					validators:{
						 integer: {
								message: '{{ trans("admin/cpt.validation.units") }}',
								thousandsSeparator: '',
								decimalSeparator: '.'
							}                    
					}
				},
				allowed_amount: {
					message:'',
					validators:{
						 integer: {
								message: '{{ trans("admin/cpt.validation.units") }}',
								thousandsSeparator: '',
								decimalSeparator: '.'
							}                    
					}
				},
				billed_amount: {
					message:'',
					validators:{
						 integer: {
								message: '{{ trans("admin/cpt.validation.units") }}',
								thousandsSeparator: '',
								decimalSeparator: '.'
							}                    
					}
				},
				min_units: {
					message:'',
					validators:{
						 integer: {
								message: '{{ trans("admin/cpt.validation.units") }}',
								thousandsSeparator: '',
								decimalSeparator: '.'
							}                    
					}
				},
				max_units: {
					message:'',
					validators:{
						 integer: {
								message: '{{ trans("admin/cpt.validation.units") }}',
								thousandsSeparator: '',
								decimalSeparator: '.'
							}                    
					}
				},*/
				ndc_number: {
					message: '',
					validators: {
						regexp: {
							regexp: /^[0-9a-zA-Z]+$/,
							message: '{{ trans("common.validation.alphanumeric") }}'
						},
						stringLength: {
							message: 'NDC Number must be minimum 10 and maximum 11 characters',
							min: 10,
							max: 11
						},
						callback: {
							message: '',
							callback: function (value, validator,$field) {
								$('#js-bootstrap-validator').bootstrapValidator('updateStatus', $("#unit_code"), 'NOT_VALIDATED')
		   .bootstrapValidator('validateField', $("#unit_code"));
								$('#js-bootstrap-validator').bootstrapValidator('updateStatus', $("#unit_cpt"), 'NOT_VALIDATED')
		   .bootstrapValidator('validateField', $("#unit_cpt"));
								if(value.length>=10) {
									$("#cmsupdate").modal();
									return true;
								} else if(value.length==0){
									return true;
								}
								return true;								
							}
						}
					}
				},
				unit_code: {
					message: '',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								if($("#ndc_number").val().length >= 10 && value=='') {
									$("#cmsupdate").modal();
									$("#cmsupdate .modal-footer .btn").prop('disabled',true);
									return {
												valid: false,
												message: '{{ trans("admin/cpt.validation.unit_code") }}'
											}; 
								}else {
									return true;
								}
							}
						}
					}
				},
				unit_cpt: {
					message: '',
					validators: {
						regexp: {
							regexp: /^[0-9]+$/,
							message: '{{ trans("common.validation.numeric") }}'
						},callback: {
							message: '',
							callback: function (value, validator) {
								if($("#ndc_number").val()!='' && $("#ndc_number").val().length >= 10 && value=='') {
									$("#cmsupdate").modal();
									$("#cmsupdate .modal-footer .btn").prop('disabled',true);
									return {
												valid: false,
												message: '{{ trans("admin/cpt.validation.unit_cpt") }}'
											}; 
								}else {
									return true;
								}
							}
						}
					}
				},
				unit_ndc: {
					message: '',
					validators: {
						regexp: {
							regexp: /^[0-9]+$/,
							message: '{{ trans("common.validation.numeric") }}'
						}
					}
				},
				min_units: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									if($.trim(value).length >0) {
										if(/^[0-9.]+$/.test(value)) {
											var count = value.split(".").length - 1;
											if(count>1) {
												return {
													valid: false,
													message: '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}'
												}; 
											}
											return true;
										}
										else {
											return {
													valid: false,
													message: '{{ trans("common.validation.numeric") }}'
												}; 
										}
									}
									return true;
								}
							}
						}
					},
					max_units: {
						message: '',
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									if($.trim(value).length >0) {
										if(/^[0-9.]+$/.test(value)) {
											var min_value = validator.getFieldElements('min_units').val();
											var error_msg = '{{ trans("practice/practicemaster/cpt.validation.max_unit_limit") }}';
											var response = getMinAlert(min_value,value,error_msg);
											var count = value.split(".").length - 1;
											if(count>1) {
												return {
													valid: false,
													message: '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}'
												}; 
											}
											if(response != true){
												return {
													valid: false,
													message: response
												}; 
											} 
											return true;
										}
										else {
											return {
													valid: false,
													message: '{{ trans("common.validation.numeric") }}'
												}; 
										}
									}
									return true;
								}
							}
						}
					},
				'modifier_id[]': {
					message: '',
					validators: {
						callback: {
							message: '{{ trans("admin/cpt.validation.modifier_max_length") }}',
							callback: function (value, validator) {
								if(value =='' || value ==null) return true;
								return (value.length > 4) ? false : true;
							}
						}
					}
				},
				medium_description:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("admin/cpt.validation.medium_des") }}'
						},
					}
				}, 
				drug_name:{
					message:'',
					validators:{
						regexp:{
							regexp: /^[A-Za-z ]+$/,
							message:  '{{ trans("common.validation.alphaspace") }}'
						}
					}
				},
				code_type:{
					message:'',
					validators:{
						regexp:{
							regexp: /^[A-Za-z 0-9]+$/,
							message:  '{{ trans("common.validation.alphanumericspac") }}'
						}
					}
				},
				revenue_code: {
					message: '',
					validators: {
						regexp: {
							regexp: /^[0-9a-zA-Z]+$/,
							message: '{{ trans("common.validation.alphanumeric") }}'
						}
					}
				},
				clia_id:{
					message:'',
					trigger: 'change keyup',
					validators:{
						regexp:{
							regexp: /^[a-zA-Z0-9\.\s]{0,15}$/,
							message:  '{{ trans("admin/cpt.validation.clia_id") }}'
						}			
					}
				},  			
				cpt_hcpcs:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("admin/cpt.validation.cpt_hcpcs") }}'
						},
					   regexp:{
							regexp: /^[a-zA-Z0-9]{0,6}$/,
							message: '{{ trans("admin/cpt.validation.cpt_hcpcs_regex") }}'
						}
					}
				},
				billed_amount:{
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var message = '';
									var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
									var count = value.split(".").length - 1;
									if(count>1) {
										return {
											valid: false,
											message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
										}; 
									}
									else if(value.length ==14){
									 return {
											valid: false,
											message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
										};
									}
									return (!regexp.test(value)) ? false:true;
									return true;
								}
							}					                                   
						}
					},
					allowed_amount:{
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var message = '';
									var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
									var count = value.split(".").length - 1;
									if(count>1) {
										return {
											valid: false,
											message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
										}; 
									}
									else if(value.length ==14){
									 return {
											valid: false,
											message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
										};
									}
									return (!regexp.test(value)) ? false:true;
									return true;
								}
							}					                                   
						}
					},
				icd: {
					message: '',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var err = 0;
								var msg_1 = '{{ trans("admin/icd.validation.code_regex") }}';
								var msg_2 = '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}';
								if(value.length > 1  && value.length < 3)
									err = 1;
								var regexp = (value.indexOf(".")== -1) ? /^[a-zA-Z0-9]{0,7}$/:/^[a-zA-Z0-9.]{0,8}$/;
								if (!regexp.test(value)) 
									err = 1;
								else {
									var val_arr = value.split(".");
									var count = value.split(".").length - 1;
									if(count>1) 
										err = 2;
									if(val_arr.length > 1 && val_arr[0].length < 3) 
										err = 1;						
								}
								if(err > 0) {
									var issue =eval("msg_"+err);
									return {
										valid: false,
										message: issue
									};
								}
								return true;
							}
						}
					}
				},
				anesthesia_unit: {
					message: '',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var count = value.split(".").length - 1;
								if(count>1) {
									return {
										valid: false,
										message: '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}'
									}; 
								}
								return true;
							}
						}
					}
				},
				effectivedate: {
					message: '',
					trigger: 'keyup change',
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: '{{ trans("common.validation.date_format") }}'
						},
						callback: {
							message: '{{ trans("common.validation.effectivedate") }}',
							callback: function (value, validator) {
								var termination_date = validator.getFieldElements('terminationdate').val();
								var response = startDate(value,termination_date);
								if (response != true){
									return {
										valid: false,
										message: response
									}; 
								} 
								return true;
							}
						}
					}
				},
				terminationdate: {
					message: '',
					trigger: 'keyup change',
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: '{{ trans("common.validation.date_format") }}'
						},
						callback: {
							message: '',
							callback: function (value, validator) {
								var eff_date = validator.getFieldElements('effectivedate').val();
								var ter_date = value;
								var response = endDate(eff_date,ter_date);
								if (response != true){
									return {
										valid: false,
										message: response
									}; 
								} 
								return true;
							}

						}
					}
				},
				work_rvu:{
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var message = '';
								var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
								var count = value.split(".").length - 1;
								if(count>1) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
									}; 
								}
								else if(value.length ==14){
								 return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
									};
								}
								return (!regexp.test(value)) ? false:true;
								return true;
							}
						}					                                   
					}
				},
				facility_practice_rvu:{
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var message = '';
								var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
								var count = value.split(".").length - 1;
								if(count>1) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
									}; 
								}
								else if(value.length ==14){
								 return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
									};
								}
								return (!regexp.test(value)) ? false:true;
								return true;
							}
						}					                                   
					}
				},
				nonfacility_practice_rvu:{
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var message = '';
								var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
								var count = value.split(".").length - 1;
								if(count>1) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
									}; 
								}
								else if(value.length ==14){
								 return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
									};
								}
								return (!regexp.test(value)) ? false:true;
								return true;
							}
						}					                                   
					}
				},
				pli_rvu:{
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var message = '';
								var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
								var count = value.split(".").length - 1;
								if(count>1) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
									}; 
								}
								else if(value.length ==14){
								 return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
									};
								}
								return (!regexp.test(value)) ? false:true;
								return true;
							}
						}					                                   
					}
				},
				total_facility_rvu:{
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var message = '';
								var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
								var count = value.split(".").length - 1;
								if(count>1) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
									}; 
								}
								else if(value.length ==14){
								 return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
									};
								}
								return (!regexp.test(value)) ? false:true;
								return true;
							}
						}					                                   
					}
				},
				total_nonfacility_rvu:{
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var message = '';
								var regexp = (value.indexOf(".")== -1) ? /^[0-9]{0,10}$/:/^[0-9.]{0,13}$/;
								var count = value.split(".").length - 1;
								if(count>1) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_format") }}'
									}; 
								}
								else if(value.length ==14){
								 return {
										valid: false,
										message: '{{ trans("practice/patients/checkReturn.validation.financial_charges_digits") }}'
									};
								}
								return (!regexp.test(value)) ? false:true;
								return true;
							}
						}					                                   
					}
				},
			}
		});
	});

	/*** Date function check start here ***/
	function startDate(start_date,end_date) {
		var date_format = new Date(end_date);
		if (end_date != '' && date_format !="Invalid Date") {
			return (start_date == '') ? '{{ trans("common.validation.eff_date_required") }}':true;
		}
		return true;
	}
	
	function endDate(start_date,end_date) {
		var eff_format = new Date(start_date);
		var ter_format = new Date(end_date);
		if (ter_format !="Invalid Date" && end_date != '' && eff_format !="Invalid Date" && end_date.length >7 && checkvalid(end_date)!=false) {
			var getdate = daydiff(parseDate(start_date), parseDate(end_date));
			return (getdate > 0) ? true : '{{ trans("common.validation.inactivedate") }}';
		}
		else if (start_date != '' && eff_format !="Invalid Date") {
			return (end_date == '') ? '{{ trans("common.validation.inactdate_required") }}':true;
		
		}
		return true;
	}
	
	function daydiff(first, second) {
		return Math.round((second-first)/(1000*60*60*24));
	}
	
	function parseDate(str) {
		var mdy = str.split('/')
		return new Date(mdy[2], mdy[0]-1, mdy[1]);
	}
	
	function checkvalid(str) {
		var mdy = str.split('/');
		if(mdy[0]>12 || mdy[1]>31 || mdy[2].length<4 || mdy[0]=='00' || mdy[0]=='0' || mdy[1]=='00' || mdy[1]=='0' || mdy[2]=='0000') {
			return false;
		}
	}
	/*** Date function check end here ***/
	$(document).ready(function(){ 
		var year = $('.js-multiFeeYear option:selected').val();
		var ins = $('.js-multiFeeInsurance option:selected').val();
		var allowed_amount = $('#allowed_amount').val();
		var billed_amount =$('#billed_amount').val();
		if(year !=""||ins!=""){
			$('#allowed_amount').attr('readonly','readonly');
			$('#billed_amount').attr('readonly','readonly'); 
		}
			// });
		
		$(document).on('change','.js-multiFeeYear',function(){ 
			$.ajax({
				type: "GET",
				url: api_site_url + '/yearInsurance/'+$(this).val(),
				success: function (result) { 
					if(result.length == 0){
						$('select[name="insurance"]').find('option').remove().end().append('<option value="">-- Select --</option><option value="0">Default</option>').val(''); 
						$('select[name="insurance"]').select2("val", null);
					}else{
						$('select[name="insurance"]').find('option').remove().end().append('<option value="">-- Select --</option><option value="0">Default</option>').val(''); 
						$.each(result, function(key, value) {   
							 $('select[name="insurance"]').append('<option value="'+ key +'">'+ value +'</option>'); 
						});
					}
				}
			});
			var year = $('.js-multiFeeYear option:selected').val();
			var ins = $('.js-multiFeeInsurance option:selected').val();
			if(year!=""||ins!=""){
				$('#allowed_amount').attr('readonly','readonly');
				$('#billed_amount').attr('readonly','readonly');
				$('#allowed_amount').val("");
				$('#billed_amount').val(""); }
			else{
				$('#allowed_amount').val(allowed_amount);
				$('#billed_amount').val(billed_amount);
				$('#allowed_amount').removeAttr('readonly','readonly');
				$('#billed_amount').removeAttr('readonly','readonly');}
		});
		
		$(document).on('change','.js-multiFeeInsurance',function(){
			var year = $('select.js-multiFeeYear').val();
			var insurance = $(this).val();
			var cpt_id = $('input[name="multiFeeScheduleCptID"]').val();
			var token = $('input[name="_token"]').val();
			$.ajax({
				type: "post",
				url: api_site_url + '/multiFeeScheduleData',
				data: {'_token':token,'insurance_id':insurance,'cpt_id':cpt_id,'year':year},
				dataType: 'json',
				success: function (result) { 
					console.log(result);
					if(result.billed_amount != '' && result.billed_amount != null) {
						if(result.billed_amount == '0.00') {
						$('input[name="billed_amount"]').val('').attr('readonly','readonly');
						} else {
						$('input[name="billed_amount"]').val(result.billed_amount).attr('readonly','readonly');
						}
					}
					else {
						$('input[name="billed_amount"]').val('').removeAttr('readonly','readonly');
					}
					if(result.allowed_amount != '' && result.allowed_amount != null) {
						if(result.allowed_amount == '0.00') {
						$('input[name="allowed_amount"]').val('').attr('readonly','readonly');
						} else {					
						$('input[name="allowed_amount"]').val(result.allowed_amount).attr('readonly','readonly');
						}
					}
					else {
						$('input[name="allowed_amount"]').val('').removeAttr('readonly','readonly');
					}
					var year = $('.js-multiFeeYear option:selected').val();
					var ins = $('.js-multiFeeInsurance option:selected').val();
					if(year!=""||ins!=""){
					$('#allowed_amount').attr('readonly','readonly');
					$('#billed_amount').attr('readonly','readonly'); }
					else{
					$('#allowed_amount').val(allowed_amount);
					$('#billed_amount').val(billed_amount);
					$('#allowed_amount').removeAttr('readonly','readonly');
					$('#billed_amount').removeAttr('readonly','readonly');}
					if(result.Modifier != '' && result.Modifier != null)
						$('#modifierId').select2('val', [result.Modifier]).prop("disabled", true).trigger('change');
					else
						$('#modifierId').select2('val', [result.default_modifier_id]).prop("disabled", false).trigger('change');
				}
			});
		});	
	});
	
</script>
@endpush