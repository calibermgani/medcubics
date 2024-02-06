@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php $faq->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($faq->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.faq')}} font14"></i> FAQ <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/faq')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>           
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/faq')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop



@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-m-18">


    @if($checkpermission->check_url_permission('admin/faq/{faq}/edit') == 1)
    <a href="{{ url('admin/faq/'.$faq->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
    @endif	
</div>
<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">                    
                <tbody>
                    <tr>
                        <td class="td-c-30">Question</td>
                        <td class="med-orange">{{ $faq->question }}</td>
                    </tr>
                    <tr>
                        <td>Answer</td>
                        <td>{{ @$faq->answer}}</td>
                    </tr>
                    <tr>
                        <td>Category</td>
                        <td><span class='font600'>{{ @ucfirst(strtolower($faq->category))}}</span></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><span class="patient-status-bg-form @if(@$faq->status == 'Active') label-success @else label-danger @endif">{{ @$faq->status}}</span></td>
                    </tr>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

@stop