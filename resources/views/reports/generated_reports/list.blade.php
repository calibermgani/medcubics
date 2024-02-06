@extends('admin')
@section('toolbar')
<?php $url = Request::url(); ?>
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports  <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Generated Reports</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
			<li><a href="#" class="js-refreshreport hide" title="Refresh Reports"><i class="fa fa-refresh"></i></a> </li>
        </ol>
    </section>
</div>
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
    <div class="col-lg-8 margin-t-m-10">
        <div class="box-body form-horizontal  bg-white">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 ">
                        <h4 class="med-green margin-b-1 med-orange">Generated Reports</h4>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-table-tr js_search_part table-responsive" style="padding-bottom: 2px; padding-top: 0px;" id="reports_list">
                        <table class="table table-striped generated_reports">
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
                                        $report_name = implode('_',$report_name).'_'.date('mdy',strtotime($list['created_at'])).'_'.$list['report_count'];
										$url = parse_url($list['report_url']);
										$type = explode('/',$url['path']); 
										$repId = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$list['id']);
										$fileLink = App\Http\Helpers\Helpers::getResourceDownloadLink('reports', $repId, @$list['report_file_name']);
                                    ?>
                                    <td>{{ $report_name }}</td>
                                    <td class="text-center">{{ $list['created_user']['short_name'] }}</td>
                                    <td class="text-center">{{ App\Http\Helpers\Helpers::dateFormat($list['created_at']) }}</td>
                                    <td class="text-center">
										@if($list['report_type']== 'xlsx')
											<i class="fa fa-file-excel-o"></i>
										@else
											<i class="fa fa-file-pdf-o"></i>
										@endif
									</td>
									<td class="text-center"><i class="fa fa-bookmark-o js-parameter cur-pointer " data-export-id="{{ $list['id'] }}" ></i></td>
									<td>
										@if($list['status'] == 'Inprocess')
											<i class="fa fa-spinner fa-spin line-height-30"></i>
										@elseif($list['status'] == 'Pending' || $list['status'] == 'Completed')
											<a href="{{ $fileLink }}" target="_blank"><i class="fa fa-download med-green-o line-height-30"></i></a> &nbsp;
											<?php /*
											<a {{($list['report_type'] == 'pdf')? 'target=_blank': ' '}}
                                     href="{{ url('/') }}{{ "/".$list['report_file_name'] }}"><i class="fa fa-download med-green-o line-height-30"></i></a>
											*/ ?>
										@endif 
										| 
										<a href="javascript:void(0)"><i class="fa fa-trash med-green-o line-height-30 js-generate-delete" data-generate-id="{{ $list['id'] }}"></i></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-md" id="paramenter_info"></div>
</div>

<script>
	setInterval(updateReports,30000);
	var ajaxUrl = "<?php echo url('reports/generated_reports_view'); ?>";
	function updateReports(){
		displayLoadingImage();
		$.ajax({
			type: "GET",
			url: ajaxUrl,
			success: function (result) {
				$("#reports_list").html(result);
				$("table.table-striped.generated_reports").DataTable({
					"paging": true,
                    "pageLength": 25,
					"lengthChange": false,
					"searching": true,
					"ordering": true,
					"info": true,
					"responsive": true,
					"order": [2, 'desc']
				});
				hideLoadingImage();
		   }
	   });
	}
</script>
@stop

@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function(){
		$('table.table-striped').DataTable({
			"paging": true,
            "pageLength": 25,
			"length": true,
			"lengthChange": false,
			"searching": true,
			"ordering": true,
			"info": true,
			"responsive": true,
			"order": [2, 'desc']
		});
	});
</script>
@endpush