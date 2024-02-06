@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Notes</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('provider/'.$provider->id) }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @if(count($notes)>0)
                <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                    @include('layouts.practice_module_export', ['url' => 'api/providernotesreports/provider/'.$provider->id.'/'])
               </li>
            @endif
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
    @include ('practice/provider/tabs')  
@stop
    
@section('practice')
	<div class="col-lg-12">
		@if(Session::get('message')!== null) 
		<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
		@endif
	</div>
    
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" >
		<div class="box box-info no-shadow">
			<div class="box-block-header with-border">
				<i class="fa fa-sticky-note font14 med-green"></i>  <h3 class="box-title">Notes</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_url_permission('provider/{id}/notes/create') == 1)				
					<a class="js-notes font600 font14 margin-r-10" href="#"  data-toggle = 'modal' data-target="#create_notes" data-url="provider/{{$provider->id}}/notes/create" tabindex="-1"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Note</a>
					@endif	
				</div>
			</div><!-- /.box-header -->
			<!-- form start -->

			<div class="box-body"><!-- Box Body Starts -->     
				@if(count($notes) > 0)
				@foreach($notes as $note)
				@if(!empty($note))
				<?php $note->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$note->id,'encode'); ?>
					<div class="col-lg-12 col-md-12 col-sm-12"><!-- Inner width Starts -->  
						<div class="timeline-messages">
							<div class="msg-time-chat">
								<a href="#" class="message-img">{!! HTML::image('img/notes.png')!!}</a>
								<div class="message-body msg-in">
									<div class="text notes box-comments">
										<p class="attribution">
										   <a href="{{ url('provider/'.$provider->id.'/notes/'.@$note->id.'/edit') }}">{{ @$note->title }} </a>   
											<span class='notesdate'>
												@if($checkpermission->check_url_permission('provider/{id}/notes/{notes}/edit'))
													<a class="js-notes" href="#"  data-toggle = 'modal' data-target="#create_notes" data-url="provider/{{$provider->id}}/notes/{{@$note->id}}/edit" tabindex="-1"><i class="livicon tooltips" style="margin-right: 2px; margin-left: 3px;" data-placement="bottom"  data-name="edit" data-color="#009595" data-size="17" data-title='Edit Note' data-hovercolor="#009595" title="Edit"></i></a>
												@endif	
												@if($checkpermission->check_url_permission('provider/{provider_id}/notes/delete/{id}'))
													<a href="{{ url('provider/'.$provider->id.'/notes/delete/'.@$note->id) }}" class="js-delete-confirm" data-text="Are you sure to delete the entry?"><i class="livicon tooltips" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Note' data-hovercolor="#009595"></i></a>
												@endif	
												<span>{{ App\Http\Helpers\Helpers::dateFormat(@$note->created_at,'datetime')}}</span>
											</span>
										</p>
										<p class="notes-msg">{{ @$note->content }}</p>
									
										<div class="box-comment margin-t-5">
											<?php
												$img_details = [];
												$filename = @$note->user->avatar_name . '.' . @$note->user->avatar_ext; 
											?>
											
											@if(isset($note->user) && $note->user->practice_user_type == "customer")
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
											{!! $image_tag !!}
											<div class="comment-text">
												<span class="username">
													{{ App\Http\Helpers\Helpers::shortname(@$note->created_by) }}
													@if($note->updated_by != '0')
													<span class="text-muted pull-right med-gray"><span class="med-gray">Last Modified:</span> {{ App\Http\Helpers\Helpers::dateFormat($note->updated_at,'datetime') }}</span>
													</span><!-- /.username -->
													@endif
												{!! @$note->user->designation !!}
											</div>
											<!-- /.comment-text -->
										</div>
									</div>                                    
								</div>
							</div>
						</div>						
					</div><!-- Inner width Ends --> 
					@endif	
				@endforeach
			@else
				<p class="med-gray text-center no-bottom">No Records Found</p>
			@endif
			</div> <!-- Box Body Ends -->    
		</div><!-- /.box-body -->               
	</div><!-- /.box -->     
@stop    