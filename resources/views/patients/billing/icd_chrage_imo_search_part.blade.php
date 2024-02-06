<div class="col-lg-12 col-md-12 col-sm-12 space20 no-padding">
    <?php $temp_arr = array(); ?>
    @foreach ($imo_icd_list as $imo_icd_list_val)
        @if (!in_array($imo_icd_list_val['@attributes']['ICD10CM_CODE'], $temp_arr) && $imo_icd_list_val['@attributes']['ICD10CM_CODE']!='')
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <ul class="cpt-grid icd-charge-imo">
                <li class="superbill">
                    <table class="table-striped-view">
                        <tbody>
                        <tr>
                            <td class="padding-0-4 td-c-5">
                            <?php 
								$unique_icd_class_name = 'icd_'.str_replace('.', '_', $imo_icd_list_val['@attributes']['ICD10CM_CODE']);
								if (in_array($imo_icd_list_val['@attributes']['ICD10CM_CODE'], $sel_icds_arr)) {
									$sel_opt_val  = 'checked';
								} else { 
									$sel_opt_val  = '';
								}
							?>
                            <input {{$sel_opt_val}} data-id="{{$unique_icd_class_name}}" class="chk flat-red" name="imo_search_icds[]" type="radio" value="{!! $imo_icd_list_val['@attributes']['ICD10CM_CODE'] !!}::{!! $imo_icd_list_val['@attributes']['ICD10CM_TITLE'] !!}">
                            </td>                                                
                            <td class="td-c-82">{!! ucfirst(strtolower($imo_icd_list_val['@attributes']['ICD10CM_TITLE'])) !!}</td>
                            <td class="td-c-13">{!! $imo_icd_list_val['@attributes']['ICD10CM_CODE'] !!}</td>
                        </tr>
                        </tbody>
                    </table>                                     
                </li>	
            </ul>
        </div>
        <?php $temp_arr[] = $imo_icd_list_val['@attributes']['ICD10CM_CODE']; ?>
        @endif
    @endforeach
</div>
<script type="text/javascript">
$(document).ready(function () {
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass: 'iradio_flat-green'
    });
});
</script>