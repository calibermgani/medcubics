@extends('admin')
@section('toolbar')    
    <div class="row toolbar-header">
        <section class="content-header">
           <h1>
                <small class="toolbar-heading"><i class="fa font14 fa-cart-arrow-down"></i> Claims <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> EDI Reports </span></small>
            </h1>
            <ol class="breadcrumb">                
                <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom" data-toggle="tooltip" data-original-title="Print"></i></a></li-->
                <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            </ol>
        </section>
    </div>
@stop
@section('practice')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="box box-info no-shadow">
            <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom js-dynamic-tab-menu">
				<ul class="nav nav-tabs">
					<li class="active"><a href="javascript:void(0);" ><i class="fa fa-bars i-font-tabs"></i><span id="edireportdetlink_main0" class="js_edireportdetlink"> List</span></a></li>             
				</ul>
			</div>
            
			<span id="edi_report_list_part">                
                @include('claims/claims/edi_reports_list',['list_page'=>'non_archive_list']) 
            </span>
			
        </div>
    </div>
@stop