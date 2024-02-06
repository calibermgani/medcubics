@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>            
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i>Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Notes</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/customer/') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>				
				@if($checkpermission->check_adminurl_permission('admin/customernotesmedcubics/{id}/{export}') == 1)
					<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'admin/customernotesmedcubics/'.$customer->id.'/'])
					</li>
				@endif				
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/note')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	@include ('admin/customer/tabs')                                    
@stop

@section('practice')
	<div class="col-md-12"><!-- Inner Content for full width Starts -->
		<div class="box-body-block"><!--Background color for Inner Content Starts -->
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
				<div class="box box-info no-shadow">
					<div class="box-block-header with-border">
						<i class="fa fa-sticky-note font14 med-green"></i><h3 class="box-title">Notes</h3>
						<div class="box-tools pull-right margin-t-2">
						  @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customernotes/create') == 1)    
								<a href="{{ url('admin/customer/'.$customer->id.'/customernotes/create') }}" class="font600 font14"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i> New</a>
							@endif
							
						</div>
					</div><!-- /.box-header -->
					<!-- form start -->

					<div class="box-body"><!-- Box Body Starts -->     
						 @if(count($notes) > 0)
							@foreach($notes as $note)
							<?php $note->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($note->id,'encode'); ?>  
							<div class="col-lg-12 col-md-12 col-sm-12"><!-- Inner width Starts -->  						  
								<div class="timeline-messages">
								<div class="msg-time-chat">
									<a href="#" class="message-img">{!! HTML::image('img/notes.png')!!}</a>
									<div class="message-body msg-in">
										<div class="text notes">
											<p class="attribution">
												<a href="customernotes/{{ $note->id }}/edit">{{ $note->title }} </a>  
												<span class='notesdate'>
												@if($checkpermission->check_adminurl_permission('admin/customer/{id}/customernotes/{customernotes}/edit') == 1)
													<a href="{{ url('admin/customer/'.$customer->id.'/customernotes/'.$note->id.'/edit') }}"><i class="livicon tooltips" data-placement="bottom"  data-name="edit" data-color="#009595" data-size="16" data-title='Edit Note' data-hovercolor="#009595"></i></a>
												@endif													@if($checkpermission->check_adminurl_permission('admin/customer/{cust_id}/customernotes/delete/{customernotes_id}') == 1)
													<a class="js-delete-confirm" data-text="Are you sure to delete the entry?" href="{{ url('admin/customer/'.$customer->id.'/customernotes/delete/'.$note->id) }}"><i class="livicon tooltips" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Note' data-hovercolor="#009595"></i></a>
												@endif
												<span>{{ App\Http\Helpers\Helpers::dateFormat(@$note->created_at)}}</span> | {{ App\Http\Helpers\Helpers::dateFormat(@$note->created_at,'timestamp')}}
											</p>
											<p class="notes-msg">{{ $note->content }}</p>
											<div class="box-comment margin-t-5">
												<?php
														$img_details = [];
														$filename = @$note->user->avatar_name . '.' . @$note->user->avatar_ext; 
													?>

												@if(@$note->user->practice_user_type == "customer")
												<?php $img_details['module_name']='customers';
													$img_details['practice_name']="admin"; ?>
												@else
												<?php $img_details['module_name']='user';
													$img_details['practice_name']="admin"; ?>
												@endif
												<?php 
														$img_details['file_name']=$filename;
														$img_details['class']='img-circle img-sm';
														$img_details['alt']='customers-image';
														$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
													?>
												{!! @$image_tag !!}
												<div class="comment-text">
													<span class="username">
														{{ @$note->user->name }} 
														<span class="text-muted pull-right med-gray"><span class="med-gray">Last Modified:</span> {{ App\Http\Helpers\Helpers::dateFormat($note->updated_at,'datetime') }}</span>
													</span><!-- /.username -->
													{!! @$note->user->designation !!}
												</div>
												<!-- /.comment-text -->
											</div>
											
										</div>                                    
									</div>
								</div>
							</div>
						</div><!-- Inner width Ends -->    
					 @endforeach
					@else
						<div class="alert">No records found</div>
					@endif
					</div> <!-- Box Body Ends -->    
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div><!--/.col (left) -->
		</div><!--Background color for Inner Content Ends -->
	</div><!-- Inner Content for full width Ends -->       
@stop    