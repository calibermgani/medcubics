@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Messages </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="" data-url="" class="js_next_process hide"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-9 col-md-12 col-xs-12 col-xs-12 m-b-m-15 margin-t-m-13"><!-- Profile Header Starts -->
    @include('profile/layouts/tabs')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >

        <div class="box no-shadow">
            <div class="box-block-header with-border">
                <i class="fa {{Config::get('cssconfigs.common.message')}}"></i> <h3 class="box-title">Messages</h3>
                <div class="box-tools pull-right margin-t-2">

                </div>
            </div><!-- /.box-header -->
            <!-- form start -->

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <p class="font16 text-center med-green margin-b-10 margin-t-20" style="font-size: 26px;"> Messages</p>
            </div>
            <div class="box-body margin-t-10">
                
                <div class="col-lg-2 col-md-3 no-padding ">
                <p> <a href="javascript:void(0)" id="new_compose_mail_display" data-url="{{ url('profile/message/composemail')}}" class="text-center med-bg-green med-white line-height-26" style="display: block"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} med-white"></i> New</a></p>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border" style="height: 556px;">
					@include('profile/message/category')
					@include('profile/message/listing')  
					@include('profile/message/details')  
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>
<div class="col-lg-3 col-md-3 col-sm-6  col-xs-12 p-l-0">
    @include('profile/layouts/rightside-tabs')
</div>


<div id="add_new_label_modal" class="modal fade in"> <!-- Modal for creating New Label Starts -->
                <div class="col-lg-6 col-md-6 col-sm-6 modal-dialog" style="left:30%;margin: 30px auto;position: relative;"> <!-- Modal Dialog Starts -->
                    <div class="modal-content"> <!-- Modal Content Starts -->
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Add New Label</h4>
                        </div>
						
                        <div class="modal-body form-horizontal"> <!-- Modal Body Starts -->
						{!! Form::open(['name'=>'add_new_label','id'=>'add_new_label','files'=>true]) !!}
                            <div class="form-group margin-l-5">
                                <label class="col-lg-4 col-md-4 col-sm-4 margin-l-5 control-label-popup" for="title">Name</label>
                                <div class="col-lg-6 col-md-6 col-sm-6">
									{!! Form::text('label_name',null,['class'=>'form-control','id'=>'label_name']) !!}
                                    <span id='label_name_err' style='display:none;'><small class='help-block med-red' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'><span id="label_name_err_content"></span></small></span>
                                </div>                                
                            </div>
							
							<div class="form-group margin-l-5">
                                <label class="col-lg-4 col-md-4 col-sm-4 margin-l-5 control-label-popup" for="title">Categorize color</label>

								 <input class="form-control form-cursor" value="#00877F" type="hidden" id="label_color" name="label_color" style="padding: 0px;width: 20%;">
								 
								<div class="btn-group btn-group-sm">
								  <button id="demo2" class="btn btn-default" type="button"><span style="background-color:#000;" class="color-fill-icon dropdown-color-fill-icon"></span>&nbsp;<b class="caret"></b></button>
								</div>
								
                            </div>
							<div class="modal-footer">
								<button class="btn btn-medcubics-small add-new-label-submit" id="true" type="button">Submit</button>
								<button class="btn btn-medcubics-small" id="false" type="button" data-dismiss="modal">Cancel</button>
							</div>
						{!! Form::close() !!}	
                        </div> <!-- Modal Body Ends -->
                    </div> <!-- Modal Content Ends -->
                </div> <!-- Modal Dialog Ends -->
            </div> <!-- Modal for creating New Label Ends -->

@stop

