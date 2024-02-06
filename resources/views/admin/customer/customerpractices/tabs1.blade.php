<div class="col-md-12 margin-t-m-15 ">
    <div class="box-block">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="text-center">
                   <?php 
                                        $filename = $practice->avatar_name.'.'.$practice->avatar_ext;
                                        $avatar_url = App\Http\Helpers\Helpers::checkAndGetAvatar('practice',$filename);
                                        ?>
                                        {!! HTML::image($avatar_url,null,['class'=>'img-border']) !!}
                </div>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-right-border">
                <h3>{{ $practice->practice_name }}</h3>
                <p class="push">{{ $practice->practice_description }}</p>
                <a href="{{ $practice->practice_link }}" target="blank"><button class="btn btn-medcubics-small" type="button">Know More</button></a>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5">
                <ul class="icons push no-padding">
                    <li><i class="livicon"  data-name="phone" data-animate="false" ></i>{{$practice->phone}}</li>
                    <li><i class="livicon"  data-name="printer" data-animate="false" ></i>{{$practice->fax}}</li>
                    <li><i class="livicon"  data-name="mail" data-animate="false" ></i><a href="mailto:{{$practice->email}}">{{$practice->email}}</a></li>
                    <li><i class="livicon"  data-name="globe" data-animate="false" ></i><a href="{{$practice->practice_link}}" target="_blank">{{$practice->practice_link}}</a></li>
                    <li><i class="livicon"  data-name="facebook" data-animate="false" ></i><a href="{{$practice->facebook}}" target="_blank">{{substr($practice->facebook, 0, 25)}}</a></li>
                    <li><i class="livicon"  data-name="twitter" data-animate="false" ></i><a href="{{$practice->twitter}}" target="_blank">{{$practice->twitter}}</a></li>
                </ul>

            </div>

            
            
        </div><!-- /.box-body -->
		

        <!-- Sub Menu -->		
<?php  $activetab = 'practice_details'; 
        	$routex = explode('.',Route::currentRouteName());
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
    
    <div class="med-tab nav-tabs-custom space20 no-bottom">
        <ul class="nav nav-tabs">
             @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerpractices/{customerpractices}') == 1)
            <li class="@if($selected_tab == 'admin/customerpractices') active @endif"><a href="{{ url('admin/customer/'.$customer_id.'/customerpractices/'.$practice->id) }}" ><i class="livicon" data-name="medkit" style="margin-right: 0px;"></i> Practice Details</a></li>
             @endif
            
             @if($checkpermission->check_adminurl_permission('admin/customer/{customer_id}/practice/{practice_id}/providers') == 1)
            <li class="@if($selected_tab == 'provider_details') active @endif"><a href="{{ url('admin/customer/'.$customer_id.'/practice/'.$practice->id.'/providers') }}" ><i class="livicon" data-name="upload-alt" style="margin-right: 0px;"></i> Providers</a></li>
             @endif
        </ul>
    </div>
</div>