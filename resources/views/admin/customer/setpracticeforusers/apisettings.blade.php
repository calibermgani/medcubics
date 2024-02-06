@if($practice->api_ids != Null and $practice->api_ids != '')
<div class="box box-info no-shadow">
		<div class="box-block-header with-border">
			<h3 class="box-title"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="address-book" data-color='#008e97' data-size='16'></i> API Settings</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->

		<div class="box-body form-horizontal">
		<div class=" js-address-class" id="js-address-general-address">
			<div class="form-group">
				{!! Form::label('Select API', 'Select API', ['class'=>'col-lg-1 col-md-1 col-sm-2 col-xs-12 control-label']) !!}
				<div class="col-lg-25 col-md-25 col-sm-25 col-xs-19 @if($errors->first('role_id')) error @endif">
					<?php $selectedpracticeapis = explode(",",$practice->api_ids)  ?>
					<?php $selecteduserapis 	 = explode(",",$Setapiforusers->api)  ?>
					@foreach($apilist as $api_key=>$api_value)
						  @if(in_array($api_key,$selectedpracticeapis))
					  
							@if(@$apilist_subcat[$api_value]) 
							<span class='margin-l-4 med-green'>{{ ucwords($api_value) }}</span>	
								
							<span data-id="js_{{$api_value}}_{{$api_key}}" >(
								@foreach($apilist_subcat[$api_value] as $sub_api_key=>$sub_api_value)
									@if(in_array($sub_api_key,$getActivePracticeAPI))
										<span>{!! Form::checkbox('apilist[]', $sub_api_key,(in_array($sub_api_key,$selecteduserapis))?true:false, ["class" => "flat-red js-subselect", 'id' =>'apilist']) !!}<span class='margin-l-4 med-green'>{{ @$api_name[$sub_api_key] }}</span></span>
									@endif
								@endforeach
								)
							</span>
							@else
								@if(in_array($api_key,$getActivePracticeAPI))
									<span class="ad-checkbox-color">{!! Form::checkbox('apilist[]', $api_key,(in_array($api_key,$selecteduserapis))?true:false, ["class" => "flat-red js-subselect", 'id' =>'apilist']) !!}<span class='margin-l-4'>{{ @$api_name[$api_key] }}</span></span>
								@endif
							@endif
						  @endif
					@endforeach
				</div>
				<div class="col-sm-1 col-xs-2"></div>
			</div>

		   </div><!-- /.box-body -->
		</div><!-- /.box -->
	</div><!--/.col (left) -->
@endif	