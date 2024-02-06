<footer class="main-footer">
	<a href="#js-model-swith-patient" data-toggle="modal"data-target="#js-model-swith-patient"tabindex="-1"></a>
	<div id="js-model-swith-patient" class="modal fade">
		<div class="modal-sm-usps">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Search Patient</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						{!! Form::select('search_by',['patient_name'=>'Patient Name','act_no'=>'Acc No', 'dob'=>'DOB', 'ssn'=>'SSN'], null, ['class'=>'select2 form-control js_modal_search_patient_by','id'=>"js_modal_search_patient_by"]) !!}
					</div>

					<div class="form-group input-group input-group-sm">
						<input name="modal_search_swith_patient_keyword" type="text" class="form-control js_modal_search_swith_patient_keyword" id="js_modal_search_swith_patient_keyword" placeholder="Search patient using key words">
						<span class="input-group-btn">
							<button class="btn btn-flat btn-medgreen js_modal_search_swith_patient_button" type="button">Search</button>
						</span>
					</div>
					<span id='search_swith_patient_keyword_err' class='help-block med-red hide' data-bv-validator='notEmpty' data-bv-for='search_swith_patient_keyword_err' data-bv-result='INVALID'><span id="search_swith_patient_keyword_err_content">Please enter search keyword!</span></span>
					<div class="form-horizontal" id="swith_patient_search_result"></div>
				</div>
			</div>
		</div>
	</div>
	
    <div class="modal fade" id="newupdates" tabindex="-1" role="dialog" aria-labelledby="newupdates" aria-hidden="true">
        <div class="modal-dialog modal-dialog-slideout modal-sm" role="document">
            <div class="modal-content" style="box-shadow: none !important;">
                <div class="modal-header">
                    <button type="button" class="close-slide" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times med-darkgray" aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                     <div id="newfeauture-model"></div>
                   <!-- Recent Updates Goes Here -->
                </div>                
            </div>
        </div>
    </div>   
   	<div class="hidden-xs" style="position: fixed; bottom: 2px; right: 0;">
        <!-- <a href="#" class="footer-med-bot" style="margin-right:10px;">Ask Medcubics</a> -->
        @if(Auth::check() && Auth::user()->isProvider())
        <script type="text/javascript">
				var isProvider = 'yes';
		</script>
        @else
        <script type="text/javascript">
				var isProvider = 'no';
		</script>
        <a href="#js-model-swith-patient" class="footer-icon-extra" data-target="#js-model-swith-patient" data-toggle="modal">
        	<i style="margin-left: 20px; margin-right:20px;" class="livicon" data-toggle="tooltip" data-placement="top" title="Search" data-name="search" data-size="18" data-color="#ffffff" ></i>
        </a>
        @endif
         <?php        
            $updatescount = \App\Http\Helpers\Helpers::getNewUpdatesCount(); 
		?>
        @if($updatescount != '0' && $updatescount != '')
        |         
        <a href="#" class="footer-icon-extra" data-toggle="modal" data-target="#newupdates" data-url="{{url('/profile/newfeauture-model')}}" id="new-features">
        	<i style="margin-left: 20px; margin-right:20px;" class="livicon footer-icon-extra" data-toggle="tooltip" data-placement="top" title="Updates" tooltip="Updates" data-name="sky-dish" data-size="18" data-color="#ffffff" ></i>
        </a>
        @endif
    </div>
    Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.
	<?php
		if(isset(Auth::user()->role_id) && Auth::user()->role_id != 1){
			$end_date = \App\Http\Helpers\Helpers::timezone(date('m/d/Y H:i:s'),'m/d/Y',Session::all()['practice_dbid']);
			$start_date = date('m-01-Y', strtotime($end_date));
		}else{
			$end_date = $start_date = '';
		}
    ?>
	<!-- JS block starts -->

    <!-- Common error message end -->
	<script type="text/javascript">
		var start_date = "{{$start_date}}";
		var end_date = "{{$end_date}}";
		var today = end_date;
		var api_site_url = '{{url("/")}}';
		var chk_env_site = '{{ getenv("APP_ENV") }}';
		var pageName = '';
	</script>
    <!-- Session Alert Window Ends  -->
    <?php App\Http\Helpers\CssMinify::minifyJs('common_js'); ?>
    {!! HTML::script('js/'.md5("common_js").'.js') !!}

    <?php
		App\Http\Helpers\CssMinify::minifyJs('app_js');
		App\Http\Helpers\CssMinify::minifyJs('function_js');
    ?>
	{!! HTML::script('js/'.md5("app_js").'.js') !!}
	{!! HTML::script('plugins/input-mask/jquery.inputmask.js?'.mt_rand())!!}
	{!! HTML::script('plugins/timepicker/bootstrap-timepicker.min.js?'.mt_rand())!!}

	@if(strpos($currnet_page, 'patients') !== false)
		{!! HTML::script('js/patients.js?'.mt_rand())!!}
	@endif

	{!! HTML::script('js/function.js?'.mt_rand())!!}

	{!! HTML::script('js/twilio.min.js')!!}
	{!! HTML::script('js/mousewheel.js?'.mt_rand())!!}

	{!! HTML::script('js/changedatefunction.js?'.mt_rand())!!}
    {!! HTML::script('js/document_modal.js?'.mt_rand())!!}
	@if(strpos($currnet_page, 'dashboard') !== "FALSE")
		{!! HTML::script('js/dashboard/fusioncharts.js?'.mt_rand())!!}
		{!! HTML::script('js/dashboard/fusioncharts.theme.fint1.js?'.mt_rand())!!}
		{!! HTML::script('js/dashboard/fusioncharts.theme.fint2.js?'.mt_rand())!!}
		{!! HTML::script('js/dashboard/fusioncharts.theme.fintinsurance.js?'.mt_rand())!!}
		{!! HTML::script('js/dashboard/fusioncharts.charts.js?'.mt_rand())!!}
		{!! HTML::script('js/dashboard.js?'.mt_rand())!!}
	@endif

    @if((count($routex) >= 2 && ($routex[1] != 'show' && $routex[1] != 'edit' && $routex[1] != 'create'))
		||($currnet_page == 'listinsurancefavourites'
	|| $currnet_page == 'armanagement'
	|| $currnet_page == 'armanagement/insurance'
	|| $currnet_page == 'armanagement/insurancewise'
	|| $currnet_page == 'armanagement/insurance1'
	|| $currnet_page == 'armanagement/1'
	|| $currnet_page == 'listfavourites'
	|| $currnet_page == 'claims/{type?}'
	|| $currnet_page == 'claims/status/{type?}'
	|| $currnet_page == 'claims/search/{type?}'
	|| $currnet_page == 'claims'
	|| $currnet_page == 'listicdfavourites'
	|| $patient_current_page  == 'patients'
	|| $patient_current_page  == 'uploadedpatients'
	|| strpos($currnet_page, 'searchicd') !== false
	|| strpos($currnet_page, 'searchcpt') !== false
	|| strpos($currnet_page, 'admin/searchicd') !== false
	|| strpos($currnet_page, 'admin/searchcpt') !== false)
	|| (strpos($currnet_page, 'scheduler') !== false)
	|| (strpos($currnet_page, 'useractivity') !== false)
	|| (strpos($currnet_page, 'myticket') !== false)
	|| (strpos($currnet_page, 'report') !== false)
	|| (strpos($currnet_page, 'individualstatement') !== false)
	|| $currnet_page == 'claims/transmission/{id}'
	|| $currnet_page == 'claims/transmission'
	|| $currnet_page == 'claims/edireports'
	|| (strpos($currnet_page, 'statementhistory') !== false
	|| $currnet_page == 'armanagement/armanagementlist')
	|| (strpos($currnet_page, 'charges') !== false)
	|| $currnet_page == 'armanagement/myproblemlist'
	|| $currnet_page == 'armanagement/problemlist'
	|| $currnet_page == 'armanagement/myfollowup'
	|| $currnet_page == 'armanagement/denials'
	|| $currnet_page == 'armanagement/otherfollowup'
	|| $currnet_page == 'followup/category'
	|| $currnet_page == 'followup/question'
	|| $currnet_page == 'admin/metrics'
	|| $currnet_page == 'analytics/financials'
	|| $currnet_page == 'practice/charge/delete'
	|| $currnet_page =='admin/userLoginHistory/{pageType}'
	|| strpos($currnet_page, 'bulkstatement') !== false
	|| strpos($currnet_page, 'managecare') !== false
	|| strpos($currnet_page, 'facility') !== false
	|| strpos($currnet_page, 'provider') !== false
	|| strpos($currnet_page, 'insurance') !== false
	|| strpos($currnet_page, 'icd') !== false
	|| strpos($currnet_page, 'cpt') !== false
	|| strpos($currnet_page, 'modifierlevel1') !== false
	|| strpos($currnet_page, 'modifierlevel2') !== false
	|| strpos($currnet_page, 'code') !== false
	|| strpos($currnet_page, 'employer') !== false
	|| strpos($currnet_page, 'comhistory') !== false
	|| strpos($currnet_page, 'feeschedule') !== false
	|| strpos($currnet_page, 'reason') !== false
	|| strpos($currnet_page, 'holdoption') !== false
	|| strpos($currnet_page, 'claimsubstatus') !== false
	|| strpos($currnet_page, 'statementcategory') !== false
	|| strpos($currnet_page, 'procedurecategory') !== false
	|| strpos($currnet_page, 'questionnaires') !== false
	|| strpos($currnet_page, 'apptemplate') !== false
	|| strpos($currnet_page, 'staticpage') !== false)

        <?php App\Http\Helpers\CssMinify::minifyJs('datatables_js'); ?>
		{!! HTML::script('js/'.md5("datatables_js").'.js') !!}
		{!! HTML::script('js/datatables/datatable_search_highlight.js?'.mt_rand())!!}

		<!-- Popup modal file start [admin.blade file split] -->
		@include('layouts/datatable_pipeline')
		<!-- Popup modal file end -->
		<script type="text/javascript">
			$("#example1").DataTable(/*{
				"dom": 'lBfrtip',
				"sSwfPath": "../macro/$proj/js/Pdfjs/copy_csv_xls_pdf.swf",
				"buttons": [
						{
							extend: 'collection',
							text: 'Export',
							orientation: 'landscape',
							'sTitle': "Benchmark Report",
							pageSize: 'LEGAL',
							buttons: [
								'copy',
								'excel',
								'csv',
								'pdf',
								'print'
							]
						}
					]
			// other options
			}*/);
			var str = $('.dataTables_filter input').val();
			if($.trim(str) != ''){
				listingpageHighlight('example1'); // Set highlight
			}
			var table = $('#search_table_payment').DataTable({"paging": true,
							"lengthChange": false,
							"searching": true,
							"ordering": true,
							"info": true,
							"responsive": true,
						 });
			var str = $('.dataTables_filter input').val();
			if($.trim(str) != ''){
				listingpageHighlight('search_table_payment'); // Set highlight
            }
            $("#list_noorder").DataTable({
				"aaSorting": [],
			});

			$('#search_table').DataTable({"paging": true,
							"lengthChange": false,
							"searching": true,
							"ordering": false,
							"info": true,
							"responsive": true,
						 });

			$("#documents").DataTable({
				"paging": true,
                "lengthChange": true,
                "searching": true,
                "info": true,
				"columnDefs": [{ "orderable": false, "targets": -1 }],
				"fnDrawCallback": function(settings) {
					var str = $('.dataTables_filter input').val();
					if($.trim(str) != ''){
						listingpageHighlight('documents');
					}
				}
			});

            var table = $(".claims").DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "order": [],
                "autoWidth": false,
				"columnDefs": [{ "orderable": false, "targets": 0 } ]
            });

            table.on( 'draw', function () {
		    	var body = $( table.table().body() );
			        body.unhighlight();
			    body.highlight( table.search() );
			});

            $("#claim_list_table").DataTable({
                "order": []
            });

            $('#example2').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "fixedHeader": true,
                "responsive": true,
                "autoWidth": true
            });

            $('#claims_table').DataTable({
			    "paging": true,
			    "lengthChange": false,
			    "searching": true,
                "searchHighlight"   : true,
			    "ordering": true,
			    "info": true,
			    //"fixedHeader": true,
			    "responsive": true,
			    //"autoWidth": true,
			    "order": [1, 'asc'],
			    "columnDefs": [{ "orderable": false, "targets": 0 } ],
				"fnDrawCallback": function(settings) {
                    $('#claims_table td').unhighlight();
					if ($('.dataTables_filter input').val() != "") {
						var selector_name = $("#claims_table tr td");
						var str = $('.dataTables_filter input').val();
						selector_name.highlight($.trim(str));
					}
                }
			});

			$("#eligibility,#benefit_verification").DataTable({
				"paging": false,
                "lengthChange": false,
                "searching": false,
                "info": false,
				"columnDefs": [{ "orderable": false, "targets": -1 }]
			});

			$("#scheduler_reports").DataTable({
				"paging": true,
                "lengthChange": false,
                "searching": false,
                "info": false
			});
		</script>
	@endif

    <?php App\Http\Helpers\CssMinify::minifyJs('form_js'); ?>
    {!! HTML::script('js/'.md5("form_js").'.js') !!}

    @if(strpos($currnet_page, 'armanagementlist') == true)
    	{!! HTML::script('js/stats_list_selection.js?'.mt_rand())!!}
    @endif

    @if(strpos($currnet_page, 'document') !== false)
    {!! HTML::script('js/documents_module.js?'.mt_rand())!!}
	@endif

	@if(strpos($currnet_page, 'edit') !== false ||  strpos($currnet_page, 'create') !== false || $currnet_page == 'admin/useractivity' || $currnet_page == 'admin/metrics' || $currnet_page == 'practicescheduler/provider/{provider_id}' || $currnet_page == 'practiceproviderscheduler/{provider_id}/{scheduler_id}' || strpos($currnet_page, 'import') !== false || strpos($currnet_page, 'scheduler') !== false || (strpos($currnet_page, 'event') !== false) || (strpos($currnet_page, 'profile') !== false) || @$currnet_arr[1] == 'maillist'  || $patient_current_page == 'patients' || $patient_current_page == 'uploadedpatients' || strpos($currnet_page, 'searchicd') !== false || strpos($currnet_page, 'searchcpt') !== false || $currnet_page == 'claims/{type?}' || $currnet_page == 'claims'|| (strpos($currnet_page, 'statementhistory') !== false))
		{!! HTML::script('js/eligibility.js?'.mt_rand())!!}
        {!! HTML::script('js/admin.js?'.mt_rand())!!}

		<script type="text/javascript">
			$(function () {
				//Provider Page DOB
				$("#dob").datepicker({
					/*minDate: "-60Y -6M",
					maxDate: "-5Y +12M",
					changeMonth: true,
					changeYear: true*/
				});
				$("#txtAge").datepicker({
					yearRange: "-90:+0",
					changeMonth: true,
					changeYear: true
				});
				<?php if(Request::segment(1) != 'patients' && Request::segment(3) != 'problemlist'){ ?>
					$("#dos").datepicker({minDate: 0, maxDate: "+1M +10D"});
				<?php } ?>
				$("#date_of_birth").datepicker({
					yearRange: "-90:+0",
					changeMonth: true,
					changeYear: true
				});

				$("#effective_date").datepicker({
					yearRange: "-10:+20",
					changeMonth: true,
					changeYear: true
				});

				$("#termination_date").datepicker({
					yearRange: "-10:+10",
					changeMonth: true,
					changeYear: true
				});

				if(typeof(get_default_timezone) != "undefined" && get_default_timezone !== null) {
				var eventDates = {};
    			eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
				$('.datepicker').datepicker({
					format: "yyyy-mm-dd",
					autoclose: true,
					beforeShowDay: function(d) {
				        setTimeout(function() {
				        $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');
				         }, 10);

				        var highlight = eventDates[d];
						if( highlight ) {
							 return [true, "ui-state-highlight", ''];
						} else {
							 return [true, '', ''];
						}
				   	}
				});
				}else{
					$('.datepicker').datepicker({format: "yyyy-mm-dd", autoclose: true});
				}
				$("#note_date").datepicker({minDate: 0});
				$(document).on('focusin','#follow_up_date',function(){
					$(this).datepicker({minDate: 0});
				});
				// $("#follow_up_date").datepicker({minDate: 0});
			});
		</script>
		{!! HTML::script('js/profile.js?'.mt_rand())!!}
    @endif

	@if(strpos($currnet_page, 'ticket') !== false || strpos($currnet_page, 'searchticket') !== false)
		<script type="text/javascript">
			var desc_errormessage = '{{ trans("common.validation.description") }}';
			var emptymsg = '{{ trans("common.validation.not_found_msg") }}';
		</script>
		{!! HTML::script('js/ticket.js?'.mt_rand())!!}
	@endif

    @if(strpos($currnet_page, 'claims') !== false)
        {!! HTML::script('js/claims.js?'.mt_rand() ) !!}
    @endif

    @if(strpos($currnet_page, 'blog') !== false)
		{!! HTML::script('js/blog.js?'.mt_rand())!!}
		{!! HTML::script('js/ckeditor/ckeditor.js?'.mt_rand())!!}
		<script>
			CKEDITOR.config.toolbar = [
				['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'Find', 'Replace', '-', 'Outdent', 'Indent', '-', 'Print', 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
				['Image', 'Table', '-', 'Link', 'Flash', 'Smiley', 'TextColor', 'BGColor', 'Source']
			];
		</script>
    @endif
    @if(strpos($currnet_page, 'admin/updates') !== false)
		{!! HTML::script('js/blog.js?'.mt_rand())!!}
		{!! HTML::script('js/ckeditor/ckeditor.js?'.mt_rand())!!}
		<script>
			CKEDITOR.config.toolbar = [
				['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'Find', 'Replace', '-', 'Outdent', 'Indent', '-', 'Print', 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
				['Image', 'Table', '-', 'Link', 'Flash', 'Smiley', 'TextColor', 'BGColor', 'Source']
			];
		</script>
    @endif

	<!-- Javascript Plugin enable code start [admin.blade file split]-->
	@include('layouts/plugin_enable')
	<!-- Javascript Plugin enable code end -->

    @if(strpos($currnet_page, 'patients') !== false &&  strpos($currnet_page, 'superbill') !== false && strpos($currnet_page, 'create') !== false)
		{!! HTML::script('js/superbill_claim.js?'.mt_rand())!!}
    @endif

	@if((strpos($currnet_page, 'superbills') !== false))
		{!! HTML::script('js/superbill.js?'.mt_rand())!!}
    @endif

	@if((strpos($currnet_page, 'questionnaire') !== false))
		{!! HTML::script('js/questionnaire.js?'.mt_rand())!!}
    @endif

    @if(strpos($currnet_page, 'patients') !== false && $routex[0]!='patientstatementsettings')
        {!! HTML::script('js/patients-function.js?'.mt_rand())!!}
		{!! HTML::script('js/patients-contact-function.js?'.mt_rand())!!}
		{!! HTML::script('js/patients-contact-function-edit.js?'.mt_rand())!!}
    @endif

	@if(strpos($currnet_page, 'charges') !== false || (strpos($currnet_page, 'patients') !== false &&  strpos($currnet_page, 'billing') !== false || strpos($currnet_page, 'billing_authorization') !== false || strpos($currnet_page, 'payment') !== false) || strpos($currnet_page, 'armanagement') !== false || strpos($currnet_page, 'ledger') !== false)
        {!! HTML::script('js/billing.js?'.mt_rand())!!}
    @endif

	@if(strpos($currnet_page, 'armanagement') !== false || strpos($currnet_page, 'ledger') !== false || strpos($currnet_page, 'payments') !== false|| strpos($currnet_page, 'billing') !== false || strpos($currnet_page, 'charges') !== false )
		{!! HTML::script('js/armanagement.js?'.mt_rand())!!}
	@endif

	@if(strpos($currnet_page, 'patientstatementsettings') !== false || strpos($currnet_page, 'bulkstatement') !== false || strpos($currnet_page, 'individualstatement') !== false || strpos($currnet_page, 'patients') !== false || strpos($currnet_page, 'charges') !== false)
		{!! HTML::script('js/patient_statement.js?'.mt_rand())!!}
	@endif

	@if(strpos($currnet_page, 'maillist') !== false)
		{!! HTML::script('js/bootstrap-colorpicker.min.js?'.mt_rand())!!}
		{!! HTML::script('js/bootstrap-colorpicker-plus.js?'.mt_rand())!!}
		{!! HTML::script('js/bootstrap-tokenfield.js?'.mt_rand())!!}
		{!! HTML::script('js/mailbox.js?'.mt_rand())!!}
		{!! HTML::script('js/ckeditor/ckeditor.js?'.mt_rand())!!}

		<script>
			CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
			CKEDITOR.config.autoParagraph = false;
			CKEDITOR.config.fillEmptyBlocks = false;	// Prevent filler nodes in all empty blocks.
			CKEDITOR.config.toolbar = [
				['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'Find', 'Replace', '-', 'Outdent', 'Indent', '-', 'Print', 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
				['Image', 'Table', '-', 'Link', 'Flash', 'Smiley', 'TextColor', 'BGColor', 'Source']
			];
		</script>

		@if(@$currnet_arr[2] != 'settings')
			<script>
				$(document).ready(function () {
					/*--------------------------*/
					var CK_EDITOR = CKEDITOR.replace('compose-textarea');
					var textbox = $("textarea#compose-textarea");
					CK_EDITOR.on('change', function (event) {
						textbox.text(this.getData());
					});
				});
			</script>
		@else
			<script>
				$(document).ready(function () {
					var CK_EDITOR1 = CKEDITOR.replace('signature_content');
					var textbox1 = $("textarea#signature_content");
					CK_EDITOR1.on('change', function (event) {
						textbox1.text(this.getData());
					});
				});
			</script>
		@endif
    @endif

	@if(strpos($currnet_page, 'message') !== false)
		{!! HTML::script('js/bootstrap-colorpicker.min.js?'.mt_rand())!!}
		{!! HTML::script('js/bootstrap-colorpicker-plus.js?'.mt_rand())!!}
		{!! HTML::script('js/bootstrap-tokenfield.js?'.mt_rand())!!}
		{!! HTML::script('js/ckeditor/ckeditor.js?'.mt_rand())!!}

		<script>
			CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
			CKEDITOR.config.autoParagraph = false;
			CKEDITOR.config.fillEmptyBlocks = false;	// Prevent filler nodes in all empty blocks.
			CKEDITOR.config.toolbar = [
				['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'Find', 'Replace', '-', 'Outdent', 'Indent', '-', 'Print', 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
				['Image', 'Table', '-', 'Link', 'Flash', 'Smiley', 'TextColor', 'BGColor', 'Source']
			];
		</script>
    @endif

    <!-- { !! HTML::script('js/scanner/scanner.js?'.mt_rand())!! } -->
	@if(strpos($currnet_page, 'correspondence/{temp_id}/edit') !== false)
		{!! HTML::script('js/ckeditor/ckeditor.js?'.mt_rand())!!}
		<script>
			$(document).ready(function () {
				/*--------------------------*/
				var CK_EDITOR = CKEDITOR.replace('editor1');
				var textbox = $("textarea#editor1");

				CK_EDITOR.on('change', function (event) {
					textbox.text(this.getData());
				});
			});
		</script>
    @endif

	@if(strpos($currnet_page, 'armanagement') !== false && strpos($currnet_page, 'myproblemlist') == false && strpos($currnet_page, 'problemlist') == false && strpos($currnet_page, 'denials') == false)
		{!! HTML::script('js/ckeditor/ckeditor.js?'.mt_rand())!!}
		<script>
			CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
			CKEDITOR.config.autoParagraph = false;
			CKEDITOR.config.fillEmptyBlocks = false;	// Prevent filler nodes in all empty blocks.
			CKEDITOR.config.allowedContent = true;
			CKEDITOR.config.toolbar = [
				['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'Source'],
			];

			$(document).ready(function () {
				/*--------------------------*/
				var CK_EDITOR = CKEDITOR.replace('areditor1');
				var textbox = $("textarea#areditor1");
				CK_EDITOR.on('change', function (event) {
					textbox.text(this.getData());
				});
			});
		</script>
    @endif

    @if($currnet_page == "patients/{id}")
	    <script type="text/javascript">
	        var checking_hash = window.location.hash;
	        if (checking_hash == '') {
	            window.location.hash = "#personal-info";
	        }
	    </script>
    @endif

    @if(count($routex) >= 1)
		@if(($routex[0] == 'practice' || $routex[0] == 'facility') || (array_key_exists(3,$routex) && $routex[3] == 'customerpractices'))
			@if(strpos($currnet_page, 'edit') !== false ||  strpos($currnet_page, 'create') !== false )
				{!! HTML::script('js/jslider/js/tmpl.js') !!}
				<?php App\Http\Helpers\CssMinify::minifyJs('slider_js'); ?>
				{!! HTML::script('js/'.md5("slider_js").'.js') !!}
				<!-- Slider file start [admin.blade file split] -->
				@include('layouts/slider')
				<!-- Slider modal file end -->
			@endif
		@endif
    @endif
    <!-- Ends Here -->

    @if(count($routex) >= 1)
		<!-- Templates Starts Here -->
		@if($routex[0] == 'templates' || $routex[0] == 'apptemplate'|| $routex[0] == 'staticpage')
			@if(strpos($currnet_page, 'edit') !== false ||  strpos($currnet_page, 'create') !== false )
				{!! HTML::script('js/ckeditor/ckeditor.js?'.mt_rand()) !!}
				{!! HTML::script('js/templates-function.js?'.mt_rand()) !!}
			@endif
		@endif
    @endif

    <!-- Ends - Templates CK Editor  -->
    @if((strpos($currnet_page, 'calendar') !== false) || ((strpos($currnet_page, 'event') !== false) && (strpos($currnet_page, 'bloggroup') === false)))
		{!! HTML::script('js/eventCalendar/simplecalendar.min.js?'.mt_rand()) !!}
		{!! HTML::script('js/eventCalendar/simplecalendar.js?'.mt_rand()) !!}
		{!! HTML::script('js/eventCalendar/timepicker.js?'.mt_rand()) !!}
		{!! HTML::script('js/eventCalendar/weekline.min.js?'.mt_rand()) !!}
		{!! HTML::script('js/eventCalendar/weekline.js?'.mt_rand()) !!}
		{!! HTML::script('js/jquery-ui.js?'.mt_rand()) !!} <!-- datepicker  -->

		<?php App\Http\Helpers\CssMinify::minifyJs('form_js'); ?>
		{!! HTML::script('js/'.md5("form_js").'.js') !!} <!-- select 2  -->
		<script type="text/javascript">
			$(function () {
				$('.dropdown').on('show.bs.dropdown', function (e) {
					$(this).find('.dropdown-menu').first().stop(true, true).slideDown();
				});

				// Add slideUp animation to dropdown
				$('.dropdown').on('hide.bs.dropdown', function (e) {
					$(this).find('.dropdown-menu').first().stop(true, true).slideUp();
				});

				//Initialize Select2 Elements
				$(".select2").select2();

				//iCheck for checkbox and radio inputs
				// @@ check and remove it, if not used
				$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
					checkboxClass: 'icheckbox_minimal-blue',
					radioClass: 'iradio_minimal-blue'
				});

				//Red color scheme for iCheck
				// @@ check and remove it, if not used
				$('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
					checkboxClass: 'icheckbox_minimal-red',
					radioClass: 'iradio_minimal-red'
				});

				//Flat red color scheme for iCheck
				$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
					checkboxClass: 'icheckbox_flat-green',
					radioClass: 'iradio_flat-green'
				});
			});
			function startTime() {
				var today = new Date();
				var h = today.getHours();
				var m = today.getMinutes();
				var s = today.getSeconds();
				m = checkTime(m);
				s = checkTime(s);
				document.getElementById('time').innerHTML = h + ":" + m + ":" + s;
				var t = setTimeout(startTime, 500);
			}
			function checkTime(i) {
				if (i < 10) {
					i = "0" + i
				}
				;  // add zero in front of numbers < 10
				return i;
			}
		</script>
    @endif

    <!-- Starts - Delete confirmation message -->
    {!! HTML::script('js/jquery_confirm.js?'.mt_rand()) !!}
    <script type="text/javascript">
		$(document).ready(function () {
            $(".js-delete-confirm").confirm();
        });
	</script>
    <!-- Ends - Delete confirmation message -->

    <!-- Reports Page Starts -->
    @if(strpos($currnet_page, 'reports') !== false || strpos($currnet_page, 'armanagement/armanagement') !== false || strpos($currnet_page, 'armanagement1/armanagement') !== false  || strpos($ar_main_page, 'armanagement') !== false)
		{!! HTML::script('js/plugins/morris.js?'.mt_rand())!!}
    @endif

	@if(strpos($currnet_page, 'reports') !== false)
		{!! HTML::script('js/reports.js?'.mt_rand())!!}
    @endif
    {!! HTML::script('js/export.js?'.mt_rand())!!}
    <!-- Reports Page Ends -->

    <!-- Main Scheduler -->
    @if(count($routex) >= 1)
		@if($routex[0] == 'scheduler' || (strpos($currnet_page, 'scheduler') !== false))
			{!! HTML::script('js/fullcalendar/moment.min.js?'.mt_rand())!!}
			{!! HTML::script('js/fullcalendar/fullcalendar.min.js?'.mt_rand())!!}
			{!! HTML::script('js/fullcalendar/jquery.ui.touch.js?'.mt_rand())!!}
			{!! HTML::script('js/fullcalendar/scheduler.js?'.mt_rand())!!}
			{!! HTML::script('js/scheduler.js?'.mt_rand())!!}
			{!! HTML::script('plugins/iCheck/icheck.min.js?'.mt_rand())!!}
		@endif
		@if(strpos($currnet_page, 'scheduler/list') !== false)
			{!! HTML::script('js/schedulerlist.js?'.mt_rand())!!} <!-- Scheduler list page script include here-->
		@endif
    @endif

    <!-- Main Scheduler Ends -->
	@if(strpos($currnet_page, 'searchicd') !== false || strpos($currnet_page, 'searchcpt') !== false)
		{!! HTML::script('js/advanced-search-function.js?'.mt_rand())!!}
    @endif

    @stack('view.scripts1')
    @stack('view.scripts')
    @stack('view.document_popup_scripts')

	@if(Session::has('success'))
		<script type="text/javascript">
			$(document).ready(function(){
				msg = '<?php echo Session::get('success'); ?>';
				js_sidebar_notification('success',msg);
			})
		</script>
	@endif

	@if(Session::has('error'))
		<script>
			$(document).ready(function(){
				msg = '<?php echo Session::get('error'); ?>';
				js_sidebar_notification('error',msg);
			})
		</script>
	@endif

	@if(Session::has('info'))
		<script>
			$(document).ready(function(){
				msg = '<?php echo Session::get('info'); ?>';
				js_sidebar_notification('info',msg);
			})
		</script>
	@endif

    <script type="text/javascript">
        // For Alert Message Hide After 5 seconds
       $('#new-features').click(function(e){
             url = $(this).attr('data-url');
            $.ajax({
                url: url,
                type: 'get',
                data: '',
                success: function (result, textStatus, jQxhr) {                    
                    split_result = result.data;                   
                    $('#newfeauture-model').html(split_result);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        });
        $("#success-alert").hide();
        $("#success-alert").alert();
        $("#success-alert").fadeTo(3000, 500).slideUp(500, function () {
            $("#success-alert").alert('close');
        });
    </script>

    <!-- Session popup Starts -->
    <script>
        /*** Session time check starts ***/
        function sessionCheck() {
            var url = api_site_url + '/sessioncheck';
            $.post(url, {'_token': '{!! csrf_token() !!}'}, function (result) {
                var result = result.split("/");
                var interval = result[0];
                var exitTime = result[1];
				if (interval < 0) {
					if ($('#session_model').hasClass('in') ==false) alertFunc();
					var convert_value = Math.abs(interval);
					if (($('#session_model').hasClass('in') ==true) && exitTime < convert_value) {
						window.onbeforeunload = UnPopIt;
                        sessionLogout();
                    }
                }
            });
        }
        /*** Session time check ends ***/

        /*** Session loguot starts ***/
        function sessionLogout() {
			window.location = api_site_url + "/auth/logout";
			window.location = api_site_url + "/auth/login";
        }
        /*** Session loguot ends ***/

        /*** Session popup trigger starts ***/
        function alertFunc() {
            $("#session_model")
				.modal({show: 'false', keyboard: false})
				.one('click', '.js_session_confirm', function (e) {
					var conformation = $(this).attr('id');
					if (conformation == "true") {
						var url = api_site_url + '/sessioninsert';
						$.post(url, {'_token': '{!! csrf_token() !!}'}, function (result) { });
					} else {
						sessionLogout();
					}
				});
        }
        /*** Session popup trigger ends ***/
        //setInterval(sessionCheck, 1000 * 10); //get every one minutes check last session
    </script>
    <!-- Session popup Ends -->

	<!-- Date picker icon click start -->
    <script>
        //set focus for open datepicker
        $(document).on('click','.fa-calendar-o',function() {
			if($(this).next('input').hasClass("dm-date"))
				$(this).next('input.dm-date').focus();
			else
				$(this).prev('input.dm-date').focus();
		});

		//set datepicker icon color reset when datepicker closed
		$(document).on('focusout','input.hasDatepicker',function() {
			$(this).prev('i.fa-calendar-o').removeClass('med-green');
		});
    </script>
    <!-- Date picker icon click end -->

	@if(strpos($currnet_page, 'dashboard') !== false )
		<link href="https://fonts.googleapis.com/css?family=Fjalla+One" rel="stylesheet">
		@include('dashboard/dashboard')
    @endif

    <!-- Common ARManagament list and patient armanagement list both pages included -->
	@if((Request::segment(3) == 'armanagement' && Request::segment(4) == 'list') || (Request::segment(2) == 'armanagementlist'))
		<script>
			document.addEventListener('visibilitychange', function(){
				if(document.visibilityState == 'visible'){
					if($('.modal').is(':visible') == false){
						var current_active_class = $('.js_claimdetlink').parents('li.active').attr('class');
						current_active_class = current_active_class.replace(" ", ".");
						// Reload only for claim review tabs not for list
						if (current_active_class.indexOf("js-claim-tab-info_") >= 0){
							$('.'+current_active_class).find('span').click();
							$('body').removeClass('modal-open');
						}
					}
				}
			});
		</script>
	@endif

	<script>
		<?php if(Request::segment(1) != 'admin'){ ?>
		document.addEventListener('visibilitychange', function(){
			if(document.visibilityState == 'visible'){
				var prv_id = "<?php echo Session::get('practice_dbid'); ?>";
				var ajaxUrl = "<?php echo url('/get_practice_session_id'); ?>";
				$.ajax({
                    type: "GET",
                    url: ajaxUrl,
                    dataType: "json",
                    success: function (json) {
						var current_id = json.id;
						var role_id = json.role_id;
						if(role_id == 0){
							if(prv_id != current_id) {
								js_alert_popup("{{ trans('practice/practicemaster/practice.validation.practice_change') }}");
								$(".js_note_confirm").addClass('practice_change');
							}
						}
                    }
                });
			}
		});
		$(document).on('click',".practice_change",function(){
			location.reload();
		});
		<?php } ?>
	</script>

	<script>
	$(document).on('change','.auto-generate',function(){
		$('#search_remember').prop('checked',false);
	});
	</script>

	<!-- JS block ends -->
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</footer>
<style type="text/css">
	div#botmanWidgetRoot > div{
    min-height: 65px !important;
    min-width: 65px !important;
    bottom: 0px !important;
    right: 5px !important;
}
.desktop-closed-message-avatar {
    top: 3px !important;
    right: 3px !important;
}
</style>
<script type="text/javascript">
	$(document).ready(function () {
		$('.listing_search_append').show();
	});
	// Author: Baskar
	// Session out redirection
	$(document).ajaxError(function(event, jqxhr, settings, exception) {

    if (exception == 'Unauthorized') {

        /*// Prompt user if they'd like to be redirected to the login page
        var redirect = confirm("You're session has expired. Would you like to be redirected to the login page?");

        // If the answer is yes
        if (redirect) {*/

            // Redirect
            window.location = "{{url('/')}}";
        //}
    }
});
</script>
<!--<script>
var api='{{url("/botman")}}';
var text = "Hi, this is MedBot I am here you to assist you with Medcubics";
var botmanWidget = {
	title:'MedBot',
	chatServer:api + "/webhook",
	frameEndpoint:api + "/chat",
	introMessage:text,
	placeholderText:'Type Here...',
	aboutText:'Powered by Medcubics.',
	aboutLink:'https://medcubics.com'

};
</script>
<script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>-->
