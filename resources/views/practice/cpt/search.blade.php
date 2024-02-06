@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} font14"></i> CPT / HCPCS</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('cpt') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
    <!--           <li><a href="{{ url('cpt/create') }}"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></a></li>            -->
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

            <!--li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/cptreports/'])
            </li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/cpt/tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
           <div id="example1_filter" class="dataTables_filter pull-left"> 
			{!! Form::open(['method'=>'POST','id'=>'js-bootstrap-validator','class'=>'advanced_search_form','name'=>'search_keyword_cpt']) !!}
				<label>
					<input type="search" name="search_keyword" class="form-control" placeholder="Enter your text here..." aria-controls="example1">
					<input type="hidden" name="search_for" value="cpt">
					<a class="js_advanced_search btn btn-medgreen line-height-10 margin-t-m-5" data-value="cpt"><i class="fa {{Config::get('cssconfigs.common.search')}}"></i> Search</a>
				</label>
			{!! Form::close() !!}
			</div>
        </div>
    </div><!--Background color for Inner Content Ends -->
    
    <div class="box-body-block"><!-- Search result Body starts  -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 "><!-- Search result Col Starts -->
            <div class="box box-info no-shadow hide" id="js_result_show"><!-- Search result box starts -->
                <div class="box-header with-border"><!-- Box header starts -->
                    <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Search List</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header --> 
                <div class="box-body" id="js_advanced_result_table">
                </div><!-- /.box-body -->
            </div><!-- search result box ends -->

            <!-- IMO Search popmodel and reload button Start-->
            <div id="overlay_part" class="overlay col-xs-offset-2 hide med-green font16 font600">
                <i class="fa fa-spinner fa-spin med-green"></i> Processing
            </div>

            <div id="form_modal_view" class="modal fade in">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">CPT / HCPCS</h4>
                        </div>
                        <div class="modal-body">
                            <span id="js_content"></span>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-medcubics" data-dismiss="modal" type="button">Close</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- IMO Search popup modal and reload button End-->            
        </div><!-- Search result Col Ends -->
    </div><!-- <!-- Search result Body Ends -->   
@stop