@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.maintenance.sql')}} font14"></i> Maintenance SQL</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('api/admin/adminuserexport/{export}') == 1)
            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/admin/adminuserexport/'])
            </li>
            @endif
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/admin_user')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Maintenance SQL List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_adminurl_permission('admin/adminuser/create') == 1)
                <a href="" class="font600 font14" data-placement="bottom" data-toggle="modal" data-original-title="New" data-target="#maintenance"><i class="fa fa-plus-circle"></i> Add SQL</a>
                @endif
            </div>
        </div>
        <!-- /.box-header --> 
        <!-- /.box-body -->
        <div class="box-body table-responsive">
            <table id="tblMaintanance" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Developer</th>
                        <th>SQL Query</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Applied Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenance as $r)
                    <?php
                        $mid = $r->id; 
						$m_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($mid, 'encode'); 
					?>
                    <tr>
                        <td>{{ @$r->id }}</td>
                        <td>{{ @$r->developer_name }}</td>
                        <td>{{ str_limit(@$r->query, $limit = 150, $end = '...')}}</td>
                        <td>@if($r->status=="Incomplete")
                            <span class="med-orange">{{ @$r->status }}</span>
                            @elseif($r->status=="Error")
                            <span class="med-red">{{ @$r->status }}</span>
                            @else
                            <span class="med-green">{{ @$r->status }}</span> @endif
                        </td>
                        <td>{{ date('m/d/Y H:i:s', strtotime( @$r->created_at )) }}</td>
                        <td>@if(!empty($r->applied_date)) {{ date('m/d/Y H:i:s', strtotime( @$r->applied_date )) }} @else - @endif</td>
                        <td>
                            @if($r->status=="Incomplete" || $r->status=="Error") <a href="javascript:void(0);" data-id='{{ @$r->id }}' class="apply">Apply</a> @else Applied @endif
                        </td>
                    </tr>
                    @empty
						No records found.
					@endforelse
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->

</div>
<input type="hidden" name="token" value="{{ csrf_token() }}"/>

<div id="maintenance" class="modal fade" aria-hidden="false">
    <div class="modal-md-700">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Maintenance SQL</h4>
            </div>
            <div class="modal-body no-padding">
            	<div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal no-padding">                        
                        <?php echo Form::open(['url'=>'#', 'id' => 'maintenance_form', 'class' => 'medcubicsform']); ?>

                        <div class="col-md-12 no-padding"><!-- Inner Content for full width Starts -->
                            <div class="box-body-block"><!--Background color for Inner Content Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- General Details Full width Starts -->                           
                                    <div class="box no-border  no-shadow" ><!-- Box Starts -->
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  1st Content Starts -->

                                            <div class="box-body form-horizontal no-padding"><!-- Box Body Starts --> 
                                                <div class="form-group-billing">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                                                    <?php echo Form::label('sql_query', 'SQL Query', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']); ?>
                                                        <?php echo Form::textarea('query',null,['maxlength'=>'5000','class'=>'form-control','spellcheck'=>'false','rows'=>'10', 'style'=>'min-height:350px; font-family: monospace; font-size: 12px; font-variant: stacked-fractions;']); ?> 
                                                    </div>                                   
                                                </div>
                                                <div class="form-group-billing">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <?php echo Form::label('Developer Name', 'Developer Name', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']); ?>
                                                        <?php echo Form::text('developer_name',null,['maxlength'=>'20','class'=>'form-control']); ?> 
                                                    </div>                                   

                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <?php echo Form::label('developed_date', 'Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']); ?>
                                                        <?php echo Form::text('developed_date',null,['maxlength'=>'20','class'=>'form-control', 'autocomplete' => "off"]); ?> 
                                                    </div>                                   
                                                </div>
                                            </div><!-- /.box-body Ends-->

                                        </div><!--  1st Content Ends -->                            
                                    </div><!--  Box Ends -->

                                </div><!-- General Details Full width Ends -->
                            </div><!-- Inner Content for full width Ends -->

                        </div><!--Background color for Inner Content Ends --> 
                        <?php echo Form::submit('Create', ['class' => 'pull-right js-create-batch margin-b-5 margin-t-4 margin-r-20 btn btn-medcubics-small']); ?>

                        <?php echo Form::close(); ?>                                           
                    </div>                     

                </div><!-- /.box-body -->   
			</div>
        </div><!-- /.modal-content -->
    </div>
</div>
<style type="text/css">
    .modal-content{
        overflow: auto;
    }
    td:nth(2) {
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@stop
@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function() {
    	$("#maintenance_form").bootstrapValidator({
            message: 'This value is not valid',
            excluded: ':disabled',
            feedbackIcons: {
                valid: '',
                invalid: '',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                query: {
                    validators: {
                        notEmpty: {
                            message: "Enter SQL Query"
                        }
                    }
                },
                developer_name: {
                    validators: {
                        notEmpty: {
                            message: "Enter Developer name"
                        }
                    }
                },
                developed_date: {
                    validators: {
                        notEmpty: {
                            message: "Enter Developer Date"
                        },
	                    date: {
	                        format: 'MM/DD/YYYY'
	                    }
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();

            // Get the form instance
            var $form = $(e.target);

            // Use Ajax to submit form data
            $.ajax({
				url:"{{url('/admin/sql/create')}}",
				method:"post",
				data:$form.serialize(),
				success:function(result){
					if(result.success==0){
						$('#maintenance').modal('hide');
						js_alert_popup(result.message);
					} else {
						$form.bootstrapValidator('disableSubmitButtons', false)  // Enable the submit buttons
						.bootstrapValidator('resetForm', true);             // Reset the form
						$('#maintenance').modal('hide');
						js_alert_popup(result.message);
						location.reload();
					}
				}
			});
        });
	});

	$("#developed_date").datepicker();

    $(".apply").on("click",function(){
        var id = $(this).data('id');
         $.ajax({
            url:"{{url('/admin/sql/execute')}}",
            method:"post",
            data:{id:id,"_token": "{{ csrf_token() }}"},
            success:function(result){
                if(result.success==0){
                    js_alert_popup(result.message);
                } else if(result.success==2) {
                    var fail = "<p>Failure :</p><ol>"; success = "<p>Success :</p><ol>";
                    $.each(JSON.parse(result.message),function(index,value){
						if(value.Success_Practice)
							success += "<li>"+value.Success_Practice+"</li>";
						if(value.Failure_Practice)
							fail += "<li>"+value.Failure_Practice+"</li>";
                    });
                    js_alert_popup("<div style='text-align:left;'>"+success+"</ol>"+fail+"</ol></div>");
                } else {
                    js_alert_popup(result.message);
                    location.reload();
                }
            }
        });
     });
</script>
<?php App\Http\Helpers\CssMinify::minifyJs('datatables_js'); ?>
{!! HTML::script('js/'.md5("datatables_js").'.js') !!}      
{!! HTML::script('js/datatables/datatable_search_highlight.js') !!}
<script type="text/javascript">
	$("#tblMaintanance").DataTable({
		"paging": true,
		"lengthChange": true,
		"searching": true,	
		"ordering": true,
		"info": true,
		"order": [],
		"autoWidth": false,
		"pageLength": 10,
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
	});
</script>
@endpush