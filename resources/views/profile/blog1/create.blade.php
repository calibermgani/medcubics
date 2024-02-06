@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-user font14"></i> Blogs <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Blog</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('/profile/blog')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/code')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
	<!--1st Data-->    
	{!! Form::open(['url'=>'/profile/blog','id'=>'js-bootstrap-validator','class'=>'blog_create_form','enctype'=>'multipart/form-data']) !!}   
		@include ('profile/blog/form',['submitBtn'=>'Save' ,'heading'=>'Add'])
	{!! Form::close() !!}                            
 @stop            