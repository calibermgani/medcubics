@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>            
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>Users <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>
						<?php $customeruser_name = App\Http\Helpers\Helpers::getNameformat("$customerusers->lastname","$customerusers->firstname",""); 
                        $customerusers_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($customerusers->id,'encode');
                        $customer_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($customer->id,'encode');
                        ?> 
						{{ $customeruser_name }} </span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/customer/'.$customer_id.'/customerusers/') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/user')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')
@include ('admin/customer/tabs')
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">            @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerusers/{customerusers}/edit') == 1)
            <a href="{{ url('admin/customer/'.$customer_id.'/customerusers/'.$customerusers_id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a></li>
            @endif
        </div>
        
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="user"></i> <h3 class="box-title">User Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table class="table-responsive table-striped-view table">    
                        <tbody>
                            <tr>
                                <td>Customer name</td>
                                <?php
								@$filename =@$user->avatar_name . '.' .@$user->avatar_ext;
								$img_details = [];
								$img_details['module_name']='user';
								$img_details['file_name']=$filename;
								$img_details['practice_name']='admin';
								$img_details['alt']='user-image';
								$img_details['style']='height:50px;width:50px;';
								$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
							?>
                                <td>{{ @$customerusers->customer->customer_name }}</td>
                            </tr>
                            <tr>
                                <td>Last name</td>
                                <td>{{ @$customerusers->lastname }}</td>                               
                            </tr>
                            <tr>
                                <td>First name</td>
                                <td>{{ @$customerusers->firstname }}</td>
                            </tr>
							<tr>
                                <td>Short name</td>
                                <td>{{ @$customerusers->short_name }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{!! $image_tag !!}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>                               
                                <td>@if($customerusers->dob != '0000-00-00')
								<span class="bg-date">	
                                {{ App\Http\Helpers\Helpers::dateFormat(@$customerusers->dob,'dob') }}
								</span>
                                @endif	
								</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>{{ @$customerusers->gender }}</td>
                            </tr>
                            <tr>
                                <td>Designation</td>
                                <td>{{ @$customerusers->designation }}</td>
                            </tr>
                            <tr>
                                <td>Department</td>
                                <td>{{ @$customerusers->department }}</td>
                            </tr>
                            <tr>
                                <td>Language</td>
                                <td>{{ @$customerusers->language->language }}</td>
                            </tr>
                            <tr>
                                <td>Ethnicity</td>
                                <td>{{ @$customerusers->ethnicity->name }}</td>
                            </tr>
                            
                            @if($customerusers->practice_user_type == 'practice_admin')
								<?php $practicearraylist = json_decode(json_encode($practicelist), True);
									   $getpractice = explode(",",$customerusers->admin_practice_id);
								?>
                            <tr>
                                <td>Practice List</td>	
                                <td>
                                    @foreach($getpractice as $practiceid)
                                    {{ @$practicearraylist[$practiceid].',' }} 
                                    @endforeach
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="address-book"></i> <h3 class="box-title">Contact details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table class="table-responsive table-striped-view table">    
                        <tbody>
                            <tr>
                                <td>Address Line 1</td>
                                <td>{{ $customerusers->addressline1 }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Address Line 2</td>
                                <td>{{ @$customerusers->addressline2 }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>City</td>
                                <td>{{ @$customerusers->city }} @if(@$customerusers->state != '') - <span class=" bg-state ">{{ @$customerusers->state }}</span>@endif</td>
								<td></td>
                            </tr>
                            <tr>
                                <td>Zip Code</td>
                                <td>{{ @$customerusers->zipcode5 }} @if(@$customerusers->zipcode4 != '')- {{ $customerusers->zipcode4 }}@endif</td>
                                <td>
                                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match'],'show'); ?>   
                                    <?php echo $value; ?>                                    
                                </td>                                
                            </tr>
                            <tr>
                                <td>Cell Phone</td>
                                <td>{{ @$customerusers->phone }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Fax</td>
                                <td>{{ @$customerusers->fax }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>E-mail</td>
                                <td><a href="mailto:{{ $customerusers->email }}">{{ @$customerusers->email }}</a></td>
                                <td></td>
                            </tr>
                            <tr>
                                @if(($customerusers->facebook_ac)!='')
									<td>Facebook</td>
									<td>{{ $customerusers->facebook_ac }}</td>
									<td></td>
                                @endif
                            </tr>
                            <tr>
                                @if(($customerusers->twitter)!='')
									<td>Twitter</td>
									<td>{{ $customerusers->twitter }}</td>
									<td></td>
                                @endif
                            </tr>
                            <tr>
                                @if(($customerusers->linkedin)!='')
									<td>Linkedin</td>
									<td>{{ $customerusers->linkedin }}</td>
									<td></td>
                                @endif
                            </tr>
                            <tr>
                                @if(($customerusers->googleplus)!="")
									<td>Google+</td>
									<td>{{ $customerusers->googleplus }}</td>
									<td></td>
                                @else
                                <td></td>
                                @endif
                            </tr>
                            @if(empty($customerusers->admin_practice_id))
                            @if(@$customerusers->useraccess != "app")
								<tr>
								  @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerusers/{id}/setpracticeforusers') == 1)
										<td>Set Permission</td><td>
										<a href="{{ url('admin/customer/'.@$customer_id.'/customerusers/'.$customerusers_id.'/setpracticeforusers') }}" class="font600 med-orange">Click Here</a>
										@endif
									</td><td></td>
								</tr>

                                @if(!empty($practices) && isset($practices))
                                <tr>
                               
                                        <td>Permission List</td><td>
                                            <?php $practice_name = array(); ?>
                                            @foreach($practices as $practice)
                                           <?php  $practice_name[] = $practice->practice_name; ?>
                                            @endforeach
                                          <?php echo implode(',',$practice_name);?>
                               
                                   
                                    </td><td></td>
                                </tr> 
                                @endif 
                            @endif  
                            @endif	
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
  
<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- /.modal  Ends-->

@stop 