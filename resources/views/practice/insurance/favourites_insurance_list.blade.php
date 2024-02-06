<div class="table-responsive"> 
    <table id="example1" class="table table-bordered table-striped">
        <thead>
            <tr>
            	<th>Short Name</th>
               <th>Insurance Name</th>                       
                <th>Insurance Type</th>
                <th>Payer ID</th>
                <th>Phone</th>
                <th>Favourite</th>
            </tr>
        </thead>
		<tbody>
			@foreach($favourites as $favourite)
			<?php $favourite->insurance->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($favourite->insurance->id,'encode'); ?>
				<tr data-url="{{ url('insurance/'.$favourite->insurance->id) }}" class="js-table-click clsCursor">
					<td>{{ @$favourite->insurance->short_name }}</td>
					<td>{{ @$favourite->insurance->insurance_name }}</td>
					<td>{{ @$favourite->insurance->insurancetype->type_name }}</td>
					<td>{{ @$favourite->insurance->payerid }}</td>
					<td>{{ @$favourite->insurance->phone1 }}</td>
					<td><a  data-url="{{ url('toggleinsurancefavourites/'.$favourite->insurance->id)}}" data-id="{{$favourite->insurance->id}}" class="js-favourite-record tooltips" href="javascript:void(0);"><i data-original-title="Remove from favourite" data-toggle="tooltip" data-placement="bottom" class="fa {{Config::get('cssconfigs.common.star')}} tooltips"></i></a></td>
				</tr>
			@endforeach
		</tbody>
    </table>
</div>