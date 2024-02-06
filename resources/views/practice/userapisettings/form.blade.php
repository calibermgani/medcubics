<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.userapisettings") }}' />
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" ><!-- Col Starts -->
    <div class="box box-info no-shadow"><!-- Box General Information Starts -->
        <div class="box-block-header with-border">
            <h3 class="box-title"> <i class="fa {{Config::get('cssconfigs.Practicesmaster.userapisettings')}} i-font-tabs"></i> User API Settings</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->

        @if(count($practiceApiList)>0)
        <div class="box-body margin-l-10 margin-t-10 form-horizontal"><!-- Box Body Starts -->
            <div class="form-group">
                {!! Form::label('Select User','Select User', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                <div class="col-lg-3 col-md-6 col-sm-6">
                    {!! Form::select('userlist', array('' => '-- Select --') + (array)$userlist_arr,  null,['class'=>'form-control select2 js_get_user_api']) !!}
                </div>								
            </div> 
            <div class="form-group">
                {!! Form::label('API List','API List', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 no-padding">
                    @foreach($practiceApiList as $api_value) 
                    @if(in_array(@$apilist_arr[$api_value->api_id]['api_name'],$maincat_api))
                    {!! Form::checkbox('practice_api[]', $api_value->api_id,'', ["class" => "","id"=>$api_value->api_id]) !!}&nbsp;
                    {!! Form::label($api_value->api_id,ucwords(@$apilist_arr[$api_value->api_id]['category']),ucwords(str_replace(@$apilist_arr[$api_value->api_id]['api_name'],' ',str_replace('_',' ',@$apilist_arr[$api_value->api_id]['category'])))) !!}&emsp;&emsp;
                    @else
                    {!! Form::checkbox('practice_api[]', $api_value->api_id,'', ["class" => "","id"=>$api_value->api_id]) !!}&nbsp;
                    {!! Form::label($api_value->api_id,ucwords(@$apilist_arr[$api_value->api_id]['category']),ucwords(str_replace('_',' ',@$apilist_arr[$api_value->api_id]['category'])), ['class'=>'control-label med-darkgray font600']) !!}&emsp;&emsp;
                    @endif		
                    @endforeach	
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                {!! Form::submit('Update', ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
                <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
            </div>
        </div>

        @else
        <div class="box-body  form-horizontal"><!-- Box Body Starts -->
            <p class="med-gray text-center">No API settings available</p>
        </div>
        @endif

    </div><!-- Box General Information Ends -->
</div><!--/.col ends -->


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
					userlist: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/userapi.validation.user") }}'
							}
						}
					}
				}
			});
    });
</script>
@endpush