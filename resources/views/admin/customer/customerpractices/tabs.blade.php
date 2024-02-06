<div class="col-md-12 margin-t-m-18">
    <div class="box-block">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                <?php 
                    $filename = @$practice->avatar_name.'.'.@$practice->avatar_ext;
					$unique_practice = md5('P'.$practice_id);
                    $img_details = [];
					$img_details['module_name']='practice';
					$img_details['file_name']=$filename;
					$img_details['practice_name']=$unique_practice;
					
					$img_details['class']='';
					$img_details['alt']='customer-image';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
				{!! $image_tag !!}  
                </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 ">
                <h3>{{ $practice->practice_name }}</h3>
                <p class="push">{{ $practice->practice_description }}</p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 med-left-border">

              <ul class="icons push no-padding">
                  @if($practice->phone != '')<li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Phone </span> <span class="pull-right">{{ $practice->phone }}   <span class="@if($practice->phoneext == '')  @else bg-ext @endif"> {{ $practice->phoneext }}</span>  </span> </li>@endif
                  @if($practice->fax != '')<li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Fax </span> <span class="pull-right">{{ $practice->fax }}</span></li>@endif
                  @if($practice->email != '')<li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">E-mail </span> <span class="pull-right"><a href="mailto:{{$practice->email}}">{{ $practice->email }}</a></span></li>@endif
                <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Website </span>  @if($practice->practice_link != '') <span class="pull-right"><a href="{{$practice->practice_link}}" target="_blank">{{$practice->practice_link}}</a></span>@endif</li>
                  <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Facebook </span>@if($practice->facebook != '') <span class="pull-right"><a href="{{$practice->facebook}}" target="_blank">{{substr($practice->facebook, 0, 25)}}</a></span>@endif</li>
                  <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Twitter </span> @if(@$practice->twitter != '')<span class="pull-right"><a href="{{@$practice->twitter}}" target="_blank">{{@$practice->twitter}}</a></span>@endif</li>
              </ul>
            </div>
		</div><!-- /.box-body -->
 <!-- Sub Menu -->
		<?php  $activetab = 'practice_details';
        	$routex = explode('/',Route::getFacadeRoot()->current()->uri());
        ?>
       	@if($routex[0] == 'practice')
        	<?php $activetab = 'practice_details'; ?>
        @elseif($routex[0] == 'overrides')
        	<?php $activetab = 'overrides'; ?>
        @elseif($routex[0] == 'managecare')
        	<?php $activetab = 'managedcare'; ?>
        @elseif($routex[0] == 'contactdetail')
        	<?php $activetab = 'contact_details'; ?>
        @elseif($routex[0] == 'document')
        	<?php $activetab = 'document'; ?>
        @elseif($routex[0] == 'notes')
        	<?php $activetab = 'notes'; ?>
		@elseif($routex[1] == 'providers')
        	<?php $activetab = 'notes'; ?>
        @endif

 </div><!-- /.box -->

    <div class="med-tab nav-tabs-custom margin-t-10 no-bottom">
        <ul class="nav nav-tabs">
             @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerpractices/{customerpractices}') == 1)
				<li class="@if($selected_tab == 'admin/customerpractices') active @endif"><a href="{{ url('admin/customer/'.$customer_id.'/customerpractices/'.$practice->id) }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}} m-r-0" data-name="medkit"></i> Practice Details</a></li>
             @endif
			@if($checkpermission->check_adminurl_permission('admin/customer/{customer_id}/practice/{practice_id}/providers') == 1)
				<li class="@if($selected_tab == 'provider_details') active @endif"><a href="{{ url('admin/customer/'.$customer_id.'/practice/'.$practice->id.'/providers') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} m-r-0" data-name="user"></i> Providers</a></li>
             @endif
			 @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerusers') == 1)
				<li class="@if($selected_tab == 'users') active @endif"><a href="{{ url('admin/customer/'.$customer_id.'/practice/'.$practice->id.'/users') }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} m-r-0" data-name="user"></i> Users</a></li>
            @endif
        </ul>
    </div>
</div>
<!-- Modal Light Box Address starts -->
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- Modal Light Box Ends -->
