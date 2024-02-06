
<div class="col-md-12 margin-t-m-18">
    <div class="box-block">               
        <div class="box-body">
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">             
                    <div class="safari_rounded">
                        <?php
						$filename =@$customer->avatar_name . '.' .@$customer->avatar_ext;
						$img_details = [];
						$img_details['module_name']='customers';
						$img_details['file_name']=$filename;
						$img_details['practice_name']='admin';
						
						$img_details['class']='';
						$img_details['alt']='customer-image';
						$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
						?>
                        {!! $image_tag !!} 
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
                <h3>{{ $customer->customer_name }}<span class="med-orange">{{ ' '.$customer->short_name }} </span></h3>
                <p class="push">{{ $customer->customer_desc }}</p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 med-left-border">
                <ul class="icons push no-padding">
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Phone </span>@if($customer->phone != '') <span class="pull-right">{{ $customer->phone }}   <span class="@if($customer->phoneext == '')  @else bg-ext @endif"> {{ $customer->phoneext }}</span></span> @else <span class="nill pull-right">- Nil - </span>  @endif </li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Mobile </span> @if($customer->mobile != '')<span class="pull-right">{{ $customer->mobile }}</span> @else <span class="pull-right">- Nil - </span> @endif</li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Fax </span>@if($customer->fax != '') <span class="pull-right">{{ $customer->fax }}</span> @else <span class="pull-right">- Nil - </span> @endif</li>
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">E-mail </span> @if($customer->email != '') <span class="pull-right"><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></span> @else <span class="nill pull-right">- Nil - </span> @endif</li>
                </ul>
            </div>
        </div><!-- /.box-body -->
        
        <!-- Sub Menu -->
        <?php $activetab = 'admin/customer'; 
			$routex = explode('.',Route::currentRouteName());
			$currnet_page = Route::getFacadeRoot()->current()->uri();
            if(isset($customer->encid) && $customer->encid != ''){
                $customer->id = $customer->encid;
            }else{
                $customer->id = $customer->id;
            }
        ?>

        @if($currnet_page == 'admin/practice')
            <?php$activetab = 'admin/practice'; ?>
        @elseif(count($routex) > 1)
            @if($routex[0] == 'admin/customerusers')
                <?php$activetab = 'admin/customerusers'; ?>
            @elseif($routex[0] == 'admin/customernotes')
                <?php$activetab = 'admin/customernotes'; ?>
            @endif
        @endif
    </div>    
	
	@if($tabs)
    <div class="med-tab nav-tabs-custom margin-t-10 no-bottom">
        <ul class="nav nav-tabs">
            
            @if($checkpermission->check_adminurl_permission('admin/customer/{customer}') == 1)
				<li class="@if($selected_tab == 'customer') active @endif"><a href="{{ url('admin/customer/'.$customer->id) }}" ><i class="fa {{Config::get('cssconfigs.admin.users')}} m-r-0" data-name="users"></i> Customer Details</a></li>
            @endif
            
            @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerpractices') == 1)
				<li class="@if($selected_tab == 'admin/customerpractices') active @endif"><a href="{{ url('admin/customer/'.$customer->id.'/customerpractices') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}} m-r-0" data-name="medkit"></i> Practice</a></li>
            @endif
            
            @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerusers') == 1)
				<li class="@if($selected_tab == 'admin/customerusers') active @endif"><a href="{{ url('admin/customer/'.$customer->id.'/customerusers') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} m-r-0" data-name="user"></i> User</a></li>
            @endif
            
            @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customernotes') == 1)
				<li class="@if($selected_tab == 'admin/customernotes') active @endif"><a href="{{ url('admin/customer/'.$customer->id.'/customernotes') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}} m-r-0" data-name="notebook"></i> Notes</a></li>            
            @endif
        </ul>
    </div>	
	@endif    
</div>

