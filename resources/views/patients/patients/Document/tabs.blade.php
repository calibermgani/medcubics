<div class="col-md-12 margin-t-m-18">
    <!-- Sub Menu -->

    <?php $routex = explode('/',Route::getFacadeRoot()->current()->uri()); ?>

    @if(count($routex) > 0)
    @if($routex[2] == 'documents')
    <?php $activetab = 'list'; ?>
    @elseif($routex[2] == 'documentsummary')
    <?php $activetab = 'documentsummary'; ?>
    @elseif($routex[2] == 'eligibilitytemplate' || $routex[2] == 'eligibility')
    <?php $activetab = 'eligibilitytemplate'; ?>
    @elseif($routex[2] == 'correspondence')
    <?php $activetab = 'correspondence'; ?>
    @endif
    @endif
    <div class="med-tab nav-tabs-custom space10">
        <ul class="nav nav-tabs">

            <li class="@if($activetab == 'documentsummary') active @endif"><a href="{{ url('patients/'.@$id.'/documentsummary') }}"><i class="fa {{Config::get('cssconfigs.patient.file_text')}} i-font-tabs"></i> Summary</a></li>                      
            <li class="@if($activetab == 'list') active @endif"><a href="{{ url('patients/'.@$id.'/documents') }}" accesskey="c"><i class="fa {{Config::get('cssconfigs.common.search')}} i-font-tabs"></i> Sear<span class="text-underline">c</span>h Documents</a></li> <!-- For temporary we removed the link -->                                               
           
        </ul>
    </div>

</div>