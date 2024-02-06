{!! Form::hidden('temp_superbill_name',$superbill_name,['class'=>'form-control input-sm','id'=>'temp_superbill_name']) !!}
@foreach ($cpt_list_arr as $cpt_list)
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
	<ul class="cpt-grid no-padding line-height-26 margin-b-4" style="list-style-type:none;" id="">
		<li class="superbill">
			<table class="table-striped-view">
				<tbody>
					<tr>
						<td style="padding: 0px 4px; width: 5%;">
						@if (in_array($cpt_list['id'], $sel_cpts_vals))
							<?php $sel_opt_val  = true; ?>
						@else
							<?php $sel_opt_val  = null; ?>
						@endif
						<?php $unique_cpt_class_name = 'cpt_'.$cpt_list['cpt_hcpcs']; ?>
						{!! Form::checkbox('cpt_codes_seleted[]', $cpt_list['id'], $sel_opt_val, ['class'=>'chk flat-red','data-id'=>"$unique_cpt_class_name"]) !!}
						</td>                                                
						<td style="width: 82%">{!! $cpt_list['short_description'] !!}</td>
						<td style="width: 13%">{!! $cpt_list['cpt_hcpcs'] !!}</td>
					</tr>
				</tbody>
			</table>                                     
		</li>	
	</ul>
</div>
@endforeach

<script type="text/javascript">
	$(document).ready(function() {         
		$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});
	});
</script>