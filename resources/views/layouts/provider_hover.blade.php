@if($provider)
    <div class="on-hover-content js-tooltip_{{hash('sha256',@$provider->id)}}" style="display:none;">
        <span class="med-orange font600">{{ @$provider->provider_name." ".@$provider->degrees->degree_name }}</span> 
        <p class="no-bottom hover-color">
            <span class="font600">Type :</span> 
			@if(isset($provider->type_name))
				{{ $provider->type_name }}
			@else
				{{ @$provider->provider_types->name }} 
			@endif<br>
            @if(@$provider->provider_dob !='' && @$provider->provider_dob !='1970-01-01' && @$provider->provider_dob !='0000-00-00') 
                <span class="font600">DOB :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$provider->provider_dob,'dob') }} <br>
            @endif
          {{--  @if(@$provider->gender !='')<span class="font600">Gender :</span> {{ @$provider->gender }}<br> @endif--}}
            <span class="font600">ETIN Type :</span> {{(@$provider->etin_type != '')?@$provider->etin_type:'-- Nil --'}} {{(@$provider->etin_type_number != '')?'(#'.@$provider->etin_type_number.")":''}}  <br>
            <span class="font600">NPI :</span> {{(@$provider->npi != '')?@$provider->npi:'-- Nil --'}}
        </p>
    </div>
@endif
