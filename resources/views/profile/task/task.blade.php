@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.patient.file_text')}}"></i> Tasks  </small>
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
                <i class="fa {{Config::get('cssconfigs.patient.file_text')}}"></i> <h3 class="box-title">Tasks</h3>
                <div class="box-tools pull-right margin-t-2">
                    
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <p class="font16 text-center med-green margin-b-10 margin-t-20" style="font-size: 26px;"> Tasks</p>
            </div>
            <div class="box-body margin-t-10">
                <div class="col-lg-2 col-md-3 no-padding ">
                <p> <a href="" class="text-center med-bg-green med-white line-height-26" style="display: block"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} med-white"></i> New Task</a></p>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding min-height-profile-task-full yes-border">
                    <div class="col-lg-3 col-md-3 min-height-profile-task no-padding " style="border-right:1px solid #ccc;">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white task-table no-padding"> 
                            <table class="table-responsive table no-bottom">
                                <thead>                                
                                <th>Folders</th>
                             
                                </thead>
                            </table>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 task-ul" style="padding-right: 5px;">                                                                                
                            <ul style="">
                                <a href=""><li><i class="fa fa-envelope"></i> Inbox <small class="label pull-right bg-yellow" style="font-weight:400;margin-top:11px; margin-right:10px">
                                            12</small></li></a>
                                <a href=""><li><i class="fa fa-hourglass-1"></i> Today</li></a>
                                <a href=""><li><i class="fa fa-flag"></i>Due Soon</li></a>
                            </ul>


                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 task-ul1 margin-t-20" style="padding-right: 5px;"> 
                            <ul style="">                          
                                <a href=""><li style="background:#f0f0f0;padding-left:10px;"> Labels</li></a>
                                <a href=""><li><i class="fa fa-square margin-l-10 med-green-o margin-r-5"></i> Personal</li></a>
                                <a href=""><li><i class="fa fa-square margin-l-10 med-red margin-r-5"></i> Important</li></a>
                                <a href=""><li>&emsp;</li></a>
                                <a href=""><li>&emsp;</li></a>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 no-padding">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white task-table no-padding">                        
                            <table class="table-responsive table">
                                <thead>                                
                                <th>Title</th>
                                <th class="td-c-3">P</th>
                                <th class="td-c-3"></th>
                                </thead>
                                <tbody>
                                    <tr>                                        
                                        <td>Team meeting by 11.00AM</td>
                                        <td><i class="fa fa-square med-red"></i></td>
                                        <td><a href=""><i class="fa fa-trash"></i></a></td>
                                    </tr>

                                    <tr>                                        
                                        <td>Send task list to PM</td>
                                        <td><i class="fa fa-square med-blue"></i></td>
                                        <td><a href=""><i class="fa fa-trash"></i></a></td>
                                    </tr>

                                    <tr>                                        
                                        <td>New module needs to be completed</td>
                                        <td><i class="fa fa-square med-red"></i></td>
                                        <td><a href=""><i class="fa fa-trash"></i></a></td>
                                    </tr>

                                    <tr>                                        
                                        <td>Team meeting by 11.00AM</td>
                                        <td><i class="fa fa-square med-green-o"></i></td>
                                        <td><a href=""><i class="fa fa-trash"></i></a></td>
                                    </tr>

                                    <tr>                                        
                                        <td>Send task list to PM</td>
                                        <td><i class="fa fa-square med-green-o"></i></td>
                                        <td><a href=""><i class="fa fa-trash"></i></a></td>
                                    </tr>

                                    <tr>                                        
                                        <td>New module needs to be completed</td>
                                       <td><i class="fa fa-square med-blue"></i></td>
                                        <td><a href=""><i class="fa fa-trash"></i></a></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>



            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!--/.col (left) -->

</div>

<div class="col-lg-3 col-md-3 col-sm-6  col-xs-12 p-l-0">
    @include('profile/layouts/rightside-tabs')
</div>


<div id="new-task" class="modal fade">
    <div class="modal-sm-center">
        <div class="modal-content">
<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">New Task</h4>
			</div>
            <div class="modal-body">
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal">
                    <form class="">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <textarea class="form-control" placeholder="Enter your title"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('Priority', 'Priority', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                {!! Form::select('stmt_add', [''=>'-- Select --','High' => 'High','Medium' => 'Medium','Low' => 'Low'],null,['class'=>'select2 form-control']) !!}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('Repeat', 'Repeat', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                               {!! Form::radio('req_super', 'Yes',null,['class'=>'flat-red']) !!} Daily &emsp; {!! Form::radio('req_super', 'No',true,['class'=>'flat-red']) !!} Weekly
                            </div>
                        </div>
                        
                        <div class="form-group">
							{!! Form::label('From', 'From', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
								<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i> {!! Form::text('effectivedate',null,['class'=>'form-control dm-date form-cursor','id'=>'effectivedate','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}					
							</div>
						</div>
                        
                        <div class="form-group">
                            {!! Form::label('Alert on time', 'Alert on Time', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                {!! Form::select('stmt_add', [''=>'-- Select --'],null,['class'=>'select2 form-control']) !!}
                            </div>
                        </div>                        
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <button class="btn btn-medcubics-small">Save</button>
                    <a href="#" class="btn btn-medcubics-small" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->  
@stop
<!--End-->
@push('view.scripts')

<script>
    $(function () {
        $(".postIt").draggable({
            handle: '.sticky-notes',
        });
    });
</script>
@endpush