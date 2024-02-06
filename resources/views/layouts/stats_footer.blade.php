<footer class="main-footer">
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
         <a href="#js-model-swith-patient" class="footer-icon-extra" data-target="#js-model-swith-patient" data-toggle="modal">
            <i style="margin-left: 20px; margin-right:20px;" class="livicon" data-toggle="tooltip" data-placement="top" title="Search" data-name="search" data-size="18" data-color="#ffffff" ></i>
        </a>

        <?php        
            $updatescount = \App\Http\Helpers\Helpers::getNewUpdatesCount();             
        ?>
        @if($updatescount != '0' && $updatescount != '')
        |         
        <a href="#" class="footer-icon-extra" data-toggle="modal" data-target="#newupdates" id="new-features" data-url="{{url('/profile/newfeauture-model')}}">
            <i style="margin-left: 20px; margin-right:20px;" class="livicon footer-icon-extra" data-toggle="tooltip" data-placement="top" title="Updates" tooltip="Updates" data-name="sky-dish" data-size="18" data-color="#ffffff" ></i>
        </a>
        @endif
    </div>
    Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.
    <?php
		if(Auth::user()->role_id != 1){
			$end_date = \App\Http\Helpers\Helpers::timezone(date('m/d/Y H:i:s'),'m/d/Y',Session::all()['practice_dbid']);
			$start_date = date('m-01-Y', strtotime($end_date));
		}else{
			$end_date = $start_date = '';
		}
    ?>
	<script>
		var start_date = "{{$start_date}}";
		var end_date = "{{$end_date}}";
		var today = end_date;
	</script>
    <!-- JS block starts -->

    <script type="text/javascript">
        var api_site_url = '{{url("/")}}';
        var chk_env_site = '{{ getenv("APP_ENV") }}';
    </script>

    <?php App\Http\Helpers\CssMinify::minifyJs('common_js'); ?>
    {!! HTML::script('js/'.md5("common_js").'.js') !!}
    {!! HTML::script('js/mousewheel.js') !!}

    <?php
		App\Http\Helpers\CssMinify::minifyJs('app_js');
		App\Http\Helpers\CssMinify::minifyJs('function_js');
    ?>
    {!! HTML::script('js/'.md5("app_js").'.js') !!}
	{!! HTML::script('js/patients.js') !!}
    {!! HTML::script('js/function.js') !!}
    {!! HTML::script('js/export.js?'.mt_rand())!!}
    {!! HTML::script('plugins/input-mask/jquery.inputmask.js') !!}

    <!-- Charges code inclusion-->
    {!! HTML::script('plugins/timepicker/bootstrap-timepicker.min.js') !!}
    @if(strpos($currnet_page, 'charges') !== "FALSE" || (strpos($currnet_page, 'patients') !== false && strpos($currnet_page, 'billing') !== false || strpos($currnet_page, 'billing_authorization') !== false) ||strpos($currnet_page, 'payments') !== false)
    {!! HTML::script('js/billing.js') !!}
    @endif
    <?php App\Http\Helpers\CssMinify::minifyJs('datatables_js'); ?>
    {!! HTML::script('js/'.md5("datatables_js").'.js') !!}
    {!! HTML::script('js/datatables/datatable_search_highlight.js') !!}
    <!-- Javascript Plugin enable code start [admin.blade file split]-->
    @include('layouts/plugin_enable')

	@if(strpos($currnet_page, 'reports') !== false)
		{!! HTML::script('js/reports.js?'.mt_rand())!!}
    @endif
    {!! HTML::script('js/export.js?'.mt_rand())!!}


    <!-- Javascript Plugin enable code end -->
    <script type="text/javascript">
        $("#example1").DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "order": [],
            "autoWidth": false
        });
        var table = $('#search_table_payment').DataTable({"paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
        table.on('draw', function () {
            var body = $(table.table().body());
            body.unhighlight();
            body.highlight(table.search());
        });

        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false
        });

        $(document).ready(function () {
            $(".on-hover-content").hide();
        });

        $(".mm").mouseenter(function () {
            $(this).siblings(".on-hover-content").delay(600).fadeToggle("slow", "linear");
        }).mouseleave(function () {
            $(this).siblings(".on-hover-content").fadeToggle("fast", "linear");
        });

        $("#dos").datepicker({minDate: 0, maxDate: "+1M +10D"});

    </script>
	<!-- check and remove scripts1 instead use scripts. since we are using stack so it will append -->
    @stack('view.scripts1')

	@stack('view.scripts')

    @stack('view.document_popup_scripts')
	@if(Session::has('success'))
		<!--<p class="alert alert-success margin-t-m-20 margin-b-25" id="success-alert">{{ Session::get('success') }}</p>-->
		<script type="text/javascript">
			$(document).ready(function(){
				msg = '<?php echo Session::get('success'); ?>';
				js_sidebar_notification('success',msg);
			})
		</script>
	@endif

	@if(Session::has('error'))
		<!--<p class="alert alert-danger margin-t-m-20 margin-b-20" id="error-alert"><button class="close " data-dismiss="alert">×</button>{{ Session::get('error') }}</p>-->
		<script>
			$(document).ready(function(){
				msg = '<?php echo Session::get('error'); ?>';
				js_sidebar_notification('error',msg);
			})
		</script>
	@endif

	@if(Session::has('info'))
		<!--<p class="alert alert-info"><button class="close" data-dismiss="alert">×</button>{{ Session::get('info') }}</p>-->
		<script>
			$(document).ready(function(){
				msg = '<?php echo Session::get('info'); ?>';
				js_sidebar_notification('info',msg);
			});      
		</script>
	@endif
    <script type="text/javascript">
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
        // For Alert Message Hide After 5 seconds
        $("#success-alert").hide();
        $("#success-alert").alert();
        $("#success-alert").fadeTo(3000, 500).slideUp(500, function () {
            $("#success-alert").alert('close');
        });
    </script>

    @if(strpos($currnet_page, 'edit') !== false || strpos($currnet_page, 'create') !== false || $currnet_page == 'admin/useractivity' || $currnet_page == 'practicescheduler/provider/{provider_id}' || $currnet_page == 'practiceproviderscheduler/{provider_id}/{scheduler_id}' || strpos($currnet_page, 'import') !== false || strpos($currnet_page, 'scheduler1') !== false || strpos($currnet_page, 'scheduler2') !== false  || (strpos($currnet_page, 'event') !== false) || (strpos($currnet_page, 'profile') !== false) || @$currnet_arr[1] == 'maillist' ||  $patient_current_page == 'patients' ||  $patient_current_page == 'uploadedpatients' || strpos($currnet_page, 'searchicd') !== false || strpos($currnet_page, 'searchcpt') !== false)
    <?php App\Http\Helpers\CssMinify::minifyJs('form_js'); ?>
    {!! HTML::script('js/'.md5("form_js").'.js') !!}
    <script type="text/javascript">

        $(function () {
            //Provider Page DOB
            $("#dob").datepicker({
                minDate: "-60Y -6M",
                maxDate: "-5Y +12M",
                changeMonth: true,
                changeYear: true
            });
            $("#txtAge").datepicker({
                yearRange: "-90:+0",
                changeMonth: true,
                changeYear: true
            });

            $("#dos").datepicker({minDate: 0, maxDate: "+1M +10D"});
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

            $('.datepicker').datepicker({format: "yyyy-mm-dd", autoclose: true});
        });
    </script>
    @endif

    @if(strpos($currnet_page, 'edit') !== false || strpos($currnet_page, 'create') !== false || $currnet_page == 'scheduler/provider/{provider_id}' || $currnet_page == 'documents' || $currnet_page == 'patients' || $currnet_page == 'uploadedpatients' || $currnet_page == 'scheduler1' || $currnet_page == 'payments' || $currnet_page == 'payments/get-e-remittance' || strpos($currnet_page, 'import') !== false || strpos($currnet_page, 'charges') !== false)
    <?php App\Http\Helpers\CssMinify::minifyJs('form_js'); ?>
    {!! HTML::script('js/'.md5("form_js").'.js') !!}
    <script type="text/javascript">

        $(function () {
            $('#mask').click(function () {
                if ($(this).is(":checked")) {
                    // alert("yes");
                }
            });

            //Initialize Select2 Elements
            $("select2").select2();

            //Provider Page DOB
            $("#dob").datepicker({
                minDate: "-60Y -6M",
                maxDate: "-5Y +12M",
                changeMonth: true,
                changeYear: true
            });

            $("#dos").datepicker({minDate: 0, maxDate: "+1M +10D"});

            $("#date_of_birth").datepicker({
                yearRange: "-90:+0",
                changeMonth: true,
                changeYear: true
            });

            $('.datepicker').datepicker({format: "yyyy-mm-dd", autoclose: true});

            $('select.select2').select2();

            //iCheck for checkbox and radio inputs
            $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            //Red color scheme for iCheck
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
    </script>
    @endif

    @if($currnet_page == 'patients/{patients}/edit')
    {!! HTML::script('js/patients-function.js') !!}
    @endif

    <!-- Eligiblity starts -->
    {!! HTML::script('js/eligibility.js') !!}
    <!-- Eligiblity Ends -->

    <!-- Main Scheduler -->
    @if($currnet_page=='scheduler1' || $currnet_page=='scheduler2' )
    {!! HTML::script('js/fullcalendar/moment.min.js') !!}
    {!! HTML::script('js/fullcalendar/fullcalendar.min.js') !!}
    {!! HTML::script('js/fullcalendar/scheduler.js') !!}
    {!! HTML::script('js/scheduler.js') !!}
    {!! HTML::script('plugins/iCheck/icheck.min.js') !!}
    @endif
    <!-- Main Scheduler Ends -->

    <!-- Starts - Delete confirmation message -->
    @if(strpos($currnet_page, 'edit') !== false || strpos($currnet_page, 'notes') !== false || strpos($currnet_page, 'documents') !== false)
    {!! HTML::script('js/jquery_confirm.js') !!}
    <script>
        function getdeleteConformAlert() {
            $(".js-delete-confirm").confirm();
        }
        getdeleteConformAlert();
    </script>
    @endif

    <!-- Common error message start [admin.blade file split] -->
    <!-- Common error message end -->
    {!! HTML::script('js/documents_module.js') !!}
    <!-- Ends - Delete confirmation message -->
    {!! HTML::script('js/stats_list_selection.js') !!}
    <script>
<?php if (Request::segment(1) != 'admin') { ?>
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState == 'visible') {
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

        $(document).on('click', ".practice_change", function () {
            location.reload();
        });

<?php } ?>
    </script>

	<script>
    	$(document).on('change','.auto-generate',function(){
    		$('#search_remember').prop('checked',false);
    	});

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
  	aboutLink:'https://medcubics.com',
    mainColor: '#ffffff'

  };
  </script>
  <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>-->
    <!-- JS block ends -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
</footer>
