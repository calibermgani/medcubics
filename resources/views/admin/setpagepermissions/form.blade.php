<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="space20"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->

            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Set Page Permissions for {{$role_name}}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                @php $currnet_page = Route::getFacadeRoot()->current()->uri(); @endphp
				<div class="box-body form-horizontal js-address-class" id="js-address-general-address">
                    <div id="permissions_forusers">
                       <?php
                        $setpage_permissions_arr = array();
                        if(@$setpagepermissions->page_permission_id != '') {
                            if (strpos($currnet_page, 'edit') !== false)
                                $setpage_permissions_arr = explode(",", $setpagepermissions->page_permission_id);
                        }
						?>
                        {!! Form::checkbox('select_all', false, false, ["class" => "js_menu flat-red",'id'=>'js_select_all','data-class' => 'js_select_all']) !!}
                        Select All
                        @foreach($modules as $module)
                        <?php $menu_class = str_replace(" ","_",$module->menu);?>
                        <div data-id="js_practice_module" class="ad-user-list">
                            <div class="ad-user-menu-bg">{!! Form::checkbox('permission_module', $module->id, false, ['id'=>"js_practice_mainmodule","class" => "flat-red",'data_class' => 'js_practice_module_'.$menu_class]) !!} {{ $module->module }}</div>
                             @php                                   
                                    $menus   = App\Models\Medcubics\PagePermissions::getModuleMenusList($module->module);
                              @endphp    
                            @foreach($menus as $menu)
    						  @php 
    								$decode_id 		= $menu->id;
    								$menu->id 		= App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($menu->id,'encode'); 
    								$sub_menu_arr 	= App\Models\Medcubics\PagePermissions::getMenusList('menu',$menu->menu, '', $module->module);
                                    $submenu_class =  str_replace(" ","_",$menu->menu);
    						  @endphp	
                            <div data-id="js_select_all" class="ad-user-list js-check-each js_practice_module_{{$menu_class}}">
                                <div class="ad-user-menu-bg">{!! Form::checkbox('permission_menu', $menu->id, false, ["class" => "js_select_all  js_menu js_submenu flat-red",'id'=>"js_".$submenu_class."_".$decode_id, 'data-class' => "js_practice_module_".$menu_class]) !!} {{ $menu->menu }}</div>
                                @foreach($sub_menu_arr as $submenu)
        							@php 
        							$submenu->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($submenu->id,'encode');
                                    @endphp
        							<div data-id="js_{{$submenu_class}}_{{$decode_id}}" class= 'js-select-sub-list-{{ $decode_id }} js-check-{{ $submenu_class }}'>
        								<div class="ad-submenu">
        									<span class="col-lg-2 bg-role-submenu">{{ $submenu->submenu }}</span>
        								</div>
        								@php 
        								$title_arr = App\Models\Medcubics\PagePermissions::getMenusList('submenu',$submenu->submenu,$menu->menu,$module->module);
        								@endphp
        								@foreach($title_arr as $title_list)
        									@php 
        									//$title_name = $menu->menu . '_' . $submenu->submenu . '_' . $title_list->title;
                                              $title_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($title_list->id,'encode');
                                              $title_name = $menu->menu . '_' . $submenu->submenu . '_' . $title_list->title.'|'.$title_id;
        									@endphp
        									<span class="ad-checkbox-color">{!! Form::checkbox($title_name, $title_list->title, (in_array($title_list->id,$setpage_permissions_arr)?true:null), ["class" =>"js_".$submenu_class."_".$decode_id." js_submenu  flat-red"]) !!}<span class='margin-l-4'>{{ $title_list->title }}</span></span>
        								@endforeach
        							</div>
    							@endforeach
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer transparent">
                    <div class="col-lg-12 col-md-9 col-sm-10 col-xs-12">
                        {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}                        
						<a href="{{url('admin/practicerole')}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!} </a>
                    </div>
                </div>
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
    </div>
</div>