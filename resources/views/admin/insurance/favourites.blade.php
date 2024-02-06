@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="bank"></i>Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Favourite</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" onclick="history.go(-1);return false;"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>  
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="{{ url('medcubics-admin/api/insurancefavouritesreports/'.$user_id.'/export') }}"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop


@section('practice-info')
@include ('admin/insurance/tabs')
@stop


@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info  no-shadow">
        <div class="box-header with-border">
           <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Favourite List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                       <th>Insurance Name</th>
                        <th>Insurance Type</th>
                        <th>Insurance Class</th>
                        <th>Payer ID</th>
                        <th>Phone</th>
                        <th>Favourite</th>
                    </tr>
                </thead>
                <tbody>
                  
                    
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->
<!--End-->
@include('practice/layouts/favourite_modal')
@stop     