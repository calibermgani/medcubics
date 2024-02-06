<!---Export -->
<?php 
	$urlType = Request::segment(1);
	$Xurl = ($urlType == 'reports') ? '#' : url($url.'xlsx');
	$Purl = ($urlType == 'reports') ? '#' : url($url.'pdf');
?>
<li class="hide js_claim_export">
    <a class="js_search_export" href="{{ $Xurl }}" data-url="{{ url($url) }}" data-option = "xlsx">
        <i class="fa fa-file-excel-o font20" data-placement="top" data-toggle="tooltip" data-original-title="Download Excel"></i>
    </a>
</li>
<?php /*
<li class="hide js_claim_export">
    <a class="js_search_export" href="{{ $Purl }}" data-url="{{ url($url) }}" data-option = "pdf" >
        <i class="fa fa-file-pdf-o" data-placement="top" data-toggle="tooltip" data-original-title="Download PDF"></i>
    </a>    
</li>
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