@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php $user_practice[0]->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($user_practice[0]->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.user')}} font14"></i> Users <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Show </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('users')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/user')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 margin-b-10 margin-t-10">

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
				<i class="livicon" data-name="address-book"></i> <h3 class="box-title">User Information</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table class="table-responsive table-striped-view table">                    
					<tbody>
						<tr>
							<td>Name</td>
							<td>{{ $user_practice[0]->user->name }}</td>
						</tr>
						<tr>
							<td>Email</td>
							<td>{{ $user_practice[0]->user->email }}</td>
						</tr> 
						<tr>
							<td>Address Line 1</td>
							<td>{{ $user_practice[0]->user->addressline1 }}</td>
						</tr>
						<tr>
							<td>Address Line 2</td>
							<td>{{ $user_practice[0]->user->addressline2 }} </td>
						</tr>
						<tr>
							<td>City</td>
							<td>{{ $user_practice[0]->user->city }} @if($user_practice[0]->user->state != '') - <span class=" bg-state ">{{ $user_practice[0]->user->state }}</span>@endif</td>
						</tr>
						<tr>
							<td>Zip Code</td>
							<td>{{ $user_practice[0]->user->zipcode5 }} @if($user_practice[0]->user->zipcode4 != '') - {{ $user_practice[0]->user->zipcode4 }}@endif</td>
						</tr>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
				<i class="livicon" data-name="address-book"></i> <h3 class="box-title">User page permission</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table class="table-bordered table-separate table-striped-view table">                    
					<thead>
						<tr>
							<th>Menu name</th>
							<th>Pages</th>
							<th>Page permission</th>
						</tr>
					</thead>
					<tbody>
						 @foreach($user_practice[0]->page_permission_ids as $menus) 
						<?php 	$menu_types = '';
								$menu_types = explode(" >> ", $menus);
						?>
						<tr>
							<td>{{$menu_types[0]}}</td>
							<td>{{$menu_types[1]}}</td>
							<?php 
							
							$page = (explode("/", $menu_types[2]));
							$permission_page = (implode(" / ", $page));
							
							?>
							<td>{{$permission_page}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
</div>
@stop 		