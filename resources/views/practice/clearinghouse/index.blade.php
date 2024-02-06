@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{@$heading_icon}} font14"></i> {{ $heading }}</small>
        </h1>
        <ol class="breadcrumb">      
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @if($checkpermission->check_url_permission('api/edireports/{export}') == 1)
            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/edireports/'])
            </li>
            @endif
            @if($checkpermission->check_url_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/clearinghouse')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->

    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">EDI List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_url_permission('edi/create') == 1)
                    <a href="{{ url('edi/create') }}" class="font600 font14"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i> New</a>
                @endif
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
						<th>Is Enable 837?</th>
						<th>Created On</th>
						<th>Updated On</th>
						<th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clearing_house as $edi)
					<?php $edi->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($edi->id,'encode'); ?>
                    <tr data-url="{{ url('edi/'.$edi->id) }}" class="js-table-click clsCursor">
                        <td>{{ $edi->name}}</td>
                        <td>{{ $edi->status}}</td>
						<td>{{ $edi->enable_837 }}</td>
						<td>@if($edi->created_at != '' && $edi->created_at !="-0001-11-30 00:00:00" )
						{{ App\Http\Helpers\Helpers::dateFormat($edi->created_at,'date')}}
						@endif</td>
						<td>@if($edi->updated_at != '' && $edi->updated_at !="-0001-11-30 00:00:00" )
						{{ App\Http\Helpers\Helpers::dateFormat($edi->updated_at,'date')}}
						@endif</td>
                        <td>{{ App\Http\Helpers\Helpers::shortname($edi->created_by) }}</td>
					</tr>
                    @endforeach
                </tbody>
            </table>
          </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->

</div><!-- Inner Content for full width Ends -->
<!--End-->
@stop