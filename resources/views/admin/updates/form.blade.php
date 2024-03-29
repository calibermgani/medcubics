<div class="col-md-12 col-md-12 col-sm-12 col-xs-12" >

<div class="box no-shadow">
<div class="box-block-header with-border">
<h3 class="box-title"><i class="livicon" data-name="info" data-color='#008e97' data-size='16'></i> New Updates</h3>

</div><!-- /.box-header -->

<div class="box-body  form-horizontal space20 margin-l-10">

<div class="form-group">
  {!! Form::label('Title', 'Title', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label med-green font600']) !!}
  <div class="col-lg-6 col-md-4 col-sm-6 col-xs-10 @if($errors->first('title')) error @endif">
      {!! Form::text('title',null,['class'=>'form-control']) !!}
      {!! $errors->first('title', '<p> :message</p>')  !!}
  </div>
  <div class="col-sm-1"></div>
</div>

<div class="form-group">
  {!! Form::label('Description', 'Description', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label med-green font600']) !!}
  <div class="col-lg-12 col-md-12 col-sm-12 @if($errors->first('description')) error @endif">
      {!! Form::textarea('description',null,['class'=>'form-control ckeditor','id'=>'editor1']) !!}
      {!! $errors->first('description', '<p> :message</p>')  !!}
  </div>
  <div class="col-sm-1"></div>
</div>
<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>

<div class="form-group">
  {!! Form::label('Privacy', 'Privacy',  ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label med-green font600']) !!}
  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('privacy')) error @endif">
    {!! Form::select('privacy', array('' => '-- Select a Privacy --','Public' => 'Public','Private' => 'Private','Group' => 'Group','User' => 'Select User'), $privacy_id ,['class'=>'form-control select2' , 'id'=>'privacy']) !!}
    {!! $errors->first('privacy', '<p> :message</p>')  !!}
  </div>
  <div class="col-sm-1"></div>
</div>

<div class="form-group" id="group" style="display: none;">
  {!! Form::label('Group', 'Group',  ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label med-green font600']) !!}
  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('group')) error @endif">
      
  {!! Form::select('group', array('' => '-- Select a Group --'),['class'=>'form-control select2 getgrouplist']) !!}
 
  </div>
  <div class="col-sm-1"></div>
</div>

<div class="form-group" id="selectuser" @if($privacy_id != 'User') style="display: none;" @endif>
  {!! Form::label('Select user', 'Select user',  ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label med-green font600']) !!}
  <div class="col-sm-6 col-xs-10 @if($errors->first('user')) error @endif">
      
    {!! Form::select('selectuser', $get_user, $user_id, ['multiple'=>'multiple','name'=>'selectuser[]', 'class' => 'form-control select2 getuserlist']) !!}

  </div>
  <div class="col-sm-1"></div>
</div>

<div class="form-group">
  {!! Form::label('Attachment', 'Attachment',  ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label med-green font600']) !!}
  
	<?php 
		$blog_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$blog->id,'encode'); 
		if(isset($blog->attachment) && $blog->attachment != '') {
			$img_details = [];
			$img_details['module_name']='user';
			$img_details['file_name']=$blog->attachment;
			$img_details['practice_name']="admin";
			$img_details['need_url']="yes";
			$get_blogimage_url = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details); 
			$ext = pathinfo($get_blogimage_url, PATHINFO_EXTENSION); 
		} else {
			$get_blogimage_url = '';
			$ext = ''; 
		}
	?>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('attachment')) error @endif">
		@if(isset($blog->attachment) && $blog->attachment!='')
			@if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' ) 
				<a class="" style="" href="#" onClick="window.open('{{ url('admin/updates/getattachment/'.$blog_id) }}', '_blank')" >{{$blog->attachment}}</a>
			@else 
				<a href=" {{ $get_blogimage_url }}" target="_blank" class=""><i class="fa fa-paperclip font13" data-toggle="tooltip" data-original-title="View attachment"></i>{{ $blog->attachment }}</a> 
			@endif 
		@endif 	
		<br>
		<span class="fileContainer col-lg-3" style="padding:1px 20px;margin:0px;">
			<input class="col-lg-2 col-md-2 col-sm-3 form-control" name="attachment" type="file">Upload 
		</span>
		{!! $errors->first('attachment', '<p> :message</p>')  !!} 
		&emsp;<div class="js-display-error col-lg-6"></div>
	</div>
	<div class="col-sm-1"></div>
</div>

<div class="form-group">
  {!! Form::label('Url', 'Url',  ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label med-green font600']) !!}
  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('url')) error @endif">
      {!! Form::text('url',null,['class'=>'form-control']) !!}
      {!! $errors->first('url', '<p> :message</p>')  !!}
  </div>
  <div class="col-sm-1"></div>
</div>

<div class="form-group">
  {!! Form::label('status', 'Status',  ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label med-green font600']) !!}
  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('status')) error @endif">
      {!! Form::radio('status', 'Active', true ,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
      {!! Form::radio('status', 'Inactive',null ,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'InActive',['class'=>'med-darkgray font600 form-cursor']) !!}
      {!! $errors->first('status', '<p> :message</p>')  !!}
  </div>
  <div class="col-sm-1"></div>
</div>

</div><!-- /.box-body -->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
	{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
	@if(strpos($currnet_page, 'edit') !== false)
		<?php $blog->id = APP\Http\Helpers\Helpers::getEncodeAndDecodeOfId($blog->id,'encode'); ?>
		<a href="javascript:void(0)" data-url="{{url('admin/updates/'.$blog->id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	@else
		<a href="javascript:void(0)" data-url="{{url('admin/updates/')}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	@endif
</div>

</div><!-- /.box -->
</div><!--/.col (left) -->

@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function() {
            
	$('input[type="submit"]').on('click', function(evt) {
		if($('#privacy').val()=='Group' &&  $('.getgrouplist').val()==''){
			$('#js-bootstrap-validator')
				.data('bootstrapValidator')
				.updateStatus('group', 'NOT_VALIDATED')
				.validateField('group');
		} 
		if($('#privacy').val()=='User' &&  $('.getuserlist').val()==null){
			$('#js-bootstrap-validator')
				.data('bootstrapValidator')
				.updateStatus('selectuser[]', 'NOT_VALIDATED')
				.validateField('selectuser[]');
		}
	}); 

	$('#privacy').change(function(){
		if($(this).val()=='Public' || $(this).val()=='Private')
		{
			 $('#js-bootstrap-validator')
				.data('bootstrapValidator')
				.updateStatus('group', 'VALID')
				.validateField('group');

			  $('#js-bootstrap-validator')
				.data('bootstrapValidator')
				.updateStatus('selectuser[]', 'VALID')
				.validateField('selectuser[]');   
		}
	});

	CKEDITOR.replace('editor1');
	CKEDITOR.instances.editor1.on('change', function() { 
		CKEDITOR.instances['editor1'].updateElement();
		$('#js-bootstrap-validator').bootstrapValidator();
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'description');
	});

 $('.blog_create_form')
	.bootstrapValidator({
		message : 'This value is not valid',
		excluded : ':disabled',
		feedbackIcons : {
				valid : 'glyphicon glyphicon-ok',
				invalid : 'glyphicon glyphicon-remove',
				validating : 'glyphicon glyphicon-refresh'
		},
		framework: 'bootstrap',
		fields : {
				title : {
					message : '',
					validators : {
						notEmpty : {
								message : 'Enter the title!'
						},
					}
				},
				description : {
					message : '',
					validators : {
						notEmpty : {
								message : 'Enter the description!'
						},
					}
				},
				privacy : {
				message : '',
					validators : {
					   notEmpty : {
								message : 'Select the privacy!'
						}  
					}
				},
				group : {
				message : '',
						validators: {
							callback: {
								message: 'Select a group',
								callback: function(value, validator, $field) {
									var privacyby = $('.blog_create_form').find('[name="privacy"]').val();
									var groupby = $('.blog_create_form').find('[name="group"]').val();
									return ((!groupby)&&(privacyby !="Group")) ? true : (value !== '');                           
								}
							}                                
						}
				},
				'selectuser[]' : {
				message : '',
					validators: {
						callback: {
							message: 'Enter a user',
							callback: function(value, validator, $field) {
								var privacyby = $('.blog_create_form').find('[name="privacy"]').val();
								var selectuser = $('.blog_create_form').find('[name="selectuser[]"]').val();
								return ((selectuser==null)&&(privacyby =="User")) ? false  : true; 
															
							}
						}                                
					}
			},
			 attachment: {
				validators: {
					file: {
						extension: 'jpeg,jpg,png,pdf,txt,doc,docx',
						type: 'image/jpeg,image/png,application/msword,application/pdf,text/plain,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
						maxSize: 2048 * 1024,   // 2 MB
						message: 'The selected file is not valid'
					}
				}
			},
			url: {
				 validators: {
					uri: {
						message: 'URL is not valid'
					}
				}
			}
		}
	});
});
</script>
@endpush