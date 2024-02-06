@if(count($array_of_time) > 0)
<div class="form-group js-available_time_slot_div margin-r-05 yes-border border-b4f7f7 p-l-0 p-r-0">  
	<?php $seleted_avail_hidden="no"; ?>
	@foreach($array_of_time[0] as $available_time_slot)
		<?php
			$available_time_slot_arr = explode("-",$available_time_slot);
			$available_time_slot_start 	= strtotime($available_time_slot_arr[0]);
			$available_time_slot_end 	= strtotime($available_time_slot_arr[1]);
			
			if($user_selected_slot_time!='' && $user_selected_slot_time>=$available_time_slot_start && $user_selected_slot_time<=$available_time_slot_end) {
				$seleted_avail="clsSelectedSlot";
				$seleted_avail_hidden="yes";
			} elseif($user_selected_slot_time=='00000') {
				$seleted_avail="";
				$seleted_avail_hidden="yes";
			} else {
				$seleted_avail="";
			}
		?>
		<div class="clsslot @if(!in_array($available_time_slot, $user_already_selected_timeslot)) clsSelectSlot js-available_slot js-available_time_selection {{$seleted_avail}} @else js-not_avail_slot clsNotSelectSlot @endif" data-value='{{$available_time_slot}}'>{{$available_time_slot}}</div>
	@endforeach
	
	{!! Form::hidden('seleted_avail_hidden',$seleted_avail_hidden,['id'=>'seleted_avail_hidden']) !!} 
</div>
@endif