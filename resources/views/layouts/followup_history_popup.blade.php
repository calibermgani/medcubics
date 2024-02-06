<div class="box box-view no-shadow no-border"><!--  Box Starts -->
	<div class="box-body ar-full-notes form-horizontal" id="full-notes-details_{{$claim_detail_val[0]->claim_number}}">
		@if(count($claim_detail_val[0]->claim_notes_details)>0)	                                
			@include ('patients/armanagement/followup_details',['claim_detail_val_arr'=>$claim_detail_val[0]->claim_notes_details])
		@else
			No Followup History
		@endif
	</div>
</div><!-- /.box-body -->