@foreach($notes_list as $list)
<div class="col-lg-3 col-md-3 postIt">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white sticky-notes">
		<p><a><i  class="js-popupnotes-delete fa fa-close pull-right margin-t-4 med-gray m-r-m-3 delete_notes cur-pointer" data-note-id="{!! $list->id !!}" data-placement="left"  data-toggle="tooltip" data-original-title="Delete"></i><i style="display:none" class="fa fa-edit pull-right margin-t-4 med-gray m-r-m-3 edit_notes" data-note-id="{!! $list->id !!}" data-placement="left"  data-toggle="tooltip" data-original-title="Edit"></i></a></p>
		<p class="med-green font600 margin-t-m-8">{!! App\Http\Helpers\Helpers::checkAndDisplayDateInInput($list->created_at); !!}</p>
		<p class="sticky-notes-scroll">{!! $list->notes !!}</p>
		
		<p class="font600" style="">Remind On : <span class="med-orange font600"><?php if($list->date != '0000-00-00') echo App\Http\Helpers\Helpers::checkAndDisplayDateInInput($list->date);  else echo "-Nil-";   ?></span> </p>
	</div>
</div>
@endforeach 
           