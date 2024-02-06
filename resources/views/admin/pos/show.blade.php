@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php $pos->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($pos->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.pos')}}" data-name="location"></i> POS <i class="fa fa-angle-double-right med-breadcrum"></i><span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/placeofservice')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
           <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/pos')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-m-18">            		
	@if($checkpermission->check_adminurl_permission('admin/placeofservice/{placeofservice}/edit') == 1)
		<a href="{{ url('admin/placeofservice/'.$pos->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
	@endif
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
           <i class="livicon" data-name="code"></i> <h3 class="box-title">POS Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
				<tbody>
					<tr>
						<td>Code</td>
						<td><span @if($pos->code != "") class="bg-number" @endif>{{ $pos->code}}</td>
					</tr>
					  <tr>
						<td>Place of service</td>
						<td>{{ $pos->pos}}</td>
					</tr>
					<tr>
						<td>Created By</td>
						<td>{{ App\Http\Helpers\Helpers::shortname($pos->created_by) }}</td>
					</tr>
					<tr>
						<td>Created At</td>
						<td>@if((@$pos->created_at != '') &&($pos->created_at != '-0001-11-30'))<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($pos->created_at) }}</span> @endif</td>
					</tr>
					<tr>
						<td>Updated By</td>
						<td>{{ App\Http\Helpers\Helpers::shortname($pos->updated_by) }}</td>
					</tr>
					<tr>
						<td>Updated At</td>
						<td>@if(@$pos->updated_by != '' && @$pos->updated_by != 0)<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($pos->updated_at) }}</span> @endif</td>
					</tr>
				</tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
<!--End-->
@stop