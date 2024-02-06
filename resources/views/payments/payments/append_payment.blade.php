<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
 <div class="box box-info no-shadow orange-border">
    <div class="box-body textbox-bg-orange border-radius-4 p-b-5">
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 no-bottom form-horizontal">                               
            <div class="form-group-billing">                    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 billing-select2-orange">
                    {!! Form::select('patient_detail',array('name' => 'Name','last_name'=>'Last Name','first_name'=>'First Name','claim_number'=>'Claim No','account_no'=>'Account No','policy_id'=>'Policy ID', 'dob' => 'DOB', 'ssn' => 'SSN'),@$type,['class'=>'form-control select2', 'id' => 'PatientSearch','onchange' => 'selectSearchFilter(this,"js-posting-search-val")']) !!}
                </div>                                                     
            </div>                                    
        </div>           
        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6 no-bottom form-horizontal">
            <div class="form-group-billing">                                                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">                     
                   {!! Form::text('search_val',@$search_val,['id' => 'js-posting-search-val','maxlength'=>'25','class'=>'form-control input-sm-modal-billing yes-border','placeholder'=>'Search']) !!}                       
                </div>
            </div>                  
        </div>                                  
        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2 no-bottom form-horizontal">
            <div class="form-group-billing">                                                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                   <a href= "javascript:void(0)" class="btn btn-medcubics-small pull-right js-search-patient js-nextclass" style = "margin-top:1px;">Search</a>
                   @if(isset($from) && $from == "paymentdetail")
						{!!Form::hidden('paymentdetail', 1)!!}
                   @endif
                </div>
            </div>                  
        </div>                              
    </div><!-- /.box-body -->
</div><!-- /.box -->
</div>
@push('view.script')
<script type="text/javascript">
    
$('input[type="text"]').attr('autocomplete','off');

</script>
@endpush