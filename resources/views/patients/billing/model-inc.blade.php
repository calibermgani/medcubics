<div id="auth" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Authorization</h4>
            </div>
            <div class="modal-body">

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

 <div id="js-model-popup" class="modal fade in js-avoid-savepopup">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_popup" aria-label="Close"  tabindex= "-1" ><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Provider</h4>
            </div>
            <div class="modal-body">
              <div class="col-lg-12"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->  
 <div id="imosearch" class="modal fade in js-avoid-savepopup">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Search ICD-10</h4>
            </div>
            <div class="modal-body">                        
                    <div class="box box-view no-shadow no-border margin-b-10">
                        <div class="box-body form-horizontal no-padding">
                            <h5 class=""> <!--<span class="js-icd-val-error margin-r-5 med-orange font20">   </span><span class="med-orange font20 margin-r-5">is invalid !</span>-->  </h5>
                        <div class="input-group input-group-sm">
                            <input name="search_icd_keyword" type="text" class="form-control js_search_icd_list" placeholder="Search ICD-10 using key words">
                            <input class="js_icd_val" type ="hidden"/>
                            <span class="input-group-btn">
                              <button class="btn btn-flat btn-medgreen js_search_icd_button js_search_icd_list" type="button">Search</button>
                            </span>                                    
                        </div>
                        <span id='search_icd_keyword_err' class='help-block hide mrd-red' data-bv-validator='notEmpty' data-bv-for='search_icd_keyword_err' data-bv-result='INVALID'><span id="search_icd_keyword_err_content">Please enter search keyword!</span></span>
                        <center><span class="text-center med-green" id="ajx-loader"> </span></center>
                        <div id="icd_imo_search_part">
                            
                        </div>
                    </div>
                </div>                               
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->   
<div id="payment_details" class="modal fade in js-avoid-savepopup">
    <div class="modal-md-650" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Details</h4>
            </div>
            <div class="modal-body p-b-0">
             
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 
<div id="cms" class="modal fade in">
    <div class="modal-md-650">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-url = "" class="close cms-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">CMS 1500</h4>
            </div>
            <div class="modal-body">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->     