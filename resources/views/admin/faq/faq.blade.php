@extends('admin')


@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.faq')}}  font14"></i> FAQ</small>
        </h1>
        <ol class="breadcrumb">


            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('api/admin/faq/{export}') == 1)
            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/admin/faqreports/'])
            </li>
            @endif
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/faq')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop


@section('practice')
<div class="col-lg-12">
    @if(Session::get('message')!== null)
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    
        <div class="box box-info no-shadow">
            <div class="box-header margin-b-10">
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3>
                <div class="box-tools pull-right margin-t-2">
                    @if($checkpermission->check_adminurl_permission('admin/faq/create') == 1)
                    <a href="{{ url('admin/faq/create') }}" class="font600 font14"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i> Add</a>
                    @endif
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faq as $faq)
                            <?php $faq->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($faq->id,'encode'); ?>
                            <tr data-url="{{ url('admin/faq/'.$faq->id) }}" class="js-table-click clsCursor">
                                <td>{{ str_limit($faq->question, 50, '..')}}</td>
                                <td>{{ str_limit($faq->answer, 50, '..')}}</td>
                                <td>{{ str_limit($faq->category)}}</td>
                                <td>{{ str_limit($faq->status)}}</td>
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