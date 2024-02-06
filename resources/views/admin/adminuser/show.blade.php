@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font14"></i> Admin User <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <?php $adminusers->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($adminusers->id,'encode'); ?>	
            <li><a href="{{ url('admin/adminuser')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/admin_user')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-m-18">  
    @if($checkpermission->check_adminurl_permission('admin/adminuser/{adminuser}/edit') == 1)
    <a href="{{ url('admin/adminuser/'.$adminusers->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
    @endif
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view js-address-class" id="js-address-business-address">
            <i class="livicon" data-name="sign-in"></i> <h3 class="box-title">Login Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table-responsive table-striped-view table">    
                <tbody>
                    @if(@$adminusers->practice_user_type == "practice_admin")
                    <?php  $practicearraylist = json_decode(json_encode($practicelist), True);
                        $getpractice = explode(",",@$adminusers->admin_practice_id);
                    ?>
                    <tr>
                        <td>Customer Name</td>
                        <td>{{ @$adminusers->customer->customer_name }}</td>
                    </tr>
                    <tr>
                        <td>Practice List</td>
                        <td>
                            @foreach($getpractice as $practiceid)
                            {{ @$practicearraylist[$practiceid].',' }} 
                            @endforeach
                        </td>
                    </tr>
					@elseif(@$adminusers->practice_user_type == "provider")
						<?php  
							$practicearraylist = json_decode(json_encode($practicelist), True);
							$getpractice = explode(",",@$adminusers->admin_practice_id);
							$provider_name = json_decode(json_encode($provider), True);							
							$getprovider = @$adminusers->provider_access_id;
						?>
						<tr>
							<td>Customer Name</td>
							<td>{{ @$adminusers->customer->customer_name }}</td>
						</tr>
						<tr>
							<td>Practice List</td>
							<td>
								@foreach($getpractice as $practiceid)
								{{ @$practicearraylist[$practiceid].',' }} 
								@endforeach
							</td>
						</tr>
						<tr>
							<td>Provider Name</td>
							<td>{{ @$provider_name[$getprovider] }}</td>
						</tr>
                    @elseif(@$adminusers->practice_user_type == "practice_user")
						<tr>
							<td>Customer Name</td>
							<td>{{ @$adminusers->customer->customer_name }}</td>
						</tr>
						@if(@$adminusers->useraccess == "web")
							<?php  
								$practicearraylist = json_decode(json_encode($practicelist), True);
								$getpractice = explode(",",@$adminusers->admin_practice_id);
							?>
							<tr>
								<td>Practice List</td>
								<td>
									@foreach($getpractice as $practiceid)
									{{ @$practicearraylist[$practiceid].',' }} 
									@endforeach
								</td>
							</tr>
						@else
							<tr>
								<td>App Name</td>
								<td>{{ @$adminusers->app_name }}</td>
							</tr>
							<tr>
								<td>Practice Name</td>
								<td>{{ @$adminusers->practice->practice_name }}</td>
							</tr>
							@if(@$adminusers->app_name == "WEB")
								<?php  
									$facility_name = json_decode(json_encode($facility), True);
									$getfacility = @$adminusers->facility_access_id;
								?>
								<tr>
									<td>Facility Name</td>
									<td>{{ @$facility_name[$getfacility] }}</td>
								</tr>
							@else
								<?php  
									$provider_name = json_decode(json_encode($provider), True);
									$getprovider = @$adminusers->provider_access_id;
								?>
								<tr>
									<td>Provider Name</td>
									<td>{{ @$provider_name[$getprovider] }}</td>
								</tr>
							@endif
						@endif                    
                    @endif
                    <tr>
                        <td>Role Type</td>
                        <td>{{ @$adminusers->role->role_name }}</td>
                    </tr>
                    <tr>
                        <td>User Name</td>
                        <?php
                            @$filename =@$adminusers->avatar_name . '.' .@$adminusers->avatar_ext;
                            $img_details = [];
                            $img_details['module_name']='user';
                            $img_details['file_name']=$filename;
                            $img_details['practice_name']='admin';
                            $img_details['alt']='user-image';
                            $img_details['style']='height:20px;width:20px;';
                            $image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
						?>
                        <td>
							{{ $adminusers->name }} 
                            @if(@$adminusers->avatar_name != "")
                            {!!@$image_tag!!}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td class="text-lowercase"><a href="mailto:{{ $adminusers->email }}">{{ $adminusers->email }}</a></td>
                    </tr>
                    <tr>
                        <td>User Type</td>
                        <td>{{ $adminusers->user_type }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><span class="patient-status-bg-form @if($adminusers->status == 'Active') label-success @else label-danger @endif">{{ $adminusers->status }}</span></td>
                    </tr>    
                </tbody>
            </table>
            <div class="bottom-space-10 hidden-sm hidden-xs">&emsp;</div>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->

    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view js-address-class" id="js-address-business-address">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table-responsive table-striped-view table">    
                <tbody>
                    <tr>
                        <td>Designation</td>
                        <td>{{ @$adminusers->designation }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Ethnicity</td>
                        <td>{{ @$adminusers->ethnicity->name }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Address Line 1</td>
                        <td>{{ @$adminusers->addressline1 }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Address Line 2</td>
                        <td>{{ @$adminusers->addressline2 }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td>{{ @$adminusers->city }} @if($adminusers->state != '') - <span class=" bg-state ">{{ $adminusers->state }}</span>@endif</td>                               
                        <td>
                            <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
                            <?php echo $value; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Zip Code</td>
                        <td>{{ @$adminusers->zipcode5 }} @if($adminusers->zipcode4 != '')- {{ $adminusers->zipcode4 }}@endif</td>
                        <td></td> 
                    </tr>
                    <tr>
                        <td>Cell Phone</td>
                        <td>{{ @$adminusers->phone }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Fax</td>
                        <td>{{ @$adminusers->fax }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        @if(($adminusers->facebook_ac)!='')
                        <td>Facebook</td>
                        <td>{{ @$adminusers->facebook_ac }}</td>
                        <td></td>
                        @endif
                    </tr>
                    <tr>
                        @if(($adminusers->twitter)!='')
                        <td>Twitter</td>
                        <td>{{ $adminusers->twitter }}</td>
                        <td></td>
                        @endif
                    </tr>
                    <tr>
                        @if(($adminusers->linkedin)!='')
                        <td>Linkedin</td>
                        <td>{{ $adminusers->linkedin }}</td>
                        <td></td>
                        @endif
                    </tr>
                    <tr>
                        @if(($adminusers->googleplus)!="")
                        <td>google+</td>
                        <td>{{ $adminusers->googleplus }}</td>
                        <td></td>
                        @else
                        <td></td>
                        @endif
                    </tr>                        
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view js-address-class" id="js-address-business-address">
            <i class="livicon" data-name="user"></i> <h3 class="box-title">User Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table-responsive table-striped-view table">    
                <tbody>                    
                    <tr>
                        <td>Last name</td>
                        <td>{{ @$adminusers->lastname }}</td>
                    </tr>
                    <tr>
                        <td>First name</td>
                        <td>{{ @$adminusers->firstname }}</td>
                    </tr>   
                    <tr>
                        <td>Short name</td>
                        <td>{{ @$adminusers->short_name }}</td>
                    </tr>   
                    <tr>
                        <td>Gender</td>
                        <td>{{ @$adminusers->gender }}</td>
                    </tr>
                    <tr>
                        <td>Date of Birth</td>                               
                        <td>
                            @if($adminusers->dob != '0000-00-00')
                            <span class="bg-date">	
                                {{ App\Http\Helpers\Helpers::dateFormat(@$adminusers->dob,'dob') }}
                            </span>
                            @endif								
                        </td>		
                    </tr>
                    <tr>
                        <td>Language</td>
                        <td>{{ @$adminusers->language->language }}</td>
                    </tr>
                    <tr>
                        <td>Department</td>
                        <td>{{ @$adminusers->department }}</td>
                    </tr>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- /.modal  Ends-->  
<!--End-->
@stop 