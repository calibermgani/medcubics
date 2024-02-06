@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> API Settings </span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li><a href="#js-help-modal" data-url="{{url('help/apisettings')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row ends -->
@stop

@section('practice-info')
@include ('practice/apisettings/apisettings_tabs')
@stop

@section('practice')
	{!! Form::open(['url'=>'apisettings','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
		@include ('practice/apisettings/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop
@push('view.scripts')
<script type="text/javascript">
    $(document).on('click', '.js_updateapisettings', function () {
    window.onbeforeunload = UnPopIt;
    var get_remove = $('input[name=removed_api]').val();
    if (get_remove != '') {
        get_remove = get_remove.substring(0, get_remove.length - 1);
        $.ajax({
            type: "GET",
            url: api_site_url + '/api/getpracticedisabledapi',
            data: 'remove_api=' + get_remove,
            success: function (result) {
                if (result.length != 0) {
                    $.confirm({
                        text: "This API already used by " + result + ". Are you sure you want to disabled this API?",
                        confirm: function () {
                            var formname = $(".medcubicsform").attr('name');
                            document.forms[formname].submit();
                        },
                        cancel: function () {
                            window.location.href = window.location.href;
                        }
                    });
                } else {
                    var formname = $(".medcubicsform").attr('name');
                    document.forms[formname].submit();
                }
            }
        });
    } else {
        var formname = $(".medcubicsform").attr('name');
        document.forms[formname].submit();
    }
});
    var ids = '';
$(document).on('ifToggled change', '.js_api_check', function () {
    var chk = $(this).is(":checked");
    if (chk == false) {
        ids += $(this).attr('data-api') + ',';
    }
    $('input[name=removed_api]').val(ids);
});
</script>
@endpush