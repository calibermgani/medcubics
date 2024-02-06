@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Practice</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('admin/customer/'.$customer_id) }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
           
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

            @if($checkpermission->check_adminurl_permission('admin/customerpracticesmedcubics/{id}/{export}') == 1)
            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'admin/customerpracticesmedcubics/'.$customer_id.'/'])
            </li>
            @endif

            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/customerpractices')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
@include ('admin/customer/tabs')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow margin-t-20">
        <div class="box-header margin-b-10">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Practice List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerpractices/create') == 1)
                <a href="{{ url('admin/customer/'.$customer_id.'/customerpractices/create') }}" class="font600 font14"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i> New</a>
            @endif
            </div>
        </div><!-- /.box-header -->
        <div class="box-body margin-t-10">
          <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Practice Name</th>
                        <th class="td-c-30">Description</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Fax</th>
                        <th>Doing Business as</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($practices as $practice)
                   <?php $practice->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice->id,'encode'); ?> 
                    <tr data-url="{{ url('admin/customer/'.$customer_id.'/customerpractices/'.$practice->id) }}" class="js-table-click clsCursor">
                        <td>{{ $practice->practice_name }}</td>
                        <td>{{ $practice->practice_description}}</td>
                        <td>{{ $practice->email }}</td>
                        <td>{{ $practice->phone }}</td>
                        <td>{{ $practice->fax }}</td>
                        <td>{{ $practice->doing_business_s }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
    
@stop
