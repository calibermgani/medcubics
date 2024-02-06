<input type="hidden" name="valid_npi_bootstrap" value="" />
<div class="col-md-12 space10"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->   
            <div class="box box-info no-shadow"><!-- General Info Box Starts -->
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
				  <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                <div class="box-body form-horizontal">
                    <div class="form-group bottom-space-10">
                        {!! Form::label('API Name', 'API Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
 						
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('api_name')) error @endif">
                            {!! Form::text('api_name',null,['class'=>'form-control']) !!}
							{!! $errors->first('api_name', '<p> :message</p>')  !!}							
                        </div>                
                    </div>   

                    <div class="form-group bottom-space-10">
                        {!! Form::label('status', 'Status', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-8 col-md-7 col-sm-8 col-xs-12">  
                            {!! Form::radio('status', 'Active','true',['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red']) !!} Inactive                                       
                        </div>                
                    </div>
					<div class="col-lg-10 col-md-10  col-sm-12 col-xs-12 text-center">
                    {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
                    @if(strpos($currnet_page, 'edit') !== false)
						<?php $apiid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($apilist->id,'encode');  ?>
						@if($checkpermission->check_url_permission('admin/apilist/{id}/delete') == 1)
						<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure would you like to delete this API?" href="{{ url('admin/apilist/delete/'.$apiid) }}">Delete</a>
						@endif
						<a href="javascript:void(0)" data-url="{{ url('admin/apilist/'.$apiid)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                    @else
                    <a href="javascript:void(0)" data-url="{{ url('admin/apilist') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                    @endif
                </div>	

                </div><!-- /.box-body -->
            </div><!-- General info box Ends-->
        </div><!--  Left side Content Ends -->   
    </div><!--Background color for Inner Content Starts -->
</div><!-- Inner Content for full width Starts -->
		
@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {

        $('#js-bootstrap-validator').bootstrapValidator({
		
			message : '',
			excluded : ':disabled',
			feedbackIcons : {
				valid : 'glyphicon glyphicon-ok',
				invalid : 'glyphicon glyphicon-remove',
				validating : 'glyphicon glyphicon-refresh'
			},
            fields: {
                api_name: {
					 message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("admin/apilist.validation.api_name") }}'
                        },
                    }
                }
            }
        });
    });

</script>


@endpush










