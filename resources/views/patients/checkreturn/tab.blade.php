<div class="med-tab nav-tabs-custom space10 no-bottom">
    <ul class="nav nav-tabs">
        <li class="@if($activetab == 'wallet history') active @endif"><a href="{{ url('patients/'.$patient_id.'/patientpayment') }}" ><i class="fa fa-navicon i-font-tabs"></i> List</a></li>
        <!--<li class="@if($activetab == 'return check') active @endif"><a href="{{ url('patients/'.$patient_id.'/returncheck') }}" ><i class="fa fa-navicon i-font-tabs"></i> Return Check</a></li>
        <li class="@if($activetab == 'budget plan') active @endif"><a href="{{ url('patients/'.$patient_id.'/budgetplan') }}" ><i class="fa fa-navicon i-font-tabs"></i> Budget Plan</a></li>   -->     
    </ul>
</div>