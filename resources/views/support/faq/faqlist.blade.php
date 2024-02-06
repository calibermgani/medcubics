@if(count($faq_category_arr) > 0)
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10"><!--  Left side Content Starts -->
    <div class="panel-group" id="accordion">
        @foreach($faq_category_arr as $category)
        <?php $faq_arr = App\Models\Medcubics\Faq::getFaqByCategory($category->category, $search_keyword); ?>
        @if(count($faq_arr) > 0)
        <div class="box box-view no-shadow margin-b-10"><!--  Box Starts -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p class="panel-title">
						<i class="fa fa-question-circle med-orange margin-r-5"></i> {{ $category->category}} 
                        <a class="accordion-toggle med-orange" data-toggle="collapse" data-parent="#accordion" href="#{{ $category->id}}">                                            
                          <span class="fa fa-minus pull-right" style="font-size: 12px; line-height: 20px;"></span>
                        </a>
                    </p>
                </div>
            </div>
            <div id="{{ $category->id}}" class="panel-collapse collapse in">
                <div class="panel-body p-b-0">
                    @foreach($faq_arr as $faq)
						<div class="box box-view no-shadow collapsed-box margin-b-10"><!--  Box Starts -->
							<div class="">
								<i class="fa fa-hand-o-right med-green margin-r-10"></i><span class="med-green cur-pointer faq-question" data-widget="collapse">{{ $faq->question}}</span>                            
							</div><!-- /.box-header -->
							<div class="box-body table-responsive" style="border-bottom: 1px dotted #f0f0f0;"><!-- Box Body Starts -->                                
								<p class="no-bottom margin-t-m-10 margin-l-10" >{{ $faq->answer}}</p>                            
							</div><!-- /.box-body -->
						</div><!-- /.box Ends-->
                    @endforeach
                </div>
            </div>
        </div><!-- /.box Ends-->
        @endif                            
        @endforeach
    </div>
</div><!--  Left side Content Ends -->
@else
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5>No Records Found !!</h5></div>
@endif	
@push('view.scripts1')
<script type="text/javascript">
$('.accordion-toggle').click(function () {
    $(this).find('h4 i').toggleClass('fa-plus-circle fa-minus-circle');
});
</script>
@endpush