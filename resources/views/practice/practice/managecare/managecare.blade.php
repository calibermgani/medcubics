@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}} font14"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Managed Care</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @include('layouts.practice_module_stream_export', ['url' => 'api/practicereports/'])
            <li><a href="{{ url('practice/'.$practice->id)}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
    @include ('practice/practice/practice-tabs')  
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		@if(Session::get('message')!== null) 
		<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
		@endif
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
		<div class="box box-info no-shadow ">
			<div class="box-header margin-b-10">
				<i class="fa fa-bars font14"></i><h3 class="box-title">Managed Care List</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_url_permission('managecare/create') == 1) 
					<a href="{{ url('managecare/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Managed Care</a>
					@endif
				</div>
			</div><!-- /.box-header -->
			<div class="box-body">
				<div class="table-responsive">
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Insurance</th>
							<th>Provider</th>
							<th>Credential</th>
							<th>Entity Type</th>
							<th>Effective Date</th>
							<th>Termination Date</th>
							<th>Fee Schedule</th>
						</tr>
					</thead>
					<tbody>
					<?php $count = 1;   ?> 
						@if(count($managecares) > 0)
						@foreach($managecares as $managecare)
						<?php $managecare_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($managecare->id,'encode'); ?>
						<?php //dd($managecare); ?>
						<tr data-url="{{ url('managecare/'.$managecare_id) }}" class="js-table-click form-cursor clsCursor">
							<td>{{ @$managecare->insurance->insurance_name }}</td>
							<td>
								<?php 
									@$provider = $managecare->provider; 
									$provider_name = @$managecare->provider->provider_name; 
								?>
								<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
									<a id="someelem{{hash('sha256',@$provider->id.$count)}}" class="someelem" data-id="{{hash('sha256',@$provider->id.$count)}}" href="javascript:void(0);"> {{ @$provider->provider_name }} {{ @$provider->degrees->degree_name }}</a>
									 <?php @$provider->id = @$provider->id.$count; ?>
									@include ('layouts/provider_hover')
								</div> 
							</td>
							<td>{{ $managecare->enrollment }}</td>
							<td>{{ $managecare->entitytype }}</td>
							<td>{{ ($managecare->effectivedate != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($managecare->effectivedate,'date'): '' }}</td>
							<td>{{ ($managecare->terminationdate != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($managecare->terminationdate,'date'): '' }}</td>
							<td>{{ $managecare->feeschedule }}</td>

						</tr>
						<?php $count++;   ?> 
						@endforeach
					   
					@endif
					</tbody>
				</table>
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
<div id="export_csv_div"></div>
<!--End-->
@stop
@push('view.scripts')
<script type="text/javascript">
    /* Export Excel for Charges list*/
    $('.js_search_export_csv').click(function(){
	var baseurl = '{{url('/')}}';
	var url = baseurl+"/reports/streamcsv/export/practiceManagedCare";
        form = $('form').serializeArray();
        var data_arr = [];
        $('select.auto-generate:visible').each(function(){
            data_arr.push({
                name : $(this).attr('name'), 
                value:  ($(this).select2('val'))
            });
        });
        data_arr.push({
            name : "controller_name", 
            value:  "PracticeManagecareController"
        });
        data_arr.push({
            name : "function_name", 
            value:  "practiceManagedCareExport"
        });
        data_arr.push({
            name : "report_name",
            value:  "Practice_Managed_Care"
        });
        form_data = "<form id='export_csv' method='POST' action='"+url+"'>";
        $.each(data_arr,function(index,value){
            if($.isArray(value.value)) {
                if(value.value.length > 0) {
                    var avoid ="[]"
                    form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
                }
            } else {
                if(value.value.length > 0) {
                    form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
                }
            }
        });
        form_data  += "<input type='hidden' name='export' value = 'yes'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
        form_data += "</form>";
        $("#export_csv_div").html(form_data);
        $("#export_csv").submit();
        $("#export_csv").empty();
    });
</script>
@endpush