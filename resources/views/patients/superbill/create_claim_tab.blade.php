<div class="tab-pane" id="create_claim">
    <div class="box-body"><!-- Box Body Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom">                            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
                <h5><span style="background: #f07d08; color:#fff; padding: 2px 10px; border-radius: 4px;">DOS : <span id="dos_seleted_display1">MM-DD-YYYY</span></span>
                    <span style="background: #4fc5cc; color:#fff; padding: 2px 10px; border-radius: 4px;">Provider : <span id="providername_seleted_display1"></span></span>
                    <span id="template_seleted_display"></span>
                </h5>
            </div> 
         
        </div>

        <span id="create_bill_main_list_part"></span>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0">                            
            <div class="box box-view-border no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">Notes</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">

                    <div class="col-md-12 p-r-0 p-l-0">
                        <textarea placeholder="Enter the Notes" class="form-control" id="superbill_note" name="superbill_note" cols="50" rows="10"></textarea>
                        {!! Form::hidden('status','E-bill') !!}
                    </div>

                </div>                              
            </div>    
        </div>
        
        <div class="box-footer">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <!--{!! Form::submit('Save', ['class'=>'btn btn-medcubics pull-right form-group']) !!}-->
                {!! Form::button('Save', ['class'=>'btn btn-medcubics pull-right js-submit-superbillclaim_form']) !!}
                <a href="javascript:void(0)" data-url="#select_procedure">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics pull-left js_cancel_site']) !!}</a>
            </div>
        </div><!-- /.box-footer -->
    </div><!-- Box Body Ends -->
</div><!-- Tab Pane Ends --> 