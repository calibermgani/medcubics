@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-clock-o med-green med-breadcrum" data-name="list"></i> Support <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Tickets <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Search Ticket </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('searchticket')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
        </ol>
    </section>
</div>
@stop

@section('practice-info')
    @include ('support/tabs')
@stop 

@section('practice')
    <!--1st Data-->
    @include ('support/ticketstatus/form',['submitBtn'=>'Find'])
@stop