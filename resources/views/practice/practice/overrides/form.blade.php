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

                <div class="box-body form-horizontal">
                    <div class="form-group">
                        {!! Form::label('billing_provider', 'Provider', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-7 col-xs-10 @if($errors->first('providers_id')) error @endif">
                            {!! Form::select('providers_id', array(''=>'-- Select --')+(array)$providers,  $provider_id,['class'=>'form-control select2 js-sel-provider-change','id'=>'billingprovider']) !!}  
							<p style="font-size: smaller; margin-bottom: 0px;" class="js-sel-provider-type-dis hide"></p>
                            {!! $errors->first('providers_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="controls" style="display: none;">
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
                        {!! Form::label('tax_id', 'Tax ID', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-7 col-xs-10">
                            {!! Form::text('tax_id',$tax_id,['class'=>'form-control','name'=>'tax_id', 'readonly',  'tabindex'=>'-1']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('npi', 'NPI', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-7 col-xs-10 @if($errors->first('conversion_factor')) error @endif">
                            {!! Form::text('npi',$npi,['class'=>'form-control','name'=>'npi', 'readonly',  'tabindex'=>'-1']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>                                      

                    <div class="form-group">
                        {!! Form::label('provider_id', 'Provider ID', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-6 col-sm-7 col-xs-10 @if($errors->first('provider_id')) error @endif">
                            {!! Form::text('provider_id',null,['class'=>'form-control','name'=>'provider_id','maxlength'=>'15']) !!}
                            {!! $errors->first('provider_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
						
						<a id="document_add_modal_link_provider_id" href="#document_add_modal" @if(strpos($currnet_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/practice::overrides::'.$practice->id.'/'.$overrides->id.'/provider_id')}}" @else data-url="{{url('api/adddocumentmodal/practice::overrides::'.$practice->id.'/0/provider_id')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
						
						</div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('id type', 'ID Type', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-3 col-md-6 col-sm-7 col-xs-10 @if($errors->first('id_qualifiers_id')) error @endif">
                            {!! Form::select('id_qualifiers_id', array(''=>'Select')+(array)$id_qualifiers,  $id_qualifiers_id,['class'=>'form-control select2']) !!}  
                            {!! $errors->first('id_qualifiers_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                </div><!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
                        {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
                        
                        @if(strpos($currnet_page, 'edit') !== false)

                        <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete this practice overrides?"
                           href="{{ url('overrides/delete/'.$overrides->id) }}">Delete</a>
                        @endif
                        <a href="javascript:void(0)" data-url="{{ url('overrides')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
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
			.bootstrapValidator({
				message: 'This value is not valid',
				excluded: ':disabled',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					providers_id: {
						message: 'Provider field is invalid',
						validators: {
							notEmpty: {
								message: 'Provider field is required and can\'t be empty'
							}
						}
					},
					provider_id: {
						message: 'Provider ID field is invalid',
						validators: {
							notEmpty: {
								message: 'Provider ID field is required and can\'t be empty'
							},
							regexp: {
								regexp: /^[a-zA-Z0-9]{0,15}$/,
								message: 'Provider ID field can contain only 15 digits'
							}
						}
					},
					id_qualifiers_id: {
						message: 'ID qualifiers field is invalid',
						validators: {
							notEmpty: {
								message: 'ID qualifiers field is required and can\'t be empty'
							}
						}
					},
				}
			});
    });
</script>
@endpush