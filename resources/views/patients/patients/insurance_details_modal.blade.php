<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		@if(@$total_insurance>0)
			@foreach($insurances as $insurances_val)  
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-t-5 border-bottom-dotted  bg-insurance med-gray-dark">
                    <p class="med-green font600 no-bottom">
                        {!! Form::radio('js_modal_insurance_id', @$insurances_val['id'].'::'.@$insurances_val['insurancetype_id'], null,['id'=>@$insurances_val['insurance_name'],'class'=>'']) !!}
                        {!! Form::label($insurances_val['insurance_name'], $insurances_val['insurance_name'], ['class' => 'cur-pointer font600']) !!}
                        
                    </p>
                    <p class="no-bottom">{{ @$insurances_val['address_1'] }}, {{ @$insurances_val['city'] }}, {{ @$insurances_val['state'] }}, {{ @$insurances_val['zipcode5'] }} @if($insurances_val['zipcode4'] != '')- {{ $insurances_val['zipcode4'] }}@endif</p>
                    <p class="no-bottom"><span class="med-gray-dark font600">Payer ID : </span> {{ @$insurances_val['payerid'] }}</p>
                </div>
			@endforeach
		@else
            <p class="med-red no-bottom margin-l-5">No results Found</p>
		@endif
	</div>
</div>