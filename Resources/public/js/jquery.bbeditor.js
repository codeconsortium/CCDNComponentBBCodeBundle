<!--
/*
 * This file is part of the CCDNComponent BBCodeBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/CommonBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Plugin jQuery.BBEditor
 *
 * @author Reece Fowell <reece at codeconsortium dot com>
 *
 * BBCode Editor for textareas on sites using the BBCode engine.
 *
 * Requires JQuery, make sure to have JQuery included in your JS to use this.
 * JQuery needs to be loaded before this script in order for it to work.
 * @link http://jquery.com/
 *
 * Based on jQuery.BBCode plugin 
 * @link http://www.webcheatsheet.com/javascript/bbcode_editor.php
 * @link http://www.kamaikinproject.ru
 */

$(document).ready(function() {
	console.log('begin...');
	$('[class~="bb-block"]').bbeditor();
});

//(function($) {
//	$.fn.bbeditor = function() {

!function($) {

	//
	// BBEDITOR PLUGIN DEFINITION
	//
	$.fn.bbeditor = function () {
		var iters = [];

		return this.each(function() {
			var $this = $(this);
			
			console.log('button: ' + $this.data('tag'));
			
			var obj = new BBEditor($this);
			//setInterval($.proxy(obj.refresh,obj), obj.params.interval);
			$(this).click($.proxy(obj.click));
		});

	};

	//
	// BBEDITOR PUBLIC CLASS DEFINITION
	//
	var BBEditor = function (element) {
		this.init('bbeditor', element);
	};

	BBEditor.prototype = {

		constructor: BBEditor,
		
		init: function (type, element) {
			
		},
					
		getSelectedText: function (el) {
			console.log('boooo');
			
		    if (typeof el.selectionStart == "number") {
				console.log('sel' + el.selectionStart);
		        return el.value.slice(el.selectionStart, el.selectionEnd);
		    } else if (typeof document.selection != "undefined") {
				console.log('sel' + document.selection);
		        var range = document.selection.createRange();
		        if (range.parentElement() == el) {
		            return range.text;
		        }
		    }
		    return "";
		},
		
		click: function(event) {
//			var btn = $(this);
//			var txt = $('#' + btn.data('target-textarea'));
//			
			console.log($(this).data('tag'));
			console.log($(this).data('target-textarea'));
//			console.log(btn.data('tag'));
//			console.log(txt.attr('id'));
//			var r = getSelectedText(txt);
		},
		
//		$('[class~="bb-block"]').click(function(event) {
//			
//			var btn = $(this);
//			var txt = $('#' + btn.data('target-textarea'));
//			
////			console.log($(this).data('tag'));
////			console.log($(this).data('target-textarea'));
//			console.log(btn.data('tag'));
//			console.log(txt.attr('id'));
//			var r = getSelectedText(txt);
//			
//			console.log(r);
//			
//			// "[data-tag='" + event.target.data('data-tag') + "']"
//		});
		
	};
	
}(window.jQuery);


//function getSelectedText(el) {
//    if (typeof el.selectionStart == "number") {
//        return el.value.slice(el.selectionStart, el.selectionEnd);
//    } else if (typeof document.selection != "undefined") {
//        var range = document.selection.createRange();
//        if (range.parentElement() == el) {
//            return range.text;
//        }
//    }
//    return "";
//}
//
//function getLeftOfSel(el) {
//	if (typeof el.selectionStart == "number") {
//        return el.value.slice(0, el.selectionStart);
//    } else if (typeof document.selection != "undefined") {
//        var range = document.selection.createRange();
//        if (range.parentElement() == el) {
//			range.setEnd(el, range.getStart());
//			range.setStart(el, 0);
//            return range.text;
//        }
//    }
//    return "";
//}
//
//function getRightOfSel(el) {
//	if (typeof el.selectionStart == "number") {
//        return el.value.slice(el.selectionEnd, el.value.length);
//    } else if (typeof document.selection != "undefined") {
//        var range = document.selection.createRange();
//        if (range.parentElement() == el) {
//			range.setStart(el, range.getEnd());
//			range.setEnd(el, (el.value.length - range.getEnd()));
//            return range.text;
//        }
//    }
//    return "";
//}
//
//function addBlock(event, targetTextArea) {
//	
////	if ( ! event.target.getAttribute('data-tag')) { 
////		console.log('unknown data-tag');
////		return true;
////	}
//	
//	//var src = $(txt);
//	
//	//console.log(targetTextArea);
//	//return;
//			
//	var btn = $("[data-tag='" + event.target.data('data-tag') + "']");
//	var txt = $(btn.data('target-textarea'));
//	
//	
//		console.log(txt.id);
//		console.log('sel: ' + getSelectedText(txt) + '');
//		console.log('event: ' + event.target.getAttribute('data-tag'));
//	//	console.log('$event: ' + $(event.target).data('tag'));
//	
//	
//	var param = (btn.data('ask-param')) ? '=' + prompt(btn.data('ask-param')) : (btn.data('use-param')) ? '=' + btn.data('use-param') : '';
//	var value = (btn.data('ask-value')) ? prompt(btn.data('ask-value')) : getSelectedText(txt);
//	
//	var start = '[' + btn.data('tag') + param + ']';
//	var end = '[/' + btn.data('tag') + ']';
//	
//	var tag = start + value + end;
//		
//	var los = getLeftOfSel(txt);
//	var ros = getRightOfSel(txt);
//	
////	console.log('los: ' + los);
////	console.log('tag: ' + tag);
////	console.log('ros: ' + ros);
//
////	console.log('los + tag: ' + los + tag);
////	console.log('tag + ros: ' + tag + ros);
////	console.log('result: ' + los + tag + ros);
//	
//	txt.value = los + tag + ros;		
//}
//
//function addSmiley(target) {
//	var src = document.getElementById(target);
//	var btn = $(event.target);
//
//	var start = '[' + $(this).data('tag') + ']';
//	var end = '';
//
//	this.insert(start, value, end, $(target.id));
//
//	return true;
//}


// -->
