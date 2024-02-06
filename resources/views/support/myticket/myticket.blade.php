@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-clock-o med-green med-breadcrum" data-name="list"></i> Support <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Tickets <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> My Ticket </span></small>
        </h1>
        <ol class="breadcrumb">     
            <li><a href="{{url('searchticket')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
<!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'myticketreports/'])
            </li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
	@include ('support/tabs')
@stop 

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    @if(Session::get('message')!== null)
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
    <div class=" box box-info no-shadow collapsed-box">
    <div class="box-header with-border">
        <i class="fa fa-filter"></i> <h3 class="box-title">Filter</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div><!-- /.box-header -->

    <div class="box-body table-responsive">
        <div class="js_claim_search col-lg-12 col-md-12 col-sm-12 col-xs-12">  
            @include('support/myticket/search_filter_option')
            {!! Form::hidden('js_search_option_url',url('myticketreports/'),['id'=>'js_search_option_url']) !!}
        </div>             
    </div><!-- /.box-body -->
    </div>
</div><!-- /.box -->


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-info no-shadow">
            <div class="box-header margin-b-10">
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Ticket List</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div id="js_table_search_listing">	<!-- Box Body Starts -->
                    @include('support/myticket/myticket_list')
                </div>	
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div><!-- Inner Content for full width Ends -->
@stop