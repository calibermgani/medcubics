@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.user')}} font14"></i> Users</small>
        </h1>
        <ol class="breadcrumb">			
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li><a href="javascript:void(0);l" data-url="{{url('help/user')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/user/user_tabs')
@stop

@section('practice')
	<div class="col-lg-12">
		@if(Session::get('message')!== null) 
		<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
		@endif
	</div> 
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
        <div class="box box-info no-shadow">
            <div class="box-header margin-b-10">
                <i class="fa fa-bars font14"></i><h3 class="box-title"> Users List</h3>
               
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>E-mail</th>
                            <th>Page permission</th>
                        </tr>
                    </thead>
                    <tbody>
						@if(!is_null($user_practices) && count($user_practices)>0)
						
                        @foreach($user_practices as $user)  
                        <?php @$user->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$user->id,'encode');
						$count = 0;
						?>
                       <tr @if($checkpermission->check_url_permission('users/{user}')) data-url="{{ url('users/'.@$user->id) }}" @endif class="js-table-click clsCursor">
                            <td>{{ App\Http\Helpers\Helpers::shortname($user->user->short_name) }}</td>
                            <td>{{ str_limit(@$user->user->email, 40, ' ..') }}</td>
                            <td>
                                @foreach(@$user->page_permission_ids as $permission_id)
									@if($count < 2) {{$permission_id}},@endif
									<?php $count++; ?>
								@endforeach
							</td>      
                        </tr>
                        @endforeach
						@endif
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>  
<!--End-->
@stop   