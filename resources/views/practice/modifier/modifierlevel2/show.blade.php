@extends('admin')



@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <?php $modifiers->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($modifiers->id,'encode'); ?>
        <ol class="breadcrumb">
            <li><a href="{{ url('modifierlevel2')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/modifiers')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/modifier/tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
    @if($checkpermission->check_url_permission('modifierlevel2/{modifierlevel2}/edit') == 1)
    <a href="{{ url('modifierlevel2/'.$modifiers->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif
</div>



<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" ><!-- Col Starts -->
	<div class="box box-info no-shadow"><!-- Box Starts -->
		<div class="box-block-header margin-b-10">
			<i class="livicon" data-name="info"></i> <h3 class="box-title">Modifier Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->  
		<div class="box-body  form-horizontal margin-l-10"><!-- Box Body Starts -->
			

			<div class="form-group">
				{!! Form::label('code', 'Code', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
					<p class="show-border no-bottom">{{ $modifiers->code }}</p>
				</div>
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group">
				{!! Form::label('name', 'Name', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
				<div class="col-lg-4 col-md-6 col-sm-6 col-xs-10">
				   <p class="show-border no-bottom">{{ @$modifiers->name }}</p>
				</div>
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group">
				{!! Form::label('description', 'Description', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
				<div class="col-lg-4 col-md-6 col-sm-6 col-xs-10">
					<p class="show-border no-bottom">{{ @$modifiers->description }}</p>
				</div>
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group">
				{!! Form::label('anesthesia_base_unit', 'Anesthesia Base Unit', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
				   <p class="show-border no-bottom">{{ $modifiers->anesthesia_base_unit }}</p>
				</div>
				<div class="col-sm-1"></div>
			</div>
			
			<div class="form-group">
		{!! Form::label('created by', 'Created By', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!} 
		<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
			<p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::shortname($modifiers->created_by) }}</p>
		</div>
		<div class="col-sm-1"></div>
	</div>

	<div class="form-group">
		{!! Form::label('created On', 'Created On', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!} 
		<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
			<p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::dateFormat($modifiers->created_at,'date')}}</p>
		</div>
		<div class="col-sm-1"></div>
	</div>

	<div class="form-group">
		{!! Form::label('Updated By', 'Updated By', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!} 
		<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
			<p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::shortname($modifiers->updated_by) }}</p>
		</div>
		<div class="col-sm-1"></div>
	</div>

@if(@$modifiers->updated_by >='12-11-2015')
	<div class="form-group">
		{!! Form::label('Updated On', 'Updated On', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!} 
		<div class="col-lg-2 col-md-3 col-sm-6 col-xs-10">
			<p class="show-border no-bottom">@if($modifiers->updated_at  >='12-11-2015'){{ App\Http\Helpers\Helpers::dateFormat($modifiers->updated_at,'date')}} @endif</p>
		</div>
		<div class="col-sm-1"></div>
	</div>
@endif

<div class="form-group">
	{!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-6 control-label']) !!} 
	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 @if($errors->first('status')) error @endif">
		@if($modifiers->status == 'Active')  
		   {!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','disabled']) !!} Inactive 
		 @else   
			{!! Form::radio('status', 'Active',null,['class'=>'flat-red','disabled']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',true,['class'=>'flat-red']) !!} Inactive 
		@endif	 
		{!! $errors->first('status', '<p> :message</p>')  !!}
	</div>                        
</div>
</div><!-- Box Body Ends --> 

		
			
	</div><!-- /.box -->
</div><!--/.col ends -->
@stop 