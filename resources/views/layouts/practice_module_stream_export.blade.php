<!---Export -->
 <li class="hide">
    <a href="javascript:void(0)" class="js_search_export_raw">
        <i class="fa fa-file-text-o font20" data-placement="top" data-toggle="tooltip" data-original-title="Download Raw Excel"></i>
    </a>
</li> 
<li class="hide js_claim_export">
    <a href="javascript:void(0)" class="js_search_export_csv">
        <i class="fa fa-file-excel-o font20" data-placement="top" data-toggle="tooltip" data-original-title="Generate Excel"></i>
    </a>
</li>
<li class="messages-menu xlsx_report_export_spinner hide"  title="Generating Excel file" data-toggle="tooltip" data-placement="left" >
        <a href="#"><i style="font-size: 20px;" class="fa fa-spinner faa-spin animated"></i></a>
</li>
<li class="messages-menu xlsx_report_export_download hide"  data-placement="left">
     <a id="xlsx_report_export_download" href="#" title="Download Excel file"  data-toggle="tooltip"><i class="fa fa-download" ></i></a>
</li>
<li class="hide js_claim_export" style="cursor: pointer;">
    <a class="js_search_export_pdf">
        <i class="fa fa-file-pdf-o" data-placement="top" data-toggle="tooltip" data-original-title="Generate PDF"></i>
    </a>    
</li>
<li class="messages-menu pdf_report_export_spinner hide"  title="Generating PDF file" data-toggle="tooltip" data-placement="left">
        <a href="#"><i style="font-size: 20px;" class="fa fa-spinner faa-spin animated" ></i></a>
</li>
<li class="messages-menu pdf_report_export_download hide" title="Download PDF file" data-toggle="tooltip" data-placement="left">
        <a id="pdf_report_export_download" href="#"><i class="fa fa-download" ></i></a>
</li>
<?php /*
<?php
    $urlType = Request::segment(1);
    $Purl = ($urlType == 'reports') ? '#' : url($url.'pdf');
?>
@if($urlType == 'reports')
<li class="hide js_claim_export">
    <a class="js_search_export_pdf">
        <i class="fa fa-file-pdf-o" data-placement="top" data-toggle="tooltip" data-original-title="Download PDF"></i>
    </a>    
</li>
@else 
<li class="hide js_claim_export">
    <a class="js_search_export" href="{{ $Purl }}" data-url="{{ url($url) }}" data-option = "pdf" >
        <i class="fa fa-file-pdf-o" data-placement="top" data-toggle="tooltip" data-original-title="Download PDF"></i>
    </a>    
</li>
@endif
*/?>
<!--
<ul class="dropdown-menu" style="margin-top: 3px;">
    <li>
        <ul class="menu" style="list-style-type:none; ">
            <li>
                <a class="js_search_export" href="{{ url($url.'xlsx') }}" data-url="{{ url($url) }}" data-option = "xlsx">
                    <i class="fa fa-file-excel-o"></i> Excel
                </a>
            </li>
            <li class="">
                <a class="js_search_export" href="{{ url($url.'pdf') }}" data-url="{{ url($url) }}" data-option = "pdf">
                    <i class="fa fa-file-pdf-o" data-placement="right" data-toggle="tooltip" data-original-title="pdf"></i> PDF
                </a>
            </li>
            <li class="hide">
                <a class="js_search_export" href="{{ url($url.'csv') }}" data-url="{{ url($url) }}" data-option = "csv">
                    <i class="fa fa-file-code-o" data-placement="right" data-toggle="tooltip" data-original-title="csv"></i> CSV
                </a>
            </li>
        </ul>
    </li>
</ul>          

-->
<!---Export -->  