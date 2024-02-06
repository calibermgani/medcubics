
<div class="col-md-12 ">
    <div class="box-block">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center img-border med-circle">
                  {{ $icd->code }}
                </div>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-9 col-xs-12">
                <h3>{{ $icd->code }}</a>
</h3>

                 <p class="push">{{substr($icd->medium_desc, 0, 125)}}</p>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 med-left-border">

                <div>
                    <span class="med-green"><i class="livicon"  data-name="filter" data-animate="false" ></i>Code Category</span>
                    <h5 class="med-header-list-bg">{{ $icd->category }}</h5>
                </div>
                <div>
                    <span class="med-green"><i class="livicon"  data-name="flag" data-animate="false" ></i>Code Status</span>
                    <h5 class="med-header-list-bg">@if($icd->code_status != '') {{ $icd->code_status }} @else <span class="notavailable">Nil</span> @endif</h5>
                </div>
            </div>
        </div><!-- /.box-body -->

<!-- Sub Menu -->
    <?php  $activetab = 'admin/icd09';
        $routex = explode('.',Route::currentRouteName());
        $currnet_page = Route::getFacadeRoot()->current()->uri();
    ?>

    @if($currnet_page == 'admin/icd09')
        <?php $activetab = 'admin/icd09'; ?>
    @elseif(count($routex) > 1)
        @if($routex[0] == 'admin/icd')
            <?php $activetab = 'admin/icd'; ?>
        @endif
    @endif


    </div>


   <div class="med-tab nav-tabs-custom space20 no-bottom">
       <ul class="nav nav-tabs">
           @if($checkpermission->check_adminurl_permission('admin/icd09') == 1)
           <li class="@if($activetab == 'admin/icd09') active @endif"><a href="{{ url('admin/icd09') }}" ><i class="livicon" data-name="archive-extract" style="margin-right: 0px;"></i> ICD -9</a></li>
           @endif

           @if($checkpermission->check_adminurl_permission('admin/icd') == 1)
           <li class="@if($activetab == 'admin/icd') active @endif"><a href="{{ url('admin/icd') }}" ><i class="livicon" data-name="archive-extract" style="margin-right: 0px;"></i> ICD -10</a></li>
           @endif
   </div>
</div>
