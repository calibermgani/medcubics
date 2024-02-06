<div class="col-md-12 ">
    <div class="box-block">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                   {!! HTML::image('img/noimage.png',null,['class'=>'img-border']) !!}
                </div>
            </div>

			<div class="col-lg-7 col-md-7 col-sm-9 col-xs-12">
                <h3>{{ $insurancetypes->code }}</h3>
                Category : {{ $insurancetypes->type_name }} <br/><br/>
            </div>

			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 med-left-border">

                <div>
                    <span class="med-green"><i class="livicon"  data-name="filter" data-animate="false" ></i>Created at</span>
                    <h5 class="med-header-list-bg">{{ $insurancetypes->created_at }}</h5>
                </div>

                <div>
                    <span class="med-green"><i class="livicon"  data-name="tag" data-animate="false" ></i>Updated at</span>
                    <h5 class="med-header-list-bg">{{ $insurancetypes->updated_at}}</h5>
                </div>
            </div>
        </div><!-- /.box-body -->
	</div>	
</div>