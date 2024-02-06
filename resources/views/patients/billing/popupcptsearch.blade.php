<div class="box box-view no-shadow no-border no-bottom" id='Patient_list'><!--  Box Starts -->
    <div class="box-body form-horizontal no-padding">
        <div class="input-group input-group-sm">
            {!! Form::text('search_keyword',@$search_keyword,['class'=>'form-control','placeholder'=>'Search CPT using key words']) !!}
            <span class="input-group-btn">
                <button class="btn btn-flat btn-medgreen js-submit" type="button">Search</button>
            </span>
        </div>
         <div class="js-spin-image text-center med-green"></div>
         
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10  modal-icd-scroll-500 js-spin">
            		
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <ul class="cpt-grid icd-charge-imo no-bottom">
                    <li class="superbill">
                        <table id="patientList" class="table-striped-view">
                            <tbody>
                                @if(!empty($cpts))
									@foreach ($cpts as $key => $cpt)
										@if(@$cpt->short_description != '') 
											<tr>
												<td class="padding-0-4 td-c-82" style="border-bottom: 1px solid #bbeff1;">
													<input class="chk js-sel-cpt" name="imo_search_cpts[]" type="checkbox" value="{{$cpt->cpt_hcpcs}}" id="CPT{{$key}}"><label for="CPT{{$key}}" class="no-bottom med-darkgray"> {!!$cpt->short_description !!}</label>
												</td>
												<td class="td-c-13 p-l-10">{!!$cpt->cpt_hcpcs !!}</td>
											</tr>
										@endif
									@endforeach
								@elseif(!empty($search_keyword))
									{{ trans("practice/patients/billing.validation.cpt_search") }}
								@endif
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>