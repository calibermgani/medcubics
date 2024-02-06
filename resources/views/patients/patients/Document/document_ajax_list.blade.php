<?php $user_type = Auth::user()->practice_user_type; ?>
<table id="documents" class="table table-bordered table-striped monitor-width">
	<thead>
		<tr>
			<th></th>
			<th>Created On</th> 
			<th>User</th> 
			<th>Category</th>
			<th>Sub Category</th>
			<th>Title</th>           
			<th>Patient Name</th>           
			<th>Claim No</th>
			<th>Payer</th>
			<th>Check No</th>
			<th>Check Date</th>
			<th>Check Amount</th>
			<th>Assigned To</th>
			<th>Follow up Date</th>
			<th>Status</th>
			<th>Pages</th>
			<th>Priority</th>
			<th>File Type</th>
			<th style="width: 4%"></th>                                       
		</tr>
	</thead>
	<tbody>
		@foreach($total_document as $keys=>$list)
			<?php 
				$doc_id = $list->id;
				$list->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id,'encode');
				$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode');
				$url = 'patients/'.$patient_id.'/document/get/'.$list->document_type.'/'.$list->filename;
				$category_name = (@$list->document_categories =='' || @$list->document_categories ==null) ? App\Models\Document::getDocumentCategoryName(@$list->category): @$list->document_categories->category_value; 
			?>
			 <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$doc_id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$doc_id.'/show')}}" data-document-show="js_update_row_{{ @$doc_id }}">
				<td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{url($url)}}" data-id = "{{$list->id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
				<td>{{ App\Http\Helpers\Helpers::timezone($list->created_at,'m/d/y')}}</td>
				<td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
				<td>{{ @$list->document_categories->module_name." - ".@$list->document_categories->category }}</td>
				<td>{{ @$list->document_categories->category_value }}</td>
				<td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
				<td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
				<td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
				<td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
				<td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
				<td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
				<td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
				<td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
				<td class="jsfollowup">
					<?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
					@if(date("m/d/y") == $fllowup_date)
						<span class="med-orange">{{$fllowup_date}}</span>
					@elseif(date("m/d/y") >= $fllowup_date)
						<span class="med-red">{{$fllowup_date}}</span>
					@else
						<span class="med-gray">{{$fllowup_date}}</span>
					@endif
				</td>
				<td class="jsstatus font600"><span class="{{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
				<td>{{ $list->page }}</td>
				<td class="jspriority">
					<span class="{{@$list->document_followup->priority}}">
					@if(@$list->document_followup->priority == 'High')
						<span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
					@elseif(@$list->document_followup->priority == 'Low')
						<span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
					@elseif(@$list->document_followup->priority == 'Moderate')
						<span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
					@endif							
					</span>
				</td>
				<td> 
				<?php 
				$file_type = explode('.', $list->filename);
				$doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'decode');
				
				?>{{ $file_type[1] }}</td>
				
				
				<td class="">
						
					<span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')"><a><i class="fa  {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"> </i></a></span>
					
					<span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i> </a></span>
					@if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
					<span class="document-delete ">
						<a class="js-common-delete-document js-prevent-redirect" data-text="Are you sure to delete the entry?"  data-doc-id="{{$doc_id}}"><i class="fa  {{Config::get('cssconfigs.common.delete')}}  js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete" ></i></a></center>                                                       
					</span>
					@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
<!-- Show Problem list start-->
<div id="show_document_assigned_list" class="modal fade in js_model_show_document_assigned_list"></div><!-- /.modal-dialog -->
<!-- Show Problem list end-->