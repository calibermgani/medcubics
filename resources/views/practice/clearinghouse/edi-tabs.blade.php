<div class="col-md-12 margin-t-m-18">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                
            </div>
            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-9">
                <h3>{{ $clearing_house->name }}</h3>

                <p class="push">{{substr($clearing_house->description, 0, 230)}}</p>

            </div>

            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 med-left-border">

                <ul class="icons push no-padding">
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Contact Name </span>  @if($clearing_house->contact_name != '')<span class="pull-right">{{$clearing_house->contact_name}} </span> @endif </li>
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Contact Phone</span> @if($clearing_house->contact_phone != '')<span class="pull-right">{{$clearing_house->contact_phone}} </span> @endif </li>
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Contact Fax</span>@if(@$clearing_house->contact_fax != '') <span class="pull-right">{{$clearing_house->contact_fax}} </span> @endif </li>
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Status</span>@if($clearing_house->status != '') <span class="pull-right">{{$clearing_house->status}} </span> @endif </li>
                                 
                </ul>                            
            </div>          

        </div><!-- /.box-body -->

        <!--Sub Menu

        <?php $activetab = 'listfavourites';
            $routex = explode('.',Route::currentRouteName());
        ?>
        @if(count($routex) > 0)
            @if($routex[0] == 'cpt')
                <?php $activetab = 'cpt'; ?>

            @endif
        @endif

        <div class="table-container">
            <div class="col-table-cell col-lg-4"><i class="livicon" data-name="notebook" data-size="20" data-color="#00837C" data-hovercolor="#f07d08"></i> <a href="{{ url('cpt') }}" @if($activetab == 'cpt')class="active"@endif>CPT List</a></div>
        </div>-->
    </div>
</div>
<!--End Sub Menu-->