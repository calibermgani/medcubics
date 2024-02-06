<?php $sel_code_val_arr = explode(',',$sel_code_val); ?>
@foreach($denial_codes_arr as $keys=>$denial_codes_val)
<tr>
    <td class="padding-0-4">          
		<?php
			if (in_array(@$denial_codes_val->id, $sel_code_val_arr)) {
				$sel_opt_val  = 'checked';
			} else {
				$sel_opt_val  = '';
			}
		?>
        <div class="no-margin" aria-checked="false" aria-disabled="false">
            <input name="denial_codes[]" id="{{$keys}}denial" type="checkbox" value="{!! $denial_codes_val->id !!}" class="js_denial_codes js_denial_frm_denial_codes_{{@$denial_claim_number}}">
            <label for="{{$keys}}denial" class="margin-b-5">
                &nbsp;
            </label>
        </div>
    </td>                                                
    <td style="width: 7%;padding-left:0px;">{{@$denial_codes_val->transactioncode_id}}</td>
    <td style="width: 85%">{{@$denial_codes_val->description}}</td>
</tr>
@endforeach