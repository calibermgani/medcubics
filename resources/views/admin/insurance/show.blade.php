@extends('admin')
@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}}  font14"></i> Insurance <i class="fa fa-angle-double-right med-breadcrum"></i><span>View</span></small>
        </h1>
		<?php  $insurance->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurance->id,'encode'); ?>
        <ol class="breadcrumb">
             <li><a href="{{ url('admin/insurance/') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            

            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif

        </ol>
    </section>

</div>
@stop


@section('practice-info')
@include ('admin/insurance/insurance_tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">    
       @if($checkpermission->check_adminurl_permission('admin/insurance/{insurance}/edit') == 1)
             <a href="{{url('admin/insurance/'.$insurance->id.'/edit')}}"  class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
              @endif       
</div>
  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
            <div class="box box-view no-shadow js-address-class" id="js-address-business-address"><!--  Box Starts -->

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


                <div class="box-header-view">
            <i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Business Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table-responsive table-striped-view table">
                    <tbody>

                    <tr>
                        <td>Address Line 1</td>
                        <td>{{ $insurance->address_1 }}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>Address Line 2</td>
                        <td>{{ $insurance->address_2 }}</td>
                        <td colspan="2"></td>
                    </tr>

                    <tr>
                        <td>City</td>
                        <td>{{ $insurance->city }}</td>
                        <td colspan="2"></td>
                    </tr>

                    <tr>
                        <td>State</td>
                        <td>{{ $insurance->state }}</td>
                        <td colspan="2"></td>
                    </tr>

                    <tr colspan="4">
                        <td>Zip Code</td>
                        <td>{{ $insurance->zipcode5 }} @if($insurance->zipcode4 != '')- {{ $insurance->zipcode4 }}@endif</td>
                        <td>
                            <?php  $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match'],'show'); ?>   
                                <?php echo $value;?>
                        </td>
                    </tr>                   

                    </tbody>
                </table>

            </div>
        </div>

<div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
                    <i class="livicon" data-name="shield"></i> <h3 class="box-title">Credentials</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
            <tbody>                        

                        <tr>
                            <td>Insurance Type</td>
                                <td>@if($insurance->insurancetype){{ $insurance->insurancetype->type_name }}@endif</td>
                            <td ></td>
                        </tr>

						<tr>
                            <td>Enrollment Required</td>
                            <td> <span class="patient-status-bg-form @if($insurance->enrollment == 'Yes')label-success @else label-danger @endif">@if($insurance->enrollment == 'Yes')Yes @else No @endif</span> </td>
                            <td></td>
                        </tr>
						
                        <tr>
                            <td>Managed Care ID</td>
                            <td><span @if($insurance->managedcareid != "") class="bg-number" @endif/>{{ $insurance->managedcareid }}</td>
                            <td><!-- <a id="document_add_modal_link_managed_care_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/insurance/'.$insurance->id.'/managed_care_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('app.document_upload_modal_icon')}}"></i></a></i></a> --></td>
                        </tr>

                        <tr>
                            <td>Medigap ID</td>
                            <td><span @if($insurance->medigapid != "") class="bg-number" @endif/>{{ $insurance->medigapid }}</td>
                            <td><!-- <a id="document_add_modal_link_medigap_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/insurance/'.$insurance->id.'/medigap_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('app.document_upload_modal_icon')}}"></i></a> --></td>
                        </tr>

                        <tr>
                            <td>Payer ID</td>
                            <td><span @if($insurance->payerid != "") class="bg-number" @endif/>{{ $insurance->payerid }}</td>
                                <td></td>
                        </tr>
                        <tr>
                            <td>ERA Payer ID</td>
                            <td><span @if($insurance->era_payerid  != "") class="bg-number" @endif/>{{ $insurance->era_payerid }}</td>
                                <td></td>
                        </tr>
                        <tr>
                            <td>Eligibility Payer ID</td>
                            <td><span @if($insurance->eligibility_payerid != "") class="bg-number" @endif/>{{ $insurance->eligibility_payerid }}</td>
                                <td></td>
                        </tr>
                        <tr>
                            <td>Fee schedule</td>
                            <td>{{ $insurance->feeschedule }}</td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>Status</td>
                                <td> <span class="patient-status-bg-form @if($insurance->status == 'Active')label-success @else label-danger @endif">@if($insurance->status == 'Active')Active @else Inactive @endif</span> </td>
                            <td colspan="2"></td>
                        </tr>

                    </tbody>
                </table>
            <div class="margin-b-10 hidden-sm hidden-xs">&emsp;</div>
            </div>
        </div>
</div>



<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
           <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
            <tbody>
					<tr>
						<td>Primary Timely Filing Days</td>
						<td>{{  $insurance->primaryfiling }}</td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td>Secondary Timely Filing Days</td>
						<td>{{ $insurance->secondaryfiling }}</td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td>Appeal Filing Days</td>
						<td>{{ $insurance->appealfiling }}</td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td>Claim Type</td>
						<td>{{ @$insurance->claimtype }}</td>
						<td colspan="2"></td>
					</tr>
						

				</tbody>
			</table>
            <div class="margin-b-10 hidden-sm hidden-xs">&emsp;</div>
       
		</div>
        
	</div>
            <div class="box box-view no-shadow"><!--  Box Starts -->                            
                <div class="box-header-view">
                    <i class="livicon" data-name="notebook"></i> <h3 class="box-title">Additional Contacts</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table-responsive table-striped-view table">    
                        <tbody>
							<tr>
								<td>Claim Status Phone</td>
								<td>{{ $insurance->claim_ph }} @if($insurance->claim_ext != '')  <span class=" bg-ext ">{{ $insurance->claim_ext }}</span>@endif</td>
								<td></td>
							</tr>                                           

							<tr>
								<td>Eligibility Phone</td>
								<td>{{ $insurance->eligibility_ph }} @if($insurance->eligibility_ext != '')  <span class=" bg-ext ">{{ $insurance->eligibility_ext }}</span>@endif</td> 
								<td></td>
							</tr>
							<tr>
								<td>Eligibility Phone 2</td>
								<td>{{ $insurance->eligibility_ph2 }}  @if($insurance->eligibility_ext2 != '')  <span class=" bg-ext ">{{ $insurance->eligibility_ext2 }}</span>@endif</td> 
								<td></td>
							</tr>
							<tr>
								<td>Enrollment Phone</td>
								<td>{{ $insurance->enrollment_ph }} @if($insurance->enrollment_ext != '') <span class=" bg-ext ">{{ $insurance->enrollment_ext }}</span>@endif</td> 
								<td></td>
							</tr>
							<tr>
								<td>Prior Auth Phone </td>
								<td>{{ $insurance->prior_ph }} @if($insurance->prior_ext != '') <span class=" bg-ext ">{{ $insurance->prior_ext }}</span>@endif</td> 
								<td></td>
							</tr>  
							
							 <tr>
								<td>Claim Status Fax</td>
								<td>{{ $insurance->claim_fax }}</td>  
								<td></td>
							</tr>
							<tr>
								<td>Eligibility Fax</td>
								<td>{{ $insurance->eligibility_fax }}</td>  
								<td></td>
							</tr> 
							<tr>
								<td>Eligibility Fax 2</td>
								<td>{{ $insurance->eligibility_fax2 }}</td>  
								<td></td>
							</tr>
							<tr>
								<td>Enrollment Fax</td>
								<td>{{ $insurance->enrollment_fax }}</td>  
								<td></td>
							</tr>
							<tr>
								<td>Prior Auth Fax</td>
								<td>{{ $insurance->prior_fax }}</td>  
								<td></td>
							</tr>                                                                        
                        </tbody>
                    </table>
				</div>
			</div>

        </div>
        
@include('practice/layouts/favourite_modal')

 <!-- Modal Light Box starts -->
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- Modal Light Box Ends -->


 @stop