<div class="col-lg-12 col-md-12 col-sm-12 no-padding   modal-icd-scroll-500 space20">
	<?php $temp_arr = array(); ?>
	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <ul class="cpt-grid no-padding line-height-26 no-bottom" style="list-style-type:none;">
                <li class="superbill">
                    <table class="table-striped-view">
                        <tbody>
                            @if(!empty($imo_icd_list))	
                            @foreach ($imo_icd_list as $key => $imo_icd_list_val)
                            @if (!in_array($imo_icd_list_val['@attributes']['ICD10CM_CODE'], $temp_arr) && $imo_icd_list_val['@attributes']['ICD10CM_CODE']!='')
                            <tr>
                                <td style="padding: 0px 4px; width: 85%;">
                                    <?php $unique_icd_class_name = 'icd_'.str_replace('.', '_', $imo_icd_list_val['@attributes']['ICD10CM_CODE']); ?>
                                    @if (in_array($imo_icd_list_val['@attributes']['ICD10CM_CODE'], $sel_icds_arr))
                                    <?php $sel_opt_val  = 'checked'; ?>
                                    @else
                                    <?php $sel_opt_val  = ''; ?>
                                    @endif

                                    <input {{$sel_opt_val}} data-id="{{$unique_icd_class_name}}" class="chk" name="imo_search_icds[]" type="checkbox" value="{!! $imo_icd_list_val['@attributes']['ICD10CM_CODE'] !!}::{!! $imo_icd_list_val['@attributes']['ICD10CM_TITLE'] !!}" id="ICD{{$key}}"><label class="no-bottom med-darkgray" for="ICD{{$key}}">{!! ucfirst(strtolower($imo_icd_list_val['@attributes']['ICD10CM_TITLE'])) !!}</label>
                                </td>                                                

                                <td style="width: 13%;line-height: 22px;min-width: 50px;padding-left: 5px;">{!! $imo_icd_list_val['@attributes']['ICD10CM_CODE'] !!}</td>
                            </tr>
                            <?php $temp_arr[] = $imo_icd_list_val['@attributes']['ICD10CM_CODE']; ?>
                            @endif
                            @endforeach
                            @else
                        <p class="no-bottom">No ICD available with searched criteria</p>
                        @endif
                        </tbody>
                    </table>                                     
                </li>	
            </ul>
	</div>
	
</div>

<script type="text/javascript">
/*	$(document).ready(function() {         
		$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});
	}); */
</script>