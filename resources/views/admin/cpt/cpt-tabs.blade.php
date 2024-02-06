<div class="col-md-12 margin-t-m-18">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3">
                <div class="text-center med-circle">
                    {{$cpt->cpt_hcpcs}}
                </div>
            </div>           
            
            <div class="col-lg-7 col-md-7 col-sm-10 col-xs-12">                               
                <div class="form-group">				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                       <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
                        <span class="med-green font600">CPT</span>  
                       </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            <span class="med-orange">{!! $cpt->cpt_hcpcs !!}</span>   
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
               
               
            </div>    
            
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 med-left-border">
                <ul class="icons push no-padding">
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Code Category </span>  @if($cpt->code_type != '')<span class="pull-right">{{$cpt->code_type}} </span> @endif </li>
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Global Period </span> @if($cpt->medicare_global_period != '')<span class="pull-right">{{$cpt->medicare_global_period}} </span> @endif </li>
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">ICD </span>@if($cpt->icd != '') <span class="pull-right">{{$cpt->icd}} </span> @endif </li>
                  <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Effective Date </span> @if($cpt->effectivedate != '0000-00-00') <span class="pull-right"><span class="bg-date">{{ App\Http\Helpers\Helpers::dateFormat($cpt->effectivedate,'date') }}</span> </span> @endif </li>
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Inactive Date </span> @if($cpt->terminationdate != '0000-00-00')<span class="pull-right"><span class="bg-date">{{  App\Http\Helpers\Helpers::dateFormat($cpt->terminationdate,'date') }} </span></span> @endif  </li>                  
                </ul>                            
            </div>
        </div><!-- /.box-body -->
    </div>
</div>
<!--End Sub Menu-->
