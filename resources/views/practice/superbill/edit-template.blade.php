<div class="js_orgin">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 box-body">
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view margin-b-10">
				<i class="livicon" data-name="info"></i> <h3 class="box-title">Selected Header List</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive js_add_header">
				<input type="hidden" class="js_template_id" value="{{ $superbill_array->id }}" />
				@foreach ($superbill_array->get_list_order as $key => $get_list)
				<?php $header_list = explode(",",$superbill_array->order_header) ?>
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 js_common_header no-padding" id="js_{{ $get_list }}"data-index="{{ $get_list }}">                            
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="box box-view-border no-shadow yes-border med-border-color"><!--  Box Starts -->
                            <div class="box-header-view med-bg-green no-border-radius med-white ">
								<i class="livicon" data-name="info" data-color="#fff"></i> <h3 class="box-title med-white">{{ $header_list[$key] }}</h3>
								<div class="box-tools pull-right margin-t-m-3">
									<span class="btn btn-box-tool js_close_header js_session_confirm"><i data-original-title="Header delete" data-toggle="tooltip" data-placement="bottom" style="cursor: pointer;"class="fa {{Config::get('cssconfigs.common.close')}}"></i></span>
								</div>
							</div><!-- /.box-header -->
							<div class="box-body table-responsive">
								<div class="col-lg-12 no-padding">
									<div class="col-lg-12 no-padding" id="{{ $get_list }}">
									@foreach ($superbill_array->$get_list as $ind_key => $ind_val)
										<?php $sep_val = explode("::",$ind_val) ?>										
										<ul class="cpt-grid" style="list-style-type:none; padding:0px; line-height:26px; margin-bottom:4px;" id="">										
											<li class="superbill js_count">
												<table class="table-striped-view">
													<tbody>
														<tr>
															<td style="width: 1%"></td>
															<td style="width: 74%;font-size:11px;" class="js_checked_content_text_{{ $sep_val[0] }}">{{ $sep_val[1] }}</td>
															<td style="width: 15%" class="js_all_code js_checked_content_code_{{ $sep_val[0] }}" data-value="{{ $sep_val[1] }}">{{ $sep_val[0] }}</td>
															<td style="width: 15%; background-color: white;">
																<i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon remove_selected_icds" data-original-title="Delete" data-toggle="tooltip" data-placement="bottom" style="cursor: pointer;"></i>
															</td>
														</tr>
													</tbody>
												</table>
												<?php $value = $get_list."::".$sep_val[0]."::".$sep_val[1]."::".$header_list[$key]?>
												
												@if($get_list == "skin_procedures")
													<?php $value = $value."::".@$superbill_array->skin_procedures_units[$ind_key] ?>
												@endif
												@if($get_list == "medications")
													<?php $value = $value."::".@$superbill_array->medications_units[$ind_key] ?>
												@endif
												<input type="hidden" name="all_html_values[]" class="all_html_values" id="js_{{ $sep_val[0] }}" value="{{ $value }}">
											</li>
										</ul>
										@endforeach
										
									</div>
								</div>
							</div><!-- /.box-body -->
						</div><!-- /.box Ends-->
						<input type="hidden" class="js_div_empty_alert" value="1" />
					</div><!-- /.box Ends-->
				</div><!-- /.box Ends-->
				@endforeach
			</div>
		</div>
	</div>
	{!! Form::close() !!}
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
                <i class="fa {{Config::get('cssconfigs.common.search')}}"></i> <h3 class="box-title">CPT Search</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				{!! Form::open(['method'=>'POST','class'=>'search_form','name'=>'search_keyword']) !!}
				<div class="col-lg-8 col-lg-offset-2">
					<div class="input-group input-group-sm">
						<input name="search_keyword" type="text" class="form-control" placeholder="Search CPT using key words">
						<span class="input-group-btn">
							<button class="btn btn-flat btn-medgreen js_search" type="button">Search</button>
						</span>
					</div>
				</div>
				<div class="col-lg-12 col-md-12 col-sm-12 no-padding margin-t-10">
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2  js_add_section hide">
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-7 no-padding select2-white-popup @if($errors->first('provider_id')) error @endif">
							{!! Form::select('selected_list', array(''=>'-- Select --')+(array)$superbill_array->drop_down, NULL,['class'=>'form-control input-sm  select2','id'=>'js_drop_down']) !!}
						</div>						
						<button class="col-lg-2 col-md-2 col-sm-4 col-xs-4 margin-l-5 margin-t-0 btn-medcubics-small js_add" type="button">Attach</button>						
					</div>
				</div>                                                               
                                
				<div class="box-body col-lg-12 col-md-12 col-sm-12">
					<div id="js_loading_image" class="box-body overlay col-xs-offset-2 med-green font16 font600 hide">
						<i class="fa fa-spinner fa-spin med-green"></i>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 js_search_reslut"></div>
				</div>
				{!! Form::close() !!}
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div>
	<div class="col-lg-12 col-md-6 col-sm-6 col-xs-12 js_checked_content no-padding hide">
		<ul class="cpt-grid" style="list-style-type:none; padding:0px; line-height:26px; margin-bottom:4px;" id="">
			<li class="superbill js_count">
				<table class="table-striped-view">
					<tbody>
						<tr>
							<td style="width: 1%"></td>
							<td style="width: 74%;font-size:11px;" class="js_checked_content_text"></td>
							<td style="width: 15%" class="js_all_code js_checked_content_code"></td>
							<td style="width: 15%; background-color: white;">
								<i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon remove_selected_icds" data-original-title="Delete" data-toggle="tooltip" data-placement="bottom" style="cursor: pointer;" ></i>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" name="all_html_values[]" class="all_html_values" id="all_html_values" />
			</li>
		</ul>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center  js_genarate">		
		<button class="btn btn-medcubics" id='js_show_template'>Show template</button>
		<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure to delete the entry?" href="{{ url('superbills/template/delete/'.$superbill_array->id)}}">Delete</a>
		<a href="{{ url('superbills/'.$superbill_array->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
	</div>
</div>
{!! Form::open(['method'=>'POST','class'=>'all_values','name'=>'all_values']) !!}
<div class="all_values_input"></div>
{!! Form::close() !!}
<div class="js_prev_template hide"></div>
</div><!-- /.box Ends-->		