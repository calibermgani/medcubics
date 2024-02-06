<div class="col-lg-12 col-md-12 col-sm-12 space20">
	<?php $temp_arr = array(); ?>
	@foreach ($imo_icd_list as $imo_icd_list_val)
	@if (!in_array($imo_icd_list_val['ICD10CM_CODE'], $temp_arr) && $imo_icd_list_val['ICD10CM_CODE']!='')
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
	<ul class="cpt-grid no-padding line-height-26 no-bottom" style="list-style-type:none;" id="">
		<li class="superbill">
			<table class="table-striped-view">
				<tbody>
					<tr>
						<td style="padding: 0px 4px; width: 5%;">
						<?php $unique_icd_class_name = 'icd_'.str_replace('.', '_', $imo_icd_list_val['ICD10CM_CODE']); ?>
						@if (in_array($imo_icd_list_val['ICD10CM_CODE'], $sel_icds_arr))
							<?php $sel_opt_val  = 'checked'; ?>
						@else
							<?php $sel_opt_val  = ''; ?>
						@endif
						<input {{$sel_opt_val}} data-id="{{$unique_icd_class_name}}" class="chk flat-red" name="imo_search_icds[]" type="checkbox" value="{!! $imo_icd_list_val['ICD10CM_CODE'] !!}::{!! $imo_icd_list_val['ICD10CM_TITLE'] !!}">
						</td>                                                
						<td style="width: 82%">{!! $imo_icd_list_val['ICD10CM_TITLE'] !!}</td>
						<td style="width: 13%">{!! $imo_icd_list_val['ICD10CM_CODE'] !!}</td>
					</tr>
				</tbody>
			</table>                                     
		</li>	
	</ul>
	</div>
	<?php $temp_arr[] = $imo_icd_list_val['ICD10CM_CODE']; ?>
	@endif
	@endforeach
</div>

<script type="text/javascript">
	$(document).ready(function() {         
		$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});
	});
</script>