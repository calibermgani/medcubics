@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}} font14" aria-hidden="true"></i> User History</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('api/admin/history/{export}') == 1)
					<li class="dropdown messages-menu hide"><a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/history/'])
				</li>
				@endif
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/history')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')
	<div class="col-lg-12">
		@if(Session::get('message')!== null)
			<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
		@endif
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->

		<div class="box box-info no-shadow">
			<div class="box-header margin-b-10">
				<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">User History List</h3>
				<div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>

			</div><!-- /.box-header -->
			<div class="box-body">
				<div class="form-horizontal">
                    {!! Form::open(['onsubmit'=>"event.preventDefault();" ,'id'=>'js-getUser_activity_module', 'name'=>'get_user_form']) !!}

                    <div class="form-group">
                         {!! Form::label('Customer','Customer' ,['class'=>'control-label col-lg-2 col-md-3 col-sm-4 col-xs-12']) !!}
                        <div class="col-lg-3 col-md-3  col-sm-6 col-xs-10">
							{!! Form::select('customer', array('' => '-- Select --') + (array)@$customers,null,['class'=>'form-control select2', 'id' => 'customer']
                            ) !!}
                        </div>
                        <input id="getUser_activity_module" accesskey="s" data-id="js-getUser_activity_module" class="btn btn-medcubics-small" type="submit" value="Get">
                    </div>
                    {!! Form::close() !!}
                </div>

			<div class="ajax_table_list hide"></div>
				<div class="data_table_list" id="js_ajax_part">
					<table id="patients_column_list" class="table table-bordered table-striped mobile-width">
					<thead>
						<tr>
							<th>IP address</th>
							<th>Browser name</th>
							<th>Login time</th>
							<th>Logout time</th>
							<th>User</th>
							<th>User Type</th>
						</tr>
					</thead>
					<tbody>
						@foreach($history as $history)
							<?php
								$logout_time = trim($history->logout_time);
							?>
							<tr>
								<td>{{ $history->ip_address}}</td>
								<td>{{ $history->browser_name}}</td>
								<td>{{ App\Http\Helpers\Helpers::dateFormat(@$history->login_time,'time') }}</td>
								<td>
									@if(@$logout_time !='')
										{{ App\Models\Profile\UserLoginHistory::LogoutTime(@$history->logout_time) }}
									@else
										Current User
									@endif
								</td>
								<td>{{ @$history->user->short_name }}</td>
								<td>{{ isset($history->user->user_type)?$history->user->user_type:'-Nil-'}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			  </div>
			</div><!-- /.box-body -->
		</div><!-- /.box -->

	</div><!-- Inner Content for full width Ends -->
<!--End-->
@stop


@push('view.scripts')
{!! HTML::script('js/datatables_serverside.js') !!}
{!! HTML::script('js/daterangepicker_dev.js') !!}

<script type="text/javascript">

	var api_site_url = '{{url('/')}}';
    var total_rec = '{{ @$total_rec }}';
    var listing_page_ajax_url = api_site_url + '/admin/userhistorylist';;
    var dataArr = {};
    var wto = '';


	historySearch();


	function historySearch(allcolumns) {
        var dtable= $("#patients_column_list").DataTable({
            "createdRow": function (row, data, index) {
                if (data[3] != undefined)
                    data[3] = data[3].replace(/[\-,]/g, '');
            },
            "bDestroy": true,
            "paging": true,
			//"processing": true,
            "searching"   :   false,
            "info": true,
            "aoColumns": allcolumns,
            "columnDefs": [{orderable: false, targets: [11, 12]}],
            "autoWidth": false,
            "lengthChange": false,
            "searchHighlight": true,
            "serverSide": true,
            "order": [[0, "desc"], [1, "desc"]],
            "ajax": $.fn.dataTable.pipeline({
                url: listing_page_ajax_url,
                data:{'dataArr':dataArr},
				beforeSend: displayLoadingImage(),
                pages: 2, // number of pages to cache
                success: function () {

                }
            }),
            "columns": [
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(".ajax_table_list").html(aData + "</tr>");
				var elem = $(".ajax_table_list tr");
                var get_orig_html = elem.html();
                var get_attr = elem.attr("data-url");
                var get_class = elem.attr("class");
                $(nRow).addClass(get_class);
                $(nRow).attr('data-url', get_attr);
                $(nRow).closest('tr').html(get_orig_html);
                $(".ajax_table_list").html("");
            },
            "fnDrawCallback": function (settings) {
                //var length = settings._iDisplayStart;
                //var sorting_length = settings.aLastSort.length;
                hideLoadingImage(); // Hide loader once content get loaded.
            }
        });
    }

	/* Search function start */
    var column_length = $('#patients_column_list thead th').length;

    /* Dynamic append */
    function accessAll() {
        var selected_column = ['IP address', 'Browser name','Login time' ,'Logout time', 'User', 'User Type'];
        var allcolumns = [];
        for (var i = 0; i < column_length; i++) {
            allcolumns.push({"name": selected_column[i], "bSearchable": true});
        }
        historySearch(allcolumns); /* Trigger datatable */
    }

	$(document).on('click','#getUser_activity_module',function (event) {
        clearTimeout(wto);
		var data_arr = {};
		wto = setTimeout(function() {
			$('#customer').each(function(){
				 data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
			});
			// Getting all data in input fields
			dataArr = {data:data_arr};
			accessAll();
		}, 100);
    });
</script>
@endpush
