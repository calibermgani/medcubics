
@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

<section class="content-header">
<h1>
<small class="toolbar-heading">>Codes - <span>Import</span></small>
</h1>
<ol class="breadcrumb">
	<li><a href="#" onclick="history.go(-1);return false;"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
	<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
	<li><a href="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
</ol>
</section>

</div>
@stop

@section('practice')
{!! Form::open(['url'=>'medcubics-admin/importcpt','id'=>'js-bootstrap-validator','files'=>true]) !!}
@include ('admin/cpt/import/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#js-bootstrap-validator')
			.bootstrapValidator({
			   message: 'This value is not valid',
			   excluded: ':disabled',
			   feedbackIcons: {
				   valid: 'glyphicon glyphicon-ok',
				   invalid: 'glyphicon glyphicon-remove',
				   validating: 'glyphicon glyphicon-refresh'
			   },
			   fields: {
				   frm_filename:{
					   message: '',
					   validators:{
						   notEmpty:{
							   message: 'This field is required.'
						   },
						   file:{
							   extension: 'csv,txt',
							   message: 'This selected file is not valid'
						   },
						   callback:{
							   callback:function(value, validator){
								   var pos = value.lastIndexOf('.')+1;
								   if((value.substr(pos)).toLowerCase() == 'csv'){
									   $(validator.getFieldElements('frm_delimiter')).find("option[value=',']").attr('selected',true);
									   //validator.validate('frm_delimiter');//uncommenting this line bring too much recursion error
								   }
								   else{
									   //validator.validate('frm_delimiter');//uncommenting this line bring too much recursion error
								   }
								   return true;
							   }	
						   }
					   }
				   },
				   frm_delimiter: {
					   message: 'The Code is invalid',
					   validators: {
						   notEmpty: {
							   message: 'This field is mandatory!'
						   }
					   }
				   },
			   }
		});
    });
</script>
@endpush