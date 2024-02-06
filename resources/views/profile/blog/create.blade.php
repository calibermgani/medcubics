@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Blogs <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Manage Blogs <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span> Create </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{url('/profile/blog')}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/blog')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice')
	<!--1st Data-->
	{!! Form::open(['url'=>'/profile/blog','id'=>'js-bootstrap-validator','class'=>'blog_create_form medcubicsform','enctype'=>'multipart/form-data','name'=>'medcubicsform']) !!}   
			@include ('profile/blog/form',['submitBtn'=>'Save' ,'heading'=>'Add'])
	{!! Form::close() !!}
                            
@stop            