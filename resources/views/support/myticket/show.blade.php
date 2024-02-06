@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-clock-o med-green med-breadcrum" data-name="list"></i> Support <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Tickets <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> My Ticket <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>Ticket Details</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('myticket')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
        </ol>
    </section>
</div>
@stop

@section('practice-info')
	@include ('support/tabs')
@stop 

@section('practice')
@foreach($get_ticket_detail as $detail)
<?php 
	$last_con 	= $detail->description; 
	$updated_by = $detail->posted_by; 
?>
@endforeach
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!--  Left side Content Starts -->
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 p-l-0"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Ticket Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body"><!-- Box Body Starts -->
            <table class="table table-striped">
                <tr>
                    <td class="med-green font600">Ticket ID</td>
                    <td>{{ @$get_ticket->ticket_id }}</td>
                </tr>
                <tr>
                    <td class="med-green font600">Title</td>
                    <td>{{ @$get_ticket->title }}</td>
                </tr>
                <tr>
                    <td class="med-green font600">Updated By</td>
                    <td>{{  @$updated_by }}</td>
                </tr>
            </table>	
        </div><!-- Box Body Ends --> 
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 p-r-0"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table table-striped table-striped">
                <tr>
                    <td class="med-green font600">Name</td>
                    <td>{{ @$get_ticket->name }}</td>
                </tr>
                <tr>
                    <td class="med-green font600">Email</td>
                    <td>{{ @$get_ticket->email_id }}</td>
                </tr>
                <tr>
                    <td class="med-green font600">Status</td>
                    <td><span class="js_close patient-status-bg-form  @if(@$get_ticket->status == 'Open')label-danger @else label-success @endif">    {{  @$get_ticket->status }}</span></td>
                </tr>
            </table>	
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="comments"></i> <h3 class="box-title">Conversation Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <div class="ticket_convarsation">
                <?php	$page = 'view_ticket';  ?>
                @include ('support/ticketstatus/ticketconversation') 
            </div>	
			
            <div class="js_reply_loading_msg hide text-center med-green">	
				<i class="fa fa-spinner fa-spin font20"></i> Processing..
			</div>
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide js_display_reply_form">
				<i class="fa fa-spinner fa-spin font20"></i> Processing..
			</div>
			
            <input type="hidden" name="ticket_id" value="{{ $get_ticket->ticket_id }}">
            <input type="hidden" name="page" value="view_ticket">
        </div><!-- /.box-body -->
		
		<div class="js_reply_success_msg hide alert alert-success">
			{{ trans("support/ticket.validation.reply_success") }}
		</div>
		
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

<div class="hide js_get_reply_form" >	
    <div class="col-lg-6 col-md-3 col-sm-3 col-xs-12 form-horizontal" style="margin: 25px;">	
        {!! Form::open(['url'=>['ticketstatus'],'name'=>'medcubicsform','class'=>'js_reply_validator','id'=>'js_reply_validator']) !!}
        <div class="form-group">					
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
                {!! Form::textarea('description',null,['class'=>'form-control','placeholder'=>'Description']) !!}
            </div>					
        </div>
        <div class="form-group ">
		<div class="col-lg-6 col-md-4 col-sm-6 col-xs-10">
            <div class="col-lg-1 col-md-4 col-sm-6 col-xs-10 js_checkbox">
                {!! Form::checkbox('closeticket'); !!}
            </div>					
			<div class="col-lg-6 col-md-4 col-sm-6 col-xs-10 med-green font600">
				Close Ticket
			</div>
		</div>	
        </div>
        <div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
				<span class="fileContainer" style="padding:1px 10px;"> 
				{!! Form::file('attachmentfile',['class'=>'form-control form-cursor','id'=>'attachment']) !!}Attachment  </span>
				&emsp;<span class="js-display-error"></span>
			</div>
		</div>
			
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 text-center">
            {!! Form::button('Submit', ['name'=>'sample','class'=>'btn btn-medcubics-small js_save_reply_from']) !!}
            <a href="javascript:void(0)" class="btn btn-medcubics-small js_reply_cancel">Cancel</a>
        </div>
        {!! Form::close() !!}	
    </div>
</div>
@stop