<div class="col-md-12 ">
    <div class="box-block">
        <div class="box-body">
<?php
if($insurance->favourite == null)
$text = "Add to favourite";
else
$text = "Remove from favourite";
 ?>
            <div class="col-md-2 hidden-sm">
                <div class="text-center">
                    <?php
                    $filename = $insurance->avatar_name . '.' . $insurance->avatar_ext;
                    $avatar_url = App\Http\Helpers\Helpers::checkAndGetAvatar('insurance', $filename);
                    ?>
                    {!! HTML::image($avatar_url,null,['class'=>'img-border']) !!} 
                </div>
            </div>
            <div class="col-md-4" >
                <h3>{{ $insurance->insurance_name }} <a href="javascript:void(0);" class="js-favourite-record fav_button" data-id="{{$insurance->id}}" data-url='{{url('medcubics-admin/toggleinsurancefavourites/'.$insurance->id)}}'> <i class="fa @if($insurance->favourite) fa-star @else  fa-star-o  @endif tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="{{$text}}"></i></a></h3>
                <p class="push"><span  class="med-green">Address Line 1  : </span>{{ $insurance->address_1 }}</p>
                <p class="push"><span  class="med-green">City : </span>{{ $insurance->city }} <span class="med-green">State: </span>{{ $insurance->state }}</p>
            </div>
            
            <div class="col-md-3" style="border-right:1px solid #ccc;">
                <h3>&emsp;</h3>
                <p class="push"><span  class="med-green">Address Line 2 : </span>{{ $insurance->address_2 }}</p>
               
                <p class="push"><span  class="med-green"> Zip Code :</span> {{ $insurance->zipcode5 }} - {{ $insurance->zipcode4 }}
                    <span class="js-address-success @if($address_flag['general']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-tab"></i></a></span>	
                    <span class="js-address-error @if($address_flag['general']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-tab"></i></a></span>
                </p>
            </div>

            <div class="col-md-3">
                <ul class="icons push">
                    <li><span class="med-green font600">Phone </span> <span class="pull-right">{{ $insurance ->telephone }}</span></li>
                    <li><span class="med-green font600">Fax </span> <span class="pull-right">{{ $insurance ->fax }}</span></li>
                    @if($insurance ->email != '')<li><span class="med-green font600">E-mail </span> <span class="pull-right"><a href="mailto:{{ $insurance ->email }}">{{ $insurance ->email }}</a></span></li>@endif
                    @if($insurance ->website != '')<li><span class="med-green font600">Website </span> <span class="pull-right"><a href="{{$insurance ->website}}" target="_blank">{{ $insurance ->website }}</a></span></li>@endif

                </ul>

            </div>

        </div><!-- /.box-body -->

        <!--Sub Menu-->

        <?php $activetab = 'insurance'; 
	$routex = explode('.',Route::currentRouteName());
?>

        @if(count($routex) > 3)
        @if($routex[3] == 'insuranceoverrides')
        <?php$activetab = 'overrides'; ?>
        @endif
        @endif
        

   </div>
    
    <div class="med-tab nav-tabs-custom margin-t-10 no-bottom">
        <ul class="nav nav-tabs">
		<?php $insurance->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurance->id,'encode'); ?>
            <li class="@if($activetab == 'insurance') active @endif"><a href="{{ url('medcubics-admin/insurance/'.$insurance->id) }}" ><i class="fa {{Config::get('cssconfigs.common.insurance')}}" data-name="bank" style="margin-right: 0px;"></i> Insurance Details</a></li>            
            <li class="@if($activetab == 'overrides') active @endif"><a href="{{ url('medcubics-admin/insurance/'.$insurance->id.'/insuranceoverrides') }}"><i class="fa {{Config::get('cssconfigs.common.ins_overrides')}}" data-name="user" style="margin-right: 0px;"></i> Overrides</a></li>           
            <li class=""><a href="javascript:void(0);" ><i class="livicon" data-name="notebook" style="margin-right: 0px;"></i> Others</a></li>           
        </ul>
    </div>
    
</div>
<!--End Sub Menu-->



<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">USPS Address Details</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-list" id="modal_show_success_message" @if($address_flag['general']['is_address_match'] != 'Yes') class="hide" @endif>
                    <li class="nav-header">Address : <span id="modal_address2">{{$address_flag['general']['address1']}}</span></li>
                    <li class="nav-header">City : <span id="modal_city">{{$address_flag['general']['city']}}</span></li>
                    <li class="nav-header">State : <span id="modal_state">{{$address_flag['general']['state']}}</span></li>
                    <li class="nav-header">Zipcode : <span >{{$address_flag['general']['zip5']}}-{{$address_flag['general']['zip4']}}</span></li>
                    
                </ul> 
                <p id="modal_show_error_message" @if($address_flag['general']['is_address_match'] != 'No') class="hide" @endif>{{$address_flag['general']['error_message']}}</p>							
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->  

