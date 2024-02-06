<div class="col-md-12 margin-t-m-15">
   
        

<!-- Sub Menu -->
<?php  $activetab = 'listinsurancefavourites'; 
	$routex = explode('/',Route::current()->uri());
?>
@if(count($routex) > 0 && isset($routex[1]))
    @if($routex[1] == 'medcubicsrole' || @$role->role_type=='Medcubics' || $routex[1] == 'adminpermission')
            <?php $activetab = 'medcubicsrole'; ?>
    @elseif($routex[1] == 'practicerole' || @$role->role_type=='Practice' || $routex[1] == 'setpagepermissions')
    <?php $activetab = 'practicerole'; ?>
    @endif
@endif

    <div class="med-tab nav-tabs-custom  no-bottom">
        <ul class="nav nav-tabs">
            @if($checkpermission->check_adminurl_permission('admin/medcubicsrole') == 1 or @$role->role_type=='Medcubics')
            <li class="@if($activetab == 'medcubicsrole') active @endif"><a href="javascript:void(0)" data-url="{{ url('admin/medcubicsrole') }}" class="js_next_process"> <i class="fa fa-cube i-font-tabs"></i> Medcubics</a></li>            
            @endif
            
            @if($checkpermission->check_adminurl_permission('admin/practicerole') == 1 or @$role->role_type=='Practice')
            <li class="@if($activetab == 'practicerole') active @endif"><a href="javascript:void(0)" data-url="{{ url('admin/practicerole') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}} i-font-tabs"></i> Practice</a></li>
            @endif
        </ul>
    </div>
    
    </div><!-- /.box -->
