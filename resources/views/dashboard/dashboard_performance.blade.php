@foreach($performanceData as $key => $data)
<?php 
	$total = "0.00";
	$data_arr = (array)$data;
	$total = array_sum($data_arr);
?>
<tr>
    <td class="med-green">{{$key}}</td>
    <td style="text-align:right">{{$data->January}}</td>
    <td style="text-align:right">{{$data->February}}</td>
    <td style="text-align:right">{{$data->March}}</td>
    <td style="text-align:right">{{$data->April}}</td>
    <td style="text-align:right">{{$data->May}}</td>
    <td style="text-align:right">{{$data->June}}</td>
    <td style="text-align:right">{{$data->July}}</td>
    <td style="text-align:right">{{$data->August}}</td>
    <td style="text-align:right">{{$data->September}}</td>
    <td style="text-align:right">{{$data->October}}</td>
    <td style="text-align:right">{{$data->November}}</td>
    <td style="text-align:right">{{$data->December}}</td>
    <td style="text-align:right" class="med-green font600">{!!App\Http\Helpers\Helpers::priceFormat($total) !!}</td>
</tr>
@endforeach