@extends('admin')

@section('toolbar')
    <?php
        if(isset($practice->encid) && $practice->encid != '' ){
            $practice->id = $practice->encid;
        }else{
            $practice->id = $practice->id;
        }
    ?>
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>            
			<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right" ></i>Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>Users</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/customer/'.$customer_id.'/customerpractices/'.$practice->id) }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'admin/practiceuserreports/customer/'.$customer_id.'/'.$practice->id.'/'])
				</li>				
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')
	@include ('admin/customer/customerpractices/tabs')
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">            
            @if($checkpermission->check_adminurl_permission('admin/customer/{customer_id}/practice/{practice_id}/practiceusers/{practice_user_id}/edit') == 1)
            <a href="{{ url('admin/customer/'.$customer_id.'/practice/'.$practice->id.'/practiceusers/'.$practice_user_id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a></li>
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
                                <td>{{ @$practiceusers->customer->customer_name }}</td>
                            </tr>
                            <tr>
                                <td>Last name</td>
                                <td>{{ @$practiceusers->lastname }}</td>                               
                            </tr>
                            <tr>
                                <td>First name</td>
                                <td>{{ @$practiceusers->firstname }}</td>
                            </tr>
							<tr>
                                <td>Short name</td>
                                <td>{{ @$practiceusers->short_name }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{!! $image_tag !!}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>                               
                                <td>@if($practiceusers->dob != '0000-00-00')
								<span class="bg-date">	
                                {{ App\Http\Helpers\Helpers::dateFormat(@$practiceusers->dob,'dob') }}
								</span>
                                @endif	
								</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>{{ @$practiceusers->gender }}</td>
                            </tr>
                            <tr>
                                <td>Designation</td>
                                <td>{{ @$practiceusers->designation }}</td>
                            </tr>
                            <tr>
                                <td>Department</td>
                                <td>{{ @$practiceusers->department }}</td>
                            </tr>
                            <tr>
                                <td>Language</td>
                                <td>{{ @$practiceusers->language->language }}</td>
                            </tr>
                            <tr>
                                <td>Ethnicity</td>
                                <td>{{ @$practiceusers->ethnicity->name }}</td>
                            </tr>
                            
                            @if($practiceusers->practice_user_type == 'practice_admin')
								<?php $practicearraylist = json_decode(json_encode($practicelist), True);
									   $getpractice = explode(",",$practiceusers->admin_practice_id);
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
                                <td>{{ $practiceusers->addressline1 }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Address Line 2</td>
                                <td>{{ @$practiceusers->addressline2 }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>City</td>
                                <td>{{ @$practiceusers->city }} @if(@$practiceusers->state != '') - <span class=" bg-state ">{{ @$practiceusers->state }}</span>@endif</td>
								<td></td>
                            </tr>
                            <tr>
                                <td>Zip Code</td>
                                <td>{{ @$practiceusers->zipcode5 }} @if(@$practiceusers->zipcode4 != '')- {{ $practiceusers->zipcode4 }}@endif</td>
                                <td>
                                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match'],'show'); ?>   
                                    <?php echo $value; ?>                                    
                                </td>                                
                            </tr>
                            <tr>
                                <td>Cell Phone</td>
                                <td>{{ @$practiceusers->phone }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Fax</td>
                                <td>{{ @$practiceusers->fax }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>E-mail</td>
                                <td><a href="mailto:{{ $practiceusers->email }}">{{ @$practiceusers->email }}</a></td>
                                <td></td>
                            </tr>
                            <tr>
                                @if(($practiceusers->facebook_ac)!='')
									<td>Facebook</td>
									<td>{{ $practiceusers->facebook_ac }}</td>
									<td></td>
                                @endif
                            </tr>
                            <tr>
                                @if(($practiceusers->twitter)!='')
									<td>Twitter</td>
									<td>{{ $practiceusers->twitter }}</td>
									<td></td>
                                @endif
                            </tr>
                            <tr>
                                @if(($practiceusers->linkedin)!='')
									<td>Linkedin</td>
									<td>{{ $practiceusers->linkedin }}</td>
									<td></td>
                                @endif
                            </tr>
                            <tr>
                                @if(($practiceusers->googleplus)!="")
									<td>Google+</td>
									<td>{{ $practiceusers->googleplus }}</td>
									<td></td>
                                @else
                                <td></td>
                                @endif
                            </tr> 
                            @if(@$practiceusers->useraccess != "app")
								<tr>
								  @if($checkpermission->check_adminurl_permission('admin/customer/{id}/practiceusers/{id}/setpracticeforusers') == 1)
										<td>Set Permission</td><td>
										<a href="{{ url('admin/customer/'.@$customer_id.'/practiceusers/'.$practice_user_id.'/setpracticeforusers') }}" class="font600 med-orange">Click Here</a>
										@endif
									</td><td></td>
								</tr> 
                            @endif	
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