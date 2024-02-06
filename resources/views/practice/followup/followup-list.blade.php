<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="accordion">
        @if(!empty($category))
        @foreach($category as $list)

        <div class="accordion-group" style="padding: 6px;">
            <span class="font600 normal-font font14 margin-r-10"><i class="fa fa-edit js-category-edit cur-pointer med-green" data-placement="top"  data-toggle="tooltip" data-original-title="Edit" data-category-id="{{ $list->id }}" ></i></span>
            <div class="accordion-heading js_accordion_header" style="display: inline;">

                <a class="accordion-toggle" style="display: inline">
                    <h4 class="margin-t-0 no-bottom font16" style="display:inline"><i class="fa fa-plus-circle margin-r-10"></i> {{ ucfirst($list->name) }}  <span class="text-right pull-right normal-font font13 med-gray margin-r-10 js-count"><i class="fa fa-circle pull-right margin-l-10 @if($list->status=='Active') med-green-o @else med-red @endif" data-placement="top"  data-toggle="tooltip" data-original-title="{{$list->status}}"></i> <span class="">|</span> &nbsp; Created : <span class="med-green">{{ App\Http\Helpers\Helpers::dateFormat($list->created_at, 'date') }}</span></span><span class="text-right pull-right normal-font font13 margin-r-10 med-gray">Modified : <span class="med-green">{{ App\Http\Helpers\Helpers::dateFormat($list->updated_at, 'date') }}</span></span> </h4> 
                </a>
            </div> 

            <div class="accordion-body js_accordion_content collapse">
                <div class="accordion-inner margin-t-m-8 no-padding"> 
                    <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 hide" style=" position: absolute; z-index: 9999; margin-left:100px;left:0px; margin-top: 12px;">                                       
                        <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
                           class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a>
                    </div> 
                    <div class="table-responsive"> 
                        <p> <a href="#" class="pull-right btn btn-medcubics-small margin-b-4 clear-question-add" data-category-id="{{$list->id}}" data-toggle="modal" data-target="#addQuestion"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Question</a></p>
                        <table id="{{ ucfirst($list->name) }}" class="table table-separate no-bottom popup-table-border">
                            <thead>
                                <tr>
                                    <th class="" style="">Question</th> 
                                    <th class="" style="">Hint</th> 
                                    <th class="" style="">Field Type</th> 
                                    <th class="" style="">Field Validation Type</th>
                                    <th class="" style="">Status</th>
                                    <th class="" style="">Created Date</th>
                                    <th class="" style="">User</th>
                                    <th class="" style=""></th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list->question as $qlist)
                                <tr class="" style="line-height:30px;">
                                    <td class="line-height-24 med-text">{{ $qlist->question }}</td><!-- Don't remove this inline style -->
                                    <td class="line-height-24">{{ $qlist->hint }}</td>
                                    <td class="line-height-24">{{ $qlist->field_type }}</td>
                                    <td class="line-height-24">@if($qlist->field_type != 'date') {{ $qlist->field_validation }} @else {{ $qlist->date_type }} @endif</td>
                                    <td class="line-height-24">{{ $qlist->status }}</td>
                                    <?php /*
                                    <td class="line-height-24">{{ date('m/d/y',strtotime($qlist->created_at)) }}</td>
                                    */ ?>
                                    <td class="line-height-24">{{ App\Http\Helpers\Helpers::dateFormat($qlist->created_at, 'date') }}</td>
                                    <td class="line-height-24">{{@$qlist->user->short_name }}</td>
                                    <td class="line-height-24"><span><a onClick=""><i data-question-id="{{ $qlist->id }}" class="edit_followup fa  {{Config::get('cssconfigs.common.edit')}} js-prevent-action cur-pointer" data-placement="bottom"  data-toggle="tooltip" title="Edit"> </i></a></span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@push('view.scripts')
<script type="text/javascript">

	$('.accordion-toggle').click(function () {	
		//$(this).find('h4 i').toggleClass('fa-plus-circle fa-minus-circle');
	});

	$(document).on('click', ".js_accordion_header .js-category-edit", function (event) {
		event.stopPropagation();
	});

	$(document).on('click', ".js_accordion_header", function (event) { 
	   // $(this).parent(".accordion-group").find(".js_accordion_content").slideToggle("slow");
		//$(this).find('h4 i').toggleClass('fa-plus-circle fa-minus-circle');
	});
</script>
@endpush