@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Notes </span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="" data-url="{{ url('profile') }}" class="js_next_process hide"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')


<div class="col-lg-9 col-md-12 col-xs-12 col-xs-12 m-b-m-15 margin-t-m-13"><!-- Profile Header Starts -->

    @include('profile/layouts/tabs')

    {!! Form::open(['method' => 'POST','url' => 'profile/changepassword','enctype'=>'multipart/form-data','id'=>'js-bootstrap-validator']) !!}

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >
        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <div class="box no-shadow">
            <div class="box-block-header with-border">
                <i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}}"></i> <h3 class="box-title">Notes</h3>
                <div class="box-tools pull-right margin-t-2">
                    <a href="#" data-toggle="modal" data-target="#myModal" id="popup" class="font600 form-cursor js-addmore_insurance font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New</a>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <p class="font16 text-center med-green" style="font-size: 26px;margin-top: 20px;margin-bottom: 20px;"> Notes</p>
            </div>
            <div class="box-body margin-t-10 min-height-notes-sec" id="dynamicnotes">
                @foreach($notes_list as $list)
                <div class="col-lg-3 col-md-3 postIt">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white sticky-notes">
                        <p><a><i  class="js-popupnotes-delete fa fa-close pull-right margin-t-4 med-gray m-r-m-3 delete_notes cur-pointer" data-note-id="{!! $list->id !!}" data-placement="left"  data-toggle="tooltip" data-original-title="Delete"></i>
                         <i style="display:none" class="fa fa-edit pull-right margin-t-4 med-gray m-r-m-3 edit_notes" data-note-id="{!! $list->id !!}" data-placement="left"  data-toggle="tooltip" data-original-title="Edit"></i></a></p>
                        <p class="med-green font600 margin-t-m-8">{!! App\Http\Helpers\Helpers::checkAndDisplayDateInInput($list->created_at); !!}</p>
                        <p class="sticky-notes-scroll">{!! $list->notes !!}</p>                        
                        <p class="font600" style="">Remind On : <span class="med-orange font600"><?php if($list->date != '0000-00-00') echo App\Http\Helpers\Helpers::checkAndDisplayDateInInput($list->date);  else echo "-Nil-";   ?></span> </p>
                    </div>
                </div>
                @endforeach  
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!--/.col (left) -->
    {!! Form::close() !!}
</div>






<div id="myModal" class="modal fade">
    <div class="modal-md-500">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="notes_title">Add Note</h4>
            </div>
            <div class="modal-body form-horizontal p-b-0">
                
                
                <div class="form-group">
                    {!! Form::label('Description', 'Description', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label']) !!}
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-9">
                        <textarea name="personal_note" class="form-control" id="personal_note"></textarea>
						<span style="color:red" class="personal_note_error"></span>
                    </div>                        
                </div> 
                
                <div class="form-group">
                    {!! Form::label('Date', 'Reminder Date', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label']) !!}
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-9">
                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i>
                        <input type="text" class="form-control js_datepicker dm-date" name="note_date" value="" placeholder="MM/DD/YYYY" id="note_date"/>
                        <input type="hidden" name="note_id" id="note_id"/>
                    </div>                        
                </div> 
                
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <button type="button" id="note_save"  class="btn btn-medcubics-small" data-dismiss="modal" >Save</button>
                    <a href="#" class="btn btn-medcubics-small" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->  

<div id="js_confirm_patient_demo_remove" class="modal fade js_common_modal_popup">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center">Are you sure want to delete?</div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_modal_confirm js_common_modal_popup_save btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal">Yes</button>
					<button class="js_modal_confirm js_common_modal_popup_cancel btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 





<div class="col-lg-3 col-md-3 col-sm-6  col-xs-12 p-l-0">
    @include('profile/layouts/rightside-tabs')
</div>

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