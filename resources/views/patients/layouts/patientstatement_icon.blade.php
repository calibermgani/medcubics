@if(Config::get('siteconfigs.patient.statement_icon') == true)
<?php $get_statementsettings = App\Http\Controllers\Api\PatientindividualstatementApiController::checkPatientClaimInfo($uniquepatientid);   ?>
	@if($get_statementsettings == 1)
		<li><a href="javascript:void(0);" data-patientid="{{ $uniquepatientid }}" class="js-patientstatement"><i class="fa fa-file font16" data-placement="bottom"  data-toggle="tooltip" data-original-title="Patient Statement"></i></a></li>
	@endif	
@endif