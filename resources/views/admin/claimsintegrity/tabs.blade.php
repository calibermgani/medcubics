<div class="col-md-12 margin-t-m-5">
    <!-- Sub Menu -->
    <?php
        $routex = explode('/',Route::getFacadeRoot()->current()->uri()); 
        if(count($routex) > 0) {
            if($routex[0] == 'admin') {
                $activetab = 'admin';
            } elseif($routex[1] == 'claimsintegrity') {
                $activetab = 'claimsintegrity';
            }
        }
    ?>  
    
    <div class="med-tab nav-tabs-custom margin-t-10">
        <ul class="nav nav-tabs" id="document_dynanic_tab">

            <li class="active itegrity_default-dynamic-details " data-type="summery" id="summery" data-title="Category"><a href="javascript:void(0)" data-model="summary"><i class="fa {{Config::get('cssconfigs.maintenance.sql')}} font16"></i> Data Test</a></li>
            <li class="itegrity_default-dynamic-details " data-type="assigned" id="assigned" data-title="Assigned Document"><a href="javascript:void(0)" data-model="assigned"><i class="fa {{Config::get('cssconfigs.maintenance.sql')}} font16"></i> Errors</a></li>

        </ul>
    </div>

</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-18 hide">
    <div class="box-info no-shadow margin-t-m-10">

        <div class="box-body form-horizontal  padding-4">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">                                                                
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive margin-b-5 no-padding">
                        <table class="popup-table-wo-border table margin-b-1">                    
                            <tbody>
                                <tr>
                                    <td class="font600 line-height-30">Total Documents</td>
                                    <td class="line-height-30"><span class="font600 font16">total_document_count </span></td> 
                                </tr>                            
                                <tr>
                                    <td class="font600 line-height-30">Assigned</td>
                                    <td class="line-height-30"><span class="font600 font16">assigned_document_count</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive tab-l-b-1 p-l-0  md-display tabs-lightgreen-border">
                        <table class="popup-table-wo-border table margin-b-1">                    
                            <tbody>     

                                <tr>
                                    <td class="font600 line-height-30">Total In Process</td>
                                    <td class="line-height-30"><span class="font600 font16">inprocess_document_count </span></td>
                                </tr>  
                                <tr>
                                    <td class="font600 line-height-30">Total Review</td>
                                    <td class="font600 line-height-30"><span class="font16">review_document_count</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>                                
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive p-l-0 tab-l-b-1  md-display tabs-lightgreen-border">
                        <table class="popup-table-wo-border table margin-b-1">                    
                            <tbody>
                                <tr>                                               
                                    <td class="font600 line-height-30">Total Pending</td>
                                    <td class="med-orange font600 line-height-30"><span class="font16">pending_document_count </span> </td>
                                </tr>
                                <tr>
                                    <td class="font600 line-height-30">Total Completed</td>
                                    <td class="font600 line-height-30"><span class="font16">completed_document_count </span> </td>
                                </tr>        
                            </tbody>
                        </table>
                    </div>                                
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    if(!Request::ajax()){
        $display = "style=display:none;";
    }
?>
<div class="js-common-document-link" <?php echo $display; ?>  >
    <div class="margin-left: 3%;">
        @include("admin/claimsintegrity/common_data")
    </div>
</div>
<style type="text/css">
    #documents_wrapper{
        margin: 1%;
    }
</style>