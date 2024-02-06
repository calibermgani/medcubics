@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php $facility->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> View</span></small>
        </h1>
        <ol class="breadcrumb">
            @if($checkpermission->check_url_permission('facility/create') == 1)
            <li class=""><a href="{{ url('facility/create') }}" class="" accesskey="n"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="New Facility"></i></a></li>
            @endif	
            <li><a href="{{ url('facility')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
           	
            <!--li><a href="" data-url="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/facility')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/facility/tabs')
@stop

@section('practice')
<?php $facilityid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'decode'); ?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
    @if($checkpermission->check_url_permission('facility/{facility}/edit') == 1)
        <a href="{{ url('facility/'.$facility->id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif
</div>

 <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box no-shadow margin-b-10"><!-- Business Info Box Starts -->
        
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="briefcase"></i> <h3 class="box-title"> Business Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        
        <div class="box-body form-horizontal js-address-class margin-l-10 p-b-12" id="js-address-general-address"><!-- Box Body Ends -->

           
            <div class="form-group">
                {!! Form::label('Specialty', 'Specialty', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                  <p class="show-border no-bottom">{{ @$facility->speciality_details->speciality }}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('Taxonomy', 'Taxonomy',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$facility->taxanomy_details->code }}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                
                {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                  <p class="show-border no-bottom">{{ @$facility->facility_address->address1 }}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                  <p class="show-border no-bottom">{{ @$facility->facility_address->address2 }}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">  
                    <p class="show-border no-bottom">{{ @$facility->facility_address->city }}</p>
                </div>
                {!! Form::label('st', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                   <p class="show-border no-bottom">{{ @$facility->facility_address->state }}</p>
                </div>
            </div>   

            <div class="form-group">
                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                  <p class="show-border no-bottom">{{ @$facility->facility_address->pay_zip5 }}</p>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-2 col-xs-4"> 
                  <p class="show-border no-bottom">{{ @$facility->facility_address->pay_zip4 }}</p>
                </div>
                <div class="col-md-1 col-sm-2 col-xs-2 p-l-0">            
                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
                    <?php echo $value; ?>    
                </div> 
            </div>

            <div class="form-group">
                {!! Form::label('County', 'County', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-7 col-sm-6 col-xs-10">                    
                    <p class="show-border no-bottom">{{ @$facility->county->name }}</p>                                      
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('timezone', 'Time Zone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-7 col-sm-6 col-xs-10">                    
                    <p class="show-border no-bottom">{{ @$facility->timezone }}</p>                                      
                </div>
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
          
            
    <div class="box no-shadow margin-b-10">
       
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title"> General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        
        <div class="box-body form-horizontal margin-l-10 p-b-20">
            <div class="form-group margin-b-20">
                {!! Form::label('scheduler', 'Scheduler', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-7 col-sm-8 col-xs-12"> 
				@if($facility->scheduler =="Yes")	
                    {!! Form::radio('scheduler', 'Yes',true,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('scheduler', 'No',null,['class'=>'flat-red','disabled']) !!} No                                       
				@else	
					{!! Form::radio('scheduler', 'Yes',null,['class'=>'flat-red','disabled']) !!} Yes &emsp; {!! Form::radio('scheduler', 'No',true,['class'=>'flat-red']) !!} No                                       
				@endif	
                </div>                
            </div>   

            <div class="form-group margin-b-20">
                {!! Form::label('medication_prescribed', 'Medication Prescribed', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-7 col-sm-8 col-xs-12">  
				@if($facility->medication_prescr =="Yes")
                    {!! Form::radio('medication_prescr', 'Yes','true',['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('medication_prescr', 'No',null,['class'=>'flat-red','disabled']) !!} No 
                    
                    @else	
					{!! Form::radio('medication_prescr', 'Yes','null',['class'=>'flat-red','disabled']) !!} Yes &emsp; {!! Form::radio('medication_prescr', 'No',true,['class'=>'flat-red']) !!} No  
				@endif		
                </div>                
            </div>   
            <div class="form-group margin-b-20">
                {!! Form::label('superbill', 'Super Bill', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-7 col-sm-8 col-xs-12">  
				@if($facility->superbill =="Available")
                    {!! Form::radio('superbill', 'Available','true',['class'=>'flat-red']) !!} Available &emsp; {!! Form::radio('superbill', 'Not Available',null,['class'=>'flat-red','disabled']) !!} Not Available 
				@else	
					{!! Form::radio('superbill', 'Available','null',['class'=>'flat-red','disabled']) !!} Available &emsp; {!! Form::radio('superbill', 'Not Available',true,['class'=>'flat-red']) !!} Not Available                                       
				@endif	
                </div>                
            </div>

            <div class="form-group margin-b-20">
                {!! Form::label('credit_cart_accepted', 'Credit Card', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-7 col-sm-8 col-xs-12">
				@if($facility->credit_cart_accepted =="Accepted")
                    {!! Form::radio('credit_cart_accepted', 'Accepted',true,['class'=>'flat-red']) !!} Accepted &emsp; {!! Form::radio('credit_cart_accepted', 'Not Accepted',null,['class'=>'flat-red','disabled']) !!} Not Accepted 
				@else	
					{!! Form::radio('credit_cart_accepted', 'Accepted',null,['class'=>'flat-red','disabled']) !!} Accepted &emsp; {!! Form::radio('credit_cart_accepted', 'Not Accepted',true, ['class'=>'flat-red']) !!} Not Accepted                                       
				@endif	
                </div>                
            </div>

            <div class="form-group margin-b-20">
                {!! Form::label('Status', 'Status', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-7 col-sm-8 col-xs-12">
				@if($facility->status =="Active")
                    {!! Form::radio('status', 'Active','true',['class'=>'flat-red']) !!} Active &emsp; 
					<?php /*{!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','disabled']) !!} Inactive  */ ?>             
				@else
                    {!! Form::radio('status', 'Active','null',['class'=>'flat-red','disabled']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',true,['class'=>'flat-red']) !!} Inactive               
				@endif	
                </div>                
            </div>

            <div class="form-group">
                {!! Form::label('no_of_visit_per_week', 'Visit per Week', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}                                                  
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">  
                    <p class="show-border no-bottom">{{ (@$facility->no_of_visit_per_week==0)?'':@$facility->no_of_visit_per_week }}</p>
                </div>                
            </div>
          
        </div><!-- /.box-body -->
    </div><!-- General info box Ends-->
            
            
             <div class="box box-view no-shadow"><!--  Box Starts -->               

        <div class="box-header-view">
            <i class="livicon" data-name="clock"></i> <h3 class="box-title">Office Hours</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">                    
                <tbody>
                    <tr>
                        <td>Monday</td>
                        <td>
                            <span class="@if($facility->monday_forenoon != '0;0')forenoon-time @else not-available @endif">@if($facility->monday_forenoon != '0;0'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->monday_forenoon,'forenoon')}}@else Not Available @endif</span>
                            <span class="@if($facility->monday_afternoon != '720;720')noon-time @else not-available @endif">@if($facility->monday_afternoon != '720;720'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->monday_afternoon,'afternoon')}}@else Not Available @endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Tuesday</td>
                        <td>
                            <span class="@if($facility->tuesday_forenoon != '0;0')forenoon-time @else not-available @endif">@if($facility->tuesday_forenoon != '0;0'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->tuesday_forenoon,'forenoon')}}@else Not Available @endif</span>
                            <span class="@if($facility->tuesday_afternoon != '720;720')noon-time @else not-available @endif">@if($facility->tuesday_afternoon != '720;720'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->tuesday_afternoon,'afternoon')}}@else Not Available @endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Wednesday</td>
                        <td>
                            <span class="@if($facility->wednesday_forenoon != '0;0')forenoon-time @else not-available @endif">@if($facility->wednesday_forenoon != '0;0'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->wednesday_forenoon,'forenoon')}}@else Not Available @endif</span>
                            <span class="@if($facility->wednesday_afternoon != '720;720')noon-time @else not-available @endif">@if($facility->wednesday_afternoon != '720;720'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->wednesday_afternoon,'afternoon')}}@else Not Available @endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Thursday</td>
                        <td>
                            <span class="@if($facility->thursday_forenoon != '0;0')forenoon-time @else not-available @endif">@if($facility->thursday_forenoon != '0;0'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->thursday_forenoon,'forenoon')}}@else Not Available @endif</span>
                            <span class="@if($facility->thursday_afternoon != '720;720')noon-time @else not-available @endif">@if($facility->thursday_afternoon != '720;720'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->thursday_afternoon,'afternoon')}}@else Not Available @endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Friday</td>
                        <td>
                            <span class="@if($facility->friday_forenoon != '00;00')forenoon-time @else not-available @endif">@if($facility->friday_forenoon != '00;00'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->friday_forenoon,'forenoon')}}@else Not Available @endif</span>
                            <span class="@if($facility->friday_afternoon != '720;720')noon-time @else not-available @endif">@if($facility->friday_afternoon != '720;720'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->friday_afternoon,'afternoon')}}@else Not Available @endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Saturday</td>
                        <td>
                            <span class="@if($facility->saturday_forenoon != '0;0')forenoon-time @else not-available @endif">@if($facility->saturday_forenoon != '0;0'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->saturday_forenoon,'forenoon')}}@else Not Available @endif</span>
                            <span class="@if($facility->saturday_afternoon != '720;720')noon-time @else not-available @endif">@if($facility->saturday_afternoon != '720;720'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->saturday_afternoon,'afternoon')}}@else Not Available @endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Sunday</td>
                        <td>
                            <span class="@if($facility->sunday_forenoon != '0;0')forenoon-time @else not-available @endif">@if($facility->sunday_forenoon != '0;0'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->sunday_forenoon,'forenoon')}}@else Not Available @endif</span>
                            <span class="@if($facility->sunday_afternoon != '720;720')noon-time @else not-available @endif">@if($facility->sunday_afternoon != '720;720'){{App\Http\Helpers\Helpers::sliderTimeDisplay($facility->sunday_afternoon,'afternoon')}}@else Not Available @endif</span>
                        </td>
                    </tr>                    

                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->  
            
        </div><!--  Left side Content Ends -->        

 <div class="col-lg-6 col-md-6 col-xs-12"><!--  Right side Content Starts -->
            <div class="box no-shadow margin-b-10">
               
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="shield"></i> <h3 class="box-title"> Credentials</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                
                <div class="box-body form-horizontal margin-l-10 p-b-20">
                    <div class="form-group">
                        {!! Form::label('Tax ID', 'Tax ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                         <p class="show-border no-bottom">{{ $facility->facility_tax_id }}</p>
                        </div>
                        <div class="col-sm-1 col-xs-1">
                            <a id="document_add_modal_link_tax_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/facility/'.$facility->id.'/tax_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_tax_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>                        
                    </div> 

                    <div class="form-group">
                        {!! Form::label('NPI', 'NPI', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                            <p class="show-border no-bottom">{{ $facility->facility_npi }}</p>
                        </div>
                        <div class="col-sm-1 col-xs-1">
                            <a id="document_add_modal_link_npi" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/facility/'.$facility->id.'/npi')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_npi->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                        <div class="col-sm-1 col-xs-1">                            
                             <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi'], 'induvidual'); ?>   
                            <?php echo $value; ?>      

                        </div>                        
                    </div> 

                    <div>
                        {!! Form::hidden('type','facility',['id'=>'type']) !!}
                        {!! Form::hidden('type_id','',['id'=>'type_id']) !!}
                        {!! Form::hidden('type_category','Individual',['id'=>'type_category']) !!}
                        @include ('practice/layouts/npi_form_fields')
                    </div>                                                                       

                    <div class="form-group">
                        {!! Form::label('CLIA Number', 'CLIA Number', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                           <p class="show-border no-bottom">{{ $facility->clia_number }}</p>
                        </div>
                        <div class="col-sm-1 col-xs-1">
                            <a id="document_add_modal_link_clia_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/facility/'.$facility->id.'/clia_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_clia_id->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>                        
                    </div>                        

                    <div class="form-group">
                        {!! Form::label('POS', 'POS',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                            <p class="show-border no-bottom">{{ $facility->pos_details->code }}</p>
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Default Provider', 'Default Provider', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                            <p class="show-border no-bottom">{{ @$facility->provider_details->provider_name }}</p>
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('FDA', 'FDA',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                            <p class="show-border no-bottom">{{ $facility->fda }}</p>
                        </div>
                        {!! Form::hidden('fda_attach',@$documents_fda->category) !!}
                        <div class="col-sm-1 col-xs-2 p-l-0">
                             <a id="document_add_modal_link_fda" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/facility/'.$facility->id.'/fda')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_fda->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                        </div>
                    </div> 

                    <div class="form-group margin-b-6">
                        {!! Form::label('Claim Format', 'Claim Format',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
                           @foreach($claimformats as $key=>$get_format)
								@if($key == '1')
								{!! Form::radio('claim_format',$key,true,['class'=>'flat-red hidden']+['checked'=>'checked']) !!} {{ $get_format }} &emsp; 	
								@endif
						  @endforeach
						  
                        </div>	
                        <div class="col-sm-1 col-xs-2"></div>
                    </div> 
                    <div class="margin-b-30 margin-b-15 p-b-4 hidden-sm hidden-xs">
                        &emsp;
                    </div>
                </div><!-- /.box-body -->
            </div><!-- Credential box Ends-->

            <div class="box box-view no-shadow  no-border-radius no-bottom margin-b-10"><!-- Business Info Box Starts -->
                
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="address-book"></i> <h3 class="box-title"> Contact Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                
                <div class="box-body form-horizontal margin-l-10">

                    <div class="form-group">
                        {!! Form::label('statement_address', 'Statement Address', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-4 col-md-7 col-sm-6 col-xs-10">  
                           <p class="show-border no-bottom">{{ $facility->statement_address }}</p>
                        </div>                
                    </div>

                    <div class="form-group">
                        {!! Form::label('Facility Manager', 'Facility Manager', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">  
                           <p class="show-border no-bottom">{{ str_limit($facility->facility_manager,50) }}</p>
                        </div>                
                    </div>

                    <div class="form-group">
                        {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                          <p class="show-border no-bottom">{{ $facility->facility_manager_phone }}</p>
                        </div>
                        {!! Form::label('st', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                            <p class="show-border no-bottom">{{ $facility->facility_manager_ext }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Email', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">  
                            <p class="show-border no-bottom">{{ $facility->facility_manager_email }}</p>
                        </div>                
                    </div>

                    <div class="form-group">
                        {!! Form::label('Facility Biller', 'Facility Biller', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">  
                            <p class="show-border no-bottom">{{ str_limit($facility->facility_biller,50) }}</p>
                        </div>                
                    </div>

                    <div class="form-group">
                        {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
                            <p class="show-border no-bottom">{{ $facility->facility_biller_phone }}</p>
                        </div>
                        {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                            <p class="show-border no-bottom">{{ $facility->facility_biller_ext }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Email', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">  
                            <p class="show-border no-bottom">{{ $facility->facility_biller_email }}</p>
                        </div>
                    </div>                                       
                </div><!-- /.box-body -->
            </div><!-- Contact Info Box Ends -->
        </div><!--  Right side Content Ends -->    
<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include ('practice/layouts/usps_form_modal')
</div><!-- Modal Light Box Ends --> 
@include ('practice/layouts/npi_form_modal')
@stop