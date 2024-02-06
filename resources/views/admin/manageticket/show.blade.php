@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.ticket')}} font14"></i> Manage Ticket <i class="fa fa-angle-double-right med-breadcrum"></i><span>Ticket Details</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)"  data-url="{{ url('admin/manageticket')}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/manageticket')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('admin/manageticket/tabs')
@stop 

@section('practice')
<?php $get_ticket_data_id = \App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($get_ticket->id,'encode'); ?>
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
				<table class="table  table-billing-edit">
					<tr>
						<td class="med-green font600">Ticket ID</td>
						<td>{{ @$get_ticket->ticket_id }}</td>
					</tr>
					<tr>
						<td class="med-green font600">Title</td>
						<td>{{ @$get_ticket->title }}</td>
					</tr>
					 <tr>
						<td class="med-green font600">Last Assigned To</td>
						<td>{{ (@$get_ticket->get_assignee->name=='')?'-':@$get_ticket->get_assignee->name }}</td>
					</tr>
					 <tr>
						<td class="med-green font600">Last Assigned By</td>
						<td>{{ (@$get_ticket->get_assignedby->name=='')?'-':@$get_ticket->get_assignedby->name }}</td>
					</tr>
					 <tr>
						<td class="med-green font600">Last Assigned On</td>
						<td>{{ ($get_ticket->assigneddate == '0000-00-00')? '-' :  App\Http\Helpers\Helpers::dateFormat($get_ticket->assigneddate) }}</td>
					</tr>
					<tr>
						<td class="med-green font600">Last updated On</td>
						<td><span class="js_set_lastupdate"></span></td>
					</tr>
					<?php
						$assigneduserid	 = 	App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($get_ticket->assigned,'encode');  
					?>
					<tr>
						<td class="med-green font600">Assigned</td>
						<td class="changeassigntype{{$get_ticket->ticket_id}}">
						@if($get_ticket->assigned == 0)
							<a data-url="{{ url('admin/assignticket/')}}" data-ticketid="{{$get_ticket->ticket_id}}" data-backdrop="false" data-toggle="modal" data-userid="" class="js_ticketassign tooltips"  data-target="#ticketassign_modal" href="">Assign</a>
						@else
							{{ $get_ticket->get_assignee->name  }}
							
							@if($get_ticket->status != 'Closed')
								<a data-url="{{ url('admin/assignticket/')}}" data-ticketid="{{$get_ticket->ticket_id}}" data-backdrop="false" data-toggle="modal" data-userid="{{ $assigneduserid }}" class="js_ticketassign tooltips margin-l-20"  data-target="#ticketassign_modal" href=""><span class="font600 text-underline med-orange">Reassign</span></a>
							@endif
						@endif	
						</td>
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
			<div class="box-body p-b-15">
				<table class="table table-billing-edit">
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
						<td><span class="patient-status-bg-form js_set_status @if(@$get_ticket->status == 'Open')label-danger @else label-success @endif"> {{ @$get_ticket->status }}</span></td>
					</tr>
				</table>	
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
</div>

@if(count($get_assigneelist)>0)
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="comments"></i> <h3 class="box-title">Assignee Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
		<div class="box-body margin-t-10">
			<div class="table-responsive">
				<table id="list_noorder" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Assigned To</th>
							<th>Assigned By</th>
							<th>Assigned On</th>
						</tr>
					</thead>
					<tbody>
					   @foreach($get_assigneelist as $key=>$assigneeval) 
						<tr style="cursor:default;">
							<td>{{ $assigneeval->get_assignedto->name }}</td>
							<td>{{ $assigneeval->get_assignedby->name }}</td>
							<td>{{ App\Http\Helpers\Helpers::dateFormat($assigneeval->created_at) }}</td>
						</tr>
					   @endforeach	
					</tbody>
				</table>
			</div>
		</div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
@endif

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="comments"></i> <h3 class="box-title">Conversation Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <div class="ticket_convarsation js_control_height">
                @include ('admin/manageticket/conversation')
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	@if(@$get_ticket->status =="Open" && (@$get_ticket->assigned == Auth::user ()->id || Auth::user ()->id == 1))
		<span data-url="{{ url('admin/manageticket/'.@$get_ticket_data_id.'/edit')}}" class="med-white med-bg-green padding-4-15 js_reply_ticket form-cursor"> Reply</span> 
	@endif	
</div>

<div class="col-lg-12 col-md-12 col-sm-12 no-padding js_form hide"></div>
@stop
@push('view.scripts')
<script type="text/javascript">
    $(function () {
        $(window).load(function () {
            var get_current_val = $(".js_control_height").height();
            if (get_current_val > 260)
                get_current_val = 249;
            $(".js_control_height").parents("div.ledger-scroll").css("height", get_current_val);
            $(".js_control_height").parents("div.slimScrollDiv").css("height", get_current_val);
        });
    });
    setStatus();
    /*** Get the stats details function starts ***/
    $(document).on('click', '.js_reply_ticket', function () {
        $(".js_reply_ticket").addClass("hide");
        $(".js_form").removeClass("hide");
        var stats_name = $(this).attr("data-url"); //url
        $.ajax({
            url: stats_name,
            type: 'GET',
            success: function (msg) {
                $(".js_form").html(msg);
                callicheck();
                $('[name="description"]').focus();
                $("html, body").animate({scrollTop: $(document).height()}, 800);
                formValidate();
            }
        })
    });
	
    $(document).on('click', '.js_cancel', function () {
        resetOption();
    });
	
    $(document).on('click', '.js_submit', function (e) {
        $('#js-bootstrap-validator').data("bootstrapValidator").resetForm();
        $('#js-bootstrap-validator').bootstrapValidator('validate');
        $('#js-bootstrap-validator').unbind("success").on('success.form.bv', function (ev) {
            ev.preventDefault();
            var formData = new FormData();
            var image_detail = $('input[name="filefield1"]')[0].files[0];
            var description = $('textarea[name="description"]').val();
            image_detail = (image_detail == '' || image_detail == null) ? '' : image_detail;
            formData.append('attachment', image_detail);
            formData.append('description', description);
            formData.append('status', $('input[name="status"]:checked').val());
            $("#js_wait_popup").modal("show");
            $.ajax({
                url: api_site_url + "/admin/manageticket/update/" + $('form#js-bootstrap-validator').attr("data-id"),
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                },
                type: "POST", // Type of request to be send, called as method
                data: formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false, // The content type used when sending data to the server.
                cache: false, // To unable request pages to be cached
                processData: false, // To send DOMDocument or non processed data file it is set to false
                success: function (result) {
                    if (result == 0) {
                        $("#js_wait_popup").modal("hide");
                        $("html, body").animate({scrollTop: "0px"});
                        if ($("p.alert-danger").length == 0) {
                            $('section.content').find(".row:first").prepend('<p class="alert alert-danger margin-t-m-20 margin-b-20" id="success-alert"><button class="close " data-dismiss="alert">×</button>Something went wrong, try after sometime</p>');
                        }
                        $(".alert-danger").fadeTo(1000, 600).slideUp(600, function () {
                            $(".alert-danger").alert('close');
                        });
                        resetOption();
                        readMore();
                        setStatus();
                    } else {
                        $(".js_control_height").html(result);
                        resetOption();
                        readMore();
                        setStatus();
                        $("#js_wait_popup").modal("hide");
                    }
                }
            });
        });
        $(this).off('click');
    });
    /*** Get the stats details function Ends ***/

    /*** Get the stats updated icon function starts ***/
    function formValidate() {
        $('#js-bootstrap-validator').bootstrapValidator({
				message: 'This value is not valid',
				excluded: ':disabled',
				feedbackIcons: {
					valid: '',
					invalid: '',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					description: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("common.validation.description") }}'
							},
						}
					},
					status: {
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("admin/faq.validation.status") }}'
							},
						}
					},
					filefield1: {
						message: '',
						validators: {
							file: {
								extension: 'pdf,jpeg,jpg,png,gif,doc,zip,xls,csv,docx,xlsx,txt',
								message: attachment_valid_lang_err_msg
							},
							callback: {
								message: attachment_length_lang_err_msg,
								callback: function (value, validator, element) {
									if ($('[name="filefield1"]').val() != "") {
										var size = parseFloat($('[name="filefield1"]')[0].files[0].size / 1024).toFixed(2);
										var get_image_size = Math.ceil(size);
										return (get_image_size > filesize_max_defined_length) ? false : true;
									}
									return true;
								}
							}
						}
					}
				}
			});
    }
	
    function resetOption() {
        var status = $(".js_class_status").val().trim();
        if (status != "Closed")
            $(".js_reply_ticket").removeClass("hide");
        $(".js_form").addClass("hide").html("");
    }
	
    function setStatus() {
        var lastupdate = $(".js_lastupdate").val();
        var class_status = $(".js_class_status").val();
		if(class_status == 'Closed'){
			$(".js_set_status").removeClass('label-danger');
			$(".js_set_status").addClass('label-success');
		}
        $(".js_set_lastupdate").html(lastupdate);
        $(".js_set_status").html(class_status);
    }
    /*** Get the stats updated icon function Ends ***/
</script>
@endpush