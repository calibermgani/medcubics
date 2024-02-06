<input type="hidden" name="customer_id" value="{{ Request::segment(3) }}" />

<input type="hidden" name="valid_npi_bootstrap" value="" />

<span style="display:none;">
    {{ $segment = Request::segment(3) }}
</span>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.customer_practice") }}' />        
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-20"><!--  Left side Content Starts -->
    <div class="box  no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Business Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10">                   
            <div class="form-group">
                {!! Form::label('DoingBusinessAs', 'Doing Business As', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('doing_business_s')) error @endif">
					{!! Form::text('doing_business_s',null,['class'=>'form-control ','name'=>'doing_business_s']) !!}
                    {!! $errors->first('doing_business_s', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"> </div>
            </div>

            <div class="form-group">
                {!! Form::label('Specialty', 'Specialty', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('speciality_id')) error @endif">
                    {!! Form::select('speciality_id', array(''=>'-- Select --')+(array)$specialities,  $speciality_id,['class'=>'select2 form-control','id'=>'js-speciality-change']) !!}
                    {!! $errors->first('speciality_id', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Taxonomy', 'Taxonomy',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('taxanomy_id')) error @endif">
                    {!! Form::select('taxanomy_id', array(''=>'-- Select --')+(array)$taxanomies, $taxanomy_id, ['id' => 'taxanomies-list','class'=>'select2 form-control']) !!}
                    {!! $errors->first('taxanomy_id', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
           
            <div class="form-group">
                {!! Form::label('billing_entity', 'Billing Entity', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 @if($errors->first('billing_entity')) error @endif">
                    {!! Form::radio('billing_entity', 'Yes',null,['class'=>'','id'=>'b_entity_y']) !!} {!! Form::label('b_entity_y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    {!! Form::radio('billing_entity', 'No',true,['class'=>'','id'=>'b_entity_n']) !!} {!! Form::label('b_entity_n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                    {!! $errors->first('billing_entity', '<p> :message</p>')  !!}
                </div>                        
            </div> 
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 @if($errors->first('status')) error @endif">                           
                    {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'s_active']) !!} {!! Form::label('s_active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'s_inactive']) !!} {!! Form::label('s_inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
                    {!! $errors->first('status', '<p> :message</p>')  !!}
                </div>                        
            </div>
            <div class="form-group margin-b-5">
                {!! Form::label('timezone', 'Time Zone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('timezone')) error @endif">                    
                    <select name="timezone" id="timezone" class="form-control select2">
                        <option value="">--Select--</option>
                        <option {{ 'Africa/Abidjan' == @$practice->timezone ? ' selected' : '' }} value="Africa/Abidjan">(GMT +00:00), Africa/Abidjan</option>
                        <option {{ 'Africa/Accra' == @$practice->timezone ? ' selected' : '' }} value="Africa/Accra">(GMT +00:00), Africa/Accra</option>
                        <option {{ 'Africa/Addis' == @$practice->timezone ? ' selected' : '' }} value="Africa/Addis">(GMT +03:00), Africa/Addis</option>
                        <option {{ 'Africa/Algiers' == @$practice->timezone ? ' selected' : '' }} value="Africa/Algiers">(GMT +01:00), Africa/Algiers</option>
                        <option {{ 'Africa/Asmara' == @$practice->timezone ? ' selected' : '' }} value="Africa/Asmara">(GMT +03:00), Africa/Asmara</option>
                        <option {{ 'Africa/Bamako' == @$practice->timezone ? ' selected' : '' }} value="Africa/Bamako">(GMT +00:00), Africa/Bamako</option>
                        <option {{ 'Africa/Bangui' == @$practice->timezone ? ' selected' : '' }} value="Africa/Bangui">(GMT +01:00), Africa/Bangui</option>
                        <option {{ 'Africa/Banjul' == @$practice->timezone ? ' selected' : '' }} value="Africa/Banjul">(GMT +00:00), Africa/Banjul</option>
                        <option {{ 'Africa/Bissau' == @$practice->timezone ? ' selected' : '' }} value="Africa/Bissau">(GMT +00:00), Africa/Bissau</option>
                        <option {{ 'Africa/Blantyre' == @$practice->timezone ? ' selected' : '' }} value="Africa/Blantyre">(GMT +02:00), Africa/Blantyre</option>
                        <option {{ 'Africa/Brazzaville' == @$practice->timezone ? ' selected' : '' }} value="Africa/Brazzaville">(GMT +01:00), Africa/Brazzaville</option>
                        <option {{ 'Africa/Bujumbura' == @$practice->timezone ? ' selected' : '' }} value="Africa/Bujumbura">(GMT +02:00), Africa/Bujumbura</option>
                        <option {{ 'Africa/Cairo' == @$practice->timezone ? ' selected' : '' }} value="Africa/Cairo">(GMT +02:00), Africa/Cairo</option>
                        <option {{ 'Africa/Casablanca' == @$practice->timezone ? ' selected' : '' }} value="Africa/Casablanca">(GMT +01:00), Africa/Casablanca</option>
                        <option {{ 'Africa/Ceuta' == @$practice->timezone ? ' selected' : '' }} value="Africa/Ceuta">(GMT +02:00), Africa/Ceuta</option>
                        <option {{ 'Africa/Conakry' == @$practice->timezone ? ' selected' : '' }} value="Africa/Conakry">(GMT +00:00), Africa/Conakry</option>
                        <option {{ 'Africa/Dakar' == @$practice->timezone ? ' selected' : '' }} value="Africa/Dakar">(GMT +00:00), Africa/Dakar</option>
                        <option {{ 'Africa/Dar' == @$practice->timezone ? ' selected' : '' }} value="Africa/Dar">(GMT +03:00), Africa/Dar</option>
                        <option {{ 'Africa/Djibouti' == @$practice->timezone ? ' selected' : '' }} value="Africa/Djibouti">(GMT +03:00), Africa/Djibouti</option>
                        <option {{ 'Africa/Douala' == @$practice->timezone ? ' selected' : '' }} value="Africa/Douala">(GMT +01:00), Africa/Douala</option>
                        <option {{ 'Africa/El' == @$practice->timezone ? ' selected' : '' }} value="Africa/El">(GMT +01:00), Africa/El</option>
                        <option {{ 'Africa/Freetown' == @$practice->timezone ? ' selected' : '' }} value="Africa/Freetown">(GMT +00:00), Africa/Freetown</option>
                        <option {{ 'Africa/Gaborone' == @$practice->timezone ? ' selected' : '' }} value="Africa/Gaborone">(GMT +02:00), Africa/Gaborone</option>
                        <option {{ 'Africa/Harare' == @$practice->timezone ? ' selected' : '' }} value="Africa/Harare">(GMT +02:00), Africa/Harare</option>
                        <option {{ 'Africa/Johannesburg' == @$practice->timezone ? ' selected' : '' }} value="Africa/Johannesburg">(GMT +02:00), Africa/Johannesburg</option>
                        <option {{ 'Africa/Juba' == @$practice->timezone ? ' selected' : '' }} value="Africa/Juba">(GMT +03:00), Africa/Juba</option>
                        <option {{ 'Africa/Kampala' == @$practice->timezone ? ' selected' : '' }} value="Africa/Kampala">(GMT +03:00), Africa/Kampala</option>
                        <option {{ 'Africa/Khartoum' == @$practice->timezone ? ' selected' : '' }} value="Africa/Khartoum">(GMT +02:00), Africa/Khartoum</option>
                        <option {{ 'Africa/Kigali' == @$practice->timezone ? ' selected' : '' }} value="Africa/Kigali">(GMT +02:00), Africa/Kigali</option>
                        <option {{ 'Africa/Kinshasa' == @$practice->timezone ? ' selected' : '' }} value="Africa/Kinshasa">(GMT +01:00), Africa/Kinshasa</option>
                        <option {{ 'Africa/Lagos' == @$practice->timezone ? ' selected' : '' }} value="Africa/Lagos">(GMT +01:00), Africa/Lagos</option>
                        <option {{ 'Africa/Libreville' == @$practice->timezone ? ' selected' : '' }} value="Africa/Libreville">(GMT +01:00), Africa/Libreville</option>
                        <option {{ 'Africa/Lome' == @$practice->timezone ? ' selected' : '' }} value="Africa/Lome">(GMT +00:00), Africa/Lome</option>
                        <option {{ 'Africa/Luanda' == @$practice->timezone ? ' selected' : '' }} value="Africa/Luanda">(GMT +01:00), Africa/Luanda</option>
                        <option {{ 'Africa/Lubumbashi' == @$practice->timezone ? ' selected' : '' }} value="Africa/Lubumbashi">(GMT +02:00), Africa/Lubumbashi</option>
                        <option {{ 'Africa/Lusaka' == @$practice->timezone ? ' selected' : '' }} value="Africa/Lusaka">(GMT +02:00), Africa/Lusaka</option>
                        <option {{ 'Africa/Malabo' == @$practice->timezone ? ' selected' : '' }} value="Africa/Malabo">(GMT +01:00), Africa/Malabo</option>
                        <option {{ 'Africa/Maputo' == @$practice->timezone ? ' selected' : '' }} value="Africa/Maputo">(GMT +02:00), Africa/Maputo</option>
                        <option {{ 'Africa/Maseru' == @$practice->timezone ? ' selected' : '' }} value="Africa/Maseru">(GMT +02:00), Africa/Maseru</option>
                        <option {{ 'Africa/Mbabane' == @$practice->timezone ? ' selected' : '' }} value="Africa/Mbabane">(GMT +02:00), Africa/Mbabane</option>
                        <option {{ 'Africa/Mogadishu' == @$practice->timezone ? ' selected' : '' }} value="Africa/Mogadishu">(GMT +03:00), Africa/Mogadishu</option>
                        <option {{ 'Africa/Monrovia' == @$practice->timezone ? ' selected' : '' }} value="Africa/Monrovia">(GMT +00:00), Africa/Monrovia</option>
                        <option {{ 'Africa/Nairobi' == @$practice->timezone ? ' selected' : '' }} value="Africa/Nairobi">(GMT +03:00), Africa/Nairobi</option>
                        <option {{ 'Africa/Ndjamena' == @$practice->timezone ? ' selected' : '' }} value="Africa/Ndjamena">(GMT +01:00), Africa/Ndjamena</option>
                        <option {{ 'Africa/Niamey' == @$practice->timezone ? ' selected' : '' }} value="Africa/Niamey">(GMT +01:00), Africa/Niamey</option>
                        <option {{ 'Africa/Nouakchott' == @$practice->timezone ? ' selected' : '' }} value="Africa/Nouakchott">(GMT +00:00), Africa/Nouakchott</option>
                        <option {{ 'Africa/Ouagadougou' == @$practice->timezone ? ' selected' : '' }} value="Africa/Ouagadougou">(GMT +00:00), Africa/Ouagadougou</option>
                        <option {{ 'Africa/Porto' == @$practice->timezone ? ' selected' : '' }} value="Africa/Porto">(GMT +01:00), Africa/Porto</option>
                        <option {{ 'Africa/Sao' == @$practice->timezone ? ' selected' : '' }} value="Africa/Sao">(GMT +00:00), Africa/Sao</option>
                        <option {{ 'Africa/Tripoli' == @$practice->timezone ? ' selected' : '' }} value="Africa/Tripoli">(GMT +02:00), Africa/Tripoli</option>
                        <option {{ 'Africa/Tunis' == @$practice->timezone ? ' selected' : '' }} value="Africa/Tunis">(GMT +01:00), Africa/Tunis</option>
                        <option {{ 'Africa/Windhoek' == @$practice->timezone ? ' selected' : '' }} value="Africa/Windhoek">(GMT +02:00), Africa/Windhoek</option>
                        <option {{ 'America/Adak' == @$practice->timezone ? ' selected' : '' }} value="America/Adak">(GMT -09:00), America/Adak</option>
                        <option {{ 'America/Anchorage' == @$practice->timezone ? ' selected' : '' }} value="America/Anchorage">(GMT -08:00), America/Anchorage</option>
                        <option {{ 'America/Anguilla' == @$practice->timezone ? ' selected' : '' }} value="America/Anguilla">(GMT -04:00), America/Anguilla</option>
                        <option {{ 'America/Antigua' == @$practice->timezone ? ' selected' : '' }} value="America/Antigua">(GMT -04:00), America/Antigua</option>
                        <option {{ 'America/Araguaina' == @$practice->timezone ? ' selected' : '' }} value="America/Araguaina">(GMT -03:00), America/Araguaina</option>
                        <option {{ 'America/Argentina/Buenos_Aires' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/Buenos_Aires">(GMT -03:00), America/Argentina/Buenos_Aires</option>
                        <option {{ 'America/Argentina/Catamarca' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/Catamarca">(GMT -03:00), America/Argentina/Catamarca</option>
                        <option {{ 'America/Argentina/Cordoba' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/Cordoba">(GMT -03:00), America/Argentina/Cordoba</option>
                        <option {{ 'America/Argentina/Jujuy' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/Jujuy">(GMT -03:00), America/Argentina/Jujuy</option>
                        <option {{ 'America/Argentina/La_Rioja' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/La_Rioja">(GMT -03:00), America/Argentina/La_Rioja</option>
                        <option {{ 'America/Argentina/Mendoza' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/Mendoza">(GMT -03:00), America/Argentina/Mendoza</option>
                        <option {{ 'America/Argentina/Rio_Gallegos' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/Rio_Gallegos">(GMT -03:00), America/Argentina/Rio_Gallegos</option>
                        <option {{ 'America/Argentina/Salta' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/Salta">(GMT -03:00), America/Argentina/Salta</option>
                        <option {{ 'America/Argentina/San_Juan' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/San_Juan">(GMT -03:00), America/Argentina/San_Juan</option>
                        <option {{ 'America/Argentina/San_Luis' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/San_Luis">(GMT -03:00), America/Argentina/San_Luis</option>
                        <option {{ 'America/Argentina/Tucuman' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/Tucuman">(GMT -03:00), America/Argentina/Tucuman</option>
                        <option {{ 'America/Argentina/Ushuaia' == @$practice->timezone ? ' selected' : '' }} value="America/Argentina/Ushuaia">(GMT -03:00), America/Argentina/Ushuaia</option>
                        <option {{ 'America/Aruba' == @$practice->timezone ? ' selected' : '' }} value="America/Aruba">(GMT -04:00), America/Aruba</option>
                        <option {{ 'America/Asuncion' == @$practice->timezone ? ' selected' : '' }} value="America/Asuncion">(GMT -04:00), America/Asuncion</option>
                        <option {{ 'America/Atikokan' == @$practice->timezone ? ' selected' : '' }} value="America/Atikokan">(GMT -05:00), America/Atikokan</option>
                        <option {{ 'America/Bahia' == @$practice->timezone ? ' selected' : '' }} value="America/Bahia">(GMT -03:00), America/Bahia</option>
                        <option {{ 'America/Bahia_Banderas' == @$practice->timezone ? ' selected' : '' }} value="America/Bahia_Banderas">(GMT -05:00), America/Bahia_Banderas</option>
                        <option {{ 'America/Barbados' == @$practice->timezone ? ' selected' : '' }} value="America/Barbados">(GMT -04:00), America/Barbados</option>
                        <option {{ 'America/Belem' == @$practice->timezone ? ' selected' : '' }} value="America/Belem">(GMT -03:00), America/Belem</option>
                        <option {{ 'America/Belize' == @$practice->timezone ? ' selected' : '' }} value="America/Belize">(GMT -06:00), America/Belize</option>
                        <option {{ 'America/Blanc-Sablon' == @$practice->timezone ? ' selected' : '' }} value="America/Blanc-Sablon">(GMT -04:00), America/Blanc-Sablon</option>
                        <option {{ 'America/Boa_Vista' == @$practice->timezone ? ' selected' : '' }} value="America/Boa_Vista">(GMT -04:00), America/Boa_Vista</option>
                        <option {{ 'America/Bogota' == @$practice->timezone ? ' selected' : '' }} value="America/Bogota">(GMT -05:00), America/Bogota</option>
                        <option {{ 'America/Boise' == @$practice->timezone ? ' selected' : '' }} value="America/Boise">(GMT -06:00), America/Boise</option>
                        <option {{ 'America/Cambridge_Bay' == @$practice->timezone ? ' selected' : '' }} value="America/Cambridge_Bay">(GMT -06:00), America/Cambridge_Bay</option>
                        <option {{ 'America/Campo_Grande' == @$practice->timezone ? ' selected' : '' }} value="America/Campo_Grande">(GMT -04:00), America/Campo_Grande</option>
                        <option {{ 'America/Cancun' == @$practice->timezone ? ' selected' : '' }} value="America/Cancun">(GMT -05:00), America/Cancun</option>
                        <option {{ 'America/Caracas' == @$practice->timezone ? ' selected' : '' }} value="America/Caracas">(GMT -04:00), America/Caracas</option>
                        <option {{ 'America/Cayenne' == @$practice->timezone ? ' selected' : '' }} value="America/Cayenne">(GMT -03:00), America/Cayenne</option>
                        <option {{ 'America/Cayman' == @$practice->timezone ? ' selected' : '' }} value="America/Cayman">(GMT -05:00), America/Cayman</option>
                        <option {{ 'America/Chicago' == @$practice->timezone ? ' selected' : '' }} value="America/Chicago">(GMT -05:00), America/Chicago</option>
                        <option {{ 'America/Chihuahua' == @$practice->timezone ? ' selected' : '' }} value="America/Chihuahua">(GMT -06:00), America/Chihuahua</option>
                        <option {{ 'America/Costa' == @$practice->timezone ? ' selected' : '' }} value="America/Costa">(GMT -06:00), America/Costa</option>
                        <option {{ 'America/Creston' == @$practice->timezone ? ' selected' : '' }} value="America/Creston">(GMT -07:00), America/Creston</option>
                        <option {{ 'America/Cuiaba' == @$practice->timezone ? ' selected' : '' }} value="America/Cuiaba">(GMT -04:00), America/Cuiaba</option>
                        <option {{ 'America/Curacao' == @$practice->timezone ? ' selected' : '' }} value="America/Curacao">(GMT -04:00), America/Curacao</option>
                        <option {{ 'America/Danmarkshavn' == @$practice->timezone ? ' selected' : '' }} value="America/Danmarkshavn">(GMT +00:00), America/Danmarkshavn</option>
                        <option {{ 'America/Dawson' == @$practice->timezone ? ' selected' : '' }} value="America/Dawson">(GMT -07:00), America/Dawson</option>
                        <option {{ 'America/Dawson_Creek' == @$practice->timezone ? ' selected' : '' }} value="America/Dawson_Creek">(GMT -07:00), America/Dawson_Creek</option>
                        <option {{ 'America/Denver' == @$practice->timezone ? ' selected' : '' }} value="America/Denver">(GMT -06:00), America/Denver</option>
                        <option {{ 'America/Detroit' == @$practice->timezone ? ' selected' : '' }} value="America/Detroit">(GMT -04:00), America/Detroit</option>
                        <option {{ 'America/Dominica' == @$practice->timezone ? ' selected' : '' }} value="America/Dominica">(GMT -04:00), America/Dominica</option>
                        <option {{ 'America/Edmonton' == @$practice->timezone ? ' selected' : '' }} value="America/Edmonton">(GMT -06:00), America/Edmonton</option>
                        <option {{ 'America/Eirunepe' == @$practice->timezone ? ' selected' : '' }} value="America/Eirunepe">(GMT -05:00), America/Eirunepe</option>
                        <option {{ 'America/El_Salvador' == @$practice->timezone ? ' selected' : '' }} value="America/El_Salvador">(GMT -06:00), America/El_Salvador</option>
                        <option {{ 'America/Fort_Nelson' == @$practice->timezone ? ' selected' : '' }} value="America/Fort_Nelson">(GMT -07:00), America/Fort_Nelson</option>
                        <option {{ 'America/Fortaleza' == @$practice->timezone ? ' selected' : '' }} value="America/Fortaleza">(GMT -03:00), America/Fortaleza</option>
                        <option {{ 'America/Glace_Bay' == @$practice->timezone ? ' selected' : '' }} value="America/Glace_Bay">(GMT -03:00), America/Glace_Bay</option>
                        <option {{ 'America/Godthab' == @$practice->timezone ? ' selected' : '' }} value="America/Godthab">(GMT -02:00), America/Godthab</option>
                        <option {{ 'America/Goose_Bay' == @$practice->timezone ? ' selected' : '' }} value="America/Goose_Bay">(GMT -03:00), America/Goose_Bay</option>
                        <option {{ 'America/Grand_Turk' == @$practice->timezone ? ' selected' : '' }} value="America/Grand_Turk">(GMT -04:00), America/Grand_Turk</option>
                        <option {{ 'America/Grenada' == @$practice->timezone ? ' selected' : '' }} value="America/Grenada">(GMT -04:00), America/Grenada</option>
                        <option {{ 'America/Guadeloupe' == @$practice->timezone ? ' selected' : '' }} value="America/Guadeloupe">(GMT -04:00), America/Guadeloupe</option>
                        <option {{ 'America/Guatemala' == @$practice->timezone ? ' selected' : '' }} value="America/Guatemala">(GMT -06:00), America/Guatemala</option>
                        <option {{ 'America/Guayaquil' == @$practice->timezone ? ' selected' : '' }} value="America/Guayaquil">(GMT -05:00), America/Guayaquil</option>
                        <option {{ 'America/Guyana' == @$practice->timezone ? ' selected' : '' }} value="America/Guyana">(GMT -04:00), America/Guyana</option>
                        <option {{ 'America/Halifax' == @$practice->timezone ? ' selected' : '' }} value="America/Halifax">(GMT -03:00), America/Halifax</option>
                        <option {{ 'America/Havana' == @$practice->timezone ? ' selected' : '' }} value="America/Havana">(GMT -04:00), America/Havana</option>
                        <option {{ 'America/Hermosillo' == @$practice->timezone ? ' selected' : '' }} value="America/Hermosillo">(GMT -07:00), America/Hermosillo</option>
                        <option {{ 'America/Indiana/Indianapolis' == @$practice->timezone ? ' selected' : '' }} value="America/Indiana/Indianapolis">(GMT -04:00), America/Indiana/Indianapolis</option>
                        <option {{ 'America/Indiana/Knox' == @$practice->timezone ? ' selected' : '' }} value="America/Indiana/Knox">(GMT -05:00), America/Indiana/Knox</option>
                        <option {{ 'America/Indiana/Marengo' == @$practice->timezone ? ' selected' : '' }} value="America/Indiana/Marengo">(GMT -04:00), America/Indiana/Marengo</option>
                        <option {{ 'America/Indiana/Petersburg' == @$practice->timezone ? ' selected' : '' }} value="America/Indiana/Petersburg">(GMT -04:00), America/Indiana/Petersburg</option>
                        <option {{ 'America/Indiana/Tell_City' == @$practice->timezone ? ' selected' : '' }} value="America/Indiana/Tell_City">(GMT -05:00), America/Indiana/Tell_City</option>
                        <option {{ 'America/Indiana/Vevay' == @$practice->timezone ? ' selected' : '' }} value="America/Indiana/Vevay">(GMT -04:00), America/Indiana/Vevay</option>
                        <option {{ 'America/Indiana/Vincennes' == @$practice->timezone ? ' selected' : '' }} value="America/Indiana/Vincennes">(GMT -04:00), America/Indiana/Vincennes</option>
                        <option {{ 'America/Indiana/Winamac' == @$practice->timezone ? ' selected' : '' }} value="America/Indiana/Winamac">(GMT -04:00), America/Indiana/Winamac</option>
                        <option {{ 'America/Inuvik' == @$practice->timezone ? ' selected' : '' }} value="America/Inuvik">(GMT -06:00), America/Inuvik</option>
                        <option {{ 'America/Iqaluit' == @$practice->timezone ? ' selected' : '' }} value="America/Iqaluit">(GMT -04:00), America/Iqaluit</option>
                        <option {{ 'America/Jamaica' == @$practice->timezone ? ' selected' : '' }} value="America/Jamaica">(GMT -05:00), America/Jamaica</option>
                        <option {{ 'America/Juneau' == @$practice->timezone ? ' selected' : '' }} value="America/Juneau">(GMT -08:00), America/Juneau</option>
                        <option {{ 'America/Kentucky/Louisville' == @$practice->timezone ? ' selected' : '' }} value="America/Kentucky/Louisville">(GMT -04:00), America/Kentucky/Louisville</option>
                        <option {{ 'America/Kentucky/Monticello' == @$practice->timezone ? ' selected' : '' }} value="America/Kentucky/Monticello">(GMT -04:00), America/Kentucky/Monticello</option>
                        <option {{ 'America/Kralendijk' == @$practice->timezone ? ' selected' : '' }} value="America/Kralendijk">(GMT -04:00), America/Kralendijk</option>
                        <option {{ 'America/La_Paz' == @$practice->timezone ? ' selected' : '' }} value="America/La_Paz">(GMT -04:00), America/La_Paz</option>
                        <option {{ 'America/Lima' == @$practice->timezone ? ' selected' : '' }} value="America/Lima">(GMT -05:00), America/Lima</option>
                        <option {{ 'America/Los_Angeles' == @$practice->timezone ? ' selected' : '' }} value="America/Los_Angeles">(GMT -07:00), America/Los_Angeles</option>
                        <option {{ 'America/Lower_Princes' == @$practice->timezone ? ' selected' : '' }} value="America/Lower_Princes">(GMT -04:00), America/Lower_Princes</option>
                        <option {{ 'America/Maceio' == @$practice->timezone ? ' selected' : '' }} value="America/Maceio">(GMT -03:00), America/Maceio</option>
                        <option {{ 'America/Managua' == @$practice->timezone ? ' selected' : '' }} value="America/Managua">(GMT -06:00), America/Managua</option>
                        <option {{ 'America/Manaus' == @$practice->timezone ? ' selected' : '' }} value="America/Manaus">(GMT -04:00), America/Manaus</option>
                        <option {{ 'America/Marigot' == @$practice->timezone ? ' selected' : '' }} value="America/Marigot">(GMT -04:00), America/Marigot</option>
                        <option {{ 'America/Martinique' == @$practice->timezone ? ' selected' : '' }} value="America/Martinique">(GMT -04:00), America/Martinique</option>
                        <option {{ 'America/Matamoros' == @$practice->timezone ? ' selected' : '' }} value="America/Matamoros">(GMT -05:00), America/Matamoros</option>
                        <option {{ 'America/Mazatlan' == @$practice->timezone ? ' selected' : '' }} value="America/Mazatlan">(GMT -06:00), America/Mazatlan</option>
                        <option {{ 'America/Menominee' == @$practice->timezone ? ' selected' : '' }} value="America/Menominee">(GMT -05:00), America/Menominee</option>
                        <option {{ 'America/Merida' == @$practice->timezone ? ' selected' : '' }} value="America/Merida">(GMT -05:00), America/Merida</option>
                        <option {{ 'America/Metlakatla' == @$practice->timezone ? ' selected' : '' }} value="America/Metlakatla">(GMT -08:00), America/Metlakatla</option>
                        <option {{ 'America/Mexico_City' == @$practice->timezone ? ' selected' : '' }} value="America/Mexico_City">(GMT -05:00), America/Mexico_City</option>
                        <option {{ 'America/Miquelon' == @$practice->timezone ? ' selected' : '' }} value="America/Miquelon">(GMT -02:00), America/Miquelon</option>
                        <option {{ 'America/Moncton' == @$practice->timezone ? ' selected' : '' }} value="America/Moncton">(GMT -03:00), America/Moncton</option>
                        <option {{ 'America/Monterrey' == @$practice->timezone ? ' selected' : '' }} value="America/Monterrey">(GMT -05:00), America/Monterrey</option>
                        <option {{ 'America/Montevideo' == @$practice->timezone ? ' selected' : '' }} value="America/Montevideo">(GMT -03:00), America/Montevideo</option>
                        <option {{ 'America/Montserrat' == @$practice->timezone ? ' selected' : '' }} value="America/Montserrat">(GMT -04:00), America/Montserrat</option>
                        <option {{ 'America/Nassau' == @$practice->timezone ? ' selected' : '' }} value="America/Nassau">(GMT -04:00), America/Nassau</option>
                        <option {{ 'America/New_York' == @$practice->timezone ? ' selected' : '' }} value="America/New_York">(GMT -04:00), America/New_York</option>
                        <option {{ 'America/Nipigon' == @$practice->timezone ? ' selected' : '' }} value="America/Nipigon">(GMT -04:00), America/Nipigon</option>
                        <option {{ 'America/Nome' == @$practice->timezone ? ' selected' : '' }} value="America/Nome">(GMT -08:00), America/Nome</option>
                        <option {{ 'America/Noronha' == @$practice->timezone ? ' selected' : '' }} value="America/Noronha">(GMT -02:00), America/Noronha</option>
                        <option {{ 'America/North_Dakota/Beulah' == @$practice->timezone ? ' selected' : '' }} value="America/North_Dakota/Beulah">(GMT -05:00), America/North_Dakota/Beulah</option>
                        <option {{ 'America/North_Dakota/Center' == @$practice->timezone ? ' selected' : '' }} value="America/North_Dakota/Center">(GMT -05:00), America/North_Dakota/Center</option>
                        <option {{ 'America/North_Dakota/New_Salem' == @$practice->timezone ? ' selected' : '' }} value="America/North_Dakota/New_Salem">(GMT -05:00), America/North_Dakota/New_Salem</option>
                        <option {{ 'America/Ojinaga' == @$practice->timezone ? ' selected' : '' }} value="America/Ojinaga">(GMT -06:00), America/Ojinaga</option>
                        <option {{ 'America/Panama' == @$practice->timezone ? ' selected' : '' }} value="America/Panama">(GMT -05:00), America/Panama</option>
                        <option {{ 'America/Pangnirtung' == @$practice->timezone ? ' selected' : '' }} value="America/Pangnirtung">(GMT -04:00), America/Pangnirtung</option>
                        <option {{ 'America/Paramaribo' == @$practice->timezone ? ' selected' : '' }} value="America/Paramaribo">(GMT -03:00), America/Paramaribo</option>
                        <option {{ 'America/Phoenix' == @$practice->timezone ? ' selected' : '' }} value="America/Phoenix">(GMT -07:00), America/Phoenix</option>
                        <option {{ 'America/Port-au-Prince' == @$practice->timezone ? ' selected' : '' }} value="America/Port-au-Prince">(GMT -04:00), America/Port-au-Prince</option>
                        <option {{ 'America/Port_of_Spain' == @$practice->timezone ? ' selected' : '' }} value="America/Port_of_Spain">(GMT -04:00), America/Port_of_Spain</option>
                        <option {{ 'America/Porto_Velho' == @$practice->timezone ? ' selected' : '' }} value="America/Porto_Velho">(GMT -04:00), America/Porto_Velho</option>
                        <option {{ 'America/Puerto_Rico' == @$practice->timezone ? ' selected' : '' }} value="America/Puerto_Rico">(GMT -04:00), America/Puerto_Rico</option>
                        <option {{ 'America/Punta_Arenas' == @$practice->timezone ? ' selected' : '' }} value="America/Punta_Arenas">(GMT -03:00), America/Punta_Arenas</option>
                        <option {{ 'America/Rainy_River' == @$practice->timezone ? ' selected' : '' }} value="America/Rainy_River">(GMT -05:00), America/Rainy_River</option>
                        <option {{ 'America/Rankin_Inlet' == @$practice->timezone ? ' selected' : '' }} value="America/Rankin_Inlet">(GMT -05:00), America/Rankin_Inlet</option>
                        <option {{ 'America/Recife' == @$practice->timezone ? ' selected' : '' }} value="America/Recife">(GMT -03:00), America/Recife</option>
                        <option {{ 'America/Regina' == @$practice->timezone ? ' selected' : '' }} value="America/Regina">(GMT -06:00), America/Regina</option>
                        <option {{ 'America/Resolute' == @$practice->timezone ? ' selected' : '' }} value="America/Resolute">(GMT -05:00), America/Resolute</option>
                        <option {{ 'America/Rio_Branco' == @$practice->timezone ? ' selected' : '' }} value="America/Rio_Branco">(GMT -05:00), America/Rio_Branco</option>
                        <option {{ 'America/Santarem' == @$practice->timezone ? ' selected' : '' }} value="America/Santarem">(GMT -03:00), America/Santarem</option>
                        <option {{ 'America/Santiago' == @$practice->timezone ? ' selected' : '' }} value="America/Santiago">(GMT -04:00), America/Santiago</option>
                        <option {{ 'America/Santo_Domingo' == @$practice->timezone ? ' selected' : '' }} value="America/Santo_Domingo">(GMT -04:00), America/Santo_Domingo</option>
                        <option {{ 'America/Sao_Paulo' == @$practice->timezone ? ' selected' : '' }} value="America/Sao_Paulo">(GMT -03:00), America/Sao_Paulo</option>
                        <option {{ 'America/Scoresbysund' == @$practice->timezone ? ' selected' : '' }} value="America/Scoresbysund">(GMT +00:00), America/Scoresbysund</option>
                        <option {{ 'America/Sitka' == @$practice->timezone ? ' selected' : '' }} value="America/Sitka">(GMT -08:00), America/Sitka</option>
                        <option {{ 'America/St_Barthelemy' == @$practice->timezone ? ' selected' : '' }} value="America/St_Barthelemy">(GMT -04:00), America/St_Barthelemy</option>
                        <option {{ 'America/St_Johns' == @$practice->timezone ? ' selected' : '' }} value="America/St_Johns">(GMT -02:30), America/St_Johns</option>
                        <option {{ 'America/St_Kitts' == @$practice->timezone ? ' selected' : '' }} value="America/St_Kitts">(GMT -04:00), America/St_Kitts</option>
                        <option {{ 'America/St_Lucia' == @$practice->timezone ? ' selected' : '' }} value="America/St_Lucia">(GMT -04:00), America/St_Lucia</option>
                        <option {{ 'America/St_Thomas' == @$practice->timezone ? ' selected' : '' }} value="America/St_Thomas">(GMT -04:00), America/St_Thomas</option>
                        <option {{ 'America/St_Vincent' == @$practice->timezone ? ' selected' : '' }} value="America/St_Vincent">(GMT -04:00), America/St_Vincent</option>
                        <option {{ 'America/Swift_Current' == @$practice->timezone ? ' selected' : '' }} value="America/Swift_Current">(GMT -06:00), America/Swift_Current</option>
                        <option {{ 'America/Tegucigalpa' == @$practice->timezone ? ' selected' : '' }} value="America/Tegucigalpa">(GMT -06:00), America/Tegucigalpa</option>
                        <option {{ 'America/Thule' == @$practice->timezone ? ' selected' : '' }} value="America/Thule">(GMT -03:00), America/Thule</option>
                        <option {{ 'America/Thunder_Bay' == @$practice->timezone ? ' selected' : '' }} value="America/Thunder_Bay">(GMT -04:00), America/Thunder_Bay</option>
                        <option {{ 'America/Tijuana' == @$practice->timezone ? ' selected' : '' }} value="America/Tijuana">(GMT -07:00), America/Tijuana</option>
                        <option {{ 'America/Toronto' == @$practice->timezone ? ' selected' : '' }} value="America/Toronto">(GMT -04:00), America/Toronto</option>
                        <option {{ 'America/Tortola' == @$practice->timezone ? ' selected' : '' }} value="America/Tortola">(GMT -04:00), America/Tortola</option>
                        <option {{ 'America/Vancouver' == @$practice->timezone ? ' selected' : '' }} value="America/Vancouver">(GMT -07:00), America/Vancouver</option>
                        <option {{ 'America/Whitehorse' == @$practice->timezone ? ' selected' : '' }} value="America/Whitehorse">(GMT -07:00), America/Whitehorse</option>
                        <option {{ 'America/Winnipeg' == @$practice->timezone ? ' selected' : '' }} value="America/Winnipeg">(GMT -05:00), America/Winnipeg</option>
                        <option {{ 'America/Yakutat' == @$practice->timezone ? ' selected' : '' }} value="America/Yakutat">(GMT -08:00), America/Yakutat</option>
                        <option {{ 'America/Yellowknife' == @$practice->timezone ? ' selected' : '' }} value="America/Yellowknife">(GMT -06:00), America/Yellowknife</option>
                        <option {{ 'Antarctica/Casey' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/Casey">(GMT +08:00), Antarctica/Casey</option>
                        <option {{ 'Antarctica/Davis' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/Davis">(GMT +07:00), Antarctica/Davis</option>
                        <option {{ 'Antarctica/DumontDUrville' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/DumontDUrville">(GMT +10:00), Antarctica/DumontDUrville</option>
                        <option {{ 'Antarctica/Macquarie' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/Macquarie">(GMT +11:00), Antarctica/Macquarie</option>
                        <option {{ 'Antarctica/Mawson' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/Mawson">(GMT +05:00), Antarctica/Mawson</option>
                        <option {{ 'Antarctica/McMurdo' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/McMurdo">(GMT +12:00), Antarctica/McMurdo</option>
                        <option {{ 'Antarctica/Palmer' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/Palmer">(GMT -03:00), Antarctica/Palmer</option>
                        <option {{ 'Antarctica/Rothera' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/Rothera">(GMT -03:00), Antarctica/Rothera</option>
                        <option {{ 'Antarctica/Syowa' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/Syowa">(GMT +03:00), Antarctica/Syowa</option>
                        <option {{ 'Antarctica/Troll' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/Troll">(GMT +02:00), Antarctica/Troll</option>
                        <option {{ 'Antarctica/Vostok' == @$practice->timezone ? ' selected' : '' }} value="Antarctica/Vostok">(GMT +06:00), Antarctica/Vostok</option>
                        <option {{ 'Arctic/Longyearbyen' == @$practice->timezone ? ' selected' : '' }} value="Arctic/Longyearbyen">(GMT +02:00), Arctic/Longyearbyen</option>
                        <option {{ 'Asia/Aden' == @$practice->timezone ? ' selected' : '' }} value="Asia/Aden">(GMT +03:00), Asia/Aden</option>
                        <option {{ 'Asia/Almaty' == @$practice->timezone ? ' selected' : '' }} value="Asia/Almaty">(GMT +06:00), Asia/Almaty</option>
                        <option {{ 'Asia/Amman' == @$practice->timezone ? ' selected' : '' }} value="Asia/Amman">(GMT +03:00), Asia/Amman</option>
                        <option {{ 'Asia/Anadyr' == @$practice->timezone ? ' selected' : '' }} value="Asia/Anadyr">(GMT +12:00), Asia/Anadyr</option>
                        <option {{ 'Asia/Aqtau' == @$practice->timezone ? ' selected' : '' }} value="Asia/Aqtau">(GMT +05:00), Asia/Aqtau</option>
                        <option {{ 'Asia/Aqtobe' == @$practice->timezone ? ' selected' : '' }} value="Asia/Aqtobe">(GMT +05:00), Asia/Aqtobe</option>
                        <option {{ 'Asia/Ashgabat' == @$practice->timezone ? ' selected' : '' }} value="Asia/Ashgabat">(GMT +05:00), Asia/Ashgabat</option>
                        <option {{ 'Asia/Atyrau' == @$practice->timezone ? ' selected' : '' }} value="Asia/Atyrau">(GMT +05:00), Asia/Atyrau</option>
                        <option {{ 'Asia/Baghdad' == @$practice->timezone ? ' selected' : '' }} value="Asia/Baghdad">(GMT +03:00), Asia/Baghdad</option>
                        <option {{ 'Asia/Bahrain' == @$practice->timezone ? ' selected' : '' }} value="Asia/Bahrain">(GMT +03:00), Asia/Bahrain</option>
                        <option {{ 'Asia/Baku' == @$practice->timezone ? ' selected' : '' }} value="Asia/Baku">(GMT +04:00), Asia/Baku</option>
                        <option {{ 'Asia/Bangkok' == @$practice->timezone ? ' selected' : '' }} value="Asia/Bangkok">(GMT +07:00), Asia/Bangkok</option>
                        <option {{ 'Asia/Barnaul' == @$practice->timezone ? ' selected' : '' }} value="Asia/Barnaul">(GMT +07:00), Asia/Barnaul</option>
                        <option {{ 'Asia/Beirut' == @$practice->timezone ? ' selected' : '' }} value="Asia/Beirut">(GMT +03:00), Asia/Beirut</option>
                        <option {{ 'Asia/Bishkek' == @$practice->timezone ? ' selected' : '' }} value="Asia/Bishkek">(GMT +06:00), Asia/Bishkek</option>
                        <option {{ 'Asia/Brunei' == @$practice->timezone ? ' selected' : '' }} value="Asia/Brunei">(GMT +08:00), Asia/Brunei</option>
                        <option {{ 'Asia/Chita' == @$practice->timezone ? ' selected' : '' }} value="Asia/Chita">(GMT +09:00), Asia/Chita</option>
                        <option {{ 'Asia/Choibalsan' == @$practice->timezone ? ' selected' : '' }} value="Asia/Choibalsan">(GMT +08:00), Asia/Choibalsan</option>
                        <option {{ 'Asia/Colombo' == @$practice->timezone ? ' selected' : '' }} value="Asia/Colombo">(GMT +05:30), Asia/Colombo</option>
                        <option {{ 'Asia/Damascus' == @$practice->timezone ? ' selected' : '' }} value="Asia/Damascus">(GMT +03:00), Asia/Damascus</option>
                        <option {{ 'Asia/Dhaka' == @$practice->timezone ? ' selected' : '' }} value="Asia/Dhaka">(GMT +06:00), Asia/Dhaka</option>
                        <option {{ 'Asia/Dili' == @$practice->timezone ? ' selected' : '' }} value="Asia/Dili">(GMT +09:00), Asia/Dili</option>
                        <option {{ 'Asia/Dubai' == @$practice->timezone ? ' selected' : '' }} value="Asia/Dubai">(GMT +04:00), Asia/Dubai</option>
                        <option {{ 'Asia/Dushanbe' == @$practice->timezone ? ' selected' : '' }} value="Asia/Dushanbe">(GMT +05:00), Asia/Dushanbe</option>
                        <option {{ 'Asia/Famagusta' == @$practice->timezone ? ' selected' : '' }} value="Asia/Famagusta">(GMT +03:00), Asia/Famagusta</option>
                        <option {{ 'Asia/Gaza' == @$practice->timezone ? ' selected' : '' }} value="Asia/Gaza">(GMT +03:00), Asia/Gaza</option>
                        <option {{ 'Asia/Hebron' == @$practice->timezone ? ' selected' : '' }} value="Asia/Hebron">(GMT +03:00), Asia/Hebron</option>
                        <option {{ 'Asia/Ho' == @$practice->timezone ? ' selected' : '' }} value="Asia/Ho">(GMT +07:00), Asia/Ho</option>
                        <option {{ 'Asia/Hong' == @$practice->timezone ? ' selected' : '' }} value="Asia/Hong">(GMT +08:00), Asia/Hong</option>
                        <option {{ 'Asia/Hovd' == @$practice->timezone ? ' selected' : '' }} value="Asia/Hovd">(GMT +07:00), Asia/Hovd</option>
                        <option {{ 'Asia/Irkutsk' == @$practice->timezone ? ' selected' : '' }} value="Asia/Irkutsk">(GMT +08:00), Asia/Irkutsk</option>
                        <option {{ 'Asia/Jakarta' == @$practice->timezone ? ' selected' : '' }} value="Asia/Jakarta">(GMT +07:00), Asia/Jakarta</option>
                        <option {{ 'Asia/Jayapura' == @$practice->timezone ? ' selected' : '' }} value="Asia/Jayapura">(GMT +09:00), Asia/Jayapura</option>
                        <option {{ 'Asia/Jerusalem' == @$practice->timezone ? ' selected' : '' }} value="Asia/Jerusalem">(GMT +03:00), Asia/Jerusalem</option>
                        <option {{ 'Asia/Kabul' == @$practice->timezone ? ' selected' : '' }} value="Asia/Kabul">(GMT +04:30), Asia/Kabul</option>
                        <option {{ 'Asia/Kamchatka' == @$practice->timezone ? ' selected' : '' }} value="Asia/Kamchatka">(GMT +12:00), Asia/Kamchatka</option>
                        <option {{ 'Asia/Karachi' == @$practice->timezone ? ' selected' : '' }} value="Asia/Karachi">(GMT +05:00), Asia/Karachi</option>
                        <option {{ 'Asia/Kathmandu' == @$practice->timezone ? ' selected' : '' }} value="Asia/Kathmandu">(GMT +05:45), Asia/Kathmandu</option>
                        <option {{ 'Asia/Khandyga' == @$practice->timezone ? ' selected' : '' }} value="Asia/Khandyga">(GMT +09:00), Asia/Khandyga</option>
                        <option {{ 'Asia/Kolkata' == @$practice->timezone ? ' selected' : '' }} value="Asia/Kolkata">(GMT +05:30), Asia/Kolkata</option>
                        <option {{ 'Asia/Krasnoyarsk' == @$practice->timezone ? ' selected' : '' }} value="Asia/Krasnoyarsk">(GMT +07:00), Asia/Krasnoyarsk</option>
                        <option {{ 'Asia/Kuala' == @$practice->timezone ? ' selected' : '' }} value="Asia/Kuala">(GMT +08:00), Asia/Kuala</option>
                        <option {{ 'Asia/Kuching' == @$practice->timezone ? ' selected' : '' }} value="Asia/Kuching">(GMT +08:00), Asia/Kuching</option>
                        <option {{ 'Asia/Kuwait' == @$practice->timezone ? ' selected' : '' }} value="Asia/Kuwait">(GMT +03:00), Asia/Kuwait</option>
                        <option {{ 'Asia/Macau' == @$practice->timezone ? ' selected' : '' }} value="Asia/Macau">(GMT +08:00), Asia/Macau</option>
                        <option {{ 'Asia/Magadan' == @$practice->timezone ? ' selected' : '' }} value="Asia/Magadan">(GMT +11:00), Asia/Magadan</option>
                        <option {{ 'Asia/Makassar' == @$practice->timezone ? ' selected' : '' }} value="Asia/Makassar">(GMT +08:00), Asia/Makassar</option>
                        <option {{ 'Asia/Manila' == @$practice->timezone ? ' selected' : '' }} value="Asia/Manila">(GMT +08:00), Asia/Manila</option>
                        <option {{ 'Asia/Muscat' == @$practice->timezone ? ' selected' : '' }} value="Asia/Muscat">(GMT +04:00), Asia/Muscat</option>
                        <option {{ 'Asia/Nicosia' == @$practice->timezone ? ' selected' : '' }} value="Asia/Nicosia">(GMT +03:00), Asia/Nicosia</option>
                        <option {{ 'Asia/Novokuznetsk' == @$practice->timezone ? ' selected' : '' }} value="Asia/Novokuznetsk">(GMT +07:00), Asia/Novokuznetsk</option>
                        <option {{ 'Asia/Novosibirsk' == @$practice->timezone ? ' selected' : '' }} value="Asia/Novosibirsk">(GMT +07:00), Asia/Novosibirsk</option>
                        <option {{ 'Asia/Omsk' == @$practice->timezone ? ' selected' : '' }} value="Asia/Omsk">(GMT +06:00), Asia/Omsk</option>
                        <option {{ 'Asia/Oral' == @$practice->timezone ? ' selected' : '' }} value="Asia/Oral">(GMT +05:00), Asia/Oral</option>
                        <option {{ 'Asia/Phnom' == @$practice->timezone ? ' selected' : '' }} value="Asia/Phnom">(GMT +07:00), Asia/Phnom</option>
                        <option {{ 'Asia/Pontianak' == @$practice->timezone ? ' selected' : '' }} value="Asia/Pontianak">(GMT +07:00), Asia/Pontianak</option>
                        <option {{ 'Asia/Pyongyang' == @$practice->timezone ? ' selected' : '' }} value="Asia/Pyongyang">(GMT +09:00), Asia/Pyongyang</option>
                        <option {{ 'Asia/Qatar' == @$practice->timezone ? ' selected' : '' }} value="Asia/Qatar">(GMT +03:00), Asia/Qatar</option>
                        <option {{ 'Asia/Qostanay' == @$practice->timezone ? ' selected' : '' }} value="Asia/Qostanay">(GMT +06:00), Asia/Qostanay</option>
                        <option {{ 'Asia/Qyzylorda' == @$practice->timezone ? ' selected' : '' }} value="Asia/Qyzylorda">(GMT +05:00), Asia/Qyzylorda</option>
                        <option {{ 'Asia/Riyadh' == @$practice->timezone ? ' selected' : '' }} value="Asia/Riyadh">(GMT +03:00), Asia/Riyadh</option>
                        <option {{ 'Asia/Sakhalin' == @$practice->timezone ? ' selected' : '' }} value="Asia/Sakhalin">(GMT +11:00), Asia/Sakhalin</option>
                        <option {{ 'Asia/Samarkand' == @$practice->timezone ? ' selected' : '' }} value="Asia/Samarkand">(GMT +05:00), Asia/Samarkand</option>
                        <option {{ 'Asia/Seoul' == @$practice->timezone ? ' selected' : '' }} value="Asia/Seoul">(GMT +09:00), Asia/Seoul</option>
                        <option {{ 'Asia/Shanghai' == @$practice->timezone ? ' selected' : '' }} value="Asia/Shanghai">(GMT +08:00), Asia/Shanghai</option>
                        <option {{ 'Asia/Singapore' == @$practice->timezone ? ' selected' : '' }} value="Asia/Singapore">(GMT +08:00), Asia/Singapore</option>
                        <option {{ 'Asia/Srednekolymsk' == @$practice->timezone ? ' selected' : '' }} value="Asia/Srednekolymsk">(GMT +11:00), Asia/Srednekolymsk</option>
                        <option {{ 'Asia/Taipei' == @$practice->timezone ? ' selected' : '' }} value="Asia/Taipei">(GMT +08:00), Asia/Taipei</option>
                        <option {{ 'Asia/Tashkent' == @$practice->timezone ? ' selected' : '' }} value="Asia/Tashkent">(GMT +05:00), Asia/Tashkent</option>
                        <option {{ 'Asia/Tbilisi' == @$practice->timezone ? ' selected' : '' }} value="Asia/Tbilisi">(GMT +04:00), Asia/Tbilisi</option>
                        <option {{ 'Asia/Tehran' == @$practice->timezone ? ' selected' : '' }} value="Asia/Tehran">(GMT +04:30), Asia/Tehran</option>
                        <option {{ 'Asia/Thimphu' == @$practice->timezone ? ' selected' : '' }} value="Asia/Thimphu">(GMT +06:00), Asia/Thimphu</option>
                        <option {{ 'Asia/Tokyo' == @$practice->timezone ? ' selected' : '' }} value="Asia/Tokyo">(GMT +09:00), Asia/Tokyo</option>
                        <option {{ 'Asia/Tomsk' == @$practice->timezone ? ' selected' : '' }} value="Asia/Tomsk">(GMT +07:00), Asia/Tomsk</option>
                        <option {{ 'Asia/Ulaanbaatar' == @$practice->timezone ? ' selected' : '' }} value="Asia/Ulaanbaatar">(GMT +08:00), Asia/Ulaanbaatar</option>
                        <option {{ 'Asia/Urumqi' == @$practice->timezone ? ' selected' : '' }} value="Asia/Urumqi">(GMT +06:00), Asia/Urumqi</option>
                        <option {{ 'Asia/Ust' == @$practice->timezone ? ' selected' : '' }} value="Asia/Ust">(GMT +10:00), Asia/Ust</option>
                        <option {{ 'Asia/Vientiane' == @$practice->timezone ? ' selected' : '' }} value="Asia/Vientiane">(GMT +07:00), Asia/Vientiane</option>
                        <option {{ 'Asia/Vladivostok' == @$practice->timezone ? ' selected' : '' }} value="Asia/Vladivostok">(GMT +10:00), Asia/Vladivostok</option>
                        <option {{ 'Asia/Yakutsk' == @$practice->timezone ? ' selected' : '' }} value="Asia/Yakutsk">(GMT +09:00), Asia/Yakutsk</option>
                        <option {{ 'Asia/Yangon' == @$practice->timezone ? ' selected' : '' }} value="Asia/Yangon">(GMT +06:30), Asia/Yangon</option>
                        <option {{ 'Asia/Yekaterinburg' == @$practice->timezone ? ' selected' : '' }} value="Asia/Yekaterinburg">(GMT +05:00), Asia/Yekaterinburg</option>
                        <option {{ 'Asia/Yerevan' == @$practice->timezone ? ' selected' : '' }} value="Asia/Yerevan">(GMT +04:00), Asia/Yerevan</option>
                        <option {{ 'Atlantic/Azores' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/Azores">(GMT +00:00), Atlantic/Azores</option>
                        <option {{ 'Atlantic/Bermuda' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/Bermuda">(GMT -03:00), Atlantic/Bermuda</option>
                        <option {{ 'Atlantic/Canary' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/Canary">(GMT +01:00), Atlantic/Canary</option>
                        <option {{ 'Atlantic/Cape' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/Cape">(GMT -01:00), Atlantic/Cape</option>
                        <option {{ 'Atlantic/Faroe' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/Faroe">(GMT +01:00), Atlantic/Faroe</option>
                        <option {{ 'Atlantic/Madeira' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/Madeira">(GMT +01:00), Atlantic/Madeira</option>
                        <option {{ 'Atlantic/Reykjavik' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/Reykjavik">(GMT +00:00), Atlantic/Reykjavik</option>
                        <option {{ 'Atlantic/South' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/South">(GMT -02:00), Atlantic/South</option>
                        <option {{ 'Atlantic/St' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/St">(GMT +00:00), Atlantic/St</option>
                        <option {{ 'Atlantic/Stanley' == @$practice->timezone ? ' selected' : '' }} value="Atlantic/Stanley">(GMT -03:00), Atlantic/Stanley</option>
                        <option {{ 'Australia/Adelaide' == @$practice->timezone ? ' selected' : '' }} value="Australia/Adelaide">(GMT +09:30), Australia/Adelaide</option>
                        <option {{ 'Australia/Brisbane' == @$practice->timezone ? ' selected' : '' }} value="Australia/Brisbane">(GMT +10:00), Australia/Brisbane</option>
                        <option {{ 'Australia/Broken' == @$practice->timezone ? ' selected' : '' }} value="Australia/Broken">(GMT +09:30), Australia/Broken</option>
                        <option {{ 'Australia/Currie' == @$practice->timezone ? ' selected' : '' }} value="Australia/Currie">(GMT +10:00), Australia/Currie</option>
                        <option {{ 'Australia/Darwin' == @$practice->timezone ? ' selected' : '' }} value="Australia/Darwin">(GMT +09:30), Australia/Darwin</option>
                        <option {{ 'Australia/Eucla' == @$practice->timezone ? ' selected' : '' }} value="Australia/Eucla">(GMT +08:45), Australia/Eucla</option>
                        <option {{ 'Australia/Hobart' == @$practice->timezone ? ' selected' : '' }} value="Australia/Hobart">(GMT +10:00), Australia/Hobart</option>
                        <option {{ 'Australia/Lindeman' == @$practice->timezone ? ' selected' : '' }} value="Australia/Lindeman">(GMT +10:00), Australia/Lindeman</option>
                        <option {{ 'Australia/Lord' == @$practice->timezone ? ' selected' : '' }} value="Australia/Lord">(GMT +10:30), Australia/Lord</option>
                        <option {{ 'Australia/Melbourne' == @$practice->timezone ? ' selected' : '' }} value="Australia/Melbourne">(GMT +10:00), Australia/Melbourne</option>
                        <option {{ 'Australia/Perth' == @$practice->timezone ? ' selected' : '' }} value="Australia/Perth">(GMT +08:00), Australia/Perth</option>
                        <option {{ 'Australia/Sydney' == @$practice->timezone ? ' selected' : '' }} value="Australia/Sydney">(GMT +10:00), Australia/Sydney</option>
                        <option {{ 'Europe/Amsterdam' == @$practice->timezone ? ' selected' : '' }} value="Europe/Amsterdam">(GMT +02:00), Europe/Amsterdam</option>
                        <option {{ 'Europe/Andorra' == @$practice->timezone ? ' selected' : '' }} value="Europe/Andorra">(GMT +02:00), Europe/Andorra</option>
                        <option {{ 'Europe/Astrakhan' == @$practice->timezone ? ' selected' : '' }} value="Europe/Astrakhan">(GMT +04:00), Europe/Astrakhan</option>
                        <option {{ 'Europe/Athens' == @$practice->timezone ? ' selected' : '' }} value="Europe/Athens">(GMT +03:00), Europe/Athens</option>
                        <option {{ 'Europe/Belgrade' == @$practice->timezone ? ' selected' : '' }} value="Europe/Belgrade">(GMT +02:00), Europe/Belgrade</option>
                        <option {{ 'Europe/Berlin' == @$practice->timezone ? ' selected' : '' }} value="Europe/Berlin">(GMT +02:00), Europe/Berlin</option>
                        <option {{ 'Europe/Bratislava' == @$practice->timezone ? ' selected' : '' }} value="Europe/Bratislava">(GMT +02:00), Europe/Bratislava</option>
                        <option {{ 'Europe/Brussels' == @$practice->timezone ? ' selected' : '' }} value="Europe/Brussels">(GMT +02:00), Europe/Brussels</option>
                        <option {{ 'Europe/Bucharest' == @$practice->timezone ? ' selected' : '' }} value="Europe/Bucharest">(GMT +03:00), Europe/Bucharest</option>
                        <option {{ 'Europe/Budapest' == @$practice->timezone ? ' selected' : '' }} value="Europe/Budapest">(GMT +02:00), Europe/Budapest</option>
                        <option {{ 'Europe/Busingen' == @$practice->timezone ? ' selected' : '' }} value="Europe/Busingen">(GMT +02:00), Europe/Busingen</option>
                        <option {{ 'Europe/Chisinau' == @$practice->timezone ? ' selected' : '' }} value="Europe/Chisinau">(GMT +03:00), Europe/Chisinau</option>
                        <option {{ 'Europe/Copenhagen' == @$practice->timezone ? ' selected' : '' }} value="Europe/Copenhagen">(GMT +02:00), Europe/Copenhagen</option>
                        <option {{ 'Europe/Dublin' == @$practice->timezone ? ' selected' : '' }} value="Europe/Dublin">(GMT +01:00), Europe/Dublin</option>
                        <option {{ 'Europe/Gibraltar' == @$practice->timezone ? ' selected' : '' }} value="Europe/Gibraltar">(GMT +02:00), Europe/Gibraltar</option>
                        <option {{ 'Europe/Guernsey' == @$practice->timezone ? ' selected' : '' }} value="Europe/Guernsey">(GMT +01:00), Europe/Guernsey</option>
                        <option {{ 'Europe/Helsinki' == @$practice->timezone ? ' selected' : '' }} value="Europe/Helsinki">(GMT +03:00), Europe/Helsinki</option>
                        <option {{ 'Europe/Isle' == @$practice->timezone ? ' selected' : '' }} value="Europe/Isle">(GMT +01:00), Europe/Isle</option>
                        <option {{ 'Europe/Istanbul' == @$practice->timezone ? ' selected' : '' }} value="Europe/Istanbul">(GMT +03:00), Europe/Istanbul</option>
                        <option {{ 'Europe/Jersey' == @$practice->timezone ? ' selected' : '' }} value="Europe/Jersey">(GMT +01:00), Europe/Jersey</option>
                        <option {{ 'Europe/Kaliningrad' == @$practice->timezone ? ' selected' : '' }} value="Europe/Kaliningrad">(GMT +02:00), Europe/Kaliningrad</option>
                        <option {{ 'Europe/Kiev' == @$practice->timezone ? ' selected' : '' }} value="Europe/Kiev">(GMT +03:00), Europe/Kiev</option>
                        <option {{ 'Europe/Kirov' == @$practice->timezone ? ' selected' : '' }} value="Europe/Kirov">(GMT +03:00), Europe/Kirov</option>
                        <option {{ 'Europe/Lisbon' == @$practice->timezone ? ' selected' : '' }} value="Europe/Lisbon">(GMT +01:00), Europe/Lisbon</option>
                        <option {{ 'Europe/Ljubljana' == @$practice->timezone ? ' selected' : '' }} value="Europe/Ljubljana">(GMT +02:00), Europe/Ljubljana</option>
                        <option {{ 'Europe/London' == @$practice->timezone ? ' selected' : '' }} value="Europe/London">(GMT +01:00), Europe/London</option>
                        <option {{ 'Europe/Luxembourg' == @$practice->timezone ? ' selected' : '' }} value="Europe/Luxembourg">(GMT +02:00), Europe/Luxembourg</option>
                        <option {{ 'Europe/Madrid' == @$practice->timezone ? ' selected' : '' }} value="Europe/Madrid">(GMT +02:00), Europe/Madrid</option>
                        <option {{ 'Europe/Malta' == @$practice->timezone ? ' selected' : '' }} value="Europe/Malta">(GMT +02:00), Europe/Malta</option>
                        <option {{ 'Europe/Mariehamn' == @$practice->timezone ? ' selected' : '' }} value="Europe/Mariehamn">(GMT +03:00), Europe/Mariehamn</option>
                        <option {{ 'Europe/Minsk' == @$practice->timezone ? ' selected' : '' }} value="Europe/Minsk">(GMT +03:00), Europe/Minsk</option>
                        <option {{ 'Europe/Monaco' == @$practice->timezone ? ' selected' : '' }} value="Europe/Monaco">(GMT +02:00), Europe/Monaco</option>
                        <option {{ 'Europe/Moscow' == @$practice->timezone ? ' selected' : '' }} value="Europe/Moscow">(GMT +03:00), Europe/Moscow</option>
                        <option {{ 'Europe/Oslo' == @$practice->timezone ? ' selected' : '' }} value="Europe/Oslo">(GMT +02:00), Europe/Oslo</option>
                        <option {{ 'Europe/Paris' == @$practice->timezone ? ' selected' : '' }} value="Europe/Paris">(GMT +02:00), Europe/Paris</option>
                        <option {{ 'Europe/Podgorica' == @$practice->timezone ? ' selected' : '' }} value="Europe/Podgorica">(GMT +02:00), Europe/Podgorica</option>
                        <option {{ 'Europe/Prague' == @$practice->timezone ? ' selected' : '' }} value="Europe/Prague">(GMT +02:00), Europe/Prague</option>
                        <option {{ 'Europe/Riga' == @$practice->timezone ? ' selected' : '' }} value="Europe/Riga">(GMT +03:00), Europe/Riga</option>
                        <option {{ 'Europe/Rome' == @$practice->timezone ? ' selected' : '' }} value="Europe/Rome">(GMT +02:00), Europe/Rome</option>
                        <option {{ 'Europe/Samara' == @$practice->timezone ? ' selected' : '' }} value="Europe/Samara">(GMT +04:00), Europe/Samara</option>
                        <option {{ 'Europe/San' == @$practice->timezone ? ' selected' : '' }} value="Europe/San">(GMT +02:00), Europe/San</option>
                        <option {{ 'Europe/Sarajevo' == @$practice->timezone ? ' selected' : '' }} value="Europe/Sarajevo">(GMT +02:00), Europe/Sarajevo</option>
                        <option {{ 'Europe/Saratov' == @$practice->timezone ? ' selected' : '' }} value="Europe/Saratov">(GMT +04:00), Europe/Saratov</option>
                        <option {{ 'Europe/Simferopol' == @$practice->timezone ? ' selected' : '' }} value="Europe/Simferopol">(GMT +03:00), Europe/Simferopol</option>
                        <option {{ 'Europe/Skopje' == @$practice->timezone ? ' selected' : '' }} value="Europe/Skopje">(GMT +02:00), Europe/Skopje</option>
                        <option {{ 'Europe/Sofia' == @$practice->timezone ? ' selected' : '' }} value="Europe/Sofia">(GMT +03:00), Europe/Sofia</option>
                        <option {{ 'Europe/Stockholm' == @$practice->timezone ? ' selected' : '' }} value="Europe/Stockholm">(GMT +02:00), Europe/Stockholm</option>
                        <option {{ 'Europe/Tallinn' == @$practice->timezone ? ' selected' : '' }} value="Europe/Tallinn">(GMT +03:00), Europe/Tallinn</option>
                        <option {{ 'Europe/Tirane' == @$practice->timezone ? ' selected' : '' }} value="Europe/Tirane">(GMT +02:00), Europe/Tirane</option>
                        <option {{ 'Europe/Ulyanovsk' == @$practice->timezone ? ' selected' : '' }} value="Europe/Ulyanovsk">(GMT +04:00), Europe/Ulyanovsk</option>
                        <option {{ 'Europe/Uzhgorod' == @$practice->timezone ? ' selected' : '' }} value="Europe/Uzhgorod">(GMT +03:00), Europe/Uzhgorod</option>
                        <option {{ 'Europe/Vaduz' == @$practice->timezone ? ' selected' : '' }} value="Europe/Vaduz">(GMT +02:00), Europe/Vaduz</option>
                        <option {{ 'Europe/Vatican' == @$practice->timezone ? ' selected' : '' }} value="Europe/Vatican">(GMT +02:00), Europe/Vatican</option>
                        <option {{ 'Europe/Vienna' == @$practice->timezone ? ' selected' : '' }} value="Europe/Vienna">(GMT +02:00), Europe/Vienna</option>
                        <option {{ 'Europe/Vilnius' == @$practice->timezone ? ' selected' : '' }} value="Europe/Vilnius">(GMT +03:00), Europe/Vilnius</option>
                        <option {{ 'Europe/Volgograd' == @$practice->timezone ? ' selected' : '' }} value="Europe/Volgograd">(GMT +04:00), Europe/Volgograd</option>
                        <option {{ 'Europe/Warsaw' == @$practice->timezone ? ' selected' : '' }} value="Europe/Warsaw">(GMT +02:00), Europe/Warsaw</option>
                        <option {{ 'Europe/Zagreb' == @$practice->timezone ? ' selected' : '' }} value="Europe/Zagreb">(GMT +02:00), Europe/Zagreb</option>
                        <option {{ 'Europe/Zaporozhye' == @$practice->timezone ? ' selected' : '' }} value="Europe/Zaporozhye">(GMT +03:00), Europe/Zaporozhye</option>
                        <option {{ 'Europe/Zurich' == @$practice->timezone ? ' selected' : '' }} value="Europe/Zurich">(GMT +02:00), Europe/Zurich</option>
                        <option {{ 'Indian/Antananarivo' == @$practice->timezone ? ' selected' : '' }} value="Indian/Antananarivo">(GMT +03:00), Indian/Antananarivo</option>
                        <option {{ 'Indian/Chagos' == @$practice->timezone ? ' selected' : '' }} value="Indian/Chagos">(GMT +06:00), Indian/Chagos</option>
                        <option {{ 'Indian/Christmas' == @$practice->timezone ? ' selected' : '' }} value="Indian/Christmas">(GMT +07:00), Indian/Christmas</option>
                        <option {{ 'Indian/Cocos' == @$practice->timezone ? ' selected' : '' }} value="Indian/Cocos">(GMT +06:30), Indian/Cocos</option>
                        <option {{ 'Indian/Comoro' == @$practice->timezone ? ' selected' : '' }} value="Indian/Comoro">(GMT +03:00), Indian/Comoro</option>
                        <option {{ 'Indian/Kerguelen' == @$practice->timezone ? ' selected' : '' }} value="Indian/Kerguelen">(GMT +05:00), Indian/Kerguelen</option>
                        <option {{ 'Indian/Mahe' == @$practice->timezone ? ' selected' : '' }} value="Indian/Mahe">(GMT +04:00), Indian/Mahe</option>
                        <option {{ 'Indian/Maldives' == @$practice->timezone ? ' selected' : '' }} value="Indian/Maldives">(GMT +05:00), Indian/Maldives</option>
                        <option {{ 'Indian/Mauritius' == @$practice->timezone ? ' selected' : '' }} value="Indian/Mauritius">(GMT +04:00), Indian/Mauritius</option>
                        <option {{ 'Indian/Mayotte' == @$practice->timezone ? ' selected' : '' }} value="Indian/Mayotte">(GMT +03:00), Indian/Mayotte</option>
                        <option {{ 'Indian/Reunion' == @$practice->timezone ? ' selected' : '' }} value="Indian/Reunion">(GMT +04:00), Indian/Reunion</option>
                        <option {{ 'Pacific/Apia' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Apia">(GMT +13:00), Pacific/Apia</option>
                        <option {{ 'Pacific/Auckland' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Auckland">(GMT +12:00), Pacific/Auckland</option>
                        <option {{ 'Pacific/Bougainville' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Bougainville">(GMT +11:00), Pacific/Bougainville</option>
                        <option {{ 'Pacific/Chatham' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Chatham">(GMT +12:45), Pacific/Chatham</option>
                        <option {{ 'Pacific/Chuuk' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Chuuk">(GMT +10:00), Pacific/Chuuk</option>
                        <option {{ 'Pacific/Easter' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Easter">(GMT -06:00), Pacific/Easter</option>
                        <option {{ 'Pacific/Efate' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Efate">(GMT +11:00), Pacific/Efate</option>
                        <option {{ 'Pacific/Enderbury' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Enderbury">(GMT +13:00), Pacific/Enderbury</option>
                        <option {{ 'Pacific/Fakaofo' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Fakaofo">(GMT +13:00), Pacific/Fakaofo</option>
                        <option {{ 'Pacific/Fiji' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Fiji">(GMT +12:00), Pacific/Fiji</option>
                        <option {{ 'Pacific/Funafuti' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Funafuti">(GMT +12:00), Pacific/Funafuti</option>
                        <option {{ 'Pacific/Galapagos' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Galapagos">(GMT -06:00), Pacific/Galapagos</option>
                        <option {{ 'Pacific/Gambier' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Gambier">(GMT -09:00), Pacific/Gambier</option>
                        <option {{ 'Pacific/Guadalcanal' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Guadalcanal">(GMT +11:00), Pacific/Guadalcanal</option>
                        <option {{ 'Pacific/Guam' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Guam">(GMT +10:00), Pacific/Guam</option>
                        <option {{ 'Pacific/Honolulu' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Honolulu">(GMT -10:00), Pacific/Honolulu</option>
                        <option {{ 'Pacific/Kiritimati' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Kiritimati">(GMT +14:00), Pacific/Kiritimati</option>
                        <option {{ 'Pacific/Kosrae' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Kosrae">(GMT +11:00), Pacific/Kosrae</option>
                        <option {{ 'Pacific/Kwajalein' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Kwajalein">(GMT +12:00), Pacific/Kwajalein</option>
                        <option {{ 'Pacific/Majuro' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Majuro">(GMT +12:00), Pacific/Majuro</option>
                        <option {{ 'Pacific/Marquesas' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Marquesas">(GMT -09:30), Pacific/Marquesas</option>
                        <option {{ 'Pacific/Midway' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Midway">(GMT -11:00), Pacific/Midway</option>
                        <option {{ 'Pacific/Nauru' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Nauru">(GMT +12:00), Pacific/Nauru</option>
                        <option {{ 'Pacific/Niue' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Niue">(GMT -11:00), Pacific/Niue</option>
                        <option {{ 'Pacific/Norfolk' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Norfolk">(GMT +11:00), Pacific/Norfolk</option>
                        <option {{ 'Pacific/Noumea' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Noumea">(GMT +11:00), Pacific/Noumea</option>
                        <option {{ 'Pacific/Pago' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Pago">(GMT -11:00), Pacific/Pago</option>
                        <option {{ 'Pacific/Palau' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Palau">(GMT +09:00), Pacific/Palau</option>
                        <option {{ 'Pacific/Pitcairn' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Pitcairn">(GMT -08:00), Pacific/Pitcairn</option>
                        <option {{ 'Pacific/Pohnpei' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Pohnpei">(GMT +11:00), Pacific/Pohnpei</option>
                        <option {{ 'Pacific/Port' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Port">(GMT +10:00), Pacific/Port</option>
                        <option {{ 'Pacific/Rarotonga' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Rarotonga">(GMT -10:00), Pacific/Rarotonga</option>
                        <option {{ 'Pacific/Saipan' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Saipan">(GMT +10:00), Pacific/Saipan</option>
                        <option {{ 'Pacific/Tahiti' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Tahiti">(GMT -10:00), Pacific/Tahiti</option>
                        <option {{ 'Pacific/Tarawa' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Tarawa">(GMT +12:00), Pacific/Tarawa</option>
                        <option {{ 'Pacific/Tongatapu' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Tongatapu">(GMT +13:00), Pacific/Tongatapu</option>
                        <option {{ 'Pacific/Wake' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Wake">(GMT +12:00), Pacific/Wake</option>
                        <option {{ 'Pacific/Wallis' == @$practice->timezone ? ' selected' : '' }} value="Pacific/Wallis">(GMT +12:00), Pacific/Wallis</option>
                    </select>                                        
                </div>
            </div>
            
            

        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->

    <div class="box no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="message-out"></i> <h3 class="box-title">Pay to Address</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 js-address-class" id="js-address-pay-to-address">
            {!! Form::hidden('pta_address_type','practice',['class'=>'js-address-type']) !!}
            {!! Form::hidden('pta_address_type_category','pay_to_address',['class'=>'js-address-type-category']) !!}
            {!! Form::hidden('pta_address1',$address_flag['pta']['address1'],['class'=>'js-address-address1']) !!}
            {!! Form::hidden('pta_city',$address_flag['pta']['city'],['class'=>'js-address-city']) !!}
            {!! Form::hidden('pta_state',$address_flag['pta']['state'],['class'=>'js-address-state']) !!}
            {!! Form::hidden('pta_zip5',$address_flag['pta']['zip5'],['class'=>'js-address-zip5']) !!}
            {!! Form::hidden('pta_zip4',$address_flag['pta']['zip4'],['class'=>'js-address-zip4']) !!}
            {!! Form::hidden('pta_is_address_match',$address_flag['pta']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
            {!! Form::hidden('pta_error_message',$address_flag['pta']['error_message'],['class'=>'js-address-error-message']) !!}


            <div class="form-group">
                {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('pay_add_1')) error @endif">
                    {!! Form::text('pay_add_1',null,['id'=>'pay_add_1','class'=>'form-control js-address-check dm-address']) !!}
                    {!! $errors->first('pay_add_1', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('pay_add_2')) error @endif">
                    {!! Form::text('pay_add_2',null,['id'=>'pay_add_2','class'=>'form-control js-address2-tab dm-address']) !!}
                    {!! $errors->first('pay_add_2', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            
            
            <div class="form-group">
                {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('pay_city')) error @endif">  
                    {!! Form::text('pay_city',null,['class'=>'form-control js-letters-caps-format js-address-check dm-address','id'=>'pay_city']) !!}
                    {!! $errors->first('pay_city', '<p> :message</p>')  !!}
                    
                </div>{!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 @if($errors->first('pay_state')) error @endif">
                    {!! Form::text('pay_state',null,['class'=>'form-control js-all-caps-letter-format js-address-check js-state-tab dm-state','id'=>'pay_state']) !!}
					{!! $errors->first('pay_state', '<p> :message</p>')  !!}
                </div>
            </div>   

            <div class="form-group">
                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('pay_zip5')) error @endif">
                    {!! Form::text('pay_zip5',null,['class'=>'form-control js-address-check dm-zip5','id'=>'pay_zip5']) !!}
                    {!! $errors->first('pay_zip5', '<p> :message</p>')  !!}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                    {!! Form::text('pay_zip4',null,['class'=>'form-control js-address-check dm-zip4','id'=>'pay_zip4']) !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2">
                    <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                     <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['pta']['is_address_match']); ?>   
                    <?php echo $value;?>                            
                </div>
            </div>
            <div class="form-group no-bottom m-b-m-5 margin-l-m-5">
                <input type="checkbox" class="js-same-as-address" name="same_as_paytoaddress" id="mail_same_address" /> <label for="mail_same_address" class="no-bottom js-same-as-address">Mailing Address same as Pay to Address</label>
            </div>
            
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->

    <div class="box no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="mail"></i> <h3 class="box-title">Primary Location</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 js-address-class" id="js-address-primary-address">
            {!! Form::hidden('pa_address_type','practice',['class'=>'js-address-type']) !!}
            {!! Form::hidden('pa_address_type_category','primary_address',['class'=>'js-address-type-category']) !!}
            {!! Form::hidden('pa_address1',$address_flag['pa']['address1'],['class'=>'js-address-address1']) !!}
            {!! Form::hidden('pa_city',$address_flag['pa']['city'],['class'=>'js-address-city']) !!}
            {!! Form::hidden('pa_state',$address_flag['pa']['state'],['class'=>'js-address-state']) !!}
            {!! Form::hidden('pa_zip5',$address_flag['pa']['zip5'],['class'=>'js-address-zip5']) !!}
            {!! Form::hidden('pa_zip4',$address_flag['pa']['zip4'],['class'=>'js-address-zip4']) !!}
            {!! Form::hidden('pa_is_address_match',$address_flag['pa']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
            {!! Form::hidden('pa_error_message',$address_flag['pa']['error_message'],['class'=>'js-address-error-message']) !!}

            <div class="form-group">
                {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('primary_add_1')) error @endif">
                    {!! Form::text('primary_add_1',null,['id'=>'primary_add_1','class'=>'form-control js-address-check dm-address']) !!}
                    {!! $errors->first('primary_add_1', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('primary_add_2')) error @endif">
                    {!! Form::text('primary_add_2',null,['id'=>'primary_add_2','class'=>'form-control js-address2-tab dm-address']) !!}
                    {!! $errors->first('primary_add_2', '<p> :message</p>')  !!}
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            
            
            <div class="form-group">
                {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('primary_city')) error @endif">  
                    {!! Form::text('primary_city',null,['class'=>'form-control js-letters-caps-format js-address-check dm-address','id'=>'primary_city']) !!}
                    {!! $errors->first('primary_city', '<p> :message</p>')  !!}
                    
                </div>
                {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 @if($errors->first('primary_state')) error @endif">
                    {!! Form::text('primary_state',null,['class'=>'form-control js-all-caps-letter-format js-address-check js-state-tab dm-state','id'=>'primary_state']) !!}
					{!! $errors->first('primary_state', '<p> :message</p>')  !!}
                </div>
            </div>   

            <div class="form-group">
                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('primary_zip')) error @endif">
                    {!! Form::text('primary_zip5',null,['class'=>'form-control js-address-check dm-zip5','id'=>'primary_zip5']) !!}
                    {!! $errors->first('primary_zip', '<p> :message</p>')  !!}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                    {!! Form::text('primary_zip4',null,['class'=>'form-control js-address-check dm-zip4','id'=>'primary_zip4']) !!}
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2">
                    <span class="add-on js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                     <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['pa']['is_address_match']); ?>   
                    <?php echo $value;?>  
                </div>
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->           
                            
    
    <div class="box no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="gear"></i> <h3 class="box-title">API Settings</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 js-address-class" id="js-address-primary-address">
           
            <div class="form-group">
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 @if($errors->first('api')) error @endif">
                    @foreach($apilist as $api_key=>$api_value)
                    @if(@$apilist_subcat->$api_value) 
                    <div class="js-each-api" data-id={{"js_".$api_value."_".$api_key }}>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                            
                            <p class='med-green font600 no-bottom'>
                                @if($api_value == 'apex' || $api_value == 'eligible')
                            {!! Form::radio('api_list', $api_value,null,['class'=>"js_menu js_menu_api  ". $api_value,'id'=>"js_".$api_value."_".$api_key]) !!} 
                            @else 
                            {!! Form::checkbox('api_list_check', $api_value,null,['class'=>'js_menu ','id'=>"js_".$api_value."_".$api_key]) !!} 
                            @endif
                               {!! Form::label("js_".$api_value."_".$api_key,  ucwords($api_value) ,['class'=>'med-darkgray font600 form-cursor']) !!}  
                                
                            </p>
                        </div>
                        <p style="" data-id="js_{{$api_value}}_{{$api_key}}" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " >
                            @foreach($apilist_subcat->$api_value as $sub_api_key=>$sub_api_value)
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 border-bottom-dotted">
                                <p class=''>
                                    {!! Form::checkbox('apilist[]', $sub_api_key, (in_array($sub_api_key,$setapi)?true:null), ["class" => "js_".$api_value."_".$api_key." js_submenu js_submenu_api ", 'data-id' =>$api_value,"id"=>$sub_api_key]) !!} 
                                    {!! Form::label($sub_api_key,  @$api_name[$sub_api_key] ,['class'=>'med-darkgray font600 form-cursor']) !!} 
                                </p>
                            </div>
                            @endforeach
                        </p>
                    </div>
                    @else
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <p class='font600 med-green border-bottom-dotted margin-t-5 p-b-5'>
                            {!! Form::checkbox('apilist[]', $api_key, (in_array($api_key,$setapi)?true:null), ["class" => " js-subselect", 'id' =>$api_key]) !!} 
                            {!! Form::label($api_key,  @$api_name[$api_key] ,['class'=>'med-darkgray font600 form-cursor']) !!} 
                        </p>
                    </div>
                    @endif
                    @endforeach
                    {!! $errors->first('api', '<p> :message</p>')  !!}
                </div>
                
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
    
    
    
    
</div><!--  Left side Content Ends -->

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-20"><!--  Right side Content Starts -->
            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="inbox"></i> <h3 class="box-title">Credentials</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10">

					<?php 
					$current_page = Route::getFacadeRoot()->current()->uri(); 
                    ?>
                    @if(strpos($current_page, 'edit') == false)
                         {!! Form::hidden('status','In Progress') !!}
                    @endif					
                    <div class="form-group">
                        {!! Form::label('EntityType', 'Entity Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 @if($errors->first('entity_type')) error @endif">
                            
							@if(strpos($current_page, 'edit') !== false)
								{!! Form::radio('entity_type', $practice->entity_type ,null,['class'=>'form-control js-entity-type', 'id'=>'entity_type_edit']) !!} {!! Form::label('entity_type_edit', $practice->entity_type,['class'=>'med-darkgray font600 form-cursor']) !!} 
							@else
                            {!! Form::radio('entity_type', 'Individual',true,['class'=>'form-control js-entity-type', 'id'=>'entity_type_ind']) !!} {!! Form::label('entity_type_ind', 'Individual',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; {!! Form::radio('entity_type', 'Group',null,['class'=>'form-control js-entity-type', 'id'=>'entity_type_g']) !!}{!! Form::label('entity_type_g', 'Group',['class'=>'med-darkgray font600 form-cursor']) !!} 
							@endif
							
                            {!! $errors->first('entity_type', '<p> :message</p>')  !!}
							
							
                        </div>                       
                    </div>

                    <?php 
                    $entity_type = 'Individual';
					?>
					
					@if(Request::old('entity_type') != '')
						<?php $entity_type  = Request::old('entity_type'); ?>
					@elseif(strpos($current_page, 'edit') !== false)
						<?php $entity_type  = $practice->entity_type; ?>
					@endif

                    <div id="js-individual-entity-type" @if($entity_type != 'Individual') class="hide" @endif>

                         <div class="form-group">
                            {!! Form::label('TaxID', 'Tax ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('tax_id')) error @endif">
                                {!! Form::text('tax_id',null,['class'=>'form-control dm-tax-id','id'=>'tax_id']) !!}

                                {!! $errors->first('tax_id', '<p> :message</p>')  !!}
                            </div>
                            </div>

                        <div class="form-group">
                            {!! Form::label('NPI', 'NPI', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('npi')) error @endif">
                                {!! Form::text('npi',null,['class'=>'form-control js-npi-check dm-npi','id'=>'npi',(@strpos($current_page, 'edit') !== false) ? 'readonly' : '']) !!}
                                {!! $errors->first('npi', '<p> :message</p>')  !!}
                            </div>
                            <div class="col-sm-1 col-xs-2">
                                <span class="js-npi-individual-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                 <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi'] , 'induvidual'); ?>   
                                 <?php echo $value;?>                            

                            </div>
                        </div>
                    </div>

                    <div id="js-group-entity-type" @if($entity_type != 'Group') class="hide" @endif>

                         <div class="form-group">
                            {!! Form::label('GroupTaxID', 'Group Tax ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('group_tax_id')) error @endif">
                                {!! Form::text('group_tax_id',null,['class'=>'form-control dm-tax-id','id'=>'group_tax_id']) !!}
                                {!! $errors->first('group_tax_id', '<p> :message</p>')  !!}
                            </div>
                            </div>

                        <div class="form-group">
                            {!! Form::label('GroupNPI', 'Group NPI', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('group_npi')) error @endif">
                                {!! Form::text('group_npi',null,['class'=>'form-control js-npi-check dm-npi','id'=>'group_npi']) !!}

                                {!! $errors->first('group_npi', '<p> :message</p>')  !!}
                            </div>
                            <div class="col-sm-1 col-md-1 col-xs-2">
								 <span class="js-npi-group-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                  <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi']); ?>   
                                 <?php echo $value;?> 
                            </div>
                        </div>
                    </div>

                    <div>
                        {!! Form::hidden('type','practice',['id'=>'type']) !!}
                        {!! Form::hidden('type_category',$entity_type,['id'=>'type_category']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('MedicarePTAN', 'Medicare PTAN', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('medicare_ptan')) error @endif">
                            {!! Form::text('medicare_ptan',null,['id'=>'medicare_ptan','class'=>'form-control dm-medicare']) !!}
                            {!! $errors->first('medicare_ptan', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <p id="ptan" class="emp"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('MedicaidID', 'Medicaid ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('medicaid')) error @endif">
                            {!! Form::text('medicaid',null,['class'=>'form-control dm-medicare']) !!}
                            {!! $errors->first('medicaid', '<p> :message</p>')  !!}
                        </div>
                        </div>

                    <div class="form-group no-bottom">
                        {!! Form::label('BCBS ID', 'BCBS ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('bcbs_id')) error @endif">
                            {!! Form::text('bcbs_id',null,['class'=>'form-control dm-medicare']) !!}
                            {!! $errors->first('bcbs_id', '<p> :message</p>')  !!}
                        </div>
                    </div>

                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->

            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="mail"></i> <h3 class="box-title">Mailing Address</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10 js-address-class" id="js-address-mailling-address">
                    {!! Form::hidden('ma_address_type','practice',['class'=>'js-address-type']) !!}
                    {!! Form::hidden('ma_address_type_category','mailling_address',['class'=>'js-address-type-category']) !!}
                    {!! Form::hidden('ma_address1',$address_flag['ma']['address1'],['class'=>'js-address-address1']) !!}
                    {!! Form::hidden('ma_city',$address_flag['ma']['city'],['class'=>'js-address-city']) !!}
                    {!! Form::hidden('ma_state',$address_flag['ma']['state'],['class'=>'js-address-state']) !!}
                    {!! Form::hidden('ma_zip5',$address_flag['ma']['zip5'],['class'=>'js-address-zip5']) !!}
                    {!! Form::hidden('ma_zip4',$address_flag['ma']['zip4'],['class'=>'js-address-zip4']) !!}
                    {!! Form::hidden('ma_is_address_match',$address_flag['ma']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                    {!! Form::hidden('ma_error_message',$address_flag['ma']['error_message'],['class'=>'js-address-error-message']) !!}


                    <div class="form-group">
                        {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('mail_add_1')) error @endif">
                            {!! Form::text('mail_add_1',null,['id'=>'mail_add_1','class'=>'form-control js-address-check dm-address']) !!}
                            {!! $errors->first('mail_add_1', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('mail_add_2')) error @endif">
                            {!! Form::text('mail_add_2',null,['id'=>'mail_add_2','class'=>'form-control js-address2-tab dm-address']) !!}
                            {!! $errors->first('mail_add_2', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>                    
                    
                    <div class="form-group">
                        {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('mail_city')) error @endif">  
                            {!! Form::text('mail_city',null,['class'=>'form-control js-letters-caps-format js-address-check dm-address','id'=>'mail_city']) !!}
                            {!! $errors->first('mail_city', '<p> :message</p>')  !!}
                            
                        </div>
                        {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 @if($errors->first('mail_state')) error @endif"> 
                            {!! Form::text('mail_state',null,['class'=>'form-control js-all-caps-letter-format js-address-check js-state-tab dm-state','id'=>'mail_state']) !!}
							{!! $errors->first('mail_state', '<p> :message</p>')  !!}
                        </div>
                    </div>   

                    <div class="form-group margin-b-20">
                        {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('mail_zip5')) error @endif">
                            {!! Form::text('mail_zip5',null,['class'=>'form-control js-address-check dm-zip5','id'=>'mail_zip5']) !!}
                            {!! $errors->first('mail_zip5', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                            {!! Form::text('mail_zip4',null,['class'=>'form-control js-address-check dm-zip4','id'=>'mail_zip4']) !!}
							{!! $errors->first('mail_zip4', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-md-1 col-sm-1 col-xs-2">
                            <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                              <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['ma']['is_address_match']); ?>   
                            <?php echo $value;?>                            
                        </div>
                    </div>

                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->


            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10">                   
                    <div class="form-group">
                        {!! Form::label('Primary Language', 'Primary Language',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                        <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                            <?php 
								$language_id  = $language_id=="" ? '5' : $language_id;
							?>
                            {!! Form::select('language_id', array(''=>'-- Select --')+(array)$languages,  $language_id,['class'=>'select2 form-control']) !!}
                        </div>                       
                    </div>                  
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
			
			<div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="globe"></i> <h3 class="box-title">Host credentials</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal margin-l-10">


                    <div class="form-group">
                        {!! Form::label('Host Username', 'Host Username', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}  
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('hostusername')) error @endif">                                                     
                            {!! Form::text('hostname',null,['maxlength'=>'15','class'=>'form-control']) !!}                           
                            {!! $errors->first('hostname', '<p> :message</p>')  !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('Host Password', 'Host Password', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}  
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('host_password')) error @endif">                                                     
                            {!! Form::text('hostpassword',null,['class'=>'form-control','maxlength'=>'15']) !!}                           
                            {!! $errors->first('hostpassword', '<p> :message</p>')  !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('Host IP', 'Host IP', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}  
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('hostip')) error @endif">                                                     
                            {!! Form::text('ipaddress',null,['class'=>'form-control']) !!}                           
                            {!! $errors->first('ipaddress', '<p> :message</p>')  !!}
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Right side Content Ends -->


        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
			 <a href="javascript:void(0)" data-url="{{ url('admin/customer/'. Request::segment(3) .'/customerpractices')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
        </div> 

<!-- Modal Light Box starts -->
<div id="form-content" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Doing Business As</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-list">

                    <li class="nav-header">Upload</li>
                    <li><input class="input-xlarge" value="" type="file" name="upload"></li>
                    <li class="nav-header">Message</li>
                    <li><textarea class="form-control" placeholder="Description">
                        </textarea></li>
                </ul>
            </div>
            <div class="modal-footer">
                <button class="btn btn-medcubics-small">Submit</button>

                <a href="#" class="btn btn-medcubics-small" data-dismiss="modal">Close</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<!-- Popup Content -->

<!-- Modal Light Box Address starts -->
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- Modal Light Box Ends -->

<!-- Popup Content -->

@include ('practice/layouts/npi_form_fields')
@include ('practice/layouts/npi_form_modal')
@push('view.scripts')
<script type="text/javascript">
		var base = '<?php echo url('/'); ?>';
		avator_url = base+"/img/noimage.jpg";
		$(document).on('change', '.btn-file input[type="file"]', function (e) {
		e.preventDefault();
		setTimeout(function(){
			var new_file = $(".fileupload").hasClass('fileupload-new'); 
			if(new_file) {
				$(".js-delete-confirm").addClass('hide'); 
			}
			else {
				$(".js-delete-confirm").removeClass('hide'); 
			}
		}, 10);
		
	});
	
	$(document).on('change', '.btn-file input[type="file"]', function (e) {
	if($(this).val() ==""){
		$(".fileupload.fileupload-exists .fileupload-preview").find("img").attr('src', $(".fileupload .js_default_img").attr('src'));
	}
		var img_file = $(this).val();
		img_file = img_file.split(".");
		var file_type = img_file[img_file.length-1];
		if((file_type !="jpg") || (file_type !="png") || (file_type !="jpeg") )
		{	
			$('.fileupload-preview').html('<img class="js_default_img" src="'+avator_url+'">');
			$( ".thumbnail .fileupload-preview" ).html();
			//$(".js_default_img").removeClass('hide')
		}
		e.preventDefault();
		setTimeout(function(){
			var new_file = $(".fileupload").hasClass('fileupload-new'); 
			var value = $(".fileupload").val();
			if(new_file) {
				$(".fileupload .js-delete-confirm").addClass('hide'); 
			}
			else {
				$(".fileupload .js-delete-confirm").removeClass('hide'); 
			}
		}, 50);
		
	});
	$(document).on('click', '.confirm', function (e) {
		if ($(this).text() == 'Yes') {
			var new_file = $(".fileupload").hasClass('fileupload-new');
			if(new_file) {
				$(".fileupload.fileupload-new img").attr('src', $(".fileupload .js_default_img").attr('src'));
				$(".fileupload .js-delete-confirm").addClass('hide'); 
				$(".safari_rounded img").addClass('default'); 
				$(".fileupload.fileupload-new .fileupload-preview").html('<input type="hidden" name="imagefile" value="" >');
			}
			else {
				if($(".safari_rounded img").hasClass('default')) {
					$(".fileupload .js-delete-confirm").addClass('hide'); 
					$(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
					$('[name="avatar_url"]').val("");
					$('.fileupload-preview').html('<img class="js_default_img" src="'+avator_url+'">');
				}
				else {
					$('[name="avatar_url"]').val("");
					$(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
				}
				
				$(".fileupload fileupload-preview.fileupload-exists.thumbnail").html('<img class="js_default_img" src="'+avator_url+'">');
			}
		}
	});
	$(document).on('ifToggled change', 'input[name="entity_type"]:checked', function () {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="tax_id"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="npi"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="group_tax_id"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="group_npi"]'));
	});
	$(document).ready(function () {
		$('#js-bootstrap-validator')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    excluded: ':disabled',
                    feedbackIcons: {
                        valid: '',
                        invalid: '',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
						 practice_name: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("admin/practice.validation.practice_name") }}'
                                },
								callback: {
									message: '',
									callback: function (value, validator) {
										var regexp =  new RegExp(/^[A-Za-z ]+$/);
										var regexp_msg =  '{{ trans("common.validation.alphaspace") }}';
										var msg = lengthValidation(value,'practice_name',regexp,regexp_msg);
										if(value.length >0 && msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
						practice_description: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("common.validation.description") }}'
                                }
                            }
                        },
						phone: {
                            message: '',
                            trigger: 'keyup change',
                            validators: {
                                callback: {
									message:'',
									callback: function (value, validator,$field) {
										var phone_msg = '{{ trans("common.validation.phone_limit") }}';
										var ext_msg = '{{ trans("common.validation.phone") }}';
										var ext_length = validator.getFieldElements('phone').closest("div.form-group").find("input:last.dm-phone-ext").val().length;
										var response = phoneValidation(value,phone_msg,ext_length,ext_msg);
										if(response !=true) {
											return {
												valid: false, 
												message: response
											};
										}
										return true;
									}
								}
                            }
                        },
                        fax: {
                            message: '',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                            var fax_msg = '{{ trans("common.validation.fax_limit") }}';
											var response = phoneValidation(value,fax_msg);
											if(response !=true) {
												return {
													valid: false, 
													message: response
												};
											}
											return true;
                                        }
                                }
                            }
                        },
						email:{
							message:'',
							validators:{
								callback: {
									message: '',
									callback: function (value, validator) {
										var response = emailValidation(value);
										if(response !=true) {
											return {
												valid: false, 
												message: response
											};
										}
										return true;
									}
								}
							}
						},
						website: {
							message: '',
							validators: {
							   callback: {
									message: '',
									callback: function (value, validator) {
										var website_valid = '{{ trans("common.validation.website_valid") }}';
										var regex = new RegExp(/^(http(s)?:\/\/)?(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/);
										var msg = lengthValidation(value,'feeschedule',regex,website_valid);
										if(value.length>0 && msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
								
							}
						},
                        facebook: {
                            message: '',
                            validators: {
                               callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var website_valid = '{{ trans("admin/practice.validation.facebook_name") }}';
                                        var regex = new RegExp(/^(http(s)?:\/\/)?(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/);
                                        var msg = lengthValidation(value,'feeschedule',regex,website_valid);
                                        if(value.length>0 && msg != true){
                                            return {
                                                valid: false,
                                                message: msg
                                            };
                                        }
                                        return true;
                                    }
                                }
                                
                            }
                        },
                        twitter: {
                            message: '',
                            validators: {
                               callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var website_valid = '{{ trans("admin/practice.validation.twitter_name") }}';
                                        var regex = new RegExp(/^(http(s)?:\/\/)?(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/);
                                        var msg = lengthValidation(value,'feeschedule',regex,website_valid);
                                        if(value.length>0 && msg != true){
                                            return {
                                                valid: false,
                                                message: msg
                                            };
                                        }
                                        return true;
                                    }
                                }
                                
                            }
                        },
                        doing_business_s: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/practice.validation.doingbusinessus") }}'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9 _.]+$/,
                                    message: '{{ trans("practice/practicemaster/practice.validation.alphanumericspacedot") }}'
                                },
								callback: {
                                    message: '{{ trans("practice/practicemaster/practice.validation.doingbusinessus_limit") }}',
                                    callback: function (value, validator) {
                                       return (value.length>100) ? false : true;
									}
								}
                            }
                        },
                        speciality_id: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/practice.validation.speciality") }}'
                                },
                            }
                        },
                        taxanomy_id: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/practice.validation.taxanomy") }}'
                                }
                            }
                        },
                        billing_entity: {
                            message: '',
                            trigger:"change ",
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/practice.validation.billing_entity") }}'
                                }
                            }
                        },
                        timezone: {
                            message: '',
                            trigger:"change ",
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/practice.validation.timezone") }}'
                                }
                            }
                        },
                        entity_type: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/practicemaster/practice.validation.entity_type") }}'
                                }
                            }
                        },
                        tax_id: {
                            trigger: 'change keyup',
                            validators: {
                                callback: {
                                    message: '{{ trans("practice/practicemaster/practice.validation.taxid") }}',
                                    callback: function (value, validator) {
										var entity_type = $('#js-bootstrap-validator').find('[name="entity_type"]:checked').val();
                                        if (entity_type == 'Individual') {
                                            if (value == "") {
                                                return false;
                                            }
                                            else if (value.search("[0-9]{9}") == -1) {
                                                return false;
                                            }
                                        }
                                        else {
                                            return true;
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        npi: {
                            trigger: 'change keyup',
                            validators: {
                                callback: {
                                    message: '{{ trans("common.validation.npi") }}',
                                    callback: function(value, validator) {
											var entity_type = $('#js-bootstrap-validator').find('[name="entity_type"]:checked').val();
											if (entity_type == 'Individual'){
												if (value == ""){
													$('input[type=hidden][name="valid_npi_bootstrap"]').val('');
													return {
														valid: false,
														message: '{{ trans("common.validation.npi") }}'
													};
												}
												else if (value.search("[0-9]{10}") == -1){
													$('input[type=hidden][name="valid_npi_bootstrap"]').val('');
													return {
														valid: false,
														message: '{{ trans("common.validation.npi_regex") }}'
													};
												}
												else{
													if($('input[type=hidden][name="valid_npi_bootstrap"]').val()!=''){
														return {
															valid: false,
															message: '{{ trans("common.validation.npi_validcheck") }}'
														};
													}
												}
											}
											else{
												return true;
											}
									        return true;
                                        }
                                }
                            }
                        },
                        group_tax_id: {
                            trigger: 'change keyup',
                            validators: {
                                callback: {
                                    message: '{{ trans("practice/practicemaster/practice.validation.taxid") }}',
                                    callback: function (value, validator) {
                                        var entity_type = $('#js-bootstrap-validator').find('[name="entity_type"]:checked').val();
                                        if (entity_type == 'Group') {
                                            if (value == "") {
                                                return false;
                                            }
                                            else if (value.search("[0-9]{9}") == -1) {
                                                return true;
                                            }
                                        }
                                        else {
                                            return true;
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        group_npi: {
                            trigger: 'change keyup',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var entity_type = $('#js-bootstrap-validator').find('[name="entity_type"]:checked').val();
                                        if (entity_type == 'Group') {
                                            if (value == "") {
                                                $('input[type=hidden][name="valid_npi_bootstrap"]').val('');
                                                return {
                                                    valid: false,
                                                    message: '{{ trans("common.validation.npi") }}'
                                                };
                                            }
                                            else if (value.search("[0-9]{10}") == -1) {
                                                $('input[type=hidden][name="valid_npi_bootstrap"]').val('');
                                                return {
                                                    valid: false,
                                                    message: '{{ trans("common.validation.npi_regex") }}'
                                                };
                                            }
                                            else {
                                                if ($('input[type=hidden][name="valid_npi_bootstrap"]').val() != '') {
                                                    return {
                                                        valid: false,
                                                        message: '{{ trans("common.validation.npi_validcheck") }}'
                                                    };
                                                }
                                            }
                                        }
                                        else {
                                            return true;
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        medicare_ptan: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]{0,15}$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                }
                            }
                        },
                        medicaid: {
                            message: '',
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]{0,15}$/,
                                    message: '{{ trans("common.validation.alphanumeric") }}'
                                }
                            }
                        },
                        pay_add_1: {
                            message: '',
                            validators: {
                               callback: {
									message: '',
									callback: function (value, validator) {
										var msg = addressValidation(value,"required");
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        }, 
						pay_add_2: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = addressValidation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
                        pay_city: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = cityValidation(value,"required");
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
                        pay_state: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = stateValidation(value,"required");
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
                        pay_zip5: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = zip5Validation(value,"required");
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
						pay_zip4: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = zip4Validation(value,"required");
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
                        mail_add_1: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = addressValidation(value,"required");
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
						mail_add_2: {
                            message: '',
                            validators: {
                               callback: {
									message: '',
									callback: function (value, validator) {
										var msg = addressValidation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
                        mail_city: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = cityValidation(value,"required");
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
                        mail_state: {
                            message: '',
                            validators: {
                                 callback: {
									message: '',
									callback: function (value, validator) {
										var msg = stateValidation(value,"required");
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
                        mail_zip5: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = zip5Validation(value,"required");
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
						mail_zip4: {
                            message: '',
                            validators: {
								callback: {
									message: '',
									callback: function (value, validator) {
										var msg = zip4Validation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
						language_id: {
							message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("common.validation.language") }}'
                                }
                            }
						},
						primary_add_1:{
                            message: '',
                            validators: {
                               callback: {
									message: '',
									callback: function (value, validator) {
										var msg = addressValidation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
						primary_add_2: {
                            message: '',
                            validators: {
                               callback: {
									message: '',
									callback: function (value, validator) {
										var msg = addressValidation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
						primary_city: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = cityValidation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
						primary_state: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = stateValidation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
						primary_zip5: {
                            message: '',
                            validators: {
                                callback: {
									message: '',
									callback: function (value, validator) {
										var msg = zip5Validation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
                        primary_zip4: {
                            message: '',
                            validators: {
								callback: {
									message: '',
									callback: function (value, validator) {
										var msg = zip4Validation(value);
										if(msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                            }
                        },
                        ipaddress: {
                            validators: {
                                regexp: {
                                    regexp: /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/,
                                    message: '{{ trans("admin/practice.validation.host_ip") }}'
                                },                                                              
                            }            
                        },
                    }
                });

    });
</script> 
@endpush