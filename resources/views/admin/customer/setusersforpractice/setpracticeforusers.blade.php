@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Practices  <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> User <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>{{ $customerusers->firstname.' '.$customerusers->lastname }} </span></small>
        </h1> 
		@php  
		@$practice->id= App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$practice->id,'encode'); @endphp 
        <ol class="breadcrumb">
			<li><a href="{{ url('admin/customer/'.$customer_id.'/practice/'.$practice->id.'/users') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
			@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
			@endif
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
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa- "></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>                    
						<th>Practice Name</th> 
						<th>Created By</th> 
						<th>Updated By</th> 
						<th>Updated Date</th> 
						<th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-url="" class="clsCursor">
                        <td>{{ $practice->practice_name }}</td>
                        <td>{{ @$practice->user->name }}</td>
                        <td>{{ @$practice->update_user->name }}</td>
                        <td>{{ App\Http\Helpers\Helpers::dateFormat($practice->updated_at,'date') }}</td>
						<td>
						<a class="edit-practice-user-model" id="edit-practice-user-model" data-url="{{ url('admin/customer/'.$customer_id.'/customerusers/setpracticeforusers/'.$practice->id.'/user/'.$customer_user_id.'/edit')}}" ><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit User"></i></a>
						<a class="js-delete-confirm" data-text="Are you sure would you like to delete this practice?" href="{{ url('admin/'.$customer_id) .'/setpracticeforusers/'.$customer_user_id.'/delete/'.$practice->id}}"><i class="livicon tooltips" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Note' data-hovercolor="#009595"></i></a>
						</td>
                    </tr>
                    
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

 

<div id="add_edit_practiceuser" class="modal fade in">
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

@stop   