<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-body bg-white border-radius-4"><!-- Box Body Starts -->
		@if(isset($charges) && count($charges)>0)
	        <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
	            <table id="charge_del" class="table table-bordered table-striped">         
	                <thead>
	                    <tr>                                     
	                        <th>Claim Number</th>
	                        <th>Status</th>               
	                        <th>Action</th>               
	                    </tr>
	                </thead>
	                <tbody>
	                    @foreach($charges as $charge)
	                    <tr id="row_{{$charge->id}}">
	                        <td>{{ $charge->claim_number }}</td>
	                        <td>{{ $charge->status }}</td>  
	                        <td><a  href="javascript:void(0);" class="delete_charge"  data-id='{{$charge->id}}'> <i class="fa fa-trash"></i></a></td>
	                    </tr>
	                    @endforeach      
	                </tbody>
	            </table>
	            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
	                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-10 no-padding dataTables_info">
	                    Showing {{@$pagination['from']}} to {{@$pagination['to']}} of {{@$pagination['total']}} entries
	                </div>
	                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding margin-t-m-10">{!! $pagination['pagination_prt'] !!}</div>
	            </div>
	        </div>
	    @else
	        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5>No Records Found !!</h5></div>
	    @endif
	</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->
<link href="https://fonts.googleapis.com/css?family=Fjalla+One&display=swap" rel="stylesheet">