@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> APP Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Questionnaires <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('questionnaire/template')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/questionnaire_template')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
	@include ('practice/questionnaires/tabs') 
@stop

@section('practice')
<?php 
	$question_count = 1;
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
    @if($checkpermission->check_url_permission('questionnaire/template/{template}/edit') == 1)
    <a href="{{ url('questionnaire/template/'.$id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif	
</div>    
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
	@foreach($questionnaries as $list_key => $list_val)
		@if($question_count ==1)
        <div class="box-header-view margin-b-10">
            <h3 class="box-title"> {{ $list_val->title }}</h3>
            
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
		@endif
			<div>
				<div class="med-green font600">{{$question_count}}. {{$list_val->question}}</div>
				@if($list_val->answer_type =="text")
					<div class="ad-submenu">
						<input type="{{ $list_val->answer_type}}" class="form-control" placeholder="User value comes here..." style="width:30%;" disabled />
					</div>
				@else
					
					<div class="ad-submenu">
					@foreach($list_val->questionnaries_option as $list_ans_key => $list_ans_val)
						<?php  $val = $list_val->answer_type ?>
						<span class="bg-role-submenu"><input type="{{ $list_val->answer_type}}" style="position:relative;top:3px;" disabled />&nbsp;{{$list_ans_val->option}}</span>
					@endforeach
					</div>
				@endif
			</div>
        
		<?php  $question_count++; ?>
		@endforeach
		</div><!-- /.box-body -->
    </div><!-- /.box Ends-->

</div><!--  Left side Content Ends -->  

@stop            