@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-inbox font14"></i> Group <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Group</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('profile/bloggroup/'.$id)}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/bloggroup')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>



@stop
@section('practice')
<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-md-12" >

            <div class="box box-info no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <!-- form start -->



			{!! Form::model($group_list,['method' => 'PATCH','url'=>['/profile/bloggroup/'.$id], 'enctype'=>'multipart/form-data']) !!}
                

                <div class="box-body  form-horizontal">
				    <div class="form-group">
                        {!! Form::label('GroupName', 'Group Name', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 ">
                            {!! Form::text('group_name',$group_list->group_name,['class'=>'form-control']) !!}
							<?php $group_users = explode(',',$group_list->group_users);?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
				
                    <div class="form-group">
                        {!! Form::label('GroupMember', 'GroupMember', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10" >
                            {!! Form::select('group_users[]',$group_user, $group_users,['multiple'=>'multiple','class'=>'form-control select2']
                            ) !!}
                           
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 ">
                           {!! Form::radio('status',($group_list->status =="Active") ? true:null,'active') !!}  Active &nbsp;&nbsp;&nbsp;
							{!! Form::radio('status',($group_list->status =="Inactive") ? true:null,'inactive')!!} InActive
                            
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                </div><!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
					
					{!! Form::submit('submit', ['class' => 'btn btn-medcubics form-group',]) !!}
                     <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete this fee schedule?" href="{{ url('profile/blog/group/delete/'.$id) }}">Delete</a>                            
					<a href="{{url('profile/bloggroup/'.$id)}}"><button type="button" class="btn btn-medcubics">Cancel </button></a>    
                       
                    	
                    </div>
                </div><!-- /.box-footer -->

            </div><!-- /.box -->


        </div><!--/.col (left) -->
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->        

                           
 @stop 
 @include('practice/layouts/favourite_modal')