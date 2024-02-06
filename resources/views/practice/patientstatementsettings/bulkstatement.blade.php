@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Patient Statement <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Bulk Statement</span></small>
        </h1>
        <ol class="breadcrumb">
            @include('layouts.practice_module_stream_export', ['url' => ''])
            <li><a href="{{ url('patientstatementsettings') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/patientstatement')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/patientstatementsettings/tabs')
@stop

@section('practice')
@if(@$psettings->bulkstatement == 1)

<div class="col-lg-12 col-md-12 col-xs-12 margin-t-20"><!--  Col-12 Starts -->
    <div class="box no-shadow"><!--  Left side Content Starts -->
        <div class="box-header-view">
            <i class="fa {{Config::get('cssconfigs.common.bulkstatement')}}"></i> <h3 class="box-title">
			@if(@$psettings->statementcycle == 'All')
				Statement Cycle - All	
			@else
				Statement Cycle - {{ $psettings->statementcycle }} - Week {{ $get_currentweek }}
			@endif
			</h3>
            <div class="box-tools pull-right margin-t-2">
               @if($psettings->paymentmessage_1 != '' )
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding text-right selPmtMsg hide">
                    <a href="" data-toggle="modal" data-target="#paymentmessage-modal" class="font600 js_changemessage" ><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit Message</a>
				</div>
			   @endif
            </div>
        </div><!-- /.box-header -->
        		
		<div class="box-body form-horizontal p-b-0">  
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_bulkpatientlist no-padding">
                
				<div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="ajax_table_list hide"></div>
					<div class="data_table_list" id="js_ajax_part">					
			
						<table class="table table-striped table-bordered" id="bulkstatementsearch">
							<thead>
								<tr>
									<th class="td-c-1"><input type="checkbox" id="js_select_all" class="margin-t-0"><label for="js_select_all" class="no-bottom">&nbsp;</label></th>	
									<th>Patient Name</th>	
									<th>Acc No</th>
									<th>Statements</th>
									<th>Last Payment Date</th> 
									<th>Last Payment Amt($)</th> 
									<th>Pat Balance($)</th> 
									<th>Options</th>
								</tr>
							</thead>
							<tbody>
								@include ('practice/patientstatementsettings/statement_list', 
								['patients_arr' => $patients_arr, 'insurance_list' => $insurance_list])							
							</tbody>
						</table>
					
						{!! Form::open(array('url' => 'bulkstatement','id'=>'js_bulkstatement_form')) !!}
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-group no-bottom">	
							<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12 form-group">
								<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
									<input type="hidden" name="patient_ids" class="js_patientids">
								</div>
								<textarea name="paymentmessage_1" class="hide js_paymentmessage">{{ 
									$psettings->paymentmessage_1
								}}</textarea>
								
							</div>
							<input type="hidden" name="sendtype" class="js_sendtype">
						</div>	
					</div>
				</div>				
				
            </div>			
        </div>
		
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding text-center js_button hide">
			<div class="box-body form-horizontal">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					{!! Form::submit('Send XML Statement', ['name'=>'send','class'=>'btn btn-medcubics-small margin-l-m-5 js_bulkstatement']) !!}
					
					{!! Form::submit('Send CSV Statement', ['name'=>'send','class'=>'btn btn-medcubics-small js_bulkstatement']) !!}
					
					{!! Form::submit('Send PDF Statement', ['name'=>'send','class'=>'btn btn-medcubics-small js_bulkstatement']) !!}
					
					{!! Form::submit('Email Statement',['name'=>'send','class'=>'btn btn-medcubics-small js_bulkstatement']) !!} 
				</div>
					
			</div><!-- /.box Ends-->
		</div>
		

	{!! Form::close() !!}
			
    </div><!--  Left side Content Ends -->
</div><!--Background color for Inner Content Ends -->

<div id="paymentmessage-modal" class="modal fade in">
    <div class="modal-md">
		<div class="modal-content">
			
			<div class="modal-header">
				<button type="button" class="close js_pclose_form" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Message</h4>
			</div>
			{!! Form::open(array('id'=>'js_paymentmessageform','class'=>'popupmedcubicsform')) !!}
			<div class="modal-body">
				@if($psettings->paymentmessage_1 != '')	
				<div class="med-green font600 form-group">
					<textarea class="form-control" name="paymentmessage_popup" cols="30" rows="5" data-bv-field="paymentmessage_popup">{{ 
							$psettings->paymentmessage_1
					}}</textarea>
				</div>
				<div class="modal-footer">
					<button class="confirm btn btn-medcubics-small js_move_message" type="submit">Save</button>
					<button class="cancel btn btn-medcubics-small" type="button" data-dismiss="modal">Cancel</button>
				</div>
				
				@endif
			</div>
			{!! Form::close() !!}
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
</div><!-- Modal Light Box Ends -->

@else
<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-xs-12 margin-t-20"><!--  Col-12 Starts -->
            <div class="box no-shadow"><!--  Left side Content Starts -->
                {{ trans("common.validation.not_found_msg") }}
            </div><!--  Left side Content Ends -->
        </div><!--Background color for Inner Content Ends -->
    </div><!-- Inner Content for full width Ends -->
</div>
@endif

@stop 

@push('view.scripts')

{!! HTML::script('js/datatables_serverside.js') !!} 

<script type="text/javascript">
	
	var api_site_url = '{{url("/")}}';	
	var listing_page_ajax_url = api_site_url+"/bulkstatement/statementList";
    var dataArr = {};   
    var wto = '';
	
	$(document).ready(function(){
		
		/* Search function start */
		var column_length = $('#bulkstatementsearch thead th').length; 		
	
		function accessAll() {		
								
			var selected_column = ['Patient Name', 'Acc No', 'Statements', 'Last Payment Date', 'Last Payment Amt($)', 
			'Pat Balance($)', 'Options'];
			var allcolumns = [];
			for (var i = 0; i < column_length; i++) {
				allcolumns.push({"name": selected_column[i], "bSearchable": true});
			}
			statementSearch(allcolumns); /* Trigger datatable */
		}		
		accessAll(); /* Trigger datatable */	
		
	});

	function statementSearch(allcolumns) {		
		$("#bulkstatementsearch").DataTable({			
			"createdRow": 	function ( row, data, index ) {
								if(data[1] != undefined)
									data[1] = data[1].replace(/[\-,]/g, '');
							},		
			"bDestroy"	:	true,
			"paging"	: 	true,
			"searching"	: 	false,
			"ordering"	: 	false,
			"info"		: 	true,
			"aoColumns"	: 	allcolumns,
			"columnDefs": [ { "orderable": false, "targets": 0 }, { "orderable": false, "targets": -1 } ],
			"autoWidth"	: 	false,
			"lengthChange"		: true,
			"searchHighlight"	: true,
			//"processing": true,
			"searchDelay": 450,
			"serverSide": true,	
			//"order": [1, 'asc'],			
			"ajax": $.fn.dataTable.pipeline({
				url: listing_page_ajax_url,
				beforeSend: displayLoadingImage(),
				pages: 1, // number of pages to cache
				success: function(){
                    //                 
				}	
			}),
	        "columns": [
	            {"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" }
	        ],	
	        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(".ajax_table_list").html(aData+"</tr>");
				var get_orig_html = $(".ajax_table_list tr").html();
				var get_attr = $(".ajax_table_list tr").attr("data-url");
				var get_class = $(".ajax_table_list tr").attr("class");
				$(nRow).addClass(get_class).attr('data-url', get_attr);
				$(nRow).closest('tr').html(get_orig_html);
				$(".ajax_table_list").html("");				
			},
			"fnDrawCallback": function(settings) {
				var str = $('.js_patientids').val();
				// Make previous selected records remains selected.
				if ($.trim(str) != '') {
					var strArray = str.split(',');
					for (var i = 0; i < strArray.length; i++) {
						$('.js_remove'+strArray[i]).find('.js_sub_checkbox:checkbox').prop('checked', true);
					}
				}
			
				$(".js_button").removeClass('hide');
				//$(".selPmtMsg").removeClass('hide');
				hideLoadingImage(); // Hide loader once content get loaded.
				//searchHighLight();
			}
		});
	}
		
	$(document).ready(function() {
		
		$(document).on('click','.js_changemessage', function (e) {
			$('[name=paymentmessage_popup]').val($('.js_paymentmessage').val());
		});	
		
		$('#js_paymentmessageform')
			.bootstrapValidator({
				message: 'This value is not valid',
				excluded: ':disabled',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					paymentmessage_popup:{
						message:'',
						validators:{
							notEmpty:{
								message: '{{ trans("practice/practicemaster/patientstatementsettings.validation.paymentmessage") }}'
							}
						}
					}
				}
			});
	});
	
	$(document).on('click','.js_move_message', function (e) {
		e.preventDefault();
		$('textarea.js_paymentmessage').val($('[name=paymentmessage_popup]').val());	
		$('#paymentmessage-modal').modal('hide');
		js_sidebar_notification('success',"Message saved successfully");
		//$('#patientnote_model').find('.text-center').html('Message saved successfully');
		//$('#patientnote_model').modal('show');
	});
</script>
@endpush