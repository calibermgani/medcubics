@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> {{ucfirst($selected_tab)}}</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/providerreports/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    @if(Session::get('message')!== null) 
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow">
        <div class="box-header">
            <i class="fa fa-bars"></i><h3 class="box-title">Provider List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_url_permission('provider/create') == 1)
                <a href="{{ url('provider/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Provider</a>
                @endif
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive" id="js_table_search_listing"> 				
				@include('practice/provider/provider-list')				
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->
@stop   