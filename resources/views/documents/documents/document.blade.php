@extends('admin_stat_view')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="folders"></i> Documents </small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <!--li><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/documents')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
	@include ('documents/documents/tabs')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" id="js_ajax_part">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 alert"></div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10">
			<div class="box-header">
				<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3>
				<div class="box-tools pull-right margin-t-4">
					<a class="font600 font13 hidden-print" href="" data-url="{{url('documents/create')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal" accesskey="m"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Docu<span class="text-underline">m</span>ent</a>
				</div>
			</div><!-- /.box-header -->
		</div><!-- /.box-header -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom m-b-m-15 hide"><!-- Search Toolbar Starts Here -->
            <div class="box box-info no-shadow no-bottom" >
                <div class="box-body hidden-print">                  
                    <div class="col-lg-5 col-md-6 col-sm-7 col-xs-12 no-bottom form-horizontal margin-t-2 no-print">                               
                        <div class="form-group-billing">                         
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 billing-select2-orange">
								{!! Form::radio('search_module', 'practice', true, ['class'=>'flat-red js_search_module', 'id'=>'c-practice']) !!} {!! Form::label('c-practice', 'Practice',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp; 
								{!! Form::radio('search_module', 'facility',null,['class'=>'flat-red js_search_module', 'id'=>'c-facility']) !!} {!! Form::label('c-facility', 'Facility',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp; 
								{!! Form::radio('search_module', 'provider',null,['class'=>'flat-red js_search_module', 'id'=>'c-provider']) !!} {!! Form::label('c-provider', 'Provider',['class'=>'med-darkgray font600 form-cursor']) !!} &nbsp;
								{!! Form::radio('search_module', 'patients',null,['class'=>'flat-red js_search_module', 'id'=>'c-patient']) !!} {!! Form::label('c-patient', 'Patient',['class'=>'med-darkgray font600 form-cursor']) !!}
                            </div>                                                     
                        </div>
                    </div>
                    
					<?php $count = count($documents_list); ?>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 form-horizontal margin-t-5 hidden-xs">                
                                   
                    </div>
                                        
                    <div class="col-lg-5 col-md-4 col-sm-3 col-xs-12 no-bottom form-horizontal"  style="border-color:#8ce5bb;">
                        <div class="form-group-billing">                                                
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <input type="search" class="form-control input-sm-modal" placeholder="Search here..." aria-controls="documents">
                            </div>
                        </div>                  
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- Search Toolbar Ends here -->
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
            <div class="box no-border no-shadow no-bottom">                 
				<div class="box-body">
                        <div class="table-responsive js_table_list">
							@include('documents/documents/document_list')                            
						</div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
		</div>
	</div>  
	<?php  if(!isset($get_default_timezone)){
		$get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
	}?>
@stop

@push('view.scripts')
    <style> 
		#documents_tab_filter input{display:none;} 
	</style>
	<script type="text/javascript">
		var get_default_timezone = '<?php echo $get_default_timezone;?>'; 
		$(document).on("change", "#jsclaimnumber", function (event) {
			claim_number_val = $(this).val();
			if (claim_number_val == 'all') {
				$("#jsclaimnumber").select2({
					formatSelectionTooBig: function (a) {
						return "Delete all to get claims displayed";
					},
					maximumSelectionSize: 1
				});
			} else if (claim_number_val == null || claim_number_val == '') {
				var optionExists = $("#jsclaimnumber option[value=all]").length;
				if (!optionExists) {
					$('#jsclaimnumber').prepend('<option value="all">All</option>');
				}
				$("#jsclaimnumber").select2({});
			} else if (claim_number_val != 'all') {
				$('#jsclaimnumber option[value="all"]').remove();
			}
		});
		<?php if($doctype == 'all'){?>
				setTimeout(function(){	
				$('li.default-dynamic-details').removeClass('active');
				$('li.default-dynamic-details#all').addClass('active');
				$('.js-common-document-link').show();
				$( document ).ready(function() {  $('.default-dynamic-details#all').trigger('click');});		}, 00);
		<?php	}
		?>
	</script>
@endpush