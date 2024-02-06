<div class="col-md-12 margin-t-m-15 no-print">
    <?php $activetab = $selected_tab; ?>
    <div class="med-tab nav-tabs-custom no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'questionnaire/template') active @endif"><a href="{{ url('questionnaire/template') }}" ><i class="fa fa-list i-font-tabs"></i> Questionnaires</a></li>
			
            <li class="@if($activetab == 'questionnaires') active @endif"><a href="{{ url('questionnaires') }}" ><i class="fa fa-list-alt i-font-tabs"></i> Set Questionnaires</a></li>
			@if($checkpermission->check_url_permission('apptemplate') == 1)
				<li class="@if($activetab == 'apptemplate') active @endif"><a href="{{ url('apptemplate') }}" class="js_next_process"><i class="fa fa-bars i-font-tabs i-font-tabs"></i> App Template</a></li> 
			@endif	
        </ul>
    </div>
</div>  
