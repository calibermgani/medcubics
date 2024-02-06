@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.userapisettings')}} font14"></i> User Activity</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/user_activity')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    <div class="col-xs-12">
        <div class="box box-info no-shadow">
            <div class="box-header with-border">
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">User Activity List</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="form-horizontal">
                    {!! Form::open(['onsubmit'=>"event.preventDefault();" ,'id'=>'js-getUser_activity_module', 'name'=>'get_user_form']) !!}
                    <div class="form-group">
                        {!! Form::label('Select User', 'Sort by User', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-3  col-sm-6 col-xs-10">
                            {!! Form::select('user', array('' => '-- Select --') + (array)@$user,@$user_id,['class'=>'form-control select2', 'id' => 'user' ] ) !!}
                        </div>
                        {!! Form::label('Select Practice', 'Practice', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-3  col-sm-6 col-xs-10">
                            {!! Form::select('practice', array('' => '-- Select --') + (array)@$practice,@$practice_id,['class'=>'form-control select2', 'id' => 'practice']
                            ) !!}
                        </div>
                                               
                    </div>
                    <div class="form-group">
                         {!! Form::label('module','Module' ,['class'=>'control-label col-lg-2 col-md-3 col-sm-4 col-xs-12']) !!}
                        <div class="col-lg-3 col-md-3  col-sm-6 col-xs-10">
							{!! Form::select('module', array('' => '-- Select --') + (array)@$module,@$practice_id,['class'=>'form-control select2', 'id' => 'module']
                            ) !!}
                        </div>
                        {!! Form::label('transaction_date','Active On' ,['class'=>'control-label col-lg-2 col-md-3 col-sm-4 col-xs-12']) !!}
                        <div class="col-lg-3 col-md-3  col-sm-6 col-xs-10">
							{!! Form::input('text','transaction_date',null,['class'=>'date auto-generate bg-white form-control form-select js-date-range','id'=>'transaction_date']) !!}
                        <!-- <i class="fa fa-calendar-o"></i> -->
                        </div>                     
                        <input id="getUser_activity_module" accesskey="s" data-id="js-getUser_activity_module" class="btn btn-medcubics-small" type="submit" value="Get">
                    </div>
					<?php /*
                    <div class="form-group">
                        {!! Form::label('Select Practice', 'Sort by Practice', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-3  col-sm-6 col-xs-10">
                            {!! Form::select('practice', array('' => '-- Select --') + (array)@$practice,@$practice_id,['class'=>'form-control select2']
                            ) !!}
                        </div>
                        {!! Form::submit('Get', ['name'=>'get','class'=>'btn btn-medcubics']) !!}                        
                    </div>
					*/ ?>
                    {!! Form::close() !!}
                </div>
                <div class="table-responsive" id="js_user_activity">
					<table id="useractivity_list" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>User Type</th>
								<th>From</th>
								<th>Module</th>
								<th>Activity</th>
								<th>Activity On</th>
							</tr>
						</thead>
					</table>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
    {!! HTML::style('css/search_fields.css') !!}
</div>
<style>
    .btn-medcubics { margin: 0px; }
</style>
@stop   

@push('view.scripts')
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script type="text/javascript">
    $(document).on('click','#getUser_activity_module',function (event) {
       //var form_id_val = $(this).attr('data-id');
       //var data = $('#'+form_id_val).serialize();
        var target = api_site_url + '/admin/useractivitylist';
        $.ajax({
            type: 'POST',
            url: target,
            data: { '_token': $('input[name="_token"]').val(), 'user': $('#user').val(), 'practice': $('#practice').val(), 'module': $('#module').val(), 'transaction_date': $('#transaction_date').val(), },
            success: function (res) {               
                $('#js_user_activity').html(res);
                $("#js_user_activity_tbl").DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "info": true
                });
            }
        });
    });
	
    $(document).ready(function () {
        // $('#user_filter').  change(function(){
        //     var user_id = $('#user_filter').data('id');
        //     var name = $(this).data('value');
        //     alert(user_id);
        // }); 
        $('#js-bootstrap-validator')
			.bootstrapValidator({
				message: 'This value is not valid',
				excluded: ':disabled',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				framework: 'bootstrap',
				// fields: {
				//     user: {
				//         message: '',
				//         validators: {
				//             notEmpty: {
				//                 message: 'Select the User!'
				//             },
				//         }
				//     }
				// },
				onSuccess: function(e, data) {
					//this section before submit					 
					 $("#js_user_activity").html('<div style="text-align:center;color:#00877f"><i class="fa fa-spinner fa-spin font20"></i> Processing</div>');
					 var user_id = $('select[name="user"] :selected').val();
					 var practice_id = $('select[name="practice"] :selected').val();
					 var date_range = $('#transaction_date').val();
					 var module = $('select[name="module"] :selected').val();
					$.ajax({
						type : 'POST',
						headers: {
							'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
						},
						data: {user_id, practice_id, date_range,module},
						url  : api_site_url+'/admin/useractivity',
						success :  function(result){
							$("#js_user_activity").html(result);
							$("#example1").dataTable();
							$('input[type="submit"]').prop('disabled', false);
						}
					});
					e.preventDefault();
				}
			});
    });

</script>
@endpush