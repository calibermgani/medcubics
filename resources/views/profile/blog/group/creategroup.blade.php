@extends('admin')

@section('toolbar')

<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Blogs <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Manage Group <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span> Create </span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{url('profile/bloggroup')}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
           
           <li><a href="#js-help-modal" data-url="{{url('help/bloggroup')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>

@stop

@section('practice')

<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >

            <div class="box box-info no-shadow">
                 <div class="box-block-header with-border">
                    <i class="fa fa-sticky-note font14"></i>  <h3 class="box-title">General Information</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <!-- form start -->
				{!! Form::open(['method' => 'POST','url' => 'profile/bloggroup','enctype'=>'multipart/form-data','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
                <div class="box-body  form-horizontal">
                    <div class="form-group">
                        {!! Form::label('Group Name', 'Group Name', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                        <div class="col-lg-4 col-md-7 col-sm-12">
                            {!! Form::text('group_name',null,['class'=>'form-control']) !!}
						</div>
                    </div>  
					
					<div class="form-group">
							{!! Form::label('Group Member', 'Group Member', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
							<div class="col-lg-4 col-md-7 col-sm-12">
							{!! Form::select('group_users[]',$group_user,null,['multiple'=>'multiple','class'=>'form-control select2']
						) !!}
							</div>
					</div>
					
					<div class="form-group">
							{!! Form::label('Status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
							<div class="col-lg-4 col-md-7 col-sm-12">
							{!! Form::radio('status','active', true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
							{!! Form::radio('status','inactive',null,['class'=>'','id'=>'c-inactive'])!!} {!! Form::label('c-inactive', 'InActive',['class'=>'med-darkgray font600 form-cursor']) !!}
							</div>
					</div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-lg-7 col-md-10 col-sm-12 col-xs-12">
                        {!! Form::submit('Save', ['class' => 'btn  btn-medcubics form-group','id'=>'create_project']) !!}
						<a href="javascript:void(0)" data-url="{{url('profile/bloggroup')}}"><button type="button" class="btn  btn-medcubics js_cancel_site form-group">Cancel </button></a>
					</div>
				</div><!-- /.box-footer -->
				
            </div><!-- /.box -->

        </div><!--/.col (left) -->
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->   
{!! Form::close() !!}

@push('view.scripts')
<script type="text/javascript">
$(document).ready(function() {

    $('#js-bootstrap-validator').bootstrapValidator({
        fields: {
            group_name:{
                validators:{
                    notEmpty:{
                        message: 'Group Name field is required and can\'t be empty'
                   	},
                }
            },
			'group_users[]':{
                validators:{
                    notEmpty:{
                        message: 'Group Member field is required and can\'t be empty'
                   	},
                }
            }
        }
    });
});
</script>
@endpush


 @stop 