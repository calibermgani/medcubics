@if($cpt_list != "")
	@foreach ($cpt_list as $list_val)
		<?php $value = @$list_val->cpt_hcpcs."::".@$list_val->short_description; ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<ul class="cpt-grid line-height-26 no-padding" style="list-style-type:none;">
				<li>
					<table class="table-striped-view">
						<tbody>
							<tr>
								<td style="padding: 0px 4px; width: 5%;">
									{!! Form::checkbox('search_cpts[]', @$value, null, ['class'=>'chk flat-red','data-value'=>@$list_val->cpt_hcpcs]) !!}
								</td>                                                
								<td style="width: 82%;text-align:left !important;">{{ @$list_val->short_description }}</td>
								<td style="width: 13%">{{ @$list_val->cpt_hcpcs }}</td>
							</tr>
							<tr>
								<td colspan="3" style="color:red !important;font-size:11px;" id="js_alert_{{@$list_val->cpt_hcpcs}}" class="js_alert_msg hide">{{ trans("practice/practicemaster/superbill.validation.code_unique") }}</td>
							</tr>
						</tbody>
					</table> 
				</li>
			</ul>
		</div>
	@endforeach
@else
	1
@endif