@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.user')}} font14"></i> Users <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>User Activity </span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/user_activity')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/user/user_tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
	<div class="box space20 box-info no-shadow">
		<div class="box-header">
			<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">User Activity List</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
                
		<div class="box-body mobile-scroll">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 hidden-print hidden-sm hidden-xs pat-filter">
                {!! Form::open(['class'=>'js_user_list']) !!} 
                             
					<div class="form-group">                                 					
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 pull-right user-activity-filter">
						 	{!! Form::select('user', array('' => '-- Select User --') + (array)$user,$user_id,['class'=>'form-control select2 js_user_activity_list','autocomplete'=>'off']) !!}
						</div>					                                
					</div>                                   
                             
				{!! Form::close() !!}
            </div>
                    
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id ="js_users_table">
				@include ('practice/useractivity/useractivitylisttable')
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</div>
@stop   

@push('view.scripts')
<script type="text/javascript">
	$(document).on('click', '.js-useractivity-click', function () {

        var getUrl = $(this).attr('data-url');
        var getActivity = $(this).attr('data-activity');
        var getAction = $(this).attr('data-action');
        var getModule = $(this).attr('data-module');
        alert(getModule);
        if (getActivity != '' && getModule != 'notes' && getModule != 'patients-notes' && getModule != 'document' && getModule != 'providerdocuments' && getModule != 'templatetypes') {
            if (getActivity == 'admin' || getActivity == 'practice') {
                window.open(getUrl, '_blank');
            }
            else {
                $.ajax({
                    url: api_site_url + '/admin/setuserpractice/' + getActivity,
                    type: 'get',
                    success: function (data, textStatus, jQxhr) {
                        window.open(getUrl, '_blank');
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }
        }
    });
	$(document).on('change','.js_user_activity_list', function(){
		$("#js_users_table").html('<div style="text-align:center;color:#00877f"><i class="fa fa-spinner fa-spin font20"></i> Processing</div>');
		var myform = $('.js_user_list').serialize();
		$.ajax({
			url		: api_site_url+'/practices/useractivitylist',
			data	: myform,
			type 	: 'POST',
			success: function(responce) {
				$("#js_users_table").html(responce);
				$("#example1").DataTable();
			}
		});		
	});
</script>
@endpush