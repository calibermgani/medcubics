<?php $current_tab = Request::segment(2) ?>
@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Security Code <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Pending Approval</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="{{url('help/reason_for_visit')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop

@section('practice-info')
	@include ('practice/userloginhistory/tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" ><!-- Col-12 starts -->
    <div class="box box-info no-shadow"><!-- Box Starts -->
        <div class="box-header margin-b-10">
            <i class="fa fa-bars font14"></i> <h3 class="box-title">@if($current_tab == 'pendingApproval') Pending Approval @elseif($current_tab == 'approvedIp') Approved IP @endif</h3>
            <div class="box-tools pull-right margin-t-2">	
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">	<!-- Box Body Starts -->
            <div class="table-responsive">
			
                <table id="example1" class="table table-bordered table-striped table-separate">         
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email ID</th>
                            @if(Auth::user()->practice_user_type == 'customer')
                            <th>Practice Name</th>
                            @endif
                            <th>Security Code</th>
                            <th>IP </th>
                            <th>Approval</th>
                            <th># of Attempts</th>
                            <th>Login Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($userLoginInfo))
						 @foreach($userLoginInfo as $list) 
							<tr>
								<td>{{ $list->user->short_name }}</td>
								<td>{{ $list->user->email }}</td>
								@if(Auth::user()->practice_user_type == 'customer')
									<td>{{ App\Http\Helpers\Helpers::getPracticeNames($list->user->admin_practice_id,$list->user->id) }}</td>
								@endif
								<td>{{ $list->security_code }}</td>
								<td>{{ $list->ip_address }}</td>
								<td>{{ $list->approved }}</td>
								<td class="attempt_code_{{ $list->id }}">{{ $list->security_code_attempt }}</td>
								<td>
									{{ App\Http\Helpers\Helpers::dateFormat($list->created_at, 'date') }}
								</td>
								<td class="text-right margin-r-10"><a data-user-id="{{ $list->user->id }}" class="js-block cur-pointer" ><i class="fa fa-ban" data-placement="bottom"  data-toggle="tooltip" data-original-title="Block"></i></a> <a class="margin-l-5 margin-r-5 med-gray">|</a> <a data-userip-id="{{ $list->id }}" class="js-reset-code cur-pointer" ><i class="fa fa-refresh" data-placement="bottom"  data-toggle="tooltip" data-original-title="Reset"></i></a></td>
							</tr>
						@endforeach
						
						@endif
                    </tbody>
                </table>
			</div>                                
        </div><!-- /.box-body ends -->
    </div><!-- /.box  ends -->
</div><!-- Col-12 Ends -->

@stop

@push('view.scripts')
<script>
    $(document).ready(function(){
        $('small>span').text($("ul.nav-tabs>li.active").text());
    })
	
	$(document).on('click','.js-block',function(){
		var user_id = $(this).attr('data-user-id');
		$.ajax({
            url: api_site_url + '/userStatusChange',
            type: 'post',
            data: {'_token':'<?php echo csrf_token(); ?>','user_id':user_id},
            success: function (data) {
				js_sidebar_notification(data,'Successfully user status changed');
			}
		});		
	});
	
	$(document).on('click','.js-reset-code',function(){
		var userip_id = $(this).attr('data-userip-id');
		$.ajax({
            url: api_site_url + '/userIpSecurityCodeRest',
            type: 'post',
            data: {'_token':'<?php echo csrf_token(); ?>','userip_id':userip_id},
            success: function (data) {
				js_sidebar_notification(data,'Successfully security attempt reset');
				$('td.attempt_code_'+userip_id).html('0');
			}
		});		
	});
</script>
@endpush