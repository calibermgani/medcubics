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
                        {!! Form::label('API For', 'API For', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('api_for')) error @endif">
                            {!! Form::text('api_for',null,['class'=>'form-control']) !!}
							{!! $errors->first('api_for', '<p> :message</p>')  !!}							
                        </div>                
                    </div>
                    <div class="form-group bottom-space-10">
                        {!! Form::label('API Name', 'API Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('api_name')) error @endif">
                            {!! Form::text('api_name',null,['class'=>'form-control']) !!}
                            {!! $errors->first('api_name', '<p> :message</p>')  !!}                         
                        </div>                
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('API Username', 'API Username', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('api_username')) error @endif">
                            {!! Form::text('api_username',null,['class'=>'form-control']) !!}
                            {!! $errors->first('api_username', '<p> :message</p>')  !!}                         
                        </div>                
                    </div>
                    <div class="form-group bottom-space-10">
                        {!! Form::label('API Password', 'API Password', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('api_password')) error @endif">
                            {!! Form::text('api_password',null,['class'=>'form-control']) !!}
                            {!! $errors->first('api_password', '<p> :message</p>')  !!}                         
                        </div>                
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('API Category', 'API Category', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('category')) error @endif">
                            {!! Form::text('category',null,['class'=>'form-control']) !!}
                            {!! $errors->first('category', '<p> :message</p>')  !!}                         
                        </div>                
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('API USPS', 'API USPS', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('usps_user_id')) error @endif">
                            {!! Form::text('usps_user_id',null,['class'=>'form-control']) !!}
                            {!! $errors->first('usps_user_id', '<p> :message</p>')  !!}                         
                        </div>                
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('API Token', 'API Token', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('token')) error @endif">
                            {!! Form::text('token',null,['class'=>'form-control']) !!}
                            {!! $errors->first('token', '<p> :message</p>')  !!}                         
                        </div>                
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('API Host', 'API Host', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('host')) error @endif">
                            {!! Form::text('host',null,['class'=>'form-control']) !!}
                            {!! $errors->first('host', '<p> :message</p>')  !!}                         
                        </div>                
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('API port', 'API port', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('port')) error @endif">
                            {!! Form::text('port',null,['class'=>'form-control']) !!}
                            {!! $errors->first('port', '<p> :message</p>')  !!}                         
                        </div>                
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('status', 'Status', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-8 col-md-7 col-sm-8 col-xs-12">  
                            {!! Form::radio('api_status', 'Active','true',['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('api_status', 'Inactive',null,['class'=>'flat-red']) !!} Inactive                                       
                        </div>                
                    </div>

                    <div class="form-group bottom-space-10">
                        {!! Form::label('API URL', 'API URL', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}  
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 @if($errors->first('url')) error @endif">
                            {!! Form::text('url',null,['class'=>'form-control']) !!}
                            {!! $errors->first('url', '<p> :message</p>')  !!}                         
                        </div>                
                    </div>

					<div class="col-lg-10 col-md-10  col-sm-12 col-xs-12 text-center">
                    {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
                    @if(strpos($currnet_page, 'edit') !== false)
						<?php $apiid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($apiconfig->id,'encode');  ?>
						@if($checkpermission->check_url_permission('admin/apiconfig/{id}/delete') == 1)
						<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure would you like to delete this API?" href="{{ url('admin/apiconfig/'.$apiid.'/delete') }}">Delete</a>
						@endif
						<a href="javascript:void(0)" data-url="{{ url('admin/apiconfig')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                    @else
                    <a href="javascript:void(0)" data-url="{{ url('admin/apiconfig') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
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
                api_for: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Enter API Details'
                        },
                    }
                }
            }
        }); 
    });

</script>
@endpush