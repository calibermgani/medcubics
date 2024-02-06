@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Documents</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{url('facility/'.$facility->id)}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		 <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
		
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
    @include ('practice/facility/tabs')
@stop

@section('practice')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
        @if($checkpermission->check_url_permission('facility/{id}/facilitydocument/create') == 1)
        <a href="{{url('facility/'.$facility->id.'/facilitydocument/create')}}" class="pull-right font600 font14"><i class="fa fa-plus-circle"></i> New Document</a>         
        @endif	
    </div>
    
	@include ('practice/practice/document/practice_document_table',['document_type'=>'facility'])  
@stop            