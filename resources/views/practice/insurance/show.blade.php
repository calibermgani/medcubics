@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('insurance/') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop


@section('practice-info')
@include ('practice/insurance/insurance_tabs')
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
		@if($checkpermission->check_url_permission('insurance/{insurance}/edit') == 1)
		<a href="{{url('insurance/'.$insurance->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
		@endif
	</div>

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->

		<div class="box no-shadow margin-b-10">
			<div class="box-block-header margin-b-10">
				<i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Business Details</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body form-horizontal margin-l-10 p-b-20">
				<div class=" js-address-class" id="js-address-business-address">
					{!! Form::hidden('general_address_type','insurance',['class'=>'js-address-type']) !!}
					{!! Form::hidden('general_address_type_id',$insurance->id,['class'=>'js-address-type-id']) !!}
					{!! Form::hidden('general_address_type_category','mailling_address',['class'=>'js-address-type-category']) !!}
					{!! Form::hidden('general_address1',$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
					{!! Form::hidden('general_city',$address_flag['general']['city'],['class'=>'js-address-city']) !!}
					{!! Form::hidden('general_state',$address_flag['general']['state'],['class'=>'js-address-state']) !!}
					{!! Form::hidden('general_zip5',$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
					{!! Form::hidden('general_zip4',$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
					{!! Form::hidden('general_is_address_match',$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
					{!! Form::hidden('general_error_message',$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}

					<div class="form-group">
						{!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label star']) !!} 
						<div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">                                                     
							<p class="show-border no-bottom">{{ $insurance->address_1 }}</p>
						</div>
						<div class="col-sm-1 col-xs-2"></div>
					</div> 

					<div class="form-group">
						{!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
						<div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">                            
							<p class="show-border no-bottom">{{ $insurance->address_2 }}</p>
						</div>
						<div class="col-sm-1 col-xs-2"></div>
					</div> 

					<div class="form-group">
						{!! Form::label('City', 'City', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label star']) !!}
						<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">  
							<p class="show-border no-bottom">{{ $insurance->city }}</p>
						</div>
						{!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"> 
							<p class="show-border no-bottom">{{ $insurance->state }}</p>
						</div>
					</div>   
					<div class="form-group no-bottom">
						{!! Form::label('zipcode', 'Zip Code', ['class'=>'col-md-4 col-sm-3 col-xs-12 control-label star']) !!}
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">                             
							<p class="show-border no-bottom">{{ $insurance->zipcode5 }}</p>                                                    
						</div>
						<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">                             
							<p class="show-border no-bottom">{{ $insurance->zipcode4 }}</p>                          
						</div>
						<div class="col-md-1 col-sm-2 col-xs-2">            
							<?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match'],'show'); ?>   
							<p class=" margin-t-13"> <?php echo $value; ?></p>                       
						</div> 
					</div>                    

				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends--> 



		<div class="box no-shadow margin-b-10">
			<div class="box-block-header margin-b-10">
				<i class="livicon" data-name="shield"></i> <h3 class="box-title">Credentials</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body form-horizontal margin-l-10 p-b-12">

				<div class="js-add-new-select" id="js-insurance-type">

					<div class="form-group">
						{!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
						<div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
							<p class="show-border no-bottom">{{ @$insurance->insurancetype->type_name }}</p>
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('cmsType', 'CMS Type', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
						<div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
							<p class="show-border no-bottom">{{ @$insurance->insurancetype->cms_type }}</p>
						</div>
					</div>
					
				</div>  

				<div class="form-group">
					{!! Form::label('Enrollment', 'Enrollment Required', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}       
					<div class="control-group col-lg-8 col-md-8 col-sm-8">
					@if($insurance->enrollment == 'Yes')
						{!! Form::radio('enrollment', 'Yes',true,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('enrollment', 'No',null,['class'=>'flat-red','disabled']) !!}  No &emsp;
					@else	
						{!! Form::radio('enrollment', 'Yes',null,['class'=>'flat-red','disabled']) !!} Yes &emsp; {!! Form::radio('enrollment', 'No',true,['class'=>'flat-red']) !!}  No &emsp;
					@endif	
					</div>
					<div class="col-sm-1"></div>
				</div> 

				{!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
				<div class="form-group">                
					{!! Form::label('Managedcareid', 'Managed Care ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                            
					<div class="col-lg-4 col-md-6 col-sm-7 col-xs-10">                    
						<p class="show-border no-bottom">{{ $insurance->managedcareid }}</p>
					</div>
					<div class="col-sm-1 col-xs-1 hide">
						<a id="document_add_modal_link_managed_care_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/insurance/'.$insurance->id.'/managed_care_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
					</div>                
				</div> 

				<div class="form-group">                
					{!! Form::label('Medigapid', 'Medigap ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-6 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $insurance->medigapid }}</p>                       
					</div>
					<div class="col-sm-1 col-xs-1 hide">
						<a id="document_add_modal_link_medigap_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/insurance/'.$insurance->id.'/medigap_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
					</div>                
				</div> 

				<div class="form-group">                
					{!! Form::label('PayerID', 'Payer ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-6 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $insurance->payerid }}</p>                  
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div> 
				<div class="form-group">
					{!! Form::label('ERA payerid', 'ERA Payer ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-6 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $insurance->era_payerid }}</p>                        
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div> 
				<div class="form-group">
					{!! Form::label('Eligibility payerid', 'Eligibility Payer ID', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}                            
					<div class="col-lg-4 col-md-6 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $insurance->eligibility_payerid }}</p>                          
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div> 

				<div class="form-group">        
					{!! Form::label('Fee schedule', 'Fee schedule', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
					<div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
						<p class="show-border no-bottom">{{ $insurance->feeschedule }}</p>
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div> 

				<div class="form-group">
					{!! Form::label('Status', 'Status', ['class'=>'col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label']) !!}
					<div class="control-group col-lg-6 col-md-6 col-sm-6">
					@if($insurance->status == 'Active')
						{!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp;<?php /* {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','disabled']) !!} Inactive */ ?>
					@else	
						{!! Form::radio('status', 'Active',null,['class'=>'flat-red','disabled']) !!} Active &emsp;<?php /* {!! Form::radio('status', 'Inactive',true,['class'=>'flat-red']) !!} Inactive */ ?>
					@endif	
					</div>
					<div class="col-sm-1"></div>
				</div> 
				
				<div class="bottom-space-15 hidden-sm hidden-xs">&emsp;</div>
				<div class="bottom-space-10 hidden-sm hidden-xs">&emsp;</div>

			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends --> 

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
		<div class="box no-shadow margin-b-10">
			<div class="box-block-header margin-b-10">
				<i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body form-horizontal margin-l-10 p-b-15">

				<div class="form-group">        
					{!! Form::label('Primaryfiling', 'Primary Timely Filing Days', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
						<p class="show-border no-bottom">{{  $insurance->primaryfiling }}</p>
					</div>
					<div class="col-sm-1"></div>
				</div>

				<div class="form-group">        
					{!! Form::label('Secondayfiling', 'Secondary Timely Filing Days', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
						<p class="show-border no-bottom">{{ $insurance->secondaryfiling }}</p>
					</div>
					<div class="col-sm-1"></div>
				</div>

				<div class="form-group">        
					{!! Form::label('Appealfiling', 'Appeal Filing Days', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}                                                                                 
					<div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
						<p class="show-border no-bottom">{{ $insurance->appealfiling }}</p>
					</div>
					<div class="col-sm-1"></div>
				</div> 
				<div class="form-group bottom-space-10">
					{!! Form::label('Claimtype', 'Claim Type', ['class'=>'col-lg-5 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="control-group col-lg-6 col-md-6 col-sm-6">
					@if($insurance->claimtype == 'Electronic')
						{!! Form::radio('claimtype', 'Electronic',true,['class'=>'flat-red']) !!} Electronic &emsp; {!! Form::radio('claimtype', 'Paper',null,['class'=>'flat-red','disabled']) !!} Paper
					@else	
						{!! Form::radio('claimtype', 'Electronic',null,['class'=>'flat-red','disabled']) !!} Electronic &emsp; {!! Form::radio('claimtype', 'Paper',true,['class'=>'flat-red']) !!} Paper
					@endif	
					</div>
					<div class="col-sm-1"></div>
				</div>

			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->

		<div class="box no-shadow margin-b-10"><!-- Box Additional COntacts Starts -->
			<div class="box-block-header margin-b-10">
				<i class="livicon" data-name="notebook"></i> <h3 class="box-title">Additional Contacts</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body form-horizontal margin-l-10 p-b-30"><!-- Box Body Starts -->                     


				<div class="form-group">
					{!! Form::label('Claim Status Phone', 'Claim Status Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
						<p class="show-border no-bottom">{{ $insurance->claim_ph }}</p>
					</div>
					{!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
						<p class="show-border no-bottom">{{ $insurance->claim_ext }}</p>
					</div>
				</div>

				<div class="form-group">
					{!! Form::label('Eligibility Phone', 'Eligibility Phone1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
						<p class="show-border no-bottom">{{ $insurance->eligibility_ph }}</p>
					</div>
					{!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
						<p class="show-border no-bottom">{{ $insurance->eligibility_ext }}</p>
					</div>
				</div>

				<div class="form-group">
					{!! Form::label('Eligibility Phone', 'Eligibility Phone2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
						<p class="show-border no-bottom">{{ $insurance->eligibility_ph2 }}</p>
					</div>
					{!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
						<p class="show-border no-bottom">{{ $insurance->eligibility_ext2 }}</p>
					</div>
				</div>

				<div class="form-group">
					{!! Form::label('Enrollment Phone', 'Enrollment Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
						<p class="show-border no-bottom">{{ $insurance->enrollment_ph }}</p>
					</div>
					{!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
						<p class="show-border no-bottom">{{ $insurance->enrollment_ext }}</p>
					</div>
				</div>

				<div class="form-group">
					{!! Form::label('Prior Auth Phone', 'Prior Auth Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
						<p class="show-border no-bottom">{{ $insurance->prior_ph }}</p>
					</div>
					{!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
						<p class="show-border no-bottom">{{ $insurance->prior_ext }}</p>
					</div>
				</div>


				<div class="form-group">
					{!! Form::label('Claim Status Fax', 'Claim Status Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
					   <p class="show-border no-bottom">{{ $insurance->claim_fax }}</p>
					</div>                        
				</div>                                           

				<div class="form-group">
					{!! Form::label('Eligibility Fax', 'Eligibility Fax1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
					   <p class="show-border no-bottom">{{ $insurance->eligibility_fax }}</p>
					</div>                        
				</div>                                           

				<div class="form-group">
					{!! Form::label('Eligibility Fax', 'Eligibility Fax2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
						<p class="show-border no-bottom">{{ $insurance->eligibility_fax2 }}</p>
					</div>                        
				</div>   

				<div class="form-group">
					{!! Form::label('Enrollment Fax', 'Enrollment Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
						<p class="show-border no-bottom">{{ $insurance->enrollment_fax }}</p>
					</div>                        
				</div>

				<div class="form-group">
					{!! Form::label('Prior Auth Fax', 'Prior Auth Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6">  
						<p class="show-border no-bottom">{{ $insurance->prior_fax }}</p>
					</div>                        
				</div>   

			</div><!-- Box Body Ends -->
		</div> <!-- Box Additional contacts ends -->
	</div><!--  Right side Content Ends -->
	<!-- Modal Light Box starts -->  
	<div id="form-address-modal" class="modal fade in">
		@include ('practice/layouts/usps_form_modal')
	</div><!-- Modal Light Box Ends -->   

	@include('practice/layouts/favourite_modal') 
@stop            