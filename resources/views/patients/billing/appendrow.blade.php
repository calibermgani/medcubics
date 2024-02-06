<?php  $modifier_readonly = "readonly";?>
  <li id = "js-modifier-list-{{$i}}" class="billing-grid js_line_validate js-validate-lineitem js-disable-div-{{$i}}">
    <table class="table-billing-view superbill-claim">
        <tbody>
            <tr>
                <td class="td-c-4" tabindex="0"><a href="javascript:void(0);" class="js-chargelineitem-delete" data-id= "<?php echo $i ?>"><i class= "fa {{Config::get('cssconfigs.common.delete')}}"></i></a> <input tabindex = -1 type="checkbox" id="<?php echo $i; ?>" class="js-icd-highlight"><label for="{{$i}}" class="no-bottom">&nbsp;</label>
                    <i class="fa fa-plus med-green form-cursor js-showhide-box24 textboxrow" data-placement="top" data-toggle="tooltip" data-original-title="Shaded Area"  id="<?php echo $i; ?>"></i>	
                </td>  
                <td class="td-c-6"><input type="text" data-postition="left_first_row" class="js_validate_date from_dos dm-date billing-noborder js_from_date textboxrow" name=<?php echo "dos_from[" . $i . "]"; ?>   value = "{{@$date_from}}"   onchange="datevalidation(<?php echo $i; ?>)"></td>                                             
                <td class="td-c-6"><input type="text" value = "" class="textboxrow js_validate_date dm-date to_dos billing-noborder js_to_date" name=<?php echo "dos_to[" . $i . "]"; ?>  onchange="todatevalidation(<?php echo $i; ?>)"></td>
                <td class="td-c-8"><input type="text" id="<?php echo $i; ?>" readonly="readonly" class="textboxrow js-cpt billing-noborder" tabindex = -1 value = "" name= <?php echo "cpt[" . $i . "]"; ?> >
                 <span id="cpt-<?php echo $i; ?>" class="cpt-hover" style="display:none;"></span></td>               
                <td class="td-c-4"><input type="hidden" class="billing-noborder cpt_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->charge}}" name="<?php echo "cpt_amt[" . $i . "]"; ?>">
				{!! Form::text('modifier1['.$i.']',@$claims->dosdetails[$i]->modifier1,['class'=>'form-control textboxrow billing-noborder modifier_open js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier1-'.$i , $modifier_readonly]) !!}</td>
                <td class="td-c-4">{!! Form::text('modifier2['.$i.']' ,@$claims->dosdetails[$i]->modifier2,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier2-'.$i, $modifier_readonly]) !!}</td>
                <td class="td-c-4">{!! Form::text('modifier3['.$i.']' ,@$claims->dosdetails[$i]->modifier3,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier3-'.$i , $modifier_readonly]) !!}</td>
                <td class="td-c-4">{!! Form::text('modifier4['.$i.']' ,@$claims->dosdetails[$i]->modifier4,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier4-'.$i, $modifier_readonly]) !!}</td>                  
                @for($j=1;$j<=12;$j++)
                <td class="td-c-2"> 
                <input type="text" class="textboxrow icd_pointer billing-icd-pointers" tabindex = -1 readonly="readonly" name=<?php echo 'icd' . $j . '_' . $i; ?> value = "" id="<?php echo 'icd' . $j . '_' . $i; ?>">
                </td>
                @endfor             
                </td>
                <td class="td-c-3"><input class="textboxrow cpt_unit billing-noborder" value= "" maxlength = 5 type="text" id="<?php echo $i ?>" name=<?php echo "unit[" . $i . "]"; ?> ></td>
                <td class="td-c-6">
					<input type="text" data-postition="right_last_row" autocomplete="off" class = "text-right textboxrow js-charge js_charge_amt billing-noborder allownumericwithdecimal" id= "charge_<?php echo $i ?>" name=<?php echo "charge[" . $i . "]"; ?> value="">                
					<input type="hidden" class="cpt_icd_map billing-nb" value = "" name=<?php echo "cpt_icd_map[" . $i . "]"; ?>  onChange="modelvalue()"></td>
					<input type="hidden" class="cpt_allowed_amt_<?php echo $i; ?>" value = "" name="<?php echo "cpt_allowed[" . $i . "]"; ?>">
					<input type="hidden" class="cpt_icd_map_key billing-nb" value = "" name=<?php echo "cpt_icd_map_key[" . $i . "]"; ?> >
				</td>
					<input name= <?php echo "copay_Transcation_ID[" . $i . "]"; ?> value = "{{@$claim_cpt_TxId}}" type="hidden" tabindex = -1>
				<td class="td-c-6">
					<input type="text" data-postition="right_last_row" autocomplete="off" class = "copay_applied text-right textboxrow form-control input-sm-header-billing billing-noborder allownumericwithdecimal" name=<?php echo "copay_applied[" . $i . "]"; ?>>
				</td>
			 </tr>
        </tbody>
    </table>
	<div id="js_box_24_{{$i}}" style="display: none;">
		<input type="text" maxlength= '61' class = "text-right textboxrow form-control input-sm-header-billing box_24_atoj" name=<?php echo "box_24_AToG[]"; ?> id="<?php echo $i; ?>">
	</div>
</li> 