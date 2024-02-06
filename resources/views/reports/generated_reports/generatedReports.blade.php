<table class="table table-striped">
    <thead>
        <tr>    
            <th>Report Name</th>
            <th>User</th>
            <th>Generated Date</th>
            <th>Type</th>
            <th>Parameters</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $list)
        <tr>
            <?php
                $report_name = str_replace('- ','',$list['report_name']);
                $report_name = explode(' ',$report_name);
                $report_name = implode('_',$report_name).'_'.App\Http\Helpers\Helpers::timezone($list['created_at'],'mdy').'_'.$list['report_count'];
				$repId = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$list['id']);
				$fileLink = App\Http\Helpers\Helpers::getResourceDownloadLink('reports', $repId, @$list['report_file_name']);
				$url = parse_url($list['report_url']);
				$type = explode('/',$url['path']); 
            ?>
            <td>{{ $report_name }}</td>
            <td class="text-center">{{ $list['created_user']['short_name'] }}</td>
            <td class="text-center">{{ App\Http\Helpers\Helpers::timezone($list['created_at'],'m/d/y') }}</td>
            <td class="text-center">				
				@if($list['report_type']== 'xlsx')
					<i class="fa fa-file-excel-o"></i>
				@else
					<i class="fa fa-file-pdf-o"></i>
				@endif
            </td>
            <td class="text-center"><i class="fa fa-bookmark-o js-parameter cur-pointer" data-export-id="{{ $list['id'] }}" ></i></td>
            <td>
				@if($list['status'] == 'Inprocess')
					<i class="fa fa-spinner fa-spin line-height-30"></i>
				@elseif($list['status'] == 'Pending' || $list['status'] == 'Completed') 
					<a href="{{ $fileLink }}" target="_blank"><i class="fa fa-download med-green-o line-height-30"></i></a> &nbsp;
					<?php /*					
					<a href="{{ url('/exportDownload/') }}{{ "/".$list['id'] }}"><i class="fa fa-download med-green-o line-height-30"></i></a>
					*/ ?>
				@endif
				
				<a href="javascript:void(0)"><i class="fa fa-trash med-green-o line-height-30 js-generate-delete" data-generate-id="{{ $list['id'] }}"></i></a>
			</td>
        </tr>
    @endforeach
    </tbody>
</table>