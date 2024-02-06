//Permission page checkbox
$(document).on('ifToggled change', '.js_submenu', function () {
    var chk = $(this).is(":checked");
    var get_class = $(this).attr('class');
    var split_class = get_class.split(' ');
    var cur_class = split_class[0];
    var data_class = $(this).attr('data-class');
    allmoduleCheck(data_class);
    menuCheck(cur_class);
});

$(document).on('ifToggled click change', '.js_menu', function () {
    var check_status = $(this).is(":checked");
    var get_id = $(this).attr('id');
    clas_name = (get_id == "js_select_all") ? "js_submenu" : get_id;
    $("." + clas_name).prop('checked', check_status);
    menuCheck(clas_name);
});

function menuCheck(cur_class) {
    var total_count = 0;
    $("." + cur_class + ".js_submenu").each(function (i) {
        total_count += 1;
    });
    var checked_count = 0;
    var dataclass = $("#" + cur_class + ".js_menu").attr('data-class');
    $('.' + cur_class + '.js_submenu:checked').each(function (i) {
        checked_count += 1;
    });
    if (total_count == checked_count) {
        $("#" + cur_class + ".js_menu").prop('checked', true);
    } else {
        $("#" + cur_class + ".js_menu").prop('checked', false);
    }
    allCheck(".js_submenu");
    allmoduleCheck(dataclass)
}

function allCheck(class_name) {
    var dataclass = $(this).attr('data-class');
    var checked_count = total_count = 0;
    $(class_name).each(function (i) {
        total_count += 1;
    });
    $(class_name + ':checked').each(function (i) {
        checked_count += 1;
    });
    allmoduleCheck(dataclass)
    if (total_count == checked_count) {
        $("#js_select_all").prop('checked', true);
    } else {
        $("#js_select_all").prop('checked', false);
    }
    //$('input[type="checkbox"].flat-red').iCheck('update');
    //$('input[type="checkbox"]#js_select_all').iCheck('update');
    if ($('#js_select_all').is(':checked')) {
        $('input[name="permission_module"]').prop('checked', true);
    } else {
        $('input[name="permission_module"]').prop('checked', false);
    }
}

$(document).on('click change', '#js_practice_mainmodule', function () {
    alert();
    var data_class = $(this).attr("data_class");
    var status = false;
    if ($(this).is(':checked')) {
        var status = true;
    }
    $('div.' + data_class).find(':checkbox').each(function () {
        $(this).prop('checked', status);
    });
    var permission_module_length = $('input[name="permission_module"]').length;
    var permission_module_checked_len = $('input[name="permission_module"]:checked').length;
    if (permission_module_length == permission_module_checked_len) {
        $("#js_select_all").prop('checked', true);
    } else {
        $("#js_select_all").prop('checked', false);
    }
})

function allmoduleCheck(data_class) {
    if (typeof data_class != 'undefined') {
        var submodule_length = $('input[data-class=' + data_class + ']').length;
        var sub_checked_len = $('input[data-class=' + data_class + ']:checked').length;
        if (submodule_length == sub_checked_len) {
            $('input[data_class=' + data_class + ']').prop('checked', true);
        } else {

            $('input[data_class=' + data_class + ']').prop('checked', false);
        }
    }
}
/*** Set Permission page admin side and Patient registration ends ***/
