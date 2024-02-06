@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font14"></i>  Users</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('api/admin/adminuserexport/{export}') == 1)
				<li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/adminuserexport/'])
				</li>
            @endif
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="#js-help-modal" data-url="{{url('help/admin_user')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12">
    @if(Session::get('message')!== null) 
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->

    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">All Users</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_adminurl_permission('admin/adminuser/create') == 1)
					<a href="{{ url('admin/adminuser/create') }}" class="font600 font14"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i> Add New User</a>
                @endif
            </div>
        </div>
		<?php /*
		<!-- /.box-header Hide Admin User List 
        <div class="box-body table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Role</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adminusers as $user) 
                    <?php $user->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($user->id,'encode'); ?>	
                    <tr data-url="{{ url('admin/adminuser/'.$user->id) }}" class="js-table-click clsCursor">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->gender }}</td>
                        <td>{{ @$user->role->role_name }}</td>
                        <td>{{ App\Http\Helpers\Helpers::shortname($user->created_by) }}</td>
                        <td>{{ App\Http\Helpers\Helpers::shortname($user->updated_by) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
		*/ ?>
        <!-- /.box-header --> 
        <div class="box-body table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>User Access</th>
                        <th>Practice</th>
                        <th>Created On</th>
                        <th>Last Access</th>
                        <th>Password Updated On</th>
						<th>Security Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adminusers as $user)
                   
                    <?php 
						$userid = $user->id;
						@$admin_practice = $user->admin_practice_id;
						@$admin_practice_name = App\Models\Medcubics\Users::adminPracticeName(@$admin_practice);
						$userEncId = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($userid, 'encode'); 
					?>
                    <tr data-url="{{ url('admin/adminuser/'.$userEncId) }}" class="js-table-click clsCursor">
                        <td>{{ @$user->short_name }}</td>
                        <td>{{ @$user->email }}</td>
                        <td>{{ @$user->user_type }} - {{ @$user->practice_user_type }}</td>
                        <td>{{ @$user->useraccess }} - {{ @$user->app_name }}</td>
                        @if($user->useraccess == "web" && ($user->practice_user_type == "practice_admin" || $user->practice_user_type == "practice_user" ))
							<td>{{ @$admin_practice_name }}</td>
                        @elseif($user->useraccess == "app" && $user->practice_user_type == "practice_user")
							<td>{!! @$user->practice_name->practice_name!!}</td>
                        @else
							<td>-</td>
                        @endif
                        <td>{{ date('m/d/Y H:i:s', strtotime( @$user->created_at )) }}</td>
                        @if($user->last_access_date == '0000-00-00 00:00:00')
							<td>-NA-</td>
                        @else
							<td>{{ date('m/d/Y H:i:s', strtotime( @$user->last_access_date )) }}</td>
                        @endif
                        @if($user->password_change_time == '0000-00-00 00:00:00')
							<td>-NA-</td>
                        @else
							<td>{{ date('m/d/Y H:i:s', strtotime( @$user->password_change_time )) }}</td>
						
                        @endif
						
						<td>						
						<a  href="javascript:void(0);" >
                                <i class="updateCode" id="update_security_code_<?php echo $userid; ?>" data-placement="bottom" data-toggle="tooltip" data-original-title="Yes or No Security Code" data-id="{{ $userid }}">{{@$user->security_code }}</i>
                            </a>
						</td>
						
                        <td>
                            <a href="{{ url('admin/adminuser/'.$userEncId.'/edit')}}">
                                <i class="fa fa-edit font13 margin-r-5" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit"></i>
                            </a>

                            <a  href="javascript:void(0);" >
                                <i class="fa fa-refresh font13 margin-r-5 cur-pointer loginAttempt" id="userid_loginAttempt_<?php echo $userid; ?>" data-placement="bottom" data-toggle="tooltip" data-original-title="Reset Password Attempt" data-id="{{ $userid }}"></i>
                            </a>

                            <a  href="javascript:void(0);" >
                                <i class="fa fa-sign-out font13 margin-r-5 cur-pointer med-orange updatelogoutUser " id="user_id_logout_<?php echo $userid; ?>" data-placement="bottom" data-toggle="tooltip" data-original-title="Logout" data-id="{{ $userid }}"></i>
                            </a>

                            <a  href="javascript:void(0);" >
                                <i class="fa fa-close font13 margin-r-5 cur-pointer updateStatus" id="user_id_status_<?php echo $userid; ?>" data-placement="bottom" data-toggle="tooltip" data-original-title="Deactive or Active User" data-id="{{ $userid }}"></i>
                            </a>

                            <a  href="javascript:void(0);" >
                                <i class="fa fa-plus-circle font13 margin-r-5 cur-pointer " id="" data-placement="bottom" data-toggle="tooltip" data-original-title="Set Practise" data-id="{{ $userid }}"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->

</div>
<input type="hidden" name="token" value="{{ csrf_token() }}"/>
@stop