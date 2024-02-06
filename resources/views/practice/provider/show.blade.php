@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            
            @if($checkpermission->check_url_permission('provider/create') == 1)
            <li class=""><a href="{{ url('provider/create') }}" class="" accesskey="n"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="New Provider"></i></a></li>
            @endif
            <li><a href="{{ url('provider') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0)" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice-info')
@include ('practice/provider/tabs')
@stop
@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
    @if($checkpermission->check_url_permission('provider/{provider}/edit') == 1)
    <a href="{{ url('provider/'.$provider->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif
</div>

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->

    <div class="box no-shadow margin-b-10"><!-- Personal Information Box Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="address-book"></i> <h3 class="box-title">Personal Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 p-b-20"><!-- Box Body Starts -->

            <div class="form-group">
                {!! Form::label('ProviderType', 'Provider Type', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$provider->provider_type_details->name }}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>


            <div class="form-group margin-b-20">
                {!! Form::label('ProviderDOB', 'DOB', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 ">
                    <p class="show-border no-bottom">
					{{ ($provider->provider_dob!='0000-00-00' && $provider->provider_dob!='')?App\Http\Helpers\Helpers::dateFormat($provider->provider_dob,'dob') : ''}}
					</p>
                </div>                        
            </div>

            <div class="form-group margin-b-20">
                {!! Form::label('Gender', 'Gender', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-7">
				@if($provider->gender == 'Male')
                    {!! Form::radio('gender', 'Male',true,['id'=>'gender_m','class'=>'flat-red']) !!} Male &emsp; {!! Form::radio('gender', 'Female',null,['id'=>'gender_f','class'=>'flat-red','disabled']) !!} Female &emsp; 
                    {!! Form::radio('gender', 'Others',null,['id'=>'gender_o','class'=>'flat-red','disabled']) !!} Others
				@elseif($provider->gender == 'Female')	
					{!! Form::radio('gender', 'Male',null,['id'=>'gender_m','class'=>'flat-red','disabled']) !!} Male &emsp; {!! Form::radio('gender', 'Female',true,['id'=>'gender_f','class'=>'flat-red']) !!} Female &emsp; 
                    {!! Form::radio('gender', 'Others',null,['id'=>'gender_o','class'=>'flat-red','disabled']) !!} Others
				@else	
					<!--Change the male value as null and disabled -->
                    {!! Form::radio('gender', 'Male',null,['id'=>'gender_m','class'=>'flat-red','disabled']) !!} Male &emsp; {!! Form::radio('gender', 'Female',null,['id'=>'gender_f','class'=>'flat-red','disabled']) !!} Female &emsp; 
					<!--Change the others value as true-->
                    {!! Form::radio('gender', 'Others',true,['id'=>'gender_o','class'=>'flat-red']) !!} Others
                @endif    
					{!! $errors->first('gender', '<p> :message</p>')  !!}
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('SSN', 'SSN ', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->ssn }}</p>
                </div>
                <div class="col-sm-1 col-xs-2">
                    <a id="document_add_modal_link_ssn" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/personal_ssn')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_personal_ssn->category)?'icon-orange-attachment':'icon-green-attachment') ?>" ></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Degree', 'Degree', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$provider->degrees->degree_name }}</p>
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('Job Title', 'Job Title', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->job_title }}</p>
                </div>                        
            </div>
            <div class=" js-address-class" id="js-address-general-address"><!-- Address Div Starts -->
                {!! Form::hidden('general_address_type','employer',['class'=>'js-address-type']) !!}
                {!! Form::hidden('general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                {!! Form::hidden('general_address_type_category','general_information',['class'=>'js-address-type-category']) !!}
                {!! Form::hidden('general_address1',$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                {!! Form::hidden('general_city',$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                {!! Form::hidden('general_state',$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                {!! Form::hidden('general_zip5',$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                {!! Form::hidden('general_zip4',$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                {!! Form::hidden('general_is_address_match',$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                {!! Form::hidden('general_error_message',$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}

                <div class="form-group">
                    {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                        <p class="show-border no-bottom">{{ $provider->address_1 }}</p>
                    </div>                           
                </div>

                <div class="form-group">
                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                        <p class="show-border no-bottom">{{ $provider->address_2 }}</p>
                    </div>                           
                </div>


                <div class="form-group">
                    {!! Form::label('City / State', 'City ', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                        <p class="show-border no-bottom">{{ $provider->city }}</p>
                    </div>
                    {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                        <p class="show-border no-bottom">{{ $provider->state }}</p>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
                        <p class="show-border no-bottom">{{ $provider->zipcode5 }}</p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-4">
                        <p class="show-border no-bottom">{{ $provider->zipcode4 }}</p>
                    </div>
                    <div class="col-md-1 col-sm-2 col-xs-2">
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
                        <?php echo $value; ?>     
                    </div>
                </div>
                <div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>
                <div class="margin-b-23 hidden-sm hidden-xs">&emsp;</div>
                
            </div><!-- Address Div Ends -->
        </div><!-- /.box-body -->
    </div><!-- Personal Information Box Ends -->



    <div class="box no-shadow margin-b-10"><!-- Credentials Box Starts -->
        <div class="box-block-header with-border margin-b-10">
            <i class="livicon" data-name="shield"></i> <h3 class="box-title">Enrollment Status</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 p-b-25"><!-- Box Body Starts -->
            <div class="form-group">
                {!! Form::label('Medicare PTAN', 'Medicare PTAN', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->medicareptan }}</p>
                </div>
                <div class="col-sm-1 col-xs-2">
                    <a id="document_add_modal_link_medicare_ptan" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/medicare_ptan')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_PTAN->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Medicaid ID', 'Medicaid ID', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->medicaidid }}</p>
                </div>
                <div class="col-sm-1 col-xs-2">
                    <a id="document_add_modal_link_medicaid_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/medicaid_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" ><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_medicaid_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('BCBS_ID ', 'BCBS ID',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->bcbsid }}</p>                  
                </div>
                <div class="col-sm-1 col-xs-2">
                    <a id="document_add_modal_link_bcbs_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/bcbs_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_bcbs_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Aetna_ID', 'Aetna ID',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->aetnaid }}</p>
                </div>
                <div class="col-sm-1 col-xs-2">
                    <a id="document_add_modal_link_aetna_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/aetna_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" ><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_aetna_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('UHC_ID', 'UHC ID', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->uhcid }}</p>
                </div>
                <div class="col-sm-1 col-xs-2">
                    <a id="document_add_modal_link_uhc_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/uhc_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_uhc_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Other ID', 'Other ID 1', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->otherid }}</p>
                </div>

                {!! Form::label('Ins Type', 'Ins', ['class'=>'col-lg-1 col-md-4 col-sm-1 col-xs-12 m-t-md-5 control-label']) !!}
                <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10 m-t-md-5">
                    <p class="show-border no-bottom">{{ str_limit(App\Models\Insurance::getInsuranceName($provider->otherid_ins), 25, '...') }}</p>
                </div>
                <div class="col-sm-1 col-xs-2 m-t-md-5 p-l-0">
                    <a id="document_add_modal_link_other_id1" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/other_id1')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_other_id1->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>                        
            </div>

            <div class="form-group">
                {!! Form::label('Other ID', 'Other ID 2', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12  control-label']) !!}
                <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->otherid2 }}</p>
                </div>
                {!! Form::label('Ins Type', 'Ins', ['class'=>'col-lg-1 col-md-4 col-sm-1 col-xs-12 m-t-md-5 control-label']) !!}
                <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10 m-t-md-5">
                    <p class="show-border no-bottom">{{ str_limit(App\Models\Insurance::getInsuranceName($provider->otherid_ins2), 25, '...') }}</p>
                </div>
                <div class="col-sm-1 col-xs-2 m-t-md-5 p-l-0">
                    <a id="document_add_modal_link_other_id2" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/other_id2')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_other_id2->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Other ID', 'Other ID 3', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->otherid3 }}</p>
                </div>
                {!! Form::label('Ins Type', 'Ins', ['class'=>'col-lg-1 col-md-4 col-sm-1 col-xs-12 m-t-md-5 control-label']) !!}
                <div class="col-lg-3 col-md-7 col-sm-3 col-xs-10 m-t-md-5">
                    <p class="show-border no-bottom">{{ str_limit(App\Models\Insurance::getInsuranceName($provider->otherid_ins3), 25, '...') }}</p>
                </div>
                <div class="col-sm-1 col-xs-2 m-t-md-5 p-l-0">
                    <a id="document_add_modal_link_other_id3" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/other_id3')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_other_id3->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

<div class="col-lg-6 col-md-6 col-xs-12"><!--  Right side Content Starts -->
    <div class="box no-shadow margin-b-10"><!-- Professional Identifications Box Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="medal"></i> <h3 class="box-title">Professional Identifications</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 p-b-12"><!-- Box Body Starts -->
            <div class="form-group">
			<!-- change the value of SSN and TAX ID -->
                {!! Form::label('etin_type', 'ETIN Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-2 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
				@if($provider->etin_type == 'SSN')
                    {!! Form::radio('etin_type', 'SSN',true,['class'=>'flat-red etin_type','id'=>'etin_ssn']) !!} SSN &emsp; {!! Form::radio('etin_type', 'TAX ID',null,['class'=>'flat-red etin_type','id'=>'etin_tax','disabled']) !!} TAX ID
				@else
					{!! Form::radio('etin_type', 'SSN',null,['class'=>'flat-red etin_type','id'=>'etin_ssn','disabled']) !!} SSN &emsp; {!! Form::radio('etin_type', 'TAX ID',true,['class'=>'flat-red etin_type','id'=>'etin_tax']) !!} TAX ID
				
				@endif	
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('etin_type_number', 'SSN or TAX ID',['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->etin_type_number }}</p>
                </div>
                <div class="col-sm-1 col-xs-2">
                    @if($provider->etin_type == 'SSN') 
                    <a id="document_add_modal_link_ssn" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/professional_ssn')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                    @else
                    <a id="document_add_modal_link_tax_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/tax_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_tax_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                    @endif
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Specialty 1', 'Specialty 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$provider->speciality->speciality }}</p>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Taxonomy 1', 'Taxonomy 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$provider->taxanomy->code }}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('Specialty 2', 'Specialty 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$provider->speciality2->speciality }}</p>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Taxonomy 2', 'Taxonomy 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$provider->taxanomy2->code }}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>


            <div class="form-group">
                {!! Form::label('State License', 'State License 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                    <p class="show-border no-bottom">{{ $provider->statelicense }}</p>
                </div>
                {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    <p class="show-border no-bottom">{{ $provider->state_1 }}</p>
                </div>
                <div class="col-sm-1 col-xs-2 p-l-0">
                    <a id="document_add_modal_link_state_license1" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/state_license1')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_state_license1->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('State License', 'State License 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                    <p class="show-border no-bottom">{{ $provider->statelicense_2 }}</p>
                </div>
                {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    <p class="show-border no-bottom">{{ $provider->state_2 }}</p>
                </div>
                <div class="col-sm-1 col-xs-2 p-l-0">
                    <a id="document_add_modal_link_state_license1" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/state_license2')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_state_license2->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('State License', 'State License 3', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                    <p class="show-border no-bottom">{{ $provider->specialitylicense }}</p>
                </div>
                {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    <p class="show-border no-bottom">{{ $provider->state_speciality }}</p>
                </div>
                <div class="col-sm-1 col-xs-2 p-l-0">
                    <a id="document_add_modal_link_state_license1" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/state_license3')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_state_license3->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('DEA Number', 'DEA Number', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                    <p class="show-border no-bottom">{{ $provider->deanumber }}</p>
                </div>
                {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    <p class="show-border no-bottom">{{ $provider->state_dea }}</p>
                </div>
                <div class="col-sm-1 col-xs-2 p-l-0">
                    <a id="document_add_modal_link_dea_number" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/dea_number')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_dea_number->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('TAT', 'TAT', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->tat }}</p>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('MammographyCert#', 'Mammography Cert#', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->mammography }}</p>
                </div>
                <div class="col-sm-1 col-xs-2 p-l-0">
                    <a id="document_add_modal_link_mammography_cert" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/mammography_cert')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_mammography_cert->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('CarePlanOversight#', 'Care Plan Oversight#', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->careplan }}</p>
                </div>
                <div class="col-sm-1 col-xs-2 p-l-0">
                    <a id="document_add_modal_link_care_plan" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider/'.$provider->id.'/care_plan')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_care_plan_oversight->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
    <div class="box no-shadow margin-b-10"><!-- General Information Box Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10 p-b-20"><!-- Box Body Starts -->
            <div class="form-group margin-b-20">
                {!! Form::label('Requires Supervision', 'Requires Supervision', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
				@if($provider->req_super == 'Yes')
                    {!! Form::radio('req_super', 'Yes',true,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('req_super', 'No',null,['class'=>'flat-red','disabled']) !!} No
				@else	
					{!! Form::radio('req_super', 'Yes',null,['class'=>'flat-red','disabled']) !!} Yes &emsp; {!! Form::radio('req_super', 'No',true,['class'=>'flat-red']) !!} No
				@endif	
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Default_Facility', 'Default Facility', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-5 col-md-6 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$provider->facility_details->facility_name }}</p>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('Statement Address', 'Statement Address', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-5 col-md-6 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ $provider->stmt_add }}</p>
                </div>
            </div>

            <div class="form-group margin-b-20">
                {!! Form::label('Hospice_Employed', 'Hospice Employed', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
				@if($provider->hospice_emp != 'Yes')
                    {!! Form::radio('hospice_emp', 'Yes',null,['class'=>'flat-red','disabled']) !!} Yes &emsp; {!! Form::radio('hospice_emp', 'No',true,['class'=>'flat-red']) !!} No
				@else
					{!! Form::radio('hospice_emp', 'Yes',true,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('hospice_emp', 'No',null,['class'=>'flat-red','disabled']) !!} No
				@endif	
                </div>
            </div>
            <div class="form-group margin-b-15">
                {!! Form::label('status', 'Status',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
				@if($provider->status == 'Active')
                    {!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp; <?php /*{!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','disabled']) !!} Inactive*/ ?>
				@else	
					{!! Form::radio('status', 'Active',null,['class'=>'flat-red','disabled']) !!} Active &emsp; <?php /*{!! Form::radio('status', 'Inactive',true,['class'=>'flat-red']) !!} Inactive  */ ?>
				@endif	
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-upload margin-t-10">
                    {!! Form::label('Digital_Signature', 'Digital Signature',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                    <div class="col-lg-6 col-md-6 col-sm-7 col-xs-6 no-padding @if($errors->first('filefield')) error @endif">
                          @if($provider->digital_sign_name!='')
                            <a data-toggle="modal" href="#image-content">{!! HTML::image('img/preview.png') !!}  </a>
                            @endif  
                    </div>
                </div>
            </div>

            <div class="bottom-space-10 hidden-sm hidden-xs">&emsp;</div>
            <div class="margin-b-15 hidden-sm hidden-xs">&emsp;</div>

        </div><!-- /.box-body -->
    </div><!-- General Information box Ends-->
</div><!--  Right side Content Ends -->

<!-- Modal Light Box starts -->
<div id="form-pta-address-modal" class="modal fade in">
    @include ('practice/layouts/usps_form_modal')
</div><!-- Modal Light Box Ends -->

<div id="image-content" class="modal fade in">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Digital Signature</h4>
            </div>

            <div class="modal-body">
                <center>
                    <?php
						$filename = $provider->digital_sign_name . '.' . $provider->digital_sign_ext;
						$img_details = [];
						$img_details['module_name']='provider';
						$img_details['file_name']=$filename;
						$img_details['practice_name']="";
						
						$img_details['class']='img-responsive';
						$img_details['alt']='provider-image';
						$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
					?>
                    {!! $image_tag !!}</center>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include ('practice/layouts/usps_form_modal')
</div><!-- Modal Light Box Ends --> 
@include ('practice/layouts/npi_form_modal')
<!--End-->
@stop