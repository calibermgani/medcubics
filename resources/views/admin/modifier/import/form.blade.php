<?php
echo session ( 'message' );
?>
<div class="col-md-12">
	<!-- Inner Content for full width Starts -->
	<div class="box-body-block">
		<!--Background color for Inner Content Starts -->
		<div class="col-lg-12 col-md-6 col-sm-6 col-xs-6 space20">

			<div class="box box-info no-shadow">
				<div class="box-block-header with-border">
					<i class="livicon" data-name="upload-alt"></i>
					<h3 class="box-title">Import Details</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse">
							<i class="fa fa-minus"></i>
						</button>
					</div>
				</div>
				<!-- /.box-header -->
				<!-- form start -->

				<div class="box-body  form-horizontal">

					<div class="form-group">
						{!! Form::label('', 'File name', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
						<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('frm_filename')) error @endif ">
							{!! Form::file('frm_filename') !!} 
							{!!	$errors->first('frm_filename', '<p>:message</p>') !!}
						</div>
						<div class="col-sm-1"></div>
					</div>

					<div class="form-group">
						{!! Form::label('', 'Delimiter name', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
						<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('frm_delimiter')) error @endif ">
							{!! Form::select('frm_delimiter',$delimiters,old('frm_delimiter'),['class'=>'form-control']) !!} 
							{!!	$errors->first('frm_delimiter', '<p>:message</p>') !!}
						</div>
						<div class="col-sm-1"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="box-footer">
	<div class="col-lg-12 col-md-12 col-sm-6">
		{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!} 
	</div>
</div>
