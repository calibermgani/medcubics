@extends('admin')


@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Blogs <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Manage Group</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('profile/blog') }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		@if($checkpermission->check_url_permission('profile/bloggroup/create') == 1)	
        <li><a href="{{ url('profile/bloggroup/create') }}"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></a></li>            
		@endif
        <li><a href="#js-help-modal" data-url="{{url('help/bloggroup')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop



@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info no-shadow">
        <div class="box-header with-border">
           <i class="fa fa-bars"></i><h3 class="box-title">Group List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Group Name</th>
						<th>User Member</th>
						<th>Status</th>
                    </tr>
                </thead>
                <tbody>
				
                    @foreach ($group_list as $group_list)
						<tr class="js-table-click clsCursor" data-url="{{ url('profile/bloggroup/'.$group_list->id) }}">
							<td>{{ $group_list->group_name}}</td>
							<td>{{App\User::getusername($group_list->group_users)}}</td>
							<td>{{ $group_list->status }}</td>
						</tr>
					@endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->
<!--End-->
@stop   