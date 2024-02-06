@extends('admin')

@section('toolbar')

<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-book font14"></i> Group
			<i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Create</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="../group/index" }} ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
           
           <li><a href="#js-help-modal" data-url="{{url('help/bloggroup')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
		    <div class="box box-info no-shadow">
                <div class="box-header with-border">
                   <i class="fa fa-info-circle"></i> <h3 class="box-title"> General Information</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>{!! Form::open(['method' => 'POST','url' => 'profile/bloggroup','enctype'=>'multipart/form-data']) !!}
                </div><!-- /.box-header -->
                <div class="box-body table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12  space20"></div>
							{!! Form::label('group_name', 'group_name', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 ">
							{!! Form::text('group_name',null,['class'=>'form-control']) !!}
						</div>
						<div class="col-lg-4"></div>
					</div>
					<div class="form-group ">
						<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12  space20"></div>
							{!! Form::label('Group Member', 'GroupMember', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10"> 
							{!! Form::select('group_users[]',$group_user,null,['multiple'=>'multiple','class'=>'form-control select2']
						) !!}
						</div>
						<div class="col-lg-4"></div>
					</div>

					<div class="form-group form-horizontal">
						<div class="col-lg-2 col-md-3 col-sm-12 col-xs-12  space20"></div>
							{!! Form::label('Status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
							{!! Form::radio('status','active', true) !!} Active &nbsp;&nbsp;&nbsp;
							{!! Form::radio('status','inactive')!!} InActive
						</div>
						<div class="col-lg-4"></div>
					</div>
					<div class="form-group ">
						<div class="col-lg-2 col-md-3 col-sm-12 col-xs-12  space20"></div>
							
						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
							{!! Form::submit('Save', ['class' => 'btn  btn-medcubics form-group','id'=>'create_project']) !!}
							<a href="{{url('profile/bloggroup')}}"><button type="button" class="btn  btn-medcubics form-group">Cancel </button></a>
						</div>
						<div class="col-lg-4"></div>
					</div>
				</div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->
<!--End-->
		
 @stop 
 @include('practice/layouts/favourite_modal') 