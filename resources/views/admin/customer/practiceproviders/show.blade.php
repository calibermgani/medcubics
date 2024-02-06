@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php 
	$practiceid_ori = $practice_id;
	$practice_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice_id,'encode'); 
	$provider->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider->id,'encode');
?> 
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Provider</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
          
            
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop


@section('practice-info')

@include ('admin/customer/practiceproviders/tabs')  
@stop
@section('practice')
<!--1st Data-->


<!------------------>



<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">     
    @if($checkpermission->check_adminurl_permission('admin/customer/{customer_id}/practice/{practice_id}/providers/{providers}/edit') == 1)
    <a href="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice_id.'/providers/'.$provider->id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
    @endif            
</div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="address-book"></i> <h3 class="box-title">Personal Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table class="table-responsive table-striped-view table">                    
                        <tbody>

                            <tr>
                                <td>Provider Type</td>
                                <td>{{ @$provider->provider_type_details->name }}</td>
                                <td></td>                               
                            </tr>

                            <tr>
                                <td>DOB</td>
                                <td>{{ ($provider->provider_dob != '0000-00-00') ? App\Http\Helpers\Helpers::dateFormat($provider->provider_dob,'dob') : '' }}</td>
                                <td></td>                                
                            </tr>
                            
                            <tr>
                                <td>Gender</td>
                                <td>{{ $provider->gender }}</td>
                                <td></td>                                
                            </tr>

                            <tr>
                                <td>SSN</td>
                                <td>{{ @$provider->ssn }}</td>
                                <td></td>
                            </tr>
                            
                            <tr>
                                <td>Degree</td>
                                <td>{{ @$provider->degrees->degree_name }}</td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>Job Title</td>
                                <td>{{ $provider->job_title }}</td>  
                                <td></td>                               
                            </tr>

                            <tr>
                                <td>Address Line 1</td>
                                <td>{{ $provider->address_1 }} </td>
                                <td></td>                                
                            </tr>

                            <tr>
                                <td>Address Line 2</td>
                                <td>{{ $provider->address_2 }} </td>
                                <td></td>                                
                            </tr>

                            <tr>
                                <td>City</td>
                                <td>{{ $provider->city }} @if($provider->state != '') - <span class=" bg-state ">{{ $provider->state }}</span>@endif</td>
                                <td></td>                                
                            </tr>

                            <tr>
                                <td>Zip Code</td>
                                <td>{{ $provider->zipcode5 }} @if($provider->zipcode4 !='') - {{ $provider->zipcode4 }} @endif </td>
                                <td>
                                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match'],'show'); ?>   
                                    <span> <?php echo $value; ?></span>

                                </td>
                            </tr>                                    
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->

         <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="inbox"></i> <h3 class="box-title">Credentials</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table class="table-responsive table-striped-view table">                    
                        <tbody>
                            <tr>
                                <td>Medicare PTAN </td>
                                <td>{{ $provider->medicareptan }}</td>
                                <td></td>                               
                            </tr>
                            
                            <tr>
                                
                                <td>Medicaid ID</td>
                                <td>{{ $provider->medicaidid }}</td>
                                <td></td>                                
                            </tr>

                            <tr>
                                <td>BCBS ID</td>
                                <td>{{ $provider->bcbsid }}</td>   
                                <td></td>                                
                            </tr>
                            
                            <tr>                              
                                <td>Aetna ID</td>
                                <td>{{ $provider->aetnaid }}</td>
                                 <td></td>                                
                            </tr>

                            <tr>
                                <td>UHC ID</td>
                                <td>{{ $provider->uhcid }}</td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>Other ID 1</td>
                                <td>{{ $provider->otherid }}</td>                              
                                <td></td>
                            </tr>
                            
                            <tr>
                                <td>Ins Type 1</td>
                                <td>{{ substr(App\Models\Insurance::getInsuranceName($provider->otherid_ins), 0, 25) }}</td>                                   
                                <td></td>
                            </tr>

                            <tr>
                                <td>Other ID 2</td>
                                <td>{{ $provider->otherid2 }}</td>                                      
                                <td></td>
                            </tr>
                            
                            <tr>
                                <td>Ins Type 2</td>
                                <td>{{ substr(App\Models\Insurance::getInsuranceName($provider->otherid_ins2), 0, 25) }}</td>                               
                                <td></td>
                            </tr>

                            <tr>
                                <td>Other ID 3</td>
                                <td>{{ $provider->otherid3 }}</td>                                
                                <td></td>
                            </tr>
                            
                            <tr>
                                <td>Ins Type 3</td>
                                <td>{{ substr(App\Models\Insurance::getInsuranceName($provider->otherid_ins3), 0, 25) }}</td>                          
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->

        </div><!--  Left side Content Ends -->



        <div class="col-md-6 col-xs-12"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="medal"></i> <h3 class="box-title">Professional Identification</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table class="table-responsive table-striped-view table">                                        

                        <tbody>
                            <tr>
                                <td>ETIN Type</td>
                                <td>{{ $provider->etin_type }}</td>
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <td>@if($provider->etin_type == 'SSN') SSN @else TAX ID @endif Number</td>                                 
                                <td><span @if($provider->etin_type_number)class="bg-number" @endif>{{ $provider->etin_type_number }}</span></td>
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <td>NPI</td>
                                <td><span class="bg-number" />{{ $provider->npi }}</td>
                                <td colspan="2">
                                    <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view($npi_flag['is_valid_npi'], 'induvidual'); ?>   
                                    <span style= "position:relative;bottom:13px;"> <?php echo $value;?></span>                                  
                                </td> 
                            </tr>
                            <tr>
                                <td>Specialty 1</td>
                                <td>{{ @$provider->speciality->speciality }}</td>
                                <td colspan="2"></td>                                        
                            </tr>

                            <tr colspan="4">
                                <td>Taxonomy 1</td>
                                <td><span @if(@$provider->taxanomy->code) class="bg-number" @endif />{{ @$provider->taxanomy->code }}</td>
                                <td colspan="2"></td>
                            </tr>
                            
                             <tr>
                                <td>Specialty 2</td>
                                <td>{{ @$provider->speciality2->speciality }}</td>
                                <td colspan="2"></td>
                            </tr>

                            <tr colspan="4">
                                <td>Taxonomy 2</td>
                                <td><span @if(@$provider->taxanomy2->code != "")class="bg-number" @endif />{{ @$provider->taxanomy2->code }}</td>
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <td>State License</td>
                                <td>{{ $provider->statelicense }}</td> 
                                <td  colspan="2" class="med-green font600">State : <span>{{ $provider->state_1 }}</span></td>
                            </tr>

                            <tr>
                                <td>State License</td>
                                <td>{{ $provider->statelicense_2 }}</td> 
                                <td  colspan="2" class="med-green font600">State : <span>{{ $provider->state_2 }}</span></td>
                            </tr>

                            <tr>
                                <td>State License</td>
                                <td>{{ $provider->specialitylicense }}</td> 
                                <td colspan="2" class="med-green font600">State : <span>{{ $provider->state_speciality }}</span></td>
                            </tr>

                            <tr>
                                <td>DEA Number</td>
                                <td>{{ $provider->deanumber }} </td>
                                <td colspan="2" class="med-green font600">State : <span>{{ $provider->state_dea }}</span></td>
                            </tr>

                            <tr>
                                <td>TAT</td>
                                <td>{{ $provider->tat }} </td>
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <td>Mammography Cert#</td>
                                <td>{{ $provider->mammography }}</td>
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <td>Care Plan Oversight#</td>
                                <td>{{ $provider->careplan }}</td>
                                <td colspan="2"></td>
                            </tr>


                        </tbody>

                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->        
           

            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table class="table-responsive table-striped-view table">                    
                        <tbody>
                            <tr>
                                <td>Requires Supervision </td>
                                <td> <span class="patient-status-bg-form @if($provider->req_super == 'Yes')label-success @else label-danger @endif">{{ $provider->req_super }}</span></td>
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <td>Default Facility</td>
                                <td>{{ !empty($facility_name)?$facility_name:'' }}</td>   
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <td>Statement Address</td>
                                <td>{{ $provider->stmt_add }}</td>
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <td>Hospice Employed</td>
                                <td> <span class="patient-status-bg-form @if($provider->hospice_emp == 'Yes')label-success @else label-danger @endif">{{ $provider->hospice_emp }}</span></td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td> @if($provider->status !='')<span class="patient-status-bg-form  @if($provider->status == 'Active') label-success  @else label-danger @endif">{{ $provider->status }}</span>@endif</td>
                                <td colspan="2"></td>
                            </tr>

							<tr>
                                <td>Status</td>
                                <td> @if($provider->provider_entity_type !='')<span class="patient-status-bg-form  @if($provider->provider_entity_type == 'Person') label-success  @else label-danger @endif">{{ (($provider->provider_entity_type == 'NonPersonEntity') ? 'Non-Person Entity' : $provider->provider_entity_type) }}</span>@endif</td>
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <td>Digital Signature</td>
                                <td>
                                    @if($provider->digital_sign_name!='')
                                        <a data-toggle="modal" href="#image-content">{!! HTML::image('img/preview.png') !!}  </a>
                                        @endif
                                </td>
                                <td colspan="2"> 
                                    
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->            
        </div><!-- Right side Content Ends -->
  
<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
     @include('practice/layouts/usps_form_modal') 
</div><!-- Modal Light Box Ends -->  

<div id="image-content" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Digital Signature</h4>
            </div>

            <div class="modal-body">
                <center>
                    <?php
						$filename = $provider->digital_sign_name.'.'.$provider->digital_sign_ext; 
                        $unique_practice = md5('P'.$practiceid_ori);
						$img_details = [];
						$img_details['module_name']='provider';
						$img_details['file_name']=$filename;
						$img_details['practice_name']=$unique_practice;
						
						$img_details['class']='img-border';
						$img_details['alt']='provider-image';
						$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
					?>
					{!! $image_tag !!}  
					</center>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

@include ('practice/layouts/npi_form_modal')
<!--End-->
@stop            