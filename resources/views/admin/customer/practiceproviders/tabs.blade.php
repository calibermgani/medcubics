
    <?php  
	$practice_decode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
	?> 
    
    <div class="col-md-12 margin-t-m-18">
        <div class="box-block">
            <div class="box-body">

                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                    <div class="text-center">
                        <div class="safari_rounded">
                        <?php 
							$filename = @$provider->avatar_name . '.' .@$provider->avatar_ext;
							$unique_practice = md5('P'.$practice_decode_id);
							$img_details = [];
							$img_details['module_name']='provider';
							$img_details['file_name']=$filename;
							$img_details['practice_name']=$unique_practice;
							
							$img_details['class']='';
							$img_details['alt']='provider-image';
							$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
						?>
						{!! $image_tag !!}  
                    </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
                    <h3>{{ $provider->provider_name.' '.@$provider->degrees->degree_name }} <span class="med-orange">{{ $provider->short_name }}</span></h3>                   
                    <p class="push">{{ $provider->description }}</p>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 med-left-border">
                    <ul class="icons push no-padding">
                         <li class="col-lg-12 col-md-12 col-sm-6 col-xs-6"><span class="med-green font600">Phone </span> @if($provider->phone != '') <span class="pull-right">{{ $provider->phone }}   <span class="@if($provider->phoneext == '')  @else bg-ext @endif"> {{ $provider->phoneext }}</span></span>@endif  </li> 
                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-6"><span class="med-green font600">Fax </span> @if($provider->fax != '')<span class="pull-right">{{ $provider->fax }}</span>@endif</li>
                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-6"><span class="med-green font600">E-mail </span> @if($provider->email != '')<span class="pull-right"><a href="mailto:{{ $provider->email }}">{{ $provider->email }}</a></span>@endif</li>
                        <li class="col-lg-12 col-md-12 col-sm-6 col-xs-6"><span class="med-green font600">Website </span> @if(trim($provider->website)!='')<span class="pull-right"><a href="{{ $provider->website}}" target="_blank">{{ $provider->website}}</a></span>@endif</li>
                    </ul>
                </div>



            </div><!-- /.box-body -->

            <!-- Sub Menu -->
            <?php  $activetab = 'provider_details'; 
            $routex = explode('.',Route::currentRouteName());
            ?>
            @if(count($routex) > 2)
            @if($routex[2] == 'provideroverrides')
            <?php $activetab = 'overrides'; ?>
            @elseif($routex[2] == 'providermanagecare')
            <?php $activetab = 'managedcare'; ?>
            @elseif($routex[2] == 'providerdocuments')
            <?php $activetab = 'document'; ?>
            @elseif($routex[2] == 'notes')
            <?php $activetab = 'notes'; ?>
            @endif
            @endif

            

        </div><!-- /.box -->
        
       
        
        <div class="med-tab nav-tabs-custom margin-t-10 no-bottom">
            <ul class="nav nav-tabs">
                <li class="@if($activetab == 'provider_details') active @endif"><a href="#" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} m-r-0" data-name="user"></i> Provider Details</a></li>
            </ul>
        </div>
        
    </div>