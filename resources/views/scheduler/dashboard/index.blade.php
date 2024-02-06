@extends('admin')
@section('toolbar')
<div class="row toolbar-header" >

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="dashboard"></i>Dashboard </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" onclick="history.go(-1);
                    return false;"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href=""><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop


@section('practice')

<div class="col-md-12" style="margin-top: -10px;">
	<img src="img/dashboard.jpg" class="img-responsive">  
</div>          

@stop