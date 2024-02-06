<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-15"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
            <div class="box box-info no-shadow">
                <div class="box-block-header with-border">
                    <h3 class="box-title"><i class="livicon" data-name="address-book" data-color='#008e97' data-size='16'></i> Set Permission for User</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body form-horizontal margin-t-10">

                    <div class=" js-address-class" id="js-address-general-address">

                        <div class="form-group">
                            <?php  $currnet_page = Route::getFacadeRoot()->current()->getActionName();
								$active = '';?>
                            @if(strpos($currnet_page, 'edit') !== false)
                            <?php $active="disabled"; ?>
                            @endif
                            {!! Form::label('Practices', 'Practice', ['class'=>'col-lg-1 col-md-1 col-sm-2 col-xs-12 control-label']) !!}
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10 @if($errors->first('role_id')) error @endif">
                                {!! Form::select('practice_id', array(''=>'-- Select --')+(array)$practices, $practice_id,[$active,'class'=>'select2 form-control js_set_api']) !!}
                                {!! $errors->first('practice_id', '<p> :message</p>')  !!}
                            </div>
                            <div class="col-sm-1 col-xs-2"></div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('Roles', 'Roles', ['class'=>'col-lg-1 col-md-1 col-sm-2 col-xs-12 control-label']) !!}
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10 @if($errors->first('role_id')) error @endif">
                                {!! Form::select('role_id', array(''=>'-- Select --')+(array)$roles, $role_id,['class'=>'select2 form-control','id'=>'js_get_roles_permissions']) !!}
                                {!! $errors->first('role_id', '<p> :message</p>')  !!}
                            </div>
                            <div class="col-sm-1 col-xs-2"></div>
                        </div>

                        <div id="permissions_forusers">
                            <?php $prev_menu = ""; ?>
                            <?php $prev_submenu = ""; ?>
                            <?php
                            $setpage_permissions_arr = array();
                            if (@$practice_permissions->page_permission_ids != '') {
                                if (strpos($currnet_page, 'edit') !== false)
                                    $setpage_permissions_arr = explode(",", $practice_permissions->page_permission_ids);
                            }
                            ?>
                            {!! Form::checkbox('select_all', false, false, ["class" => "js_menu flat-red",'id'=>'js_select_all']) !!}
                            Select All
                             @foreach($modules as $module)
                            <?php $menu_class = str_replace(" ","_",$module->menu);?>
                            <div data-id="js_practice_module" class="ad-user-list">
                            <div class="ad-user-menu-bg">{!! Form::checkbox('permission_module', $module->id, false, ['id'=>"js_practice_mainmodule","class" => "flat-red",'data_class' => 'js_practice_module_'.$menu_class]) !!} {{ $module->module }}</div>
                             <?php                                    
                                    $menus   = App\Models\Medcubics\PagePermissions::getModuleMenusList($module->module);
                              ?>  
                            @foreach($menus as $menu)
                            <?php  $decode_id = $menu->id; 
                             $submenu_class =  str_replace(" ","_",$menu->menu);?>
                            <div data-id="js_select_all" class="ad-user-list js-check-each js_practice_module_{{$menu_class}}">
                                <div class="ad-user-menu-bg">{!! Form::checkbox('permission_menu', $menu->id,  false, ["class" => "js_select_all js_menu js_submenu flat-red",'id'=>"js_".$submenu_class."_".$decode_id,'data-class' => "js_practice_module_".$menu_class]) !!} {{ $menu->menu }}</div>

                                <?php 
                            $sub_menu_arr = App\Models\Medcubics\PagePermissions::getMenusList('menu',$menu->menu, '', $module->module);
							$menu_count = 1;
                            ?>

                                @foreach($sub_menu_arr as $submenu)
                                <div data-id="js_{{$submenu_class}}_{{$decode_id}}" class= 'js-select-sub-list-{{ $decode_id }} js-check-{{ $submenu_class }}'>
                                    <div class="ad-submenu">
                                        <span class="col-lg-2 bg-role-submenu">{{ $submenu->submenu }}</span>
                                    </div>
                                    <?php 
								$title_arr = App\Models\Medcubics\PagePermissions::getMenusList('submenu',$submenu->submenu,$menu->menu,$module->module);
							?>
                                    @foreach($title_arr as $title_list)
                                    <?php  //$title_name = $menu->menu . '_' . $submenu->submenu . '_' . $title_list->title; 
                                    $title_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($title_list->id,'encode');
                                          $title_name = $menu->menu . '_' . $submenu->submenu . '_' . $title_list->title.'|'.$title_id;?>
                                    <span class="ad-checkbox-color">{!! Form::checkbox($title_name, $title_list->title, (in_array($title_list->id,$setpage_permissions_arr)?true:null), ["class" => "js_".$submenu_class."_".$decode_id." js_submenu flat-red"]) !!}<span class='margin-l-4'>{{ $title_list->title }}</span></span>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                             </div>
                        @endforeach
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->

            <div class="js_api_settings">
                @if(strpos($currnet_page, 'edit') !== false)

                <?php  $selectedpracticeapis = explode(",",$practice->api_ids)  ?>
                <div class="box box-info no-shadow">
                    <div class="box-block-header with-border">
                        <h3 class="box-title"><i class="livicon" data-name="address-book" data-color='#008e97' data-size='16'></i> API Settings</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div><!-- /.box-header -->
                    @if($practice->api_ids == '') 
                    <div class="box-body  form-horizontal"><!-- Box Body Starts -->
                        No API settings available
                    </div>
                    @else
                    <div class="box-body form-horizontal">
                        <div class=" js-address-class" id="js-address-general-address">
                            <div class="form-group">
                                {!! Form::label('Select API', 'Select API', ['class'=>'col-lg-1 col-md-1 col-sm-2 col-xs-12 control-label']) !!}
                                <div class="col-lg-10 col-md-12 col-sm-12 col-xs-12 @if($errors->first('role_id')) error @endif">

                                    <?php  $selecteduserapis 	 = explode(",",$Setapiforusers->api); ?>

                                    @foreach($apilist as $api_key=>$api_value)
                                    @if(in_array($api_key,$selectedpracticeapis))

                                    @if(@$apilist_subcat->$api_value) 
                                    <span class='margin-l-4 med-green'>{{ ucwords($api_value) }}</span>	

                                    <span data-id="js_{{$api_value}}_{{$api_key}}" >(
                                        @foreach($apilist_subcat->$api_value as $sub_api_key=>$sub_api_value)
                                        @if(in_array($sub_api_key,$getActivePracticeAPI))	
                                        <span>{!! Form::checkbox('apilist[]', $sub_api_key,(in_array($sub_api_key,$selecteduserapis))?true:false, ["class" => "flat-red js-subselect", 'id' =>'apilist']) !!}<span class='margin-l-4 med-green'>{{ @$api_name[$sub_api_key] }}</span></span>
                                        @endif	
                                        @endforeach
                                        )
                                    </span>
                                    @else
                                    @if(in_array($api_key,$getActivePracticeAPI))			
                                    <span class="ad-checkbox-color">{!! Form::checkbox('apilist[]', $api_key,(in_array($api_key,$selecteduserapis))?true:false, ["class" => "flat-red js-subselect", 'id' =>'apilist']) !!}<span class='margin-l-4'>{{ @$api_name[$api_key] }}</span></span>
                                    @endif	
                                    @endif

                                    @endif
                                    @endforeach
                                </div>
                                <div class="col-sm-1 col-xs-2"></div>
                            </div>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                    @endif	
                </div><!--/.col (left) -->
                @endif
            </div>

            <div class="box-footer">
                <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12">
                    {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}

                    <?php  $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>

                    {!! Form::button('Cancel', ['class'=>'btn btn-medcubics','onclick' => 'history.back(-1)']) !!}
                </div>
            </div><!-- /.box-footer -->

        </div>
    </div>
</div>

@push('view.scripts')
<script type="text/javascript">

    $(function () {
        $(document).ready(function () {
            $('#js-bootstrap-validator')
                    .bootstrapValidator({
                        message: 'This value is not valid',
                        excluded: ':disabled',
                        feedbackIcons: {
                            valid: 'glyphicon glyphicon-ok',
                            invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                        },
                        fields: {
                            practice_id: {
                                message: 'Select practice',
                                validators: {
                                    notEmpty: {
                                        message: 'Select practice'
                                    }
                                }
                            },
                        }
                    });
        });
    });
</script>
@endpush
