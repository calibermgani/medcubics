
/*** Imo Search Option validation starts ***/
$(document).ready(function () {
    $(".js_advanced_search").click(function () {
        $('.advanced_search_form').bootstrapValidator('validate');
    });
    ValidateIt();
});
/*** Imo Search Option validation ends ***/

/*** Popup window option starts ***/
$(document).on("click",".js_toggle_view",function () {
	var current_id=$(this).attr('data-index');
	var content=$("#js_view_content_"+current_id).html();
	$("#js_content").html('');
	$("#js_content").html(content);
        $.AdminLTE.boxWidget.activate();
});
/*** Popup window option ends ***/

/*** Imo Search Option validation and form submission starts ***/
function ValidateIt() {
    var validator = $('.advanced_search_form').bootstrapValidator({
        feedbackIcons: {
             validating: "glyphicon glyphicon-refresh"
        },
        fields: {
			search_keyword: {
				message: '',
				validators: {
					notEmpty: {
						message: 'Enter keyword'
					}
				}
			}
		},
		onSuccess: function(e) {
			e.preventDefault();
			$("#js_result_show").addClass('hide');
			$('#overlay_part').removeClass('hide').addClass('show');
			var search_for = $(".js_advanced_search").attr('data-value');
			var data = $('[name=search_keyword_'+search_for+']').serialize();//only input
			$.ajax({
				url		: api_site_url+'/advanced/keywordsearch',
				type 	: 'POST', 
				data	:	data,
				success: function(msg){ 
					$('#overlay_part').removeClass('show').addClass('hide');
					$("#js_result_show").removeClass('hide');
					$("#js_advanced_result_table").html(msg);
					$('#example2').DataTable({
						"lengthChange": false,
						"searching": true,
						"ordering": true,
						"info": false,
						"fixedHeader": true,
						"responsive": true,
						"autoWidth": true
					});
				}
			})
		}
    });
}
/*** Imo Search Option validation and form submission ends ***/