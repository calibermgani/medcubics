{!! Form::open(['method'=>'POST','class'=>'submit_template','name'=>'submit_template']) !!}
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 box-body">
	<div class="box box-view no-shadow"><!--  Box Starts -->
		<div class="box-header-view">
			<i class="livicon" data-name="info"></i> <h3 class="box-title">Template</h3>			
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<p class="margin-t-10"><span class="med-orange">Alert :- </span><span class="med-green">We can move/swap each box  in inside the layout.</span></p>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 droppable box-body table-responsive no-padding margin-t-10" id="columns-almostFinal" style="border:1px solid #85E2E6;padding:50px 10px !important;">
			<?php $count = 0; ?>
			@foreach ($result as $res_first_key => $res_first_val)
			<?php $key = explode("::",@$res_first_val[0]) ?>
			<div class="draggable col-lg-4 col-md-4 col-sm-6 col-xs-12 no-padding" style="{{ @$header_style[$count] }}">    
				<ul class="checkbox-grid" style="list-style-type:none; padding:0px; line-height:26px; border: 1px solid #00877f;margin-bottom:0px !important;">
					<li class="superbill">
						<table class="table-striped-view" style="width: 100%;">
							<thead>
								<tr>
									<th style="width: 75%" data-value="{{ $res_first_key }}" class="js_header_order">{{ @$key[0] }}</th>
									<th style="text-align: center; width: 12%">New</th>
									@if(@$key[0] == "Skin Procedures" || @$key[0] == "Medications")
									<th style="text-align: center; width: 13%">Units</th> 
									@endif
								</tr>
							</thead>
						</table>                                     
					</li>
					@foreach ($res_first_val as $res_second_key => $res_second_val)
					<?php $value = explode("::",$res_second_val) ?>
					@if($res_first_key =="skin_procedures" || $res_first_key == "medications")
						@if(isset($value[3]))
							<?php $hidden_value = $value[3]; ?>
						@else
							<?php $hidden_value = ''; ?>
						@endif
					@else
						<?php $hidden_value = ''; ?>
					@endif
						
					<li class="superbill">
						<table class="table-striped-views">
							<tbody>
								<tr>
									<td style="width: 75%;font-size: 11px;">&emsp;{{ $value[2] }}</td>
									<td style="width: 12%;font-size: 11px;text-align: center !important;">{{ $value[1] }}</td>
									@if($value[0] == "Skin Procedures" || $value[0] == "Medications")
										<td style="text-align: center; width: 13%">
											<div class="form-group">
											{!! Form::text($res_first_key.'_units[]', $hidden_value,['class'=>'billing-noborder anesthesia_unit dm-unit','autocomplete'=>'off']) !!}
											</div>
										</td> 
									@endif								
								</tr>
							</tbody>
						</table>                                    
					</li>
					<input type="hidden" name="{{ $res_first_key }}[]" value="{{ $value[1] }}" />
					@endforeach	
				</ul><!-- /.box Ends-->
			</div><!-- /.box Ends-->	
			<?php $count ++; ?>		
			@endforeach
		</div>
	</div>
</div>
<div class="js_get_all_need_values"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
	<button class="btn btn-medcubics" id='js_submit_template'>Save Template</button>
	<a href="{{ url('superbills')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
</div>
{!! Form::close() !!}