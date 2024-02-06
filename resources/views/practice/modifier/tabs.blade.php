<div class="col-md-12 space-m-t-15 print-m-t-30">
    
        

      <?php	$activetab = 'modifierlevel1'; 
				$routex = explode('.',Route::currentRouteName());
		?>
    @if(count($routex) > 0)
    @if($routex[0] == 'modifierlevel2')
    <?php $activetab = 'modifierlevel2'; ?>
    @endif
    @endif
    
    <div class="med-tab nav-tabs-custom space10 no-bottom">
         <ul class="nav nav-tabs">
            @if($checkpermission->check_url_permission('modifierlevel1') == 1)
            <li class="@if($activetab == 'modifierlevel1') active @endif"><a href="javascript:void(0)" data-url="{{ url('modifierlevel1') }}" class="js_next_process" ><i class="fa fa-bars i-font-tabs"></i> Level I</a></li>      
            @endif
            @if($checkpermission->check_url_permission('modifierlevel2') == 1)	
            <li class="@if($activetab == 'modifierlevel2') active @endif"><a href="javascript:void(0)" data-url="{{ url('modifierlevel2') }}" class="js_next_process"><i class="fa fa-bars i-font-tabs"></i> Level II</a></li>        
            @endif		
        </ul>
    </div>
</div>