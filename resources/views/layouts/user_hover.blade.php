@if($user)
	<div class="js-tooltip_{{hash('sha256',@$user->id)}}" style="display:none;">
		<span class="med-orange font600">{{ucfirst(@$user->name)}}</span> 
		<p class="no-bottom hover-color">
			<span class="font600 med-green">User type :</span> {{ @$user->user_type }}<br>
			<span class="font600 med-green">DOB :</span> 
			@if(@$user->dob !='' && @$user->dob !='1970-01-01' && @$user->dob !='0000-00-00')
				{{ App\Http\Helpers\Helpers::dateFormat(@$user->dob,'dob') }}
			@else
				-- Nil --
			@endif
			<br>
			<span class="font600 med-green">Gender :</span> {{ @$user->gender }}<br>			
		</p>
	</div>
@endif