@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
		<h1>
			<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font14"></i> Employer <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Notes</span></small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="{{ url('employer/'.$employer->id) }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			<!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
			@include('layouts.practice_module_export', ['url' => 'api/employernotesreports/employer/'.$employer->id.'/'])
			</li>
			<li><a href="#js-help-modal" data-url="{{url('help/employers')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a>
			</li>
		</ol>
	</section>
</div>
@stop

@section('practice-info')
<div class="row-fluid">
	@include ('practice/employer/tabs')
</div>              
@stop
    
@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >

	<div class="box box-info no-shadow">
		<div class="box-block-header with-border">
			<i class="fa fa-sticky-note"></i><h3 class="box-title">Notes</h3>
			<div class="box-tools pull-right margin-t-2">
			   @if($checkpermission->check_url_permission('employer/{id}/notes/create') == 1)
			   <a href="{{ url('employer/'.$employer->id.'/notes/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i> Add</a>           
			@endif  
			</div>
		</div><!-- /.box-header -->
		<!-- form start -->

		<div class="box-body"><!-- Box Body Starts -->     
			 @if(count($notes) > 0)
				@foreach($notes as $note)
				<div class="col-lg-12 col-md-12 col-sm-12"><!-- Inner width Starts -->  
				  <?php $note->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($note->id,'encode'); ?>
					<div class="timeline-messages">
					<div class="msg-time-chat">
						<a href="#" class="message-img">{!! HTML::image('img/notes.png')!!}</a>
						<div class="message-body msg-in">
							<div class="text notes">
								<p class="attribution">
									<a href="{{ url('employer/'.$employer->id.'/notes/'.$note->id.'/edit') }}">{{ $note->title }} </a>  
									<span class='notesdate'>
									@if($checkpermission->check_url_permission('employer/{id}/notes/{notes}/edit') == 1)
										<a href="{{ url('employer/'.$employer->id.'/notes/'.$note->id.'/edit') }}"><i class="livicon tooltips" data-placement="bottom"  data-name="edit" data-color="#009595" data-size="16" data-title='Edit Note' data-hovercolor="#009595"></i></a>
									@endif	
									@if($checkpermission->check_url_permission('employer/{provider_id}/notes/delete/{id}') == 1)	
										<a href="{{ url('employer/'.$employer->id.'/notes/delete/'.$note->id) }}" class="js-delete-confirm" data-text="Are you sure to delete the entry?"><i class="livicon tooltips" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Note' data-hovercolor="#009595"></i></a>
									@endif	
									<span>{{ App\Http\Helpers\Helpers::dateFormat(@$note->created_at)}}</span> | {{ App\Http\Helpers\Helpers::dateFormat(@$note->created_at,'timestamp')}}
									</span>
										
								</p>
								<p class="notes-msg">{{ $note->content }}</p>
								<p class="notes-space">
								<div class="row-fluid bottom-space-10">
									<div>
										<?php
												$img_details = [];
												$filename = $note->user->avatar_name . '.' . $note->user->avatar_ext; 
											?>

										@if($note->user->practice_user_type == "customer")
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
												$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar(@$img_details);
											?>
										{!! @$image_tag !!}
										<span class="note-active">{!! $note->user->name !!}</span><br>{!! $note->user->designation !!}<span class="pull-right"><span class="notesdate">Last Modified: <span class="med-orange">{{ App\Http\Helpers\Helpers::dateFormat(@$note->updated_at)}}</span> | {{ App\Http\Helpers\Helpers::dateFormat(@$note->updated_at,'timestamp')}}
										</span>
									</div>                                           
								</div>                                        
								</p>
							</div>                                    
						</div>
					</div>
				</div>
					
				</div><!-- Inner width Ends -->    
				 @endforeach
			@else
			<div class="med-gray text-center no-bottom">No Records Found</div>
			@endif
			</div> <!-- Box Body Ends -->
		</div><!-- /.box-body -->

	</div><!-- /.box -->  
@stop           