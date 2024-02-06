@if(!empty($uploadInfo))
	@foreach($uploadInfo as $list)
		<?php $encId = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$list->id, 'encode'); ?>
		@if(isset($list->user))
		<tr class="form-cursor uploadList" id="{{ $list->id }}">
			<td class="js-table-click">{{ $list->org_filename }}</td>
			<td class="js-table-click">{{ nl2br($list->msg)	}}</td>
			<td class="js-table-click js_totpat{{ $list->id }}">{{ $list->total_patients }}</td>			
			<td class="js-table-click js_comppat{{ $list->id }}" >{{ $list->completed_patients }}</td>
			<td class="js-table-click js_totchr{{ $list->id }}">{{ $list->total_charges }}</td>
			<td class="js-table-click js_compchr{{ $list->id }}" >{{ $list->failed_charges }}</td>
			<td class="js-table-click js_status{{ $list->id }}">{{ $list->status }}</td>
			<td class="js-table-click">{{ $list->user->short_name }}</td>
			<td class="js-table-click">
				<!--
				//Patient upload created at timezone issue
				//Revision 1 - Ref: MR-2588 01 Aug 2019: Ravi				
				-->
				{{ App\Http\Helpers\Helpers::timezone(@$list->created_at, 'm/d/y') }}
			</td>
			<td class="">
				@if($list->status == 'Pending')
					<a relUrl="{{ url('/processupload/'.$encId) }}" id="{{$encId}}" class="selProcessUpload js_process{{ $list->id }}" title="Start Process"><i class="fa fa-refresh"></i></a>
					&nbsp;|&nbsp;
				@endif
				
				@if($list->file_name != '' )
					<a href="{{ url('/downloaduploadedfile/'.$list->id) }}" target="_blank" title="Uploaded Document"><i class="fa fa-file-excel-o"></i></a>
				@endif
				
				@if(($list->status == 'Completed' || $list->status == 'Failed') && $list->resp_filename != '' )&nbsp;|&nbsp;
					<a href="{{ url('/downloaduploadedrespfile/'.$list->id) }}" target="_blank" title="Response Document" ><i class="fa fa-file-excel-o"></i></a>
				@endif
				
				@if($list->error_msg != '')
					&nbsp;|&nbsp;					
					<a data-search-id="{{ $list->id }}" class='cur-pointer someelem font600' data-id="search{{ $list->id }}" id="someelemsearch{{$list->id}}"><i class="fa fa-exclamation-triangle" aria-hidden="true" title=""></i></a>					
					<div class="js-tooltip_search{{ $list->id }}" style="display:none;">
						<span style="border-bottom:1px dashed #e0d775; display:block; padding-bottom: 2px; margin-bottom: 5px;">{{ $list->error_msg }}</span>
					</div>	
				@endif				
			</td>			
		</tr>
		@endif
	@endforeach
@else
	<tr>
		<td colspan="10">No Records Found</td>
	</tr>
@endif