<div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
    @if(!empty($insurance_lists))
		<div class="box-body form-horizontal no-padding">
			@foreach($insurance_lists as $key => $insurance_list)
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="cpt-grid no-padding line-height-26 no-bottom" style="list-style-type:none;" id="">
					<li class="">
						<table class="table-striped-view">
							<tbody>
								<tr>
									<td style="padding: 0px 4px; width: 5%;">                   
									   {!!Form::radio('patient_insurance_list', $insurance_list,'',['class' => 'js-patient-ins chk', 'data-insurance' => $key, 'id'=>$insurance_list])!!}
									   {!!Form::label($insurance_list, $insurance_list,['class'=>'med-darkgray font600 form-cursor'])!!}  
									</td>                                                              
								</tr>
							</tbody>
						</table>                                     
					</li>   
				</ul>
			</div>
			@endforeach      
			<div class="text-center">
				<button class="btn btn-medcubics-small js-change-insurance" type="button">Ok</button>
				<button class="btn btn-medcubics-small" type="button" data-dismiss="modal">Cancel</button>
			</div>
		</div>
    @else 
        <div class ="">
            <p class="med-gray-dark">
            No patient insurances available. Kindly add to post payment
            </p>
        </div>
    @endif
</div>