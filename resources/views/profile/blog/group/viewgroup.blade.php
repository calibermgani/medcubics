@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Blogs <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Manage Group <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span> View </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('profile/bloggroup')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @if($checkpermission->check_url_permission('profile/bloggroup/{bloggroup}/edit') == 1)		
            <li><a href="{{ url('profile/bloggroup/'.$id.'/edit')}}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            @endif
            <li><a href="#js-help-modal" data-url="{{url('help/bloggroup')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice')
<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table-responsive table-striped-view table">                    
                        <tbody>
                            <tr>
								<td>Group Name</td>
								<td>{{$group_list->group_name }}</td>
							</tr>
							
							<tr>
								<td>Group User</td>
								<td>{{App\User::getusername($group_list->group_users)}} </td>
							</tr>

							<tr>
								<td>status</td>
								<td>{{$group_list->status }} </td>
							</tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
    </div>
</div>
@stop 