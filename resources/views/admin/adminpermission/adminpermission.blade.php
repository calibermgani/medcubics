@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="users"></i>Admin Permission</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/adminpermission')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
          <?php /*   <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'admin/api/adminpermission/'])
            </li> */ ?>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/adminpermission')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
    <div class="col-xs-12">
        <div class="box box-info no-shadow">
            <div class="box-header with-border">
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Admin Permission List</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adminpermission as $permission)                        
                            <tr data-url="javascript:void(0)" class="js-table-click clsCursor">
                                <td>{{ $permission->role_name }}</td>                        
                                <td><a href="{{ url('admin/adminpermission/'.$permission->id) }}">Set Permission</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
@stop   