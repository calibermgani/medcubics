{!! Form::open(['url'=>'insurance','role' => 'form','action' => '','files' => true,'id'=>'js-bootstrap-validatorpopup_ins','name'=>'medcubicsform1','class'=>'medcubicsform1']) !!}
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-13 no-padding">
    <div class="box-block box-info no-shadow"><!-- Box Starts -->
        <div class="box-body"><!-- Box Body Starts -->
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-8 form-horizontal">
                <div class="form-group no-bottom">  
                    
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                          {!! Form::select('insurace_search_category_modal', ['insurance_name'=>'Insurance Name','payerid' => 'Payer ID','address' => 'Address'],'insurance_name',['class'=>'select2 form-control js_insurace_search_category_popmodal','id'=>'js_insurace_search_category_popmodal']) !!}
                    </div>      
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">      
                        {!! Form::text('insurance_list',null,['placeholder'=>'Search Insurance','class'=>'form-control js-letters-caps-format insurance_list', 'maxlength'=>28,'id' =>'insurancepopup_list']) !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <input class="btn btn-medcubics-small margin-t-2" data-url="{{ url('get_insurancelist') }}" id="js-search-popupins" value="Search" type="button">
                </div>
            </div>
            
        </div>
    </div>
</div>
{!! Form::close() !!}

@if(count($insurance_arr)>0)
<div class="box-body p-l-2 p-r-2 "><!-- modal-ins-scroll-50 -->
<table class="table table-striped table-bordered table-separate " id="patientstatement">
        <thead>
            <tr>
                <th class="td-c-50">Insurance Name</th> 
                <th class="td-c-20">Insurance Type</th> 
                <th class="td-c-50">Insurance Address</th>  
                <th>Payer ID</th>
                <th class="hide">Phone</th>
                <th>Status</th>           
            </tr>
        </thead>
        <tbody>         
            @foreach(@$insurance_arr as $key=>$ins_value)
                <tr>
                <?php $get_ins_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($ins_value->id,'encode');  
					$insurance_type = (isset($ins_value->insurancetype->type_name)) ? $ins_value->insurancetype->type_name : '';
				?>
                    <td><input type="radio" id="get_ins_content_{{$key}}"  class='flat-red get_insurance_select' name="get_ins_content" data-value="{{ $get_ins_id }}"><label for="get_ins_content_{{$key}}" class="med-darkgray font600">{{ $ins_value->insurance_name }}</label></td>
                   <td>{{ @$insurance_type }}</td>
                   <td>{{ $ins_value->address_1 }}, {{ $ins_value->city }}, {{ $ins_value->state }}, {{ $ins_value->zipcode5 }}</td>
                   <td>{{ $ins_value->payerid }}</td>
                   <td class="hide">{{ $ins_value->phone1 }}</td>
                   <td>{{ $ins_value->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
    <p class="text-center med-green font600"> No Records Found</p>
@endif 