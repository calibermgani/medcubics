<div class="js-tooltip_{{hash('sha256',@$list->id)}}" style="display:none;">
    <span class="med-orange font600">Cheque Claim Count</span> 
	<?php if($list->claim_nos != '' && $list->claim_nos != 'null' && !empty($list->claim_nos)) { $claimsCount = count(json_decode(@$list->claim_nos,true)); } else { $claimsCount = 0;  } ?>
    <p class="no-bottom hover-color"><span class="font600">Total Claim Count :</span> {{ @$claimsCount }} </p>
</div>