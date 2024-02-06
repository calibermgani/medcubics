<div class="js-address-class" id="{{$div_name}}">
    <?php $address_flag['general'] = App\Models\AddressFlag::getAddressFlag($af_type,$af_type_id,$af_sub_type); ?>
    {!! Form::hidden('general_address1[]',@$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
    {!! Form::hidden('general_city[]',@$address_flag['general']['city'],['class'=>'js-address-city']) !!}
    {!! Form::hidden('general_state[]',@$address_flag['general']['state'],['class'=>'js-address-state']) !!}
    {!! Form::hidden('general_zip5[]',@$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
    {!! Form::hidden('general_zip4[]',@$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
    {!! Form::hidden('general_is_address_match[]',@$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
    {!! Form::hidden('general_error_message[]',@$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}
    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match'],'show'); ?>   
    <?php echo $value; ?>        
</div>