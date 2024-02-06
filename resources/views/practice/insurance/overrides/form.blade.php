<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
	<div class="box box-info no-shadow">
		<div class="box-block-header with-border">
			<i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<!-- form start -->

		<div class="box-body  form-horizontal margin-l-10 margin-t-10">
			
			

			<div class="form-group">
				{!! Form::label('insurance', 'Facility',  ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('facility_id')) error @endif">
					{!! Form::select('facility_id', array('' => '-- Select --') + (array)$facilities,  $facilities_id,['class'=>'select2 form-control']) !!}
					{!! $errors->first('facility_id', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>


			<div class="form-group">
				{!! Form::label('billing_provider', 'Provider', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('providers_id')) error @endif">
					{!! Form::select('providers_id', array('' => '-- Select --') + (array)$providers,  $provider_id,['class'=>'select2 form-control js-sel-provider-change','id'=>'billingprovider']) !!}
					<p style="font-size: smaller; margin-bottom: 0px;" class="js-sel-provider-type-dis hide"></p>
					{!! $errors->first('providers_id', '<p> :message</p>')  !!}  
				</div>
				<div class="col-sm-1"></div>
			</div>


			<div class="controls" style="display:none;">     
				<input type="hidden" name="billing_provider" id="billing_provider"/>
			</div>  
			<?php
			if ($provider_id != '') {
				$provider_id_cobination = explode(';', $provider_id);
				$tax_id = $provider_id_cobination[1];
				$npi = $provider_id_cobination[2];
			} else {
				$tax_id = '';
				$npi = '';
			}
			?>
				   
			<div class="form-group">
			   {!! Form::label('tax_id', 'Tax ID', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
					 {!! Form::text('tax_id',$tax_id,['class'=>'form-control','name'=>'tax_id', 'readonly',  'tabindex'=>'-1']) !!}
				</div>
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group">
				{!! Form::label('npi', 'NPI', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('conversion_factor')) error @endif">
					{!! Form::text('npi',$npi,['class'=>'form-control','name'=>'npi', 'readonly',  'tabindex'=>'-1']) !!} 
				</div>
				<div class="col-sm-1"></div>
			</div>                                      
			<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
			{!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
			<div class="form-group">
				{!! Form::label('provider_id', 'Provider ID', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('provider_id')) error @endif">
					{!! Form::text('provider_id',null,['class'=>'form-control','name'=>'provider_id','maxlength'=>'15']) !!}
					{!! $errors->first('provider_id', '<p> :message</p>')  !!}
				</div>
				 <div class="col-sm-1 col-xs-1">
						
						<!--<a id="document_add_modal_link_provider_id" href="#document_add_modal" @if(strpos($currnet_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/insurance::overrides::'.$insurance->id.'/'.$overrides->id.'/provider_id')}}" @else data-url="{{url('api/adddocumentmodal/insurance::overrides::'.$insurance->id.'/0/provider_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>-->
						
				</div>
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group">
				{!! Form::label('id type', 'ID Type', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('id_qualifiers_id')) error @endif">
					{!! Form::select('id_qualifiers_id', array(''=>'Select')+(array)$id_qualifiers,  $id_qualifiers_id,['class'=>'form-control select2']) !!}  
					{!! $errors->first('id_qualifiers_id', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			{!! Form::hidden('insurance_id',$insurance->id,['class'=>'form-control','name'=>'insurance_id',  'tabindex'=>'-1']) !!} 

		</div><!-- /.box-body -->
		
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
			{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
			@if(strpos($currnet_page, 'edit') !== false)
			@if($checkpermission->check_url_permission('insurance/{insuranceid}/insuranceoverrides/delete/{id}') == 1)	
				<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure you want to delete?" href="{{ url('insurance/'.$insurance->id.'/insuranceoverrides/delete/'.$overrides->id) }}">Delete</a>
			@endif	
		
			   <a href="javascript:void(0)" data-url="{{ url('insurance/'.$insurance->id.'/insuranceoverrides/'.$overrides->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
				@endif
										
				  @if(strpos($currnet_page, 'edit') == false)
			   <a href="javascript:void(0)" data-url="{{ url('insurance/'.$insurance->id.'/insuranceoverrides/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>   
				  @endif
			</div>
	   
	</div><!-- /.box -->


</div><!--/.col (left) -->
@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function() {
	  $('#js-bootstrap-validator')
		.bootstrapValidator({
			message: 'This value is not valid',
			excluded: ':disabled',
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				
				facility_id:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("practice/practicemaster/insurance.validation.facility_id") }}'
						}
					}
				},
				insurances_id:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("common.validation.insurance_required") }}'
						}
					}
				},
				 providers_id:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("common.validation.provider_required") }}'
						}
					}
				},				
				provider_id:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("practice/practicemaster/insurance.validation.provider_id") }}'
						},
						regexp:{
								regexp: /^[0-9]{0,15}$/,
								message: '{{ trans("practice/practicemaster/insurance.validation.provider_regex") }}'
						}
					}
				},
				id_qualifiers_id:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("practice/practicemaster/insurance.validation.type_id") }}'
						}
					}
				},
			}
		});
	});

	function splitToUpdate(source, to1, to2, to3) {
		if (!source || !to1 || !to2 || !to3) {
			return false;
		}
		else {
			source = source.nodeType == 1 ? source : document.getElementById(source);
			to1 = to1.nodeType == 1 ? to1 : document.getElementById(to1);
			to2 = to2.nodeType == 1 ? to2 : document.getElementById(to2);
			to3 = to3.nodeType == 1 ? to3 : document.getElementById(to3);

			var selOpt = source.selectedIndex,
					vals = source.getElementsByTagName('option')[selOpt].value;
			if(vals != ''){
				to1.value = vals.split(';')[0];
				to2.value = vals.split(';')[1];
				to3.value = vals.split(';')[2];
			} else {
				to1.value = '';
				to2.value = '';
				to3.value = '';                   
			}
		}
	}

	var sel = document.getElementById('billingprovider'),
			opt1 = document.getElementById('billing_provider'),
			opt2 = document.getElementById('tax_id');
	opt3 = document.getElementById('npi');

	sel.onchange = function () {
		splitToUpdate('billingprovider', 'billing_provider', 'tax_id', 'npi');
	};
</script>
@endpush