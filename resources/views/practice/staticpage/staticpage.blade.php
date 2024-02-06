@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Help </span></small>
        </h1>
        <ol class="breadcrumb">
          <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-xs-12"> 
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars font14"></i><h3 class="box-title">Help List</h3>
            <div class="box-tools pull-right margin-t-2">
                <a href="{{ url('staticpage/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New</a>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive"> 
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Module Name</th>
                            <th>Title</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staticpages as $staticpage)
    					<?php $staticpage->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($staticpage->id,'encode'); ?>
                        <tr data-url="{{ url('staticpage/'.$staticpage->id) }}" class="js-table-click clsCursor">
                            <td><?php if(strlen($staticpage->slug)== 3) echo (strtoupper($staticpage->slug)); else echo (ucfirst($staticpage->slug)); ?></td>
                            <td>{{ str_limit($staticpage->title, 25, '...') }}</td>
                            <td>{{ $staticpage->status }}</td>      
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->
@stop 