<script type="text/javascript">
//starts pipeline process
$.fn.dataTable.pipeline = function (opts) {
	
	// Configuration options
	var conf = $.extend({
		pages: 2, // number of pages to cache
		url: '', // script url
		data: null, // function or object with parameters to send to the server
		// matching how `ajax.data` works in DataTables
		method: 'GET' // Ajax HTTP method
	}, opts);
	
	// Private variables for storing the cache
	var cacheLower = -1;
	var cacheUpper = null;
	var cacheLastRequest = null;
	var cacheLastJson = null;

	return function (request, drawCallback, settings) {
		var ajax = false;
		var requestStart = request.start;
		var drawStart = request.start;
		var requestLength = request.length;
		var requestEnd = requestStart + requestLength;

		if (settings.clearCache) {
			// API requested that the cache be cleared
			ajax = true;
			settings.clearCache = false;
		}
		else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
			// outside cached data - need to make a request
			ajax = true;
		}
		else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) || JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) || JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)) {
			// properties changed (ordering, columns, searching)
			ajax = true;
		}
		// Store the request for checking next time around
		cacheLastRequest = $.extend(true, {}, request);
		if (ajax) {
			// Need data from the server
			if (requestStart < cacheLower) {
				requestStart = requestStart - (requestLength * (conf.pages - 1));

				if (requestStart < 0) {
					requestStart = 0;
				}
			}

			cacheLower = requestStart;
			cacheUpper = requestStart + (requestLength * conf.pages);

			request.start = requestStart;
			request.length = requestLength * conf.pages;

			// Provide the same `data` options as DataTables.
			if ($.isFunction(conf.data)) {
				// As a function it is executed with the data object as an arg
				// for manipulation. If an object is returned, it is used as the
				// data object to submit
				var d = conf.data(request);
				if (d) {
					$.extend(request, d);
				}
			}
			else if ($.isPlainObject(conf.data)) {
				// As an object, the data given extends the default
				$.extend(request, conf.data);
			}
			settings.jqXHR = $.ajax({
				"type": conf.method,
				"url": conf.url,
				"data": request,
				"dataType": "json",
				"cache": false,
				"success": function (json) {
					cacheLastJson = $.extend(true, {}, json);

					if (cacheLower != drawStart) {
						json.data.splice(0, drawStart - cacheLower);
					}
					json.data.splice(requestLength, json.data.length);
					drawCallback(json);
					//fnCallback(json);								
				}
			});
		}
		else {
			json = $.extend(true, {}, cacheLastJson);
			json.draw = request.draw; // Update the echo for each response						
			json.data.splice(0, requestStart - cacheLower);
			json.data.splice(requestLength, json.data.length);

			drawCallback(json);
		}
	}
};

// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
$.fn.dataTable.Api.register('clearPipeline()', function () {
	return this.iterator('table', function (settings) {
		settings.clearCache = true;
	});
});

//ends pipeline process

//starts datatable for icd10 in admin

$(".icd10_list_admin").DataTable({
	//"processing": true,
	"serverSide": true,
	"ajax": $.fn.dataTable.pipeline({
		url: api_site_url + '/api/admin/geticd10valuesAdmin',
		pages: 2 // number of pages to cache
	}),
	"columns": 	[
		{"data": "icd_code"},
		{"data": "short_description"},
		{"data": "sex"},
		{"data": "effectivedate"},
		{"data": "inactivedate"}
	],
	"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		/*if (aData['user'] != null) {
			var created_name = aData['user']['name'];
			$('td:eq(5)', nRow).html(created_name);
		}
		if (aData['userupdate'] != null) {
			var updated_name = aData['userupdate']['name'];
			$('td:eq(6)', nRow).html(updated_name);
		}*/
		//var admin_icd_id = Base64.encode(aData['id'].toString());
		var icd_row_url = api_site_url + '/admin/icd/' + aData['id'];
		$(nRow).addClass('js-table-click clsCursor');
		$(nRow).attr('data-url', icd_row_url);
		return nRow;
	},
	"fnDrawCallback": function(settings) {	
		var str = $('.dataTables_filter input').val();		
		if($.trim(str) != ''){	
			listingpageHighlight('icd10_list_admin');
		}	
	}
});
//ends datatable for icd10 in admin

//starts datatable for icd10 in practice
$("#list-icd10").DataTable({
	//"processing": true,
	"serverSide": true,
	"ajax": $.fn.dataTable.pipeline({
		url: api_site_url + '/api/geticdtablevalues',
		pages: 2 // number of pages to cache
	}),
	"columns": [
		{"data": "icd_code"},
		{"data": "short_description"},
		{"data": "sex"},
		{"data": "effectivedate"},
		{"data": "inactivedate"}
	],
	"aColumns": [{"sType":"string"}],
	"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		var icd_row_url = api_site_url + '/icd/' + aData['id'];
		var icd_row_fav_url = api_site_url + '/toggleicdfavourites/' + aData['id'];
		var fav_tool_class;
		if (aData['favourite']) {
			fav_tool_class = "fa fa-star tooltips";
		} 
		else {
			fav_tool_class = "fa fa-star-o tooltips";
		}
		
		var icdid = aData['icd_code'];
		
		if(aData[icdid] != null){
			$('td:eq(2)', nRow).html(aData[icdid]['sex']);
			$('td:eq(3)', nRow).html(aData[icdid]['effectivedate']);
			$('td:eq(4)', nRow).html(aData[icdid]['inactivedate']);
		}
		
		$(nRow).addClass('js-table-click clsCursor');
		$(nRow).attr('data-url', icd_row_url);
		return nRow;
	},
	"fnDrawCallback": function(settings) {	
		var str = $('.dataTables_filter input').val();		
		if($.trim(str) != ''){	
			listingpageHighlight('list-icd10');
		}	
	}
});
//ends datatable for icd10 in practice				

//starts datatable for insurance in practice
$("#list-insurance").DataTable({
	//"processing": true,
	"serverSide": true,
	"ajax": $.fn.dataTable.pipeline({
		url: api_site_url + '/api/getinsurancetablevalues',
		pages: 2 // number of pages to cache
	}),
	"columns": [		
		{"data": "short_name"},
		{"data": "insurance_name"},
		{"data": "insurance_type"},
		{"data": "address_1"},
		{"data": "phone1"},
		{"data": "payerid"},
		
	],
	"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		var insurance_address = '';
		if (aData['address_1'] != null) {
			var insurance_address = aData['address_1']+', '+aData['city']+', '+aData['state']+', '+aData['zipcode5']+'-'+aData['zipcode4'] ;
		}
		if (aData['short_name'] != null) {
			var short_name = aData['short_name'];
		}
		if (aData['insurance_type'] != null) {
			var insurance_type = aData['insurance_type'];
		} else {
			var insurance_type = '';
		}
		$('td:eq(0)', nRow).html(short_name);
		$('td:eq(2)', nRow).html(insurance_type);
		$('td:eq(3)', nRow).html(insurance_address);

	/*	var fav_text;
		if (aData['favourite'] == null) {
			fav_text = "Add to favourite";
		} 
		else {
			fav_text = "Remove from favourite";
		} */
		//	var insurance_id = Base64.encode(aData['id'].toString());
		var icd_row_url = api_site_url + '/insurance/' + aData['id'];
		var icd_row_fav_url = api_site_url + '/toggleinsurancefavourites/' + aData['id'];
		var fav_tool_class;
		if (aData['favourite']) {
			fav_tool_class = "fa fa-star tooltips";
		} 
		else {
			fav_tool_class = "fa fa-star-o tooltips";
		}
		$(nRow).addClass('js-table-click clsCursor');
		$(nRow).attr('data-url', icd_row_url);
		/* var fav_col_elem = "<a href='javascript:void(0);' class='js-favourite-record tooltips' data-id='" + aData['id'] + "' data-url='" + icd_row_fav_url + "'><i class='" + fav_tool_class + "' data-placement='bottom' data-toggle='tooltip' data-original-title='" + fav_text + "'></i></a>";
		$('td:eq(5)', nRow).html(fav_col_elem); */
		return nRow;
	},
	"fnDrawCallback": function(settings) {		
		var str = $('.dataTables_filter input').val();		
		if($.trim(str) != ''){	
			listingpageHighlight('list-insurance');
		}	
	}
});
//starts datatable for insurance in admin					
$(".list-insurance-admin").DataTable({
	//"processing": true,
	"serverSide": true,
	"ajax": $.fn.dataTable.pipeline({
		url: api_site_url + '/api/admin/getinsurancevaluesAdmin',
		pages: 2 // number of pages to cache
	}),
	"columns": [		
		{"data": "short_name"},
		{"data": "insurance_name"},
		{"data": "insurance_type"},
		{"data": "address_1"},
		{"data": "phone1"},
		{"data": "payerid"},
	],
	"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

		var insurance_address = '';
		if (aData['address_1'] != null) {
			var insurance_address = aData['address_1']+', '+aData['city']+', '+aData['state']+', '+aData['zipcode5']+'-'+aData['zipcode4'] ;
		}
		if (aData['short_name'] != null) {
			var short_name = aData['short_name'];
		}
		if (aData['insurance_type'] != null) {
			var insurance_type = aData['insurance_type'];
		} else {
			var insurance_type = '';
		}
		$('td:eq(0)', nRow).html(short_name);
		$('td:eq(2)', nRow).html(insurance_type);
		$('td:eq(3)', nRow).html(insurance_address);
	
		//var admin_insurance_id = Base64.encode(aData['id'].toString());
		var icd_row_url = api_site_url + '/admin/insurance/' + aData['id'];
		$(nRow).addClass('js-table-click clsCursor');
		$(nRow).attr('data-url', icd_row_url);
		
		return nRow;
	}
});
//ends datatable for insurance in admin



//starts datatable for CPT in practice
$("#list-cpt").DataTable({
	//"processing": true,
	"columnDefs": [{ "orderable": false, "targets": -1 }],
	"serverSide": true,
	"ajax": $.fn.dataTable.pipeline({
		url: api_site_url + '/api/getcpttablevalues',
		pages: 2 // number of pages to cache
	}),
	"columns": [
		{"data": "cpt_hcpcs"},
		{"data": "medium_description"},
		{"data": "billed_amount"},
		{"data": "allowed_amount"},	
		{"data": "pos_id"},
		{"data": "type_of_service"},
		/*
		{"data": "created_at"},
		{"data": "created_by"},
		{"data": "updated_at"},
		{"data": "updated_by"}, */
		{"data": "favourite"}
	],
	"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		var fav_text;
		if (aData['favourite'] == null) {
			fav_text = "Are you sure to add to favorites";
		}
		else {
			fav_text = "Are you sure to remove from favorites";
		}
		//var cpt_id = Base64.encode(aData['id'].toString());
		var icd_row_url = api_site_url + '/cpt/' + aData['id'];
		var icd_row_fav_url = api_site_url + '/togglecptfavourites/' + aData['id'];
		var fav_tool_class;
		if (aData['favourite']) {
			fav_tool_class = "fa fa-star tooltips";
		} 
		else {
			fav_tool_class = "fa fa-star-o tooltips";
		}
		$(nRow).addClass('js-table-click clsCursor');
		$(nRow).attr('data-url', icd_row_url);
		
		var cptid = aData['cpt_hcpcs'];
		if(cptid != null){
			$('td:eq(2)', nRow).html(aData['billed_amount']).addClass('text-right');
			$('td:eq(3)', nRow).html(aData['allowed_amount']).addClass('text-right');
			$('td:eq(4)', nRow).html(aData['pos_id']).addClass('text-left');
			$('td:eq(5)', nRow).html(aData['type_of_service']);
		}
		var fav_col_elem = "<a href='javascript:void(0);' class='js-favourite-record tooltips' data-id='" + aData['id'] + "' data-url='" + icd_row_fav_url + "'><i class='" + fav_tool_class + "' data-placement='bottom' data-toggle='tooltip' data-original-title='" + fav_text + "'></i></a>";
		$('td:eq(6)', nRow).html(fav_col_elem);
		return nRow;
	},
	"fnDrawCallback": function(settings) {	
		var str = $('.dataTables_filter input').val();		
		if($.trim(str) != ''){	
			listingpageHighlight('list-cpt');
		}	
	}
});
//ends datatable for CPT in practice

//starts datatable for Favourites CPT in practice
var year = insurance = '';
loaddatatablefavcpt();
function loaddatatablefavcpt(year, insurance){
	$("#js_cpt_favourites").DataTable({
	//"processing": true,
	"columnDefs": [{ "orderable": false, "targets": -1 }],
	"serverSide": true,
	"lengthChange":false,
	"ajax": $.fn.dataTable.pipeline({
		url: api_site_url + '/api/getFavouritescpttablevalues/'+year+'/'+insurance,
		pages: 2 // number of pages to cache
	}),
	"columns": [
		{"data": "cpt_hcpcs"},
		{"data": "short_description"},
		{"data": "billed_amount"},
		{"data": "allowed_amount"},
		{"data": "pos_id"},
		{"data": "modifier_id"},
		{"data": "type_of_service"},
		/*
		{"data": "created_at"},
		{"data": "created_by"},
		{"data": "updated_at"},
		{"data": "updated_by"}, */
		{"data": "favourite"}
	],
	"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		var fav_text;
		if (aData['favourite'] == null) {
			fav_text = "Are you sure to add to favorites";
		}
		else {
			fav_text = "Are you sure to remove from favorites";
		}
		//var cpt_id = Base64.encode(aData['id'].toString());
		var icd_row_url = api_site_url + '/cpt/' + aData['id'];
		var icd_row_fav_url = api_site_url + '/togglecptfavourites/' + aData['id'];
		var fav_tool_class;
		if (aData['favourite']) {
			fav_tool_class = "fa fa-star tooltips";
		} 
		else {
			fav_tool_class = "fa fa-star-o tooltips";
		}
		$(nRow).addClass('js-table-click clsCursor');
		$(nRow).attr('data-url', icd_row_url);
		
		var cptid = aData['cpt_hcpcs'];
		
		if(cptid != null){
			$('td:eq(2)', nRow).html(aData.billed_amount).addClass('text-right');
			$('td:eq(3)', nRow).html(aData.allowed_amount).addClass('text-right');
			$('td:eq(4)', nRow).html(aData.pos_id);
			$('td:eq(5)', nRow).html(aData.modifier_id);
			$('td:eq(6)', nRow).html(aData.type_of_service);
		}
		var fav_col_elem = "<a href='javascript:void(0);' class='js-favourite-record tooltips' data-id='" + aData['id'] + "' data-url='" + icd_row_fav_url + "'><i class='" + fav_tool_class + "' data-placement='bottom' data-toggle='tooltip' data-original-title='" + fav_text + "'></i></a>";
		$('td:eq(7)', nRow).html(fav_col_elem);
		return nRow;
	},
	"fnDrawCallback": function(settings) {	
		var str = $('.dataTables_filter input').val();		
		if($.trim(str) != ''){	
			listingpageHighlight('js_cpt_favourites');
		}	
	}
});
}

//ends datatable for Favourites CPT in practice	

//starts datatable for CPT in admin
$(".list-cpt-admin").DataTable({
	//"processing": true,
	"serverSide": true,
	"ajax": $.fn.dataTable.pipeline({
		url: api_site_url + '/api/admin/getcptvaluesAdmin',
		pages: 2 // number of pages to cache
	}),
	"columns": [
		{"data": "cpt_hcpcs"},
		{"data": "short_description"},
		{"data": "billed_amount"},
		{"data": "allowed_amount"},
		{"data": "pos_id"},
		{"data": "type_of_service"},
	],
	"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		var fav_text;
		//var admin_cpt_id = Base64.encode(aData['id'].toString());
		var cpt_row_url = api_site_url + '/admin/cpt/' + aData['id'];
		$(nRow).addClass('js-table-click clsCursor');
		$(nRow).attr('data-url', cpt_row_url);
		return nRow;
	}
});

$("#useractivity_list").DataTable({
	//"processing": true,
	"serverSide": true,
	"ajax": $.fn.dataTable.pipeline({
		url: api_site_url + '/admin/getuseractivitylist',
		pages: 2 // number of pages to cache
	}),
	"columns": 	[
		{"data": "usertype"},
		{"data": "main_directory"},
		{"data": "module"},
		{"data": "user_activity_msg"},
		{"data": "activity_date"}
	],
	"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		
		var icd_row_url = aData['url'];
		var icd_row_module = aData['module'];
		var icd_row_action = aData['action'];
		var icd_row_activitytype = aData['activitytype'];
		
		$(nRow).addClass('js-useractivity-click clsCursor');
		
		if (icd_row_activitytype != null) 
			$(nRow).attr('data-activity', icd_row_activitytype);
		
		if (icd_row_module != null) 
			$(nRow).attr('data-module', icd_row_module);
		
		if (icd_row_action != null) 
			$(nRow).attr('data-action', icd_row_action);
		
		if (icd_row_url != null) 
			$(nRow).attr('data-url', icd_row_url);
		return nRow;
	}
});

    function login_his_search(allcolumns) { 
		search_url = listing_page_ajax_url;
		var dtable = $("#ex1").DataTable({			
			"createdRow": 	function ( row, data, index ) {
			if(data[1] != undefined)
    			data[1] = data[1].replace(/[\-,]/g, '');
							},		
			"bDestroy"	:	true,
			"searching": false,
			"paging"	: 	true,
			"info"		: 	true,
			//"aoColumns"	: 	allcolumns,
			// "columnDefs":   [ { orderable: false, targets: [0] } ], 
			"autoWidth"	: 	false,
			"lengthChange"		: false,
			//"processing": true,
			//"searchHighlight"	: true,
			"searchDelay": 450,
			"serverSide": true,	
			"order": [[8,"desc"]],
			
            "ajax": $.fn.dataTable.pipeline({
                url: search_url, 
                data:{'dataArr':dataArr},
                pages: 1, // number of pages to cache
                success: function(){
                    // Hide loader once content get loaded.
                }   
            }),
	        "columns": [
	            {"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" }
				
	        ],	
	        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {	
				$(".ajax_table_list").html(aData+"</tr>");
				var get_orig_html = $(".ajax_table_list tr").html();
				var get_attr = $(".ajax_table_list tr").attr("data-url");
				var get_class = $(".ajax_table_list tr").attr("class");
				$(nRow).addClass(get_class).attr('data-url', get_attr);
				$(nRow).closest('tr').html(get_orig_html);
				$(".ajax_table_list").html("");				
			},
			"fnDrawCallback": function(settings) {
				hideLoadingImage(); // Hide loader once content get loaded.				
			}
		});
	}

//ends datatable for CPT in admin
//ends datatable for insurance in practice
/*$(function () {
//starts datatable for icd9 in admin side
$("#list-icd9-admin").DataTable({
"processing": true,
"serverSide": true,
"ajax": $.fn.dataTable.pipeline({
url: api_site_url + '/api/admin/geticd9tablevaluesAdmin',
pages: 2 // number of pages to cache
}),
"columns": [
{"data": "code"},
{"data": "change_indicator"},
{"data": "code_status"},
{"data": "short_desc"},
{"data": "created_by"},
{"data": "updated_by"}
],
"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
if (aData['user'] != null) {
var created_name = aData['user']['name'];
$('td:eq(4)', nRow).html(created_name);
}
if (aData['userupdate'] != null) {
var updated_name = aData['userupdate']['name'];
$('td:eq(5)', nRow).html(updated_name);
}
var icd_row_url = api_site_url + '/admin/icd09/' + aData['id'];
$(nRow).addClass('js-table-click clsCursor');
$(nRow).attr('data-url', icd_row_url);
return nRow;
}
});
//ends datatable for icd9 in admin side

//starts datatable for icd9 in practice
$("#list-icd9").DataTable({
"processing": true,
"serverSide": true,
"ajax": $.fn.dataTable.pipeline({
url: api_site_url + '/api/geticd9tablevalues',
pages: 2 // number of pages to cache
}),
"columns": [
{"data": "code"},
{"data": "change_indicator"},
{"data": "code_status"},
{"data": "short_desc"},
{"data": "favourite"}
],
"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
var fav_text;
if (aData['favourite'] == null) {
fav_text = "Add to favourite";
} else {
fav_text = "Remove from favourite";
}
var icd_row_url = api_site_url + '/icd9/' + aData['id'];
var icd_row_fav_url = api_site_url + '/toggleicd9favourites/' + aData['id'];
var fav_tool_class;
if (aData['favourite']) {
fav_tool_class = "fa fa-star tooltips";
} else {
fav_tool_class = "fa fa-star-o tooltips";
}
$(nRow).addClass('js-table-click clsCursor');
$(nRow).attr('data-url', icd_row_url);
var fav_col_elem = "<a href='javascript:void(0);' class='js-favourite-record tooltips' data-id='" + aData['id'] + "' data-url='" + icd_row_fav_url + "'><i class='" + fav_tool_class + "' data-placement='bottom' data-toggle='tooltip' data-original-title='" + fav_text + "'></i></a>";
$('td:eq(4)', nRow).html(fav_col_elem);
return nRow;
}
});
//ends datatable for icd9 in practice

//starts datatable for favourite insurance in practice
$("#list-insurance-fav").DataTable({
"processing": true,
"serverSide": true,
"ajax": $.fn.dataTable.pipeline({
url: api_site_url+'/api/getinsurancefavvalues',
pages: 2 // number of pages to cache
}),
"columns": [							
{ "data": "insurancemaster.insurance_name" },
{ "data": "insurancemaster.insurancetype" },
{ "data": "insurancemaster.payerid" },
{ "data": "insurancemaster.phone1" }							
],						
"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {	

$('td:eq(0)', nRow).html(aData['insurancemaster']['insurance_name']);
var insurance_type = '';
if(aData['insurancemaster']['insurancetype'] != null){
if(aData['insurancemaster']['insurancetype']['type_name'] != null)
var insurance_type = aData['insurancemaster']['insurancetype']['type_name'];
}
$('td:eq(1)', nRow).html(insurance_type);

$('td:eq(2)', nRow).html(aData['insurancemaster']['payerid']);	
$('td:eq(3)', nRow).html(aData['insurancemaster']['phone_1']);
var fav_text = "Remove from favourite";																						
var icd_row_url = api_site_url+'/insurancemaster/'+aData['insurancemaster']['id'];							
var icd_row_fav_url = api_site_url+'/toggleinsurancemasterfavourites/'+aData['insurancemaster']['id'];							
var fav_tool_class = "fa fa-star tooltips";	
$(nRow).addClass('js-table-click clsCursor');
$(nRow).attr('data-url', icd_row_url);							
var fav_col_elem = "<a href='javascript:void(0);' class='js-favourite-record tooltips' data-id='"+aData['insurancemaster']['id']+"' data-url='"+icd_row_fav_url+"'><i class='"+fav_tool_class+"' data-placement='bottom' data-toggle='tooltip' data-original-title='"+fav_text+"'></i></a>";							
$('td:eq(4)', nRow).html(fav_col_elem);

return nRow;
}						
});	
//ends datatable for favourite insurance in practice

//starts datatable for insurance-optum in admin					
$("#list-insurance-optum-admin").DataTable({
"processing": true,
"serverSide": true,
"ajax": $.fn.dataTable.pipeline({
url: api_site_url + '/api/admin/getinsuranceoptumvaluesAdmin',
pages: 2 // number of pages to cache
}),
"columns": [
{"data": "insurance_name"},
{"data": "insurancetype"},
{"data": "insuranceclass"},
{"data": "payerid"},
{"data": "phone_1"},
{"data": "favourite"}
],
"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
var insurance_type = aData['insurancetype']['type_name'];
$('td:eq(1)', nRow).html(insurance_type);
var insurance_class = aData['insuranceclass']['insurance_class'];
$('td:eq(2)', nRow).html(insurance_class);
var fav_text;
if (aData['favourite'] == null) {
fav_text = "Add to favourite";
} else {
fav_text = "Remove from favourite";
}
var icd_row_url = api_site_url + '/api/admin/insurance/' + aData['id'];
var icd_row_fav_url = api_site_url + '/api/admin/toggleinsurancefavourites/' + aData['id'];
var fav_tool_class;
if (aData['favourite']) {
fav_tool_class = "fa fa-star tooltips";
} else {
fav_tool_class = "fa fa-star-o tooltips";
}
$(nRow).addClass('js-table-click clsCursor');
$(nRow).attr('data-url', icd_row_url);
var fav_col_elem = "<a href='javascript:void(0);' class='js-favourite-record tooltips' data-id='" + aData['id'] + "' data-url='" + icd_row_fav_url + "'><i class='" + fav_tool_class + "' data-placement='bottom' data-toggle='tooltip' data-original-title='" + fav_text + "'></i></a>";
$('td:eq(5)', nRow).html(fav_col_elem);
return nRow;
}
});
//ends datatable for insurance-optum in admin

//starts datatable for insurance optum in practice
$("#list-insurance-optum").DataTable({
"processing": true,
"serverSide": true,
"ajax": $.fn.dataTable.pipeline({
url: api_site_url + '/api/getinsuranceoptumvalues',
pages: 2 // number of pages to cache
}),
"columns": [
{"data": "insurance_name"},
{"data": "short_name"},
{"data": "insurancetype"},
{"data": "insuranceclass"},
{"data": "payerid"},
{"data": "phone_1"},
{"data": "favourite"}
],
"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

var short_name = aData['short_name'];
$('td:eq(1)', nRow).html(short_name);
var insurance_type = aData['insurancetype']['type_name'];
$('td:eq(2)', nRow).html(insurance_type);
var insurance_class = aData['insuranceclass']['insurance_class'];
$('td:eq(3)', nRow).html(insurance_class);
var fav_text;
if (aData['favourite'] == null) {
fav_text = "Add to favourite";
} else {
fav_text = "Remove from favourite";
}
var icd_row_url = api_site_url + '/insurance/' + aData['id'];
var icd_row_fav_url = api_site_url + '/toggleinsurancefavourites/' + aData['id'];
var fav_tool_class;
if (aData['favourite']) {
fav_tool_class = "fa fa-star tooltips";
} else {
fav_tool_class = "fa fa-star-o tooltips";
}
$(nRow).addClass('js-table-click clsCursor');
$(nRow).attr('data-url', icd_row_url);
var fav_col_elem = "<a href='javascript:void(0);' class='js-favourite-record tooltips' data-id='" + aData['id'] + "' data-url='" + icd_row_fav_url + "'><i class='" + fav_tool_class + "' data-placement='bottom' data-toggle='tooltip' data-original-title='" + fav_text + "'></i></a>";
$('td:eq(5)', nRow).html(fav_col_elem);
return nRow;
}
});
//ends datatable for insurance optum in practice*/
</script>    