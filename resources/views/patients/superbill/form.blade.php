{!! Form::open(['url'=>'patients/superbill/store','id'=>'js-bootstrap-validator','name'=>'superbillclaim_form','class'=>'superbillclaim_form medcubicsform']) !!}
{!! Form::hidden('patient_id',$patient_id,['class'=>'form-control input-sm','id'=>'patient_id']) !!}

<div class="col-md-12 margin-t-m-10">
    
    
    <?php $id = Route::current()->parameters['id']; ?>
     <!-- Sub Menu -->
        <?php $activetab = 'create_superbill'; 
        	$routex = explode('.',Route::currentRouteName());
        ?>
      

    <div class="med-tab nav-tabs-custom no-bottom margin-t-m-3">
        <ul class="nav nav-tabs">
           
            <li class="@if($activetab == 'charges_list') active @endif"><a href="{{ url('patients/'.$patient_id.'/billing') }}" ><i class="fa fa-bars i-font-tabs"></i> List</a></li>           	                      	           
            <li class="@if($activetab == 'create_superbill') active @endif"><a href="{{ url('patients/'.$patient_id.'/superbill/create') }}" ><i class="fa {{Config::get('cssconfigs.patient.superbill')}} i-font-tabs"></i> Create E-Superbill</a></li>
           
        </ul>
    </div>
    
    <div class="med-tab1 margin-t-15">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                
                <li class="active"><a href="{{ url('patients/'.$id.'/superbill/create') }}"><i class="fa {{Config::get('cssconfigs.patient.superbill')}}"></i> Superbill List</a></li>
                <li><a id="select_icd_tab" href="#select_icd" data-toggle="tab"><i class="fa {{Config::get('cssconfigs.common.icd')}}"></i> Select ICD</a></li>
                <li><a id="select_procedure_tab" class="inactivelink" href="#select_procedure" data-toggle="tab"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}}"></i> Select Procedure</a></li>
                <li><a id="create_claim_tab" class="inactivelink" href="#create_claim" data-toggle="tab"><i class="fa {{Config::get('cssconfigs.patient.file')}}" data-name="folders"></i> Create Bill</a></li>

            </ul>
            <div class="tab-content">

                @include ('patients/superbill/superbill_list_tab')
                @if($checkpermission->check_url_permission('patients/{id}/superbills/add') == 1)
                @include ('patients/superbill/select_icd_tab')  

                @include ('patients/superbill/select_procedure_tab')

                @include ('patients/superbill/create_claim_tab')
                @endif

            </div><!-- /.tab-pane -->
        </div><!-- /.tab-content -->
    </div><!-- /.nav-tabs-custom -->
</div>

{!! Form::close() !!}