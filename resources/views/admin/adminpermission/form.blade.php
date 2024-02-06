<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="space20"></div>
        <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->

            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Set Admin Page Permissions</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
				<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                <div class="box-body form-horizontal js-address-class" id="js-address-general-address">                
                
                    <div class="form-group">
                        {!! Form::label('Role', 'Role', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('role_id')) error @endif">
                            <b>{!! $roles->role_name !!}</b>  
                             {!! Form::hidden('role_id',$roles->id,['class'=>'form-control']) !!} 
                            
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div> 
					<div id="permissions_forusers">
					<?php $prev_menu = "";?>
					<?php $prev_submenu = "";?>
					<?php 
					$setpage_permissions_arr = array();					
					if($setadminpagepermissions){
						$setpage_permissions_arr = explode(",",$setadminpagepermissions->page_permission_id); 												
					} 
					?>
					@foreach($adminpagepermissions as $pagepermission)						
						@if($prev_menu != $pagepermission->menu)						
                                                <div><b>{{ $pagepermission->menu }}</b></div>
						@endif
						@if($pagepermission->menu != $pagepermission->submenu && $prev_submenu != $pagepermission->submenu)						
							<div>{{ $pagepermission->submenu }}</div>
						@endif						
						<?php 
							$title_name = $pagepermission->menu.'_'.$pagepermission->submenu.'_'.$pagepermission->title;						
						?>	
						@if(in_array($pagepermission->id,$setpage_permissions_arr))
							{!! Form::checkbox($title_name, $pagepermission->title, true, ["class" => "flat_red"]) !!}  
						@else
							{!! Form::checkbox($title_name, $pagepermission->title, null, ["class" => "flat_red"]) !!}  
						@endif
						{{ $pagepermission->title }}
						<?php 
							$prev_menu = $pagepermission->menu; 
							$prev_submenu = $pagepermission->submenu;						
						?>
					@endforeach
					</div>
					
					
                </div><!-- /.box-body -->
				<div class="box-footer">
                    <div class="col-lg-6 col-md-9 col-sm-10 col-xs-12">
                        {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}                        
                        <a href="javascript:void(0)" data-url="{{ url('admin/adminpermission/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
                    </div>
                </div>
            </div><!-- /.box Ends-->            
        </div><!--  Left side Content Ends -->  
	</div>
</div>








