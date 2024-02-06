@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Practices Permissions <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> User <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>{{ @$customerusers->firstname.' '.@$customerusers->lastname }} </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/customer/'.$customer_id.'/customerusers') }}"><i class="fa fa-reply" data-placement= "bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a class="add-practice-user-model" data-url="{{ url('admin/customer/'.$customer_id.'/customerusers/'.$customer_user_id.'/setpracticeforusers/create')}}"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="Set Practice"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li class="hide"><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/customerusers')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
    
@section('practice')
<div class="col-xs-12">
    <div class="box box-info no-shadow">
        <div class="box-header with-border">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">User Practice List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>                        
                        <th>Short Name</th>
                        <th>User Name</th>
                        <th>User Type</th>
                        <th>Practice</th>
                        <th>User Access</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($practices as $practice)
                    @php  $practice->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice->id,'encode'); @endphp  
                    <?php $customeruser_name = ($practice->user->firstname!='' && $practice->user->lastname!='')?App\Http\Helpers\Helpers::getNameformat("@$practice->user->lastname","@$practice->user->firstname",""):''; ?> 
                    <tr data-url="" class="clsCursor">
                        <td>{{ $practice->user->short_name }}</td>
                        <td>{{ $customeruser_name }}</td>
                        <td>{{ @$practice->user->useraccess }}</td>
                        <td>{{ @$practice->practice_name }}</td>
                        <td>{{ @$practice->user->practice_user_type }}</td>
                        <td>{{ @$practice->user->email }}</td>
                        <td>{{ @$practice->user->status }}</td>
                        <td>
                        <a class="edit-practice-user-model" data-url="{{ url('admin/customer/'.$customer_id.'/customerusers/'.$customer_user_id.'/setpracticeforusers/'.$practice->id.'/edit')}}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit User"></i></a>
                        <a class="js-delete-confirm" data-text="Are you sure would you like to delete this practice?" href="{{ url('admin/setpracticeforusers/'.$customer_user_id.'/delete/'.$practice->id.'/'.$customer_id) }}"><i class="livicon tooltips" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Note' data-hovercolor="#009595"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<div id="add_setpractice_user" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Set Practice User</h4>
            </div>
            <div class="modal-body">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
<div id="add_edit_practiceuser" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Practice User</h4>
            </div>
            <div class="modal-body">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
@stop   