<!--
/*
 * Plugin jQuery.BBCode
 * Requires JQuery, make sure to have JQuery included in your JS to use this.
 * JQuery needs to be loaded before this script in order for it to work.
 *
 * Based on jQuery.BBCode plugin (http://www.webcheatsheet.com/javascript/bbcode_editor.php - http://www.kamaikinproject.ru)
 */
$(document).ready(function() {
	$().bbcode();
	// we unhide the tools if browser supports javascript
	$('.tool_strip_container').removeClass('collapse');
});
	
(function($) {
	$.fn.bbcode = function() {
		
		$('button.bb_button').click(function(event) {
			var element = $(this).parents('.tool_strip_container').next('textarea').get(0);
	
			var param = "";
			if ($(this).data("has-param")) {
				param = '=' + prompt($(this).data("has-param"));
			}
			
			var input = "";
			if ($(this).data("has-input-prompt")) {
				input = prompt($(this).data("has-input-prompt"));
			}
			
			var start = '[' + $(this).data("tag") + param + ']';
			var end = '[/' + $(this).data("tag") + ']';
			
			insert(start, input, end, element);
			return false;
		});
	}
	
	function insert(start, input, end, element) {
		if (document.selection) {
			element.focus();
			sel = document.selection.createRange();
			sel.text = start + sel.text + (input ? input : '') + end;
		} else if (element.selectionStart || element.selectionStart == '0') {
			element.focus();
			var startPos = element.selectionStart;
			var endPos = element.selectionEnd;
			element.value = element.value.substring(0, startPos) + start + element.value.substring(startPos, endPos) + (input ? input : '') + end + element.value.substring(endPos, element.value.length);
		} else {
			element.value += start + (input ? input : '') + end;
		}
	}
})(jQuery);

// -->
