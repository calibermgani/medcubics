<div class="box box-info no-shadow">
    <!-- form start -->
    <div class="box-body  form-horizontal">
        @if($practice->mail_add_1!='')
        <div class="form-group border-bottom-f0f0f0 p-b-8">
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                {!! Form::radio('collectaddress', '1','',['class'=>'js_get_address','data-id'=>'check_1','id'=>'check_1']) !!}<label for="check_1" class="no-bottom">&nbsp;</label>
            </div>	
            <div class="col-lg-7 col-md-6 col-sm-6">
                {!! Form::label('check_1', 'Mailing Address',['class'=>'med-green font600 form-cursor no-bottom']) !!} 
                <p class="no-bottom"><span class='address1'>{{ $practice->mail_add_1}}</span> @if($practice->mail_add_2!=''), <span class='address2'>{{ $practice->mail_add_2}}</span> @endif</p>					
                <p class="no-bottom"><span class="city">{{$practice->mail_city}}</span> - <span class="state">{{$practice->mail_state}}</span>, <span class="zip5">{{ $practice->mail_zip5}}</span> @if($practice->mail_zip4!='') - <span class="zip4">{{$practice->mail_zip4}}</span>@endif</p>
            </div>
        </div> 
        @endif

        @if($practice->pay_add_1!='')

        <div class="form-group border-bottom-f0f0f0 p-b-8">
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                {!! Form::radio('collectaddress', '1','',['class'=>'js_get_address','data-id'=>'check_2','id'=>'check_2']) !!}<label for="check_2" class="no-bottom">&nbsp;</label>
            </div>	
            <div class="col-lg-7 col-md-6 col-sm-6">
                {!! Form::label('check_2', 'Pay to Address',['class'=>'med-green font600 form-cursor no-bottom']) !!} 
                <p class="med-green font600 no-bottom"></p>   
                <p class="no-bottom"> <span class='address1'>{{ $practice->pay_add_1}}</span>@if($practice->pay_add_2!=''), <span class='address2'>{{ $practice->pay_add_2}}</span> @endif</p>

                <p class="no-bottom"><span class="city">{{$practice->pay_city}}</span> - <span class="state">{{$practice->pay_state}}</span>, <span class="zip5">{{ $practice->pay_zip5}}</span> @if($practice->pay_zip4!='') - <span class="zip4">{{$practice->pay_zip4}}</span>@endif</p>
            </div>
        </div>  
        @endif

        @if($practice->primary_add_1!='')
        <div class="form-group border-bottom-f0f0f0 p-b-8">
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                {!! Form::radio('collectaddress', '1','',['class'=>'js_get_address','data-id'=>'check_3','id'=>'check_3']) !!}<label for="check_3" class="no-bottom">&nbsp;</label>
            </div>	
            <div class="col-lg-7 col-md-6 col-sm-6">
                {!! Form::label('check_3', 'Primary Address',['class'=>'med-green font600 form-cursor no-bottom']) !!}					
                <p class="no-bottom"><span class='address1'>{{ $practice->primary_add_1}}</span> @if($practice->primary_add_2!=''), <span class='address2'>{{ $practice->primary_add_2}}</span> @endif</p>					
                <p class="no-bottom"><span class="city">{{$practice->primary_city}}</span> @if($practice->primary_state!='') - <span class="state">{{$practice->primary_state}}</span>,@endif <span class="zip5">{{ $practice->primary_zip5}}</span> @if($practice->primary_zip4!='') - <span class="zip4">{{$practice->primary_zip4}}</span>@endif</p>
            </div>
        </div> 
        @endif

        @if(count($facility)>0) 
        <?php $fac = 1; ?>
        @foreach($facility as $facilityaddress)
        <div class="form-group border-bottom-f0f0f0 p-b-8">
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                <?php $facility_name_for_label = str_replace(',', '_', $facilityaddress->facility_name); ?>
                {!! Form::radio('collectaddress', '1','',['class'=>'js_get_address','data-id'=>'facility_'.$fac,'id'=>$facility_name_for_label]) !!}<label for="{{$facility_name_for_label}}" class="no-bottom">&nbsp;</label>
            </div>	
            <div class="col-lg-7 col-md-6 col-sm-6">
                {!! Form::label($facility_name_for_label, $facilityaddress->facility_name,['class'=>'med-green font600 form-cursor no-bottom']) !!}

                <p class="no-bottom"><span class="address1">{{ $facilityaddress->facility_address->address1 }}</span> @if($facilityaddress->facility_address->address2!=''), <span class='address2'>{{ $facilityaddress->facility_address->address2 }}</span> @endif</p>

                <p class="no-bottom"><span class="city">{{$facilityaddress->facility_address->city}}</span> - <span class="state">{{$facilityaddress->facility_address->state}}</span>, <span class="zip5">{{ $facilityaddress->facility_address->pay_zip5 }}</span> @if($facilityaddress->facility_address->pay_zip4!='') - <span class="zip4">{{$facilityaddress->facility_address->pay_zip4}}</span>@endif</p>
            </div>
        </div> 
        <?php $fac++; ?>
        @endforeach
        @endif

        @if(count($provider)>0) 
        <?php $pro = 1; ?>
        @foreach($provider as $provideraddress)
        @if($provideraddress->address_1!='')
        <div class="form-group border-bottom-f0f0f0 p-b-8">
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                <?php $provider_name_for_label = str_replace(',', '_', @$provideraddress->provider_name) . '_' . @$provideraddress->degrees->degree_name . '_' . @$provideraddress->provider_types->name; ?>
                {!! Form::radio('collectaddress', '1','',['class'=>'js_get_address','data-id'=>'provider_'.$pro, 'id' => $provider_name_for_label]) !!}<label for="{{$provider_name_for_label}}" class="no-bottom">&nbsp;</label>

            </div>	
            <div class="col-lg-7 col-md-6 col-sm-6">
                <p class="med-green font600 no-bottom"></p>
                <p class="no-bottom"><span class='med-green font600'>{!! HTML::decode(Form::label($provider_name_for_label, @$provideraddress->provider_name.' '.@$provideraddress->degrees->degree_name.', <span style="color:#f07d08 !important;">'.@$provideraddress->provider_types->name.'</span>',['class'=>'med-green font600 form-cursor no-bottom'])) !!}   </p>   
                <p class="no-bottom"> <span class='address1'>{{ $provideraddress->address_1 }}</span>@if($provideraddress->address_2!=''), <span class='address2'>{{ $provideraddress->address_2 }}</span> @endif</p>
                <p class="no-bottom"><span class="city">{{$provideraddress->city}}</span> - <span class="state">{{$provideraddress->state}}</span>, <span class="zip5">{{ $provideraddress->zipcode5 }}</span> @if($provideraddress->zipcode4!='') - <span class="zip4">{{$provideraddress->zipcode4}}</span>@endif</p>
            </div>
        </div> 
        <?php $pro++; ?>
        @endif
        @endforeach
        @endif
    </div><!-- /.box-body -->
</div><!-- /.box -->