@extends('admin')
@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-exclamation  font14"></i> Error Log</small>
        </h1>
        <ol class="breadcrumb">


            <li class="hide"><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('api/admin/faq/{export}') == 1)
            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/admin/faqreports/'])
            </li>
            @endif
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/faq')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
            <div class="box-header margin-b-10 hide">
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3>
                <div class="box-tools pull-right margin-t-2">
                    @if($checkpermission->check_adminurl_permission('admin/faq/create') == 1)
                    <a href="{{ url('admin/faq/create') }}" class="font600 font14"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i> Add</a>
                    @endif
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table id="example3" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>File Created</th>
                                <th>Last Update</th>
                                <th>File Size (KB)</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($log_data as $list)
                            <tr >
                                <td>{!! $list->file_name !!}</td>
                                <td>{!! $list->file_created_time !!}</td>
                                <td>{!! $list->file_last_update !!}</td>
                                <td>{!! $list->file_size !!}</td>
                                <td>{!! $list->file_date !!}</td>
                                <td>
								<a href="{{ url('admin/viewlog/')}}/{!! $list->file_name !!}" target="_blank"><i class="fa fa-eye" data-placement="bottom" data-toggle="tooltip" data-original-title="View File" aria-hidden="true"></i></a>&nbsp;&nbsp;
								
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
<script type="text/javascript">
$("#example3").DataTable({
    "aaSorting": [[1, 'desc']]                    
});
</script>
@endpush