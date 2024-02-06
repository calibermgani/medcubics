<div class="col-md-12 margin-t-m-18 print-m-t-30">
    <div class="box-block">
		<!-- /.box-body Starts-->
        <div class="box-body">
			<?php $text = ($cpt->favourite == null) ? "Add to favorite":"Remove from favorite"; ?>
            <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                <div class="text-center med-circle">
                    {{$cpt->cpt_hcpcs}}  
                </div>
            </div>
               
            
            <div class="col-lg-7 col-md-7 col-sm-9 col-xs-12">                               
                <div class="form-group">				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                       <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
                        <span class="med-green font600">CPT / HCPCS</span>  
                       </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            <span class="med-orange">{!! $cpt->cpt_hcpcs !!} <a href="javascript:void(0);" class="js-favourite-record" data-id="{{$cpt->id}}" data-url='{{url("togglecptfavourites/".$cpt->id)}}'> <i class="fav_button fa @if($cpt->favourite) fa-star @else  fa-star-o  @endif font16" data-placement="bottom" data-toggle="tooltip" data-original-title="{{$text}}"></i></a></span>   
                        </div>
                    </div>
                </div>
                
                <div class="form-group">				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-8">
                       <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
                        <span class="med-green font600">Medium Description</span>  
                       </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            {{substr($cpt->medium_description, 0, 230)}}
                        </div>
                    </div>
                </div>
               
                <div class="form-group">				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-8">
                        <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
                            <span class="med-green font600">Medicare Global Period</span>  
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            {!! $cpt->medicare_global_period !!}
                        </div>
                    </div>
                </div>
            </div>            
                        
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 med-left-border">
				
                <ul class="icons push no-padding">
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Code Category </span>  @if($cpt->code_type != '')<span class="pull-right">{{$cpt->code_type}} </span> @endif </li>                   
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">ICD </span>@if($cpt->icd != '') <span class="pull-right">{{$cpt->icd}} </span> @endif </li>
                  <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Effective Date </span> @if($cpt->effectivedate != '0000-00-00' &&$cpt->effectivedate != '') <span class="pull-right"><span class="bg-date">{{ ($cpt->effectivedate != '0000-00-00') ? App\Http\Helpers\Helpers::dateFormat($cpt->effectivedate,'date') : ''}}</span> </span> @endif </li>
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Inactive Date </span> @if($cpt->terminationdate != '0000-00-00'&&$cpt->terminationdate != '')<span class="pull-right"><span class="bg-date">{{ ($cpt->terminationdate != '0000-00-00') ?  App\Http\Helpers\Helpers::dateFormat($cpt->terminationdate,'date') : '' }} </span></span> @endif  </li>                  
                </ul>                            
            </div>
			
        </div>
		<!-- /.box-body Ends-->
    </div>
        <?php 
			$activetab = 'listfavourites'; 
			$routex = explode('.',Route::currentRouteName());
            if(count((array)$routex) > 0){
                if($routex[0] == 'cpt'){
                    $activetab = 'cpt';
                } elseif($routex == 'searchcpt') {
                    $activetab = 'searchcpt';
                }                
            }
        ?>
        
    <div class="med-tab nav-tabs-custom space10 no-bottom">
		<ul class="nav nav-tabs">
			<li class="@if($activetab == 'listfavourites') active @endif"><a href="{{ url('listfavourites') }}" ><i class="fa {{Config::get('cssconfigs.common.favourites')}} i-font-tabs"></i> Favorites</a></li>            
			<li class="@if($activetab == 'cpt') active @endif"><a href="{{ url('cpt') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} i-font-tabs"></i> {{ " ".$cpt->cpt_hcpcs }}</a></li>
			
            <!--<li class="@if($activetab == 'searchcpt') active @endif"><a href="{{ url('searchcpt') }}" ><i class="livicon" data-name="search" style="margin-right: 0px;"></i> 
                Search</a></li> -->
		</ul>
	</div>
</div>
<!--End Sub Menu-->