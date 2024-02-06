<div class="box box-view no-shadow no-border"><!--  Box Starts -->
    <div class="box-body form-horizontal no-padding">                        
        <div class="col-md-12 no-padding"><!-- Inner Content for full width Starts -->
            <div class="box-body-block med-bg-f0f0f0 p-b-0"><!--Background color for Inner Content Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- General Details Full width Starts -->                           
                    <div class="box no-border  no-shadow" ><!-- Box Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-bg-f0f0f0"><!--  1st Content Starts -->
                            {!! Form::open(['url'=>'payments/search','id' => 'js-payment-search']) !!}
                                <div class="box-body form-horizontal no-padding"><!-- Box Body Starts -->                                
                                    <div class="form-group-billing">    
                                        {!! Form::label('claim', 'Category', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                             
                                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-10 ">
                                            {!! Form::select('category',array('Insurance'=>'Insurance Payment','Patient'=>'Patient Payment','both'=>'Both'),null,['class'=>'select2 form-control', 'id' => 'js-select-searchcat']) !!}
                                        </div>                                                     
                                    </div>
                                    <div class="form-group-billing">    
                                        {!! Form::label('claim', 'Search By', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                             
                                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-10 ">
                                            {!! Form::select('search_by',array('paymentnumber'=>'Payment ID','insurance_name'=>'Insurance','check_no'=>'Check/EFT No','claim_no' => 'Claim No',
                                            'check_date'=>'Check Date','created_at'=>'Created On','posted_by'=>'User', '' => '--'),'paymentnumber',['class'=>'select2 form-control js-search-check']) !!}
                                        </div>                                                     
                                    </div>
                                    <div class="form-group-billing js-insurance-dropdown" style="display:none;">    
                                        {!! Form::label('Insurance', 'Insurance', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                             
                                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-10 ">
                                             {!! Form::select('insurance',(array)$insurance_list,null,['class'=>'select2 form-control'])!!}                                
                                        </div>                                                     
                                    </div>
                                   
                                    <div class="form-group-billing js-name">
                                        {!! Form::label('', 'Payment ID', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green font600 js-change-label']) !!}
                                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">                                          
                                            {!! Form::text('name',null,['class'=>'form-control ']) !!}                                            
                                        </div>
                                    </div>
                                     <div class="form-group-billing js-search-date" style="display:none;">                         
                                        {!! Form::label('claim', 'From', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green font600 js-change-label']) !!}
                                        <div class="col-lg-8 col-md-8 col-sm-10 col-xs-10">
                                            <i class="fa fa-calendar-o form-icon" onclick = "iconclick('search_date')"></i>
                                            {!! Form::text('search_date',null,['maxlength'=>'15','class'=>'form-control dm-date js-auth_datepicker dm-date']) !!}                                            
                                        </div>
                                    </div>                                  
                                    <div class="form-group-billing">                         
                                        {!! Form::label('claim', 'From', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                        <div class="col-lg-8 col-md-8 col-sm-10 col-xs-10">
                                            <i class="fa fa-calendar-o form-icon" onclick = "iconclick('search_from')"></i>
                                            {!! Form::text('search_from',null,['maxlength'=>'15','class'=>'form-control dm-date js-auth_datepicker']) !!}                                            
                                        </div>
                                    </div>                     
                                    <div class="form-group-billing">      
                                        {!! Form::label('claim', 'To', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                        <div class="col-lg-8 col-md-8 col-sm-10 col-xs-10">
                                            <i class="fa fa-calendar-o form-icon" onclick = "iconclick('search_to')"></i>
                                            {!! Form::text('search_to',null,['maxlength'=>'25','class'=>'form-control dm-date js-auth_datepicker']) !!}                                            
                                        </div>
                                    </div>                                
                                    <div class="form-group-billing">                                                
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                                            {!! Form::submit('Search', ['class'=>'btn btn-medcubics-small pull-right']) !!}
                                          <!--  <button class="btn btn-medcubics-small close_popup" type="button">Cancel</button> -->
                                        </div>

                                    </div>                                                          
                                </div><!-- /.box-body Ends-->
                            {!! Form::close() !!}
                        </div><!--  1st Content Ends -->                            
                    </div><!--  Box Ends -->
                </div><!-- General Details Full width Ends -->
            </div><!-- Inner Content for full width Ends -->
        </div><!--Background color for Inner Content Ends -->        
    </div>
</div><!-- /.box-body -->

<script type="text/javascript">
    $(document).ready(function () { 
        $('input[name="check_date"]').on('change', function(){
            $('form#js-payment-search').bootstrapValidator('revalidateField', 'check_date'); 
        });
		
        $('input[name="search_from"]').on('change', function(){
            $('#js-payment-search').bootstrapValidator('revalidateField', $('input[name="search_from"]'));
            $('#js-payment-search').bootstrapValidator('revalidateField', $('input[name="search_to"]'));
        });
		
        $('input[name="search_date"]').on('change', function(){
            $('form#js-payment-search').bootstrapValidator('revalidateField', 'search_date'); 
        });
		
        $('input[name="name"]').on('change', function(){
            $('form#js-payment-search').bootstrapValidator('revalidateField', 'name'); 
        });
		
        $('input[name="search_to"]').on('change', function(){
            $('#js-payment-search').bootstrapValidator('revalidateField', $('input[name="search_from"]'));
            $('#js-payment-search').bootstrapValidator('revalidateField', $('input[name="search_to"]'));
        });  
		
        $('#js-payment-search')
			.bootstrapValidator({
				message: 'This value is not valid',
				excluded: ':disabled',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {                                                                                     
					name: {
						validators: {                                
							 callback: {
								message: 'Enter search value',
									callback: function (value, validator) {
										search_val = $(".js-search-check option:selected").val() ;
										if(value == "" && (search_val == "check_no" || search_val == "paymentnumber" || search_val == "posted_by" )){
											return false;
										}                                            
										return true;
									},                                    
							},                         
						}
					}, 
					search_date: {
						validators: {                                
							 callback: {
								message: 'Enter search date',
									callback: function (value, validator) {
										search_sel = $(".js-search-check option:selected").val(); 
										 value_date =   $('input[name="search_date"]').val();                                             
										 if(value_date == "" && (search_sel =="created_at" || search_sel =="check_date")){
											return false;
										 }                                                   
										 var current_date=new Date(value_date);
										 var d=new Date();
										  if(value_date != '' && d.getTime() < current_date.getTime()){  
											//console.log("comes inside check date");
												return {
													valid: false,
													message: future_date,
												};
										 }                                         
										 return true;
									},                                    
							}, 
							date: {
								format: 'MM/DD/YYYY',
								message: '{{ trans("common.validation.date_format") }}'
							},                          
						}
					}, 
					search_from: {
						validators: {
							 callback: {
								message: '',
								callback: function (value, validator) {
									value_date =   value;                                        
									 var current_date=new Date(value_date);
									 var d=new Date();
									  if(value_date != '' && d.getTime() < current_date.getTime()){              
											return {
												valid: false,
												message: future_date,
											};
									 }
									 var search_to = validator.getFieldElements('search_to').val();
									 var response = startDate(value,search_to);
									 if (response != true){
										return {
											valid: false,
											message: response
										}; 
									 }                                          
									 return true;
								},                                    
							}, 
							date: {
								format: 'MM/DD/YYYY',
								message: '{{ trans("common.validation.date_format") }}'
							},                          
						}

					}, 
					search_to: {
						validators: {
							callback: {
								message: '',
									callback: function (value, validator) {               
										value_date =   value;                                     
										var current_date=new Date(value_date);
										var d=new Date();
										if(value_date != '' && d.getTime() < current_date.getTime()){ 
											return {
												valid: false,
												message: future_date,
											};
										 }  
										 var eff_date = validator.getFieldElements('search_from').val();
										 var ter_date = value;
										 var response = endDate(eff_date,ter_date);
										 if (response != true){
											return {
												valid: false,
												message: response
											}; 
										 }                                        
										 return true;
									},                                    
							 },
							date: {
								format: 'MM/DD/YYYY',
								message: '{{ trans("common.validation.date_format") }}'
						   },                         
						}

					},                                                             
				},
			});
	});  
	/*** Date function check start here ***/
	function startDate(start_date,end_date) {
		var date_format = new Date(end_date);
		if (end_date != '' && date_format !="Invalid Date") {
			return (start_date == '') ? '{{ trans("practice/patients/payments.validation.search_fromdate")}}':true;
		}
		return true;
	}
	
	function endDate(start_date,end_date) {
		var eff_format = new Date(start_date);
		var ter_format = new Date(end_date);
		if (ter_format !="Invalid Date" && end_date != '' && eff_format !="Invalid Date" && end_date.length >7 && checkvalid(end_date)!=false) {
			var getdate = daydiff(parseDate(start_date), parseDate(end_date));
			return (getdate >= 0) ? true : '{{ trans("practice/patients/payments.validation.beforestart")}}';
		}
		else if (start_date != '' && eff_format !="Invalid Date") {
			return (end_date == '') ? '{{ trans("practice/patients/payments.validation.search_todate")}}':true;
		
		}
		return true;
	}
	
	function daydiff(first, second) {
		return Math.round((second-first)/(1000*60*60*24));
	}
	
	function parseDate(str) {
		var mdy = str.split('/')
		return new Date(mdy[2], mdy[0]-1, mdy[1]);
	}
	
	function checkvalid(str) {
		var mdy = str.split('/');
		if(mdy[0]>12 || mdy[1]>31 || mdy[2].length<4 || mdy[0]=='00' || mdy[0]=='0' || mdy[1]=='00' || mdy[1]=='0' || mdy[2]=='0000') {
			return false;
		}
	}
	/*** Date function check end here ***/
</script>