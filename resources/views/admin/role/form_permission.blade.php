<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="space20"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="briefcase"></i> <h3 class="box-title">Set Admin Page Permissions</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <?php  $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                <div class="box-body form-horizontal js-address-class" id="js-address-general-address">
                    <div class="form-group">
                        {!! Form::label('Role', 'Role', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12']) !!}
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10 @if($errors->first('role_id')) error @endif">
                            <b class="med-orange">{!! $roles->role_name !!}</b>
                            {!! Form::hidden('role_id',$roles->id,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>
                    <div id="permissions_forusers">
                        <?php
                        $setpage_permissions_arr = array();
                        if ($setadminpagepermissions) {
                            $setpage_permissions_arr = explode(",", $setadminpagepermissions->page_permission_id);
                        }
                        ?>
                        {!! Form::checkbox('select_all', false, false, ["class" => "js_menu flat-red",'id'=>'js_select_all']) !!}
                        Select All
                        @foreach($menus as $menu)
                        <?php  $decode_id = $menu->id;
					 $menu->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($menu->id,'encode'); ?>		
                        <div data-id="js_select_all" class="ad-user-list js-check-each">
                            <div class="ad-user-menu-bg">{!! Form::checkbox('permission_menu', $menu->id, false, ["class" => "js_select_all js_menu js_submenu flat-red",'id'=>"js_".$menu->menu."_".$decode_id]) !!} {{ $menu->menu }}</div>
                            <?php
								$sub_menu_arr = App\Models\Medcubics\AdminPagePermissions::getAdminMenusList('menu', $menu->menu);
                            ?>
                            @foreach($sub_menu_arr as $submenu)
								<?php  
									$submenu->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($submenu->id,'encode'); ?>
                            <div data-id="js_{{$menu->menu}}_{{$decode_id}}" class= 'js-select-sub-list-{{ $decode_id }} js-check-{{ $menu->menu }}'>
                                <div class="ad-submenu">
                                    <span class="col-lg-2 bg-role-submenu">{{ $submenu->submenu }}</span>
                                </div>
                                <?php  
									$title_arr = App\Models\Medcubics\AdminPagePermissions::getAdminMenusList('submenu',$submenu->submenu,$menu->menu);
								?>
								@foreach($title_arr as $title_list)
									<?php  
										$title_name = $menu->menu . '_' . $submenu->submenu . '_' . $title_list->title;
									?>
									<span class="ad-checkbox-color">{!! Form::checkbox($title_name, $title_list->title, (in_array($title_list->id,$setpage_permissions_arr)?true:null), ["class" =>"js_".$menu->menu."_".$decode_id." js_submenu flat-red"]) !!}<span class='margin-l-4'>{{ $title_list->title }}</span></span>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>

				</div><!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
                        {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group']) !!}
                        <a href="{{ url('admin/medcubicsrole/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>
                    </div>
                </div>
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
    </div>
</div>
