@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="pen"></i> Data Integrity Test</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="{{url('help/apiconfig')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop

@section('practice')
  
 <div class="col-lg-12">
@if(Session::get('message')!== null) 
<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
@endif
</div>
    @include ('admin/claimsintegrity/tabs')
<!--End-->
<!-- active menu start -->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
    <div class="box no-border no-shadow no-bottom">                 
        <div class="box-body">
                <div class="table-responsive js_table_list">
                    @include('admin/claimsintegrity/integrity')                            
                </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
 
@stop