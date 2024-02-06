<span style="display:none;">
    {{ $segment = Request::segment(3) }} 
</span>

<input type="text" name="providers_id" value="{{ Request::segment(2) }}" style="display:none;"/>


<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
			<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
			{!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
            <div class="box box-info no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <!-- form start -->

                <div class="box-body  form-horizontal">
                    
             <div class="form-group">
				{!! Form::label('provider_override', 'Provider', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
				<div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('provider_override_id')) error @endif">
					{!! Form::select('provider_override', array(''=>'-- Select --')+(array)$provider_override,  $provider_override_id,['class'=>'select2 form-control js-sel-provider-change','id'=>'provider_override']) !!}  
					<p style="font-size: smaller; margin-bottom: 0px;" class="js-sel-provider-type-dis hide"></p>
					{!! $errors->first('provider_override_id', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
	<?php 
		if($provider_override_id != '')
		{
			$provider_id_combination = explode(';',$provider_override_id);
				$provider_override_id_split = $provider_id_combination[0];
			$tax_id = $provider_id_combination[1];
			$npi = $provider_id_combination[2];
		} else {
				$provider_override_id_split = '';
			$tax_id = '';
			$npi = '';
		}
	?>
<div class="controls" style="display: none;">
    <input type="hidden" name="provider_override_id" id="provider_override_id" value="{{$provider_override_id_split}}"/>
</div>


                    <div class="form-group">
                        {!! Form::label('tax_id', 'Tax ID', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            {!! Form::text('tax_id',$tax_id,['class'=>'form-control','name'=>'tax_id', 'readonly',  'tabindex'=>'-1']) !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('npi', 'NPI', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            {!! Form::text('npi',$npi,['class'=>'form-control','name'=>'npi', 'readonly',  'tabindex'=>'-1']) !!} 
                        </div>
                        <div class="col-sm-1"></div>
                    </div>                                      

                              
                    <div class="form-group">
                        {!! Form::label('provider_id', 'Provider ID', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('provider_id')) error @endif">
                            {!! Form::text('provider_id',null,['class'=>'form-control','name'=>'provider_id','maxlength'=>'15']) !!}
                            {!! $errors->first('provider_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1">
						
						<a id="document_add_modal_link_provider_id" href="#document_add_modal" @if(strpos($currnet_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/provider::overrides::'.$provider->id.'/'.$overrides->id.'/provider_id')}}" @else data-url="{{url('api/adddocumentmodal/provider::overrides::'.$provider->id.'/0/provider_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('id type', 'ID Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('id_qualifiers_id')) error @endif">
                            {!! Form::select('id_qualifiers_id', array(''=>'-- Select --')+(array)$id_qualifiers,  $id_qualifiers_id,['class'=>'form-control select2','id'=>'id_qualifiers_id']) !!}  
                            {!! $errors->first('id_qualifiers_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                </div><!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-lg-6 col-md-9 col-sm-10 col-xs-12">
                        {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
                        @if(strpos($currnet_page, 'edit') !== false)
                        @if($checkpermission->check_url_permission('provider/{providerid}/provideroverrides/delete/{id}') == 1)
                        <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete this provider overrides?" href="{{ url('provider/'.$provider->id.'/provideroverrides/'.$overrides->id.'/delete') }}">Delete</a></center>
						@endif
                    
                        <a href="javascript:void(0)" data-url="{{ url('provider/'.$provider->id.'/provideroverrides/'.$overrides->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                        @endif
                        
                        @if(strpos($currnet_page, 'edit') == false)
                            <a href="javascript:void(0)" data-url="{{ url('provider/'.$provider->id.'/provideroverrides/') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                        @endif
                    </div>
                </div><!-- /.box-footer -->

            </div><!-- /.box -->


        </div><!--/.col (left) -->
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->        


@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function () {
		$('#js-bootstrap-validator')
            .find('[name="provider_override"]')               
                .change(function(e) {
                    $('#js-bootstrap-validator')
                        .data('bootstrapValidator')
                        .updateStatus('provider_override', 'NOT_VALIDATED')
                        .validateField('provider_override');
                })
                .end()  

			.bootstrapValidator({
					message: 'This value is not valid',
					excluded: ':disabled',
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
					fields: {
						 provider_override:{
							message:'Provider field is invalid',
							validators:{
								notEmpty:{
									message: 'Provider field is required and can\'t be empty'
									},
							}
						},
						
						provider_id: {
							message: '',
							validators: {
								notEmpty: {
									message: 'This field is required and can\'t be empty'
								},
								regexp:{
											regexp: /^[0-9]{0,15}$/,
											message: 'Provider ID field can contain only 15 digits'
                                      }
							}
						},
						id_qualifiers_id: {
							message: '',
							validators: {
								notEmpty: {
									message: 'This field is required and can\'t be empty'
								},
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

        var sel = document.getElementById('provider_override'),
                opt1 = document.getElementById('provider_override_id'),
                opt2 = document.getElementById('tax_id');
        opt3 = document.getElementById('npi');

        sel.onchange = function () {
            splitToUpdate('provider_override', 'provider_override_id', 'tax_id', 'npi');
        };
    </script>
@endpush