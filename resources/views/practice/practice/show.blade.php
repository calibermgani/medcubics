@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa font14 {{Config::get('cssconfigs.Practicesmaster.practice')}}"></i> Practice</small>
        </h1>
        <ol class="breadcrumb">          
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
	{!! Form::model($practice, ['method'=>'PATCH','name'=>'myform','files'=>true,'url'=>'practice/'.$practice->id]) !!}  
	@include ('practice/practice/practice-tabs')
@stop

@section('practice')

<?php 
	$provider_count = App\Models\Practice::getProviderCount($practice->id); 
	$facility_count = App\Models\Practice::getFacilityCount($practice->id);
?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">  
    @if($checkpermission->check_url_permission('contactdetail/{contactdetail}/edit') == 1)
        <a href="{{ url('practice/'.$practice->id.'/edit') }}" class=" pull-right font14 font600 margin-r-5 hidden-print"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif
</div>
     
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10"><!-- Statistics Starts here -->
        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
            <div class="med-statistic" >
                {!! HTML::image('img/stat-provider.png') !!}
            </div>
            <h4 class="counts med-orange font20">{{$provider_count}} </h4>
            <h4 class="med-stat-title">No. Providers</h4>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
            <div class="med-statistic" >
                {!! HTML::image('img/stat-facility.png') !!}
            </div>
            <h4 class="counts med-orange font20">{{$facility_count}} </h4>
            <h4 class="med-stat-title">No. Facilities</h4>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
            <div class="med-statistic" >
                {!! HTML::image('img/content-icon3.png') !!}
            </div>
            <h4 class="counts med-orange font20">{{App\Models\Practice::getPatientrCount($practice->id)}} </h4>
            <h4 class="med-stat-title">No. Patients</h4>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
            <div class="med-statistic" >
                {!! HTML::image('img/content-icon1.png') !!}
            </div>
            <h4 class="counts med-orange font20">{{App\Models\Practice::getPracticeUserCount($practice->id)}} </h4>
            <h4 class="med-stat-title">No. Users</h4>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
            <div class="med-statistic" >
                {!! HTML::image('img/claim-icon1.png') !!}
            </div>
            <h4 class="counts med-orange font20">{!! App\Models\Practice::getPatientOSamount() !!} </h4>
            <h4 class="med-stat-title">Patient AR</h4>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
            <div class="med-statistic" >
                {!! HTML::image('img/content-icon5.png') !!}
            </div>
            <h4 class="counts med-orange font20">{!! App\Models\Practice::getInsOSamount() !!}</h4>
            <h4 class="med-stat-title">Insurance AR</h4>
        </div>
    </div><!-- Statistics Ends here -->
    <?php $practiceid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice->id,'decode'); ?>
        
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->            
        <div class="box no-shadow margin-b-10"><!-- Business Info Box Starts -->
            
            <div class="box-block-header with-border" >
                <i class="livicon" data-name="briefcase"></i> <h3 class="box-title"> Business  Details</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            
            <div class="box-body form-horizontal margin-l-10 p-b-20"><!-- Box body Starts -->                                                 
                <div class="form-group">
                    {!! Form::label('DoingBusinessAs', 'Doing Business As', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">  
                        <p class="show-border no-bottom">{{ $practice->doing_business_s }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2">
                        <a tabindex="-1" id="document_add_modal_link_doing_business_as" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/practice/'.$practice->id.'/doing_business_as')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"></a>
                    </div>
                </div>                                                                               

                <div class="form-group">
                    {!! Form::label('Specialty', 'Specialty', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ $practice->speciality_details->speciality }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('Taxonomy', 'Taxonomy',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ @$practice->taxanomy_details->code }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('Billing Entity', 'Billing Entity', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        {{ $practice->billing_entity }}
					</div> 
                </div>
				
                <div class="form-group">
                    {!! Form::label('EntityType', 'Entity Type', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                        {{ $practice->entity_type }}
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>   
            </div><!-- /.box-body -->
        </div><!-- Business Info Box Ends -->

        <div class="box no-shadow margin-b-10"><!-- Pay to Address Box Starts -->
          
            <div class="box-block-header with-border">
                <i class="livicon" data-name="message-out"></i> <h3 class="box-title"> Pay to Address</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            
            <div class="box-body form-horizontal js-address-class margin-l-10  p-b-20" id="js-address-pay-to-address"><!-- Box Body Starts -->
            
			{!! Form::hidden('pta_address_type','practice',['class'=>'js-address-type']) !!}
            {!! Form::hidden('pta_address_type_id',$practice->id,['class'=>'js-address-type-id']) !!}
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
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ $practice->pay_add_1 }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ $practice->pay_add_2 }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                        <p class="show-border no-bottom">{{ $practice->pay_city }}</p>
                    </div>
                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                        <p class="show-border no-bottom">{{ $practice->pay_state }}</p>
                    </div>
                </div>   

                <div class="form-group">
                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                        <p class="show-border no-bottom">{{ $practice->pay_zip5 }}</p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4"> 
                        <p class="show-border no-bottom">{{ $practice->pay_zip4 }} </p>
                    </div>
                    <div class="col-md-1 col-sm-1 col-xs-2" tabindex="-1">            
                         <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['pta']['is_address_match'], 'show'); ?>   
                         <p class=" margin-t-13"> <?php echo $value; ?> </p> 
                    </div> 
                </div>
                
            </div><!-- /.box-body ends -->
        </div><!-- Pay to Address Box Ends -->

        <div class="box no-shadow"><!-- Primary Location Box Starts -->
           
            <div class="box-block-header with-border">
                <i class="livicon" data-name="mail"></i> <h3 class="box-title"> Primary Location</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            
            <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
				{!! Form::hidden('pa_address_type','practice',['class'=>'js-address-type']) !!}
				{!! Form::hidden('pa_address_type_id',$practice->id,['class'=>'js-address-type-id']) !!}
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
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ $practice->primary_add_1 }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ $practice->primary_add_2 }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                        <p class="show-border no-bottom">{{ $practice->primary_city }}</p>
                    </div>
                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                        <p class="show-border no-bottom">{{ $practice->primary_state }}</p>
                    </div>
                </div>   

                <div class="form-group">
                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 ">  
                        <p class="show-border no-bottom">{{ $practice->primary_zip5 }} </p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4"> 
                        <p class="show-border no-bottom">{{ $practice->primary_zip4 }}</p>
                    </div>
                    <div class="col-md-1 col-sm-1 col-xs-2">            
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['pa']['is_address_match'], 'show'); ?>   
                        <p class=" margin-t-13"><?php echo $value; ?>  </p>                       
                    </div>
                </div>  
                <div class="form-group">
                {!! Form::label('timezone', 'Time Zone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-8 col-xs-12"> 
                    <p class="show-border no-bottom">{{ @$practice->timezone }}</p>                                      
                </div>
            </div>   
            </div><!-- /.box-body -->
        </div><!-- Primary Location box Ends-->
    </div><!--  Left side Content Ends -->       
          
       
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20"><!--  Right side Content Starts -->
        <div class="box no-shadow margin-b-10"><!--  Credentials Box Starts -->                
            
            <div class="box-block-header with-border">
                <i class="livicon" data-name="shield"></i> <h3 class="box-title"> Credentials</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            
            <div class="box-body form-horizontal margin-l-10"><!-- Box Body Starts -->
                                                                     
                @if($practice->entity_type == 'Individual')
                    <div class="form-group">
                        {!! Form::label('TaxID', 'Tax ID', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                           <p class="show-border no-bottom">{{ $practice->tax_id }}</p>
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <a tabindex ="-1" id="document_add_modal_link_tax_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/practice/'.$practice->id.'/tax_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"></a>
                        </div>
                    </div>       

                    <div class="form-group">
                        {!! Form::label('NPI', 'NPI', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                            <p class="show-border no-bottom">{{ $practice->npi }}</p>
                        </div>
                        
                         <div class="col-sm-1 col-xs-2">
                            <a tabindex ="-1" id="document_add_modal_link_npi" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/practice/'.$practice->id.'/npi')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"></a>
						</div>
                        <div class="col-sm-1">
                            <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi'],'induvidual'); ?>
                            <?php echo $value; ?>
                        </div>
                    </div>   
                @else
                     <div class="form-group">                   
                        {!! Form::label('GroupTaxID', 'Group Tax ID', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                        
                        <div class="col-lg-4 col-md-6 col-sm-4 col-xs-10">
                           <p class="show-border no-bottom">{{ $practice->group_tax_id }}</p>
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <a tabindex ="-1" id="document_add_modal_link_group_tax_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/practice/'.$practice->id.'/group_tax_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"></a>
                        </div>
                    </div>  

                    <div class="form-group">                   
                        {!! Form::label('GroupNPI', 'Group NPI', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                      
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10 ">
                            <p class="show-border no-bottom">{{ $practice->group_npi }}</p>
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <a tabindex ="-1" id="document_add_modal_link_group_npi" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/practice/'.$practice->id.'/group_npi')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"></a>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-1 col-xs-2">
                            <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi']); ?>
                            <p class=" margin-t-13"><?php echo $value; ?> </p>                             
                        </div>
                    </div>                                                   
                
                @endif

                <div>                  
                </div>

                <div class="form-group">
                    {!! Form::label('MedicarePTAN', 'Medicare PTAN', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">
                       <p class="show-border no-bottom">{{ $practice->medicare_ptan }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2">
                        <a tabindex ="-1" id="document_add_modal_link_medicare_ptan" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/practice/'.$practice->id.'/medicare_ptan')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"></a>
                        <p id="ptan" class="emp"></p>
                    </div>
                </div> 

                <div class="form-group">
                    {!! Form::label('Medicaid', 'Medicaid ID',['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                    
                      <p class="show-border no-bottom">{{ $practice->medicaid }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2">
                        <a tabindex ="-1" id="document_add_modal_link_medicaid_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/practice/'.$practice->id.'/medicaid_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"></a>
                    </div>
                </div> 

                <div class="form-group">
                    {!! Form::label('BCBS ID', 'BCBS ID', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                    
                       <p class="show-border no-bottom">{{ $practice->bcbs_id }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2">
                        <a tabindex ="-1" id="document_add_modal_link_bcbs_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/practice/'.$practice->id.'/bcbs_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"></a>
                    </div>
                </div> 

            </div><!-- /.box-body -->
        </div><!--  Credentials Box Ends -->        
        
        <div class="box no-shadow no-border margin-b-10" ><!--  Mailing Address Box Starts -->
            
            <div class="box-block-header with-border">
                <i class="livicon" data-name="mail"></i> <h3 class="box-title"> Mailing Address</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            
            <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-mailling-address"><!-- Box Body Starts -->
                {!! Form::hidden('ma_address_type','practice',['class'=>'js-address-type']) !!}
				{!! Form::hidden('ma_address_type_id',$practice->id,['class'=>'js-address-type-id']) !!}
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
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ $practice->mail_add_1 }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                       <p class="show-border no-bottom">{{ $practice->mail_add_2 }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                      <p class="show-border no-bottom">{{ $practice->mail_city }}</p>
                    </div>
                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"> 
                        <p class="show-border no-bottom">{{ $practice->mail_state }}</p>
                    </div>
                </div>   

                <div class="form-group">
                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                       <p class="show-border no-bottom">{{ $practice->mail_zip5 }}</p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4"> 
                        <p class="show-border no-bottom">{{ $practice->mail_zip4 }}</p>
                    </div>
                    <div class="col-md-1 col-sm-1 col-xs-2">            
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['ma']['is_address_match'], 'show'); ?>   
                        <p class=" margin-t-13"><?php echo $value; ?>  </p>
                    </div>
                   
                </div>

            </div><!-- /.box-body ends -->
        </div><!--  Mailing Address Box Ends -->


        <div class="box no-shadow"><!--  General Information Box Starts -->
            
            <div class="box-block-header with-border">
                <i class="livicon" data-name="info"></i> <h3 class="box-title"> General Details</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            
            <div class="box-body form-horizontal margin-l-10 p-b-20"><!-- Box Body Starts -->           
                <div class="form-group">
                    {!! Form::label('Practice Start Date', 'Practice Start Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-3 col-md-4 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::timezone($practice->created_at,'m/d/y')}}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div> 

                <div class="form-group">
                    {!! Form::label('Primary Language', 'Primary Language',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-3 col-md-4 col-sm-7 col-xs-10">
                     <p class="show-border no-bottom">{{ $practice->languages_details->language }}</p>
                    </div>
                    <div class="col-sm-1 col-xs-2"></div>
                </div>                                                            

                <div class="form-group">
                    {!! Form::label('Provider', 'Providers', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5">  
                       <p class="show-border no-bottom">{{$provider_count}}</p>
                    </div>
                    
                </div>   
                
                <div class="form-group">
                   
                    {!! Form::label('Facilities', 'Facilities', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5"> 
                        <p class="show-border no-bottom">{{$facility_count}}</p>
                    </div>
                </div> 

				<div class="form-group">
					{!! Form::label('backdate', 'Back Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
						{!! Form::radio('backDate', 'Yes',null,['class'=>'','id'=>'c-yes','disabled']) !!} {!! Form::label('c-yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp;&nbsp; &nbsp;
						{!! Form::radio('backDate', 'No',true,['class'=>'','id'=>'c-no','disabled']) !!} {!! Form::label('c-no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
					</div>                        
				</div>
				
				<div class="form-group">
					{!! Form::label('icd_autopopulate', 'ICD Autopopulate', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
						{!! Form::radio('icd_autopopulate', 'Yes',null,['class'=>'','id'=>'i-yes','disabled']) !!} {!! Form::label('i-yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp;&nbsp; &nbsp;
						{!! Form::radio('icd_autopopulate', 'No',true,['class'=>'','id'=>'i-no','disabled']) !!} {!! Form::label('i-no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
					</div>                        
				</div>

            </div><!-- /.box-body -->
        </div><!--  General Information Box Ends -->
        
        @include ('practice/layouts/npi_form_fields')
        @include ('practice/layouts/npi_form_modal')
    </div><!--  Right side Content Ends -->
        
    <!-- Modal Light Box starts -->  
    <div id="form-address-modal" class="modal fade in">
    @include ('practice/layouts/usps_form_modal')
    </div><!-- Modal Light Box Ends --> 
@stop       