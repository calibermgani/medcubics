<?php $i=1; ?>
@foreach($edi_reports as $key => $edi_report)
	<?php
		$edi_report_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($edi_report->id,'encode');
		if(@$edi_report->is_read=='No')
			$read_class='text-bold';
		else
			$read_class='';
	?>	
	<tr class="{{$read_class}}" >
		@if($i == 1)
		<input type="hidden" id="list_page" value="{{$list_page}}"/>
		@endif
		<td data-i="{{$i}}" style="width:2%; margin: -1px 0px 1px; padding: 0px; text-align: center; vertical-align: middle;">
			{!! Form::checkbox("edireport_ids[]",@$edi_report->id,null,["class" => "no-margin js-select-all-sub-checkbox js_sel_edireport_ids",'id'=>$key]) !!}<label for="{{$key}}" class="no-bottom">&nbsp;</label>
		 </td>
		<?php
			$temp = explode('.',$edi_report->file_name); 
		?>
		<td>{{substr($edi_report->file_name, 0, 100)}}</td>
		<td>{{App\Http\Helpers\Helpers::dateFormat($edi_report->file_created_date,'date')}}</td>
		<td>{{$edi_report->file_type}}</td>
		<td>{{$edi_report->file_size}}</td>
		<td></td>
		<td class="text-center">
			@if($temp[(count($temp) - 1)] != 'zip')
				<a class="unread_status" href="{{url('claims/edi_report/'.$edi_report_id.'/show')}}" target="_blank"><i class="fa fa-eye" data-placement="bottom" data-toggle="tooltip" data-original-title="View File" aria-hidden="true"></i></a>&nbsp;&nbsp;
			@endif
			<a class="unread_status" href="{{ url('claims/download/835/'.$edi_report_id) }}"><i class="fa fa-download" data-placement="bottom"  data-toggle="tooltip" data-original-title="Response Download"></i></a>&nbsp;&nbsp;
			
			<a class="js-edi_reportdelete-confirm hide" id="edidel_{{@$edi_report->id}}"><i class="fa {{Config::get('cssconfigs.common.delete')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i> </a>
			
			@if($edi_report->file_type == "Error")									
				<a class="unread_status" href="{{ url('claims/download/request/'.$edi_report_id) }}"><i class="fa fa-download" data-placement="bottom"  data-toggle="tooltip" data-original-title="Request Download"></i></a>&nbsp;&nbsp;
			@endif
		</td>
	</tr>
	<?php $i++; ?>
@endforeach      