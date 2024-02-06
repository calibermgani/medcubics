@extends('admin')
@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.ticket')}} font14"></i> Manage Ticket <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>All Ticket</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('api/admin/manageticket/{export}') == 1)
				<li class="dropdown messages-menu hide"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'admin/api/manageticketreports/'])
            </li>
            @endif
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="#js-help-modal" data-url="{{url('help/manageticket')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('admin/manageticket/tabs')
@stop

@section('practice')
<div class="col-lg-12">
    @if(Session::get('message')!== null)
		<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!-- Inner Content for full width Starts -->
	<div class="col-xs-12">
		<div class="box box-info no-shadow">
			<div class="box-header margin-b-10">
				<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Ticket List</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_adminurl_permission('admin/createnewticket') == 1)
						<a href="{{ url('admin/createnewticket') }}" class="font600 font14"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="Create New Ticket"></i> New</a>
					@endif
				</div>
			</div><!-- /.box-header -->
			<div class="box-body">
				<div class="table-responsive">
					<table id="list_noorder" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Ticket ID</th>
								<th>Name</th>
								<th>Email</th>
								<th>Title</th>
								<th>Status</th>
								<th>Notification Sent</th>
								<th>Assigned</th>
							</tr>
						</thead>
						<tbody>
							@foreach($ticket as $manageticket)
							<?php
								$manageticket->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($manageticket->id,'encode'); 
								$assigneduserid	= App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($manageticket->assigned,'encode')
							?>
							<tr data-url="{{ url('admin/manageticket/'.$manageticket->id) }}" class="js-table-click clsCursor">
								<td>{{ $manageticket->ticket_id }}</td>
								<td>{{ str_limit($manageticket->name, 50, '..') }}</td>
								<td>{{ str_limit($manageticket->email_id, 50, '..') }}</td>
								<td>{{ str_limit($manageticket->title) }}</td>
								<td>{{ ($manageticket->assigned == '0' && $manageticket->status == 'Open' && $manageticket->read == '0')? 'New' : $manageticket->status }}</td>
								<td>{{ $manageticket->notification_sent }}</td>
								<td class="changeassigntype{{$manageticket->ticket_id}}">
								
									@if($manageticket->assigned == 0)
										@if($manageticket->status != 'Closed')
											<a data-url="{{ url('admin/assignticket/')}}" data-ticketid="{{$manageticket->ticket_id}}" data-backdrop="false" data-toggle="modal" data-userid="" class="js_ticketassign tooltips margin-l-10 font600 med-orange text-underline"  data-target="#ticketassign_modal" href="#">Assign</a>
										@endif
									@else
										{{ $manageticket->get_assignee->name  }}
										
										@if($manageticket->status != 'Closed')
											<a data-url="{{ url('admin/assignticket/')}}" data-ticketid="{{$manageticket->ticket_id}}" data-backdrop="false" data-toggle="modal" data-userid="{{ $assigneduserid }}" class="js_ticketassign tooltips margin-l-10 font600 med-orange text-underline" data-target="#ticketassign_modal" href="#">Reassign</a>
										@endif
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div><!-- Inner Content for full width Ends -->
<!--End-->
@stop