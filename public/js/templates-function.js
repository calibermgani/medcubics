$(document).ready(function () {
	var CK_EDITOR = CKEDITOR.replace('editor1');
	var textbox = $("textarea#editor1");
	CK_EDITOR.on('change', function (event) {
		textbox.text(this.getData());
	});
	$(document).on('click',"#templatetags",function (event) {
		var code = $(this).attr('data-value');
		CK_EDITOR.insertText(code);
	});
});
